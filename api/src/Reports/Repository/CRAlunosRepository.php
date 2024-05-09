<?php

namespace App\Reports\Repository;

use Doctrine\Persistence\ManagerRegistry;

class CRAlunosRepository
{
    /**
     * @var ManagerRegistry
     */
    private ManagerRegistry $managerRegistry;

    public function __construct(ManagerRegistry $managerRegistry)
    {
        $this->managerRegistry = $managerRegistry;
    }

    public function get($franqueadaId, $turmaId)
    {
        $sql = <<<SQL
            select pessoa.nome_contato as nome_aluno 
            from contrato
            inner join aluno 
                on contrato.aluno_id = aluno.id
            inner join pessoa
                on aluno.pessoa_id = pessoa.id
            where contrato.turma_id = $turmaId
            and contrato.franqueada_id = $franqueadaId
        SQL;

        return $this->managerRegistry->getConnection()->fetchAllAssociative($sql);
    }
}