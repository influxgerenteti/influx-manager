<?php

namespace App\Reports\Repository;

use Doctrine\Persistence\ManagerRegistry;

class HistAlun4ContratoRepository
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
                concat(aluno_id,'/',contrato.id) as numero,
                date_format(data_inicio_contrato,'%d/%m/%Y') as data_inicio_contrato,
                date_format(data_termino_contrato,'%d/%m/%Y') as data_termino_contrato,
                pessoa.nome_contato as contratante
            from contrato 
            inner join pessoa
                on contrato.responsavel_financeiro_pessoa_id = pessoa.id
            
            where franqueada_id = $franqueadaId
                and aluno_id = $alunoId
        SQL;

        return $this->managerRegistry->getConnection()->fetchAllAssociative($sql);
    }
}