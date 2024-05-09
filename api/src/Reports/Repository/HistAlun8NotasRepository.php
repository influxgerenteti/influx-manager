<?php

namespace App\Reports\Repository;

use Doctrine\Persistence\ManagerRegistry;

class HistAlun8NotasRepository
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
                aa.turma_id,
                l.descricao as estagio,
                t.descricao as turma,
                (select descricao from conceito_avaliacao where id = aa.nota_mid_term_oral_id) as nota_mid_term_oral, 
                aa.nota_mid_term_escrita,
                aa.nota_mid_term_test, 
                aa.nota_mid_term_composition, 
                (select descricao from conceito_avaliacao where id = aa.nota_final_oral_id) as nota_final_oral,
                aa.nota_final_escrita, 
                aa.nota_final_test, 
                aa.nota_final_composition, 
                (select descricao from conceito_avaliacao where id = aa.nota_retake_mid_term_oral_id) as nota_retake_mid_term_oral,
                aa.nota_retake_mid_term_escrita, 
                (select descricao from conceito_avaliacao where id = aa.nota_retake_final_oral_id) as nota_retake_final_oral,
                aa.nota_retake_final_escrita,
                (aa.nota_mid_term_escrita+aa.nota_final_escrita+
                aa.nota_retake_mid_term_escrita+aa.nota_retake_final_escrita)/4 as media_WG
            from aluno_avaliacao aa
            inner join turma t 
                on aa.turma_id = t.id
            inner join livro l 
                on aa.livro_id = l.id
            where 
                aa.franqueada_id = $franqueadaId
                and aa.aluno_id = $alunoId
                -- and t.intensidade = 'R'
            order by aa.turma_id
        SQL;

        return $this->managerRegistry->getConnection()->fetchAllAssociative($sql);
    }
}