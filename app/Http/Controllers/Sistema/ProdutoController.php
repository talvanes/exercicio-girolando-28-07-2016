<?php

namespace App\Http\Controllers\Sistema;

use App\Entities\Product;
use Illuminate\Http\Request;

use App\Http\Requests;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;

class ProdutoController extends Controller
{
    public function index()
    {
        return view('content.produto.index', ['produtos' => Product::where('productStock', '>', 0)->orderBy('id', 'desc')->paginate(9)]);
    }

    public function show(Product $product)
    {

        if(Auth::guest() && $product->productSpecial) abort(403);
        return view('content.produto.show', ['produto' => $product]);
    }
}
