<?php

namespace App\Reports\Repository;

use Doctrine\Persistence\ManagerRegistry;

class NotasFrequenciasRepository
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
            select aa.franqueada_id as franqueada_id,
                t.id as turma_id,
                t.descricao as turma_descricao,
                a.id as aluno_id,
                upper(p.nome_contato) as nome_aluno,
                concat(format(((select COUNT(ad.presenca) 
                    from aluno_diario ad
                    inner join turma_aula ta
                        on ad.turma_aula_id = ta.id
                    where
                        ta.turma_id = aa.turma_id
                        and ad.aluno_id = aa.aluno_id
                        and ad.presenca = 'P')
                    /
                 (select COUNT(ad.presenca) 
                    from aluno_diario ad
                    inner join turma_aula ta
                        on ad.turma_aula_id = ta.id
                    where
                        ta.turma_id = aa.turma_id
                        and ad.aluno_id = aa.aluno_id)
                )*100,2,'de_DE') , "%") as frequencia_percent,
                (select COUNT(ad.presenca) 
                        from aluno_diario ad
                        inner join turma_aula ta
                            on ad.turma_aula_id = ta.id
                        where
                            ta.turma_id = aa.turma_id
                            and ad.aluno_id = aa.aluno_id
                            and ad.presenca = 'P') as qtde_faltas,
            
                        (select COUNT(ad.presenca) 
                        from aluno_diario ad
                        inner join turma_aula ta
                            on ad.turma_aula_id = ta.id
                        where
                            ta.turma_id = aa.turma_id
                            and ad.aluno_id = aa.aluno_id)
                         as qtde_aulas,	
            
                aa.nota_mid_term_oral,
                aa.nota_mid_term_test,
                aa.nota_mid_term_composition,
                aa.nota_final_oral,
                aa.nota_final_test,
                aa.nota_final_composition,
                aa.nota_retake_mid_term_oral,
                aa.nota_retake_mid_term_test,
                aa.nota_retake_mid_term_composition,
                aa.nota_retake_final_oral,
                aa.nota_retake_final_test,
                aa.nota_retake_final_composition
                
            from aluno_avaliacao aa
            
            inner join turma t
                on aa.turma_id = t.id
            
            inner join aluno a
                on aa.aluno_id = a.id
            
            inner join pessoa p	
                on a.pessoa_id = p.id
                
            where $where
                
            order by aa.turma_id, p.nome_contato
        SQL;

        return $this->managerRegistry->getConnection()->fetchAllAssociative($sql);
    }
}