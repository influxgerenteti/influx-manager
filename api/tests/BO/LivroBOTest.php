<?php
namespace App\Tests\BO;

use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use App\Helper\FunctionHelper;
use App\BO\Principal\LivroBO;
use App\Helper\ConstanteParametros;
use App\Entity\Principal\Livro;

/**
 *
 * @author Luiz Antonio Costa
 */
class LivroBOTest extends KernelTestCase
{

    /**
     *
     * @var \App\BO\Principal\LivroBO
     */
    private $livroBO;

    /**
     *
     * @var \App\Repository\Principal\LivroRepository
     */
    private $livroRepository;

    protected function setUp()
    {
        $kernel        = self::bootKernel();
        $this->livroBO = new LivroBO($kernel->getContainer()->get('doctrine')->getManager());
        $this->livroRepository = $kernel->getContainer()->get('doctrine')->getManager()->getRepository(\App\Entity\Principal\Livro::class);
    }

    protected function tearDown()
    {
        $this->livroBO         = null;
        $this->livroRepository = null;
    }

    public function testLivroExiste()
    {
        $objetoORM    = null;
        $mensagemErro = "";
        $retorno      = $this->livroBO->verificaLivroExiste($this->livroRepository, 1, $mensagemErro, $objetoORM);

        $this->assertTrue($retorno === true, FunctionHelper::mostrarTextoUnitVermelho("O teste foi programado para que o objeto exista, porem ele nao consta na base de dados."));
        $this->assertEmpty($mensagemErro, FunctionHelper::mostrarTextoUnitVermelho("O retorno esperado era para que nao contenha erros na variavel de mensagem porem obteve."));
        $this->assertEquals(\App\Entity\Principal\Livro::class, get_class($objetoORM), FunctionHelper::mostrarTextoUnitVermelho("O retorno esperado era para que fosse um objeto."));
    }

    public function testLivroNaoExiste()
    {
        $objetoORM    = null;
        $mensagemErro = "";
        $retorno      = $this->livroBO->verificaLivroExiste($this->livroRepository, 99999, $mensagemErro, $objetoORM);

        $this->assertFalse($retorno === true, FunctionHelper::mostrarTextoUnitVermelho("O teste foi programado para que o retorno fosse FALSE porem o registro foi encontrado, do qual nao deveria existir."));
        $this->assertNotEmpty($mensagemErro, FunctionHelper::mostrarTextoUnitVermelho("O teste estava esperando que tivesse retorno da pesquisa, porem nao obteve."));
        $this->assertEquals(null, $objetoORM, FunctionHelper::mostrarTextoUnitVermelho("O retorno do objeto nao eh nulo"));
    }

    public function testPodeSalvarValido()
    {
        $mensagemErro = "";
        $parametros   = [
            ConstanteParametros::CHAVE_SISTEMA_AVALIACAO  => 1,
            ConstanteParametros::CHAVE_CURSO              => [1],
            ConstanteParametros::CHAVE_ITEM               => 1,
            ConstanteParametros::CHAVE_PLANEJAMENTO_LICAO => 1,
        ];

        $retorno = $this->livroBO->podeSalvar($parametros, $mensagemErro);

        $this->assertTrue($retorno === true, FunctionHelper::mostrarTextoUnitVermelho("O teste foi programado para que o objeto exista, porem ele nao consta na base de dados."));
        $this->assertEmpty($mensagemErro, FunctionHelper::mostrarTextoUnitVermelho("O retorno esperado era para que nao contenha erros na variavel de mensagem porem obteve."));
    }

    public function testPodeSalvarInvalido()
    {
        $mensagemErro = "";
        $parametros   = [
            ConstanteParametros::CHAVE_SISTEMA_AVALIACAO  => 1,
            ConstanteParametros::CHAVE_CURSO              => [1],
            ConstanteParametros::CHAVE_ITEM               => 1,
            ConstanteParametros::CHAVE_PLANEJAMENTO_LICAO => 999999,
        ];

        $retorno = $this->livroBO->podeSalvar($parametros, $mensagemErro);
        $this->assertFalse($retorno === true, FunctionHelper::mostrarTextoUnitVermelho("O teste foi programado para que o retorno fosse FALSE porem o registro foi encontrado, do qual nao deveria existir."));
        $this->assertNotEmpty($mensagemErro, FunctionHelper::mostrarTextoUnitVermelho("O teste estava esperando que tivesse retorno da pesquisa, porem nao obteve."));
    }


    public function testPodeAlterarValido()
    {
        $mensagemErro = "";
        $parametros   = [
            ConstanteParametros::CHAVE_SISTEMA_AVALIACAO  => 1,
            ConstanteParametros::CHAVE_CURSO              => [1],
            ConstanteParametros::CHAVE_ITEM               => 1,
            ConstanteParametros::CHAVE_PLANEJAMENTO_LICAO => 1,
        ];
        $objetoORM    = new Livro();
        $retorno      = $this->livroBO->podeAlterar($parametros, $mensagemErro, $objetoORM);
        $this->assertTrue($retorno === true, FunctionHelper::mostrarTextoUnitVermelho("O teste foi programado para que o objeto exista, porem ele nao consta na base de dados."));
        $this->assertEquals(\App\Entity\Principal\SistemaAvaliacao::class, get_class($objetoORM->getSistemaAvaliacao()), FunctionHelper::mostrarTextoUnitVermelho("O retorno esperado era para que fosse um objeto."));
        $this->assertEquals(\Doctrine\Common\Collections\ArrayCollection::class, get_class($objetoORM->getCurso()), FunctionHelper::mostrarTextoUnitVermelho("O retorno esperado era para que fosse um objeto."));
        $this->assertEquals(\App\Entity\Principal\Item::class, get_class($objetoORM->getItem()), FunctionHelper::mostrarTextoUnitVermelho("O retorno esperado era para que fosse um objeto."));
        $this->assertEquals(\App\Entity\Principal\PlanejamentoLicao::class, get_class($objetoORM->getPlanejamentoLicao()), FunctionHelper::mostrarTextoUnitVermelho("O retorno esperado era para que fosse um objeto."));
    }

    public function testPodeAlterarInvalido()
    {
        $mensagemErro = "";
        $parametros   = [
            ConstanteParametros::CHAVE_SISTEMA_AVALIACAO  => 999999,
            ConstanteParametros::CHAVE_CURSO              => [999999],
            ConstanteParametros::CHAVE_ITEM               => 999999,
            ConstanteParametros::CHAVE_PLANEJAMENTO_LICAO => 999999,
        ];
        $objetoORM    = new Livro();
        $retorno      = $this->livroBO->podeAlterar($parametros, $mensagemErro, $objetoORM);
        $this->assertFalse($retorno === true, FunctionHelper::mostrarTextoUnitVermelho("O teste foi programado para que o objeto exista, porem ele nao consta na base de dados."));
        $this->assertEquals(null, $objetoORM->getSistemaAvaliacao(), FunctionHelper::mostrarTextoUnitVermelho("O retorno do objeto nao eh nulo"));
        $this->assertEquals(0, $objetoORM->getCurso()->count(), FunctionHelper::mostrarTextoUnitVermelho("O retorno do objeto nao eh nulo"));
        $this->assertEquals(null, $objetoORM->getItem(), FunctionHelper::mostrarTextoUnitVermelho("O retorno do objeto nao eh nulo"));
        $this->assertEquals(null, $objetoORM->getPlanejamentoLicao(), FunctionHelper::mostrarTextoUnitVermelho("O retorno do objeto nao eh nulo"));
    }


}
