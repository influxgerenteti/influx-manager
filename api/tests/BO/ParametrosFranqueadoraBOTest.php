<?php
namespace App\Tests\BO;

use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use App\Helper\FunctionHelper;
use App\Helper\ConstanteParametros;
use App\BO\Principal\ParametrosFranqueadoraBO;
use App\Facade\Principal\TituloPagarFacade;

/**
 *
 * @author Luiz Antonio Costa
 */
class ParametrosFranqueadoraBOTest extends KernelTestCase
{

    /**
     *
     * @var \App\BO\Principal\ParametrosFranqueadoraBO
     */
    private $parametrosFranqueadoraBO;

    /**
     *
     * @var \App\Facade\Principal\TituloPagarFacade
     */
    private $tituloPagarFacade;

    protected function setUp()
    {
        $kernel = self::bootKernel();
        $this->parametrosFranqueadoraBO = new ParametrosFranqueadoraBO($kernel->getContainer()->get('doctrine')->getManager());
        $this->tituloPagarFacade        = new TituloPagarFacade($kernel->getContainer()->get('doctrine'));
    }

    protected function tearDown()
    {
        $this->parametrosFranqueadoraBO = null;
        $this->tituloPagarFacade        = null;
    }

    public function testParametrosFranqueadoraValido()
    {
        $parametros = [
            ConstanteParametros::CHAVE_CONDICAO_PAGAMENTO => 1,
            ConstanteParametros::CHAVE_NF_VALOR_TITULO    => 1430,
            ConstanteParametros::CHAVE_NF_DATA_EMISSAO    => "2018-08-27T14:06:40.000Z",
            ConstanteParametros::CHAVE_PARCELA            => [],
        ];
        $parametros[ConstanteParametros::CHAVE_PARCELA][1] = [
            ConstanteParametros::CHAVE_TIT_DATA_VENCIMENTO          => "2018-09-26T14:06:40.000Z",
            ConstanteParametros::CHAVE_TIT_NUMERO_PARCELA_DOCUMENTO => 1,
            ConstanteParametros::CHAVE_TIT_VALOR_DOCUMENTO          => 429.00,
        ];
        $parametros[ConstanteParametros::CHAVE_PARCELA][2] = [
            ConstanteParametros::CHAVE_TIT_DATA_VENCIMENTO          => "2018-10-26T14:06:40.000Z",
            ConstanteParametros::CHAVE_TIT_NUMERO_PARCELA_DOCUMENTO => 2,
            ConstanteParametros::CHAVE_TIT_VALOR_DOCUMENTO          => 357.50,
        ];
        $parametros[ConstanteParametros::CHAVE_PARCELA][3] = [
            ConstanteParametros::CHAVE_TIT_DATA_VENCIMENTO          => "2018-11-25T14:06:40.000Z",
            ConstanteParametros::CHAVE_TIT_NUMERO_PARCELA_DOCUMENTO => 3,
            ConstanteParametros::CHAVE_TIT_VALOR_DOCUMENTO          => 214.50,
        ];
        $parametros[ConstanteParametros::CHAVE_PARCELA][4] = [
            ConstanteParametros::CHAVE_TIT_DATA_VENCIMENTO          => "2018-12-25T14:06:40.000Z",
            ConstanteParametros::CHAVE_TIT_NUMERO_PARCELA_DOCUMENTO => 4,
            ConstanteParametros::CHAVE_TIT_VALOR_DOCUMENTO          => 214.50,
        ];
        $parametros[ConstanteParametros::CHAVE_PARCELA][5] = [
            ConstanteParametros::CHAVE_TIT_DATA_VENCIMENTO          => "2019-01-24T14:06:40.000Z",
            ConstanteParametros::CHAVE_TIT_NUMERO_PARCELA_DOCUMENTO => 5,
            ConstanteParametros::CHAVE_TIT_VALOR_DOCUMENTO          => 214.50,
        ];

        foreach ($parametros[ConstanteParametros::CHAVE_PARCELA] as &$parcela) {
            FunctionHelper::formataCampoDateTimeJS($parcela[ConstanteParametros::CHAVE_TIT_DATA_VENCIMENTO], $parcela[ConstanteParametros::CHAVE_TIT_DATA_VENCIMENTO]);
        }

        $mensagemErro       = "";
        $parcelasCalculadas = $this->tituloPagarFacade->calcular($mensagemErro, $parametros);
        $retorno            = $this->parametrosFranqueadoraBO->verificaParametrosFranqueadora($parametros, $mensagemErro, $parcelasCalculadas);

        $this->assertTrue($retorno, FunctionHelper::mostrarTextoUnitVermelho("Ocorreu um erro no teste: " . $mensagemErro));
        $this->assertEmpty($mensagemErro, FunctionHelper::mostrarTextoUnitVermelho("O retorno esperado era para que nao contenha erros na variavel de mensagem porem obteve."));
    }

    public function testParametrosFranqueadoraInvalido()
    {
        $parametros = [
            ConstanteParametros::CHAVE_CONDICAO_PAGAMENTO => 1,
            ConstanteParametros::CHAVE_NF_VALOR_TITULO    => 1430,
            ConstanteParametros::CHAVE_NF_DATA_EMISSAO    => "2018-08-27T14:06:40.000Z",
            ConstanteParametros::CHAVE_PARCELA            => [],
        ];
        $parametros[ConstanteParametros::CHAVE_PARCELA][1] = [
            ConstanteParametros::CHAVE_TIT_DATA_VENCIMENTO          => "2018-09-26T14:06:40.000Z",
            ConstanteParametros::CHAVE_TIT_NUMERO_PARCELA_DOCUMENTO => 1,
            ConstanteParametros::CHAVE_TIT_VALOR_DOCUMENTO          => 429.00,
        ];
        $parametros[ConstanteParametros::CHAVE_PARCELA][2] = [
            ConstanteParametros::CHAVE_TIT_DATA_VENCIMENTO          => "2018-10-26T14:06:40.000Z",
            ConstanteParametros::CHAVE_TIT_NUMERO_PARCELA_DOCUMENTO => 2,
            ConstanteParametros::CHAVE_TIT_VALOR_DOCUMENTO          => 359.00,
        ];
        $parametros[ConstanteParametros::CHAVE_PARCELA][3] = [
            ConstanteParametros::CHAVE_TIT_DATA_VENCIMENTO          => "2018-11-25T14:06:40.000Z",
            ConstanteParametros::CHAVE_TIT_NUMERO_PARCELA_DOCUMENTO => 3,
            ConstanteParametros::CHAVE_TIT_VALOR_DOCUMENTO          => 214.00,
        ];
        $parametros[ConstanteParametros::CHAVE_PARCELA][4] = [
            ConstanteParametros::CHAVE_TIT_DATA_VENCIMENTO          => "2018-12-25T14:06:40.000Z",
            ConstanteParametros::CHAVE_TIT_NUMERO_PARCELA_DOCUMENTO => 4,
            ConstanteParametros::CHAVE_TIT_VALOR_DOCUMENTO          => 214.00,
        ];
        $parametros[ConstanteParametros::CHAVE_PARCELA][5] = [
            ConstanteParametros::CHAVE_TIT_DATA_VENCIMENTO          => "2019-01-24T14:06:40.000Z",
            ConstanteParametros::CHAVE_TIT_NUMERO_PARCELA_DOCUMENTO => 5,
            ConstanteParametros::CHAVE_TIT_VALOR_DOCUMENTO          => 214.00,
        ];

        foreach ($parametros[ConstanteParametros::CHAVE_PARCELA] as &$parcela) {
            FunctionHelper::formataCampoDateTimeJS($parcela[ConstanteParametros::CHAVE_TIT_DATA_VENCIMENTO], $parcela[ConstanteParametros::CHAVE_TIT_DATA_VENCIMENTO]);
        }

        $mensagemErro       = "";
        $parcelasCalculadas = $this->tituloPagarFacade->calcular($mensagemErro, $parametros);
        $retorno            = $this->parametrosFranqueadoraBO->verificaParametrosFranqueadora($parametros, $mensagemErro, $parcelasCalculadas);

        $this->assertFalse($retorno === true, FunctionHelper::mostrarTextoUnitVermelho("O teste foi programado para que o retorno fosse FALSE porem o registro foi encontrado, do qual nao deveria existir."));
        $this->assertNotEmpty($mensagemErro, FunctionHelper::mostrarTextoUnitVermelho("O teste estava esperando que tivesse retorno da pesquisa, porem nao obteve."));
    }


}
