<?php
namespace App\Tests\BO;

use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use App\Helper\FunctionHelper;
use App\Helper\ConstanteParametros;
use App\Helper\SituacoesSistema;
use App\BO\Principal\TituloPagarBO;
use App\Entity\Principal\TituloPagar;
use App\Entity\Principal\CondicaoPagamento;
use App\Entity\Principal\CondicaoPagamentoParcela;

/**
 *
 * @author Rodrigo de Souza Fernandes (GATI labs)
 */
class TituloPagarBOTest extends KernelTestCase
{

    /**
     *
     * @var \App\BO\Principal\TituloPagarBO
     */
    private $tituloPagarBO;

    /**
     *
     * @var \App\Repository\Principal\CondicaoPagamentoParcelaRepository
     */
    private $condicaoPagamentoParcelaRepository;

    /**
     *
     * @var \App\Repository\Principal\TituloPagarRepository
     */
    private $tituloPagarRepository;

    protected function setUp()
    {
        $kernel = self::bootKernel();
        $this->tituloPagarBO         = new TituloPagarBO($kernel->getContainer()->get('doctrine')->getManager());
        $this->tituloPagarRepository = $kernel->getContainer()->get('doctrine')->getManager()->getRepository(TituloPagar::class);
        $this->condicaoPagamentoParcelaRepository = $kernel->getContainer()->get('doctrine')->getManager()->getRepository(CondicaoPagamentoParcela::class);
    }

    protected function tearDown()
    {
        $this->tituloPagarBO         = null;
        $this->tituloPagarRepository = null;
        $this->condicaoPagamentoParcelaRepository = null;
    }

    public function testPodeSalvarValido()
    {
        $parametros = [
            ConstanteParametros::CHAVE_CONTA          => 1,
            ConstanteParametros::CHAVE_NF_VALOR_TOTAL => 1499.99,
            ConstanteParametros::CHAVE_PARCELA        => [],
        ];
        $parametros[ConstanteParametros::CHAVE_PARCELA][] = [
            ConstanteParametros::CHAVE_TIT_VALOR_DOCUMENTO          => 449.99,
            ConstanteParametros::CHAVE_TIT_DATA_VENCIMENTO          => "2018-09-26T14:06:40.000Z",
            ConstanteParametros::CHAVE_TIT_NUMERO_PARCELA_DOCUMENTO => 1,
        ];
        $parametros[ConstanteParametros::CHAVE_PARCELA][] = [
            ConstanteParametros::CHAVE_TIT_VALOR_DOCUMENTO          => 375,
            ConstanteParametros::CHAVE_TIT_DATA_VENCIMENTO          => "2018-10-26T14:06:40.000Z",
            ConstanteParametros::CHAVE_TIT_NUMERO_PARCELA_DOCUMENTO => 2,
        ];
        $parametros[ConstanteParametros::CHAVE_PARCELA][] = [
            ConstanteParametros::CHAVE_TIT_VALOR_DOCUMENTO          => 225,
            ConstanteParametros::CHAVE_TIT_DATA_VENCIMENTO          => "2018-11-25T14:06:40.000Z",
            ConstanteParametros::CHAVE_TIT_NUMERO_PARCELA_DOCUMENTO => 3,
        ];
        $parametros[ConstanteParametros::CHAVE_PARCELA][] = [
            ConstanteParametros::CHAVE_TIT_VALOR_DOCUMENTO          => 225,
            ConstanteParametros::CHAVE_TIT_DATA_VENCIMENTO          => "2018-12-25T14:06:40.000Z",
            ConstanteParametros::CHAVE_TIT_NUMERO_PARCELA_DOCUMENTO => 4,
        ];
        $parametros[ConstanteParametros::CHAVE_PARCELA][] = [
            ConstanteParametros::CHAVE_TIT_VALOR_DOCUMENTO          => 225,
            ConstanteParametros::CHAVE_TIT_DATA_VENCIMENTO          => "2019-01-24T14:06:40.000Z",
            ConstanteParametros::CHAVE_TIT_NUMERO_PARCELA_DOCUMENTO => 5,
        ];

        $condicaoPagamentoParcela = $this->condicaoPagamentoParcelaRepository->findBy([ConstanteParametros::CHAVE_ID => [1, 2, 3, 4, 5]]);

        $parcelas        = [];
        $data_vencimento = "";

        FunctionHelper::formataCampoDateTimeJS("2018-09-26T14:06:40.000Z", $data_vencimento);
        $parcelas[1] = [
            ConstanteParametros::CHAVE_ID                           => $condicaoPagamentoParcela[0],
            ConstanteParametros::CHAVE_TIT_DATA_VENCIMENTO          => $data_vencimento,
            ConstanteParametros::CHAVE_TIT_NUMERO_PARCELA_DOCUMENTO => 1,
            ConstanteParametros::CHAVE_TIT_VALOR_DOCUMENTO          => 449.99,
        ];

        FunctionHelper::formataCampoDateTimeJS("2018-10-26T14:06:40.000Z", $data_vencimento);
        $parcelas[2] = [
            ConstanteParametros::CHAVE_ID                           => $condicaoPagamentoParcela[1],
            ConstanteParametros::CHAVE_TIT_DATA_VENCIMENTO          => $data_vencimento,
            ConstanteParametros::CHAVE_TIT_NUMERO_PARCELA_DOCUMENTO => 2,
            ConstanteParametros::CHAVE_TIT_VALOR_DOCUMENTO          => 375,
        ];

        FunctionHelper::formataCampoDateTimeJS("2018-11-25T14:06:40.000Z", $data_vencimento);
        $parcelas[3] = [
            ConstanteParametros::CHAVE_ID                           => $condicaoPagamentoParcela[2],
            ConstanteParametros::CHAVE_TIT_DATA_VENCIMENTO          => $data_vencimento,
            ConstanteParametros::CHAVE_TIT_NUMERO_PARCELA_DOCUMENTO => 3,
            ConstanteParametros::CHAVE_TIT_VALOR_DOCUMENTO          => 225,
        ];

        FunctionHelper::formataCampoDateTimeJS("2018-12-25T14:06:40.000Z", $data_vencimento);
        $parcelas[4] = [
            ConstanteParametros::CHAVE_ID                           => $condicaoPagamentoParcela[3],
            ConstanteParametros::CHAVE_TIT_DATA_VENCIMENTO          => $data_vencimento,
            ConstanteParametros::CHAVE_TIT_NUMERO_PARCELA_DOCUMENTO => 4,
            ConstanteParametros::CHAVE_TIT_VALOR_DOCUMENTO          => 225,
        ];

        FunctionHelper::formataCampoDateTimeJS("2019-01-24T14:06:40.000Z", $data_vencimento);
        $parcelas[5] = [
            ConstanteParametros::CHAVE_ID                           => $condicaoPagamentoParcela[4],
            ConstanteParametros::CHAVE_TIT_DATA_VENCIMENTO          => $data_vencimento,
            ConstanteParametros::CHAVE_TIT_NUMERO_PARCELA_DOCUMENTO => 5,
            ConstanteParametros::CHAVE_TIT_VALOR_DOCUMENTO          => 225,
        ];

        $mensagemErro = "";
        $retorno      = $this->tituloPagarBO->podeCriar($parametros, $mensagemErro, $parcelas);

        $this->assertTrue($retorno, FunctionHelper::mostrarTextoUnitVermelho("Ocorreu um erro no teste: " . $mensagemErro));
    }

    public function testPodeSalvarInvalido()
    {
        $parametros = [
            "conta"                            => 999,
            "valor_total_nota"                 => 1499.99,
            ConstanteParametros::CHAVE_PARCELA => [],
        ];
        $parametros[ConstanteParametros::CHAVE_PARCELA][] = [
            ConstanteParametros::CHAVE_TIT_VALOR_DOCUMENTO          => 449.99,
            ConstanteParametros::CHAVE_TIT_DATA_VENCIMENTO          => "2018-09-26T14:06:40.000Z",
            ConstanteParametros::CHAVE_TIT_NUMERO_PARCELA_DOCUMENTO => 1,
        ];
        $parametros[ConstanteParametros::CHAVE_PARCELA][] = [
            ConstanteParametros::CHAVE_TIT_VALOR_DOCUMENTO          => 375,
            ConstanteParametros::CHAVE_TIT_DATA_VENCIMENTO          => "2018-10-26T14:06:40.000Z",
            ConstanteParametros::CHAVE_TIT_NUMERO_PARCELA_DOCUMENTO => 2,
        ];
        $parametros[ConstanteParametros::CHAVE_PARCELA][] = [
            ConstanteParametros::CHAVE_TIT_VALOR_DOCUMENTO          => 225,
            ConstanteParametros::CHAVE_TIT_DATA_VENCIMENTO          => "2018-11-25T14:06:40.000Z",
            ConstanteParametros::CHAVE_TIT_NUMERO_PARCELA_DOCUMENTO => 3,
        ];
        $parametros[ConstanteParametros::CHAVE_PARCELA][] = [
            ConstanteParametros::CHAVE_TIT_VALOR_DOCUMENTO          => 225,
            ConstanteParametros::CHAVE_TIT_DATA_VENCIMENTO          => "2018-12-25T14:06:40.000Z",
            ConstanteParametros::CHAVE_TIT_NUMERO_PARCELA_DOCUMENTO => 4,
        ];
        $parametros[ConstanteParametros::CHAVE_PARCELA][] = [
            ConstanteParametros::CHAVE_TIT_VALOR_DOCUMENTO          => 225,
            ConstanteParametros::CHAVE_TIT_DATA_VENCIMENTO          => "2019-01-24T14:06:40.000Z",
            ConstanteParametros::CHAVE_TIT_NUMERO_PARCELA_DOCUMENTO => 5,
        ];

        $condicaoPagamentoParcela = $this->condicaoPagamentoParcelaRepository->findBy([ConstanteParametros::CHAVE_ID => [1, 2, 3, 4, 5]]);

        $parcelas        = [];
        $data_vencimento = "";

        FunctionHelper::formataCampoDateTimeJS("2018-09-26T14:06:40.000Z", $data_vencimento);
        $parcelas[1] = [
            ConstanteParametros::CHAVE_ID                           => $condicaoPagamentoParcela[0],
            ConstanteParametros::CHAVE_TIT_DATA_VENCIMENTO          => $data_vencimento,
            ConstanteParametros::CHAVE_TIT_NUMERO_PARCELA_DOCUMENTO => 1,
            ConstanteParametros::CHAVE_TIT_VALOR_DOCUMENTO          => 449.99,
        ];

        FunctionHelper::formataCampoDateTimeJS("2018-10-26T14:06:40.000Z", $data_vencimento);
        $parcelas[2] = [
            ConstanteParametros::CHAVE_ID                           => $condicaoPagamentoParcela[1],
            ConstanteParametros::CHAVE_TIT_DATA_VENCIMENTO          => $data_vencimento,
            ConstanteParametros::CHAVE_TIT_NUMERO_PARCELA_DOCUMENTO => 2,
            ConstanteParametros::CHAVE_TIT_VALOR_DOCUMENTO          => 375,
        ];

        FunctionHelper::formataCampoDateTimeJS("2018-11-25T14:06:40.000Z", $data_vencimento);
        $parcelas[3] = [
            ConstanteParametros::CHAVE_ID                           => $condicaoPagamentoParcela[2],
            ConstanteParametros::CHAVE_TIT_DATA_VENCIMENTO          => $data_vencimento,
            ConstanteParametros::CHAVE_TIT_NUMERO_PARCELA_DOCUMENTO => 3,
            ConstanteParametros::CHAVE_TIT_VALOR_DOCUMENTO          => 225,
        ];

        FunctionHelper::formataCampoDateTimeJS("2018-12-25T14:06:40.000Z", $data_vencimento);
        $parcelas[4] = [
            ConstanteParametros::CHAVE_ID                           => $condicaoPagamentoParcela[3],
            ConstanteParametros::CHAVE_TIT_DATA_VENCIMENTO          => $data_vencimento,
            ConstanteParametros::CHAVE_TIT_NUMERO_PARCELA_DOCUMENTO => 4,
            ConstanteParametros::CHAVE_TIT_VALOR_DOCUMENTO          => 225,
        ];

        FunctionHelper::formataCampoDateTimeJS("2019-01-24T14:06:40.000Z", $data_vencimento);
        $parcelas[5] = [
            ConstanteParametros::CHAVE_ID                           => $condicaoPagamentoParcela[4],
            ConstanteParametros::CHAVE_TIT_DATA_VENCIMENTO          => $data_vencimento,
            ConstanteParametros::CHAVE_TIT_NUMERO_PARCELA_DOCUMENTO => 5,
            ConstanteParametros::CHAVE_TIT_VALOR_DOCUMENTO          => 225,
        ];

        $mensagemErro = "";
        $retorno      = $this->tituloPagarBO->podeCriar($parametros, $mensagemErro, $parcelas);

        $this->assertFalse($retorno === true, FunctionHelper::mostrarTextoUnitVermelho("Ocorreu um erro no teste"));

    }

    public function testPodeCalcularValido()
    {
        $parametros = [
            ConstanteParametros::CHAVE_CONDICAO_PAGAMENTO => 1,
            ConstanteParametros::CHAVE_NF_DATA_EMISSAO    => "2018-08-28T14:06:40.000Z",
        ];

        $mensagemErro         = "";
        $condicaoPagamentoORM = null;
        $retorno = $this->tituloPagarBO->podeCalcularParcelas($parametros, $mensagemErro, $condicaoPagamentoORM);

        $this->assertTrue($retorno, FunctionHelper::mostrarTextoUnitVermelho("Ocorreu um erro no teste"));
        $this->assertEquals(CondicaoPagamento::class, get_class($condicaoPagamentoORM), FunctionHelper::mostrarTextoUnitVermelho("O Retorno de objetos nao eh o mesmo que o esperado"));
        $this->assertEmpty($mensagemErro, FunctionHelper::mostrarTextoUnitVermelho("Ocorreu algum erro no ORM, segue msg de erro:\n" . $mensagemErro));
    }

    public function testPodeCalcularInvalido()
    {
        $parametros = [
            ConstanteParametros::CHAVE_CONDICAO_PAGAMENTO => 999,
            ConstanteParametros::CHAVE_NF_DATA_EMISSAO    => "2018-08-28T14:06:40.000Z",
        ];

        $mensagemErro         = "";
        $condicaoPagamentoORM = null;
        $retorno = $this->tituloPagarBO->podeCalcularParcelas($parametros, $mensagemErro, $condicaoPagamentoORM);

        $this->assertFalse($retorno === true, FunctionHelper::mostrarTextoUnitVermelho("Ocorreu um erro no teste"));
        $this->assertEquals(null, $condicaoPagamentoORM, FunctionHelper::mostrarTextoUnitVermelho("O Retorno de objetos nao eh o mesmo que o esperado"));
        $this->assertNotEmpty($mensagemErro, FunctionHelper::mostrarTextoUnitVermelho("O retorno esperado deste teste eh para que tenha vindo um erro de base de dados"));
    }

    public function testPodeListarValido()
    {
        $parametros = [
            ConstanteParametros::CHAVE_FRANQUEADA       => 1,
            ConstanteParametros::CHAVE_TIT_DATA_INICIAL => "2018-09-26T00:00:00.000Z",
            ConstanteParametros::CHAVE_TIT_DATA_FINAL   => "2018-11-25T23:59:59.000Z",
        ];

        $mensagemErro = "";
        $retorno      = $this->tituloPagarBO->podeListar($parametros, $mensagemErro);

        $this->assertTrue($retorno, FunctionHelper::mostrarTextoUnitVermelho("Ocorreu um erro no teste"));
        $this->assertEmpty($mensagemErro, FunctionHelper::mostrarTextoUnitVermelho("Ocorreu algum erro, segue msg de erro:\n" . $mensagemErro));
    }

    public function testPodeListarInvalido()
    {
        $parametros = [
            ConstanteParametros::CHAVE_FRANQUEADA       => 1,
            ConstanteParametros::CHAVE_TIT_DATA_INICIAL => "2018-09-26T00:00:00.000Z",
            ConstanteParametros::CHAVE_TIT_DATA_FINAL   => "2018-11-25",
        ];

        $mensagemErro = "";
        $retorno      = $this->tituloPagarBO->podeListar($parametros, $mensagemErro);

        $this->assertFalse($retorno === true, FunctionHelper::mostrarTextoUnitVermelho("Ocorreu um erro no teste"));
        $this->assertNotEmpty($mensagemErro, FunctionHelper::mostrarTextoUnitVermelho("Ocorreu algum erro, segue msg de erro:\n" . $mensagemErro));
    }

    public function testVerificaTituloPagarExiste()
    {
        $mensagemErro   = "";
        $tituloPagarORM = null;
        $retorno        = $this->tituloPagarBO->verificaTituloPagarExisteId($this->tituloPagarRepository, 1, $mensagemErro, $tituloPagarORM);

        $this->assertTrue($retorno === true, FunctionHelper::mostrarTextoUnitVermelho("TituloPagar nao encontrado na base de dados.\n" . $mensagemErro));
        $this->assertEquals(TituloPagar::class, get_class($tituloPagarORM), FunctionHelper::mostrarTextoUnitVermelho("O Retorno de objetos nao eh o mesmo que o esperado"));
        $this->assertEmpty($mensagemErro, FunctionHelper::mostrarTextoUnitVermelho("Ocorreu algum erro no ORM, segue msg de erro:\n" . $mensagemErro));
    }

    public function testVerificaTituloPagarNaoExiste()
    {
        $mensagemErro   = "";
        $tituloPagarORM = null;
        $retorno        = $this->tituloPagarBO->verificaTituloPagarExisteId($this->tituloPagarRepository, 999, $mensagemErro, $tituloPagarORM);

        $this->assertFalse($retorno === true, FunctionHelper::mostrarTextoUnitVermelho("TituloPagar foi encontrado na base, e isto nao poderia acontecer."));
        $this->assertEquals(null, $tituloPagarORM, FunctionHelper::mostrarTextoUnitVermelho("O Retorno de objetos nao eh o mesmo que o esperado"));
        $this->assertNotEmpty($mensagemErro, FunctionHelper::mostrarTextoUnitVermelho("O retorno esperado deste teste eh para que tenha vindo um erro de base de dados"));
    }

    public function testPodeAtualizarSaldoValido()
    {
        $parametros   = [
            ConstanteParametros::CHAVE_VALOR_SALDO_CALCULADO    => 0,
            ConstanteParametros::CHAVE_MC_VALOR_MONTANTE        => 350,
            ConstanteParametros::CHAVE_MC_VALOR_LANCAMENTO      => 350,
            ConstanteParametros::CHAVE_MC_VALOR_DESCONTO        => null,
            ConstanteParametros::CHAVE_MC_VALOR_JUROS           => null,
            ConstanteParametros::CHAVE_MC_VALOR_DIFERENCA_BAIXA => null,
            ConstanteParametros::CHAVE_TIT_VALOR_SALDO          => 449.99,
        ];
        $mensagemErro = "";
        $retorno      = $this->tituloPagarBO->podeAtualizarSaldo($parametros, $mensagemErro);

        $this->assertTrue($retorno === true, FunctionHelper::mostrarTextoUnitVermelho("Ocorreu algum erro, segue msg de erro:\n" . $mensagemErro));
        $this->assertEmpty($mensagemErro, FunctionHelper::mostrarTextoUnitVermelho("Ocorreu algum erro no ORM, segue msg de erro:\n" . $mensagemErro));
    }

    public function testPodeAtualizarSaldoInvalido()
    {
        $parametros   = [
            ConstanteParametros::CHAVE_VALOR_SALDO_CALCULADO    => 0,
            ConstanteParametros::CHAVE_MC_VALOR_MONTANTE        => 449.99,
            ConstanteParametros::CHAVE_MC_VALOR_LANCAMENTO      => 449.99,
            ConstanteParametros::CHAVE_MC_VALOR_DESCONTO        => null,
            ConstanteParametros::CHAVE_MC_VALOR_JUROS           => null,
            ConstanteParametros::CHAVE_MC_VALOR_DIFERENCA_BAIXA => null,
            ConstanteParametros::CHAVE_TIT_VALOR_SALDO          => 350,
        ];
        $mensagemErro = "";
        $retorno      = $this->tituloPagarBO->podeAtualizarSaldo($parametros, $mensagemErro);

        $this->assertFalse($retorno === true, FunctionHelper::mostrarTextoUnitVermelho("Ocorreu algum erro, segue msg de erro:\n" . $mensagemErro));
        $this->assertNotEmpty($mensagemErro, FunctionHelper::mostrarTextoUnitVermelho("Ocorreu algum erro, segue msg de erro:\n" . $mensagemErro));
    }

    public function testCalculaValoresSaldoValido()
    {
        $parametros = [
            ConstanteParametros::CHAVE_VALOR_SALDO_CALCULADO    => 449.99,
            ConstanteParametros::CHAVE_MC_VALOR_DIFERENCA_BAIXA => 0,
            ConstanteParametros::CHAVE_TIT_VALOR_SALDO          => 449.99,
        ];
        $valor_calculado_esperado = round(($parametros[ConstanteParametros::CHAVE_TIT_VALOR_SALDO] - $parametros[ConstanteParametros::CHAVE_VALOR_SALDO_CALCULADO]), 2);

        $tituloPagarORM = new TituloPagar();
        $tituloPagarORM->setValorDocumento(449.99);

        $this->tituloPagarBO->calculaValoresSaldo($parametros, $tituloPagarORM);
        $valor_saldo_titulo = $tituloPagarORM->getValorSaldo();

        $this->assertEquals($valor_calculado_esperado, $valor_saldo_titulo, FunctionHelper::mostrarTextoUnitVermelho("O valor do saldo esperado está incorreto.\nValor esperado: " . $valor_calculado_esperado . " Saldo do titulo: " . $valor_saldo_titulo));

        if ($valor_saldo_titulo === 0) {
            $situacao_esperada = SituacoesSistema::SITUACAO_BAIXADO;
            $situacao_titulo   = $tituloPagarORM->getSituacao();
            $this->assertEquals($situacao_esperada, $situacao_titulo, FunctionHelper::mostrarTextoUnitVermelho("A situacao não corresponde com a esperada.\Situacao esperada: " . $situacao_esperada . " Situacao do titulo: " . $situacao_titulo));
        }
    }

    public function testCalculaValoresSaldoInvalido()
    {
        $parametros = [
            ConstanteParametros::CHAVE_VALOR_SALDO_CALCULADO    => 350,
            ConstanteParametros::CHAVE_MC_VALOR_DIFERENCA_BAIXA => null,
            ConstanteParametros::CHAVE_TIT_VALOR_SALDO          => 449.99,
        ];
        $valor_calculado_esperado = round(449.99, 2);
        $mensagemErro = "";

        $tituloPagarORM = new TituloPagar();
        $tituloPagarORM->setValorDocumento(449.99);

        $this->tituloPagarBO->calculaValoresSaldo($parametros, $tituloPagarORM);
        $valor_saldo_titulo = $tituloPagarORM->getValorSaldo();

        $this->assertNotEquals($valor_calculado_esperado, $valor_saldo_titulo, FunctionHelper::mostrarTextoUnitVermelho("O valor do saldo esperado está incorreto. Saldo esperado nao pode ser igual ao saldo do titulo"));

        if ($valor_saldo_titulo === 0) {
            $situacao_esperada = SituacoesSistema::SITUACAO_BAIXADO;
            $situacao_titulo   = $tituloPagarORM->getSituacao();
            $this->assertNotEquals($situacao_esperada, $situacao_titulo, FunctionHelper::mostrarTextoUnitVermelho("A situacao nao corresponde com a esperada.\nSituacao esperada: " . $situacao_esperada . " Situacao do titulo: " . $situacao_titulo));
        }
    }


}
