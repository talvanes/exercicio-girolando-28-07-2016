<?php
/**
 * Created by PhpStorm.
 * User: ansilva
 * Date: 27/07/2016
 * Time: 18:12
 */

namespace App\Services;


use App\Entities\Chart;
use App\Entities\Product;
use App\Events\Invoice\InvoiceWasCreated;
use App\Exceptions\CarrinhoException;
use App\Exceptions\UserNotLoggedException;
use App\Jobs\PaymentJob;
use Illuminate\Contracts\Auth\Guard;
use Illuminate\Contracts\Events\Dispatcher;
use Illuminate\Contracts\Mail\Mailer;
use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Session\Store;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;

class CarrinhoService
{
    use DispatchesJobs;

    protected $invoiceService;
    protected $guard;
    protected $eventDispatcher;
    protected $mailer;
    protected $sessionManager;
    protected $sessionKey;

    /**
     * CarrinhoService constructor.
     * @param $invoiceService
     * @param $guard
     * @param $eventDispatcher
     * @param $mailer
     */
    public function __construct(InvoiceService $invoiceService, Guard $guard, Dispatcher $eventDispatcher, Mailer $mailer, Store $sessionManager)
    {
        $this->invoiceService = $invoiceService;
        $this->guard = $guard;
        $this->eventDispatcher = $eventDispatcher;
        $this->mailer = $mailer;
        $this->sessionManager = $sessionManager;
        $this->sessionKey = 'charts';
        $this->initialize();
    }


    private function initialize()
    {

    }


    private function getFromSession()
    {
        $itens = $this->sessionManager->get($this->sessionKey);
        $itens = json_decode($itens);
        return new Collection($itens);
    }

    private function storeOnSession(Chart $chart)
    {
        $itens = $this->getFromSession();
        //dd($itens);
        //checando a existencia do mesmo produto:
        $existente = null;
        foreach($itens as $key => $item){
            if($item->productId == $chart->productId){
                $item->chartQty = $chart->chartQty; //se já existe, faço um update da qtd pq posso ta add ou removendo

                $itens->put($key, $item);
                if($item->chartQty <= 0){
                    $itens->splice($key, 1);

                }
                $this->sessionManager->set($this->sessionKey, $itens->toJson());
                return true;
            }
        }

        $itens->push($chart);
        $itens = $itens->toJson();
        $this->sessionManager->set($this->sessionKey, $itens);
        return true;

    }


    public function add(Product $product, $qty = 1)
    {
        //validações:
        if($this->guard->guest() && $product->productSpecial) throw new CarrinhoException('Esse produto só pode ser adquirido por usuários logados!');
        if($product->productStock <= 0) throw new CarrinhoException('Produto sem estoque no momento!');


        $chart = new Chart();
        $chart->productId = $product->id;
        $chart->chartQty = $qty;
        if($this->guard->guest()) {
            $this->storeOnSession($chart);
            return true;
        }
        $existente = Chart::where('userId','=', $this->guard->id())->where('productId','=',$product->id)->first();
        if($existente){
            $chart = $existente;
            $chart->chartQty = $qty;
        }
        $chart->userId = $this->guard->id();

        $chart->save();
        return ($chart);
    }


    public function getCount()
    {
        //dd($this->sessionManager->get($this->sessionKey));
        if($this->guard->guest())
            return $this->getFromSession()->count();
        return Chart::where('userId','=',$this->guard->id())->selectRaw('sum(chartQty) as soma')->first()->soma;
    }


    public function getItems()
    {
        if($this->guard->guest()) {
            $itens = $this->getFromSession();
            foreach($itens as $key => $item){
                $itens->put($key, (new Chart())->fill((array) $item));
            }
            return $itens;
        }

        return Chart::where('userId','=',$this->guard->id())->get();
    }


    public function clear()
    {
        if($this->guard->guest()) return $this->sessionManager->set($this->sessionKey, new Collection());
        Chart::where('userId','=',$this->guard->id())->delete();
    }


    public function finish()
    {
        if($this->guard->guest()) throw new UserNotLoggedException();
        $invoice = $this->invoiceService->create($this->getItems());
        $this->eventDispatcher->fire(new InvoiceWasCreated($invoice));
        $this->dispatch(new PaymentJob($invoice));
        $this->clear();
    }


}