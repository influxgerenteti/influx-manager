<?php

namespace App\Reports\Repository;

use Doctrine\Persistence\ManagerRegistry;

class MatriculasRenovarRepository
{
    /**
     * @var ManagerRegistry
     */
    private ManagerRegistry $managerRegistry;

    public function __construct(ManagerRegistry $managerRegistry)
    {
        $this->managerRegistry = $managerRegistry;
    }

    public function get($franqueadaId, $modalidadeTurma, $dataInicial, $dataFinal)
    {
        $sql = <<<SQL
            select 
                concat(aluno_id,'/',sequencia_contrato) as contrato,
                pessoa.nome_contato as aluno,
                coalesce(case length(REPLACE(pessoa.telefone_preferencial,' ',''))
                                when 11 then CONCAT(
                                    LEFT(REPLACE(pessoa.telefone_preferencial,' ',''), 2),
                                    ' ',
                                    MID(REPLACE(pessoa.telefone_preferencial,' ',''), 3, 5),
                                    '-',
                                    RIGHT(REPLACE(pessoa.telefone_preferencial,' ',''), 4)
                                )
                                when 10 then CONCAT(
                                    LEFT(REPLACE(pessoa.telefone_preferencial,' ',''), 2),
                                    ' ',
                                    MID(REPLACE(pessoa.telefone_preferencial,' ',''), 3, 4),
                                    '-',
                                    RIGHT(REPLACE(pessoa.telefone_preferencial,' ',''), 4)
                                )
                                else
                                    pessoa.telefone_preferencial
                                end,
                            case length(REPLACE(pessoa.telefone_contato,' ',''))
                                when 11 then CONCAT(
                                    LEFT(REPLACE(pessoa.telefone_contato,' ',''), 2),
                                    ' ',
                                    MID(REPLACE(pessoa.telefone_contato,' ',''), 3, 5),
                                    '-',
                                    RIGHT(REPLACE(pessoa.telefone_contato,' ',''), 4)
                                )
                                when 10 then CONCAT(
                                    LEFT(REPLACE(pessoa.telefone_contato,' ',''), 2),
                                    ' ',
                                    MID(REPLACE(pessoa.telefone_contato,' ',''), 3, 4),
                                    '-',
                                    RIGHT(REPLACE(pessoa.telefone_contato,' ',''), 4)
                                )
                                else
                                    pessoa.telefone_contato
                                end) as fone,
                turma.descricao as turma,
                DATE_FORMAT(data_contrato, '%d/%m/%Y') as data_contrato,
                DATE_FORMAT(data_inicio_contrato, '%d/%m/%Y') as data_inicio_contrato,
                DATE_FORMAT(data_termino_contrato, '%d/%m/%Y') as data_termino_contrato,
                case (select 1 from contrato c2 
                        where c2.aluno_id = contrato.aluno_id 
                            and c2.curso_id = contrato.curso_id 
                            and c2.data_termino_contrato > $dataFinal
                    )
                    when 1 
                    then 1
                    else 0
                end	as renovada
            from contrato
            inner join aluno
                on contrato.aluno_id = aluno.id
            inner join pessoa
                on aluno.pessoa_id = pessoa.id
            left join turma
                on contrato.turma_id = turma.id
            where 
                contrato.franqueada_id = $franqueadaId
            and	data_termino_contrato >= $dataInicial
            and data_termino_contrato <= $dataFinal
            and ( turma.modalidade_turma_id = $modalidadeTurma 
                    or
                    $modalidadeTurma = ''
                    )
        SQL;

        return $this->managerRegistry->getConnection()->fetchAllAssociative($sql);
    }
}