<?php
namespace App\Tests\BO;

use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use App\Helper\FunctionHelper;
use App\BO\Principal\TipoMovimentoEstoqueBO;

/**
 *
 * @author Luiz Antonio Costa
 */
class TipoMovimentoEstoqueBOTest extends KernelTestCase
{

    /**
     *
     * @var \App\Repository\Principal\TipoMovimentoEstoqueRepository
     */
    private $tipoMovimentoEstoqueRepository;

    /**
     *
     * @var \App\BO\Principal\TipoMovimentoEstoqueBO
     */
    private $tipoMovimentoEstoqueBO;

    protected function setUp()
    {
        $kernel = self::bootKernel();
        $this->tipoMovimentoEstoqueRepository = $kernel->getContainer()->get('doctrine')->getManager()->getRepository(\App\Entity\Principal\TipoMovimentoEstoque::class);
        $this->tipoMovimentoEstoqueBO         = new TipoMovimentoEstoqueBO();
    }

    protected function tearDown()
    {
        $this->tipoMovimentoEstoqueRepository = null;
        $this->tipoMovimentoEstoqueBO         = null;
    }

    public function testVerificaTpMovimentoEstoqueExiste()
    {
        $mensagem  = "";
        $objetoORM = null;
        $retorno   = $this->tipoMovimentoEstoqueBO->verificaTpMovimentoEstoqueExiste($this->tipoMovimentoEstoqueRepository, 1, $mensagem, $objetoORM);

        $this->assertTrue($retorno === true, FunctionHelper::mostrarTextoUnitVermelho("O retorno esperado era para que existisse o dado no banco."));
        $this->assertNotEmpty($mensagem, FunctionHelper::mostrarTextoUnitVermelho("O teste ocorreu sem erros, porem, foi programado para ter erro."));
        $this->assertEquals(\App\Entity\Principal\TipoMovimentoEstoque::class, get_class($objetoORM), FunctionHelper::mostrarTextoUnitVermelho("O Objeto retornado eh diferente do esperado"));
    }

    public function testVerificaTpMovimentoEstoqueNaoExiste()
    {
        $mensagem  = "";
        $objetoORM = null;
        $retorno   = $this->tipoMovimentoEstoqueBO->verificaTpMovimentoEstoqueExiste($this->tipoMovimentoEstoqueRepository, 999, $mensagem, $objetoORM);

        $this->assertFalse($retorno === true, FunctionHelper::mostrarTextoUnitVermelho("O retorno esperado era para que existisse o dado no banco."));
        $this->assertEmpty($mensagem, FunctionHelper::mostrarTextoUnitVermelho("O teste ocorreu sem erros, porem, foi programado para ter erro."));
        $this->assertEquals(null, $objetoORM, FunctionHelper::mostrarTextoUnitVermelho("O Objeto retornado eh diferente do esperado"));
    }

    public function testDescricaoTpMovimentoEstoqueJaCadastrado()
    {
        $mensagem  = "";
        $objetoORM = null;
        $descricao = "Conta a pagar";

        $descricaoJaExiste = $this->tipoMovimentoEstoqueBO->verificaDescricaoExiste($this->tipoMovimentoEstoqueRepository, null, $descricao, $mensagem, $objetoORM);

        $this->assertTrue($descricaoJaExiste, FunctionHelper::mostrarTextoUnitVermelho("O retorno esperado era para que existisse o dado no banco."));
        $this->assertNotEmpty($mensagem, FunctionHelper::mostrarTextoUnitVermelho("O teste ocorreu sem erros, porem, foi programado para ter erro."));
        $this->assertEquals(gettype([]), gettype($objetoORM), FunctionHelper::mostrarTextoUnitVermelho("O Objeto retornado eh diferente do esperado"));
    }

    public function testDescricaoTpMovimentoEstoqueNaoCadastrado()
    {
        $mensagem  = "";
        $objetoORM = null;
        $retorno   = $this->tipoMovimentoEstoqueBO->verificaDescricaoExiste($this->tipoMovimentoEstoqueRepository, null, "AUIOOSAHOASHUAS", $mensagem, $objetoORM);

        $this->assertFalse($retorno === true, FunctionHelper::mostrarTextoUnitVermelho("O retorno esperado era para que existisse o dado no banco."));
        $this->assertEmpty($mensagem, FunctionHelper::mostrarTextoUnitVermelho("O teste ocorreu sem erros, porem, foi programado para ter erro."));
        $this->assertEquals(null, $objetoORM, FunctionHelper::mostrarTextoUnitVermelho("O Objeto retornado eh diferente do esperado"));
    }


}
