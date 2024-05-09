<?php

namespace App\Reports\Repository;

use Doctrine\Persistence\ManagerRegistry;

class MatriculasPerdidasRepository
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
            select  date_format(data_matricula_perdida, "%d/%m/%Y") as data_contato,
                    nome,
                    interessado.telefone_contato,
                    interessado.email_contato,
                    idade,
                    concat(pessoa.nome_contato,' (', funcionario.apelido, ')') as responsavel_ultimo_contato,
                    curso.descricao as curso
                    
            from interessado
            inner join funcionario
                on interessado.consultor_funcionario_id = funcionario.id
            inner join pessoa
                on funcionario.pessoa_id = pessoa.id
            left join curso
                on interessado.curso_id = curso.id
            where $where
        SQL;

        return $this->managerRegistry->getConnection()->fetchAllAssociative($sql);
    }
}