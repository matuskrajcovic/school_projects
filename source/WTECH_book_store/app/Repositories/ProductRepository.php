<?php

namespace App\Repositories;

use App\Interfaces\ProductRepositoryInterface;
use App\Interfaces\CategoryRepositoryInterface;
use App\Models\AudioBook;
use App\Models\Product;
use App\Models\Book;
use App\Models\EBook;
use App\Models\Merchandice;

class ProductRepository implements ProductRepositoryInterface
{
	public function __construct(CategoryRepositoryInterface $cri)
	{
		$this->categoryRepository = $cri;
	}

	public function getAllProducts()
	{
		return Product::all();
	}

	public function getAllProductsPaginate($paginate)
	{
		return Product::paginate($paginate);
	}

	public function getAllProductsAdmin($requestData)
	{
		if(!isset($requestData['search']))
			return Product::with(['photos', 'author'])->simplePaginate(10);
		
		$author_search = function($q) use($requestData) {
			$q->where('name', 'ilike', '%' . $requestData['search'] . '%');
		};

		$filter = function($q) use($requestData, $author_search) {
			$q->where('name', 'ilike', '%' . $requestData['search'] . '%')
				->orWhereHas('product.author', $author_search);
		};

		return Product::where(function($q) use($filter) {
			$q->orWhereHas('book', $filter)
				->orWhereHas('e_book', $filter)
				->orWhereHas('audio_book', $filter)
				->orWhereHas('merchandice', $filter)
				->with(['book', 'e_book', 'audio_book', 'merchandice']);
		})->with(['photos', 'author'])->simplePaginate(10);
	}

	public function getAllProductsFilter($requestData)
	{
		$sort = [
			1 => 'price asc',
			2 => 'price desc',
		];

		$author_search = function($q) use($requestData) {
			$q->where('name', 'ilike', '%' . $requestData['search'] . '%');
		};

		$filter = function($q) use($requestData, $author_search) {
			if(isset($requestData['search']))
				$q->where('name', 'ilike', '%' . $requestData['search'] . '%')
					->orWhereHas('product.author', $author_search);
			if(isset($requestData['price_min']))
				$q->where('price', '>=', $requestData['price_min']);
			if(isset($requestData['price_max']))
				$q->where('price', '<=', $requestData['price_max']);
			if(isset($requestData['year']))
				$q->where('year', $requestData['year']);
			if(isset($requestData['lang']))
				$q->where('language_id', $requestData['lang']);
		};

		$filter_merch = function($q) use($requestData, $author_search) {
			if(isset($requestData['search']))
				$q->where('name', 'ilike', '%' . $requestData['search'] . '%')
					->orWhereHas('product.author', $author_search);
			if(isset($requestData['price_min']))
				$q->where('price', '>=', $requestData['price_min']);
			if(isset($requestData['price_max']))
				$q->where('price', '<=', $requestData['price_max']);
		};

		$products = Product::whereHas('book', $filter)
		->union(Product::whereHas('e_book', $filter))
		->union(Product::whereHas('audio_book', $filter))
		->union(Product::whereHas('merchandice', $filter_merch))
		->with(['author', 'book', 'e_book', 'audio_book', 'merchandice', 'photos']);

		if(isset($requestData['sort']))
			$products->orderByRaw($sort[$requestData['sort']]);

		return $products->paginate($requestData['per_page']);
	}

	public function getAllProductsFilterByCategory($categoryId, $requestData)
	{
		$sort = [
			1 => 'price asc',
			2 => 'price desc',
		];

		$author_search = function($q) use($requestData) {
			$q->where('name', 'ilike', '%' . $requestData['search'] . '%');
		};

		$filter = function($q) use($requestData, $author_search) {
			if(isset($requestData['search']))
				$q->where('name', 'ilike', '%' . $requestData['search'] . '%')
					->orWhereHas('product.author', $author_search);
			if(isset($requestData['price_min']))
				$q->where('price', '>=', $requestData['price_min']);
			if(isset($requestData['price_max']))
				$q->where('price', '<=', $requestData['price_max']);
			if(isset($requestData['year']))
				$q->where('year', $requestData['year']);
			if(isset($requestData['lang']))
				$q->where('language_id', $requestData['lang']);
		};

		$filter_merch = function($q) use($requestData, $author_search) {
			if(isset($requestData['search']))
				$q->where('name', 'ilike', '%' . $requestData['search'] . '%')
					->orWhereHas('product.author', $author_search);
			if(isset($requestData['price_min']))
				$q->where('price', '>=', $requestData['price_min']);
			if(isset($requestData['price_max']))
				$q->where('price', '<=', $requestData['price_max']);
		};

		$categories = $this->categoryRepository->getAllSubcategoriesInArr($categoryId, ['id']);

		$products = Product::whereIn('category_id', $categories)
			->where(function($q) use($filter, $filter_merch) {
				$q->orWhereHas('book', $filter)
					->orWhereHas('e_book', $filter)
					->orWhereHas('audio_book', $filter)
					->orWhereHas('merchandice', $filter_merch)
					->with(['book', 'e_book', 'audio_book', 'merchandice']);
			})->with(['photos', 'author']);

		if(isset($requestData['sort']))
			$products->orderByRaw($sort[$requestData['sort']]);

		return $products->paginate($requestData['per_page']);
	}

	public function getProductById($productId)
	{
		return Product::find($productId);
	}

	public function getProductsByUserToArr($user, $column)
	{
		return $user->user_product->pluck($column);
	}

	public function getProductsById($productIds)
	{
		return Product::whereIn('id', $productIds)->get();
	}

	public function getProductByIdWith($productId, $with = null)
	{
		if(!isset($with))
			$with = ['photos', 'author', 'reviews', 'category'];
		return Product::with($with)->find($productId);
	}

	public function createProduct($type, $requestData)
	{
		$requestData['product_type'] = $type;
		$requestData['available'] = true;

		$product = Product::create($requestData);
		$requestData['product_id'] = $product->id;

		switch($type)
		{
			case 'book':
				$requestData['shipping_time'] = 14;
				Book::create($requestData);
				break;
			case 'e_book':
				EBook::create($requestData);
				break;
			case 'audio_book':
				AudioBook::create($requestData);
				break;
			case 'merchandice':
				$requestData['shipping_time'] = 14;
				Merchandice::create($requestData);
				break;
		}

		return $product;
	}

	public function deleteProduct($productId)
	{
	}

	public function updateProduct($productId)
	{
	}

	public function getFavorites()
	{
		$sql = 'SELECT * FROM products RIGHT JOIN (SELECT p.id, sum(r.stars) FROM products p FULL JOIN reviews r ON p.id=r.product_id GROUP BY (p.id)) s ON s.id=products.id ORDER BY s.sum DESC NULLS LAST, s.id DESC LIMIT 8';

		$result = Product::fromQuery($sql, []);
		$result->load('author', 'photos');

		return $result;
	}

	public function getTopSelling()
	{
		$sql = 'SELECT * FROM products RIGHT JOIN (SELECT p.id, sum(op.count) FROM products p FULL JOIN order_product op ON p.id=op.product_id FULL JOIN orders o ON o.id=op.order_id GROUP BY (p.id)) s ON s.id=products.id ORDER BY sum DESC NULLS LAST, s.id DESC LIMIT 8';

		
		$result = Product::fromQuery($sql, []);
		$result->load('author', 'photos');

		return $result;
	}

	public function getNew()
	{
		return Book::with('product', 'product.photos', 'product.author')->orderBy('created_at', 'desc')->limit(5)->get();
	}
}
