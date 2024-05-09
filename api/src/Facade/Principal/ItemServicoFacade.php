<?php

namespace App\Facade\Principal;

use App\Helper\ConstanteParametros;
use App\Helper\SituacoesSistema;
use Doctrine\Common\Persistence\ManagerRegistry;

/**
 *
 * @author Luiz A Costa
 */
class ItemServicoFacade extends GenericItemFacade
{


    /**
     * {@inheritDoc}
     *
     * @see \App\Facade\Principal\GenericItemFacade::__construct()
     */
    function __construct (ManagerRegistry $managerRegistry, $connection="base_principal")
    {
        parent::__construct($managerRegistry, $connection);
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
    public function buscarPorDescricaoServico ($descricao, $franqueada)
    {
        return parent::buscarPorDescricao($descricao, $franqueada, SituacoesSistema::TIPOS_SERVICO);
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
        $parametros[ConstanteParametros::CHAVE_ITEM_FRANQUEADAS] = [
            [
                ConstanteParametros::CHAVE_SALDO_ESTOQUE      => 0,
                ConstanteParametros::CHAVE_ESTOQUE_MINIMO     => 0,
                ConstanteParametros::CHAVE_VALOR_COMPRA       => 0,
                ConstanteParametros::CHAVE_VALOR_VENDA        => 0,
                ConstanteParametros::CHAVE_VALOR_VENDA . '_2' => 0,
                ConstanteParametros::CHAVE_VALOR_VENDA . '_3' => 0,
                ConstanteParametros::CHAVE_VALOR_VENDA . '_4' => 0,
                ConstanteParametros::CHAVE_VALOR_VENDA . '_5' => 0,
                ConstanteParametros::CHAVE_VALOR_VENDA . '_6' => 0,
            ],
        ];
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
        return parent::atualizar($mensagemErro, $id, $parametros);
    }

    /**
     * Retorna o primeiro item para o tipoItem informado
     *
     * @return \App\Entity\Principal\Item|NULL
     */
    public function retornaPrimeiroItemServicoPorTipoItem()
    {
        return parent::retornaPrimeiroItemPorTipoItem(SituacoesSistema::TIPOS_SERVICO);
    }


}
