<?php
namespace App\BO\Principal;

/**
 *
 * @author Luiz Antonio Costa
 */
class ConceitoAvaliacaoBO
{


    /**
     * Verifica se o ConceitoAvaliacao existe atraves da id
     *
     * @param \App\Repository\Principal\ConceitoAvaliacaoRepository $conceitoAvaliacaoRepository
     * @param int $id cheve de busca
     * @param string  $mensagemErro mensagem de erro no front
     * @param null|\App\Entity\Principal\ConceitoAvaliacao $retornoORM objeto de retorno
     *
     * @return boolean
     */
    public static function verificaConceitoAvaliacaoExiste($conceitoAvaliacaoRepository, $id, &$mensagemErro, &$retornoORM=null)
    {
        $retornoORM = $conceitoAvaliacaoRepository->find($id);

        if (is_null($retornoORM) === true) {
            $mensagemErro = "Não encontrado conceito avaliação";
        }

        return is_null($retornoORM) === false;
    }


}
