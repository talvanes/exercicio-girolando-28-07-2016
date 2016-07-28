<?php

namespace App\Http\Controllers\Sistema;

use App\Entities\Product;
use App\Exceptions\CarrinhoException;
use App\Services\CarrinhoService;
use Illuminate\Http\Request;

use App\Http\Requests;
use App\Http\Controllers\Controller;

class CarrinhoController extends Controller
{
    protected $carrinhoService;

    /**
     * CarrinhoController constructor.
     * @param $carrinhoService
     */
    public function __construct(CarrinhoService $carrinhoService)
    {
        $this->carrinhoService = $carrinhoService;
    }


    public function index()
    {
        //$this->carrinhoService->clear();
        return view('content.carrinho.index', ['items' => $this->carrinhoService->getItems()]);
    }


    public function update(Product $product, Request $request)
    {
        try{
            $this->carrinhoService->add($product, $request->chartQty);
            return redirect()->back()->with('success', 'Produto adicionado ao carrinho');
        } catch (CarrinhoException $ce) {
            return redirect()->back()->withErrors(['error' => $ce->getMessage()]);
        } catch (\Exception $e){
            return redirect()->back()->withErrors(['error' => 'Houve uma falha ao adicionar o produto no carrinho']);
        }
    }


    public function store(Request $request)
    {
        try{
            $this->carrinhoService->finish();
            return redirect()->route('produto.index')->with('success', 'Compra concluída com sucesso!');
        } catch (CarrinhoException $ce) {
            return redirect()->back()->withErrors(['error' => $ce->getMessage()]);
        } catch (\Exception $e) {
            return redirect()->back()->withErrors(['error' => 'Houve uma falha ao finalizar sua compra!']);
        }
    }
}
