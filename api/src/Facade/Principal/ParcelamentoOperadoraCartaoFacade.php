<?php

namespace App\Facade\Principal;

use App\Facade\Principal\GenericFacade;
use Doctrine\Common\Persistence\ManagerRegistry;
use App\Helper\ConstanteParametros;
use App\BO\Principal\ParcelamentoOperadoraCartaoBO;

/**
 *
 * @author Luiz A Costa
 */
class ParcelamentoOperadoraCartaoFacade extends GenericFacade
{

    /**
     *
     * @var \App\Repository\Principal\ParcelamentoOperadoraCartaoRepository
     */
    private $parcelamentoOperadoraCartaoRepository;

    /**
     *
     * @var \App\BO\Principal\ParcelamentoOperadoraCartaoBO
     */
    private $parcelamentoOperadoraCartaoBO;

    /**
     * {@inheritDoc}
     *
     * @see \App\Facade\Principal\GenericFacade::__construct()
     */
    function __construct (ManagerRegistry $managerRegistry, $connection="base_principal")
    {
        parent::__construct($managerRegistry);
        $this->parcelamentoOperadoraCartaoRepository = self::getEntityManager()->getRepository(\App\Entity\Principal\ParcelamentoOperadoraCartao::class);
        $this->parcelamentoOperadoraCartaoBO         = new ParcelamentoOperadoraCartaoBO(self::getEntityManager());
    }

    /**
     * Cria um objeto no banco de dados
     *
     * @param string $mensagemErro Mensagem que ira retornar para front-end
     * @param array $parametros Parametros a serem inclusos no objeto
     *
     * @return mixed|null|\App\Entity\Principal\ParcelamentoOperadoraCartao
     */
    public function criar(&$mensagemErro, $parametros=[])
    {
        $objetoORM = null;
        if ($this->parcelamentoOperadoraCartaoBO->podeSalvar($parametros, $mensagemErro) === true) {
            $objetoORM = \App\Factory\GeneralORMFactory::criar(\App\Entity\Principal\ParcelamentoOperadoraCartao::class, true, $parametros);
            self::persistSeguro($objetoORM, $mensagemErro);
        }

        return $objetoORM;
    }

    /**
     * Atualiza um registro no banco de dados
     *
     * @param string $mensagemErro Mensagem que ira retornar para front-end
     * @param int $id Chave primaria do registro
     * @param array $parametros Campos e valores que iram ser atualizados
     * @param \App\Entity\Principal\ParcelamentoOperadoraCartao $parcelamentoOperadoraCartaoORM
     *
     * @return boolean
     */
    public function atualizar(&$mensagemErro, $id, $parametros=[], &$parcelamentoOperadoraCartaoORM=null)
    {
        $objetoORM = $this->parcelamentoOperadoraCartaoRepository->find($id);
        if (is_null($objetoORM) === true) {
            $mensagemErro .= "ParcelamentoOperadoraCartao[" . $id . "] não encontrado na base de dados.";
        } else {
            $parcelamentoOperadoraCartaoORM = $objetoORM;
            if ($this->parcelamentoOperadoraCartaoBO->podeAlterar($parametros, $mensagemErro) === true) {
                self::getFctHelper()->setParams($parametros, $objetoORM);
            }
        }

        return empty($mensagemErro);
    }

    /**
     * Remove um registro do banco de dados
     *
     * @param string $mensagemErro Mensagem que ira retornar para o front-end
     * @param int $id Chave primaria do registro
     *
     * @return boolean
     */
    public function remover(&$mensagemErro, $id)
    {
        $objetoORM = $this->parcelamentoOperadoraCartaoRepository->find($id);
        if (is_null($objetoORM) === true) {
            $mensagemErro .= "ParcelamentoOperadoraCartao[" . $id . "] não encontrado na base de dados.";
        } else {
            self::removerSeguro($objetoORM, $mensagemErro);
        }

        return empty($mensagemErro);
    }

    /**
     * Remove um registro do banco de dados
     *
     * @param string $mensagemErro Mensagem que ira retornar para o front-end
     * @param int $id Chave primaria do registro
     *
     * @return int
     */
    public function buscarMinimoDiasRepasseParcelamento(&$mensagemErro, $id)
    {
        if (is_array($id)) {
            $id = $id["id"];
        }
      
        $parcelamentoOperadoraCartaoORM = $this->parcelamentoOperadoraCartaoRepository->find($id);
        if (is_null($parcelamentoOperadoraCartaoORM) === true) {
            $mensagemErro .= "ParcelamentoOperadoraCartao[" . $id . "] não encontrado na base de dados.";
            return false;
        }

        $parcelasParcelamentos = $parcelamentoOperadoraCartaoORM->getParcelaParcelamentos();
        if (count($parcelasParcelamentos) === 0) {
            $mensagemErro .= "Parcelas do parcelamentoOperadoraCartao[" . $id . "] não encontrado na base de dados.";
            return false;
        }

        $diasMinimo = $parcelasParcelamentos[0]->getDiasRepasse();

        foreach ($parcelasParcelamentos as $parcela) {
            $diasParcela = $parcela->getDiasRepasse();
            if ($diasMinimo > $diasParcela) {
                $diasMinimo = $diasParcela;
            }
        }

        return $diasMinimo;
    }

    /**
     * Aplica a função de atualizar/criar/remover baseada na regras aplicadas
     *
     * @param array $parametrosParcelamentoOperadoraCartaoMetaData
     * @param null|\App\Entity\Principal\ParcelamentoOperadoraCartao $parcelamentoOperadoraCartaoORM
     * @param string $mensagemErro
     *
     * @return boolean
     */
    public function criarOuAtualizarOuRemoverViaOperadoraCartao($parametrosParcelamentoOperadoraCartaoMetaData, &$parcelamentoOperadoraCartaoORM, &$mensagemErro)
    {
        $bRetorno = true;
        if ((isset($parametrosParcelamentoOperadoraCartaoMetaData[ConstanteParametros::CHAVE_ID]) === true)&&(empty($parametrosParcelamentoOperadoraCartaoMetaData[ConstanteParametros::CHAVE_ID]) === false)) {
            if ((isset($parametrosParcelamentoOperadoraCartaoMetaData[ConstanteParametros::CHAVE_DELETADO]) === true)&&(((bool) $parametrosParcelamentoOperadoraCartaoMetaData[ConstanteParametros::CHAVE_DELETADO]) === true)) {
                if ($this->remover($mensagemErro, $parametrosParcelamentoOperadoraCartaoMetaData[ConstanteParametros::CHAVE_ID]) === false) {
                    unset($parametrosParcelamentoOperadoraCartaoMetaData[ConstanteParametros::CHAVE_OPERADORA_CARTAO]);
                    $mensagemParametros = "parametros_parcelamento_operadora_cartao_" . $parametrosParcelamentoOperadoraCartaoMetaData[ConstanteParametros::CHAVE_ID] . ": " . print_r($parametrosParcelamentoOperadoraCartaoMetaData, true);
                    $mensagemErro       = $mensagemParametros . "\nOcorreu um erro para deletar o registro: " . $parametrosParcelamentoOperadoraCartaoMetaData[ConstanteParametros::CHAVE_ID] . "\nMensagem de Erro PHP:" . $mensagemErro;
                    $bRetorno           = false;
                }
            } else {
                $idParcelamento = $parametrosParcelamentoOperadoraCartaoMetaData[ConstanteParametros::CHAVE_ID];
                unset($parametrosParcelamentoOperadoraCartaoMetaData[ConstanteParametros::CHAVE_ID]);
                $retorno = $this->atualizar($mensagemErro, $idParcelamento, $parametrosParcelamentoOperadoraCartaoMetaData, $parcelamentoOperadoraCartaoORM);
                if ($retorno === false) {
                    unset($parametrosParcelamentoOperadoraCartaoMetaData[ConstanteParametros::CHAVE_OPERADORA_CARTAO]);
                    $mensagemParametros = "parametros_parcelamento_operadora_cartao_" . $idParcelamento . ": " . print_r($parametrosParcelamentoOperadoraCartaoMetaData, true);
                    $mensagemErro       = $mensagemParametros . "\nOcorreu um erro para atualizar o registro: " . $idParcelamento . "\nMensagem de Erro PHP:" . $mensagemErro;
                    $bRetorno           = false;
                }
            }
        } else {
            $parcelamentoOperadoraCartaoORM = $this->criar($mensagemErro, $parametrosParcelamentoOperadoraCartaoMetaData);
            if ((is_null($parcelamentoOperadoraCartaoORM) === true) || (empty($parcelamentoOperadoraCartaoORM) === true)) {
                unset($parametrosParcelamentoOperadoraCartaoMetaData[ConstanteParametros::CHAVE_OPERADORA_CARTAO]);
                $mensagemParametros = "parametros_parcelamento_operadora_cartao: " . print_r($parametrosParcelamentoOperadoraCartaoMetaData, true);
                $mensagemErro       = $mensagemParametros . "\nOcorreu um erro para criar o registro\nMensagem de Erro PHP:" . $mensagemErro;
                $bRetorno           = false;
            }
        }//end if

        return $bRetorno;
    }


}
