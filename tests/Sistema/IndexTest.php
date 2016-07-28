<?php namespace App\Tests\Sistema;

use App\User;
use Illuminate\Foundation\Testing\WithoutMiddleware;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use TestCase;

class IndexTest extends TestCase
{
    use DatabaseTransactions;

    /**
     * Este teste espera que se exiba apenas o formuário de login com os campos de email e senha para preencher.
     *
     * @return void
     */
    public function testVisualizaFormulario()
    {
        try {
            # ao acessar a rota
            $this->get(route('sistema.index'));
            # espero ver o formulário de login com os campos para preencher
            $this->see('name="email"')
                ->see('name="password"');

        } catch (\Exception $e) {
            $this->assertTrue(false, "Exception: {$e->getMessage()} on file {$e->getFile()}, line no. {$e->getLine()}");
        }
    }

    /**
     * Neste caso, como usuário logado, espero ser redirecionado de sistema.index para produto.index
     *
     * @return void
     */
    public function testAcessoLogado()
    {
        try {
            # como usuário logado
            $usuario = factory(User::class)->create();

            # ao visitar a rota sistema.index
            $response = $this->actingAs($usuario)
                ->get(route('sistema.index'));
            # espero ser redirecionado para produto.index
            $response->assertRedirectedTo(route('produto.index'));

        } catch (\Exception $e) {
            $this->assertTrue(false, "Exception: {$e->getMessage()} on file {$e->getFile()}, line no. {$e->getLine()}");
        }



    }


}
