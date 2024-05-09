<?php
namespace App\BO\Principal;

/**
 *
 * @author Luiz Antonio Costa
 */
class IdiomaBO
{


    /**
     * Verifica se existe o idioma no banco de dados, se existir, ele retorna no ultimo parametro na funcao, caso contrario, ele preenchera a msg de erro e retornara falso
     *
     * @param \App\Repository\Principal\IdiomaRepository $idiomaRepository Repositorio do Idioma
     * @param integer $id Chave primaria da categoria
     * @param string$mensagemErro Msg de erro para ser retornada ao front-end
     * @param \App\Entity\Principal\Idioma|null $idiomaORM Retorno no caso de sucesso
     *
     * @return boolean true|false
     */
    public static function verificaIdiomaExiste(\App\Repository\Principal\IdiomaRepository $idiomaRepository, $id, &$mensagemErro, &$idiomaORM)
    {
        $idiomaORM = $idiomaRepository->find($id);

        if ($idiomaORM === null) {
            $mensagemErro = "Idioma não encontrado na base de dados.";
            return false;
        }

        return true;
    }


}
