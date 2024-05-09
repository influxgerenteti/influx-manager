<?php
namespace App\BO\Principal;

use Doctrine\ORM\EntityManagerInterface;
use App\Helper\ConstanteParametros;
use App\Helper\FunctionHelper;
/**
 *
 * @author Luiz Antonio Costa
 */
class FuncionarioDisponibilidadeBO extends GenericBO
{
    /**
     *
     * @var \App\Repository\Principal\FuncionarioDisponibilidadeRepository
     */
    private $funcionarioDisponibilidadeRepository;

    function __construct(EntityManagerInterface $entityManager)
    {
        $this->funcionarioDisponibilidadeRepository = $entityManager->getRepository(\App\Entity\Principal\FuncionarioDisponibilidade::class);
        parent::configuraGenericBO(
            [
                "funcionarioRepository" => $entityManager->getRepository(\App\Entity\Principal\Funcionario::class),
            ]
        );
    }

    /**
     * Verifica campos relacionados a entidade
     *
     * @param array $parametros
     * @param string $mensagemErro
     *
     * @return boolean
     */
    protected function verificaCamposRelacionaisObrigatorios(&$parametros, &$mensagemErro)
    {
        if (self::verificaFuncionarioExisteBO($parametros, $mensagemErro, ConstanteParametros::CHAVE_FUNCIONARIO, $parametros[ConstanteParametros::CHAVE_FUNCIONARIO]) === true) {
            return true;
        }

        return false;
    }

    /**
     * Buscao objeto através da id informada
     *
     * @param \App\Repository\Principal\FuncionarioDisponibilidadeRepository $funcionarioDisponibilidadeRepository
     * @param int $id
     * @param string $mensagemErro
     * @param \App\Entity\Principal\FuncionarioDisponibilidade $funcionarioDisponibilidadeORM
     *
     * @return boolean
     */
    public static function verificaFuncionarioDisponibilidadeExiste(\App\Repository\Principal\FuncionarioDisponibilidadeRepository $funcionarioDisponibilidadeRepository, $id, &$mensagemErro, &$funcionarioDisponibilidadeORM)
    {
        $funcionarioDisponibilidadeORM = $funcionarioDisponibilidadeRepository->find($id);
        if (is_null($funcionarioDisponibilidadeORM) === true) {
            $mensagemErro = "Funcionario Disponibilidade não encontrado.";
            return false;
        }

        return true;
    }

    /**
     * Verifica se os campos relacionais estao validos e indica se pode salvar ou não
     *
     * @param array $parametros
     * @param string $mensagemErro
     *
     * @return boolean
     */
    public function podeSalvar(&$parametros, &$mensagemErro)
    {
        if ($this->verificaCamposRelacionaisObrigatorios($parametros, $mensagemErro) === true) {
            if ((isset($parametros[ConstanteParametros::CHAVE_HORA_INICIAL]) === true) && (empty($parametros[ConstanteParametros::CHAVE_HORA_INICIAL]) === false)) {
                FunctionHelper::formataCampoDateTimeJS($parametros[ConstanteParametros::CHAVE_HORA_INICIAL], $parametros[ConstanteParametros::CHAVE_HORA_INICIAL]);
                if ($parametros[ConstanteParametros::CHAVE_HORA_INICIAL] === false) {
                    $mensagemErro .= "Ocorreu um erro na conversão de Hora inicial.\n Formato Invalido! Dado recebido:" . $parametros[ConstanteParametros::CHAVE_HORA_INICIAL];
                } else {
                    if ((isset($parametros[ConstanteParametros::CHAVE_HORA_FINAL]) === true) && (empty($parametros[ConstanteParametros::CHAVE_HORA_FINAL]) === false)) {
                        FunctionHelper::formataCampoDateTimeJS($parametros[ConstanteParametros::CHAVE_HORA_FINAL], $parametros[ConstanteParametros::CHAVE_HORA_FINAL]);
                        if ($parametros[ConstanteParametros::CHAVE_HORA_FINAL] === false) {
                            $mensagemErro .= "Ocorreu um erro na conversão de Hora Final.\n Formato Invalido! Dado recebido:" . $parametros[ConstanteParametros::CHAVE_HORA_FINAL];
                        } else {
                            if (self::verificaDataFinalMaiorDataInicio($parametros[ConstanteParametros::CHAVE_HORA_FINAL], $parametros[ConstanteParametros::CHAVE_HORA_INICIAL]) === true) {
                                return true;
                            } else {
                                $mensagemErro .= "A hora final não pode ser menor que a hora inicial\n";
                            }
                        }
                    }
                }
            }
        }//end if

        return false;
    }

    /**
     * Verifica se pode alterar o registro
     *
     * @param array $parametros
     * @param string $mensagemErro
     * @param \App\Entity\Principal\FuncionarioDisponibilidade $objetoORM
     *
     * @return boolean
     */
    public function podeAlterar(&$parametros, &$mensagemErro, &$objetoORM)
    {
        $bAlterarHoraInicial = true;
        $bAlterarHoraFinal   = true;

        if ((isset($parametros[ConstanteParametros::CHAVE_HORA_INICIAL]) === true)
            && (empty($parametros[ConstanteParametros::CHAVE_HORA_INICIAL]) === false)
            && (isset($parametros[ConstanteParametros::CHAVE_HORA_FINAL]) === true)
            && (empty($parametros[ConstanteParametros::CHAVE_HORA_FINAL]) === false)
        ) {
            FunctionHelper::formataCampoDateTimeJS($parametros[ConstanteParametros::CHAVE_HORA_INICIAL], $parametros[ConstanteParametros::CHAVE_HORA_INICIAL]);

            if ($parametros[ConstanteParametros::CHAVE_HORA_INICIAL] !== false) {
                $objetoORM->setHoraInicial($parametros[ConstanteParametros::CHAVE_HORA_INICIAL]);
            } else {
                $mensagemErro       .= "Ocorreu um erro na conversão de Hora inicial.\n Formato Invalido! Dado recebido:" . $parametros[ConstanteParametros::CHAVE_HORA_INICIAL];
                $bAlterarHoraInicial = false;
            }

            FunctionHelper::formataCampoDateTimeJS($parametros[ConstanteParametros::CHAVE_HORA_FINAL], $parametros[ConstanteParametros::CHAVE_HORA_FINAL]);
            if ($parametros[ConstanteParametros::CHAVE_HORA_FINAL] !== false) {
                $objetoORM->setHoraFinal($parametros[ConstanteParametros::CHAVE_HORA_FINAL]);
            } else {
                $mensagemErro     .= "Ocorreu um erro na conversão de Hora final.\n Formato Invalido! Dado recebido:" . $parametros[ConstanteParametros::CHAVE_HORA_FINAL];
                $bAlterarHoraFinal = false;
            }

            if (self::verificaDataFinalMaiorDataInicio($objetoORM->getHoraFinal(), $objetoORM->getHoraInicial()) === false) {
                $mensagemErro       .= "A hora inicial não pode ser maior que a hora final\n";
                $bAlterarHoraInicial = false;
            }
        } else {
            if ((isset($parametros[ConstanteParametros::CHAVE_HORA_INICIAL]) === true) && (empty($parametros[ConstanteParametros::CHAVE_HORA_INICIAL]) === false)) {
                FunctionHelper::formataCampoDateTimeJS($parametros[ConstanteParametros::CHAVE_HORA_INICIAL], $parametros[ConstanteParametros::CHAVE_HORA_INICIAL]);
                if ($parametros[ConstanteParametros::CHAVE_HORA_INICIAL] !== false) {
                    $objetoORM->setHoraInicial($parametros[ConstanteParametros::CHAVE_HORA_INICIAL]);
                    if (self::verificaDataFinalMaiorDataInicio($objetoORM->getHoraFinal(), $objetoORM->getHoraInicial()) === false) {
                        $mensagemErro       .= "A hora inicial não pode ser maior que a hora final\n";
                        $bAlterarHoraInicial = false;
                    }
                } else {
                    $mensagemErro       .= "Ocorreu um erro na conversão de Hora inicial.\n Formato Invalido! Dado recebido:" . $parametros[ConstanteParametros::CHAVE_HORA_INICIAL];
                    $bAlterarHoraInicial = false;
                }
            }

            if ((isset($parametros[ConstanteParametros::CHAVE_HORA_FINAL]) === true) && (empty($parametros[ConstanteParametros::CHAVE_HORA_FINAL]) === false)) {
                FunctionHelper::formataCampoDateTimeJS($parametros[ConstanteParametros::CHAVE_HORA_FINAL], $parametros[ConstanteParametros::CHAVE_HORA_FINAL]);
                if ($parametros[ConstanteParametros::CHAVE_HORA_FINAL] !== false) {
                    $objetoORM->setHoraFinal($parametros[ConstanteParametros::CHAVE_HORA_FINAL]);
                    if (self::verificaDataFinalMaiorDataInicio($objetoORM->getHoraFinal(), $objetoORM->getHoraInicial()) === false) {
                        $mensagemErro     .= "A hora final não pode ser menor que a hora final\n";
                        $bAlterarHoraFinal = false;
                    }
                } else {
                    $mensagemErro     .= "Ocorreu um erro na conversão de Hora final.\n Formato Invalido! Dado recebido:" . $parametros[ConstanteParametros::CHAVE_HORA_FINAL];
                    $bAlterarHoraFinal = false;
                }
            }
        }//end if

        return ($bAlterarHoraInicial && $bAlterarHoraFinal);
    }

    /**
     * Configura os parametros para a parte de atualização
     *
     * @param array $parametros
     * @param \App\Entity\Principal\FuncionarioDisponibilidade $objetoORM
     * @param string $mensagemErro
     */
    public function configuraParametros($parametros, &$objetoORM, &$mensagemErro)
    {
        if ((isset($parametros[ConstanteParametros::CHAVE_DIA_SEMANA]) === true) && (empty($parametros[ConstanteParametros::CHAVE_DIA_SEMANA]) === false)) {
            $objetoORM->setDiaSemana($parametros[ConstanteParametros::CHAVE_DIA_SEMANA]);
        }

    }


}
