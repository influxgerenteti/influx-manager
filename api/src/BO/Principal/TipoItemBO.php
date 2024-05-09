<?php
namespace App\BO\Principal;

/**
 *
 * @author Luiz Antonio Costa
 */
class TipoItemBO
{


    /**
     * Verifica se existe a TipoItem no banco de dados, se existir, ele retorna no ultimo parametro na funcao, caso contrario, ele preenchera a msg de erro e retornara falso
     *
     * @param \App\Repository\Principal\TipoItemRepository $tipoItemRepository Repositorio da TipoItem
     * @param integer $id Chave primaria da TipoItem
     * @param string $mensagemErro Msg de erro para ser retornada ao front-end
     * @param \App\Entity\Principal\TipoItem|null $tipoItemORM Retorno no caso de sucesso
     *
     * @return boolean true|false
     */
    public static function verificaTipoItemExiste(\App\Repository\Principal\TipoItemRepository $tipoItemRepository, $id, &$mensagemErro, &$tipoItemORM)
    {
        $tipoItemORM = $tipoItemRepository->find($id);

        if (is_null($tipoItemORM) === true) {
            $mensagemErro = "TipoItem não encontrada na base de dados.";
            return false;
        }

        return true;
    }


}
