<?php

namespace App\Reports\Repository;

use Doctrine\Persistence\ManagerRegistry;

class AulasOcorridasRepository
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
                date_format(ad.data_criacao, "%d/%m/%Y") as data,
                date_format(ad.data_criacao, "%h:%m") as horario,
                case
                    when ta.finalizada = 1 then 
                        "Finalizada"
                    else
                        "Pendente"
                    end as situacao,
                f.apelido as professor,
                l.descricao as livro,
                lc.descricao as licao,
                t.descricao as turma
            
            from turma_aula ta
            
            left join aluno_diario ad
            on ad.turma_aula_id = ta.id
            
            left join funcionario f
            on ta.funcionario_id = f.id
            
            inner join turma t
            on ta.turma_id = t.id 
            
            inner join curso c
            on t.curso_id = c.id
            
            inner join livro l
            on t.livro_id = l.id
            
            inner join licao lc
            on ta.licao_id = lc.id
            
            where $where
        SQL;

        return $this->managerRegistry->getConnection()->fetchAllAssociative($sql);
    }
}