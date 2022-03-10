<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Order extends Model
{
    use HasFactory;

	protected $fillable = [
		'user',
		'address',
		'city',
		'postal_code',
		'shipping_type',
		'note',
		'count',
		'price',
	];

	public function user()
	{
		return $this->belongsTo(User::class);
	}

    public function order_product()
    {
        return $this->belongsToMany(Product::class)->withPivot('count');
    }

	public function address()
	{
		return $this->belongsTo(Address::class);
	}
}
