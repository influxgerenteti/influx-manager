<?php
namespace App\Tests\BO;

use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use App\Helper\FunctionHelper;
use App\Helper\ConstanteParametros;
use App\BO\Principal\ItemBO;

/**
 *
 * @author Luiz Antonio Costa
 */
class ItemBOTest extends KernelTestCase
{

    /**
     *
     * @var \App\BO\Principal\ItemBO
     */
    private $itemBO;

    protected function setUp()
    {
        $kernel       = self::bootKernel();
        $this->itemBO = new ItemBO($kernel->getContainer()->get('doctrine')->getManager());
    }

    protected function tearDown()
    {
        $this->itemBO = null;
    }

    public function testItemExiste()
    {
        $objetoORM    = null;
        $mensagemErro = "";
        $retorno      = $this->itemBO->verificaItemExisteBO([ConstanteParametros::CHAVE_ITEM => 1], $mensagemErro, $objetoORM);

        $this->assertTrue($retorno === true, FunctionHelper::mostrarTextoUnitVermelho("O teste foi programado para que o objeto exista, porem ele nao consta na base de dados."));
        $this->assertEmpty($mensagemErro, FunctionHelper::mostrarTextoUnitVermelho("O retorno esperado era para que nao contenha erros na variavel de mensagem porem obteve."));
        $this->assertEquals(\App\Entity\Principal\Item::class, get_class($objetoORM), FunctionHelper::mostrarTextoUnitVermelho("O retorno esperado era para que fosse um objeto."));
    }

    public function testItemNaoExiste()
    {
        $objetoORM    = null;
        $mensagemErro = "";
        $retorno      = $this->itemBO->verificaItemExistePorId(99999, $mensagemErro, $objetoORM);

        $this->assertFalse($retorno === true, FunctionHelper::mostrarTextoUnitVermelho("O teste foi programado para que o retorno fosse FALSE porem o registro foi encontrado, do qual nao deveria existir."));
        $this->assertNotEmpty($mensagemErro, FunctionHelper::mostrarTextoUnitVermelho("O teste estava esperando que tivesse retorno da pesquisa, porem nao obteve."));
        $this->assertEquals(null, $objetoORM, FunctionHelper::mostrarTextoUnitVermelho("O retorno do objeto nao eh nulo"));
    }

    public function testFranqueadaExiste()
    {
        $parametros   = [
            ConstanteParametros::CHAVE_FRANQUEADA => 1,
            ConstanteParametros::CHAVE_TIPO_ITEM  => 1,
        ];
        $mensagemErro = "";
        $retorno      = $this->itemBO->podeSalvar($parametros, $mensagemErro);

        $this->assertTrue($retorno === true, FunctionHelper::mostrarTextoUnitVermelho("Ocorreu um erro na hora de realizar o teste.\n" . $mensagemErro));
        $this->assertEmpty($mensagemErro, FunctionHelper::mostrarTextoUnitVermelho("O teste era para ter ocorrido sem retorno de erros.\nErro:" . $mensagemErro));
    }

    public function testFranqueadaNaoExiste()
    {
        $parametros   = [ConstanteParametros::CHAVE_FRANQUEADA => 19999];
        $mensagemErro = "";
        $retorno      = $this->itemBO->podeSalvar($parametros, $mensagemErro);

        $this->assertFalse($retorno === true, FunctionHelper::mostrarTextoUnitVermelho("Ocorreu um erro na execucao de teste, o teste foi programado para que nao exista o registro, porem acabou encontrando."));
        $this->assertNotEmpty($mensagemErro, FunctionHelper::mostrarTextoUnitVermelho("O teste foi executado com exito, porem o programado era para que existisse registro"));
    }


}
