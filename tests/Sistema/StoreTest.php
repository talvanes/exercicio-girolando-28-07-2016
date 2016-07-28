<?php namespace App\Tests\Sistema;

use App\User;
use Illuminate\Foundation\Testing\WithoutMiddleware;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use TestCase;

class StoreTest extends TestCase
{
    use DatabaseTransactions;

    /**
     * Este teste deveria impedir autenticação de usuários que informem email ou senha inválida ou em branco.
     *
     * @return void
     */
    public function testLogandoErrado()
    {
        $emailsInvalidos = [null, '', str_random(rand(6,15)) . '@'];
        $senhasInvalidas = [null, ''];

        try {
            # acessar a rota
            $this->visit(route('sistema.index'))
            # informar os campos
                ->type($emailsInvalidos[rand(0,2)], 'email')
                ->type($senhasInvalidas[rand(0,1)], 'password')
            # e pressionar o botão Login
                ->press('Login');
            #echo print_r($response->response->getContent(), true);

            # o que espero? continuar na index
            $this->assertRedirectedTo(route('sistema.store'))
                ->followRedirects();
            #  ver os dizeres
            $this->see(trans('Services/Exceptions/Sistema.store.exceptionEmailSenhaInvalidos'));

        } catch (\Exception $e) {
            $this->assertTrue(false, "Exception: {$e->getMessage()} on file {$e->getFile()}, line no. {$e->getLine()}");
        }
    }

    /**
     * Este teste deveria autenticar usuários que informaram corretamente suas credenciais
     *
     * @return void
     */
    public function testLogandoCerto()
    {
        $senha = str_random(rand(5,20));    # senha de usuário

        try {
            # o usuário existe e está tentando se autenticar
            $usuario = factory(User::class)->create(['password' => bcrypt($senha)]);

            # ao acessar a rota sistema.index
            $this->visit(route('sistema.index'))
                # vou informar os campos certos
                ->type($usuario->email, 'email')->type($senha, 'password')
                # e clicar no Logar
                ->press('Login');
            #echo print_r($response->response->getContent(), true);

            # o que espero? ser redirecionado para produto.index e ver o nome de usuário na página
            $this->assertRedirectedTo(route('produto.index'))
                ->followRedirects();
            $this->see($usuario->name);

        } catch (\Exception $e) {
            $this->assertTrue(false, "Exception: {$e->getMessage()} on file {$e->getFile()}, line no. {$e->getLine()}");
        }
    }


}
