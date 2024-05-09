<?php
namespace App\Tests\BO;

use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use App\Helper\FunctionHelper;
use App\BO\Principal\SistemaAvaliacaoBO;
use App\Helper\ConstanteParametros;

/**
 *
 * @author Luiz Antonio Costa
 */
class SistemaAvaliacaoBOTest extends KernelTestCase
{

    /**
     *
     * @var \App\Repository\Principal\SistemaAvaliacaoRepository
     */
    private $sistemaAvaliacaoRepository;

    /**
     *
     * @var \App\BO\Principal\SistemaAvaliacaoBO
     */
    private $sistemaAvaliacaoBO;

    protected function setUp()
    {
        $kernel = self::bootKernel();
        $this->sistemaAvaliacaoRepository = $kernel->getContainer()->get('doctrine')->getManager()->getRepository(\App\Entity\Principal\SistemaAvaliacao::class);
        $this->sistemaAvaliacaoBO         = new SistemaAvaliacaoBO($kernel->getContainer()->get('doctrine')->getManager());
    }

    protected function tearDown()
    {
        $this->sistemaAvaliacaoRepository = null;
        $this->sistemaAvaliacaoBO         = null;
    }

    public function testVerificaSistemaAvaliacaoExiste()
    {
        $mensagemErro = "";
        $objetoORM    = null;
        $retorno      = $this->sistemaAvaliacaoBO->verificaSistemaAvaliacaoExiste($this->sistemaAvaliacaoRepository, 1, $mensagemErro, $objetoORM);

        $this->assertTrue($retorno === true, FunctionHelper::mostrarTextoUnitVermelho("SistemaAvaliacao nao encontrado na base de dados.\n" . $mensagemErro));
        $this->assertEquals(\App\Entity\Principal\SistemaAvaliacao::class, get_class($objetoORM), FunctionHelper::mostrarTextoUnitVermelho("O Retorno de objetos nao eh o mesmo que o esperado"));
        $this->assertEmpty($mensagemErro, FunctionHelper::mostrarTextoUnitVermelho("Ocorreu algum erro no ORM, segue msg de erro:\n" . $mensagemErro));
    }

    public function testVerificaSistemaAvaliacaoNaoExiste()
    {
        $mensagemErro = "";
        $objetoORM    = null;
        $retorno      = $this->sistemaAvaliacaoBO->verificaSistemaAvaliacaoExiste($this->sistemaAvaliacaoRepository, 99999, $mensagemErro, $objetoORM);

        $this->assertFalse($retorno === true, FunctionHelper::mostrarTextoUnitVermelho("Sistema Avaliacao foi encontrado na base, e isto nao poderia acontecer."));
        $this->assertEquals(null, $objetoORM, FunctionHelper::mostrarTextoUnitVermelho("O Retorno de objetos nao eh o mesmo que o esperado"));
        $this->assertNotEmpty($mensagemErro, FunctionHelper::mostrarTextoUnitVermelho("O retorno esperado deste teste eh para que tenha vindo um erro de base de dados"));
    }

    public function testPodeSalvarValido()
    {
        $mensagemErro = "";
        $parametros   = [
            ConstanteParametros::CHAVE_CONCEITO_APROVACAO                   => 1,
            ConstanteParametros::CHAVE_CONCEITO_CORTE_COMPROMISSO_QUALIDADE => 6,
        ];
        $retorno      = $this->sistemaAvaliacaoBO->podeSalvar($parametros, $mensagemErro);
        $this->assertTrue($retorno === true, FunctionHelper::mostrarTextoUnitVermelho("O teste de podesalvar do Sistema de Avaliacao deu false.\n" . $mensagemErro));
    }

    public function testPodeSalvarInvalido()
    {
        $mensagemErro = "";
        $parametros   = [
            ConstanteParametros::CHAVE_CONCEITO_APROVACAO                   => 9999,
            ConstanteParametros::CHAVE_CONCEITO_CORTE_COMPROMISSO_QUALIDADE => 9999,
        ];
        $retorno      = $this->sistemaAvaliacaoBO->podeSalvar($parametros, $mensagemErro);
        $this->assertFalse($retorno === true, FunctionHelper::mostrarTextoUnitVermelho("O teste de podesalvar do Sistema de Avaliacao deu false.\n" . $mensagemErro));
    }


}
