<?php

namespace App\Reports\Repository;

use Doctrine\Persistence\ManagerRegistry;

class MediasTurmasResumidoRepository
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
                aa.turma_id, 
                t.descricao,
                    ((select COUNT(ad.presenca) 
                    from aluno_diario ad
                    inner join turma_aula ta
                        on ad.turma_aula_id = ta.id
                    where
                        ta.turma_id = aa.turma_id
                        and ad.presenca = 'P')
                    /
                 (select COUNT(ad.presenca) 
                    from aluno_diario ad
                    inner join turma_aula ta
                        on ad.turma_aula_id = ta.id
                    where
                        ta.turma_id = aa.turma_id)
                )*100 as frequencia,
                aa.nota_mid_term_oral, 
                aa.nota_mid_term_escrita,
                aa.nota_mid_term_test, 
                aa.nota_mid_term_composition, 
                aa.nota_final_oral, 
                aa.nota_final_escrita, 
                aa.nota_final_test, 
                aa.nota_final_composition, 
                aa.nota_retake_mid_term_oral, 
                aa.nota_retake_mid_term_escrita, 
                aa.nota_retake_mid_term_test, 
                aa.nota_retake_mid_term_composition, 
                aa.nota_retake_final_oral, 
                aa.nota_retake_final_escrita, 
                aa.nota_retake_final_test, 
                aa.nota_retake_final_composition
                
            from aluno_avaliacao aa
            inner join turma t 
                on aa.turma_id = t.id
            
            where aa.franqueada_id = $franqueadaId
                and t.intensidade = 'R'
                
            order by aa.turma_id
        SQL;

        return $this->managerRegistry->getConnection()->fetchAllAssociative($sql);
    }
}