<?php
namespace App\BO\Principal;

use Doctrine\ORM\EntityManagerInterface;
use App\Helper\ConstanteParametros;

/**
 *
 * @author Luiz Antonio Costa
 */
class ChecklistAtividadeBO extends GenericBO
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
     * Verifica os Parametros que precisam de relacionamento que são obrigatorios
     *
     * @param array $parametros
     * @param string $mensagemErro
     *
     * @return boolean
     */
    protected function verificaParametrosRelacionaisObrigatorios(&$parametros, &$mensagemErro)
    {
        if (self::verificaFranqueadaExisteBO($parametros, $mensagemErro, $parametros[ConstanteParametros::CHAVE_FRANQUEADA]) === true) {
            return true;
        }

        return false;
    }

    /**
     * Verifica as regras para permitir que se crie um checklist de atividade
     *
     * @param array $parametros
     * @param string $mensagemErro
     *
     * @return boolean
     */
    public function podeSalvar(&$parametros, &$mensagemErro)
    {
        if ($this->verificaParametrosRelacionaisObrigatorios($parametros, $mensagemErro) === true) {
            return true;
        }

        return false;
    }

    /**
     * Verifica se existe o ChecklistAtividade no banco de dados, se existir, ele retorna no ultimo parametro na funcao, caso contrario, ele preenchera a msg de erro e retornara falso
     *
     * @param \App\Repository\Principal\ChecklistAtividadeRepository $checklistAtividadeRepostiroy Repositorio do ChecklistAtividade
     * @param integer $id Chave primaria do ChecklistAtividade
     * @param string $mensagemErro Msg de erro para ser retornada ao front-end
     * @param \App\Entity\Principal\ChecklistAtividade|null $checklistAtivadeORM Retorno no caso de sucesso
     *
     * @return boolean true|false
     */
    public static function verificaChecklistAtividadeExiste(\App\Repository\Principal\ChecklistAtividadeRepository $checklistAtividadeRepostiroy, $id, &$mensagemErro, &$checklistAtivadeORM)
    {
        $checklistAtivadeORM = $checklistAtividadeRepostiroy->find($id);
        if (is_null($checklistAtivadeORM) === true) {
            $mensagemErro = "ChecklistAtividade não encontrada na base de dados.";
            return false;
        }

        return true;
    }


}
