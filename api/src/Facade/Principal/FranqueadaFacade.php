<?php

namespace App\Facade\Principal;

use App\Entity\Principal\Franqueada;
use App\BO\Principal\FranqueadaBO;
use App\Factory\GeneralORMFactory;
use App\Helper\SituacoesSistema;
use App\Helper\ConstanteParametros;
use Doctrine\Common\Persistence\ManagerRegistry;

/**
 *
 * @author Luiz A Costa
 */
class FranqueadaFacade extends GenericFacade
{

    /**
     *
     * @var \App\Repository\Principal\FranqueadaRepository
     */
    private $franqueadaRepository;

    /**
     *
     * @var \App\Repository\Principal\DiasSubsequentesRepository
     */
    private $diasSubsequentesRepository;

    /**
     * {@inheritDoc}
     *
     * @see \App\Facade\Principal\GenericFacade::__construct()
     */
    public function __construct(ManagerRegistry $managerRegistry, $connection="base_principal")
    {
        parent::__construct($managerRegistry);
        $this->franqueadaRepository       = self::getEntityManager()->getRepository(Franqueada::class);
        $this->diasSubsequentesRepository = self::getEntityManager()->getRepository(\App\Entity\Principal\DiasSubsequentes::class);
    }

    /**
     * Atualiza a franqueada, com os dados informados nos parametros
     *
     * @param string $msg Mensagem de retorno ao frontend
     * @param integer $id Chave primaria da franqueada
     * @param array $params
     *
     * @return boolean TRUE | FALSE
     */
    public function atualizarFranqueada(&$msg, $id, $params=[])
    {
        $franqueadaORM = $this->franqueadaRepository->find($id);
        $params[ConstanteParametros::CHAVE_TIPO_MOVIMENTO_CONTA_RECEBER] = self::getEntityManager()->getRepository(\App\Entity\Principal\TipoMovimentoConta::class)->find($params[ConstanteParametros::CHAVE_TIPO_MOVIMENTO_CONTA_RECEBER]);
        $params[ConstanteParametros::CHAVE_TIPO_MOVIMENTO_CONTA_PAGAR]   = self::getEntityManager()->getRepository(\App\Entity\Principal\TipoMovimentoConta::class)->find($params[ConstanteParametros::CHAVE_TIPO_MOVIMENTO_CONTA_PAGAR]);
        if ((isset($params[ConstanteParametros::CHAVE_ESTADO]) === true) && (is_null($params[ConstanteParametros::CHAVE_ESTADO]) === false)) {
            $params[ConstanteParametros::CHAVE_ESTADO] = self::getEntityManager()->getRepository(\App\Entity\Principal\Estado::class)->find($params[ConstanteParametros::CHAVE_ESTADO]);
        }

        if ((isset($params[ConstanteParametros::CHAVE_CIDADE]) === true) && (is_null($params[ConstanteParametros::CHAVE_CIDADE]) === false)) {
            $params[ConstanteParametros::CHAVE_CIDADE] = self::getEntityManager()->getRepository(\App\Entity\Principal\Cidade::class)->find($params[ConstanteParametros::CHAVE_CIDADE]);
        }

        $params[ConstanteParametros::CHAVE_CNPJ] = preg_replace("/\D/", "", $params[ConstanteParametros::CHAVE_CNPJ]);
        if (($params[ConstanteParametros::CHAVE_SITUACAO] === null) || (empty($params[ConstanteParametros::CHAVE_SITUACAO]) === true)) {
            unset($params[ConstanteParametros::CHAVE_SITUACAO]);
        }

        self::getFctHelper()->setParams($params, $franqueadaORM);
        self::flushSeguro($msg);
        return empty($msg);
    }

    /**
     * Busca a franqueada atraves da ID
     *
     * @param integer $id ID da franqueada para ser resgatada do banco
     *
     * @return \App\Entity\Principal\Franqueada O Objeto ou NULL
     */
    public function buscarFranqueada($id)
    {
        return $this->franqueadaRepository->buscarFranqueadaEUsuarios($id);
    }

    /**
     * Busca os parametros da franqueada informada
     *
     * @param int $id
     *
     * @return string
     */
    public function buscarParametrosFranqueada($id)
    {
        return $this->franqueadaRepository->buscarParametrosFranqueada($id);
    }

    /**
     * Busca a franqueada com sua conta padrão através da ID
     *
     * @param integer $id ID da franqueada para ser resgatada do banco
     *
     * @return \App\Entity\Principal\Franqueada O Objeto ou NULL
     */
    public function buscarFranqueadaEContaPadrao ($id)
    {
        return $this->franqueadaRepository->buscarFranqueadaEContaPadrao($id);
    }

    /**
     * Buscar Franqueadas
     *
     * @param array $parametros Variavel com os parametros de busca
     *
     * @return array Resultado da consulta com os registros filtrados
     */
    public function buscarFranqueadas($parametros=[])
    {
        $retornoRepositorio = $this->franqueadaRepository->filtraFranqueadasPorPagina($parametros, $parametros[ConstanteParametros::CHAVE_PAGINA], $parametros[ConstanteParametros::CHAVE_USUARIO]);
        $retorno            = [
            ConstanteParametros::CHAVE_TOTAL => $retornoRepositorio->getTotalItemCount(),
            ConstanteParametros::CHAVE_ITENS => $retornoRepositorio->getItems(),
        ];
        return $retorno;
    }

    /**
     * Cria uma nova franqueada no Banco de dados
     *
     * @param string $msg Mensagem de retorno ao frontend
     * @param array $params parametros com os campos para a criacao do registro no banco
     *
     * @return \App\Entity\Principal\Franqueada O objeto criado ou NULL no caso da nao criacao
     */
    public function criarFranqueada(&$msg, $params=[])
    {
        $franqueadaORM = null;
        $params[ConstanteParametros::CHAVE_TIPO_MOVIMENTO_CONTA_RECEBER] = self::getEntityManager()->getRepository(\App\Entity\Principal\TipoMovimentoConta::class)->find($params[ConstanteParametros::CHAVE_TIPO_MOVIMENTO_CONTA_RECEBER]);
        $params[ConstanteParametros::CHAVE_TIPO_MOVIMENTO_CONTA_PAGAR]   = self::getEntityManager()->getRepository(\App\Entity\Principal\TipoMovimentoConta::class)->find($params[ConstanteParametros::CHAVE_TIPO_MOVIMENTO_CONTA_PAGAR]);
        $params[ConstanteParametros::CHAVE_CNPJ] = preg_replace("/\D/", "", $params[ConstanteParametros::CHAVE_CNPJ]);
        if (isset($params[ConstanteParametros::CHAVE_CPF]) === true) {
            unset($params[ConstanteParametros::CHAVE_CPF]);
        }

        if ((isset($params[ConstanteParametros::CHAVE_ESTADO]) === true) && (is_null($params[ConstanteParametros::CHAVE_ESTADO]) === false)) {
            $params[ConstanteParametros::CHAVE_ESTADO] = self::getEntityManager()->getRepository(\App\Entity\Principal\Estado::class)->find($params[ConstanteParametros::CHAVE_ESTADO]);
        }

        if ((isset($params[ConstanteParametros::CHAVE_CIDADE]) === true) && (is_null($params[ConstanteParametros::CHAVE_CIDADE]) === false)) {
            $params[ConstanteParametros::CHAVE_CIDADE] = self::getEntityManager()->getRepository(\App\Entity\Principal\Cidade::class)->find($params[ConstanteParametros::CHAVE_CIDADE]);
        }

        if (FranqueadaBO::franqueadaExisteBanco($this->franqueadaRepository, $params, $franqueadaORM) === true) {
            $msg = "Não é possivel criar um registro de franqueada pois já existe um registro com os valores informados.";
        } else {
            $franqueadaORM    = GeneralORMFactory::criar(Franqueada::class, true, $params);
            $diasSubSequentes = $this->diasSubsequentesRepository->findBy(["numero_dia" => [5, 10, 20]]);
            foreach ($diasSubSequentes as $diaSubsequente) {
                $diaSubsequente->addFranqueada($franqueadaORM);
            }

            self::persistSeguro($franqueadaORM, $msg);
        }

        return $franqueadaORM;
    }

    /**
     * Exclui a franqueada informada
     *
     * @param string $msg Mensagem de retorno ao frontend
     * @param integer $id Chave primaria da franqueada a ser excluida
     *
     * @return boolean TRUE | FALSE
     */
    public function excluirFranqueada(&$msg, $id)
    {
        $retorno    = false;
        $franqueada = $this->franqueadaRepository->find($id);
        if (empty($franqueada) === false) {
            $franqueada->setSituacao(SituacoesSistema::SITUACAO_REMOVIDO);
            self::flushSeguro($msg);
            $retorno = empty($msg);
        } else {
            $msg = "Não foi possivel buscar a franqueada no banco de dados";
        }

        return $retorno;
    }

    /**
     * Retorna se a franqueada informada é franqueadora
     *
     * @param string $msg Mensagem de retorno ao frontend
     * @param integer $id Chave primaria da franqueada a buscar
     *
     * @return boolean
     */
    public function ehFranqueadora(&$msg, $id)
    {
        $retorno    = false;
        $franqueada = $this->franqueadaRepository->find($id);
        if (empty($franqueada) === false) {
            $retorno = $franqueada->getFranqueadora();
        } else {
            $msg = "Não foi possivel buscar a franqueada no banco de dados";
        }

        return $retorno;
    }


}
