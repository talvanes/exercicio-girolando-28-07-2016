<?php namespace App\Tests\Produto;

use App\Entities\Product;
use App\User;
use Illuminate\Foundation\Testing\WithoutMiddleware;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use TestCase;

class ShowTest extends TestCase
{
    use DatabaseTransactions;

    /**
     * Este teste deveria exibir os dados (exceto id) referentes a um produto específico
     *
     * @return void
     */
    public function testVisualizarProduto()
    {
        try {
            # usuário logado
            $usuario = factory(User::class)->create();

            # produto aleatório
            $produto = factory(Product::class)->create(['productSpecial' => 1]);

            # solicitando a rota 'produto.show'
            $this->actingAs($usuario)
                ->get(route('produto.show', $produto->id));
            # o que espero? ver nome, descrição e foto, mas não o id
            $this->see($produto->productName)
                ->see($produto->productPhoto)
                ->see($produto->productDescription);
            # FIXME: não posso ver o id!

        }catch (\Exception $e){
            $this->assertTrue(false, "Exception {$e->getMessage()} on file {$e->getFile()}, line no. {$e->getLine()}");
        }
    }

    /**
     * Este teste deveria negar acesso à página product.show a visitante, mostrando-lhe erro 403 (forbidden)
     *
     * @return void
     */
    public function testVisualizar403()
    {
        try {
            # produto aleatório (este existe)
            $produto = factory(Product::class)->create(['productSpecial' => 1]);

            # acessando a rota produto.show como visitante
            $response = $this->get(route('produto.show', $produto->id));
            # o que spero? erro 403
            $this->assertEquals(403, $response->response->getStatusCode());

        }catch (\Exception $e){
            $this->assertTrue(false, "Exception {$e->getMessage()} on file {$e->getFile()}, line no. {$e->getLine()}");
        }
    }

    /**
     * Este teste deveria exibir página 404 para produtos que não existem no banco de dados
     *
     * @return void
     */
    public function testVisualizar404()
    {
        try {
            # última inserção
            $lastInsert = Product::orderBy('id', 'desc')->first() ? Product::orderBy('id', 'desc')->first() + 1 : 1;
            # este código de produto não existe
            $productId = $lastInsert + 1;

            # solicitando a rota 'produto.show'
            $response = $this->get(route('produto.show', $productId));
            # o que espero? erro 404
            $this->assertEquals(404, $response->response->getStatusCode());

        }catch (\Exception $e){
            $this->assertTrue(false, "Exception {$e->getMessage()} on file {$e->getFile()}, line no. {$e->getLine()}");
        }
    }


}
