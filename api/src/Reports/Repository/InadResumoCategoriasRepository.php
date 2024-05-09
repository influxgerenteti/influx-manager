<?php

namespace App\Reports\Repository;

use Doctrine\Persistence\ManagerRegistry;

class InadResumoCategoriasRepository
{

    /**
     * @var ManagerRegistry
     */
    private ManagerRegistry $managerRegistry;

    public function __construct(ManagerRegistry $managerRegistry)
    {
        $this->managerRegistry = $managerRegistry;
    }

    public function get($franqueadaId)
    {
        $sql = <<<SQL
            select
                tr.observacao,
                count(tr.observacao) as qtd_parcelas_atrasadas,
                sum(tr.valor_original) as total_valor_atrasado
                
            from titulo_receber tr
            
            where DATE_FORMAT(tr.data_prorrogacao, '%Y-%m-%d') < CURDATE()
              and tr.franqueada_id = $franqueadaId
            group by tr.observacao
        SQL;

        return $this->managerRegistry->getConnection()->fetchAllAssociative($sql);
    }
}