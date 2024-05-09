<?php

namespace App\Reports\Repository;

use Doctrine\Persistence\ManagerRegistry;

class QuantidadeAlunosTurmaRepository
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
                contrato.franqueada_id,
                turma.id as turma_id,
                turma.descricao as turma_descricao,
                livro.descricao as livro_descricao,
                horario.descricao as horario_descricao,
                sala.descricao as sala_descricao,
                pessoa.nome_contato as professor,
                count(contrato.id) as qtd
            from contrato
            inner join turma
                on contrato.turma_id = turma.id
            inner join livro
                on turma.livro_id = livro.id
            inner join horario
                on turma.horario_id = horario.id
            left join sala_franqueada
                on turma.sala_franqueada_id = sala_franqueada.id
            left join sala
                on sala_franqueada.sala_id = sala.id
            left join funcionario
                on turma.funcionario_id = funcionario.id
            left join pessoa
                on funcionario.pessoa_id = pessoa.id
            
            where $where
            
            group by
                 franqueada_id,
                 turma_id,
                 turma_descricao,
                 livro_descricao,
                 horario_descricao,
                 sala_descricao,
                 professor
        SQL;

        return $this->managerRegistry->getConnection()->fetchAllAssociative($sql);
    }
}