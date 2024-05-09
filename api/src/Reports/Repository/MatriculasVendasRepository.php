<?php

namespace App\Reports\Repository;

use Doctrine\Persistence\ManagerRegistry;

class MatriculasVendasRepository
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
                    date_format(c.data_matricula, "%d/%m/%Y") as data_matricula,
                    (select icr.valor
                        from item_conta_receber icr
                        inner join item it
                            on icr.item_id = it.id
                     where 
                        icr.conta_receber_id = c.id
                        and it.tipo_item_id = 2) as taxa_matricula,
                    fp.descricao as tipo_pagamento,
                    (select icr.percentual_desconto
                        from item_conta_receber icr
                        inner join item it
                            on icr.item_id = it.id
                     where 
                        icr.conta_receber_id = c.id
                        and it.tipo_item_id = 2) as percentual_desconto,
                    "Não" as super_amigos,
                    f.apelido as consultor, 
                    case 
                        when i.tipo_lead = 'A' then	
                            "Ativo"
                        else
                            "Receptivo"
                        end as matricula
                        
            from contrato c
            
            inner join conta_receber cr
                on cr.contrato_id = c.id
                
            inner join titulo_receber tr
                on tr.conta_receber_id = cr.id 
                and POSITION("Matrícula" IN tr.observacao) > 0
                
            inner join forma_pagamento fp
                on tr.forma_recebimento_id = fp.id
            
            inner join funcionario f
                on c.responsavel_venda_funcionario_id = f.id
            
            inner join aluno a
                on c.aluno_id = a.id
                
            left join interessado i
                on i.aluno_id = a.id	
                
            where $where
        SQL;

        return $this->managerRegistry->getConnection()->fetchAllAssociative($sql);
    }
}