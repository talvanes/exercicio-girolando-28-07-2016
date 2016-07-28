<?php namespace App\Tests\Carrinho;

use App\Entities\Chart;
use App\Entities\Product;
use App\User;
use Illuminate\Foundation\Testing\WithoutMiddleware;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use TestCase;

class PutTest extends TestCase
{
    use DatabaseTransactions;

    /**
     * 
     *
     * @return void
     */
    public function testInserindoProdutoViaIndex()
    {
        try {
            # usuário logado
            $usuario = factory(User::class)->create();
            # 3 produtos para adicionar
            $produtos = factory(Product::class, 3)->create();

            # carrinho de compras
            $carrinho = new Chart();

            # solicitar a rota 'produto.index'
            $response = $this->actingAs($usuario)
                ->visit(route('produto.index'));
            foreach ($produtos as $produto) {
                # pressionando o botão para adicionar ao carrinho
                $response->click('+Carrinho');
                # adicionano ao carrinho
                $this->put(route('carrinho.update', $produto), [
                    'userId' => $usuario->id, 'productId' => $produto->id,
                ]);
                # o que espero? ver a mensagem
                $this->assertSessionHas('success', trans('Services/Session/Carrinho.put.msgAddToCart'));
            }



        } catch (\Exception $e) {
            $this->assertTrue(false, "Exception: {$e->getMessage()} on file {$e->getFile()}, line no. {$e->getLine()}");
        }
    }
}
