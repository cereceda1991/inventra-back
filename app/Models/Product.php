<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Jenssegers\Mongodb\Eloquent\Model;

class Product extends Model
{

    use HasFactory;

    protected $connection = 'mongodb';
    protected $collection = 'products';

    protected $fillable = [
        'SKU',
        'description',
        'category',
        'unit',
        'image_url',
        'price',
        'stock',
        'stock_min',
        'stock_max',
    ];

    /**
     * Default values for stock_min and stock_max.
     *
     * @var array
     */
    protected $attributes = [
        'stock_min' => 5,
        'stock_max' => 1000,
        'image_url'=>'https://res.cloudinary.com/dtfjbprru/image/upload/v1694361487/vsikwufi6lupa4clg9m0.png'
    ];
}
