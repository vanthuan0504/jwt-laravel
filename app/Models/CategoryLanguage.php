<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CategoryLanguage extends Model
{
    use HasFactory;
    protected $fillable = [
        'name',
        'description',
        'language_code',
        'language_id',
        'category_id'
    ];
}
