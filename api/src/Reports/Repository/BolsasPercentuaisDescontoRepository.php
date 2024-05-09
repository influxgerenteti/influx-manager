<?php

namespace App\Reports\Repository;

use Doctrine\Persistence\ManagerRegistry;

class BolsasPercentuaisDescontoRepository
{

    /**
     * @var ManagerRegistry
     */
    private ManagerRegistry $managerRegistry;

    public function __construct(ManagerRegistry $managerRegistry)
    {
        $this->managerRegistry = $managerRegistry;
    }

    public function get($franqueadaId, $semestreId, $situacaoAluno, $formaPagamentoId)
    {
        $sql = <<<SQL
            select
                    pessoa.nome_contato,
                    case contrato.bolsista
                        when 0 then '---'
                        else 'Bolsista'
                    end as bolsista,	
                    case 
                    WHEN aluno.situacao = 'ATI' THEN 
                        "Ativo"
                    WHEN aluno.situacao = 'BOL' THEN 
                        "Bolsista"
                    WHEN aluno.situacao = 'REN' THEN 
                        "À Renovar"
                    WHEN aluno.situacao = 'INA' THEN 
                        "Inativo"
                    WHEN aluno.situacao = 'INT' THEN 
                        "Interessado"
                    WHEN aluno.situacao = 'LEA' THEN 
                        "Lead"
                    WHEN aluno.situacao = 'FOR' THEN 
                        "Formado"
                    WHEN aluno.situacao = 'TRA' THEN 
                        "Trancado"
                    WHEN aluno.situacao = 'CAN' THEN 
                        "Cancelado"
                    WHEN aluno.situacao = 'MUD' THEN 
                        "Mudança"
                    ELSE 
                        "---"
                END AS situacao, 
            
                    item_conta_receber.valor + item_conta_receber.valor_desconto as valor_original,
                    CONCAT(replace(FORMAT(item_conta_receber.percentual_desconto,2),'.',','),' %') as percentual_desconto,
                    item_conta_receber.valor_desconto as desconto,
                    item_conta_receber.valor as valor_pago
                    
            from item_conta_receber
            inner join conta_receber
                on item_conta_receber.conta_receber_id = conta_receber.id
            left join contrato
                on conta_receber.contrato_id = contrato.id
            left join turma	
                on contrato.turma_id = turma.id
            inner join aluno
                on contrato.aluno_id = aluno.id
            inner join pessoa
                on aluno.pessoa_id = pessoa.id
                
            inner join titulo_receber
                on titulo_receber.conta_receber_id = conta_receber.id
            inner join forma_pagamento
                on titulo_receber.forma_recebimento_id = forma_pagamento.id	
            
            where item_conta_receber.percentual_desconto > 0
             and conta_receber.franqueada_id = $franqueadaId
             and turma.semestre_id = $semestreId 
             and aluno.situacao = $situacaoAluno
             and (titulo_receber.forma_recebimento_id = $formaPagamentoId or $formaPagamentoId = '')
            
            order by item_conta_receber.percentual_desconto, pessoa.nome_contato, contrato.id
        SQL;

        return $this->managerRegistry->getConnection()->fetchAllAssociative($sql);
    }
}