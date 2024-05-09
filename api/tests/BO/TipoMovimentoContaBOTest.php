<?php
namespace App\Tests\BO;

use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use App\Helper\FunctionHelper;
use App\BO\Principal\TipoMovimentoContaBO;

/**
 *
 * @author Luiz Antonio Costa
 */
class TipoMovimentoContaBOTest extends KernelTestCase
{

    /**
     *
     * @var \App\Repository\Principal\TipoMovimentoContaRepository
     */
    private $tipoMovimentoContaRepository;

    /**
     *
     * @var \App\BO\Principal\TipoMovimentoContaBO
     */
    private $tipoMovimentoContaBO;

    protected function setUp()
    {
        $kernel = self::bootKernel();
        $this->tipoMovimentoContaRepository = $kernel->getContainer()->get('doctrine')->getManager()->getRepository(\App\Entity\Principal\TipoMovimentoConta::class);
        $this->tipoMovimentoContaBO         = new TipoMovimentoContaBO($kernel->getContainer()->get('doctrine')->getManager());
    }

    protected function tearDown()
    {
        $this->tipoMovimentoContaRepository = null;
        $this->tipoMovimentoContaBO         = null;
    }

    public function testVerificaTipoMovimentoContaExiste()
    {
        $mensagemErro          = "";
        $tipoMovimentoContaORM = null;
        $retorno = $this->tipoMovimentoContaBO->verificaTipoMovimentoContaExisteId($this->tipoMovimentoContaRepository, 1, $mensagemErro, $tipoMovimentoContaORM);

        $this->assertTrue($retorno === true, FunctionHelper::mostrarTextoUnitVermelho("Tipo Movimento Conta nao encontrado na base de dados.\n" . $mensagemErro));
        $this->assertEquals(\App\Entity\Principal\TipoMovimentoConta::class, get_class($tipoMovimentoContaORM), FunctionHelper::mostrarTextoUnitVermelho("O Retorno de objetos nao eh o mesmo que o esperado"));
        $this->assertEmpty($mensagemErro, FunctionHelper::mostrarTextoUnitVermelho("Ocorreu algum erro no ORM, segue msg de erro:\n" . $mensagemErro));
    }

    public function testVerificaTipoMovimentoContaNaoExiste()
    {
        $mensagemErro          = "";
        $tipoMovimentoContaORM = null;
        $retorno = $this->tipoMovimentoContaBO->verificaTipoMovimentoContaExisteId($this->tipoMovimentoContaRepository, 99999, $mensagemErro, $tipoMovimentoContaORM);

        $this->assertFalse($retorno === true, FunctionHelper::mostrarTextoUnitVermelho("Tipo Movimento Conta foi encontrado na base, e isto nao poderia acontecer."));
        $this->assertEquals(null, $tipoMovimentoContaORM, FunctionHelper::mostrarTextoUnitVermelho("O Retorno de objetos nao eh o mesmo que o esperado"));
        $this->assertNotEmpty($mensagemErro, FunctionHelper::mostrarTextoUnitVermelho("O retorno esperado deste teste eh para que tenha vindo um erro de base de dados"));
    }


}
