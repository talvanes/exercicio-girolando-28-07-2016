<?php namespace App\Tests\Carrinho;

use App\Entities\Product;
use App\Events\Invoice\InvoiceWasCreated;
use App\Exceptions\UserNotLoggedException;
use App\Jobs\PaymentJob;
use App\Services\CarrinhoService;
use App\User;
use Illuminate\Foundation\Testing\WithoutMiddleware;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Support\Facades\Session;
use Mockery;
use TestCase;

class UnitTest extends TestCase
{
    use DatabaseTransactions;
    
    /**
     *
     *
     * @return void
     */
    public function testeComUsuarioLogado()
    {
        try {
            # usuário logado
            $usuario = factory(User::class)->create();
            # produtos aleatórios com estoque
            $produtos = factory(Product::class, 3)->create(['productStock' => rand(1, 20)]);

            # carrinho service
            $addToCart = app(CarrinhoService::class);

            # solicitando a rota 'carrinho.update' a cada produto
            $response = $this->actingAs($usuario)
                ->put(route('carrinho.update', $produtos[0]))
                ->put(route('carrinho.update', $produtos[1]))
                ->put(route('carrinho.update', $produtos[2]));
            //echo print_r($response->response->getContent());

            # o que espero? os produtos no banco de dados
            $this->seeInDatabase('chart', ['userId' => $usuario->id, 'productId' => $produtos[0]->id]);
            $this->seeInDatabase('chart', ['userId' => $usuario->id, 'productId' => $produtos[1]->id]);
            $this->seeInDatabase('chart', ['userId' => $usuario->id, 'productId' => $produtos[2]->id]);

        } catch (\Exception $e) {
            $this->assertTrue(false, "Exception: {$e->getMessage()} on file {$e->getFile()}, line no. {$e->getLine()}");
        }
    }

    /**
     *
     *
     * @return void
     */
    public function testeSemUsuarioLogado()
    {
        try {
            $addToCart = app(CarrinhoService::class);
            $produtos = factory(Product::class, 3)->create(['productStock' => rand(1, 1000)]);

            Session::shouldReceive('put')->times(3);
            foreach ($produtos as $produto) {
                $this->put(route('carrinho.update', $produto));
            }

            # o que espero?
            $this->assertSessionHas(['chart' => $addToCart->getItems()]);

        } catch (\Exception $e) {
            $this->assertTrue(false, "Exception: {$e->getMessage()} on file {$e->getFile()}, line no. {$e->getLine()}");
        }
    }

    /**
     *
     *
     * @return void
     */
    public function testComEstoque()
    {
        try {
            $addToCart = app(CarrinhoService::class);

            $usuario = factory(User::class)->create();
            $produtos = factory(Product::class, 3)->create(['productStock' => rand(1, 1000)]);

            $response = null;
            foreach ($produtos as $produto){
                $this->actingAs($usuario)->put(route('carrinho.update', $produto));
            }

            # o que espero? uma collection com 3 ítens


        } catch (\Exception $e) {
            $this->assertTrue(false, "Exception: {$e->getMessage()} on file {$e->getFile()}, line no. {$e->getLine()}");
        }
    }

    /**
     *
     *
     * @return void
     */
    public function testEstoqueZerado()
    {
        try {
            $addToCart = app(CarrinhoService::class);
            $usuario = factory(User::class)->create();
            $produto = factory(Product::class)->create(['productStock' => rand(-5, 0)]);

            $this->actingAs($usuario)->put(route('carrinho.update', $produto));

            #
            # o que espero? uma exceção
            $this->setExpectedException('CarrinhoException');

        } catch (\Exception $e) {
            $this->assertTrue(false, "Exception: {$e->getMessage()} on file {$e->getFile()}, line no. {$e->getLine()}");
        }
    }

    /**
     *
     *
     * @return void
     */
    public function testLimparCarrinho()
    {
        try {
            $usuario = factory(User::class)->create();
            $produtos = factory(Product::class)->create(['productStock' => rand(1, 1000)]);

            $addToCart = app(CarrinhoService::class);
            foreach ($produtos as $produto) {
                $this->actingAs($usuario)->put(route('carrinho.update', $produto->id));
            }
            $addToCart->clear();

            $this->dontSeeInDatabase('chart', ['userId' => $usuario->id, 'productId' => $produtos[0]->id])
                ->dontSeeInDatabase('chart', ['userId' => $usuario->id, 'productId' => $produtos[0]->id])
                ->dontSeeInDatabase('chart', ['userId' => $usuario->id, 'productId' => $produtos[0]->id]);
            $this->seeInSession('chart', null);
            $this->assertNull($addToCart->getItems());

        } catch (\Exception $e) {
            $this->assertTrue(false, "Exception: {$e->getMessage()} on file {$e->getFile()}, line no. {$e->getLine()}");
        }
    }

    /**
     *
     *
     * @return void
     */
    public function testConcluirCompraDeslogado()
    {
        try {
            $addToCart = app(CarrinhoService::class);
            $produtos = factory(Product::class, 3)->create(['productStock' => rand(1, 2000)]);

            //$this->get(route('produto.index'));
            foreach ($produtos as $produto) {
                $addToCart->add($produto);
            }
            $addToCart->finish();

            $this->setExpectedException(UserNotLoggedException::class);
            $this->seeInSession('chart', $addToCart->getItems());


        } catch (\Exception $e) {
            $this->assertTrue(false, "Exception: {$e->getMessage()} on file {$e->getFile()}, line no. {$e->getLine()}");
        }
    }

    /**
     *
     *
     * @return void
     */
    public function testConcluirCompraLogadoJobsEvents()
    {
        try {
            $this->expectsEvents(InvoiceWasCreated::class);
            $this->expectsJobs(PaymentJob::class);

            $usuario = factory(User::class)->create();
            $produtos = factory(Product::class, 3)->create(['productStock' => rand(1, 2000)]);

            $addToCart = app(CarrinhoService::class);
            foreach ($produtos as $produto){
                $this->actingAs($usuario)->put('carrinho.update', $produto);
            }
            $addToCart->finish();

            $this->dontSeeInDatabase('chart', ['userId' => $usuario->id, 'productId' => $produtos[0]->id])
                ->dontSeeInDatabase('chart', ['userId' => $usuario->id, 'productId' => $produtos[1]->id])
                ->dontSeeInDatabase('chart', ['userId' => $usuario->id, 'productId' => $produtos[2]->id]);

            $this->seeInDatabase('invoice', ['userId' => $usuario->id, 'invoicePrice' => ( $produtos[0]->productPrice + $produtos[1]->productPrice + $produtos[2]->productPrice )]);

        } catch (\Exception $e) {
            $this->assertTrue(false, "Exception: {$e->getMessage()} on file {$e->getFile()}, line no. {$e->getLine()}");
        }
    }

    /**
     *
     *
     * @return void
     */
    public function testConcluirCompraLogadoMails()
    {
        try {
            $addToCart = app(CarrinhoService::class);

            $usuario = factory(User::class)->create();
            $produtos = factory(Product::class)->create(['productStock' => rand(1, 2000)]);

            foreach ($produtos as $produto) {
                $this->actingAs($usuario)->put(route('carrinho.update', $produto->id));
            }
            $addToCart->finish();

            # TODO: envio de dois emails
            $this->dontSeeInDatabase('chart', ['userId' => $usuario->id, 'productId' => $produtos[0]->id])
                ->dontSeeInDatabase('chart', ['userId' => $usuario->id, 'productId' => $produtos[1]->id])
                ->dontSeeInDatabase('chart', ['userId' => $usuario->id, 'productId' => $produtos[2]->id]);
            $this->seeInDatabase('invoice', ['userId' => $usuario->id, 'invoicePrice' => ( $produtos[0]->productPrice + $produtos[1]->productPrice + $produtos[2]->productPrice )]);

        } catch (\Exception $e) {
            $this->assertTrue(false, "Exception: {$e->getMessage()} on file {$e->getFile()}, line no. {$e->getLine()}");
        }
    }


}
