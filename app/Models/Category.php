<?php

namespace App\Models;

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
        $path_url = 'storage/categories/';
        if (file_exists($path_url . $image) && !is_null($image))
            return $path_url . $image;
        else
            return '/assets/img/200x200.jpg';
    }
}
