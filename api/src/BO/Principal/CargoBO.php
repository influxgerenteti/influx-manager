<?php
namespace App\BO\Principal;

/**
 *
 * @author Luiz Antonio Costa
 */
class CargoBO
{


    /**
     * Verifica se a sala ja existe no banco
     *
     * @param \App\Repository\Principal\CargoRepository $cargoRepository Repositorio do Cargo
     * @param array $params Parametros que possuam os campos das entidades e valores
     * @param \App\Entity\Principal\Cargo $cargoORM retorno objeto
     *
     * @return boolean
     */
    public static function cargoExisteBanco($cargoRepository, $params, &$cargoORM=null)
    {
        $cargoORM = $cargoRepository->findOneBy($params);
        if (is_null($cargoORM) === true)
            return false;
        return true;
    }


}
