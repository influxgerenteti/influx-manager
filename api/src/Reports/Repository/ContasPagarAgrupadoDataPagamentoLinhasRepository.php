<?php

namespace App\Reports\Repository;

use Doctrine\Persistence\ManagerRegistry;

class ContasPagarAgrupadoDataPagamentoLinhasRepository
{
    /**
     * @var ManagerRegistry
     */
    private ManagerRegistry $managerRegistry;

    public function __construct(ManagerRegistry $managerRegistry)
    {
        $this->managerRegistry = $managerRegistry;
    }

    public function get($where, $dataContabil)
    {
        $sql = <<<SQL
            select
                tituloPagar.numero_parcela_documento as numero,
                replace(replace(replace(format(tituloPagar.valor_documento, 2), ',', ';'), '.', ','), ';', '.') AS valor,
                date_format(tituloPagar.data_vencimento, "%d/%m/%Y") as data_vencimento,
                favorecidoPessoa.nome_contato as destino,
                formaCobranca.descricao as forma_cobranca,
                (
                    CASE
                        tituloPagar.situacao
                        WHEN 'PEN' THEN if (tituloPagar.data_prorrogacao <  CURDATE(), 'VENCIDO' , 'Pendente')
                        WHEN 'LIQ' THEN 'Liquidado'
                        WHEN 'BAI' THEN 'Baixado'
                        WHEN 'SUB' THEN 'Substituido'
                        WHEN 'DEV' THEN 'Cheque Devolvido'
                        ELSE (tituloPagar.situacao)
                    END
                ) AS situacao,
                replace(replace(replace(format(movimentoConta.valor_lancamento, 2), ',', ';'), '.', ','), ';', '.') AS valor_movimento,
                date_format(movimentoConta.data_contabil, "%d/%m/%Y") as data_movimento
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
            and (
                (IFNULL(movimentoConta.data_contabil, "1950-01-01") = $dataContabil)
                OR (
                    movimentoConta.data_contabil >= $dataContabil
                    AND movimentoConta.data_contabil <= CONCAT($dataContabil, " 23:59:59")
                )
            )        
        SQL;

        return $this->managerRegistry->getConnection()->fetchAllAssociative($sql);
    }
}