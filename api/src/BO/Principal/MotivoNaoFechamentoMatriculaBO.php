<?php
namespace App\BO\Principal;

/**
 *
 * @author Luiz Antonio Costa
 */
class MotivoNaoFechamentoMatriculaBO
{


    /**
     * Verifica se existe o MotivoNaoFechamentoMatricula no banco de dados, se existir, ele retorna no ultimo parametro na funcao, caso contrario, ele preenchera a msg de erro e retornara falso
     *
     * @param \App\Repository\Principal\MotivoNaoFechamentoMatriculaRepository $motivoNaoFechamentoMatriculaRepository Repositorio do MotivoNaoFechamentoMatricula
     * @param integer $id Chave primaria da categoria
     * @param string $mensagemErro Msg de erro para ser retornada ao front-end
     * @param \App\Entity\Principal\MotivoNaoFechamentoMatricula|null $motivoNaoFechamentoMatriculaORM Retorno no caso de sucesso
     *
     * @return boolean true|false
     */
    public static function verificaMotivoNaoFechamentoMatriculaExiste(\App\Repository\Principal\MotivoNaoFechamentoMatriculaRepository $motivoNaoFechamentoMatriculaRepository, $id, &$mensagemErro, &$motivoNaoFechamentoMatriculaORM)
    {
        $motivoNaoFechamentoMatriculaORM = $motivoNaoFechamentoMatriculaRepository->find($id);
        if (is_null($motivoNaoFechamentoMatriculaORM) === true) {
            $mensagemErro = "MotivoNaoFechamentoMatricula não encontrado na base de dados.";
            return false;
        }

        return true;
    }


}
