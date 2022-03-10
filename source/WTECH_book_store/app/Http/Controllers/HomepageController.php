<?php

namespace App\Http\Controllers;

use App\Interfaces\ProductRepositoryInterface;
use Illuminate\Support\Facades\Cache;

class HomepageController extends Controller
{
	private ProductRepositoryInterface $productRepository;

	public function __construct(ProductRepositoryInterface $pri)
	{
		$this->productRepository = $pri;
	}

	public function index()
	{
		$favorites = Cache::remember('favorites', 60, function() {
			return $this->productRepository->getFavorites();
		});

		$topselling = Cache::remember('topselling', 60, function() {
			return $this->productRepository->getTopSelling();
		});

		$new = Cache::remember('new', 60, function() {
			return $this->productRepository->getNew();
		});

		return view('index', [
			'favorites' => $favorites,
			'topselling' => $topselling,
			'new' => $new
		]);
	}
}
