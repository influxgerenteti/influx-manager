<?php

namespace App\Reports\Repository;

use Doctrine\Persistence\ManagerRegistry;

class HistAlun5TurmasRepository
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
                    turma.descricao as turma,
                    case contrato.tipo_contrato
                        when 'M' then
                            "Matrícula"
                        when 'R' then
                            "Rematrícula"
                        else
                            "Tranferência de unidade"
                    end	as tipo,
                    date_format(contrato.data_matricula,'%d/%m/%Y') as data_matricula,
                    pessoa.nome_contato as professor,
                    (select licao.descricao 
                        from turma_aula 
                        inner join licao 
                            on turma_aula.licao_id = licao.id 
                        where turma_id = turma.id and finalizada = 1 
                        order by data_aula desc 
                        limit 1
                    ) as ultima_licao,
                    livro.descricao as estagio,
                    (select count(ad.presenca)
                        from aluno_diario ad
                        inner join turma_aula ta
                            on ad.turma_aula_id = ta.id
                        where 
                            ad.franqueada_id = contrato.franqueada_id
                            and ta.turma_id = turma.id
                            and ad.aluno_id = contrato.aluno_id
                    ) as aulas_dadas,
                    (select count(ad.presenca)
                        from aluno_diario ad
                        inner join turma_aula ta
                            on ad.turma_aula_id = ta.id
                        where 
                            ad.franqueada_id = contrato.franqueada_id
                            and ta.turma_id = turma.id
                            and ad.aluno_id = contrato.aluno_id
                            and ad.presenca = 'P'
                    ) as aulas_assistidas,
                    (select count(ad.presenca)
                        from aluno_diario ad
                        inner join turma_aula ta
                            on ad.turma_aula_id = ta.id
                        where 
                            ad.franqueada_id = contrato.franqueada_id
                            and ta.turma_id = turma.id
                            and ad.aluno_id = contrato.aluno_id
                            and ad.presenca = 'F'
                    ) as faltas,
                    (select count(ad.presenca)
                        from aluno_diario ad
                        inner join turma_aula ta
                            on ad.turma_aula_id = ta.id
                        where 
                            ad.franqueada_id = contrato.franqueada_id
                            and ta.turma_id = turma.id
                            and ad.aluno_id = contrato.aluno_id
                            and ad.atividade_ca = 'NE'
                    ) as CA_nao_entregue,
                    (select count(ad.presenca)
                        from aluno_diario ad
                        inner join turma_aula ta
                            on ad.turma_aula_id = ta.id
                        where 
                            ad.franqueada_id = contrato.franqueada_id
                            and ta.turma_id = turma.id
                            and ad.aluno_id = contrato.aluno_id
                            and ad.atividade_ce = 'NE'
                    ) as CE_nao_entregue,
                    (select count(ad.presenca)
                        from aluno_diario ad
                        inner join turma_aula ta
                            on ad.turma_aula_id = ta.id
                        where 
                            ad.franqueada_id = contrato.franqueada_id
                            and ta.turma_id = turma.id
                            and ad.aluno_id = contrato.aluno_id
                            and ad.reposicao_aula = 1
                    ) as reposicoes
            
            from contrato
            
            left join turma
                on turma.id = contrato.turma_id
            left join funcionario
                on turma.funcionario_id = funcionario.id
            left join pessoa
                on funcionario.pessoa_id = pessoa.id
            inner join livro
                on turma.livro_id = livro.id	
            
            where contrato.franqueada_id = $franqueadaId
                and contrato.aluno_id = $alunoId
        SQL;

        return $this->managerRegistry->getConnection()->fetchAllAssociative($sql);
    }
}