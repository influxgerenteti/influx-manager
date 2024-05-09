<?php

namespace App\Reports\Repository;

use Doctrine\Persistence\ManagerRegistry;

class ContasReceberRepository
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
                tituloReceber.numero_parcela_documento as numero,
                replace(replace(replace(format(tituloReceber.valor_original, 2), ',', ';'), '.', ','), ';', '.') AS valor,
                tituloReceber.valor_original,
                date_format(tituloReceber.data_prorrogacao, "%d/%m/%Y") as data_vencimento,
                sacadoPessoa.nome_contato as destino,
                formaRecebimento.descricao as forma_recebimento,
                formaPagamento.descricao as forma_cobranca,
                planoConta.descricao as categoria,
                (
                    CASE
                        tituloReceber.situacao
                        WHEN 'PEN' THEN 'Pendente'
                        WHEN 'LIQ' THEN 'Liquidado'
                        WHEN 'BAI' THEN 'Baixado'
                        WHEN 'SUB' THEN 'Substituido'
                        WHEN 'DEV' THEN 'Cheque Devolvido'
                        ELSE (tituloReceber.situacao)
                    END
                ) AS situacao,
                replace(replace(replace(format(movimentoConta.valor_lancamento, 2), ',', ';'), '.', ','), ';', '.') AS valor_movimento,
                date_format(movimentoConta.data_contabil, "%d/%m/%Y") as data_movimento
            
            from
                titulo_receber tituloReceber
            
            join franqueada 
                ON franqueada.id = tituloReceber.franqueada_id
                
            join pessoa sacadoPessoa 
                ON sacadoPessoa.id = tituloReceber.sacado_pessoa_id
                
            join conta_receber contaReceber 
                ON contaReceber.id = tituloReceber.conta_receber_id
                
            join conta 
                ON conta.id = tituloReceber.conta_id
                
            join forma_pagamento formaRecebimento 
                ON formaRecebimento.id = tituloReceber.forma_recebimento_id
                
            left join movimento_conta movimentoConta 
                ON movimentoConta.titulo_receber_id = tituloReceber.id
            left join forma_pagamento formaPagamento 
                ON formaPagamento.id = movimentoConta.forma_pagamento_id	
            
            left join item_conta_receber itemContaReceber 
                ON itemContaReceber.conta_receber_id = contaReceber.id
            left join plano_conta planoConta 
                ON planoConta.id = itemContaReceber.plano_conta_id
                
            where $where
        SQL;

        return $this->managerRegistry->getConnection()->fetchAllAssociative($sql);
    }
}