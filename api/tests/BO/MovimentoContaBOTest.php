<?php
namespace App\Tests\BO;

use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use App\Helper\FunctionHelper;
use App\Helper\ConstanteParametros;
use App\BO\Principal\MovimentoContaBO;
use App\Entity\Principal\MovimentoConta;
use App\Entity\Principal\Conta;

/**
 *
 * @author Rodrigo de Souza Fernandes (GATI labs)
 */
class MovimentoContaBOTest extends KernelTestCase
{

    /**
     *
     * @var \App\Repository\Principal\MovimentoContaRepository
     */
    private $movimentoContaRepository;

    /**
     *
     * @var \App\Repository\Principal\ContaRepository
     */
    private $contaRepository;

    /**
     *
     * @var \App\BO\Principal\MovimentoContaBO
     */
    private $movimentoContaBO;

    protected function setUp()
    {
        $kernel = self::bootKernel();
        $this->movimentoContaRepository = $kernel->getContainer()->get('doctrine')->getManager()->getRepository(MovimentoConta::class);
        $this->contaRepository          = $kernel->getContainer()->get('doctrine')->getManager()->getRepository(Conta::class);
        $this->movimentoContaBO         = new MovimentoContaBO($kernel->getContainer()->get('doctrine')->getManager());
    }

    protected function tearDown()
    {
        $this->movimentoContaRepository = null;
        $this->contaRepository          = null;
        $this->movimentoContaBO         = null;
    }

    public function testVerificaMovimentoContaExiste()
    {
        $mensagemErro      = "";
        $movimentoContaORM = null;
        $retorno           = $this->movimentoContaBO->verificaMovimentoContaExisteId($this->movimentoContaRepository, 1, $mensagemErro, $movimentoContaORM, true);

        $this->assertTrue($retorno === true, FunctionHelper::mostrarTextoUnitVermelho("MovimentoConta nao encontrado na base de dados.\n" . $mensagemErro));
        $this->assertEquals(MovimentoConta::class, get_class($movimentoContaORM), FunctionHelper::mostrarTextoUnitVermelho("O Retorno de objetos nao eh o mesmo que o esperado"));
        $this->assertEmpty($mensagemErro, FunctionHelper::mostrarTextoUnitVermelho("Ocorreu algum erro no ORM, segue msg de erro:\n" . $mensagemErro));
    }

    public function testVerificaMovimentoContaNaoExiste()
    {
        $mensagemErro      = "";
        $movimentoContaORM = null;
        $retorno           = $this->movimentoContaBO->verificaMovimentoContaExisteId($this->movimentoContaRepository, 999, $mensagemErro, $movimentoContaORM, true);

        $this->assertFalse($retorno === true, FunctionHelper::mostrarTextoUnitVermelho("MovimentoConta foi encontrado na base, e isto nao poderia acontecer."));
        $this->assertEquals(null, $movimentoContaORM, FunctionHelper::mostrarTextoUnitVermelho("O Retorno de objetos nao eh o mesmo que o esperado"));
        $this->assertNotEmpty($mensagemErro, FunctionHelper::mostrarTextoUnitVermelho("O retorno esperado deste teste eh para que tenha vindo um erro de base de dados"));
    }

    public function testPodeSalvarValido()
    {
        $parametros   = [
            ConstanteParametros::CHAVE_CONTA                => 1,
            ConstanteParametros::CHAVE_FRANQUEADA           => 1,
            ConstanteParametros::CHAVE_TIPO_MOVIMENTO_CONTA => 1,
            ConstanteParametros::CHAVE_TITULO_PAGAR         => 1,
            ConstanteParametros::CHAVE_FORMA_PAGAMENTO      => 1,
            ConstanteParametros::CHAVE_USUARIO              => 1,
            ConstanteParametros::CHAVE_MC_VALOR_MONTANTE    => 99.99,
            ConstanteParametros::CHAVE_MC_VALOR_LANCAMENTO  => 99.99,
            ConstanteParametros::CHAVE_MC_DATA_CONTABIL     => "2018-09-03T11:55:00.000Z",
            ConstanteParametros::CHAVE_MC_DATA_DEPOSITO     => null,
        ];
        $mensagemErro = "";
        $retorno      = $this->movimentoContaBO->podeCriar($parametros, $mensagemErro);

        $this->assertTrue($retorno, FunctionHelper::mostrarTextoUnitVermelho("Ocorreu um erro no teste: " . $mensagemErro));
        $this->assertEmpty($mensagemErro, FunctionHelper::mostrarTextoUnitVermelho("Ocorreu algum erro, segue msg de erro:\n" . $mensagemErro));
    }

    public function testPodeSalvarInvalido()
    {
        $parametros   = [
            ConstanteParametros::CHAVE_CONTA                => 9999999,
            ConstanteParametros::CHAVE_FRANQUEADA           => 1,
            ConstanteParametros::CHAVE_TIPO_MOVIMENTO_CONTA => 1,
            ConstanteParametros::CHAVE_TITULO_PAGAR         => 1,
            ConstanteParametros::CHAVE_FORMA_PAGAMENTO      => 1,
            ConstanteParametros::CHAVE_USUARIO              => 1,
            ConstanteParametros::CHAVE_MC_VALOR_MONTANTE    => 99.99,
            ConstanteParametros::CHAVE_MC_VALOR_LANCAMENTO  => 99.99,
            ConstanteParametros::CHAVE_MC_DATA_CONTABIL     => "2199-12-31T23:59:59.000Z",
            ConstanteParametros::CHAVE_MC_DATA_DEPOSITO     => null,
        ];
        $mensagemErro = "";
        $retorno      = $this->movimentoContaBO->podeCriar($parametros, $mensagemErro);

        $this->assertFalse($retorno, FunctionHelper::mostrarTextoUnitVermelho("Ocorreu um erro no teste: " . $mensagemErro));
        $this->assertNotEmpty($mensagemErro, FunctionHelper::mostrarTextoUnitVermelho("Ocorreu algum erro, segue msg de erro:\n" . $mensagemErro));
    }

    public function testCalculaValorSaldoFinalContaValido()
    {
        $parametros           = [
            ConstanteParametros::CHAVE_MC_VALOR_MONTANTE        => 99.99,
            ConstanteParametros::CHAVE_MC_VALOR_LANCAMENTO      => 99.99,
            ConstanteParametros::CHAVE_MC_VALOR_DESCONTO        => null,
            ConstanteParametros::CHAVE_MC_VALOR_JUROS           => null,
            ConstanteParametros::CHAVE_MC_VALOR_DIFERENCA_BAIXA => null,
        ];
        $valor_saldo_esperado = 10050.01;

        $conta = $this->contaRepository->find(1);
        $movimentoContaORM = new MovimentoConta();
        $movimentoContaORM->setConta($conta);
        $movimentoContaORM->setOperacao("D");

        $this->movimentoContaBO->calculaValorSaldoFinalConta($parametros, $movimentoContaORM);
        $valor_saldo_calculado = $movimentoContaORM->getValorSaldoFinalConta();

        $this->assertEquals($valor_saldo_esperado, $valor_saldo_calculado, FunctionHelper::mostrarTextoUnitVermelho("O valor do saldo esperado está incorreto.\nSaldo esperado: " . $valor_saldo_esperado . " Saldo calculado: " . $valor_saldo_calculado));
    }

    public function testCalculaValorSaldoFinalContaInvalido()
    {
        $parametros           = [
            ConstanteParametros::CHAVE_MC_VALOR_MONTANTE        => 99.99,
            ConstanteParametros::CHAVE_MC_VALOR_LANCAMENTO      => 100,
            ConstanteParametros::CHAVE_MC_VALOR_DESCONTO        => null,
            ConstanteParametros::CHAVE_MC_VALOR_JUROS           => null,
            ConstanteParametros::CHAVE_MC_VALOR_DIFERENCA_BAIXA => null,
        ];
        $valor_saldo_esperado = 10050.01;

        $conta = $this->contaRepository->find(1);
        $movimentoContaORM = new MovimentoConta();
        $movimentoContaORM->setConta($conta);
        $movimentoContaORM->setOperacao("D");

        $this->movimentoContaBO->calculaValorSaldoFinalConta($parametros, $movimentoContaORM);
        $valor_saldo_calculado = $movimentoContaORM->getValorSaldoFinalConta();

        $this->assertNotEquals($valor_saldo_esperado, $valor_saldo_calculado, FunctionHelper::mostrarTextoUnitVermelho("O valor do saldo esperado está incorreto.\nSaldo esperado: " . $valor_saldo_esperado . " Saldo calculado: " . $valor_saldo_calculado));
    }

    public function testPodeAtualizarValido()
    {
        $parametros   = [
            ConstanteParametros::CHAVE_MC_DATA_CONTABIL => "2018-09-03T15:30:00.000Z",
            ConstanteParametros::CHAVE_MC_DATA_DEPOSITO => null,
        ];
        $mensagemErro = "";
        $retorno      = $this->movimentoContaBO->podeAtualizar($parametros, $mensagemErro);

        $this->assertTrue($retorno === true, FunctionHelper::mostrarTextoUnitVermelho("Ocorreu um erro no teste: " . $mensagemErro));
        $this->assertEmpty($mensagemErro, FunctionHelper::mostrarTextoUnitVermelho("Ocorreu algum erro, segue msg de erro:\n" . $mensagemErro));
    }

    public function testPodeAtualizarInvalido()
    {
        $parametros   = [
            ConstanteParametros::CHAVE_MC_DATA_CONTABIL => "0Z",
            ConstanteParametros::CHAVE_MC_DATA_DEPOSITO => null,
        ];
        $mensagemErro = "";
        $retorno      = $this->movimentoContaBO->podeAtualizar($parametros, $mensagemErro);

        $this->assertFalse($retorno, FunctionHelper::mostrarTextoUnitVermelho("Ocorreu um erro no teste: " . $mensagemErro));
        $this->assertNotEmpty($mensagemErro, FunctionHelper::mostrarTextoUnitVermelho("Ocorreu algum erro, segue msg de erro:\n" . $mensagemErro));
    }


}
