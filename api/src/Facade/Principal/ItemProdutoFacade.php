<?php

namespace App\Facade\Principal;

use App\Helper\ConstanteParametros;
use App\Helper\SituacoesSistema;
use Doctrine\Common\Persistence\ManagerRegistry;
use App\BO\Principal\ItemProdutoBO;

/**
 *
 * @author Luiz A Costa
 */
class ItemProdutoFacade extends GenericItemFacade
{


    /**
     * {@inheritDoc}
     *
     * @see \App\Facade\Principal\GenericItemFacade::__construct()
     */
    function __construct (ManagerRegistry $managerRegistry, $connection="base_principal")
    {
        parent::__construct($managerRegistry, $connection);
        $this->itemBO = new ItemProdutoBO(self::getEntityManager());
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
        $parametros[ConstanteParametros::CHAVE_TIPO_ITEM] = SituacoesSistema::TIPO_ITEM_PRODUTO;
        return parent::listar($parametros);
    }

    /**
     * Busca registros de item com determinada descricao
     *
     * @param string $descricao descrição a ser buscada
     * @param integer $franqueada
     *
     * @return \App\Entity\Principal\Item[]
     */
    public function buscarPorDescricaoProduto ($descricao, $franqueada)
    {
        return parent::buscarPorDescricao($descricao, $franqueada, SituacoesSistema::TIPO_ITEM_PRODUTO);
    }

    /**
     * Cria um objeto no banco de dados
     *
     * @param string $mensagemErro Mensagem que ira retornar para front-end
     * @param array $parametros Parametros a serem inclusos no objeto
     *
     * @return null|\App\Entity\Principal\Item
     */
    public function criar(&$mensagemErro, $parametros=[])
    {
        return parent::criar($mensagemErro, $parametros);
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
        // $parametros[ConstanteParametros::CHAVE_TIPO_ITEM] = SituacoesSistema::TIPO_ITEM_PRODUTO;
        return parent::atualizar($mensagemErro, $id, $parametros);
    }

    /**
     * Retorna o primeiro item para o tipoItem informado
     *
     * @return \App\Entity\Principal\Item|NULL
     */
    public function retornaPrimeiroItemProdutoPorTipoItem()
    {
        return parent::retornaPrimeiroItemPorTipoItem(SituacoesSistema::TIPO_ITEM_PRODUTO);
    }


}
