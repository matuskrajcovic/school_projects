<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
	use HasFactory;

	protected $amount;

	protected $casts = ['price' => 'float'];

	protected $fillable = [
		'category_id',
		'author_id',
		'product_type',
		'name',
		'price',
		'main_photo',
		'available',
	];

	public function book()
	{
		return $this->hasOne(Book::class);
	}

	public function e_book()
	{
		return $this->hasOne(EBook::class);
	}

	public function audio_book()
	{
		return $this->hasOne(AudioBook::class);
	}

	public function merchandice()
	{
		return $this->hasOne(Merchandice::class);
	}

	public function author()
	{
		return $this->belongsTo(Author::class);
	}

	public function photos()
	{
		return $this->hasMany(Photo::class);
	}

	public function reviews()
	{
		return $this->hasMany(Review::class);
	}

	public function get_item()
	{
		switch($this->product_type){
			case 'book':
				return $this->hasOne(Book::class);
				break;
			case 'e_book':
				return $this->hasOne(EBook::class);
				break;
			case 'audio_book':
				return $this->hasOne(AudioBook::class);
				break;
			case 'merchandice':
				return $this->hasOne(Merchandice::class);
				break;
			default:
				return $this;
		}
	}

	public function category()
	{
		return $this->belongsTo(Category::class);
	}

	public function order_product()
	{
		return $this->belongsToMany(Order::class)->withPivot('count');
	}

	public function user_product()
	{
		return $this->belongsToMany(User::class)->withPivot('count');
	}
}
