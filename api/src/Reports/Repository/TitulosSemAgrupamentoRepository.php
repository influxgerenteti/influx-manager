<?php

namespace App\Reports\Repository;

use Doctrine\Persistence\ManagerRegistry;

class TitulosSemAgrupamentoRepository
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
            SELECT
                p.nome_contato, 
            t.observacao parcela, 
                date_format(t.data_vencimento, '%d/%m/%Y') as data_vencimento,
                ifnull(date_format(mc.data_deposito, '%d/%m/%Y'), '-') as data_pagamento,
                ifnull(t.valor_parcela_sem_desconto, 0) - ifnull(t.desconto_antecipacao, 0) valor_liquido,
                t.situacao situacao_titulo,
                fc.descricao forma_cobranca,
                fp.descricao forma_pagamento,
                ifnull((
                    select sum(case WHEN m.operacao = 'C' THEN m.valor_lancamento ELSE - m.valor_lancamento END) 
                        FROM movimento_conta m 
                    WHERE m.titulo_receber_id = t.id
                ),0) valor_pago
                from titulo_receber t 
                LEFT JOIN forma_pagamento fc ON t.forma_cobranca_id = fc.id
                LEFT JOIN movimento_conta mc ON mc.titulo_receber_id = t.id AND mc.operacao = 'C' AND mc.estornado = 0
                LEFT JOIN forma_pagamento fp ON mc.forma_pagamento_id = fp.id
            , aluno a, pessoa p, conta_receber cr, contrato c
            WHERE t.aluno_id = a.id AND a.pessoa_id = p.id AND t.conta_receber_id = cr.id AND cr.contrato_id = c.id
            $where
        SQL;

        return $this->managerRegistry->getConnection()->fetchAllAssociative($sql);
    }
}