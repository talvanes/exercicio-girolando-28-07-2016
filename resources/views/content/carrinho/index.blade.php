@extends('default')

@section('header')
    <style type="text/css">
        .product-image{
            max-width: 200px;
            max-height: 200px;
        }
        .product-details{
            min-height: 250px;
            line-height: 250px;
            vertical-align: middle;
        }
        .product-buttons{
            text-align: center;
        }
    </style>
@endsection

@section('content')

    <h1>Seu carrinho de compras</h1>
    @foreach($items as $item)
        <div class="row">
            <div class="col-md-9 product-block">
                <div class="product-details">
                    <img class="product-image" src="{!! $item->Product->productPhoto !!}"><br>
                </div>
                <h3>{!! $item->Product->productName !!}</h3>
            </div>

            <div class="col-md-3 input-group">
                <form action="{!! route('carrinho.update', $item->Product) !!}" method="post" class="form form-horizontal">
                    <input type="hidden" name="_method" value="PUT">
                    {!! csrf_field() !!}
                    <input type="number" readonly class="input form-control" name="chartQty[{!! $item->productId !!}" value="{!! $item->chartQty !!}">
                    <div class="input-group-btn">
                        <button class="btn btn-primary" name="chartQty" value="{!! $item->chartQty+1 !!}">+</button>
                        <button class="btn btn-primary" name="chartQty" value="{!! $item->chartQty-1 !!}">-</button>
                        <button class="btn btn-primary" name="chartQty" value="0">Remover</button>
                    </div>
                </form>
            </div>
        </div>
    @endforeach

    <form action="{!! route('carrinho.store') !!}" method="post" class="form form-horizontal">
        {!! csrf_field() !!}
        <div class="btn-group col-md-3 col-md-offset-9">
            <button class="btn btn-primary col-md-12">Finalizar Compra</button>
        </div>

    </form>
@endsection