<?php

namespace App\Reports\Repository;

use Doctrine\Persistence\ManagerRegistry;

class ClassRecordCronogramaRepository
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
            select	turma.descricao as turma,
                    semestre.descricao as semestre,
                    turma_aula.data_aula, 
                    licao.numero as numero_licao, 
                    licao.descricao as descricao_licao, 
                    sala.descricao as sala,
                    pessoa.nome_contato as professor,
                    horario.descricao as horario,
                    livro.descricao as estagio,
                    curso.descricao as curso,
                    turma.data_inicio,
                    turma.data_fim
                    
            from turma 
            
            inner join turma_aula
                on turma.id = turma_aula.turma_id
            inner join licao
                on turma_aula.licao_id = licao.id
            
            inner join horario
                on turma.horario_id = horario.id
            
            left join sala_franqueada
                on turma.sala_franqueada_id = sala_franqueada.id
            inner join sala
                on sala_franqueada.sala_id = sala.id
            
            inner join semestre
                on turma.semestre_id = semestre.id
            
            inner join livro
                on turma.livro_id = livro.id	
            
            left join funcionario
                on turma.funcionario_id = funcionario.id
            inner join pessoa
                on funcionario.pessoa_id = pessoa.id
                
            inner join curso
                on turma.curso_id = curso.id
                
            where turma.id = $turmaId
            and turma.franqueada_id = $franqueadaId
        SQL;

        return $this->managerRegistry->getConnection()->fetchAllAssociative($sql);
    }


}
