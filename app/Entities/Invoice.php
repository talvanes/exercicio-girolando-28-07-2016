<?php

namespace App\Entities;

use App\User;
use Illuminate\Database\Eloquent\Model;

class Invoice extends Model
{
    protected $table = 'invoice';
    protected $fillable = ['userId', 'invoicePrice', 'invoiceDate'];

    public function User()
    {
        return $this->belongsTo(User::class, 'userId');
    }
}
