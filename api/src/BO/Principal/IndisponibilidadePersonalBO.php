<?php
namespace App\BO\Principal;

use Doctrine\ORM\EntityManagerInterface;
use App\Helper\ConstanteParametros;
use Carbon\Carbon;

class IndisponibilidadePersonalBO extends GenericBO
{


    function __construct(EntityManagerInterface $entityManager)
    {
        parent::configuraGenericBO(
            [
                "franqueadaRepository" => $entityManager->getRepository(\App\Entity\Principal\Franqueada::class),
            ]
        );
    }

    /**
     * Validação de horários para indisponibilidade
     *
     * @param array $parametros
     *
     * @return bool
     */
    public function validaHorarios(&$parametros, &$mensagemErro)
    {
        if (self::verificaFranqueadaExisteBO($parametros, $mensagemErro, $parametros[ConstanteParametros::CHAVE_FRANQUEADA]) === false) {
            $mensagemErro = 'Franqueada não encontrada';
            return false;
        }

        $parametros[ConstanteParametros::CHAVE_DATA_INICIO] = \App\Helper\FunctionHelper::formataCampoDateTimeJS($parametros[ConstanteParametros::CHAVE_DATA_INICIO]);
        $parametros[ConstanteParametros::CHAVE_DATA_FIM]    = \App\Helper\FunctionHelper::formataCampoDateTimeJS($parametros[ConstanteParametros::CHAVE_DATA_FIM]);

        if ($parametros[ConstanteParametros::CHAVE_DATA_INICIO] > $parametros[ConstanteParametros::CHAVE_DATA_FIM]) {
            $mensagemErro = 'Data de início maior que data de fim';
            return false;
        }

        $parametros[ConstanteParametros::CHAVE_HORA_INICIO] = \App\Helper\FunctionHelper::formataCampoDateTimeJS($parametros[ConstanteParametros::CHAVE_HORA_INICIO]);
        $parametros[ConstanteParametros::CHAVE_HORA_FIM]    = \App\Helper\FunctionHelper::formataCampoDateTimeJS($parametros[ConstanteParametros::CHAVE_HORA_FIM]);

        if ($parametros[ConstanteParametros::CHAVE_HORA_INICIO] > $parametros[ConstanteParametros::CHAVE_HORA_FIM]) {
            $mensagemErro = 'Horário de início maior que horário de fim';
            return false;
        }

        return true;
    }


}
