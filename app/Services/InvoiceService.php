<?php
/**
 * Created by PhpStorm.
 * User: ansilva
 * Date: 27/07/2016
 * Time: 18:13
 */

namespace App\Services;


use App\Entities\Chart;
use App\Entities\Invoice;
use App\Entities\InvoiceItem;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class InvoiceService
{
    public function create(Collection $itens)
    {
        $valor = 0;
        foreach($itens as $item){
            $valor += $item->chartQty;
        }
        try {
            DB::beginTransaction();
            $invoice = Invoice::create(['userId' => Auth::id(), 'invoiceDate' => date('Y-m-d'), 'invoicePrice' => $valor]);
            foreach ($itens as $item) {
                InvoiceItem::create([
                    'invoiceId' => $invoice->id,
                    'itemReference' => $item->productId,
                    'itemDescription' => $item->Product->productName,
                    'itemUnitPrice' => $item->Product->productPrice,
                    'itemTotalPrice' => $item->Product->productPrice * $item->chartQty,
                    'itemQty' => $item->chartQty
                ]);
            }
            DB::commit();
        } catch (\Exception $e) {
            DB::rollback();
            throw $e;
        }
    }
}