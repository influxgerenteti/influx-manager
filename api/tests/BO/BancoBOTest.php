<?php
namespace App\Tests\BO;

use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use App\Helper\FunctionHelper;
use App\BO\Principal\BancoBO;

/**
 *
 * @author Luiz Antonio Costa
 */
class BancoBOTest extends KernelTestCase
{

    /**
     *
     * @var \App\BO\Principal\BancoBO
     */
    private $bancoBO;

    protected function setUp()
    {
        $kernel        = self::bootKernel();
        $this->bancoBO = new BancoBO($kernel->getContainer()->get('doctrine')->getManager()->getRepository(\App\Entity\Principal\Banco::class));
    }

    protected function tearDown()
    {
        $this->bancoBO = null;
    }

    public function testVerificaBancoExiste()
    {
        $mensagemErro = "";
        $bancoORM     = null;
        $retorno      = $this->bancoBO->verificaBancoExiste(1, $mensagemErro, $bancoORM);

        $this->assertTrue($retorno === true, FunctionHelper::mostrarTextoUnitVermelho("Banco nao encontrado na base de dados.\n" . $mensagemErro));
        $this->assertEquals(\App\Entity\Principal\Banco::class, get_class($bancoORM), FunctionHelper::mostrarTextoUnitVermelho("O Retorno de objetos nao eh o mesmo que o esperado"));
    }

    public function testVerificaBancoNaoExiste()
    {
        $mensagemErro = "";
        $bancoORM     = null;
        $retorno      = $this->bancoBO->verificaBancoExiste(9999, $mensagemErro, $bancoORM);

        $this->assertFalse($retorno === true, FunctionHelper::mostrarTextoUnitVermelho("Banco foi encontrado na base, e isto nao poderia acontecer."));
        $this->assertEquals(null, $bancoORM, FunctionHelper::mostrarTextoUnitVermelho("O Retorno de objetos nao eh o mesmo que o esperado"));
        $this->assertEmpty($mensagemErro, FunctionHelper::mostrarTextoUnitVermelho("O retorno esperado deste teste eh para que tenha vindo um erro de base de dados"));
    }

    public function testVerificaPodeSalvarBancoExiste()
    {
        $mensagem   = "";
        $objetoORM  = null;
        $parametros = [
            "codigo"    => "000",
            "descricao" => "Escola",
        ];
        $retorno    = $this->bancoBO->podeSalvar($parametros, null, $mensagem, $objetoORM);

        $this->assertFalse($retorno === true, FunctionHelper::mostrarTextoUnitVermelho("O retorno esperado era para que existisse o dado no banco."));
        $this->assertNotEmpty($mensagem, FunctionHelper::mostrarTextoUnitVermelho("O teste ocorreu sem erros, porem, foi programado para ter erro."));
    }

    public function testVerificaPodeSalvarBancoNaoExiste()
    {
        $mensagem   = "";
        $objetoORM  = null;
        $parametros = [
            "codigo"    => "985",
            "descricao" => "Escola22",
        ];
        $retorno    = $this->bancoBO->podeSalvar($parametros, null, $mensagem, $objetoORM);

        $this->assertTrue($retorno === true, FunctionHelper::mostrarTextoUnitVermelho("O retorno esperado era para que existisse o dado no banco."));
        $this->assertEmpty($mensagem, FunctionHelper::mostrarTextoUnitVermelho("O teste ocorreu sem erros, porem, foi programado para ter erro."));
        $this->assertEquals(null, $objetoORM, FunctionHelper::mostrarTextoUnitVermelho("O Objeto retornado eh diferente do esperado"));
    }

    public function testVerificaPodeSalvarNomeExiste()
    {
        $mensagem   = "";
        $objetoORM  = null;
        $parametros = [
            "codigo"    => "985",
            "descricao" => "Escola",
        ];
        $retorno    = $this->bancoBO->podeSalvar($parametros, null, $mensagem, $objetoORM);

        $this->assertFalse($retorno === true, FunctionHelper::mostrarTextoUnitVermelho("O retorno esperado era para que existisse o dado no banco."));
        $this->assertNotEmpty($mensagem, FunctionHelper::mostrarTextoUnitVermelho("O teste ocorreu sem erros, porem, foi programado para ter erro."));
        $this->assertEquals(gettype([]), gettype($objetoORM), FunctionHelper::mostrarTextoUnitVermelho("O Objeto retornado eh diferente do esperado"));
    }

    public function testVerificaPodeSalvarNomeNaoExiste()
    {
        $mensagem   = "";
        $objetoORM  = null;
        $parametros = [
            "codigo"    => "985",
            "descricao" => "Teste4555",
        ];
        $retorno    = $this->bancoBO->podeSalvar($parametros, null, $mensagem, $objetoORM);

        $this->assertTrue($retorno === true, FunctionHelper::mostrarTextoUnitVermelho("O retorno esperado era para que existisse o dado no banco."));
        $this->assertEmpty($mensagem, FunctionHelper::mostrarTextoUnitVermelho("O teste ocorreu sem erros, porem, foi programado para ter erro."));
        $this->assertEquals(null, $objetoORM, FunctionHelper::mostrarTextoUnitVermelho("O Objeto retornado eh diferente do esperado"));
    }


}
