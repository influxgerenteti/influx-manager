<?php
namespace App\BO\Principal;

/**
 *
 * @author Rodrigo de Souza Fernandes (GATI labs)
 */
class RelacionamentoAlunoBO
{


    /**
     * Verifica se existe a RelacionamentoAluno no banco de dados, se existir, ele retorna no ultimo parametro na funcao, caso contrario, ele preenchera a msg de erro e retornara falso
     *
     * @param \App\Repository\Principal\RelacionamentoAlunoRepository $relacionamentoAlunoRepositoy Repositorio de RelacionamentoAluno
     * @param integer $id Chave primaria da RelacionamentoAluno
     * @param string $mensagemErro Msg de erro para ser retornada ao front-end
     * @param \App\Entity\Principal\RelacionamentoAluno|null $relacionamentoAlunoORM Retorno no caso de sucesso
     *
     * @return boolean true|false
     */
    public static function verificaRelacionamentoAlunoExiste(\App\Repository\Principal\RelacionamentoAlunoRepository $relacionamentoAlunoRepositoy, $id, &$mensagemErro, &$relacionamentoAlunoORM)
    {
        $relacionamentoAlunoORM = $relacionamentoAlunoRepositoy->find($id);
        if ($relacionamentoAlunoORM === null) {
            $mensagemErro = "RelacionamentoAluno não encontrada na base de dados.";
            return false;
        }

        return true;
    }


}
