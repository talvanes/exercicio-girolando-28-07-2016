<?php

namespace App\Entities;

use Illuminate\Database\Eloquent\Model;

class InvoiceItem extends Model
{
    protected $table = 'invoice_item';
    protected $fillable = ['invoiceId', 'itemReference', 'itemDescription', 'itemQty', 'itemUnitPrice', 'itemTotalPrice'];

    public function Invoice()
    {
        return $this->belongsTo(Invoice::class, 'invoiceId');
    }

    public function Product()
    {
        return $this->belongsTo(Product::class, 'itemReference');
    }
}
