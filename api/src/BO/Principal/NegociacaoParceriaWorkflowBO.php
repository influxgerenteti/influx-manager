<?php
namespace App\BO\Principal;

/**
 *
 * @author Dayan Freitas
 */
class NegociacaoParceriaWorkflowBO
{


    /**
     * Verifica se existe o Negociacao parceria workflow existe no banco de dados, se existir, ele retorna no ultimo parametro na funcao, caso contrario, ele preenchera a msg de erro e retornara falso
     *
     * @param \App\Repository\Principal\NegociacaoParceriaWorkflowRepository $negociacaoParceriaWorkflowRepository Repositorio da NegociacaoParceriaWorkflow
     * @param integer $id Chave primaria da NegociacaoParceriaWorkflow
     * @param string $mensagemErro Msg de erro para ser retornada ao front-end
     * @param \App\Entity\Principal\NegociacaoParceriaWorkflow|null $negociacaoParceriaWorkflowORM Retorno no caso de sucesso
     *
     * @return boolean true|false
     */
    public static function verificarNegociacaoParceriaWorkflowExiste(\App\Repository\Principal\NegociacaoParceriaWorkflowRepository $negociacaoParceriaWorkflowRepository, $id, &$mensagemErro, &$negociacaoParceriaWorkflowORM)
    {
        $negociacaoParceriaWorkflowORM = $negociacaoParceriaWorkflowRepository->find($id);
        if (is_null($negociacaoParceriaWorkflowORM) === true) {
            $mensagemErro = "NegociacaoParceriaWorkflow não encontrada na base de dados.";
            return false;
        }

        return true;
    }


}
