<?php

namespace App\Reports\Repository;

use Doctrine\Persistence\ManagerRegistry;

class HistAlun6AtvExtraRepository
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
                date_format(atividade_extra.data_hora_inicio,  "%d/%m/%Y") as data,
                date_format(atividade_extra.data_hora_inicio,  "%h:%i") as inicio,
                date_format(atividade_extra.data_hora_fim,     "%h:%i") as fim,
                pessoa.nome_contato as funcionario,
                atividade_extra.descricao_atividade as tipo_atividade,
                case aluno_atividade_extra.presenca 
                    when 'P' then
                        "Presente"
                    when 'F' then
                        "Faltante"
                    else
                        "Reposição"
                end	as presenca
            
            from aluno_atividade_extra
            inner join atividade_extra
                on aluno_atividade_extra.atividade_extra_id = atividade_extra.id
            inner join atividade_extra_funcionario
                on aluno_atividade_extra.atividade_extra_id = atividade_extra_funcionario.atividade_extra_id
            inner join funcionario
                on atividade_extra_funcionario.funcionario_id = funcionario.id
            inner join pessoa
                on funcionario.pessoa_id = pessoa.id
            
            where atividade_extra.franqueada_id = $franqueadaId  
                and aluno_atividade_extra.aluno_id = $alunoId
        SQL;

        return $this->managerRegistry->getConnection()->fetchAllAssociative($sql);
    }
}