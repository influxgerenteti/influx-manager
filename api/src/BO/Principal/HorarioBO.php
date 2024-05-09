<?php
namespace App\BO\Principal;

use App\Helper\ConstanteParametros;
use Doctrine\ORM\EntityManagerInterface;

/**
 *
 * @author Luiz Antonio Costa
 */
class HorarioBO extends GenericBO
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
     * Verifica se o horario existe
     *
     * @param \App\Repository\Principal\HorarioRepository $horarioRepository
     * @param int $id
     * @param string $mensagemErro
     * @param \App\Entity\Principal\Horario $horarioORM
     * @param false|true $bOrdenaHorarioAulas
     *
     * @return boolean
     */
    public static function verificaHorarioExiste(\App\Repository\Principal\HorarioRepository $horarioRepository, $id, &$mensagemErro, &$horarioORM, $bOrdenaHorarioAulas=false)
    {
        if ($bOrdenaHorarioAulas === true) {
            $horarioORM = $horarioRepository->retoraComHorarioAulaOrdenado($id);
        } else {
            $horarioORM = $horarioRepository->find($id);
        }

        if (is_null($horarioORM) === true) {
            $mensagemErro = "Horário não encontrado.";
            return false;
        }

        return true;
    }

    /**
     * Verifica se os parametros da requisicao sao validos
     *
     * @param array $parametros
     * @param string $mensagemErro
     *
     * @return boolean
     */
    public function parametrosValidosCriacao(&$parametros, &$mensagemErro)
    {
        $bRetorno = true;

        if ((isset($parametros[ConstanteParametros::CHAVE_FRANQUEADA]) === true)&&(empty($parametros[ConstanteParametros::CHAVE_FRANQUEADA]) === false)) {
            if (self::verificaFranqueadaExisteBO($parametros, $mensagemErro, $parametros[ConstanteParametros::CHAVE_FRANQUEADA]) === false) {
                $mensagemErro .= "Franqueada não encontrada.\n";
                $bRetorno      = false;
            }
        }

        if ((isset($parametros[ConstanteParametros::CHAVE_DESCRICAO]) === true)&&(empty($parametros[ConstanteParametros::CHAVE_DESCRICAO]) === true)) {
            $mensagemErro .= "Descrição não veio preenchido. E ele é obrigatório.\n";
            $bRetorno      = false;
        }

        return $bRetorno;
    }

    /**
     * Verifica se os parametros da requisicao sao validos
     *
     * @param array $parametros
     * @param string $mensagemErro
     * @param \App\Entity\Principal\Horario $objetoORM
     */
    public function configuraParametrosAlteracao($parametros, &$mensagemErro, &$objetoORM)
    {

        if ((isset($parametros[ConstanteParametros::CHAVE_FRANQUEADA]) === true)&&(empty($parametros[ConstanteParametros::CHAVE_FRANQUEADA]) === false)) {
            if (self::verificaFranqueadaExisteBO($parametros, $mensagemErro, $parametros[ConstanteParametros::CHAVE_FRANQUEADA]) === true) {
                $objetoORM->setFranqueada($parametros[ConstanteParametros::CHAVE_FRANQUEADA]);
            } else {
                $mensagemErro .= "Franqueada não encontrada.\n";
            }
        }

        if ((isset($parametros[ConstanteParametros::CHAVE_DESCRICAO]) === true)&&(empty($parametros[ConstanteParametros::CHAVE_DESCRICAO]) === false)) {
            $objetoORM->setDescricao($parametros[ConstanteParametros::CHAVE_DESCRICAO]);
        }

        if ((isset($parametros[ConstanteParametros::CHAVE_SITUACAO]) === true)&&(empty($parametros[ConstanteParametros::CHAVE_SITUACAO]) === false)) {
            $objetoORM->setSituacao($parametros[ConstanteParametros::CHAVE_SITUACAO]);
        }

    }

    /**
     * Verifica se o horário existe e está ativo
     *
     * @param \App\Repository\Principal\HorarioRepository $repository
     * @param int $id
     * @param string $mensagemErro
     * @param \App\Entity\Principal\Horario $horario
     *
     * @return boolean
     */
    public static function horarioExisteEAtivo(\App\Repository\Principal\HorarioRepository $repository, $id, &$mensagemErro, &$horario)
    {
        if (self::verificaHorarioExiste($repository, $id, $mensagemErro, $horario) === false) {
            return false;
        }

        if ($horario->getSituacao() !== 'A') {
            $mensagemErro = 'O horário selecionado está inativo.';
            return false;
        }

        return true;
    }


}
