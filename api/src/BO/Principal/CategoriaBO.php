<?php
namespace App\BO\Principal;

/**
 *
 * @author Luiz Antonio Costa
 */
class CategoriaBO
{


    /**
     * Verifica se existe a categoria no banco de dados, se existir, ele retorna no ultimo parametro na funcao, caso contrario, ele preenchera a msg de erro e retornara falso
     *
     * @param \App\Repository\Principal\CategoriaRepository $categoriaRepositoy Repositorio da Categoria
     * @param integer $id Chave primaria da categoria
     * @param string $mensagemErro Msg de erro para ser retornada ao front-end
     * @param \App\Entity\Principal\Categoria|null $categoriaORM Retorno no caso de sucesso
     *
     * @return boolean true|false
     */
    public static function verificaCategoriaExiste(\App\Repository\Principal\CategoriaRepository $categoriaRepositoy, $id, &$mensagemErro, &$categoriaORM)
    {
        $categoriaORM = $categoriaRepositoy->find($id);
        if (is_null($categoriaORM) === true) {
            $mensagemErro = "Categoria não encontrada na base de dados.";
            return false;
        }

        return true;
    }


}
