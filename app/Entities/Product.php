<?php

namespace App\Entities;

use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    protected $table = 'product';
    protected $fillable = ['productName', 'productPrice', 'productDescription', 'productPhoto', 'productStock', 'productSpecial'];

    public function getProductPhotoAttribute($photo)
    {
        if(!$photo) $photo = '1.jpg';
        return url('/imagens/produtos/'.$photo);
    }
}
