<?php
namespace App\Tests\BO;

use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use App\Helper\FunctionHelper;
use App\BO\Principal\CondicaoPagamentoParcelaBO;

/**
 *
 * @author Luiz Antonio Costa
 */
class CondicaoPagamentoParcelaBOTest extends KernelTestCase
{
    /**
     *
     * @var \App\BO\Principal\CondicaoPagamentoParcelaBO
     */
    private $condicaoPagamentoParcelaBO;

    protected function setUp()
    {
        $this->condicaoPagamentoParcelaBO = new CondicaoPagamentoParcelaBO();
    }

    protected function tearDown()
    {
        $this->condicaoPagamentoParcelaBO = null;
    }

    public function testParcelaDiasValidos()
    {
        $parcelasArray = [
            [
                "numero_parcela"     => 1,
                "dias_vencimento"    => 20,
                "percentual_parcela" => 40,
            ],
            [
                "numero_parcela"     => 2,
                "dias_vencimento"    => 20,
                "percentual_parcela" => 60,
            ],
        ];
        $mensagemErro  = "";
        $retorno       = $this->condicaoPagamentoParcelaBO->parcelaValida($parcelasArray, $mensagemErro);
        $this->assertTrue(true === $retorno, FunctionHelper::mostrarTextoUnitVermelho("Ocorreu um erro:" . $mensagemErro));
    }

    public function testParcelaDiasInvalidos()
    {
        $parcelasArray = [
            [
                "numero_parcela"     => 1,
                "dias_vencimento"    => 20,
                "percentual_parcela" => 40,
            ],
            [
                "numero_parcela"     => 2,
                "dias_vencimento"    => 10,
                "percentual_parcela" => 60,
            ],
        ];
        $mensagemErro  = "";
        $retorno       = $this->condicaoPagamentoParcelaBO->parcelaValida($parcelasArray, $mensagemErro);
        $this->assertFalse(true === $retorno, FunctionHelper::mostrarTextoUnitVermelho("O teste que foi programado para ter erro, concluiu com exito"));
    }

    public function testParcelasPercentualValido()
    {
        $parcelasArray = [
            [
                "numero_parcela"     => 1,
                "dias_vencimento"    => 20,
                "percentual_parcela" => 40,
            ],
            [
                "numero_parcela"     => 2,
                "dias_vencimento"    => 20,
                "percentual_parcela" => 60,
            ],
        ];
        $mensagemErro  = "";
        $retorno       = $this->condicaoPagamentoParcelaBO->parcelaValida($parcelasArray, $mensagemErro);
        $this->assertTrue(true === $retorno, FunctionHelper::mostrarTextoUnitVermelho("Ocorreu um erro no teste:" . $mensagemErro));
    }

    public function testParcelasPercentualInvalido()
    {
        $parcelasArray = [
            [
                "numero_parcela"     => 1,
                "dias_vencimento"    => 20,
                "percentual_parcela" => 40,
            ],
            [
                "numero_parcela"     => 2,
                "dias_vencimento"    => 20,
                "percentual_parcela" => 40,
            ],
        ];
        $mensagemErro  = "";
        $retorno       = $this->condicaoPagamentoParcelaBO->parcelaValida($parcelasArray, $mensagemErro);
        $this->assertFalse(true === $retorno, FunctionHelper::mostrarTextoUnitVermelho("O teste que foi programado para ter erro, concluiu com exito"));
    }


}
