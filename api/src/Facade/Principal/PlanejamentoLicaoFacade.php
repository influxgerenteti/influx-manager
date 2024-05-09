<?php

namespace App\Facade\Principal;

use App\Facade\Principal\GenericFacade;
use Doctrine\Common\Persistence\ManagerRegistry;
use App\Helper\ConstanteParametros;
use App\Helper\SituacoesSistema;

/**
 *
 * @author Luiz A Costa
 */
class PlanejamentoLicaoFacade extends GenericFacade
{

    /**
     *
     * @var \App\Repository\Principal\PlanejamentoLicaoRepository
     */
    private $planejamentoLicaoRepository;

    /**
     * {@inheritDoc}
     *
     * @see \App\Facade\Principal\GenericFacade::__construct()
     */
    function __construct (ManagerRegistry $managerRegistry, $connection="base_principal")
    {
        parent::__construct($managerRegistry);
        $this->planejamentoLicaoRepository = self::getEntityManager()->getRepository(\App\Entity\Principal\PlanejamentoLicao::class);
    }


    /**
     * Lista todos os registros do banco de dados
     *
     * @param array $parametros Parametros da requisicao
     *
     * @return array
     */
    public function listar($parametros)
    {
        $retornoRepositorio = $this->planejamentoLicaoRepository->filtrarPlanejamentoPorPagina($parametros, $parametros[ConstanteParametros::CHAVE_PAGINA]);
        $retorno            = [
            ConstanteParametros::CHAVE_TOTAL => $retornoRepositorio->getTotalItemCount(),
            ConstanteParametros::CHAVE_ITENS => $retornoRepositorio->getItems(),
        ];
        return $retorno;
    }

    /**
     * Busca o registro pela chave primaria
     *
     * @param string $mensagemErro Mensagem que ira retornar para front-end
     * @param int $id Chave primaria do registro
     *
     * @return array|\App\Entity\Principal\PlanejamentoLicao
     */
    public function buscarPorId(&$mensagemErro, $id)
    {
        $objetoORM = $this->planejamentoLicaoRepository->buscaPorId($id);
        if (is_null($objetoORM) === true) {
            $mensagemErro = "Planejamento não encontrado na base de dados.";
        }

        return $objetoORM;
    }

    /**
     * Cria um objeto no banco de dados
     *
     * @param string $mensagemErro Mensagem que ira retornar para front-end
     * @param array $parametros Parametros a serem inclusos no objeto
     *
     * @return mixed|null|\App\Entity\Principal\PlanejamentoLicao
     */
    public function criar(&$mensagemErro, $parametros=[])
    {
        $objetoORM = null;
        if (count($this->planejamentoLicaoRepository->buscarPlanejamentoPorDescricao($parametros[ConstanteParametros::CHAVE_DESCRICAO])) < 1) {
            $objetoORM = \App\Factory\GeneralORMFactory::criar(\App\Entity\Principal\PlanejamentoLicao::class, true, $parametros);
            self::persistSeguro($objetoORM, $mensagemErro);
        } else {
            $mensagemErro = "Já existe um registro com esta descrição.";
        }

        return $objetoORM;
    }

    /**
     * Atualiza um registro no banco de dados
     *
     * @param string $mensagemErro Mensagem que ira retornar para front-end
     * @param int $id Chave primaria do registro
     * @param array $parametros Campos e valores que iram ser atualizados
     * @param \App\Entity\Principal\PlanejamentoLicao $objetoPlanejamentoLicaoORM Retorno para ser realizado comparacao
     *
     * @return boolean
     */
    public function atualizar(&$mensagemErro, $id, $parametros=[], &$objetoPlanejamentoLicaoORM=null)
    {
        $objetoORM = $this->planejamentoLicaoRepository->find($id);
        if (is_null($objetoORM) === true) {
            $mensagemErro = "Não foi possivel remover o planejamento informado.";
        } else if (count($this->planejamentoLicaoRepository->buscarPlanejamentoPorDescricao($parametros[ConstanteParametros::CHAVE_DESCRICAO])) > 0) {
            $mensagemErro = "Já existe um registro com esta descrição.";
        } else {
            if ((isset($parametros[ConstanteParametros::CHAVE_DESCRICAO]) === true) && (empty($parametros[ConstanteParametros::CHAVE_DESCRICAO]) === false)) {
                $objetoORM->setDescricao($parametros[ConstanteParametros::CHAVE_DESCRICAO]);
            }

            if ((isset($parametros[ConstanteParametros::CHAVE_SITUACAO]) === true) && (empty($parametros[ConstanteParametros::CHAVE_SITUACAO]) === false)) {
                $objetoORM->setSituacao($parametros[ConstanteParametros::CHAVE_SITUACAO]);
            }

            $objetoPlanejamentoLicaoORM = $objetoORM;
        }//end if

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
        $objetoORM = $this->planejamentoLicaoRepository->find($id);
        if (is_null($objetoORM) === true) {
            $mensagemErro = "Não foi possivel remover o planejamento informado.";
        }

        $objetoORM->setSituacao(SituacoesSistema::SITUACAO_INATIVO);
        self::flushSeguro($mensagemErro);
        return empty($mensagemErro);
    }


}
