<?php
namespace App\BO\Principal;

use App\Helper\ConstanteParametros;
use App\Helper\SituacoesSistema;
use Doctrine\ORM\EntityManagerInterface;

/**
 *
 * @author Luiz A Costa
 */
class ReagendamentoPersonalBO extends GenericBO
{


    function __construct(EntityManagerInterface $entityManager)
    {
        parent::configuraGenericBO(
            [
                "agendamentoPersonalRepository" => $entityManager->getRepository(\App\Entity\Principal\AgendamentoPersonal::class),
                "funcionarioRepository"         => $entityManager->getRepository(\App\Entity\Principal\Funcionario::class),
                "salaFranqueadaRepository"      => $entityManager->getRepository(\App\Entity\Principal\SalaFranqueada::class),
            ]
        );
    }

    /**
     * Verifica se os parametros passados são validos
     *
     * @param array $parametros
     * @param string $mensagemErro
     *
     * @return boolean
     */
    protected function verificaParametrosRelacionaisObrigatorios(&$parametros, &$mensagemErro)
    {
        if (self::verificaAgendamentoPersonalExiste($parametros, $mensagemErro, $parametros[ConstanteParametros::CHAVE_AGENDAMENTO_PERSONAL]) === true) {
            if (self::verificaFuncionarioExisteBO($parametros, $mensagemErro, ConstanteParametros::CHAVE_FUNCIONARIO, $parametros[ConstanteParametros::CHAVE_FUNCIONARIO]) === true) {
                if (self::verificaSalaFranqueadaExisteBO($parametros, $mensagemErro, $parametros[ConstanteParametros::CHAVE_SALA_FRANQUEADA]) === true) {
                    return true;
                }
            }
        }

        return false;
    }

    /**
     * Verifica se o agendamento pertence a semana atual
     *
     * @param \DateTime $dataSelecionada
     * @param \DateTime $dataOriginalAgendamento
     * @param string $mensagemErro
     *
     * @return boolean
     */
    protected function verificaAgendamentoPertenceSemana($dataSelecionada, $dataOriginalAgendamento, &$mensagemErro)
    {
        $semanaDataSelecionada         = (int) $dataSelecionada->format("W");
        $semanaDataOriginalAgendamento = (int) $dataOriginalAgendamento->format("W");
        if ($semanaDataSelecionada === $semanaDataOriginalAgendamento) {
            return true;
        }

        $mensagemErro .= "A data selecionada para a realização do agendamento, não pertence à mesma semana do agendamento criado.\nFavor informar uma data dentro da semana.";
        return false;
    }

    /**
     * Realiza a verificacao das regras para permitir ou nao a criacao do registro
     *
     * @param array $parametros Ponteiro do array para realizar a formatacao
     * @param string $mensagemErro Mensagem de erro a retornar pro front-end
     *
     * @return boolean
     */
    public function podeCriar(&$parametros, &$mensagemErro)
    {
        if ($this->verificaParametrosRelacionaisObrigatorios($parametros, $mensagemErro) === true) {
            if (self::converteData($parametros[ConstanteParametros::CHAVE_DATA_REAGENDADA], $mensagemErro, ConstanteParametros::CHAVE_DATA_REAGENDADA) === true) {
                if ($this->verificaAgendamentoPertenceSemana($parametros[ConstanteParametros::CHAVE_DATA_REAGENDADA], $parametros[ConstanteParametros::CHAVE_AGENDAMENTO_PERSONAL]->getInicio(), $mensagemErro) === true)
                return true;
            }
        }

        return false;
    }

    /**
     * Realiza a verificacao das regras para permitir ou nao a criacao do registro
     *
     * @param array $parametros Ponteiro do array para realizar a formatacao
     * @param string $mensagemErro Mensagem de erro a retornar pro front-end
     *
     * @return boolean
     */
    public function podeAtualizar(&$parametros, &$mensagemErro)
    {
        if (true) {
            return true;
        }

        return false;
    }


}
