<?php

namespace App\Interfaces;

interface ProductRepositoryInterface
{
	public function getAllProducts();
	public function getAllProductsPaginate($paginate);
	public function getAllProductsAdmin($requestData);
	public function getAllProductsFilter($requestData);
	public function getAllProductsFilterByCategory($categoryId, $requestData);
	public function getProductById($productId);
	public function getProductsByUserToArr($user, $column);
	public function getProductsById($productIds);
	public function getProductByIdWith($productId, $with);
	public function createProduct($type, $requestData);
	public function deleteProduct($productId);
	public function updateProduct($productId);

	public function getFavorites();
	public function getTopSelling();
	public function getNew();
}
