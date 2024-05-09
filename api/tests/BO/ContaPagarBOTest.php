<?php
namespace App\Tests\BO;

use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use App\Helper\FunctionHelper;
use App\BO\Principal\ContaPagarBO;

/**
 *
 * @author Luiz Antonio Costa
 */
class ContaPagarBOTest extends KernelTestCase
{

    /**
     *
     * @var \App\Repository\Principal\ContaPagarRepository
     */
    private $contaPagarRepository;

    /**
     *
     * @var \App\BO\Principal\ContaPagarBO
     */
    private $contaPagarBO;

    protected function setUp()
    {
        $kernel = self::bootKernel();
        $this->contaPagarRepository = $kernel->getContainer()->get('doctrine')->getManager()->getRepository(\App\Entity\Principal\Aluno::class);
        $this->contaPagarBO         = new ContaPagarBO($kernel->getContainer()->get('doctrine')->getManager());
    }

    protected function tearDown()
    {
        $this->contaPagarRepository = null;
        $this->contaPagarBO         = null;
    }

    public function testPodeSalvarValido()
    {
        $parametros   = [
            "franqueada"        => 2,
            "tipo_documento"    => 1,
            "fornecedor_pessoa" => 2,
            "usuario"           => 1,
            "forma_cobranca"    => 1,
        ];
        $mensagemErro = "";
        \App\Helper\VariaveisCompartilhadas::$franqueadaID = 2;

        $retorno = $this->contaPagarBO->podeCriar($parametros, $mensagemErro);

        $this->assertTrue($retorno, FunctionHelper::mostrarTextoUnitVermelho("Ocorreu um erro no teste:" . $mensagemErro));
    }

    public function testPodeSalvarInvalido()
    {
        $parametros = [
            "franqueada"        => 1,
            "tipo_documento"    => 1,
            "fornecedor_pessoa" => 123,
            "usuario"           => 1,
        ];

        $mensagemErro = "";
        $retorno      = $this->contaPagarBO->podeCriar($parametros, $mensagemErro);

        $this->assertFalse($retorno, FunctionHelper::mostrarTextoUnitVermelho("Ocorreu um erro no teste de falha, o teste de falha, executou de maneira onde nao hovue falhas"));
    }

    public function testPodeAtualizarValido()
    {
        $parametros   = ["forma_cobranca" => 1];
        $mensagemErro = "";
        $retorno      = $this->contaPagarBO->podeAtualizar($parametros, $mensagemErro);

        $this->assertTrue($retorno, FunctionHelper::mostrarTextoUnitVermelho("Ocorreu um erro no teste:" . $mensagemErro));
    }

    public function testPodeAtualizarInvalido()
    {
        $parametros   = ["fornecedor_pessoa" => 999];
        $mensagemErro = "";
        $retorno      = $this->contaPagarBO->podeAtualizar($parametros, $mensagemErro);

        $this->assertFalse($retorno, FunctionHelper::mostrarTextoUnitVermelho("Ocorreu um erro no teste de falha, o teste de falha, executou de maneira onde nao hovue falhas"));
    }


}
