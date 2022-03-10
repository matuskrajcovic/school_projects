<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Merchandice extends Model
{
    use HasFactory;

    protected $fillable = [
		'product_id',
		'stock',
		'shipping_time',
		'detail'
	];

    public function product()
    {
        return $this->belongsTo(Product::class);
    }
}
