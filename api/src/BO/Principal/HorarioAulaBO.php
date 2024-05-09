<?php
namespace App\BO\Principal;

use App\Helper\ConstanteParametros;
use Doctrine\ORM\EntityManagerInterface;

/**
 *
 * @author Luiz Antonio Costa
 */
class HorarioAulaBO extends GenericBO
{


    function __construct(EntityManagerInterface $entityManager)
    {
        parent::configuraGenericBO(
            [
                "horarioRepository" => $entityManager->getRepository(\App\Entity\Principal\Horario::class),
            ]
        );
    }

    /**
     * Verifica se nao existe nenhum horario igual para o dia solicitado
     *
     * @param \App\Entity\Principal\HorarioAula $objetoA
     * @param \App\Entity\Principal\HorarioAula[] $arrayObjetos
     * @param int $pularIndice
     *
     * @return boolean
     */
    public static function naoExisteHorarioIgualParaDiaSelecionado($objetoA, $arrayObjetos, $pularIndice, $bAtualizar=false)
    {
        $bRetorno = true;
        for ($i = 0; $i < count($arrayObjetos); $i++) {
            if (($i === $pularIndice) && ($bAtualizar === false)) {
                continue;
            } else if (($bAtualizar === true)&&($objetoA->getId() === $arrayObjetos[$i]->getId())) {
                continue;
            } else {
                if ($objetoA->getDiaSemana() === $arrayObjetos[$i]->getDiaSemana()) {
                    $horaMinuto         = $objetoA->getHorarioInicio()->format("H:i");
                    $horaMinutoLista    = $arrayObjetos[$i]->getHorarioInicio()->format("H:i");
                    $dateTimeA          = \DateTime::createFromFormat("H:i", $horaMinuto);
                    $dateTimeB          = \DateTime::createFromFormat("H:i", $horaMinutoLista);
                    $intervalo          = $dateTimeA->diff($dateTimeB);
                    $intervaloFormatado = $intervalo->format("%h%r");
                    if ($intervaloFormatado < 1) {
                        $bRetorno = false;
                    }
                }
            }
        }

        return $bRetorno;
    }

    /**
     * Verifica se o horario existe
     *
     * @param \App\Repository\Principal\HorarioAulaRepository $horarioAulaRepository
     * @param int $id
     * @param string $mensagemErro
     * @param \App\Entity\Principal\HorarioAula $horarioAulaORM
     *
     * @return boolean
     */
    public static function verificaHorarioAulaExiste(\App\Repository\Principal\HorarioAulaRepository $horarioAulaRepository, $id, &$mensagemErro, &$horarioAulaORM)
    {
        $horarioAulaORM = $horarioAulaRepository->find($id);
        if (is_null($horarioAulaORM) === true) {
            $mensagemErro = "Horario Aula não encontrado.";
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

        if ((isset($parametros[ConstanteParametros::CHAVE_DIA_SEMANA]) === true)&&(empty($parametros[ConstanteParametros::CHAVE_DIA_SEMANA]) === false)) {
            if (preg_match("/^(DOM|SEG|TER|QUA|QUI|SEX|SAB)$/", $parametros[ConstanteParametros::CHAVE_DIA_SEMANA]) !== 1) {
                $mensagemErro .= "Dia da semana invalido.\n";
                $bRetorno      = false;
            }
        } else {
            $mensagemErro .= "Dia da semana não encontrado.\n";
            $bRetorno      = false;
        }

        if ((isset($parametros[ConstanteParametros::CHAVE_HORARIO_INICIO]) === true) && (empty($parametros[ConstanteParametros::CHAVE_HORARIO_INICIO]) === false)) {
            $horarioRecebido = $parametros[ConstanteParametros::CHAVE_HORARIO_INICIO];
            \App\Helper\FunctionHelper::formataCampoDateTimeJS($horarioRecebido, $parametros[ConstanteParametros::CHAVE_HORARIO_INICIO]);
            if ($parametros[ConstanteParametros::CHAVE_HORARIO_INICIO] === false) {
                $mensagemErro .= "Ocorreu um erro na conversão no horario Inicio.\n Formato Invalido! Dado recebido:" . $horarioRecebido;
                $bRetorno      = false;
            }
        }

        return $bRetorno;
    }

    /**
     * Verifica se os parametros da requisicao sao validos
     *
     * @param array $parametros
     * @param string $mensagemErro
     * @param \App\Entity\Principal\HorarioAula $objetoORM
     */
    public function configuraParametrosAlteracao($parametros, &$mensagemErro, &$objetoORM)
    {
        if ((isset($parametros[ConstanteParametros::CHAVE_DIA_SEMANA]) === true)&&(empty($parametros[ConstanteParametros::CHAVE_DIA_SEMANA]) === false)) {
            if (preg_match("/^(DOM|SEG|TER|QUA|QUI|SEX|SAB)$/", $parametros[ConstanteParametros::CHAVE_DIA_SEMANA]) === 1) {
                $objetoORM->setDiaSemana($parametros[ConstanteParametros::CHAVE_DIA_SEMANA]);
            } else {
                $mensagemErro .= "Dia da semana invalido.\n";
            }
        }

        if ((isset($parametros[ConstanteParametros::CHAVE_HORARIO_INICIO]) === true) && (empty($parametros[ConstanteParametros::CHAVE_HORARIO_INICIO]) === false)) {
            \App\Helper\FunctionHelper::formataCampoDateTimeJS($parametros[ConstanteParametros::CHAVE_HORARIO_INICIO], $parametros[ConstanteParametros::CHAVE_HORARIO_INICIO]);
            if ($parametros[ConstanteParametros::CHAVE_HORARIO_INICIO] === false) {
                $mensagemErro .= "Ocorreu um erro na conversão no horario Inicio.\n Formato Invalido! Dado recebido:" . $parametros[ConstanteParametros::CHAVE_HORARIO_INICIO];
            } else {
                $objetoORM->setHorarioInicio($parametros[ConstanteParametros::CHAVE_HORARIO_INICIO]);
            }
        }

    }


}
