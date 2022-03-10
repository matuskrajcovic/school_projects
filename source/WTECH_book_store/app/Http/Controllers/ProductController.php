<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreProductRequest;
use App\Interfaces\CategoryRepositoryInterface;
use App\Interfaces\ProductRepositoryInterface;
use Illuminate\Http\Request;

use App\Models\Product;
use App\Models\Category;
use App\Models\Language;
use App\Models\Author;
use App\Models\Book;
use App\Models\EBook;
use App\Models\Photo;
use Carbon\Carbon;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Hash;

class ProductController extends Controller
{
	private ProductRepositoryInterface $productRepository;
	private CategoryRepositoryInterface $categoryRepository;

	public function __construct(ProductRepositoryInterface $pri, CategoryRepositoryInterface $cri)
	{
		$this->productRepository = $pri;
		$this->categoryRepository = $cri;
	}

	public function index(Request $request)
	{
		$query = $request->validate([
			'search' => 'string|max:64',
		]);

		$products = $this->productRepository->getAllProductsAdmin($query);

		return view('admin.index', [
			'products' => $products,
			'search_string' => $request->query('search')
		]);
	}

	public function create($type)
	{
		switch($type)
		{
			case 'book':
				$fields = 'admin.partials.book';
				$type_id = 1;
				break;
			case 'e_book':
				$fields = 'admin.partials.ebook';
				$type_id = 2;
				break;
			case 'audio_book':
				$fields = 'admin.partials.audiobook';
				$type_id = 3;
				break;
			case 'merchandice':
				$fields = 'admin.partials.merch';
				$type_id = 4;
				break;
			case 'author':
				return view('admin.editor', [
					'fields' => 'admin.partials.author',
					'title' => 'Vytvoriť autora'
				]);
				break;
			default:
				abort(404);
		}

		$category = Category::find($type_id);
		$categories = $this->categoryRepository->getAllSubcategoriesInArr($category->id, ['id']);

		$languages = Language::all();
		$authors = Author::orderBy('id')->get();

		return view('admin.editor', [
			'product' => null,
			'item' => null,
			'fields' => $fields,
			'categories' => Category::whereIn('id', $categories)->get(),
			'languages' => $languages,
			'authors' => $authors,
			'title' => 'Vytvoriť produkt'
		]);
	}

	public function store(StoreProductRequest $request, $type)
	{
		if (Cache::has('new')) {
			Cache::forget('new');
		}

		$requestData = $request->validated();

		if($type != 'author')
		{
			$product = $this->productRepository->createProduct($type, $requestData);

			$main_name = $this->update_main_image($request, $product->id);
			if(strlen($main_name) > 0)
				$product->update(['main_photo' => $main_name]);

			$other_names = $this->upload_other_images($request, $product->id);
			if(count($other_names) > 0)
				$product->photos()->createMany($other_names);
		}
		else if($type == 'author')
		{
			Author::create($requestData);
		}

		return redirect()->route('admin');
	}

	public function show($id)
	{
		$product = Cache::remember('product-'.$id, 60, function() use($id) {
			return Product::with(['author', 'photos', 'category'])->findOrFail($id);
		});

		if($product){
			$item = $product->get_item;
			$side_photos = $product->photos->where('main', false)->take(4);
			$cat_id = $product->category->id;

			$recommended = Product::whereHas('category', function($q) use ($cat_id) {
								$q->where('id', '=', $cat_id);
							})->where('id', '!=', $product->id)
								->inRandomOrder()
								->limit(5)
								->with(['author', 'photos'])
								->get();

			return view('detail', [
				'product' => $product,
				'item' => $item,
				'photos' => $side_photos,
				'recommended' => $recommended
			]);
		}
		else return abort(404);
	}

	public function edit($id)
	{
		$product = Product::with('author', 'photos')->findOrFail($id);
		switch($product->product_type)
		{
			case 'book':
				$fields = 'admin.partials.book';
				$type_id = 1;
				break;
			case 'e_book':
				$fields = 'admin.partials.ebook';
				$type_id = 2;
				break;
			case 'audio_book':
				$fields = 'admin.partials.audiobook';
				$type_id = 3;
				break;
			case 'merchandice':
				$fields = 'admin.partials.merch';
				$type_id = 4;
				break;
			default:
				abort(404);
		}

		$category = Category::find($type_id);
		$categories = $this->categoryRepository->getAllSubcategoriesInArr($category->id, ['id']);

		$languages = Language::all();
		$authors = Author::all();

		$photos = $product->photos->where('main', false);

		return view('admin.editor', [
			'product' => $product,
			'item' => $product->get_item,
			'fields' => $fields,
			'categories' => Category::whereIn('id', $categories)->get(),
			'languages' => $languages,
			'authors' => $authors,
			'main_image' => $product->main_photo,
			'images' => $photos,
			'title' => 'Upraviť produkt'
		]);
	}

	public function update(StoreProductRequest $request, $id)
	{
		if (Cache::has('product-'.$id)) {
			Cache::forget('product-'.$id);
		}

		$requestData = $request->validated();

		$product = $this->productRepository->getProductByIdWith($id, ['category', 'author', 'photos']);

		$main_image = $this->update_main_image($request, $id);
		if(strlen($main_image) > 0)
			$requestData['main_photo'] = $main_image;
		$product->update($requestData);

		$other_names = $this->upload_other_images($request, $product->id);
		if(count($other_names) > 0)
			$product->photos()->createMany($other_names);

		switch($product->product_type)
		{
			case 'book':
				$product->book->update($requestData);
				break;
			case 'e_book':
				$product->e_book->update($requestData);
				break;
			case 'audio_book':
				$product->audio_book->update($requestData);
				break;
			case 'merchandice':
				$product->merchandice->update($requestData);
				break;
			default:
				abort(404);
		}

		return redirect()->route('edit-product', ['id' => $id]);
	}

	public function destroy($id)
	{
		$product = Product::find($id);

		if($product == null)
			abort(404);

		$img = $product->main_photo;
		if(File::exists(public_path('images/covers/' . $img)))
			File::delete(public_path('images/covers/' . $img));

		$item = $product->get_item;
		$item->destroy($item->id);
		Product::destroy($id);

		return redirect()->route('admin');
	}

	public function delete_main_image($id)
	{
		$product = Product::find($id);

		$img = $product->main_photo;
		if(File::exists(public_path('images/covers/' . $img)))
			File::delete(public_path('images/covers/' . $img));
		else
			abort(404);

		$product->update(['main_photo' => null]);

		return response('');
	}

	public function delete_other_image(Request $request, $id)
	{
		$requestData = $request->validate([
			'photo_id' => 'required|integer|min:1',
			'path' => 'required|string',
		]);

		if(File::exists(public_path('images/covers/' . $requestData['path'])))
			File::delete(public_path('images/covers/' . $requestData['path']));
		else
			abort(404);

		Photo::destroy($requestData['photo_id']);

		return response('');
	}

	private function update_main_image(Request $request, $id)
	{
		if ($request->hasFile('main_photo'))
		{
			$image = $request->file('main_photo');
			$extension = $image->getClientOriginalExtension();
			$fs_name = md5($id . '-main').'.'.$extension;

			$image->storeAs('/covers', $fs_name, 'public_images');
			return $fs_name;
		}

		return "";
	}

	private function upload_other_images(Request $request, $id)
	{
		if ($request->hasFile('images'))
		{
			$i = 1;
			foreach($request->images as $image)
			{
				$extension = $image->getClientOriginalExtension();
				$arr['id'] = $id;
				$arr['path'] = md5($id . Carbon::createFromTimestampMs(Carbon::now()->getTimestampMs())->format('Y-m-d H:i:s.u') . $i++).'.'.$extension;
				$fs_names[] = $arr;

				$image->storeAs('/covers', $arr['path'], 'public_images');
			}

			return $fs_names;
		}

		return [];
	}
}
