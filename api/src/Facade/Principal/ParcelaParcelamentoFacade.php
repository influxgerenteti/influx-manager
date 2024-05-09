<?php

namespace App\Facade\Principal;

use App\Facade\Principal\GenericFacade;
use Doctrine\Common\Persistence\ManagerRegistry;
use App\Helper\ConstanteParametros;
use App\BO\Principal\ParcelaParcelamentoBO;

/**
 *
 * @author Luiz A Costa
 */
class ParcelaParcelamentoFacade extends GenericFacade
{

    /**
     *
     * @var \App\Repository\Principal\ParcelaParcelamentoRepository
     */
    private $parcelaParcelamentoRepository;

    /**
     *
     * @var \App\BO\Principal\ParcelaParcelamentoBO
     */
    private $parcelaParcelamentoBO;

    /**
     * {@inheritDoc}
     *
     * @see \App\Facade\Principal\GenericFacade::__construct()
     */
    function __construct (ManagerRegistry $managerRegistry, $connection="base_principal")
    {
        parent::__construct($managerRegistry);
        $this->parcelaParcelamentoRepository = self::getEntityManager()->getRepository(\App\Entity\Principal\ParcelaParcelamento::class);
        $this->parcelaParcelamentoBO         = new ParcelaParcelamentoBO(self::getEntityManager());
    }

    public function criar($parametros, &$mensagemErro)
    {
        $objetoORM = null;
        if ($this->parcelaParcelamentoBO->podeSalvar($parametros, $mensagemErro) === true) {
            $objetoORM = \App\Factory\GeneralORMFactory::criar(\App\Entity\Principal\ParcelaParcelamento::class, true, $parametros);
            self::persistSeguro($objetoORM, $mensagemErro);
        }

        return $objetoORM;
    }

    public function atualizar($parametros, $id, &$mensagemErro)
    {
        $objetoORM = $this->parcelaParcelamentoRepository->find($id);
        if (is_null($objetoORM) === true) {
            $mensagemErro .= "ParcelaParcelamento[" . $id . "] não encontrado na base de dados.";
        } else {
            if ($this->parcelaParcelamentoBO->podeAlterar($parametros, $mensagemErro) === true) {
                self::getFctHelper()->setParams($parametros, $objetoORM);
            }
        }

        return empty($mensagemErro);
    }

    public function remover($id, &$mensagemErro)
    {
        $objetoORM = $this->parcelaParcelamentoRepository->find($id);
        if (is_null($objetoORM) === true) {
            $mensagemErro .= "ParcelaParcelamento[" . $id . "] não encontrado na base de dados.";
        } else {
            self::removerSeguro($objetoORM, $mensagemErro);
        }

        return empty($mensagemErro);
    }

    public function criarOuAtualizarOuRemoverViaOperadoraCartao($parametrosParcelamentoOperadoraCartaoMetaData, &$parcelamentoOperadoraCartaoORM, &$mensagemErro)
    {
        $bRetorno = true;
        foreach ($parametrosParcelamentoOperadoraCartaoMetaData[ConstanteParametros::CHAVE_PARCELA_PARCELAMENTOS] as $parametrosParcelaParcelamentoMetaData) {
            if ((isset($parametrosParcelaParcelamentoMetaData[ConstanteParametros::CHAVE_ID]) === true)&&(empty($parametrosParcelaParcelamentoMetaData[ConstanteParametros::CHAVE_ID]) === false)) {
                if ((isset($parametrosParcelaParcelamentoMetaData[ConstanteParametros::CHAVE_DELETADO]) === true)&&(((bool) $parametrosParcelaParcelamentoMetaData[ConstanteParametros::CHAVE_DELETADO]) === true)) {
                    if ($this->remover($parametrosParcelaParcelamentoMetaData[ConstanteParametros::CHAVE_ID], $mensagemErro) === false) {
                        $mensagemParametros = "parametros_parcela_parcelamento_" . $parametrosParcelaParcelamentoMetaData[ConstanteParametros::CHAVE_ID] . ": " . print_r($parametrosParcelaParcelamentoMetaData, true);
                        $mensagemErro       = $mensagemParametros . "\nOcorreu um erro para deletar o registro: " . $parametrosParcelaParcelamentoMetaData[ConstanteParametros::CHAVE_ID] . "\nMensagem de Erro PHP:" . $mensagemErro;
                        $bRetorno           = false;
                        break;
                    }
                } else {
                    $parcelaParcelamentoID = $parametrosParcelaParcelamentoMetaData[ConstanteParametros::CHAVE_ID];
                    unset($parametrosParcelaParcelamentoMetaData[ConstanteParametros::CHAVE_ID]);
                    $parametrosParcelaParcelamentoMetaData[ConstanteParametros::CHAVE_PARCELAMENTO_OPERADORA_CARTAO] = $parcelamentoOperadoraCartaoORM->getId();
                    $retorno = $this->atualizar($parametrosParcelaParcelamentoMetaData, $parcelaParcelamentoID, $mensagemErro);
                    if ($retorno === false) {
                        $mensagemParametros = "parametros_parcela_parcelamento_" . $parametrosParcelaParcelamentoMetaData[ConstanteParametros::CHAVE_ID] . ": " . print_r($parametrosParcelaParcelamentoMetaData, true);
                        $mensagemErro       = $mensagemParametros . "\nOcorreu um erro para deletar o registro: " . $parametrosParcelaParcelamentoMetaData[ConstanteParametros::CHAVE_ID] . "\nMensagem de Erro PHP:" . $mensagemErro;
                        $bRetorno           = false;
                        break;
                    }
                }
            } else {
                $parametrosParcelaParcelamentoMetaData[ConstanteParametros::CHAVE_PARCELAMENTO_OPERADORA_CARTAO] = $parcelamentoOperadoraCartaoORM;
                $retornoParcelamentoORM = $this->criar($parametrosParcelaParcelamentoMetaData, $mensagemErro);
                if ((is_null($retornoParcelamentoORM) === true) || (empty($retornoParcelamentoORM) === true)) {
                    $mensagemParametros = "parametros_parcela_parcelamento_: " . print_r($parametrosParcelaParcelamentoMetaData, true);
                    $mensagemErro       = $mensagemParametros . "\nOcorreu um erro para criar o registro. \nMensagem de Erro PHP:" . $mensagemErro;
                    $bRetorno           = false;
                    break;
                }
            }//end if
        }//end foreach

        return $bRetorno;
    }


}
