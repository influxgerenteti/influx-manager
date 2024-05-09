<?php

namespace App\Reports\Repository;

use Doctrine\Persistence\ManagerRegistry;

class SaldoHorasPersonalRepository
{
    /**
     * @var ManagerRegistry
     */
    private ManagerRegistry $managerRegistry;

    public function __construct(ManagerRegistry $managerRegistry)
    {
        $this->managerRegistry = $managerRegistry;
    }

    public function get($alunoId, $livroId, $dataContratoDe, $dataContratoAte)
    {
        $sql = <<<SQL
            select
                pessoa.nome_contato as aluno,
                livro.descricao as livro,
                date_format(contrato.data_contrato, "%d/%m/%Y") as data_contrato,
                creditos_personal.quantidade as qtdade_horas,
                creditos_personal.saldo as saldo_horas
            from creditos_personal
            inner join contrato 
                on creditos_personal.contrato_id = contrato.id
            inner join aluno
                on contrato.aluno_id = aluno.id
            inner join pessoa
                on aluno.pessoa_id = pessoa.id
            inner join livro
                on contrato.livro_id = livro.id	
            
            where
                contrato.franqueada_id = 1
                and (aluno.id = $alunoId or $alunoId = '')
                and (livro.id = $livroId or $livroId = '')
                and (contrato.data_contrato >= $dataContratoDe or $dataContratoDe = '')
                and (contrato.data_contrato <= $dataContratoAte or $dataContratoAte = '')
        SQL;

        return $this->managerRegistry->getConnection()->fetchAllAssociative($sql);
    }
}