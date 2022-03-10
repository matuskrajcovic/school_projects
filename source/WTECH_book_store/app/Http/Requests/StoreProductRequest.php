<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreProductRequest extends FormRequest
{
	/**
	 * Determine if the user is authorized to make this request.
	 *
	 * @return bool
	 */
	public function authorize()
	{
		return true;
	}

	/**
	 * Get the validation rules that apply to the request.
	 *
	 * @return array
	 */
	public function rules()
	{
		$uri_arr = explode('/', $this->getRequestUri());
		if(count($uri_arr) > 3)
			$type = $uri_arr[3];
		else
			$type = $this->request->get('product_type');

		if($type == 'author')
			return $this->validate_author();

		return array_merge([
			'category_id' => 'required|integer|min:1',
			'name' => 'required|string|min:1|max:100',
			'price' => 'required|string|regex:/^\d*(\.\d{2})?$/',
			'author_id' => 'integer|min:1',
			'main_photo' => 'file|max:4096|image|mimes:jpg,png,jpeg',
			'images.*' => 'file|max:4096|image|mimes:jpg,png,jpeg',
		], $this->call_func($type));
	}

	private function call_func($type)
	{
		switch($type)
		{
			case 'book':
				return $this->validate_book();
			case 'e_book':
				return $this->validate_ebook();
			case 'audio_book':
				return $this->validate_audiobook();
			case 'merchandice':
				return $this->validate_merch();
			default:
				abort(404);
		}
	}

	private function validate_book()
	{
		return [
			'publisher' => 'required|string|min:1|max:100',
			'year' => 'required|integer|min:1',
			'pages' => 'required|integer|min:1',
			'language_id' => 'required|integer|min:1',
			'country' => 'required|string|min:1|max:100',
			'isbn' => 'required|string|min:1|max:20',
			'stock' => 'required|integer|min:0',
			'detail' => 'required|min:1|max:500',
			'long_detail' => 'required|min:1|max:5000',
		];
	}

	private function validate_ebook()
	{
		return [
			'publisher' => 'required|string|min:1|max:100',
			'year' => 'required|integer|min:1',
			'pages' => 'required|integer|min:1',
			'language_id' => 'required|integer|min:1',
			'format' => 'required|string|in:epub,mobi,pdf',
			'detail' => 'required|min:1|max:500',
			'long_detail' => 'required|min:1|max:5000',
		];
	}

	private function validate_audiobook()
	{
		return [
			'publisher' => 'required|string|min:1|max:100',
			'year' => 'required|integer|min:1',
			'language_id' => 'required|integer|min:1',
			'duration' => 'required|string|min:1|max:100',
			'format' => 'required|string|in:mp3,aax,ogg',
			'detail' => 'required|min:1|max:500',
			'long_detail' => 'required|min:1|max:5000',
		];
	}

	private function validate_merch()
	{
		return [
			'stock' => 'required|integer|min:0',
			'detail' => 'required|min:1|max:500'
		];
	}

	private function validate_author()
	{
		return [
			'name' => 'required|string|min:1|max:100',
			'country' => 'required|string|min:1|max:100',
			'detail' => 'required|min:1|max:500'
		];
	}
}
