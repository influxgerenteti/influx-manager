<?php

namespace App\Reports\Repository;

use Doctrine\Persistence\ManagerRegistry;

class NotasFreqFaltasRepository
{
    /**
     * @var ManagerRegistry
     */
    private ManagerRegistry $managerRegistry;

    public function __construct(ManagerRegistry $managerRegistry)
    {
        $this->managerRegistry = $managerRegistry;
    }

    public function get($franqueadaId, $turmaId, $alunoId)
    {
        $sql = <<<SQL
            select	
                    ta.turma_id,
                    ad.aluno_id,
                    date_format(ta.data_aula,"%d/%m/%Y   %h:%m") as data_aula,
                    lc.descricao as licao,
                    lv.descricao as livro,
                    case 
                        when ad.presenca = 'P' then 
                            "Presente"
                        else 
                            "Falta"
                        end as presenca,
                    ad.presenca as idPresenca,
                    ad.atividade_ce,
                    ad.atividade_ca
                        
            from aluno_diario ad
            
            inner join turma_aula ta
                on ad.turma_aula_id = ta.id
            
            left join livro lv
                on ad.livro_id = lv.id
            
            inner join licao lc
                on ta.licao_id = lc.id 
            
            where 
                ad.franqueada_id = $franqueadaId
                and ta.turma_id = $turmaId
                and ad.aluno_id = $alunoId
                
            order by ta.data_aula
        SQL;

        return $this->managerRegistry->getConnection()->fetchAllAssociative($sql);
    }
}