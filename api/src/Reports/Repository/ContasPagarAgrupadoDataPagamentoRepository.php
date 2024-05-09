<?php

namespace App\Reports\Repository;

use Doctrine\Persistence\ManagerRegistry;

class ContasPagarAgrupadoDataPagamentoRepository
{
    /**
     * @var ManagerRegistry
     */
    private ManagerRegistry $managerRegistry;

    public function __construct(ManagerRegistry $managerRegistry)
    {
        $this->managerRegistry = $managerRegistry;
    }

    public function get($where)
    {
        $sql = <<<SQL
            select
                DISTINCT(date_format(movimentoConta.data_contabil, '%Y-%m-%d')) as data_contabil_ingles,
                date_format(movimentoConta.data_contabil, '%d/%m/%Y') as data_contabil_exibicao,
                SUM(tituloPagar.valor_documento) AS soma_total,
                SUM(movimentoConta.valor_lancamento) AS soma_movimento
            from
                titulo_pagar tituloPagar
            join franqueada ON franqueada.id = tituloPagar.franqueada_id
            join pessoa favorecidoPessoa ON favorecidoPessoa.id = tituloPagar.favorecido_pessoa_id
            join conta_pagar contaPagar ON contaPagar.id = tituloPagar.conta_pagar_id
            join conta ON conta.id = tituloPagar.conta_id
            join forma_pagamento formaCobranca ON formaCobranca.id = tituloPagar.forma_cobranca_id 
            left join movimento_conta movimentoConta ON movimentoConta.titulo_pagar_id = tituloPagar.id
            left join forma_pagamento formaPagamento ON formaPagamento.id = movimentoConta.forma_pagamento_id
            left join plano_contas_conta_pagar planoContaContaPagar ON planoContaContaPagar.conta_pagar_id = contaPagar.id
            LEFT JOIN plano_conta planoConta ON planoConta.id = planoContaContaPagar.plano_conta_id
            where $where
            AND movimentoConta.data_contabil IS NOT NULL
            GROUP BY data_contabil_ingles, data_contabil_exibicao
            ORDER BY data_contabil_ingles
        SQL;

        return $this->managerRegistry->getConnection()->fetchAllAssociative($sql);
    }
}