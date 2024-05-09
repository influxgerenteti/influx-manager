<?php

namespace App\Facade\Principal;

use App\Helper\ConstanteParametros;
use Doctrine\Common\Persistence\ManagerRegistry;

/**
 *
 * @author Luiz A Costa
 */
class CondicaoPagamentoParcelaFacade extends GenericFacade
{


    /**
     * {@inheritDoc}
     *
     * @see \App\Facade\Principal\GenericFacade::__construct()
     */
    function __construct (ManagerRegistry $managerRegistry, $connection="base_principal")
    {
        parent::__construct($managerRegistry);
    }

    /**
     * Cria um objeto no banco de dados
     *
     * @param string $mensagemErro Mensagem que ira retornar para front-end
     * @param \App\Entity\Principal\CondicaoPagamento $condicaoPagamento Objeto cadastrado da condicacao de pagamento
     * @param array $parametros Parametros a serem inclusos no objeto
     *
     * @return null|\App\Entity\Principal\CondicaoPagamentoParcela
     */
    public function criar(&$mensagemErro, &$condicaoPagamento, $parametros=[])
    {
        $objetoORM = null;
        foreach ($parametros[ConstanteParametros::CHAVE_PARCELA] as $parcela) {
            $parcela[ConstanteParametros::CHAVE_CONDICAO_PAGAMENTO] = $condicaoPagamento;
            $objetoORM = \App\Factory\GeneralORMFactory::criar(\App\Entity\Principal\CondicaoPagamentoParcela::class, true, $parcela);
            self::criarRegistro($objetoORM, $mensagemErro);
            self::getEntityManager()->refresh($condicaoPagamento);
        }

        return $objetoORM;
    }


}
