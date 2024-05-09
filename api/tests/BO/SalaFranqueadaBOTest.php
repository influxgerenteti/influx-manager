<?php
namespace App\Tests\BO;

use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use App\Helper\FunctionHelper;
use App\BO\Principal\SalaFranqueadaBO;
use App\Helper\ConstanteParametros;
use App\Entity\Principal\SalaFranqueada;

/**
 *
 * @author Luiz Antonio Costa
 */
class SalaFranqueadaBOTest extends KernelTestCase
{

    /**
     *
     * @var \App\BO\Principal\SalaFranqueadaBO
     */
    private $salaFranqueadaBO;

    /**
     *
     * @var \App\Repository\Principal\SalaFranqueadaRepository
     */
    private $salaFranqueadaRepository;

    protected function setUp()
    {
        $kernel = self::bootKernel();
        $this->salaFranqueadaBO         = new SalaFranqueadaBO($kernel->getContainer()->get('doctrine')->getManager());
        $this->salaFranqueadaRepository = $kernel->getContainer()->get('doctrine')->getManager()->getRepository(\App\Entity\Principal\SalaFranqueada::class);
    }

    protected function tearDown()
    {
        $this->salaFranqueadaBO         = null;
        $this->salaFranqueadaRepository = null;
    }

    public function testSalaFranqueadaExiste()
    {
        $objetoORM    = null;
        $mensagemErro = "";
        $retorno      = $this->salaFranqueadaBO->verificaSalaFranqueadaExiste($this->salaFranqueadaRepository, 1, $mensagemErro, $objetoORM);

        $this->assertTrue($retorno === true, FunctionHelper::mostrarTextoUnitVermelho("O teste foi programado para que o objeto exista, porem ele nao consta na base de dados."));
        $this->assertEmpty($mensagemErro, FunctionHelper::mostrarTextoUnitVermelho("O retorno esperado era para que nao contenha erros na variavel de mensagem porem obteve."));
        $this->assertEquals(\App\Entity\Principal\SalaFranqueada::class, get_class($objetoORM), FunctionHelper::mostrarTextoUnitVermelho("O retorno esperado era para que fosse um objeto."));
    }

    public function testSalaFranqueadaNaoExiste()
    {
        $objetoORM    = null;
        $mensagemErro = "";
        $retorno      = $this->salaFranqueadaBO->verificaSalaFranqueadaExiste($this->salaFranqueadaRepository, 99999, $mensagemErro, $objetoORM);

        $this->assertFalse($retorno === true, FunctionHelper::mostrarTextoUnitVermelho("O teste foi programado para que o retorno fosse FALSE porem o registro foi encontrado, do qual nao deveria existir."));
        $this->assertNotEmpty($mensagemErro, FunctionHelper::mostrarTextoUnitVermelho("O teste estava esperando que tivesse retorno da pesquisa, porem nao obteve."));
        $this->assertEquals(null, $objetoORM, FunctionHelper::mostrarTextoUnitVermelho("O retorno do objeto nao eh nulo"));
    }

    public function testPodeSalvarValido()
    {
        $mensagemErro = "";
        $parametros   = [
            ConstanteParametros::CHAVE_FRANQUEADA => 1,
            ConstanteParametros::CHAVE_SALA       => 1,
        ];

        $retorno = $this->salaFranqueadaBO->podeSalvar($parametros, $mensagemErro);

        $this->assertTrue($retorno === true, FunctionHelper::mostrarTextoUnitVermelho("O teste foi programado para que o objeto exista, porem ele nao consta na base de dados."));
        $this->assertEmpty($mensagemErro, FunctionHelper::mostrarTextoUnitVermelho("O retorno esperado era para que nao contenha erros na variavel de mensagem porem obteve."));
    }

    public function testPodeSalvarInvalido()
    {
        $mensagemErro = "";
        $parametros   = [
            ConstanteParametros::CHAVE_FRANQUEADA => 999999,
            ConstanteParametros::CHAVE_SALA       => 999999,
        ];

        $retorno = $this->salaFranqueadaBO->podeSalvar($parametros, $mensagemErro);
        $this->assertFalse($retorno === true, FunctionHelper::mostrarTextoUnitVermelho("O teste foi programado para que o retorno fosse FALSE porem o registro foi encontrado, do qual nao deveria existir."));
        $this->assertNotEmpty($mensagemErro, FunctionHelper::mostrarTextoUnitVermelho("O teste estava esperando que tivesse retorno da pesquisa, porem nao obteve."));
    }


    public function testPodeAlterarValido()
    {
        $mensagemErro = "";
        $parametros   = [
            ConstanteParametros::CHAVE_FRANQUEADA => 1,
            ConstanteParametros::CHAVE_SALA       => 1,
        ];
        $objetoORM    = new SalaFranqueada();
        $retorno      = $this->salaFranqueadaBO->podeAlterar($parametros, $mensagemErro, $objetoORM);
        $this->assertTrue($retorno === true, FunctionHelper::mostrarTextoUnitVermelho("O teste foi programado para que o objeto exista, porem ele nao consta na base de dados."));
        $this->assertEquals(\App\Entity\Principal\Franqueada::class, get_class($objetoORM->getFranqueada()), FunctionHelper::mostrarTextoUnitVermelho("O retorno esperado era para que fosse um objeto."));
        $this->assertEquals(\App\Entity\Principal\Sala::class, get_class($objetoORM->getSala()), FunctionHelper::mostrarTextoUnitVermelho("O retorno esperado era para que fosse um objeto."));
    }

    public function testPodeAlterarInvalido()
    {
        $mensagemErro = "";
        $parametros   = [
            ConstanteParametros::CHAVE_FRANQUEADA => 999999,
            ConstanteParametros::CHAVE_SALA       => 999999,
        ];
        $objetoORM    = new SalaFranqueada();
        $retorno      = $this->salaFranqueadaBO->podeAlterar($parametros, $mensagemErro, $objetoORM);
        $this->assertFalse($retorno === true, FunctionHelper::mostrarTextoUnitVermelho("O teste foi programado para que o objeto exista, porem ele nao consta na base de dados."));
        $this->assertEquals(null, $objetoORM->getFranqueada(), FunctionHelper::mostrarTextoUnitVermelho("O retorno do objeto nao eh nulo"));
        $this->assertEquals(null, $objetoORM->getSala(), FunctionHelper::mostrarTextoUnitVermelho("O retorno do objeto nao eh nulo"));
    }


}