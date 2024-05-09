<?php
namespace App\BO\Principal;

use App\Helper\ConstanteParametros;

/**
 *
 * @author Luiz Antonio Costa
 */
class CondicaoPagamentoParcelaBO
{


    /**
     * Verifica se algum dia de vencimento informado eh menor que o dia de vencimento informado na primeira parcela
     *
     * @param array $parcelasArray Array de parcelas
     * @param string $mensagemErro Mensagem de erro para ser enviado para o front-end
     *
     * @return boolean
     */
    protected function diasValidos($parcelasArray, &$mensagemErro)
    {
        $bRetorno = true;
        $menorDia = $parcelasArray[0][ConstanteParametros::CHAVE_DIAS_VENCIMENTO_PARCELA];
        foreach ($parcelasArray as $parcela) {
            if ($parcela[ConstanteParametros::CHAVE_DIAS_VENCIMENTO_PARCELA] < $menorDia) {
                $mensagemErro .= "Existem dias que não batem com o valor informado.";
                $bRetorno      = false;
                break;
            }
        }

        return $bRetorno;
    }

    /**
     * Realiza a somatoria dos percentuais das parcelas e verifica se fechou em exatamente 100.0%
     *
     * @param array $parcelasArray Array de parcelas
     * @param string $mensagemErro Mensagem de erro para ser enviado para o front-end
     *
     * @return boolean
     */
    protected function totalPercentualValido($parcelasArray, &$mensagemErro)
    {
        $bRetorno     = true;
        $totalParcela = 0;
        foreach ($parcelasArray as $parcela) {
            $totalParcela += $parcela[ConstanteParametros::CHAVE_PERCENTUAL_PARCELA];
        }

        if (round($totalParcela, 2) !== 100.0) {
            $mensagemErro .= "O valor total da parcela não fechou em 100%, fechou em total de: " . $totalParcela . "%";
            $bRetorno      = false;
        }

        return $bRetorno;
    }

    /**
     * Realiza a validacao para checar se os dias informados no indice de parcela estao validos e se o percentual bateu com o que devia
     *
     * @param array $parcelasArray Array de parcelas
     * @param string $mensagemErro Mensagem de erro para retornar pro front-end
     *
     * @return boolean
     */
    public function parcelaValida($parcelasArray, &$mensagemErro)
    {
        if (self::diasValidos($parcelasArray, $mensagemErro) === true) {
            if (self::totalPercentualValido($parcelasArray, $mensagemErro) === true) {
                return true;
            }
        }

        return false;
    }


}
