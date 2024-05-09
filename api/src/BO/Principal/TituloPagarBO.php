<?php
namespace App\BO\Principal;

use App\Helper\ConstanteParametros;
use Doctrine\ORM\EntityManagerInterface;

/**
 *
 * @author Rodrigo de Souza Fernandes (GATI labs)
 */
class TituloPagarBO extends GenericBO
{


    function __construct(EntityManagerInterface $entityManager)
    {
        parent::configuraGenericBO(
            [
                "franqueadaRepository"        => $entityManager->getRepository(\App\Entity\Principal\Franqueada::class),
                "pessoaRepository"            => $entityManager->getRepository(\App\Entity\Principal\Pessoa::class),
                "contaRepository"             => $entityManager->getRepository(\App\Entity\Principal\Conta::class),
                "condicaoPagamentoRepository" => $entityManager->getRepository(\App\Entity\Principal\CondicaoPagamento::class),
                "tituloPagarRepository"       => $entityManager->getRepository(\App\Entity\Principal\TituloPagar::class),
            ]
        );
    }

    /**
     * Valida os campos passados no array de parcelas
     *
     * @param array $parametros Ponteiro de Array de parametros da requisicao
     * @param string $mensagemErro Mensagem de erro a retornar pro front-end
     *
     * @return boolean $bRetorno
     */
    protected function verificaDadosDasParcelas(&$parametros, &$mensagemErro)
    {
        $bRetorno = true;
        foreach ($parametros[ConstanteParametros::CHAVE_PARCELA] as $key => $parcela) {
            if ((isset($parcela[ConstanteParametros::CHAVE_TIT_DATA_VENCIMENTO]) === false) || (empty($parcela[ConstanteParametros::CHAVE_TIT_DATA_VENCIMENTO]) === true)) {
                $bRetorno     = false;
                $mensagemErro = "O campo data de vencimento não existe no " . $key . "º título";
                break;
            }

            if ((isset($parcela[ConstanteParametros::CHAVE_TIT_VALOR_DOCUMENTO]) === false) || (empty($parcela[ConstanteParametros::CHAVE_TIT_VALOR_DOCUMENTO]) === true)) {
                $bRetorno     = false;
                $mensagemErro = "O campo \"Valor Original\" não existe no " . $key . "º título";
                break;
            }

            if ((isset($parcela[ConstanteParametros::CHAVE_TIT_NUMERO_PARCELA_DOCUMENTO]) === false) || (empty($parcela[ConstanteParametros::CHAVE_TIT_NUMERO_PARCELA_DOCUMENTO]) === true)) {
                $bRetorno     = false;
                $mensagemErro = "O campo número da parcela não existe no " . $key . "º título";
                break;
            }
        }//end foreach

        return $bRetorno;
    }

    /**
     * Verifica se o valor total dos titulos da nota é igual a somatória do valor das parcelas
     *
     * @param array $parametros Ponteiro de Array de parametros da requisicao
     * @param string $mensagemErro Mensagem de erro a retornar pro front-end
     *
     * @return boolean
     */
    protected function verificaValorParcelasSaoIguais(&$parametros, &$mensagemErro)
    {
        $total = 0;
        foreach ($parametros[ConstanteParametros::CHAVE_PARCELA] as $parcela) {
            $total += $parcela[ConstanteParametros::CHAVE_TIT_VALOR_DOCUMENTO];
        }

        $total      = (float) $total;
        $totalParam = (float) $parametros[ConstanteParametros::CHAVE_NF_VALOR_TOTAL];
        $diferenca  = abs(($total - $totalParam) / $totalParam) < 0.001;

        if ($diferenca > 0.001) {
            $mensagemErro  = "A somatória dos valores das parcelas não bate com o valor total dos titulos gerados para a nota.<br>";
            $mensagemErro .= "Valor total dos titulos da nota: " . $parametros[ConstanteParametros::CHAVE_NF_VALOR_TOTAL] . "<br>";
            $mensagemErro .= "Somatória das parcelas: " . $total;
        }

        return empty($mensagemErro);
    }

    /**
     * Converte a data de string JS do campo de Data Vencimento para o formato JS
     *
     * @param array $parametros Ponteiro do array para realizar a formatacao
     * @param string $mensagemErro Mensagem de erro a retornar pro front-end
     *
     * @return boolean
     */
    protected function converteDataString(&$parametros, &$mensagemErro)
    {
        $bRetorno = true;
        foreach ($parametros[ConstanteParametros::CHAVE_PARCELA] as &$parcela) {
            \App\Helper\FunctionHelper::formataCampoDateTimeJS($parcela[ConstanteParametros::CHAVE_TIT_DATA_VENCIMENTO], $parcela[ConstanteParametros::CHAVE_TIT_DATA_VENCIMENTO]);
            if ($parcela[ConstanteParametros::CHAVE_TIT_DATA_VENCIMENTO] === false) {
                $mensagemErro = "Ocorreu um erro na formatação de Data no campo Data de Vencimento. Formato de data não reconhecida.";
                $bRetorno     = false;
                break;
            }
        }

        return $bRetorno;
    }

    /**
     * Verifica se a data inicial e final vieram no formato correto
     *
     * @param array $parametros Parametros a serem inclusos no objeto
     * @param string $mensagemErro Mensagem que ira retornar para front-end
     *
     * @return boolean
     */
    public function podeListar(&$parametros, &$mensagemErro)
    {
        if (self::verificaDataInicialDataFinal($parametros, $mensagemErro) === true) {
            return true;
        }

        return false;
    }

    /**
     * Realiza a verificacao das regras para permitir ou nao a criacao do registro
     *
     * @param array $parametros Ponteiro do array para realizar a formatacao
     * @param string $mensagemErro Mensagem de erro a retornar pro front-end
     * @param array $parcelasCalculadas Array de parcelas calculadas para validação dos parâmetros da franqueadora
     *
     * @return boolean
     */
    public function podeCriar(&$parametros, &$mensagemErro, $parcelasCalculadas)
    {
        if (self::verificaContaExisteBO($parametros, $mensagemErro, $parametros[ConstanteParametros::CHAVE_CONTA]) === true) {
            if ($this->verificaDadosDasParcelas($parametros, $mensagemErro) === true) {
                if ($this->verificaValorParcelasSaoIguais($parametros, $mensagemErro) === true) {
                    if ($this->converteDataString($parametros, $mensagemErro) === true) {
                        return true;
                    }
                }
            }
        }

        return false;
    }

    /**
     * Verifica se pode atualizar o saldo, apos o pagamento
     *
     * @param array $parametros Parametros a serem inclusos no objeto
     * @param string $mensagemErro Mensagem que ira retornar para front-end
     *
     * @return boolean
     */
    public function podeAtualizarSaldo(&$parametros, &$mensagemErro)
    {
        if (self::verificaValoresSaldo($parametros, $mensagemErro) === true) {
            return true;
        }

        return false;
    }

    /**
     * Verifica a existencia do TituloPagar atraves da ID
     *
     * @param \App\Repository\Principal\TituloPagarRepository $tituloPagarRepository TituloPagar repositorio
     * @param int $id Chave Primaria
     * @param string $mensagemErro Mensagem de erro para retornar pro front-end
     * @param null|\App\Entity\Principal\TituloPagar $resultadoORM Resultado da consulta
     *
     * @return boolean
     */
    public static function verificaTituloPagarExisteId(\App\Repository\Principal\TituloPagarRepository $tituloPagarRepository, $id, &$mensagemErro, &$resultadoORM=null)
    {
        $resultadoORM = $tituloPagarRepository->find($id);
        if (is_null($resultadoORM) === true) {
            $mensagemErro .= "O TituloPagar informado, não foi encontrado na base de dados.";
            return false;
        }

        return true;
    }


}
