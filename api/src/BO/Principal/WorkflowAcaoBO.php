<?php
namespace App\BO\Principal;

use Doctrine\ORM\EntityManagerInterface;
use App\Helper\ConstanteParametros;

/**
 *
 * @author Luiz Antonio Costa
 */
class WorkflowAcaoBO extends GenericBO
{


    function __construct(EntityManagerInterface $entityManager)
    {
        parent::configuraGenericBO(
            [
                "workflowRepository" => $entityManager->getRepository(\App\Entity\Principal\Workflow::class),
            ]
        );
    }

    /**
     * Aplica o proximo destino do workflow
     *
     * @param \App\Entity\Principal\WorkflowAcao $workflowAcaoORM
     * @param string $mensagemErro
     * @param \App\Entity\Principal\Workflow|NULL $workflowORM
     */
    public function aplicaProximoDestinoWorkflow($workflowAcaoORM, &$mensagemErro, &$workflowORM)
    {
        $workflowDestinoORM = $workflowAcaoORM->getDestinoWorkflow();
        if (is_null($workflowDestinoORM) === false) {
            $workflowORM = null;
            self::verificaWorkflowExisteBO([ConstanteParametros::CHAVE_WORKFLOW => $workflowDestinoORM->getId()], $mensagemErro, $workflowORM);
        } else {
            self::verificaWorkflowExisteBO([ConstanteParametros::CHAVE_WORKFLOW => $workflowAcaoORM->getWorkflow()->getId()], $mensagemErro, $workflowORM);
        }
    }

    /**
     * Verifica se o workflowAcao ja existe no banco
     *
     * @param \App\Repository\Principal\WorkflowAcaoRepository $workflowAcaoRepository Repositorio do WorkflowAcao
     * @param int $id Parametros que possuam os campos das entidades e valores
     * @param string $mensagemErro Mensagem de Erro
     * @param \App\Entity\Principal\WorkflowAcao $workflowAcaoORM retorno objeto
     *
     * @return boolean
     */
    public static function verificaWorkflowAcaoExiste(\App\Repository\Principal\WorkflowAcaoRepository $workflowAcaoRepository, $id, &$mensagemErro, &$workflowAcaoORM=null)
    {
        $workflowAcaoORM = $workflowAcaoRepository->find($id);
        if (is_null($workflowAcaoORM) === true) {
            $mensagemErro = "WorkflowAcao não encontrado na base de dados.";
            return false;
        }

        return true;
    }


}
