<?php

namespace App\Reports\Repository;

use Doctrine\Persistence\ManagerRegistry;

class HistAlun7FollowupRepository
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
                    date_format(followup_comercial.data_registro,'%d/%m/%Y') as data,
                    pessoa.nome_contato as funcionario,
                    '' as agendamento,
                    followup_comercial.followup as assunto,
                    '' as resolvido
                    
            from followup_comercial
            inner join interessado
                on followup_comercial.interessado_id = interessado.id
            inner join funcionario
                on followup_comercial.consultor_funcionario_id = funcionario.id
            left join pessoa
                on funcionario.pessoa_id = pessoa.id	
            
            where interessado.franqueada_id = $franqueadaId
            and interessado.aluno_id = $alunoId
        SQL;

        return $this->managerRegistry->getConnection()->fetchAllAssociative($sql);
    }
}