<?php

namespace App\Interfaces;

interface CategoryRepositoryInterface
{
	public function getAllSubcategories($categoryId, $columns);
	public function getAllSubcategoriesInArr($categoryId, $columns);
	public function getSubcategories($categoryId);
	public function getCategoryById($categoryId);
}
