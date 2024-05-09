<?php
namespace App\BO\Principal;

use App\Helper\ConstanteParametros;
use Doctrine\ORM\EntityManagerInterface;

/**
 *
 * @author Augusto Lucas de Souza (GATI labs)
 */
class AgendaComercialBO extends GenericBO
{


    function __construct(EntityManagerInterface $entityManager)
    {
        parent::configuraGenericBO(
            [
                "franqueadaRepository"        => $entityManager->getRepository(\App\Entity\Principal\Franqueada::class),
                "tipoAgendamentoRepository"   => $entityManager->getRepository(\App\Entity\Principal\TipoAgendamento::class),
                "followupComercialRepository" => $entityManager->getRepository(\App\Entity\Principal\FollowupComercial::class),
                "funcionarioRepository"       => $entityManager->getRepository(\App\Entity\Principal\Funcionario::class),
                "usuarioRepository"           => $entityManager->getRepository(\App\Entity\Principal\Usuario::class),
                "interessadoRepository"       => $entityManager->getRepository(\App\Entity\Principal\Interessado::class),
                "workflowAcaoRepository"      => $entityManager->getRepository(\App\Entity\Principal\WorkflowAcao::class),
            ]
        );
    }

    /**
     * Verifica campos obrigatórios relacionados a entidade
     *
     * @param array $parametros
     * @param string $mensagemErro
     *
     * @return boolean
     */
    protected function verificaCamposRelacionaisObrigatorios(&$parametros, &$mensagemErro)
    {
        if (self::verificaFranqueadaExisteBO($parametros, $mensagemErro, $parametros[ConstanteParametros::CHAVE_FRANQUEADA]) === true) {
            if (self::verificaFuncionarioExisteBO($parametros, $mensagemErro, ConstanteParametros::CHAVE_FUNCIONARIO, $parametros[ConstanteParametros::CHAVE_FUNCIONARIO]) === true) {
                if (self::verificaUsuarioExisteBO($parametros, $mensagemErro, $parametros[ConstanteParametros::CHAVE_USUARIO]) === true) {
                    return true;
                }
            }
        }

        return false;
    }

    /**
     * Verifica campos opcionais relacionados a entidade
     *
     * @param array $parametros
     * @param string $mensagemErro
     *
     * @return boolean
     */
    protected function verificaCamposRelacionaisOpcionais(&$parametros, &$mensagemErro)
    {
        $bRetornaInteressado     = true;
        $bRetornaDataAgendamento = true;

        if ((isset($parametros[ConstanteParametros::CHAVE_INTERESSADO]) === true)&&(empty($parametros[ConstanteParametros::CHAVE_INTERESSADO]) === false)) {
            $bRetornaInteressado = self::verificaInteressadoExisteBO($parametros, $mensagemErro, $parametros[ConstanteParametros::CHAVE_INTERESSADO]);
        }

        if ((isset($parametros[ConstanteParametros::CHAVE_DATA_PROXIMO_CONTATO]) === true)&&(empty($parametros[ConstanteParametros::CHAVE_DATA_PROXIMO_CONTATO]) === false)
            && (isset($parametros[ConstanteParametros::CHAVE_HORARIO_PROXIMO_CONTATO]) === true)&&(empty($parametros[ConstanteParametros::CHAVE_HORARIO_PROXIMO_CONTATO]) === false)
        ) {
                $dataProximoContato = null;
                \App\Helper\FunctionHelper::formataCampoDateTimeJS($parametros[ConstanteParametros::CHAVE_DATA_PROXIMO_CONTATO], $dataProximoContato);
                if ($parametros[ConstanteParametros::CHAVE_DATA_PROXIMO_CONTATO] === false) {
                $mensagemErro .= "Erro ao converter a data de proximo contato para o formato aceito pelo banco de dados.[" . $dataProximoContato . "]";
                } else {
                $horaMinutoArray = explode(":", $parametros[ConstanteParametros::CHAVE_HORARIO_PROXIMO_CONTATO]);
                $dataProximoContato->setTime($horaMinutoArray[0], $horaMinutoArray[1]);
                unset($parametros[ConstanteParametros::CHAVE_DATA_PROXIMO_CONTATO]);
                unset($parametros[ConstanteParametros::CHAVE_HORARIO_PROXIMO_CONTATO]);
                $parametros[ConstanteParametros::CHAVE_DATA_AGENDAMENTO] = $dataProximoContato;
                }
        }

        return $bRetornaInteressado && $bRetornaDataAgendamento;
    }

    /**
     *  Valida o tipo de agendamento
     *
     * @param array $parametros Ponteiro do array para realizar a validacao do destino workflow
     * @param string $mensagemErro Mensagem de erro a retornar pro front-end
     *
     * @return boolean
     */
    protected function validaTipoAgendaId(&$parametros, &$mensagemErro)
    {
        $bRetorno        = true;
        $tipoAgendamento = null;

        if ((isset($parametros[ConstanteParametros::CHAVE_WORKFLOW_ACAO]) === true)&&(empty($parametros[ConstanteParametros::CHAVE_WORKFLOW_ACAO]) === false)) {
            $workflowAcaoORM      = null;
            $bRetornoWorkflowAcao = self::verificaWorkflowAcaoExisteBO($parametros, $mensagemErro, $workflowAcaoORM);
            if ($bRetornoWorkflowAcao === true) {
                $destino = $workflowAcaoORM->getDestinoWorkflow();
                if (is_null($destino) === true) {
                    $destino = $workflowAcaoORM->getWorkflow();
                }

                $tipoWorkflow    = $destino->getTipoWorkflow();
                $tipoAgendamento = self::getTipoAgendamentoRepository()->findOneBy([ConstanteParametros::CHAVE_TIPO => $tipoWorkflow]);
                if (is_null($tipoAgendamento) === true) {
                    $mensagemErro         = 'Tipo de agendamento não encontrado.';
                    $bRetornoWorkflowAcao = false;
                }
            }
        } else {
            if ((isset($parametros[ConstanteParametros::CHAVE_INTERESSADO_ORM]) === true )&&(empty($parametros[ConstanteParametros::CHAVE_INTERESSADO_ORM]) === false )) {
                $interessadoORM = $parametros[ConstanteParametros::CHAVE_INTERESSADO_ORM];
                $destino        = $interessadoORM->getWorkflow();
                if (is_null($destino) === false) {
                    $tipoWorkflow    = $destino->getTipoWorkflow();
                    $tipoAgendamento = self::getTipoAgendamentoRepository()->findOneBy([ConstanteParametros::CHAVE_TIPO => $tipoWorkflow]);
                    if (is_null($tipoAgendamento) === true) {
                        $mensagemErro         = 'Tipo de agendamento não encontrado.';
                        $bRetornoWorkflowAcao = false;
                    }
                }
            }
        }//end if

        if (is_null($tipoAgendamento) === false) {
            $parametros[ConstanteParametros::CHAVE_TIPO_AGENDAMENTO] = $tipoAgendamento;
        }

        return $bRetorno;
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

        if ($this->verificaCamposRelacionaisObrigatorios($parametros, $mensagemErro) === true) {
            if ($this->verificaCamposRelacionaisOpcionais($parametros, $mensagemErro) === true) {
                if ($this->validaTipoAgendaId($parametros, $mensagemErro) === true) {
                    return true;
                }
            }
        }

        return false;
    }

    /**
     * Verifica se existe a agenda comercial no banco de dados, se existir, ele retorna no ultimo parametro na funcao, caso contrario, ele preenchera a msg de erro e retornara falso
     *
     * @param \App\Repository\Principal\AgendaComercialRepository $agendaComercialRepository Repositorio da agenda comercial
     * @param integer $id Chave primaria da agenda comercial
     * @param string$mensagemErro Msg de erro para ser retornada ao front-end
     * @param \App\Entity\Principal\AgendaComercial|null $agendaComercialORM Retorno no caso de sucesso
     * @param boolean $retornoObjeto Informa a funcao se deve retornar como Objeto ou Array por padrao sera Array
     *
     * @return boolean true|false
     */
    public static function verificaAgendaComercialExiste(\App\Repository\Principal\AgendaComercialRepository $agendaComercialRepository, $id, &$mensagemErro, &$agendaComercialORM, $retornoObjeto=false)
    {
        if ($retornoObjeto === true) {
            $agendaComercialORM = $agendaComercialRepository->find($id);
        } else {
            $agendaComercialORM = $agendaComercialRepository->buscarPorId($id);
        }

        if ($agendaComercialORM === null) {
            $mensagemErro = "Agenda comercial não encontrado na base de dados.";
            return false;
        }

        return true;
    }


}
