<?php

namespace App\Models;

use App\Helpers\GlobalApp;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Category extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'image'
    ];

    public function products()
    {
        return $this->hasMany(Product::class);
    }

    public function getImageAttribute($image)
    {
        return GlobalApp::viewImage($image, 'categories');
    }
}
