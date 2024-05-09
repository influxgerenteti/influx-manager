<?php
namespace App\Tests\BO;

use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use App\Helper\FunctionHelper;
use App\BO\Principal\ContaBO;

/**
 *
 * @author Luiz Antonio Costa
 */
class ContaBOTest extends KernelTestCase
{

    /**
     *
     * @var \App\Repository\Principal\ContaRepository
     */
    private $contaRepository;

    /**
     *
     * @var \App\BO\Principal\ContaBO
     */
    private $contaBO;

    protected function setUp()
    {
        $kernel = self::bootKernel();
        $this->contaRepository = $kernel->getContainer()->get('doctrine')->getManager()->getRepository(\App\Entity\Principal\Conta::class);
        $this->contaBO         = new ContabO($kernel->getContainer()->get('doctrine')->getManager());
    }

    protected function tearDown()
    {
        $this->contaRepository = null;
        $this->contaBO         = null;
    }

    public function testPodeSalvar()
    {
        $parametros   = [
            "banco"      => 1,
            "franqueada" => 1,
        ];
        $mensagemErro = "";
        $retorno      = $this->contaBO->podeSalvar($parametros, $mensagemErro);
        $this->assertTrue(true === $retorno, FunctionHelper::mostrarTextoUnitVermelho("O teste para realizar a gravacao de dados falhou"));
        $this->assertEmpty($mensagemErro, FunctionHelper::mostrarTextoUnitVermelho("O teste de verificar se a mensagem veio vazia nao passou"));
    }

    public function testNaoPodeSalvar()
    {
        $parametros   = [
            "banco"      => 1999999,
            "franqueada" => 2999999,
        ];
        $mensagemErro = "";
        $retorno      = $this->contaBO->podeSalvar($parametros, $mensagemErro);

        $this->assertFalse(true === $retorno, FunctionHelper::mostrarTextoUnitVermelho("O teste para realizar a gravacao de dados deu sucesso, porem foi programado para nao dar sucesso"));
        $this->assertNotEmpty($mensagemErro, FunctionHelper::mostrarTextoUnitVermelho("O teste de verificar se a mensagem nao veio vazia nao passou"));
    }

    public function testPodeSalvarFlagAtualizar()
    {
        $parametros   = [
            "banco"      => 1,
            "franqueada" => 1,
        ];
        $mensagemErro = "";
        $retorno      = $this->contaBO->podeSalvar($parametros, $mensagemErro, true);

        $this->assertTrue(true === $retorno, FunctionHelper::mostrarTextoUnitVermelho("O teste para realizar a gravacao de dados falhou"));
        $this->assertEmpty($mensagemErro, FunctionHelper::mostrarTextoUnitVermelho("O teste de verificar se a mensagem veio vazia nao passou"));
    }

    public function testNaoPodeSalvarFlagAtualizar()
    {
        $parametros   = [
            "banco"      => 99,
            "franqueada" => 95959959,
        ];
        $mensagemErro = "";
        $retorno      = $this->contaBO->podeSalvar($parametros, $mensagemErro);

        $this->assertFalse(true === $retorno, FunctionHelper::mostrarTextoUnitVermelho("O teste para realizar a gravacao de dados deu certo, porem nao era para ter dado."));
        $this->assertNotEmpty($mensagemErro, FunctionHelper::mostrarTextoUnitVermelho("O teste foi programado para causar erro, porem deu certo."));
    }

    public function testContaExiste()
    {
        $mensagemErro = "";
        $objetoORM    = null;
        $retorno      = $this->contaBO->verificaContaIdExiste($this->contaRepository, 1, $mensagemErro, $objetoORM);

        $this->assertTrue(true === $retorno, FunctionHelper::mostrarTextoUnitVermelho(""));
        $this->assertEmpty($mensagemErro, FunctionHelper::mostrarTextoUnitVermelho(""));
        $this->assertEquals(\App\Entity\Principal\Conta::class, get_class($objetoORM), FunctionHelper::mostrarTextoUnitVermelho(""));
    }

    public function testContaNaoExiste()
    {
        $mensagemErro = "";
        $objetoORM    = null;
        $retorno      = $this->contaBO->verificaContaIdExiste($this->contaRepository, 9999, $mensagemErro, $objetoORM);

        $this->assertFalse(true === $retorno, FunctionHelper::mostrarTextoUnitVermelho(""));
        $this->assertNotEmpty($mensagemErro, FunctionHelper::mostrarTextoUnitVermelho(""));
        $this->assertEquals(null, $objetoORM, FunctionHelper::mostrarTextoUnitVermelho(""));
    }


}
