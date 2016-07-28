<?php

namespace App\Entities;

use App\User;
use Illuminate\Database\Eloquent\Model;

class Chart extends Model
{
    protected $table = 'chart';
    protected $fillable = ['userId', 'productId', 'chartQty'];

    public function User()
    {
        return $this->belongsTo(User::class, 'userId');
    }

    public function Product()
    {
        return $this->belongsTo(Product::class, 'productId');
    }
}
