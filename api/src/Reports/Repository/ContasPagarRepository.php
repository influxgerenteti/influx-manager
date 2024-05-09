<?php

namespace App\Reports\Repository;

use Doctrine\Persistence\ManagerRegistry;

class ContasPagarRepository
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
            select
                tituloPagar.numero_parcela_documento as numero,
                tituloPagar.valor_documento AS valor,
                date_format(tituloPagar.data_vencimento, "%d/%m/%Y") as data_vencimento,
                favorecidoPessoa.nome_contato as destino,
                formaCobranca.descricao as forma_cobranca,
                tituloPagar.observacao AS observacao,
                (
                    CASE
                        tituloPagar.situacao
                        WHEN 'PEN' THEN if (tituloPagar.data_prorrogacao <  CURDATE(), 'VENCIDO' , 'Pendente')
                        WHEN 'LIQ' THEN 'Liquidado'
                        WHEN 'BAI' THEN 'Baixado'
                        WHEN 'SUB' THEN 'Substituido'
                        WHEN 'DEV' THEN 'Cheque Devolvido'
                        ELSE (tituloPagar.situacao)
                    END
                ) AS situacao,
                movimentoConta.valor_lancamento AS valor_movimento,
                date_format(movimentoConta.data_contabil, "%d/%m/%Y") as data_movimento
            from
                titulo_pagar tituloPagar
            join franqueada ON franqueada.id = tituloPagar.franqueada_id
            join pessoa favorecidoPessoa ON favorecidoPessoa.id = tituloPagar.favorecido_pessoa_id
            join conta_pagar contaPagar ON contaPagar.id = tituloPagar.conta_pagar_id
            join conta ON conta.id = tituloPagar.conta_id
            join forma_pagamento formaCobranca ON formaCobranca.id = tituloPagar.forma_cobranca_id 
            left join movimento_conta movimentoConta ON movimentoConta.titulo_pagar_id = tituloPagar.id
            left join forma_pagamento formaPagamento ON formaPagamento.id = movimentoConta.forma_pagamento_id
            left join plano_contas_conta_pagar planoContaContaPagar ON planoContaContaPagar.conta_pagar_id = contaPagar.id
            LEFT JOIN plano_conta planoConta ON planoConta.id = planoContaContaPagar.plano_conta_id
            where $where
        SQL;

        return $this->managerRegistry->getConnection()->fetchAllAssociative($sql);
    }

    
    public function gerarDadosRelatorioFluxoCaixa($parametros = null) {

        $wherePagar = ' AND titulo_pagar.franqueada_id = ' . $parametros['franqueada'];
        $whereReceber = ' AND titulo_receber.franqueada_id = ' . $parametros['franqueada'];

        if(isset($parametros['situacao']) && !empty($parametros['situacao'])) {
            $wherePagar .= ' AND titulo_pagar.situacao IN ("' . implode('", "', explode(',', $parametros['situacao'])) . '") ';
            $whereReceber .= ' AND titulo_receber.situacao IN ("' . implode('","', explode(',', $parametros['situacao'])) . '") ';
        }
        if(isset($parametros['forma_pagamento'])) {
            $wherePagar .= ' AND titulo_pagar.forma_cobranca_id = ' . $parametros['forma_pagamento'];
            $whereReceber .= ' AND titulo_receber.forma_recebimento_id = ' . $parametros['forma_pagamento'];
        }
        if(isset($parametros['contato'])) {
            $pessoa = ' AND pessoa.id = ' . $parametros['contato'];
            $wherePagar .= $pessoa;
            $whereReceber .= $pessoa;
        }
        if(isset($parametros['conta'])) {
            $conta = ' AND conta.id = ' . $parametros['conta'];
            $wherePagar .= $conta;
            $whereReceber .= $conta;
        }
        $dataInicial = date('Y-m-d', strtotime(isset($parametros['data_inicial_vencimento']) ? str_replace('/', '-', $parametros['data_inicial_vencimento']) : "today"));
        $dataFinal = date('Y-m-d', strtotime(isset($parametros['data_final_vencimento']) ? str_replace('/', '-', $parametros['data_final_vencimento']) : "today"));
        $wherePagar .= ' AND titulo_pagar.data_vencimento >= "' . $dataInicial . ' 00:00:00" AND titulo_pagar.data_vencimento <= "' . $dataFinal . ' 23:59:00" ';
        $whereReceber .= ' AND titulo_receber.data_vencimento >= "' . $dataInicial . ' 00:00:00" AND titulo_receber.data_vencimento <= "' . $dataFinal . ' 23:59:00" ';

        $sql = '
            SELECT
                *
            FROM (
                SELECT
                    "s" as tipo,
                    pessoa.nome_contato as contato,
                    titulo_pagar.valor_documento as total,
                    titulo_pagar.valor_saldo as saldo,
                    titulo_pagar.situacao,
                    titulo_pagar.data_vencimento as vencimento,
                    conta.descricao as conta,
                    titulo_pagar.narrativa_plano_conta as narrativa_plano_conta,
                    forma_pagamento.descricao as forma_pagamento
                FROM
                    titulo_pagar
                LEFT JOIN
                    conta_pagar ON titulo_pagar.conta_pagar_id = conta_pagar.id
                LEFT JOIN
                    conta ON conta.id = titulo_pagar.conta_id
                LEFT JOIN
                    pessoa ON titulo_pagar.favorecido_pessoa_id = pessoa.id
                LEFT JOIN
                    forma_pagamento ON titulo_pagar.forma_cobranca_id = forma_pagamento.id
                WHERE
                    titulo_pagar.excluido = 0
                    ' . $wherePagar . '
                UNION ALL
                SELECT
                    "e" as tipo,
                    pessoa.nome_contato as contato,
                    titulo_receber.valor_item as total,
                    titulo_receber.valor_saldo_devedor as saldo,
                    titulo_receber.situacao as situacao,
                    titulo_receber.data_vencimento as vencimento,
                    conta.descricao as conta,
                    titulo_receber.observacao as narrativa_plano_conta,
                    forma_pagamento.descricao as forma_pagamento
                FROM
                    titulo_receber
                LEFT JOIN
                    conta_receber ON titulo_receber.conta_receber_id = conta_receber.id
                LEFT JOIN
                    conta ON conta.id = titulo_receber.conta_id
                LEFT JOIN aluno
                    ON aluno.id = titulo_receber.aluno_id
                LEFT JOIN
                    pessoa ON aluno.pessoa_id = pessoa.id
                LEFT JOIN
                    forma_pagamento ON titulo_receber.forma_recebimento_id = forma_pagamento.id
                WHERE
                    titulo_receber.id > 0
                    ' . $whereReceber . '
            ) as fluxo
            ORDER BY vencimento ASC;
        ';

        return $this->managerRegistry->getConnection()->fetchAllAssociative($sql);
    }
}