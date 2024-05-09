<?php
namespace App\Tests\BO;

use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use App\Helper\FunctionHelper;
use App\Helper\ConstanteParametros;
use App\BO\Principal\ValorHoraBO;
use App\Entity\Principal\ValorHora;

/**
 *
 * @author Luiz Antonio Costa
 */
class ValorHoraBOTest extends KernelTestCase
{

    /**
     *
     * @var \App\BO\Principal\ValorHoraBO
     */
    private $valorHoraBO;

    /**
     *
     * @var \App\Repository\Principal\ValorHoraRepository
     */
    private $valorHoraRepository;

    protected function setUp()
    {
        $kernel            = self::bootKernel();
        $this->valorHoraBO = new ValorHoraBO($kernel->getContainer()->get('doctrine')->getManager());
        $this->valorHoraRepository = $kernel->getContainer()->get('doctrine')->getManager()->getRepository(\App\Entity\Principal\ValorHora::class);
    }

    protected function tearDown()
    {
        $this->valorHoraBO         = null;
        $this->valorHoraRepository = null;
    }

    public function testValorHoraExiste()
    {
        $objetoORM    = null;
        $mensagemErro = "";
        $retorno      = $this->valorHoraBO->verificaValorHoraExiste($this->valorHoraRepository, 1, $mensagemErro, $objetoORM);

        $this->assertTrue($retorno === true, FunctionHelper::mostrarTextoUnitVermelho("O teste foi programado para que o objeto exista, porem ele nao consta na base de dados."));
        $this->assertEmpty($mensagemErro, FunctionHelper::mostrarTextoUnitVermelho("O retorno esperado era para que nao contenha erros na variavel de mensagem porem obteve."));
        $this->assertEquals(\App\Entity\Principal\ValorHora::class, get_class($objetoORM), FunctionHelper::mostrarTextoUnitVermelho("O retorno esperado era para que fosse um objeto."));
    }

    public function testValorHoraNaoExiste()
    {
        $objetoORM    = null;
        $mensagemErro = "";
        $retorno      = $this->valorHoraBO->verificaValorHoraExiste($this->valorHoraRepository, 99999, $mensagemErro, $objetoORM);

        $this->assertFalse($retorno === true, FunctionHelper::mostrarTextoUnitVermelho("O teste foi programado para que o retorno fosse FALSE porem o registro foi encontrado, do qual nao deveria existir."));
        $this->assertNotEmpty($mensagemErro, FunctionHelper::mostrarTextoUnitVermelho("O teste estava esperando que tivesse retorno da pesquisa, porem nao obteve."));
        $this->assertEquals(null, $objetoORM, FunctionHelper::mostrarTextoUnitVermelho("O retorno do objeto nao eh nulo"));
    }

    public function testPodeSalvarValido()
    {
        $mensagemErro = "";
        $parametros   = [
            ConstanteParametros::CHAVE_FRANQUEADA        => 1,
            ConstanteParametros::CHAVE_VALOR_HORA_LINHAS => 1,
            ConstanteParametros::CHAVE_NIVEL_INSTRUTOR   => 1,
        ];

        $retorno = $this->valorHoraBO->podeSalvar($parametros, $mensagemErro);

        $this->assertTrue($retorno === true, FunctionHelper::mostrarTextoUnitVermelho("O teste foi programado para que o objeto exista, porem ele nao consta na base de dados."));
        $this->assertEmpty($mensagemErro, FunctionHelper::mostrarTextoUnitVermelho("O retorno esperado era para que nao contenha erros na variavel de mensagem porem obteve."));
    }

    public function testPodeSalvarInvalido()
    {
        $mensagemErro = "";
        $parametros   = [
            ConstanteParametros::CHAVE_FRANQUEADA        => 999999,
            ConstanteParametros::CHAVE_VALOR_HORA_LINHAS => 999999,
            ConstanteParametros::CHAVE_NIVEL_INSTRUTOR   => 9999999,
        ];

        $retorno = $this->valorHoraBO->podeSalvar($parametros, $mensagemErro);
        $this->assertFalse($retorno === true, FunctionHelper::mostrarTextoUnitVermelho("O teste foi programado para que o retorno fosse FALSE porem o registro foi encontrado, do qual nao deveria existir."));
        $this->assertNotEmpty($mensagemErro, FunctionHelper::mostrarTextoUnitVermelho("O teste estava esperando que tivesse retorno da pesquisa, porem nao obteve."));
    }


    public function testPodeAlterarValido()
    {
        $mensagemErro = "";
        $parametros   = [
            ConstanteParametros::CHAVE_FRANQUEADA        => 1,
            ConstanteParametros::CHAVE_VALOR_HORA_LINHAS => 1,
            ConstanteParametros::CHAVE_NIVEL_INSTRUTOR   => 1,
        ];
        $objetoORM    = new ValorHora();
        $retorno      = $this->valorHoraBO->podeAlterar($parametros, $mensagemErro, $objetoORM);
        $this->assertTrue($retorno === true, FunctionHelper::mostrarTextoUnitVermelho("O teste foi programado para que o objeto exista, porem ele nao consta na base de dados."));
        $this->assertEquals(\App\Entity\Principal\Franqueada::class, get_class($objetoORM->getFranqueada()), FunctionHelper::mostrarTextoUnitVermelho("O retorno esperado era para que fosse um objeto."));
        $this->assertEquals(\App\Entity\Principal\ValorHoraLinhas::class, get_class($objetoORM->getValorHoraLinhas()), FunctionHelper::mostrarTextoUnitVermelho("O retorno esperado era para que fosse um objeto."));
        $this->assertEquals(\App\Entity\Principal\NivelInstrutor::class, get_class($objetoORM->getNivelInstrutor()), FunctionHelper::mostrarTextoUnitVermelho("O retorno esperado era para que fosse um objeto."));
    }

    public function testPodeAlterarInvalido()
    {
        $mensagemErro = "";
        $parametros   = [
            ConstanteParametros::CHAVE_FRANQUEADA        => 999999,
            ConstanteParametros::CHAVE_VALOR_HORA_LINHAS => 999999,
            ConstanteParametros::CHAVE_NIVEL_INSTRUTOR   => 999999,
        ];
        $objetoORM    = new ValorHora();
        $retorno      = $this->valorHoraBO->podeAlterar($parametros, $mensagemErro, $objetoORM);
        $this->assertFalse($retorno === true, FunctionHelper::mostrarTextoUnitVermelho("O teste foi programado para que o objeto exista, porem ele nao consta na base de dados."));
        $this->assertEquals(null, $objetoORM->getFranqueada(), FunctionHelper::mostrarTextoUnitVermelho("O retorno do objeto nao eh nulo"));
        $this->assertEquals(null, $objetoORM->getValorHoraLinhas(), FunctionHelper::mostrarTextoUnitVermelho("O retorno do objeto nao eh nulo"));
        $this->assertEquals(null, $objetoORM->getNivelInstrutor(), FunctionHelper::mostrarTextoUnitVermelho("O retorno do objeto nao eh nulo"));
    }


}
