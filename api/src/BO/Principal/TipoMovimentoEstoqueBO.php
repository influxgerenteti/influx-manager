<?php
namespace App\BO\Principal;

/**
 *
 * @author Luiz Antonio Costa
 */
class TipoMovimentoEstoqueBO
{


    /**
     * Verifica se ja existe um registro no banco de dados com a ID informada
     *
     * @param \App\Repository\Principal\TipoMovimentoEstoqueRepository $tipoMovimentoEstoqueRepository Repositorio do Tipo Movimento Estoque
     * @param int $id Chave primaria a ser pesquisada
     * @param string $mensagemErro Mensagem de erro a ser retornado para o front-end
     * @param null|\App\Entity\Principal\TipoMovimentoEstoque $retornoORM Retorno da pesquisa
     *
     * @return boolean
     */
    public static function verificaTpMovimentoEstoqueExiste(\App\Repository\Principal\TipoMovimentoEstoqueRepository $tipoMovimentoEstoqueRepository, $id, &$mensagemErro, &$retornoORM=null)
    {
        $retornoORM = $tipoMovimentoEstoqueRepository->find($id);
        if (is_null($retornoORM) === false) {
            $mensagemErro .= "Já existe um registro cadastrado com esse ID.";
            return true;
        }

        return false;
    }

    /**
     * Pesquisa no banco de dados se a descricao informada ja existe na base de dados
     *
     * @param \App\Repository\Principal\TipoMovimentoEstoqueRepository $tipoMovimentoEstoqueRepository Repositorio do Tipo Movimento Estoque
     * @param int $id           Id a ser pesquisado no banco de dados
     * @param string $descricao Descricao a ser pesquisada no banco de dados
     * @param string $mensagemErro Mensagem de erro a ser retornado para o front-end
     * @param null|array $retornoORM Retorno da pesquisa
     *
     * @return boolean
     */
    public static function verificaDescricaoExiste(\App\Repository\Principal\TipoMovimentoEstoqueRepository $tipoMovimentoEstoqueRepository, $id, $descricao, &$mensagemErro, &$retornoORM=null)
    {
        $retornoORM = $tipoMovimentoEstoqueRepository->buscaPorDescricao($descricao, $id);
        if (is_null($retornoORM) === false) {
            $mensagemErro .= " Nome para o Tipo de Movimento do Estoque, já se encontra cadastrado.";
            return true;
        }

        return false;
    }


}
