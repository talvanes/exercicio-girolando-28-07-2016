<?php namespace App\Tests\Produto;

use App\Entities\Product;
use App\User;
use Illuminate\Foundation\Testing\WithoutMiddleware;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use TestCase;

class IndexTest extends TestCase
{
    use DatabaseTransactions;
    
    /**
     * Neste teste, o visitante deveria visualizar os produtos cujo productSpecial = 0
     *
     * @return void
     */
    public function testVisualizarProdutos()
    {
        try {
            # eis 9 produtos (productSpecial=0)
            $produtos = factory(Product::class, 9)->create(['productSpecial' => 0]);

            # como usuário visitante, preciso ver apenas os produtos com a prop. productSpecial = 0
            $response = $this->get(route('produto.index'));
            # preciso ver os 9 produtos
            //$response->assertViewHas('produtos');
            foreach ($produtos as $produto){
                $response->see($produto->productPhoto)
                    ->see($produto->productName)
                    ->see($produto->productPrice);
            }
            # e o link de paginação
            $response->see('<ul class="pagination">');


        } catch (\Exception $e) {
            $this->assertTrue(false, "Exception: {$e->getMessage()} on file {$e->getFile()}, line no. {$e->getLine()}");
        }
    }

    /**
     * Neste teste, o usuário logado deveria ver todos os produtos, inclusive as fotos
     *
     * @return void
     */
    public function testVisualizarProdutosLogado()
    {
        try {
            # eis 9 produtos
            $produtos = factory(Product::class, 9)->create();

            # usuário
            $usuario = factory(User::class)->create();

            $response = $this->actingAs($usuario)
                ->get(route('produto.index'));
            # o que espero? ver os 9 produtos, nome, preço e foto de cada um, e
            foreach ($produtos as $produto){
                $response->see($produto->productPhoto)->see($produto->productName)->see($produto->productPrice);
                if ($produto->productSpecial) $response->see('(especial)');
            }
            # link de paginação
            $response->see('<ul class="navigation">');
            
        } catch (\Exception $e) {
            $this->assertTrue(false, "Exception: {$e->getMessage()} on file {$e->getFile()}, line no. {$e->getLine()}");
        }
    }


}
