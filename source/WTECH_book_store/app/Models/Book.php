<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Book extends Model
{
    use HasFactory;

    protected $fillable = [
		'product_id',
		'language_id',
		'publisher',
		'year',
        'pages',
        'country',
        'isbn',
        'stock',
        'shipping_time',
        'detail',
        'long_detail'
	];

    public function product()
    {
        return $this->belongsTo(Product::class);
    }

    public function language()
    {
        return $this->belongsTo(Language::class);
    }
}
