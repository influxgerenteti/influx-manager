<?php
namespace App\BO\Principal;

use Doctrine\ORM\EntityManagerInterface;
use App\Helper\ConstanteParametros;

/**
 *
 * @author Luiz Antonio Costa
 */
class ParametrosFranqueadoraBO extends GenericBO
{

    /**
     *
     * @var \App\Repository\Principal\ParametrosFranqueadoraRepository
     */
    private static $parametrosFranqueadoraRepository;

    function __construct(EntityManagerInterface $entityManager)
    {
        self::$parametrosFranqueadoraRepository = $entityManager->getRepository(\App\Entity\Principal\ParametrosFranqueadora::class);
    }

    /**
     * Verifica a variação de dias para a data de vencimento de cada título. Caso a variação de dias ultrapasse a configuração feita pela franqueadora na tabela ParametrosFranqueadora, irá retornar falso, para que estore um erro para o front end, informando o problema
     *
     * @param \DateTime $data_vencimento_base Data de vencimento utilizada como base para realizar as validações. Está data é calculada a partir da data de emissão da conta a pagar e conforme a condição de pagamento utilizada
     * @param \DateTime $data_vencimento_parcela Data de vencimento que o usuário informou no front end para cada parcela
     * @param integer $dias_variacao_vencimento Parametro de variação de dias de vencimento que vem do banco de dados, introduzida pela franqueadora
     *
     * @return boolean
     */
    protected function verificaVariacaoDiasVencimento($data_vencimento_base, $data_vencimento_parcela, $dias_variacao_vencimento)
    {
        $objeto_intervalo_dias = $data_vencimento_base->diff($data_vencimento_parcela);
        $intervalo_dias        = (int) $objeto_intervalo_dias->format('%a');

        if ($intervalo_dias > $dias_variacao_vencimento) {
            return false;
        }

        return true;
    }

    /**
     * Verifica a variação do percentual dos valores de cada título. Caso a variação do percentual ultrapasse a configuração feita pela franqueadora na tabela ParametrosFranqueadora, irá retornar falso, para que estore um erro para o front end, informando o problema
     *
     * @param float $valor_documento Valor do documento que o usuário informou no front end para cada parcela
     * @param float $valor_titulo Valor total do titulo da conta a pagar informada pelo usuário na conta a pagar
     * @param float $percentual_parcela Percentual de parcela da parcela que está sendo validada conforme a condição de pagamento parcelas
     * @param float $percentual_variacao_valores Parametro de variação de percentual dos valores que vem do banco de dados, introduzida pela franqueadora
     *
     * @return boolean
     */
    protected function verificaVariacaoPercentualValores(float $valor_documento, float $valor_titulo, float $percentual_parcela, float $percentual_variacao_valores)
    {
        $porcentagem_parcela = round($valor_documento * 100 / $valor_titulo, 2);
        $calculo_variacao_percentual_parcela = abs($percentual_parcela - $porcentagem_parcela);

        if ($calculo_variacao_percentual_parcela > $percentual_variacao_valores) {
            return false;
        }

        return true;
    }

    /**
     * Verifica se as datas estão dentro dos parâmetros da franqueadora
     *
     * @param array $parametros Ponteiro de Array de parametros da requisicao
     * @param string $mensagemErro Mensagem de erro a retornar pro front-end
     * @param array $parcelasCalculadas Array de parcelas calculadas para validação dos parâmetros da franqueadora
     *
     * @return boolean
     */
    public static function verificaParametrosFranqueadora(&$parametros, &$mensagemErro, $parcelasCalculadas)
    {
        $bRetorno = true;
        $parametrosFranqueadora = self::$parametrosFranqueadoraRepository->find(1);

        $dias_variacao_vencimento    = $parametrosFranqueadora->getDiasVariacaoVencimento();
        $percentual_variacao_valores = $parametrosFranqueadora->getPercentualVariacaoValores();

        foreach ($parametros[ConstanteParametros::CHAVE_PARCELA] as $parcela) {
            $parcelaCalculada = $parcelasCalculadas[$parcela[ConstanteParametros::CHAVE_TIT_NUMERO_PARCELA_DOCUMENTO]];

            if ((is_null($dias_variacao_vencimento) === false) && (self::verificaVariacaoDiasVencimento($parcelaCalculada[ConstanteParametros::CHAVE_TIT_DATA_VENCIMENTO], $parcela[ConstanteParametros::CHAVE_TIT_DATA_VENCIMENTO], $dias_variacao_vencimento) === false)) {
                $mensagemErro = "Intervalo de variação de dias de vencimento da parcela " . $parcela[ConstanteParametros::CHAVE_TIT_NUMERO_PARCELA_DOCUMENTO] . " é maior do que o configurado pela franqueadora de " . $dias_variacao_vencimento . " dias.";
                $bRetorno     = false;
                break;
            }

            $condicaoPagametoParcela = $parcelaCalculada[ConstanteParametros::CHAVE_ID];
            if ((is_null($percentual_variacao_valores) === false) && (self::verificaVariacaoPercentualValores($parcela[ConstanteParametros::CHAVE_TIT_VALOR_DOCUMENTO], $parametros[ConstanteParametros::CHAVE_NF_VALOR_TITULO], $condicaoPagametoParcela->getPercentualParcela(), $percentual_variacao_valores) === false)) {
                $mensagemErro = "Variação do percentual da parcela calculado maior do que o configurado pela franqueadora de " . $percentual_variacao_valores . "% para a parcela " . $parcela[ConstanteParametros::CHAVE_TIT_NUMERO_PARCELA_DOCUMENTO];
                $bRetorno     = false;
                break;
            }
        }

        return $bRetorno;
    }

    /**
     * Retorna a média definida nos parametros para o corte de média
     *
     * @return number
     */
    public function retornaNotaDeCorteMedia()
    {
        $parametrosFranqueadoraORM = self::$parametrosFranqueadoraRepository->find(1);
        return $parametrosFranqueadoraORM->getNotaCorteMedia();
    }

    /**
     * Retorna a média definada nos parametros para o corte de média
     *
     * @return number
     */
    public function retornaNotaDeCorteMediaConceitual()
    {
        $parametrosFranqueadoraORM = self::$parametrosFranqueadoraRepository->find(1);
        return $parametrosFranqueadoraORM->getNotaConceitualCorteMedia();
    }


}
