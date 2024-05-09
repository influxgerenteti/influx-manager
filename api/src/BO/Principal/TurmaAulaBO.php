<?php
namespace App\BO\Principal;

/**
 *
 * @author Luiz Antonio Costa
 */
class TurmaAulaBO
{


    /**
     * Verifica se existe a TurmaAula no banco de dados, se existir, ele retorna no ultimo parametro na funcao, caso contrario, ele preenchera a msg de erro e retornara falso
     *
     * @param \App\Repository\Principal\TurmaAulaRepository $turmaAulaRepository Repositorio da TurmaAula
     * @param integer $id Chave primaria da TurmaAula
     * @param string $mensagemErro Msg de erro para ser retornada ao front-end
     * @param \App\Entity\Principal\TurmaAula|null $turmaAulaORM Retorno no caso de sucesso
     *
     * @return boolean true|false
     */
    public static function verificaTurmaAulaExiste(\App\Repository\Principal\TurmaAulaRepository $turmaAulaRepository, $id, &$mensagemErro, &$turmaAulaORM)
    {
        $turmaAulaORM = $turmaAulaRepository->find($id);
        if (is_null($turmaAulaORM) === true) {
            $mensagemErro = "Licao não encontrada na base de dados.";
            return false;
        }

        return true;
    }


}
