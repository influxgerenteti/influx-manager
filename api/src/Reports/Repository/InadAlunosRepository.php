<?php

namespace App\Reports\Repository;

use Doctrine\Persistence\ManagerRegistry;

class InadAlunosRepository
{

    /**
     * @var ManagerRegistry
     */
    private ManagerRegistry $managerRegistry;

    public function __construct(ManagerRegistry $managerRegistry)
    {
        $this->managerRegistry = $managerRegistry;
    }

    public function get($tipoOcorrencia, $alunoId)
    {
        $sql = <<<SQL
            select 
                tipo_ocorrencia.descricao as tipo_agendamento, 
                CASE
                    WHEN ocorrencia_academica.situacao = 'A' THEN 
                        "Aberto"
                    WHEN ocorrencia_academica.situacao = 'F' THEN 
                        "Fechado"
                    ELSE 
                        "---"
                END AS situacao,
                date_format(ocorrencia_academica_detalhes.data_criacao, "%d/%m/%Y") as data,
                ocorrencia_academica_detalhes.texto as assunto,
                pessoa.nome_contato as atendente,
                CASE
                    WHEN interessado.tipo_contato_id is not null then
                        (select nome from tipo_contato where tipo_contato.id = interessado.tipo_contato_id)
                    WHEN interessado.tipo_prospeccao_id is not null then
                        (select descricao from tipo_prospeccao where tipo_prospeccao.id = interessado.tipo_prospeccao_id)	    	
                    ELSE ""
                end as tipo_contato    		
            
            from ocorrencia_academica
            inner join tipo_ocorrencia
                on ocorrencia_academica.tipo_ocorrencia_id = tipo_ocorrencia.id
            left join ocorrencia_academica_detalhes
                on ocorrencia_academica.id = ocorrencia_academica_detalhes.ocorrencia_academica_id
            inner join funcionario
                on ocorrencia_academica.funcionario_id = funcionario.id
            inner join pessoa
                on funcionario.pessoa_id = pessoa.id
            left join interessado
                on interessado.aluno_id = ocorrencia_academica.aluno_id
            
            where ocorrencia_academica.aluno_id = $alunoId
            and ocorrencia_academica.tipo_ocorrencia_id = $tipoOcorrencia
            
            order by ocorrencia_academica_detalhes.data_criacao desc
            limit 1
        SQL;

        return $this->managerRegistry->getConnection()->fetchAllAssociative($sql);
    }
}