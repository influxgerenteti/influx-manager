<?php
namespace App\BO\Principal;

/**
 *
 * @author Luiz Antonio Costa
 */
class ChecklistBO
{


    /**
     * Verifica se existe a Checklist no banco de dados, se existir, ele retorna no ultimo parametro na funcao, caso contrario, ele preenchera a msg de erro e retornara falso
     *
     * @param \App\Repository\Principal\ChecklistRepository $checklistRepository Repositorio da Checklist
     * @param integer $id Chave primaria da Checklist
     * @param string $mensagemErro Msg de erro para ser retornada ao front-end
     * @param \App\Entity\Principal\Checklist|null $checklistORM Retorno no caso de sucesso
     *
     * @return boolean true|false
     */
    public static function verificaChecklistExiste(\App\Repository\Principal\ChecklistRepository $checklistRepository, $id, &$mensagemErro, &$checklistORM)
    {
        $checklistORM = $checklistRepository->find($id);
        if (is_null($checklistORM) === true) {
            $mensagemErro = "Checklist não encontrada na base de dados.";
            return false;
        }

        return true;
    }


}
