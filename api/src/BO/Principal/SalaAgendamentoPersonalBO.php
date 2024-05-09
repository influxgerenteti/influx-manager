<?php
namespace App\BO\Principal;

use App\Helper\ConstanteParametros;
use DateTime;

/**
 *
 * @author Gilberto M Martins
 */
class SalaAgendamentoPersonalBO extends GenericBO
{
     /**
     * Configura os parametros para a parte de atualização
     *
     * @param array $parametros
     * @param \App\Entity\Principal\SalaAgendamentoPersonal $objetoORM
     */
    public function configuraParametros($parametros, &$objetoORM, $salaFranqueadaORM=null)
    {
        if ((isset($parametros[ConstanteParametros::CHAVE_DATA_INICIO]) === true) && (empty($parametros[ConstanteParametros::CHAVE_DATA_INICIO]) === false)) {
            $dataInicio = new \DateTime($parametros[ConstanteParametros::CHAVE_DATA_INICIO]); 
            $objetoORM->setDataInicio( $dataInicio);
        }
        if ((isset($parametros[ConstanteParametros::CHAVE_DATA_FIM]) === true) && (empty($parametros[ConstanteParametros::CHAVE_DATA_FIM]) === false)) {
            $dataFim = new \DateTime($parametros[ConstanteParametros::CHAVE_DATA_FIM]); 
            $objetoORM->setDataFim( $dataFim);
        }
        if ((isset($parametros[ConstanteParametros::CHAVE_SALA_FRANQUEADA]) === true) && (empty($parametros[ConstanteParametros::CHAVE_SALA_FRANQUEADA]) === false)) {
            $objetoORM->setSalaFranqueada($salaFranqueadaORM );
        }

        if ((isset($parametros[ConstanteParametros::CHAVE_SITUACAO]) === true) && (empty($parametros[ConstanteParametros::CHAVE_SITUACAO]) === false)) {
            $objetoORM->setSituacao($parametros[ConstanteParametros::CHAVE_SITUACAO]);
        }

    }

       /**
     * Validação dos dados para criação de registro
     *
     * @param array $params Parâmetros que possuam os campos das entidades e valores
     * @param string $mensagemErro
     * @param int $id ID da turma (caso houver)
     *
     * @return boolean
     */
    public function podeCriar (&$params, &$mensagemErro, $id=null)
    {   
        if ((empty($params[ConstanteParametros::CHAVE_SALA_FRANQUEADA]) === false) && (self::verificaSalaFranqueadaExisteBO($params, $mensagemErro, $params[ConstanteParametros::CHAVE_SALA_FRANQUEADA]) === false)) {
            return false;
        }
        
        return true;
    }

}
