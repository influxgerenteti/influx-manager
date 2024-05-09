<?php
namespace App\BO\Principal;

/**
 *
 * @author Luiz Antonio Costa
 */
class MotivoNaoFechamentoConvenioBO
{


    /**
     * Verifica se existe o MotivoNaoFechamentoConvenio no banco de dados, se existir, ele retorna no ultimo parametro na funcao, caso contrario, ele preenchera a msg de erro e retornara falso
     *
     * @param \App\Repository\Principal\MotivoNaoFechamentoConvenioRepository $motivoNaoFechamentoConvenioRepositoy Repositorio da MotivoNaoFechamentoConvenio
     * @param integer $id Chave primaria da MotivoNaoFechamentoConvenio
     * @param string $mensagemErro Msg de erro para ser retornada ao front-end
     * @param \App\Entity\Principal\MotivoNaoFechamentoConvenio|null $motivoNaoFechamentoConvenioORM Retorno no caso de sucesso
     *
     * @return boolean true|false
     */
    public static function verificaMotivoNaoFechamentoConvenioExiste(\App\Repository\Principal\MotivoNaoFechamentoConvenioRepository $motivoNaoFechamentoConvenioRepositoy, $id, &$mensagemErro, &$motivoNaoFechamentoConvenioORM)
    {
        $motivoNaoFechamentoConvenioORM = $motivoNaoFechamentoConvenioRepositoy->find($id);
        if (is_null($motivoNaoFechamentoConvenioORM) === true) {
            $mensagemErro = "MotivoNaoFechamentoConvenio não encontrada na base de dados.";
            return false;
        }

        return true;
    }


}
