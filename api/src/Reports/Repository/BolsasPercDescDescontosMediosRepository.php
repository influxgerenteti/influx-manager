<?php

namespace App\Reports\Repository;

use Doctrine\Persistence\ManagerRegistry;

class BolsasPercDescDescontosMediosRepository
{

    /**
     * @var ManagerRegistry
     */
    private ManagerRegistry $managerRegistry;

    public function __construct(ManagerRegistry $managerRegistry)
    {
        $this->managerRegistry = $managerRegistry;
    }

    public function get($franqueadaId, $semestreId)
    {
        $sql = <<<SQL
            select CONCAT(replace(FORMAT(avg(percentual_desconto),2),'.',','),' %') as desconto_medio 
            from item_conta_receber
            inner join conta_receber
                on item_conta_receber.conta_receber_id = conta_receber.id
            left join contrato
                on conta_receber.contrato_id = contrato.id
            inner join semestre
                on contrato.semestre_id = semestre.id
            left join turma	
                on contrato.turma_id = turma.id
            where percentual_desconto > 0
                and conta_receber.franqueada_id = $franqueadaId
                and turma.semestre_id = $semestreId
        SQL;

        return $this->managerRegistry->getConnection()->fetchAllAssociative($sql);
    }


}
