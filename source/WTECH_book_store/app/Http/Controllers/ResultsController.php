<?php

namespace App\Http\Controllers;

use App\Interfaces\CategoryRepositoryInterface;
use App\Interfaces\ProductRepositoryInterface;
use App\Models\Language;

use Carbon\Carbon;
use Illuminate\Http\Request;


class ResultsController extends Controller
{
	private ProductRepositoryInterface $productRepository;
	private CategoryRepositoryInterface $categoryRepository;

	public function __construct(ProductRepositoryInterface $pri, CategoryRepositoryInterface $cri)
	{
		$this->productRepository = $pri;
		$this->categoryRepository = $cri;
	}

	private function validate_query(Request $request)
	{
		$query = $request->validate([
			'per_page' => 'integer|min:1|max:25',
			'search' => 'string|max:64',
			'price_min' => 'numeric|min:0',
			'price_max' => 'numeric|min:0',
			'year' => 'integer|min:1800|max:' . Carbon::now()->year,
			'lang' => 'integer|min:1',
			'sort' => 'integer|min:1|max:2',
		]);

		if(!isset($query['per_page']))
			$query['per_page'] = 10;

		return $query;
	}

	public function index(Request $request)
	{
		$query = $this->validate_query($request);

		$products = $this->productRepository->getAllProductsFilter($query)->withQueryString();

		if(isset($query['per_page']) && $query['per_page'] == 10)
			unset($query['per_page']);

		$languages = Language::all();

		return view('results', [
			'products' => $products,
			'query' => $query,
			'languages' => $languages,
		]);
	}

	public function show($id, Request $request)
	{
		$query = $this->validate_query($request);

		$products = $this->productRepository->getAllProductsFilterByCategory($id, $query)->withQueryString();

		if(isset($query['per_page']) && $query['per_page'] == 10)
			unset($query['per_page']);

		$category = $this->categoryRepository->getCategoryById($id, ['name']);
		$subcategories = $this->categoryRepository->getSubcategories($id);
		$languages = Language::all();

		return view('results', [
			'products' => $products,
			'category_name' => $category->name,
			'subcategories' => $subcategories,
			'query' => $query,
			'languages' => $languages,
		]);
	}
}
