<?php

/*
|--------------------------------------------------------------------------
| Application Routes
|--------------------------------------------------------------------------
|
| Here is where you can register all of the routes for an application.
| It's a breeze. Simply tell Laravel the URIs it should respond to
| and give it the controller to call when that URI is requested.
|
*/

Route::group(['middlewareGroups' => ['web']], function(){
    Route::get('/', ['uses' => 'Sistema\IndexController@index'])->name('sistema.index');
    Route::post('/', ['uses' => 'Sistema\IndexController@store'])->name('sistema.store');
    Route::get('/logout', ['uses' => 'Sistema\IndexController@destroy'])->name('sistema.logout');

    Route::get('/produtos', ['uses' => 'Sistema\ProdutoController@index'])->name('produto.index');
    Route::get('/produtos/{product}', ['uses' => 'Sistema\ProdutoController@show'])->name('produto.show');

    Route::get('/carrinho', ['uses' => 'Sistema\CarrinhoController@index'])->name('carrinho.index');
    Route::put('/carrinho/{product}', ['uses' => 'Sistema\CarrinhoController@update'])->name('carrinho.update');
    Route::delete('/carrinho', ['uses' => 'Sistema\CarrinhoController@destroy'])->name('carrinho.destroy');
    Route::post('/carrinho', ['uses' => 'Sistema\CarrinhoController@store'])->name('carrinho.store');
});