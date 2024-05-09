<?php
namespace App\Tests\BO;

use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use App\Helper\FunctionHelper;
use App\BO\Principal\FuncionarioValorHoraBO;
use App\Helper\ConstanteParametros;

/**
 *
 * @author Luiz Antonio Costa
 */
class FuncionarioValorHoraBOTest extends KernelTestCase
{

    /**
     *
     * @var \App\BO\Principal\FuncionarioValorHoraBO
     */
    private $funcionarioValorHoraBO;

    /**
     *
     * @var \App\Repository\Principal\FuncionarioValorHoraRepository
     */
    private $funcionarioValorHoraRepository;

    protected function setUp()
    {
        $kernel = self::bootKernel();
        $this->funcionarioValorHoraBO         = new FuncionarioValorHoraBO($kernel->getContainer()->get('doctrine')->getManager());
        $this->funcionarioValorHoraRepository = $kernel->getContainer()->get('doctrine')->getManager()->getRepository(\App\Entity\Principal\FuncionarioValorHora::class);
    }

    protected function tearDown()
    {
        $this->funcionarioValorHoraBO         = null;
        $this->funcionarioValorHoraRepository = null;
    }

    public function testFuncionarioValorHoraExiste()
    {
        $objetoORM    = null;
        $mensagemErro = "";
        $retorno      = $this->funcionarioValorHoraBO->verificaFuncionarioValorHoraExiste($this->funcionarioValorHoraRepository, 1, $mensagemErro, $objetoORM);

        $this->assertTrue($retorno === true, FunctionHelper::mostrarTextoUnitVermelho("O teste foi programado para que o objeto exista, porem ele nao consta na base de dados."));
        $this->assertEmpty($mensagemErro, FunctionHelper::mostrarTextoUnitVermelho("O retorno esperado era para que nao contenha erros na variavel de mensagem porem obteve."));
        $this->assertEquals(\App\Entity\Principal\FuncionarioValorHora::class, get_class($objetoORM), FunctionHelper::mostrarTextoUnitVermelho("O retorno esperado era para que fosse um objeto."));
    }

    public function testFuncionarioValorHoraNaoExiste()
    {
        $objetoORM    = null;
        $mensagemErro = "";
        $retorno      = $this->funcionarioValorHoraBO->verificaFuncionarioValorHoraExiste($this->funcionarioValorHoraRepository, 99999, $mensagemErro, $objetoORM);

        $this->assertFalse($retorno === true, FunctionHelper::mostrarTextoUnitVermelho("O teste foi programado para que o retorno fosse FALSE porem o registro foi encontrado, do qual nao deveria existir."));
        $this->assertNotEmpty($mensagemErro, FunctionHelper::mostrarTextoUnitVermelho("O teste estava esperando que tivesse retorno da pesquisa, porem nao obteve."));
        $this->assertEquals(null, $objetoORM, FunctionHelper::mostrarTextoUnitVermelho("O retorno do objeto nao eh nulo"));
    }

    public function testPodeSalvarValido()
    {
        $mensagemErro = "";
        $parametros   = [
            ConstanteParametros::CHAVE_FUNCIONARIO => 1,
            ConstanteParametros::CHAVE_VALOR_HORA  => 1,
        ];

        $retorno = $this->funcionarioValorHoraBO->podeSalvar($parametros, $mensagemErro);

        $this->assertTrue($retorno === true, FunctionHelper::mostrarTextoUnitVermelho("O teste foi programado para que o objeto exista, porem ele nao consta na base de dados."));
        $this->assertEmpty($mensagemErro, FunctionHelper::mostrarTextoUnitVermelho("O retorno esperado era para que nao contenha erros na variavel de mensagem porem obteve."));
    }

    public function testPodeSalvarInvalido()
    {
        $mensagemErro = "";
        $parametros   = [
            ConstanteParametros::CHAVE_FUNCIONARIO => 999999,
            ConstanteParametros::CHAVE_VALOR_HORA  => 999999,
        ];

        $retorno = $this->funcionarioValorHoraBO->podeSalvar($parametros, $mensagemErro);
        $this->assertFalse($retorno === true, FunctionHelper::mostrarTextoUnitVermelho("O teste foi programado para que o retorno fosse FALSE porem o registro foi encontrado, do qual nao deveria existir."));
        $this->assertNotEmpty($mensagemErro, FunctionHelper::mostrarTextoUnitVermelho("O teste estava esperando que tivesse retorno da pesquisa, porem nao obteve."));
    }


}
