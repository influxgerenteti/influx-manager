<?php
namespace App\BO\Principal;

/**
 *
 * @author Luiz Antonio Costa
 */
class CondicaoPagamentoBO extends GenericBO
{


    /**
     * Verifica se a condicao de pagamento existe atraves da id
     *
     * @param \App\Repository\Principal\CondicaoPagamentoRepository $condicaoPagamentoRepository
     * @param int $id
     * @param null|\App\Entity\Principal\CondicaoPagamento $retornoORM
     *
     * @return boolean
     */
    public static function verificaCondicaoPagamentoExiste($condicaoPagamentoRepository, $id, &$retornoORM=null)
    {
        $retornoORM = $condicaoPagamentoRepository->find($id);
        return is_null($retornoORM) === false;
    }


}
