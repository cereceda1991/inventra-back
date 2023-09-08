<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Jenssegers\Mongodb\Eloquent\Model;

class Logo extends Model
{
    use HasFactory;

    protected $connection = 'mongodb';
    protected $collection = 'logos';

    protected $fillable = [
        'urlImg',
        'publicId',
    ];
    protected $casts = [
        'urlImg' => 'string',
        'publicId'=>'string',
    ];

    protected $hidden = [
        'publicId',
    ];
}
