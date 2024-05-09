<?php
namespace App\Tests\BO;

use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use App\Helper\FunctionHelper;
use App\Helper\ConstanteParametros;
use App\BO\Principal\FuncionarioDisponibilidadeBO;
use App\Entity\Principal\FuncionarioDisponibilidade;

/**
 *
 * @author Luiz Antonio Costa
 */
class FuncionarioDisponibilidadeBOTest extends KernelTestCase
{

    /**
     *
     * @var \App\BO\Principal\FuncionarioDisponibilidadeBO
     */
    private $funcionarioDisponibilidadeBO;

    /**
     *
     * @var \App\Repository\Principal\FuncionarioDisponibilidadeRepository
     */
    private $funcionarioDisponibilidadeRepository;

    protected function setUp()
    {
        $kernel = self::bootKernel();
        $this->funcionarioDisponibilidadeBO         = new FuncionarioDisponibilidadeBO($kernel->getContainer()->get('doctrine')->getManager());
        $this->funcionarioDisponibilidadeRepository = $kernel->getContainer()->get('doctrine')->getManager()->getRepository(\App\Entity\Principal\FuncionarioDisponibilidade::class);
    }

    protected function tearDown()
    {
        $this->funcionarioDisponibilidadeBO         = null;
        $this->funcionarioDisponibilidadeRepository = null;
    }

    public function testFuncionarioDisponibilidadeExiste()
    {
        $objetoORM    = null;
        $mensagemErro = "";
        $retorno      = $this->funcionarioDisponibilidadeBO->verificaFuncionarioDisponibilidadeExiste($this->funcionarioDisponibilidadeRepository, 1, $mensagemErro, $objetoORM);

        $this->assertTrue($retorno === true, FunctionHelper::mostrarTextoUnitVermelho("O teste foi programado para que o objeto exista, porem ele nao consta na base de dados."));
        $this->assertEmpty($mensagemErro, FunctionHelper::mostrarTextoUnitVermelho("O retorno esperado era para que nao contenha erros na variavel de mensagem porem obteve."));
        $this->assertEquals(\App\Entity\Principal\FuncionarioDisponibilidade::class, get_class($objetoORM), FunctionHelper::mostrarTextoUnitVermelho("O retorno esperado era para que fosse um objeto."));
    }

    public function testFuncionarioDisponibilidadeNaoExiste()
    {
        $objetoORM    = null;
        $mensagemErro = "";
        $retorno      = $this->funcionarioDisponibilidadeBO->verificaFuncionarioDisponibilidadeExiste($this->funcionarioDisponibilidadeRepository, 99999, $mensagemErro, $objetoORM);

        $this->assertFalse($retorno === true, FunctionHelper::mostrarTextoUnitVermelho("O teste foi programado para que o retorno fosse FALSE porem o registro foi encontrado, do qual nao deveria existir."));
        $this->assertNotEmpty($mensagemErro, FunctionHelper::mostrarTextoUnitVermelho("O teste estava esperando que tivesse retorno da pesquisa, porem nao obteve."));
        $this->assertEquals(null, $objetoORM, FunctionHelper::mostrarTextoUnitVermelho("O retorno do objeto nao eh nulo"));
    }

    public function testPodeSalvarValido()
    {
        $mensagemErro = "";
        $parametros   = [
            ConstanteParametros::CHAVE_FUNCIONARIO  => 1,
            ConstanteParametros::CHAVE_HORA_INICIAL => "2018-10-26T13:16:11.835Z",
            ConstanteParametros::CHAVE_HORA_FINAL   => "2018-10-26T13:16:11.835Z",
        ];

        $retorno = $this->funcionarioDisponibilidadeBO->podeSalvar($parametros, $mensagemErro);

        $this->assertTrue($retorno === true, FunctionHelper::mostrarTextoUnitVermelho("O teste foi programado para que o objeto exista, porem ele nao consta na base de dados."));
        $this->assertEmpty($mensagemErro, FunctionHelper::mostrarTextoUnitVermelho("O retorno esperado era para que nao contenha erros na variavel de mensagem porem obteve."));
    }

    public function testPodeSalvarInvalido()
    {
        $mensagemErro = "";
        $parametros   = [
            ConstanteParametros::CHAVE_FUNCIONARIO  => 1,
            ConstanteParametros::CHAVE_HORA_INICIAL => "2018-10-26T13:16:11.835Z",
            ConstanteParametros::CHAVE_HORA_FINAL   => "2018-10-26T13:16:10.835Z",
        ];

        $retorno = $this->funcionarioDisponibilidadeBO->podeSalvar($parametros, $mensagemErro);
        $this->assertFalse($retorno === true, FunctionHelper::mostrarTextoUnitVermelho("O teste foi programado para que o retorno fosse FALSE porem o registro foi encontrado, do qual nao deveria existir."));
        $this->assertNotEmpty($mensagemErro, FunctionHelper::mostrarTextoUnitVermelho("O teste estava esperando que tivesse retorno da pesquisa, porem nao obteve."));
    }


    public function testPodeAlterarValido()
    {
        $mensagemErro = "";
        $parametros   = [ConstanteParametros::CHAVE_HORA_INICIAL => "2018-10-26T13:15:11.835Z"];
        $objetoORM    = new FuncionarioDisponibilidade();
        $dataFinal    = "";
        \App\Helper\FunctionHelper::formataCampoDateTimeJS("2018-10-26T13:16:13.835Z", $dataFinal);
        $objetoORM->setHoraFinal($dataFinal);
        $retorno = $this->funcionarioDisponibilidadeBO->podeAlterar($parametros, $mensagemErro, $objetoORM);
        $this->assertTrue($retorno === true, FunctionHelper::mostrarTextoUnitVermelho("O teste foi programado para que o objeto exista, porem ele nao consta na base de dados."));
        $this->assertEquals(\DateTime::class, get_class($objetoORM->getHoraInicial()), FunctionHelper::mostrarTextoUnitVermelho("O retorno esperado era para que fosse um objeto."));
        $this->assertEquals(\DateTime::class, get_class($objetoORM->getHoraFinal()), FunctionHelper::mostrarTextoUnitVermelho("O retorno esperado era para que fosse um objeto."));
    }

    public function testPodeAlterarInvalido()
    {
        $mensagemErro = "";
        $parametros   = [ConstanteParametros::CHAVE_HORA_INICIAL => "2018-10-26T13:16:15.835Z"];
        $objetoORM    = new FuncionarioDisponibilidade();
        $dataFinal    = "";
        \App\Helper\FunctionHelper::formataCampoDateTimeJS("2018-10-26T13:16:13.835Z", $dataFinal);
        $objetoORM->setHoraFinal($dataFinal);
        $retorno = $this->funcionarioDisponibilidadeBO->podeAlterar($parametros, $mensagemErro, $objetoORM);
        $this->assertFalse($retorno === true, FunctionHelper::mostrarTextoUnitVermelho("O teste foi programado para que o objeto exista, porem ele nao consta na base de dados."));
    }


}
