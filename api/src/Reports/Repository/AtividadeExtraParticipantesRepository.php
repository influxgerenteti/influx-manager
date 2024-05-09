<?php

namespace App\Reports\Repository;

use Doctrine\Persistence\ManagerRegistry;

class AtividadeExtraParticipantesRepository
{
    /**
     * @var ManagerRegistry
     */
    private ManagerRegistry $managerRegistry;

    public function __construct(ManagerRegistry $managerRegistry)
    {
        $this->managerRegistry = $managerRegistry;
    }

    public function get($atividadeExtraId, $franqueadaId)
    {
        $sql = <<<SQL
            select 
                  p.nome_contato as aluno, 
                  ti.descricao as tipo,
                  date_format(ae.data_hora_inicio,"%d/%m/%Y") as data,
                  s.descricao as sala,
                  f.apelido as instrutor
                  from aluno_atividade_extra aae
                  inner join aluno a
                  on aae.aluno_id = a.id
                  inner join pessoa p
                  on a.pessoa_id = p.id
                  inner join atividade_extra ae
                  on aae.atividade_extra_id = ae.id
                  inner join item i
                  on ae.item_id = i.id
                  inner join tipo_item ti
                  on i.tipo_item_id = ti.id
                  left join sala_franqueada sf 
                  on ae.sala_franqueada_id = sf.id
                  left join sala s
                  on sf.sala_id = s.id
                  left join atividade_extra_funcionario aef
                  on aef.atividade_extra_id = ae.id
                  left join funcionario f
                  on aef.funcionario_id = f.id
                  where aae.atividade_extra_id = $atividadeExtraId
                  and ae.franqueada_id = $franqueadaId
                  and aae.removido = 0
        SQL;

        return $this->managerRegistry->getConnection()->fetchAllAssociative($sql);
    }


}
