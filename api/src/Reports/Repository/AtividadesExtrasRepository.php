<?php

namespace App\Reports\Repository;

use Doctrine\Persistence\ManagerRegistry;

class AtividadesExtrasRepository
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
            select ti.descricao as atividade,
                p.nome_contato as aluno,
                aae.presenca,
                date_format(ae.data_hora_inicio,"%d/%m/%Y %h:%m") as data_inicio,
                date_format(ae.data_hora_fim,"%d/%m/%Y %h:%m") as data_fim,
                ae.descricao_atividade as observacao,
                case
                    when cr.valor_total is not null then
                        replace(cr.valor_total,'.',',')
                    else 
                        "Isento"
                end as valor,
                case
                    when ae.situacao = 'C' then
                        "Concluído"
                    when ae.situacao = 'P' then
                        "Pendente"
                    else 
                        "Cancelada"
                end as situacao
        
        from atividade_extra ae
        inner join item i
            on ae.item_id = i.id
        inner join tipo_item ti
            on i.tipo_item_id = ti.id
        left  join aluno_atividade_extra aae
            on aae.atividade_extra_id = ae.id
        left join aluno a
            on aae.aluno_id = a.id
        left join pessoa p
            on a.pessoa_id = p.id
        left join atividade_extra_conta_receber aecr
            on aae.atividade_extra_id = aecr.atividade_extra_id
        left join conta_receber cr
            on cr.id = aecr.conta_receber_id
            
        where $where
        SQL;

        return $this->managerRegistry->getConnection()->fetchAllAssociative($sql);
    }

}