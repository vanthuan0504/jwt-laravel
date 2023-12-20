<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ProductLanguage extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'description',
        'meta_title',
        'meta_keywords',
        'meta_description',
        'language_code',
        'language_id',
        'product_id'
    ];
}
