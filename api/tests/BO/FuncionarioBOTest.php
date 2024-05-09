<?php
namespace App\Tests\BO;

use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use App\Helper\FunctionHelper;
use App\Helper\ConstanteParametros;
use App\BO\Principal\FuncionarioBO;
use App\Entity\Principal\Funcionario;

/**
 *
 * @author Luiz Antonio Costa
 */
class FuncionarioBOTest extends KernelTestCase
{

    /**
     *
     * @var \App\BO\Principal\FuncionarioBO
     */
    private $funcionarioBO;

    /**
     *
     * @var \App\Repository\Principal\FuncionarioRepository
     */
    private $funcionarioRepository;

    protected function setUp()
    {
        $kernel = self::bootKernel();
        $this->funcionarioBO         = new FuncionarioBO($kernel->getContainer()->get('doctrine')->getManager());
        $this->funcionarioRepository = $kernel->getContainer()->get('doctrine')->getManager()->getRepository(\App\Entity\Principal\Funcionario::class);
    }

    protected function tearDown()
    {
        $this->funcionarioBO         = null;
        $this->funcionarioRepository = null;
    }

    public function testFuncionarioExiste()
    {
        $objetoORM    = null;
        $mensagemErro = "";
        $retorno      = $this->funcionarioBO->verificaFuncionarioExiste($this->funcionarioRepository, 1, $mensagemErro, $objetoORM);

        $this->assertTrue($retorno === true, FunctionHelper::mostrarTextoUnitVermelho("O teste foi programado para que o objeto exista, porem ele nao consta na base de dados."));
        $this->assertEmpty($mensagemErro, FunctionHelper::mostrarTextoUnitVermelho("O retorno esperado era para que nao contenha erros na variavel de mensagem porem obteve."));
        $this->assertEquals(\App\Entity\Principal\Funcionario::class, get_class($objetoORM), FunctionHelper::mostrarTextoUnitVermelho("O retorno esperado era para que fosse um objeto."));
    }

    public function testFuncionarioNaoExiste()
    {
        $objetoORM    = null;
        $mensagemErro = "";
        $retorno      = $this->funcionarioBO->verificaFuncionarioExiste($this->funcionarioRepository, 99999, $mensagemErro, $objetoORM);

        $this->assertFalse($retorno === true, FunctionHelper::mostrarTextoUnitVermelho("O teste foi programado para que o retorno fosse FALSE porem o registro foi encontrado, do qual nao deveria existir."));
        $this->assertNotEmpty($mensagemErro, FunctionHelper::mostrarTextoUnitVermelho("O teste estava esperando que tivesse retorno da pesquisa, porem nao obteve."));
        $this->assertEquals(null, $objetoORM, FunctionHelper::mostrarTextoUnitVermelho("O retorno do objeto nao eh nulo"));
    }

    public function testPodeSalvarValido()
    {
        $mensagemErro = "";
        $parametros   = [
            ConstanteParametros::CHAVE_PESSOA     => 1,
            ConstanteParametros::CHAVE_FRANQUEADA => 1,
            ConstanteParametros::CHAVE_CARGO      => 10,
            ConstanteParametros::CHAVE_BANCO      => 1,
        ];
        $dummyObj     = new Funcionario();
        \App\Helper\VariaveisCompartilhadas::$franqueadaID = 1;

        $retorno = $this->funcionarioBO->podeSalvar($parametros, $mensagemErro, $dummyObj);

        $this->assertTrue($retorno === true, FunctionHelper::mostrarTextoUnitVermelho("O teste foi programado para que o objeto exista, porem ele nao consta na base de dados."));
        $this->assertEmpty($mensagemErro, FunctionHelper::mostrarTextoUnitVermelho("O retorno esperado era para que nao contenha erros na variavel de mensagem porem obteve."));
    }

    public function testPodeSalvarInvalido()
    {
        $mensagemErro = "";
        $parametros   = [
            ConstanteParametros::CHAVE_PESSOA     => 1,
            ConstanteParametros::CHAVE_FRANQUEADA => 1,
            ConstanteParametros::CHAVE_CARGO      => 1,
            ConstanteParametros::CHAVE_BANCO      => 9999999999999,
        ];

        $dummyObj = new Funcionario();

        $retorno = $this->funcionarioBO->podeSalvar($parametros, $mensagemErro, $dummyObj);
        $this->assertFalse($retorno === true, FunctionHelper::mostrarTextoUnitVermelho("O teste foi programado para que o retorno fosse FALSE porem o registro foi encontrado, do qual nao deveria existir."));
        $this->assertNotEmpty($mensagemErro, FunctionHelper::mostrarTextoUnitVermelho("O teste estava esperando que tivesse retorno da pesquisa, porem nao obteve."));
    }


    public function testPodeAlterarValido()
    {
        $mensagemErro = "";
        $parametros   = [
            ConstanteParametros::CHAVE_NIVEL_INSTRUTOR => 1,
            ConstanteParametros::CHAVE_CARGO           => 1,
            ConstanteParametros::CHAVE_BANCO           => 1,
        ];
        $objetoORM    = new Funcionario();
        $retorno      = $this->funcionarioBO->podeAlterar($parametros, $mensagemErro, $objetoORM);
        $this->assertTrue($retorno === true, FunctionHelper::mostrarTextoUnitVermelho("O teste foi programado para que o objeto exista, porem ele nao consta na base de dados."));
        $this->assertEquals(\App\Entity\Principal\NivelInstrutor::class, get_class($objetoORM->getNivelInstrutor()), FunctionHelper::mostrarTextoUnitVermelho("O retorno esperado era para que fosse um objeto."));
        $this->assertEquals(\App\Entity\Principal\Cargo::class, get_class($objetoORM->getCargo()), FunctionHelper::mostrarTextoUnitVermelho("O retorno esperado era para que fosse um objeto."));
        $this->assertEquals(\App\Entity\Principal\Banco::class, get_class($objetoORM->getBanco()), FunctionHelper::mostrarTextoUnitVermelho("O retorno esperado era para que fosse um objeto."));
    }

    public function testPodeAlterarInvalido()
    {
        $mensagemErro = "";
        $parametros   = [
            ConstanteParametros::CHAVE_NIVEL_INSTRUTOR => 999999,
            ConstanteParametros::CHAVE_CARGO           => 999999,
            ConstanteParametros::CHAVE_BANCO           => 999999,
        ];
        $objetoORM    = new Funcionario();
        $retorno      = $this->funcionarioBO->podeAlterar($parametros, $mensagemErro, $objetoORM);
        $this->assertFalse($retorno === true, FunctionHelper::mostrarTextoUnitVermelho("O teste foi programado para que o objeto exista, porem ele nao consta na base de dados."));
        $this->assertEquals(null, $objetoORM->getNivelInstrutor(), FunctionHelper::mostrarTextoUnitVermelho("O retorno do objeto nao eh nulo"));
        $this->assertEquals(null, $objetoORM->getCargo(), FunctionHelper::mostrarTextoUnitVermelho("O retorno do objeto nao eh nulo"));
        $this->assertEquals(null, $objetoORM->getBanco(), FunctionHelper::mostrarTextoUnitVermelho("O retorno do objeto nao eh nulo"));
    }


}
