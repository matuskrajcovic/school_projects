<?php

namespace App\Repositories;

use App\Interfaces\CategoryRepositoryInterface;
use App\Models\Category;

class CategoryRepository implements CategoryRepositoryInterface
{
	public function getAllSubcategories($categoryId, $columns = [])
	{
		if(!count($columns))
			return Category::find($categoryId, $columns)->allCategories;
	}

	public function getAllSubcategoriesInArr($categoryId, $columns = [])
	{
		if(!count($columns))
			return Category::find($categoryId, $columns)->allCategories;

		$categories = Category::find($categoryId, $columns)->allCategories;
		$result[] = $categoryId;
		$this->parseCategories($categories, $result);

		return $result;
	}

	public function getSubcategories($categoryId)
	{
		return Category::find($categoryId)->categories;
	}

	public function getCategoryById($categoryId)
	{
		return Category::find($categoryId);
	}

	private function parseCategories($categories, &$result)
	{
		if(count($categories))
		{
			foreach($categories as $index => $category){
				array_push($result, $category->id);
				$this->parseCategories($category->allCategories, $result);
			}
		}
	}
}
