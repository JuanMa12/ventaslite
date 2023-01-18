<?php

namespace App\Models;

use App\Helpers\GlobalApp;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'barcode',
        'cost',
        'price',
        'stock',
        'alerts',
        'image',
        'category_id'
    ];

    public function category()
    {
        return $this->belongsTo(Category::class);
    }

    public function getImageAttribute($image)
    {
        return GlobalApp::viewImage($image, 'categories');
    }
}
