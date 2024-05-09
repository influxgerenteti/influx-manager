<?php
namespace App\BO\Principal;

use App\Helper\ConstanteParametros;

/**
 *
 * @author Luiz Antonio Costa
 */
class SalaBO
{


    /**
     * Verifica se a sala ja existe no banco
     *
     * @param \App\Repository\Principal\SalaRepository $salaRepository Repositorio da Franqueada
     * @param array $params Parametros que possuam os campos das entidades e valores
     * @param \App\Entity\Principal\Sala $salaORM retorno objeto
     *
     * @return boolean
     */
    public static function salaExisteBanco($salaRepository, $params, &$salaORM=null)
    {
        $salaORM = $salaRepository->findOneBy($params);
        if (is_null($salaORM) === true)
            return false;
        return true;
    }

    /**
     * Configura os parametros para a parte de atualização
     *
     * @param array $parametros
     * @param \App\Entity\Principal\SalaFranqueada $objetoORM
     */
    public function configuraParametros($parametros, &$objetoORM)
    {
        if ((isset($parametros[ConstanteParametros::CHAVE_DESCRICAO]) === true) && (empty($parametros[ConstanteParametros::CHAVE_DESCRICAO]) === false)) {
            $objetoORM->setDescricao($parametros[ConstanteParametros::CHAVE_DESCRICAO]);
        }
    }


}
