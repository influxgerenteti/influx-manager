<?php
namespace App\BO\Principal;

/**
 *
 * @author Luiz Antonio Costa
 */
class NivelInstrutorBO
{


    /**
     * Verifica se o NivelInstrutor ja existe no banco
     *
     * @param \App\Repository\Principal\NivelInstrutorRepository $nivelInstrutorRepository Repositorio da Nivel Instrutor
     * @param array $params Parametros que possuam os campos das entidades e valores
     * @param \App\Entity\Principal\NivelInstrutor $nivelInstrutorORM retorno objeto
     *
     * @return boolean
     */
    public static function nivelInstrutorExisteBanco($nivelInstrutorRepository, $params, &$nivelInstrutorORM=null)
    {
        $nivelInstrutorORM = $nivelInstrutorRepository->findOneBy($params);
        if (is_null($nivelInstrutorORM) === true)
            return false;
        return true;
    }


}
