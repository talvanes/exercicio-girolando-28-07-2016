@extends('default')

@section('header')
    <style type="text/css">
        .product-image{
            max-width: 400px;
            max-height: 400px;
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
    <div class="col-md-4">
        <img src="{!! $produto->productPhoto !!}" class="product-image">
    </div>
    <div class="col-md-8">
        <h1>{!! $produto->productName !!}</h1>
        <form class="form form-horizontal" method="post" action="{!! route('carrinho.update', $produto) !!}">
            {!! csrf_field() !!}
            <input type="hidden" name="_method" value="PUT">
            <div class="form-group">
                <label class="col-md-2 form-label">Preço: </label>
                <div class="col-md-10">
                    <input type="text" value="{!!  $produto->productPrice !!}" class="form-control" readonly>
                </div>
            </div>

            {!! $produto->productDescription !!}
            <div class="input-group col-md-4 col-md-offset-8 text-right">
                <input class="form-control" type="number" name="chartQty" placeholder="Quantidade">
                <div class="input-group-btn">
                    <button class="btn btn-primary">+ Carrinho</button>
                </div>
            </div>
        </form>
    </div>
@endsection