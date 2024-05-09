<?php

namespace App\Reports\Repository;

use Doctrine\Persistence\ManagerRegistry;

class InadimplentesValoresRepository
{

    /**
     * @var ManagerRegistry
     */
    private ManagerRegistry $managerRegistry;

    public function __construct(ManagerRegistry $managerRegistry)
    {
        $this->managerRegistry = $managerRegistry;
    }

    public function get($franqueadaId)
    {
        $sql = <<<SQL
            select	distinct
                    tr.franqueada_id,
                    a.id as aluno_id, 
                    p.nome_contato as aluno, 
                    YEAR(FROM_DAYS(DATEDIFF(CURRENT_DATE,p.data_nascimento))) as idade, 
            
                    coalesce(case length(REPLACE(p.telefone_preferencial,' ',''))
                                when 11 then CONCAT(
                                    LEFT(REPLACE(p.telefone_preferencial,' ',''), 2),
                                    ' ',
                                    MID(REPLACE(p.telefone_preferencial,' ',''), 3, 5),
                                    '-',
                                    RIGHT(REPLACE(p.telefone_preferencial,' ',''), 4)
                                )
                                when 10 then CONCAT(
                                    LEFT(REPLACE(p.telefone_preferencial,' ',''), 2),
                                    ' ',
                                    MID(REPLACE(p.telefone_preferencial,' ',''), 3, 4),
                                    '-',
                                    RIGHT(REPLACE(p.telefone_preferencial,' ',''), 4)
                                )
                                else
                                    p.telefone_preferencial
                                end,
                            case length(REPLACE(p.telefone_contato,' ',''))
                                when 11 then CONCAT(
                                    LEFT(REPLACE(p.telefone_contato,' ',''), 2),
                                    ' ',
                                    MID(REPLACE(p.telefone_contato,' ',''), 3, 5),
                                    '-',
                                    RIGHT(REPLACE(p.telefone_contato,' ',''), 4)
                                )
                                when 10 then CONCAT(
                                    LEFT(REPLACE(p.telefone_contato,' ',''), 2),
                                    ' ',
                                    MID(REPLACE(p.telefone_contato,' ',''), 3, 4),
                                    '-',
                                    RIGHT(REPLACE(p.telefone_contato,' ',''), 4)
                                )
                                else
                                    p.telefone_contato
                                end) as fone, 
            
                    CASE
                        WHEN YEAR(FROM_DAYS(DATEDIFF(CURRENT_DATE,p.data_nascimento))) < 18 then
                            (select nome_contato from pessoa pai where pai.id = a.responsavel_financeiro_pessoa_id)
                        ELSE "O próprio"
                    end as nome_responsavel,
                    
                    CASE
                        WHEN YEAR(FROM_DAYS(DATEDIFF(CURRENT_DATE,p.data_nascimento))) < 18 then
                            (select 
                                case length(REPLACE(telefone_preferencial,' ',''))
                                    when 11 then CONCAT(
                                        LEFT(REPLACE(telefone_preferencial,' ',''), 2),
                                        ' ',
                                        MID(REPLACE(telefone_preferencial,' ',''), 3, 5),
                                        '-',
                                        RIGHT(REPLACE(telefone_preferencial,' ',''), 4)
                                    )
                                    when 10 then CONCAT(
                                        LEFT(REPLACE(telefone_preferencial,' ',''), 2),
                                        ' ',
                                        MID(REPLACE(telefone_preferencial,' ',''), 3, 4),
                                        '-',
                                        RIGHT(REPLACE(telefone_preferencial,' ',''), 4)
                                    )
                                    else
                                        telefone_preferencial
                                    end
                             from pessoa pai where pai.id = a.responsavel_financeiro_pessoa_id)	
                        ELSE ""
                    end as fone_responsavel,
                    CASE
                        WHEN YEAR(FROM_DAYS(DATEDIFF(CURRENT_DATE,p.data_nascimento))) < 18 then
                            (select 
                                case length(REPLACE(telefone_contato,' ',''))
                                    when 11 then CONCAT(
                                        LEFT(REPLACE(telefone_contato,' ',''), 2),
                                        ' ',
                                        MID(REPLACE(telefone_contato,' ',''), 3, 5),
                                        '-',
                                        RIGHT(REPLACE(telefone_contato,' ',''), 4)
                                    )
                                    when 10 then CONCAT(
                                        LEFT(REPLACE(telefone_contato,' ',''), 2),
                                        ' ',
                                        MID(REPLACE(telefone_contato,' ',''), 3, 4),
                                        '-',
                                        RIGHT(REPLACE(telefone_contato,' ',''), 4)
                                    )
                                    else
                                        telefone_contato
                                    end
                             from pessoa pai where pai.id = a.responsavel_financeiro_pessoa_id)
                        ELSE ""
                    end as celular_responsavel
            from titulo_receber tr
            inner join aluno a
                on tr.aluno_id = a.id
            inner join pessoa p
                on a.pessoa_id = p.id	
                
              where 
                    DATE_FORMAT(tr.data_prorrogacao, '%Y-%m-%d') < CURDATE()
                    and tr.franqueada_id = $franqueadaId
        SQL;

        return $this->managerRegistry->getConnection()->fetchAllAssociative($sql);
    }
}