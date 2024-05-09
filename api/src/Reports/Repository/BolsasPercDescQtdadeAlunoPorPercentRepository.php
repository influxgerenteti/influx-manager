<?php

namespace App\Reports\Repository;

use Doctrine\Persistence\ManagerRegistry;

class BolsasPercDescQtdadeAlunoPorPercentRepository
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
            select distinct
                semestre.descricao,
                CONCAT(replace(FORMAT(i1.percentual_desconto,2),'.',','),' %') as percentual_desconto,
                (
                    select
                        count(i2.percentual_desconto)		
                    from item_conta_receber i2
                        inner join conta_receber cr
                            on i2.conta_receber_id = cr.id
                        left join contrato c
                            on cr.contrato_id = c.id
                    where i2.percentual_desconto = i1.percentual_desconto 
                        and c.bolsista = 0  
                ) as qntdade_nao_bolsistas,
                (
                    select
                        count(i2.percentual_desconto)		
                    from item_conta_receber i2
                        inner join conta_receber cr
                            on i2.conta_receber_id = cr.id
                        left join contrato c
                            on cr.contrato_id = c.id
                    where i2.percentual_desconto = i1.percentual_desconto 
                        and c.bolsista = 1  
                ) as qntdade_bolsistas,
                (
                    select
                        count(i2.percentual_desconto)		
                    from item_conta_receber i2
                    where i2.percentual_desconto = i1.percentual_desconto  
                ) as qntdade_total
                    
            from item_conta_receber i1
            inner join conta_receber
                on i1.conta_receber_id = conta_receber.id
            left join contrato
                on conta_receber.contrato_id = contrato.id
            inner join semestre
                on contrato.semestre_id = semestre.id
            left join turma	
                on contrato.turma_id = turma.id
            
            where i1.percentual_desconto > 0 
                and conta_receber.franqueada_id =$franqueadaId       
                and turma.semestre_id = $semestreId
        SQL;

        return $this->managerRegistry->getConnection()->fetchAllAssociative($sql);
    }
}