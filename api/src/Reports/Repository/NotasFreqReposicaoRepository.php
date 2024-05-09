<?php

namespace App\Reports\Repository;

use Doctrine\Persistence\ManagerRegistry;

class NotasFreqReposicaoRepository
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
            select 	ra.turma_id,
                    ra.aluno_id,
                    lv.descricao as livro, 
                    lc.descricao as licao,
                    i.descricao as item,
                    date_format(ra.data_hora_inicio,"%d/%m/%Y %h:%m") as data_hora_inicio,
                    date_format(ra.data_hora_fim,"%d/%m/%Y %h:%m") as data_hora_fim,
                    case 
                        when ra.presenca = 'P' then 
                            "Presente"
                        else 
                            "Falta"
                    end as presenca,
                    case 
                        when ra.situacao = 'P' then 
                            "Pendente"
                        when ra.situacao = 'C' then 
                            "Concluída"
                        else 
                            "Cancelada"
                    end as situacao,
                    ra.nota_mid_term_oral,
                    ra.nota_mid_term_test,
                    ra.nota_mid_term_composition,
                    ra.nota_final_oral,
                    ra.nota_final_test,
                    ra.nota_final_composition,
                    ra.nota_retake_mid_term_oral,
                    ra.nota_retake_mid_term_test,
                    ra.nota_retake_mid_term_composition,
                    ra.nota_retake_final_oral,
                    ra.nota_retake_final_test,
                    ra.nota_retake_final_composition
                    
            from reposicao_aula ra
            
            inner join turma t
                on ra.turma_id = t.id
            
            inner join livro lv
                on t.livro_id = lv.id
            
            inner join licao lc
                on ra.licao_id = lc.id 	
                
            inner join item i
                on ra.item_id = i.id	
            
            
            where 
                ra.franqueada_id = $franqueadaId
                and ra.turma_id = $turmaId
                and ra.aluno_id = $alunoId
                
            order by ra.data_hora_inicio
        SQL;

        return $this->managerRegistry->getConnection()->fetchAllAssociative($sql);
    }
}