<?php

namespace App\Facade\Principal;


use App\Helper\ConstanteParametros;
use Doctrine\Common\Persistence\ManagerRegistry;

/**
 *
 * @author Luiz A Costa
 */
class TipoMovimentoEstoqueFacade extends GenericFacade
{

    /**
     *
     * @var \App\Repository\Principal\TipoMovimentoEstoqueRepository
     */
    private $tipoMovimentoEstoqueRepository;

    /**
     * {@inheritDoc}
     *
     * @see \App\Facade\Principal\GenericFacade::__construct()
     */
    function __construct (ManagerRegistry $managerRegistry, $connection="base_principal")
    {
        parent::__construct($managerRegistry);
        $this->tipoMovimentoEstoqueRepository = self::getEntityManager()->getRepository(\App\Entity\Principal\TipoMovimentoEstoque::class);
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
        $retornoRepositorio = $this->tipoMovimentoEstoqueRepository->filtrarTpMovimentoEstoquePorPagina($parametros, $parametros[ConstanteParametros::CHAVE_PAGINA]);
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
     * @return array|\App\Entity\Principal\TipoMovimentoEstoque
     */
    public function buscarPorId(&$mensagemErro, $id)
    {
        $tpMovimentoEstoqueORM = null;
        \App\BO\Principal\TipoMovimentoEstoqueBO::verificaTpMovimentoEstoqueExiste($this->tipoMovimentoEstoqueRepository, $id, $mensagemErro, $tpMovimentoEstoqueORM);
        return $tpMovimentoEstoqueORM;
    }

    /**
     * Cria um objeto no banco de dados
     *
     * @param string $mensagemErro Mensagem que ira retornar para front-end
     * @param array $parametros Parametros a serem inclusos no objeto
     *
     * @return null|\App\Entity\Principal\TipoMovimentoEstoque
     */
    public function criar(&$mensagemErro, $parametros=[])
    {
        $objetoORM = null;
        if (\App\BO\Principal\TipoMovimentoEstoqueBO::verificaDescricaoExiste($this->tipoMovimentoEstoqueRepository, $parametros[ConstanteParametros::CHAVE_DESCRICAO], $mensagemErro, $objetoORM) === false) {
            $objetoORM = \App\Factory\GeneralORMFactory::criar(\App\Entity\Principal\TipoMovimentoEstoque::class, true, $parametros);
            self::criarRegistro($objetoORM, $mensagemErro);
        }

        return $objetoORM;
    }

    /**
     * Atualiza um registro no banco de dados
     *
     * @param string $mensagemErro Mensagem que ira retornar para front-end
     * @param int $id Chave primaria do registro
     * @param array $parametros Campos e valores que iram ser atualizados
     *
     * @return boolean
     */
    public function atualizar(&$mensagemErro, $id, $parametros=[])
    {
        $tpMovimentoEstoqueORM = null;
        if (\App\BO\Principal\TipoMovimentoEstoqueBO::verificaTpMovimentoEstoqueExiste($this->tipoMovimentoEstoqueRepository, $id, $mensagemErro, $tpMovimentoEstoqueORM) === true) {
            $validaORM = null;
            if (\App\BO\Principal\TipoMovimentoEstoqueBO::verificaDescricaoExiste($this->tipoMovimentoEstoqueRepository, $id, $parametros[ConstanteParametros::CHAVE_DESCRICAO], $mensagemErro, $validaORM) === false) {
                self::limparParametrosVazios($parametros);
                self::getFctHelper()->setParams($parametros, $tpMovimentoEstoqueORM);
                self::flushSeguro($mensagemErro);
                return true;
            }
        }

        return false;
    }

    /**
     * Atualiza a situacao de um registro do banco de dados
     *
     * @param string $mensagemErro Mensagem que ira retornar para o front-end
     * @param int $id Chave primaria do registro
     * @param string $situacao Situação a ser gravada no banco
     *
     * @return boolean
     */
    public function atualizarSituacao(&$mensagemErro, $id, $situacao)
    {
        $tpMovimentoEstoqueORM = null;
        if (\App\BO\Principal\TipoMovimentoEstoqueBO::verificaTpMovimentoEstoqueExiste($this->tipoMovimentoEstoqueRepository, $id, $mensagemErro, $tpMovimentoEstoqueORM) === true) {
            $tpMovimentoEstoqueORM->setSituacao($situacao);
            self::flushSeguro($mensagemErro);
            return true;
        }

        return false;
    }


}
