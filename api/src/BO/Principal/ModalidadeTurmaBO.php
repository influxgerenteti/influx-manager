<?php
namespace App\BO\Principal;

/**
 *
 * @author Marcelo André Naegeler
 */
class ModalidadeTurmaBO extends GenericBO
{


    /**
     * Buscao objeto através da id informada
     *
     * @param \App\Repository\Principal\ModalidadeTurmaRepository $modalidadeTurmaRepository
     * @param int $id
     * @param string $mensagemErro
     * @param \App\Entity\Principal\ModalidadeTurma $modalidadeTurmaORM
     *
     * @return boolean
     */
    public static function modalidadeTurmaExiste (\App\Repository\Principal\ModalidadeTurmaRepository $modalidadeTurmaRepository, $id, &$mensagemErro, &$modalidadeTurmaORM)
    {
        $modalidadeTurmaORM = $modalidadeTurmaRepository->find($id);

        if (is_null($modalidadeTurmaORM) === true) {
            $mensagemErro = "Modalidade de turma não encontrada.";
            return false;
        }

        return true;
    }


}
