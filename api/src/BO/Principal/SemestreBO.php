<?php
namespace App\BO\Principal;

/**
 *
 * @author Luiz Antonio Costa
 */
class SemestreBO
{


    /**
     * Verifica se o semestre ja existe no banco
     *
     * @param \App\Repository\Principal\SemestreRepository $semestreRepository Repositorio do semestre
     * @param int $id
     * @param string $mensagemErro
     * @param \App\Entity\Principal\Semestre $semestreORM retorno objeto
     *
     * @return boolean
     */
    public static function verificaSemestreExiste(\App\Repository\Principal\SemestreRepository $semestreRepository, $id, &$mensagemErro, &$semestreORM=null)
    {
        $semestreORM = $semestreRepository->find($id);
        if (is_null($semestreORM) === true) {
            $mensagemErro = "Semestre não encontrado na base de dados.";
            return false;
        }

        return true;
    }


}
