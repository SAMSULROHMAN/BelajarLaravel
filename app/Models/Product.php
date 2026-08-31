<?php

namespace App\Models;

use App\Models\Category;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    use HasFactory;

    protected $fillable = [
        'category_id', 'name', 'description',
        'price', 'stock', 'image',
    ];

    public function category()
    {
        return $this->belongsTo(Category::class);
    }

    // Pencarian
    public function scopeSearch($query, $keyword)
    {
        return $query->when($keyword, fn ($q) =>
            $q->where('name', 'like', '%' . $keyword . '%')
        );
    }

    public function scopeCategory($query, $categoryId)
    {
        return $query->when($categoryId, fn($q) =>
            $q->where('category_id', $categoryId)
        );
    }
}
