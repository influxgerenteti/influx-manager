<?php

namespace App\Reports\Repository;

use Doctrine\Persistence\ManagerRegistry;

class ChequesSinteticoRepository
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
            SELECT
                date_format(cheque.data_bom_para, "%d/%m/%Y") as data_bom_para,
                date_format(cheque.data_entrada, "%d/%m/%Y") as data_entrada,
                date_format(cheque.data_baixa, "%d/%m/%Y") as data_baixa,
                date_format(cheque.data_devolucao, "%d/%m/%Y") as data_devolucao,
                date_format(cheque.data_segunda_devolucao, "%d/%m/%Y") as data_segunda_devolucao,
                replace(replace(format(cheque.valor, 2), ',', ''), '.', ',') as valor,
                cheque.numero,
                cheque.titular,
                (
                    CASE LENGTH(pessoa.cnpj_cpf)
                        WHEN 14 THEN CONCAT(
                            LEFT(pessoa.cnpj_cpf, 2),
                            '.',
                            MID(pessoa.cnpj_cpf, 3, 3),
                            '.',
                            MID(pessoa.cnpj_cpf, 6, 3),
                            '/',
                            MID(pessoa.cnpj_cpf, 9, 4),
                            '-',
                            RIGHT(pessoa.cnpj_cpf, 2)
                        )
                        ELSE CONCAT(
                            LEFT(pessoa.cnpj_cpf, 3),
                            '.',
                            MID(pessoa.cnpj_cpf, 4, 3),
                            '.',
                            MID(pessoa.cnpj_cpf, 7, 3),
                            '-',
                            RIGHT(pessoa.cnpj_cpf, 2)
                        )
                    END
                ) AS cnpj_cpf,
                concat(cheque.conta, '/', cheque.agencia, '/', cheque.banco) AS conta_agencia_banco,
                (
                    CASE cheque.situacao
                        WHEN 'B' THEN 'Baixado'
                        WHEN 'D' THEN 'Devolvido'
                        WHEN 'C' THEN 'Cancelado'
                        ELSE 'Pendente'
                    END
                ) AS situacao,
                (
                    CASE cheque.tipo
                        WHEN 'P' THEN 'Pagar'
                        ELSE 'Receber'
                    END
                ) AS tipo,
                (IF(contaPagar.descricao IS NOT NULL, contaPagar.descricao, contaReceber.descricao)) AS conta,
                contaMovimento.descricao AS conta_creditada
            from cheque
            join franqueada on cheque.franqueada_id = franqueada.id
            join pessoa on cheque.pessoa_id = pessoa.id
            LEFT JOIN titulo_receber ON titulo_receber.id = cheque.titulo_receber_id
            LEFT JOIN conta contaReceber ON contaReceber.id = titulo_receber.conta_id
            LEFT JOIN titulo_pagar ON titulo_pagar.id = cheque.titulo_pagar_id
            LEFT JOIN conta contaPagar ON contaPagar.id = titulo_pagar.conta_id
            LEFT JOIN movimento_conta ON (movimento_conta.titulo_receber_id = titulo_receber.id OR movimento_conta.titulo_pagar_id = titulo_pagar.id)
            LEFT JOIN conta contaMovimento ON contaMovimento.id = movimento_conta.conta_id
            LEFT JOIN motivo_devolucao_cheque ON motivo_devolucao_cheque.id = cheque.motivo_devolucao_cheque_id
            where ($where)
        SQL;

        return $this->managerRegistry->getConnection()->fetchAllAssociative($sql);
    }
}