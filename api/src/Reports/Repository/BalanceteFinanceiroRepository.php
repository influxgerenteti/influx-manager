<?php

namespace App\Reports\Repository;

use Doctrine\Persistence\ManagerRegistry;

class BalanceteFinanceiroRepository
{
    /**
     * @var ManagerRegistry
     */
    private ManagerRegistry $managerRegistry;

    public function __construct(ManagerRegistry $managerRegistry)
    {
        $this->managerRegistry = $managerRegistry;
    }

    public function get($franqueadaId, $conta, $mesInicial, $dataInicial, $dataFinal)
    {
        $sql = <<<SQL
            SELECT movimentoConta.observacao, data_contabil,   
                CASE
                WHEN operacao = 'C' THEN 
                    valor_lancamento
                ELSE 
                    valor_lancamento*(-1)
                END AS valor, 
                CASE
                WHEN operacao = 'D' THEN 
                    "Despesas"
                ELSE 
                    "Receitas"
                END AS operacao, (
                                    select					
                                        CASE
                                            WHEN operacao = 'D' THEN 
                                                valor_saldo_final_conta + valor_lancamento
                                            ELSE 
                                                valor_saldo_final_conta - valor_lancamento
                                        end as saldo
                                    FROM movimento_conta 
                                    where 
                                        movimentoConta.franqueada_id = $franqueadaId and	
                                        conta_id = $conta and	
                                        month(data_contabil) = $mesInicial
                                    order by data_movimento
                                    limit 1
                                 ) as saldo_inicial,
                conta.descricao as descricao_conta
            FROM movimento_conta movimentoConta
            inner join conta
                on movimentoConta.conta_id = conta.id
            where 
                movimentoConta.franqueada_id = $franqueadaId and	
                conta_id = $conta  and	
                data_contabil >= $dataInicial and
                data_contabil <= $dataFinal
            order by data_contabil
        SQL;

        return $this->managerRegistry->getConnection()->fetchAllAssociative($sql);
    }


}
