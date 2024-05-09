<?php
namespace App\BO\Principal;

use App\Helper\ConstanteParametros;
use Doctrine\ORM\EntityManagerInterface;

/**
 *
 * @author Dayan Freitas
 */
class AlunosBonusClassBO extends GenericBO
{


    function __construct(EntityManagerInterface $entityManager)
    {
        parent::configuraGenericBO(
            [
                "franqueadaRepository" => $entityManager->getRepository(\App\Entity\Principal\Franqueada::class),
                "bonusClassRepository" => $entityManager->getRepository(\App\Entity\Principal\BonusClass::class),
                "alunoRepository"      => $entityManager->getRepository(\App\Entity\Principal\Aluno::class),
            ]
        );
    }

    /**
     * Verifica os relacionamentos que não são obrigatórios
     *
     * @param array $parametros
     * @param string $mensagemErro
     *
     * @return boolean
     */
    protected function verificaRelacionamentoOpcionais(&$parametros, &$mensagemErro)
    {
        $bRetornoAluno = true;

        if ((isset($parametros[ConstanteParametros::CHAVE_ALUNO]) === true)&&(empty($parametros[ConstanteParametros::CHAVE_ALUNO]) === false)) {
            $bRetornoAluno = self::verificaAlunoExisteBO($parametros, $mensagemErro, $parametros[ConstanteParametros::CHAVE_ALUNO]);
        }

        return $bRetornoAluno;
    }

    /**
     * Verifica se é possivel prosseguir com a criacao do registro com os parametros informados
     *
     * @param array $parametros
     * @param string $mensagemErro
     *
     * @return boolean
     */
    protected function verificaRelacionamentoObrigatorio(&$parametros, &$mensagemErro)
    {
        if (self::verificaBonusClassBO($parametros, $mensagemErro, $parametros[ConstanteParametros::CHAVE_BONUS_CLASS]) === true) {
            return true;
        }

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
        $bHorarioAula = null;

        if ((isset($parametros[ConstanteParametros::CHAVE_HORARIO_AULA]) === true)&&(empty($parametros[ConstanteParametros::CHAVE_HORARIO_AULA]) === false)) {
            \App\Helper\FunctionHelper::formataCampoDateTimeJS($parametros[ConstanteParametros::CHAVE_HORARIO_AULA], $bHorarioAula);

            if ($bHorarioAula === false) {
                $mensagemErro .= "Erro ao converter a data aula o formato aceito pelo banco de dados.[" . $bHorarioAula . "]";
            } else {
                $parametros[ConstanteParametros::CHAVE_HORARIO_AULA] = $bHorarioAula;
            }
        }

        return $bHorarioAula !== false;
    }

    /**
     * Verifica as regras para atualizar os dados
     *
     * @param array $parametros
     * @param string $mensagemErro
     *
     * @return boolean
     */
    public function podeSalvar (&$parametros, &$mensagemErro)
    {
        if ($this->verificaRelacionamentoObrigatorio($parametros, $mensagemErro) === true) {
            if ($this->verificaRelacionamentoOpcionais($parametros, $mensagemErro) === true) {
                if ($this->configuraCampos($parametros, $mensagemErro) === true) {
                    return true;
                }
            }
        }

        return false;
    }


}
