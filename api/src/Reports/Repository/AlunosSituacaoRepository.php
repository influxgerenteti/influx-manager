<?php

namespace App\Reports\Repository;

use Doctrine\Persistence\ManagerRegistry;

class AlunosSituacaoRepository
{
    /**
     * @var ManagerRegistry
     */
    private ManagerRegistry $managerRegistry;

    public function __construct(ManagerRegistry $managerRegistry)
    {
        $this->managerRegistry = $managerRegistry;
    }

    public function get($condition)
    {
        $sql = <<<SQL
            SELECT id, 
                CASE
                    WHEN situacao_atual = 'ATI' THEN 
                        "Ativo"
                    WHEN situacao_atual = 'BOL' THEN 
                        "Bolsista"
                    WHEN situacao_atual = 'REN' THEN 
                        "À Renovar"
                    WHEN situacao_atual = 'INA' THEN 
                        "Inativo"
                    WHEN situacao_atual = 'INT' THEN 
                        "Interessado"
                    WHEN situacao_atual = 'LEA' THEN 
                        "Lead"
                    WHEN situacao_atual = 'FOR' THEN 
                        "Formado"
                    WHEN situacao_atual = 'TRA' THEN 
                        "Trancado"
                    WHEN situacao_atual = 'CAN' THEN 
                        "Cancelado"
                    WHEN situacao_atual = 'MUD' THEN 
                        "Mudança"
                    ELSE 
                        "---"
                END AS situacao,
                data_alteracao 
            FROM historico_situacao_aluno
            WHERE $condition
        SQL;

        return $this->managerRegistry->getConnection()->fetchAllAssociative($sql);
    }
}
