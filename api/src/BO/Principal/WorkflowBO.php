<?php
namespace App\BO\Principal;

use App\Helper\ConstanteParametros;
use App\Helper\SituacoesSistema;
use Doctrine\ORM\EntityManagerInterface;

/**
 *
 * @author Luiz Antonio Costa
 */
class WorkflowBO extends GenericBO
{

    /**
     *
     * @var \App\Repository\Principal\WorkflowRepository
     */
    private $workflowRepository;

    function __construct(EntityManagerInterface $entityManager)
    {
        $this->workflowRepository = $entityManager->getRepository(\App\Entity\Principal\Workflow::class);
    }

    /**
     * Verifica se existe contato telefonico
     *
     * @param array $parametros
     *
     * @return boolean
     */
    protected function verificaExisteContatoTelefonico($parametros)
    {
        $bTelefoneContato           = ((isset($parametros[ConstanteParametros::CHAVE_TELEFONE_CONTATO]) === true)&&(empty($parametros[ConstanteParametros::CHAVE_TELEFONE_CONTATO]) === false));
        $bTelefoneContatoSecundario = ((isset($parametros[ConstanteParametros::CHAVE_TELEFONE_SECUNDARIO]) === true)&&(empty($parametros[ConstanteParametros::CHAVE_TELEFONE_SECUNDARIO]) === false));

        return ($bTelefoneContato === true)||($bTelefoneContatoSecundario === true);
    }

    /**
     * Verifica qual workflow a ser aplicado no parametro, se não existir registro ele irá retornar false
     *
     * @param array $parametros
     *
     * @return boolean
     */
    public function verificaWorkflowParaAplicar(&$parametros)
    {
        $parametros[ConstanteParametros::CHAVE_WORKFLOW] = $this->workflowRepository->findOneBy([ConstanteParametros::CHAVE_TIPO_WORKFLOW => SituacoesSistema::WORKFLOW_CONTATO_INICIAL]);
        return is_null($parametros[ConstanteParametros::CHAVE_WORKFLOW]) === false;
    }

    /**
     * Verifica se o workflow ja existe no banco
     *
     * @param \App\Repository\Principal\WorkflowRepository $workflowRepository Repositorio do Workflow
     * @param int $id Parametros que possuam os campos das entidades e valores
     * @param string $mensagemErro Mensagem de Erro
     * @param \App\Entity\Principal\Workflow $workflowORM retorno objeto
     *
     * @return boolean
     */
    public static function verificaWorkflowExiste(\App\Repository\Principal\WorkflowRepository $workflowRepository, $id, &$mensagemErro, &$workflowORM=null)
    {
        $workflowORM = $workflowRepository->find($id);
        if (is_null($workflowORM) === true) {
            $mensagemErro = "Workflow não encontrado na base de dados.";
            return false;
        }

        return true;
    }


}
