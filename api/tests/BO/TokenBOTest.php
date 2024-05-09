<?php
namespace App\Tests\BO;

use App\BO\Principal\TokenBO;
use App\Entity\Principal\Usuario;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

/**
 *
 * @author Marcelo Andre Naegeler
 */
class TokenBOTest extends KernelTestCase
{


    public function testValidarParametrosIncompletos ()
    {
        $parametros    = ['senha' => 'Teste'];
        $mensagem      = '';
        $podeAtualizar = TokenBO::podeAtualizarSenha(
            $mensagem,
            $parametros,
            new Usuario()
        );

        $this->assertEquals(
            'Campos senha e confirmar senha estão inválidos.',
            $mensagem
        );

        $this->assertFalse($podeAtualizar);
    }

    public function testValidarParametrosDiferentes ()
    {
        $parametros    = [
            'senha'          => 'Teste',
            'confirmarSenha' => 'teste',
        ];
        $mensagem      = '';
        $podeAtualizar = TokenBO::podeAtualizarSenha(
            $mensagem,
            $parametros,
            new Usuario()
        );

        $this->assertEquals(
            'Campos senha e confirmar senha estão inválidos.',
            $mensagem
        );

        $this->assertFalse($podeAtualizar);
    }

    public function testValidarParametrosUsuarioNull ()
    {
        $parametros    = [
            'senha'          => 'Teste',
            'confirmarSenha' => 'Teste',
        ];
        $mensagem      = '';
        $podeAtualizar = TokenBO::podeAtualizarSenha(
            $mensagem,
            $parametros,
            null
        );

        $this->assertEquals(
            'Usuário inválido.',
            $mensagem
        );

        $this->assertFalse($podeAtualizar);
    }

    public function testValidarParametrosUsuarioValido ()
    {
        $parametros    = [
            'senha'          => 'Teste',
            'confirmarSenha' => 'Teste',
        ];
        $mensagem      = '';
        $podeAtualizar = TokenBO::podeAtualizarSenha(
            $mensagem,
            $parametros,
            new Usuario()
        );

        $this->assertTrue($podeAtualizar);
    }


}
