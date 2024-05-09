<?php
namespace App\BO\Principal;

use Doctrine\ORM\EntityManagerInterface;
use App\Helper\ConstanteParametros;

/**
 *
 * @author Dayan Freitas
 */
class BonusClassBO extends GenericBO
{


    function __construct(EntityManagerInterface $entityManager)
    {
        parent::configuraGenericBO(
            [
                "franqueadaRepository"     => $entityManager->getRepository(\App\Entity\Principal\Franqueada::class),
                "funcionarioRepository"    => $entityManager->getRepository(\App\Entity\Principal\Funcionario::class),
                "salaFranqueadaRepository" => $entityManager->getRepository(\App\Entity\Principal\SalaFranqueada::class),
            ]
        );
    }


    /**
     * Verificar os campos relacionais obrigatorios
     *
     * @param array $parametros Ponteiro de array de parametros para realizar a validacao
     * @param string $mensagemErro Mensagem de erro a ser retornado ao front-end
     *
     * @return boolean
     */
    protected function verificaRelacionamentosObrigatorios (&$parametros, &$mensagemErro)
    {
        $parametros[ConstanteParametros::CHAVE_FRANQUEADA] = \App\Helper\VariaveisCompartilhadas::$franqueadaID;
        self::verificaFranqueadaExisteBO($parametros, $mensagemErro, $parametros[ConstanteParametros::CHAVE_FRANQUEADA]);

        return true;
    }

    /**
     * Verificar os campos relacionais opcionais
     *
     * @param array $parametros Ponteiro de array de parametros para realizar a validacao
     * @param string $mensagemErro Mensagem de erro a ser retornado ao front-end
     *
     * @return boolean
     */
    protected function verificaRelacionamentosOpcionais (&$parametros, &$mensagemErro)
    {
        $bRetornoFuncionario    = true;
        $bRetornoSalaFranqueada = true;

        if ((isset($parametros[ConstanteParametros::CHAVE_FUNCIONARIO]) === true) && (empty($parametros[ConstanteParametros::CHAVE_FUNCIONARIO]) === false)) {
            $bRetornoFuncionario = self::verificaFuncionarioExisteBO($parametros, $mensagemErro, ConstanteParametros::CHAVE_FUNCIONARIO, $parametros[ConstanteParametros::CHAVE_FUNCIONARIO]);
        }

        if ((isset($parametros[ConstanteParametros::CHAVE_SALA_FRANQUEADA]) === true) && (empty($parametros[ConstanteParametros::CHAVE_SALA_FRANQUEADA]) === false)) {
            $bRetornoSalaFranqueada = self::verificaSalaFranqueadaExisteBO($parametros, $mensagemErro, $parametros[ConstanteParametros::CHAVE_SALA_FRANQUEADA]);
        }

        return $bRetornoFuncionario && $bRetornoSalaFranqueada;
    }

    /**
     * Fução para configurar os parametros
     *
     * @param array $parametros Ponteiro de array de parametros para realizar a validacao
     * @param string $mensagemErro Mensagem de erro a ser retornado ao front-end
     *
     * @return boolean
     */
    protected function configuraCampos(&$parametros, &$mensagemErro)
    {
        $bRetornoDataAula       = null;
        $bRetornoHorarioInicio  = null;
        $bRetornoHorarioTermino = null;

        if ((isset($parametros[ConstanteParametros::CHAVE_DATA_AULA]) === true)&&(empty($parametros[ConstanteParametros::CHAVE_DATA_AULA]) === false)) {
            \App\Helper\FunctionHelper::formataCampoDateTimeJS($parametros[ConstanteParametros::CHAVE_DATA_AULA], $bRetornoDataAula);
            if ($bRetornoDataAula === false) {
                $mensagemErro .= "Erro ao converter a data aula o formato aceito pelo banco de dados.[" . $bRetornoDataAula . "]";
            } else {
                $parametros[ConstanteParametros::CHAVE_DATA_AULA] = $bRetornoDataAula;
            }
        }

        if ((isset($parametros[ConstanteParametros::CHAVE_HORARIO_INICIO]) === true)&&(empty($parametros[ConstanteParametros::CHAVE_HORARIO_INICIO]) === false)) {
            \App\Helper\FunctionHelper::formataCampoDateTimeJS($parametros[ConstanteParametros::CHAVE_HORARIO_INICIO], $bRetornoHorarioInicio);
            if ($bRetornoHorarioInicio === false) {
                $mensagemErro .= "Erro ao converter o horario de inicio o formato aceito pelo banco de dados.[" . $bRetornoHorarioInicio . "]";
            } else {
                $parametros[ConstanteParametros::CHAVE_HORARIO_INICIO] = $bRetornoHorarioInicio;
            }
        }

        if ((isset($parametros[ConstanteParametros::CHAVE_HORARIO_TERMINO]) === true)&&(empty($parametros[ConstanteParametros::CHAVE_HORARIO_TERMINO]) === false)) {
            \App\Helper\FunctionHelper::formataCampoDateTimeJS($parametros[ConstanteParametros::CHAVE_HORARIO_TERMINO], $bRetornoHorarioTermino);
            if ($bRetornoHorarioTermino === false) {
                $mensagemErro .= "Erro ao converter o horario de termino o formato aceito pelo banco de dados.[" . $bRetornoHorarioTermino . "]";
            } else {
                $parametros[ConstanteParametros::CHAVE_HORARIO_TERMINO] = $bRetornoHorarioTermino;
            }
        }

        return ($bRetornoDataAula !== false) && ($bRetornoHorarioInicio !== false) && ($bRetornoHorarioTermino !== false);
    }

    /**
     * Verifica se podemos prosseguir com o processo de salvar
     *
     * @param array $parametros Ponteiro de array de parametros para realizar a validacao
     * @param string $mensagemErro Mensagem de erro a ser retornado ao front-end
     *
     * @return boolean
     */
    public function podeSalvar(&$parametros, &$mensagemErro)
    {
        if ($this->verificaRelacionamentosObrigatorios($parametros, $mensagemErro) === true) {
            if ($this->verificaRelacionamentosOpcionais($parametros, $mensagemErro) === true) {
                if ($this->configuraCampos($parametros, $mensagemErro) === true) {
                    return true;
                }
            }
        }

        return false;
    }


    /**
     * Verifica se o bonus class existe atraves da Chave Primaria informada
     *
     * @param int $id Chave primaria a ser consultada
     * @param string $mensagemErro Mensagem de erro a ser retornado ao front-end
     * @param null|\App\Entity\Principal\BonusClass $bonusClassORM Ponteiro para retornar o objeto
     * @param null|\App\Repository\Principal\BonusClassRepository $bonusClassRepository Repositorio para ser acessado estaticamente
     *
     * @return boolean
     */
    public static function verificarBonusClassExite ($id, &$mensagemErro, &$bonusClassORM, $bonusClassRepository)
    {
        $bonusClassORM = $bonusClassRepository->buscarPorId($id);
        if (is_null($bonusClassORM) === true) {
            $mensagemErro .= "não encontrado na base de dados.";
            return false;
        }

        return true;
    }

    /**
     * Verifica se existe a Bonus class no banco de dados, se existir, ele retorna no ultimo parametro na funcao, caso contrario, ele preenchera a msg de erro e retornara falso
     *
     * @param \App\Repository\Principal\BonusClassRepository $bonusClassRepository Repositorio da BonusClass
     * @param integer $id Chave primaria da categoria
     * @param string $mensagemErro Msg de erro para ser retornada ao front-end
     * @param \App\Entity\Principal\BonusClass|null $bonusClassORM Retorno no caso de sucesso
     *
     * @return boolean true|false
     */
    public static function verificarBonusClassExiteBO(\App\Repository\Principal\BonusClassRepository $bonusClassRepository, $id, &$mensagemErro, &$bonusClassORM)
    {

        $bonusClassORM = $bonusClassRepository->find($id);
        if (is_null($bonusClassORM) === true) {
            $mensagemErro = "bonusClass não encontrado na base de dados.";
            return false;
        }

        return true;
    }


}
