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
    @foreach($produtos as $produto)
        <div class="col-md-4 text-center product-block">
            <div class="product-details">
                <img class="product-image" src="{!! $produto->productPhoto !!}"><br>
                @if($produto->productSpecial)
                    (especial)
                @endif
            </div>
            <h3>{!! $produto->productName !!}</h3>
            <h4>{!! $produto->productPrice !!}</h4>
            <div class="btn-group product-buttons col-md-12">
                <a class="btn btn-sm btn-primary col-md-4" href="{!! route('produto.show', $produto) !!}">Detalhes</a>
                <form action="{!! route('carrinho.update', $produto) !!}" method="post">
                    {!! csrf_field() !!}
                    <input type="hidden" name="_method" value="PUT">
                    <input type="hidden" name="chartQty" value="1">
                    <button class="btn btn-sm btn-default col-md-6" name="adicionar[{!! $produto->id !!}">+Carrinho</button>
                </form>
            </div>
        </div>
    @endforeach
    <hr>
    <div class="text-center">
        {!! $produtos->render() !!}
    </div>
@endsection