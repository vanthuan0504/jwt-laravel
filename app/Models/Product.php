<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    use HasFactory;
    protected $fillable = [
        'slug',
        'name',
        'description',
        'base_price',
        'sale_percent',
        'meta_title',
        'meta_keywords',
        'meta_description',
        'category_id'
    ];
}
