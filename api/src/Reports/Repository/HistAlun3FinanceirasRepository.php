<?php

namespace App\Reports\Repository;

use Doctrine\Persistence\ManagerRegistry;

class HistAlun3FinanceirasRepository
{
    /**
     * @var ManagerRegistry
     */
    private ManagerRegistry $managerRegistry;

    public function __construct(ManagerRegistry $managerRegistry)
    {
        $this->managerRegistry = $managerRegistry;
    }

    public function get($franqueadaId, $alunoId)
    {
        $sql = <<<SQL
            select 
                    observacao,
                    numero_parcela_documento,
                    date_format(data_vencimento,'%d/%m/%Y') as data_vencimento,
                    replace(replace(replace(format(valor_original, 2), ',', ';'), '.', ','), ';', '.') AS valor_original,
                    '' as valor_liquido,
                    case situacao
                        when 'LIQ' then
                            date_format(data_prorrogacao,'%d/%m/%Y') 
                        else ''
                    end as data_pagto,
                    replace(replace(replace(format(valor_original-valor_saldo_devedor, 2), ',', ';'), '.', ','), ';', '.') AS valor_pago,
                    '' as numero_boleto
            from titulo_receber
            where franqueada_id = $franqueadaId
                and aluno_id = $alunoId
        SQL;

        return $this->managerRegistry->getConnection()->fetchAllAssociative($sql);
    }


}
