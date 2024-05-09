<?php

namespace App\Reports\Repository;

use Doctrine\Persistence\ManagerRegistry;

class TurmasExistentesRepository
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
                turma.descricao as turma,
                pessoa.nome_contato as professor,
                horario.descricao as horario,
                livro.descricao as livro,
                sala.descricao as sala,
                date_format(turma.data_inicio, "%d/%m/%Y") as data_inicio,
                date_format(turma.data_fim, "%d/%m/%Y") as data_fim
            
            from turma
            inner join curso
                on turma.curso_id = curso.id
            inner join idioma
                on curso.idioma_id = idioma.id	
            inner join livro
                on turma.livro_id = livro.id
            inner join modalidade_turma
                on turma.modalidade_turma_id = modalidade_turma.id
            inner join horario
                on turma.horario_id = horario.id
            left join funcionario
                on turma.funcionario_id = funcionario.id
            left join pessoa
                on funcionario.pessoa_id = pessoa.id
            left join sala_franqueada
                on turma.sala_franqueada_id = sala_franqueada.id
            left join sala
                on sala_franqueada.sala_id = sala.id
            
            where $where
        SQL;

        return $this->managerRegistry->getConnection()->fetchAllAssociative($sql);
    }
}