<?php
namespace App\BO\Principal;

use Doctrine\ORM\EntityManagerInterface;
use App\Helper\ConstanteParametros;

/**
 *
 * @author Luiz Antonio Costa
 */
class ChecklistAtividadeRealizadaBO extends GenericBO
{


    function __construct(EntityManagerInterface $entityManager)
    {
        parent::configuraGenericBO(
            [
                "checklistRepository"          => $entityManager->getRepository(\App\Entity\Principal\Checklist::class),
                "checklistAtividadeRepository" => $entityManager->getRepository(\App\Entity\Principal\ChecklistAtividade::class),
                "franqueadaRepository"         => $entityManager->getRepository(\App\Entity\Principal\Franqueada::class),
                "usuarioRepository"            => $entityManager->getRepository(\App\Entity\Principal\Usuario::class),
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
            if (self::verificaUsuarioExisteBO($parametros, $mensagemErro, $parametros[ConstanteParametros::CHAVE_USUARIO]) === true) {
                if (self::verificaChecklistExisteBO($parametros, $mensagemErro, $parametros[ConstanteParametros::CHAVE_CHECKLIST]) === true) {
                    if (self::verificaChecklistAtividadeBO($parametros, $mensagemErro, $parametros[ConstanteParametros::CHAVE_CHECKLIST_ATIVIDADE]) === true) {
                        return true;
                    }
                }
            }
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
     * Verifica se existe o ChecklistAtividadeRealizada no banco de dados, se existir, ele retorna no ultimo parametro na funcao, caso contrario, ele preenchera a msg de erro e retornara falso
     *
     * @param \App\Repository\Principal\ChecklistAtividadeRealizadaRepository $checklistAtividadeRealizadaRepostiroy Repositorio do ChecklistAtividadeRealizada
     * @param integer $id Chave primaria do ChecklistAtividadeRealizada
     * @param string $mensagemErro Msg de erro para ser retornada ao front-end
     * @param \App\Entity\Principal\ChecklistAtividadeRealizada|null $checklistAtivadeRealizadaORM Retorno no caso de sucesso
     *
     * @return boolean true|false
     */
    public static function verificaChecklistAtividadeRealizadaExiste(\App\Repository\Principal\ChecklistAtividadeRealizadaRepository $checklistAtividadeRealizadaRepostiroy, $id, &$mensagemErro, &$checklistAtivadeRealizadaORM)
    {
        $checklistAtivadeRealizadaORM = $checklistAtividadeRealizadaRepostiroy->find($id);
        if (is_null($checklistAtivadeRealizadaORM) === true) {
            $mensagemErro = "ChecklistAtividadeRealizada não encontrada na base de dados.";
            return false;
        }

        return true;
    }


}
