<?php
namespace App\BO\Principal;

use Doctrine\ORM\EntityManagerInterface;
use App\Helper\ConstanteParametros;

/**
 *
 * @author Luiz Antonio Costa
 */
class TransacaoCartaoBO extends GenericBO
{
    /**
     *
     * @var \App\Repository\Principal\TransacaoCartaoRepository
     */
    private $transacaoCartaoRepository;

    /**
     *
     * @var \App\Repository\Principal\ParcelaParcelamentoRepository
     */
    private $parcelaParcelamentoRepository;

    function __construct(EntityManagerInterface $entityManager)
    {
        $this->transacaoCartaoRepository     = $entityManager->getRepository(\App\Entity\Principal\TransacaoCartao::class);
        $this->parcelaParcelamentoRepository = $entityManager->getRepository(\App\Entity\Principal\ParcelaParcelamento::class);
        parent::configuraGenericBO(
            [
                "tituloReceberRepository"               => $entityManager->getRepository(\App\Entity\Principal\TituloReceber::class),
                "franqueadaRepository"                  => $entityManager->getRepository(\App\Entity\Principal\Franqueada::class),
                "operadoraCartaoRepository"             => $entityManager->getRepository(\App\Entity\Principal\OperadoraCartao::class),
                "parcelamentoOperadoraCartaoRepository" => $entityManager->getRepository(\App\Entity\Principal\ParcelamentoOperadoraCartao::class),
            ]
        );
    }

    /**
     * Verifica parametros de relacionamento com a TransacaoCartao
     *
     * @param array $parametros
     * @param string $mensagemErro
     *
     * @return boolean
     */
    protected function verificaParametrosRelacionaisObrigatorio(&$parametros, &$mensagemErro)
    {
        if (self::verificaFranqueadaExisteBO($parametros, $mensagemErro, $parametros[ConstanteParametros::CHAVE_FRANQUEADA]) === true) {
            if (self::verificaOperadoraCartaoExisteBO($parametros, $mensagemErro, $parametros[ConstanteParametros::CHAVE_OPERADORA_CARTAO]) === true) {
                if (self::verificaParcelamentoOperadoraCartaoExisteBO($parametros, $mensagemErro, $parametros[ConstanteParametros::CHAVE_PARCELAMENTO_OPERADORA_CARTAO]) === true) {
                    return true;
                }
            }
        }

        return false;
    }

    /**
     * Verifica se as datas enviadas pela requisicao estao validas
     *
     * @param array $parametros
     * @param string $mensagemErro
     *
     * @return boolean
     */
    protected function verificaParametrosDatasExistentes(&$parametros, &$mensagemErro)
    {
        $bPrevisaoRepasseRetorno = true;
        $bDataEstornoRetorno     = true;
        $bDataPagamentoRetorno   = true;

        if (isset($parametros[ConstanteParametros::CHAVE_DATA_ESTORNO]) === true) {
            if (empty($parametros[ConstanteParametros::CHAVE_DATA_ESTORNO]) === true) {
                $dataRequisicao = null;
            } else {
                $dataRequisicao = $parametros[ConstanteParametros::CHAVE_DATA_ESTORNO];
                //\App\Helper\FunctionHelper::formataCampoDateTimeJS($dataRequisicao, $dataRequisicao);
            }

            if ($dataRequisicao === false) {
                $bDataEstornoRetorno = false;
                $mensagemErro       .= "A data de estorno informada é invalida.\n";
            } else {
                $parametros[ConstanteParametros::CHAVE_DATA_ESTORNO] = $dataRequisicao;
            }
        }

        if (isset($parametros[ConstanteParametros::CHAVE_PREVISAO_REPASSE]) === true) {
            if (empty($parametros[ConstanteParametros::CHAVE_PREVISAO_REPASSE]) === true) {
                $dataRequisicao = null;
            } else {
                $dataRequisicao = $parametros[ConstanteParametros::CHAVE_PREVISAO_REPASSE];
                // \App\Helper\FunctionHelper::formataCampoDateTimeJS($dataRequisicao, $dataRequisicao);
            }

            if ($dataRequisicao === false) {
                $bPrevisaoRepasseRetorno = false;
                $mensagemErro           .= "A data de previsão de repasse informada é invalida.\n";
            } else {
                $parametros[ConstanteParametros::CHAVE_PREVISAO_REPASSE] = $dataRequisicao;
            }
        }

        if (isset($parametros[ConstanteParametros::CHAVE_DATA_PAGAMENTO]) === true) {
            if (empty($parametros[ConstanteParametros::CHAVE_DATA_PAGAMENTO]) === true) {
                $dataRequisicao = false;
            } else {
                $dataRequisicao = $parametros[ConstanteParametros::CHAVE_DATA_PAGAMENTO];
                \App\Helper\FunctionHelper::formataCampoDateTimeJS($dataRequisicao, $dataRequisicao);                
                if ($dataRequisicao === false) {
                    $dataRequisicao = $parametros[ConstanteParametros::CHAVE_DATA_PAGAMENTO];
                    \App\Helper\FunctionHelper::formataCampoDateTime($dataRequisicao, $dataRequisicao);                
                }
            }

            if ($dataRequisicao === false) {
                $bDataPagamentoRetorno = false;
                $mensagemErro         .= "A data de pagamento informada é invalida.\n";
            } else {
                $parametros[ConstanteParametros::CHAVE_DATA_PAGAMENTO] = $dataRequisicao;
            }
        }

        return $bPrevisaoRepasseRetorno && $bDataEstornoRetorno && $bDataPagamentoRetorno;
    }

    /**
     * Executa a validacao das regras para permitir salvar
     *
     * @param array $parametros
     * @param string $mensagemErro
     *
     * @return boolean
     */
    public function podeSalvar(&$parametros, &$mensagemErro)
    {
        if ($this->verificaParametrosRelacionaisObrigatorio($parametros, $mensagemErro) === true) {
            if ($this->verificaParametrosDatasExistentes($parametros, $mensagemErro) === true) {
                return true;
            }
        }

        return false;
    }

    /**
     * Configura os parametros para serem setados no objeto
     *
     * @param array $parametros
     * @param \App\Entity\Principal\TransacaoCartao $objetoORM
     */
    public function configuraParametros(&$parametros, &$objetoORM)
    {
        if ((isset($parametros[ConstanteParametros::CHAVE_NUMERO_LANCAMENTO]) === true)&&(empty($parametros[ConstanteParametros::CHAVE_NUMERO_LANCAMENTO]) === false)) {
            $objetoORM->setNumeroLancamento($parametros[ConstanteParametros::CHAVE_NUMERO_LANCAMENTO]);
        }

        if ((isset($parametros[ConstanteParametros::CHAVE_PREVISAO_REPASSE]) === true) && (empty($parametros[ConstanteParametros::CHAVE_PREVISAO_REPASSE]) === false)) {
            $dataRequisicao = $parametros[ConstanteParametros::CHAVE_PREVISAO_REPASSE];
            \App\Helper\FunctionHelper::formataCampoDateTimeJS($dataRequisicao, $dataRequisicao);
            $objetoORM->setPrevisaoRepasse($dataRequisicao);
        }

        if ((isset($parametros[ConstanteParametros::CHAVE_DATA_PAGAMENTO]) === true) && (empty($parametros[ConstanteParametros::CHAVE_DATA_PAGAMENTO]) === false)) {
            $dataRequisicao = $parametros[ConstanteParametros::CHAVE_DATA_PAGAMENTO];
            \App\Helper\FunctionHelper::formataCampoDateTimeJS($dataRequisicao, $dataRequisicao);
            $objetoORM->setDataPagamento($dataRequisicao);
        }

        if ((isset($parametros[ConstanteParametros::CHAVE_SITUACAO]) === true)&&(empty($parametros[ConstanteParametros::CHAVE_SITUACAO]) === false)) {
            $objetoORM->setSituacao(trim($parametros[ConstanteParametros::CHAVE_SITUACAO]));
        }
    }

    /**
     * Gera o valor da taxa de acordo com as informações da transação
     *
     * @param array $parametros
     * @param string $mensagemErro
     */
    public function gerarTaxa(&$parametros, &$mensagemErro)
    {
        $parcelamentoOperadoraCartaoId = $parametros[ConstanteParametros::CHAVE_PARCELAMENTO_OPERADORA_CARTAO]->getId();
        $valor = $parametros[ConstanteParametros::CHAVE_VALOR_LIQUIDO];

        $parcelaParcelamento = $this->parcelaParcelamentoRepository->buscarRegistroPorParcelamentoOperadoraCartao($parcelamentoOperadoraCartaoId, false);
        if (is_null($parcelaParcelamento) === true) {
            $mensagemErro .= "ParcelaParcelamento não foi encontrada na base de dados.";
            return false;
        }

        $parametros[ConstanteParametros::CHAVE_TAXA] = $valor * $parcelaParcelamento["taxa"] / 100;
    }

    /**
     * Verifica se a Transacao existe atraves do campo ID
     *
     * @param \App\Repository\Principal\TransacaoCartaoRepository $transacaoCartaoRepository Repositorio da Transacao
     * @param int $id Chave primaria a ser consultada
     * @param string $mensagemErro Mensagem de erro a ser retornado ao front-end
     * @param null|\App\Entity\Principal\TransacaoCartao $resultadoORM Ponteiro para retornar o objeto
     *
     * @return boolean
     */
    public static function verificaTransacaoCartaoExiste(\App\Repository\Principal\TransacaoCartaoRepository $transacaoCartaoRepository, $id, &$mensagemErro, &$resultadoORM=null)
    {
        $resultadoORM = $transacaoCartaoRepository->find($id);
        if (is_null($resultadoORM) === true) {
            $mensagemErro .= "TransacaoCartao não foi encontrada na base de dados.";
            return false;
        }

        return true;
    }


}
