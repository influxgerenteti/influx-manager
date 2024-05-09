<?php

namespace App\Reports\Repository;

use App\Helper\ConstanteParametros;
use Doctrine\Persistence\ManagerRegistry;

class InadimplentesAlunosRepository
{

    /**
     * @var ManagerRegistry
     */
    private ManagerRegistry $managerRegistry;

    public function __construct(ManagerRegistry $managerRegistry)
    {
        $this->managerRegistry = $managerRegistry;
    }

    /**
     * Método monta a query para retornar os inadimplentes de acordo com os filtros passados no parâmetro
     * É retornado um array com os resultados da consulta
     * 
     * @param array $filtros
     * @return array
     */
    public function getInadimplentes($filtros)
    {
        $andWhere = "";
        if(isset($filtros[ConstanteParametros::CHAVE_DATA_INICIO]) && isset($filtros[ConstanteParametros::CHAVE_DATA_FIM])){
            $dataInicio = date("Y-m-d", strtotime(str_replace("/","-",$filtros[ConstanteParametros::CHAVE_DATA_INICIO])))." 00:00:00";
            $dataFim = date("Y-m-d", strtotime(str_replace("/","-",$filtros[ConstanteParametros::CHAVE_DATA_FIM])))." 23:59:59";
            
            $andWhere = " AND tr.data_prorrogacao >= '{$dataInicio}' AND tr.data_prorrogacao <= '{$dataFim}'";
        }
        if(isset($filtros[ConstanteParametros::CHAVE_SITUACAO])){
            $situacao = implode("','", $filtros[ConstanteParametros::CHAVE_SITUACAO]);
            $andWhere .= " AND a.situacao IN ('".$situacao."')";
        }
        if(isset($filtros[ConstanteParametros::CHAVE_CLASSIFICACAO_ALUNO])){
            $andWhere .= " AND a.classificacao_aluno_id = ".$filtros[ConstanteParametros::CHAVE_CLASSIFICACAO_ALUNO];
        }
        if(isset($filtros[ConstanteParametros::CHAVE_FORMA_COBRANCA])){
            $andWhere .= " AND tr.forma_cobranca_id = ".$filtros[ConstanteParametros::CHAVE_FORMA_COBRANCA];
        }
        if(isset($filtros[ConstanteParametros::CHAVE_TIPO_ALUNO])){
            switch ((int) $filtros[ConstanteParametros::CHAVE_TIPO_ALUNO]) {
                case 0:
                    $andWhere .= " AND tr.data_prorrogacao < '".(new \DateTime())->format('Y-m-d H:i:s')."' or tr.negativado = 1";
                    break;
                case 1:
                    $andWhere .= " AND tr.negativado = 1";
                    break;
                case 2:
                    $andWhere .= " AND tr.data_prorrogacao < '".(new \DateTime())->format('Y-m-d H:i:s')."' and tr.negativado = 0";
                    break;
            }
        }
        
        $sql = "select	distinct
                a.id as aluno_id, 
                p.nome_contato, 
                YEAR(FROM_DAYS(DATEDIFF(CURRENT_DATE,p.data_nascimento))) as idade, 
                a.situacao,
                p.endereco,
                p.bairro_endereco as bairro,
                c.nome as cidade,
                (CASE LENGTH(p.cep_endereco)
                    WHEN 8 THEN CONCAT(
                        LEFT(p.cep_endereco, 2),
                        '.',
                        MID(p.cep_endereco, 3, 3),
                        '-',
                        RIGHT(p.cep_endereco, 3)
                    )
                    ELSE
                        p.cep_endereco
                END) as cep,
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
                p.numero_identidade as rg, 
                    (CASE LENGTH(p.cnpj_cpf)
                        WHEN 14 AND INSTR(p.cnpj_cpf, '-') THEN 
                            CONCAT(
                                LEFT(p.cnpj_cpf, 3),
                                '.',
                                MID(p.cnpj_cpf, 3, 4),
                                '.',
                                MID(p.cnpj_cpf, 6, 3),
                                '/',
                                MID(p.cnpj_cpf, 9, 4),
                                '-',
                                RIGHT(p.cnpj_cpf, 2)
                            )
                        WHEN 11 THEN 
                            CONCAT(
                                LEFT(p.cnpj_cpf, 3),
                                '.',
                                MID(p.cnpj_cpf, 4, 3),
                                '.',
                                MID(p.cnpj_cpf, 7, 3),
                                '-',
                                RIGHT(p.cnpj_cpf, 2)
                                )
                                ELSE 
                                    p.cnpj_cpf
                        END 
                    ) AS cnpj_cpf,
                CASE
                    WHEN YEAR(FROM_DAYS(DATEDIFF(CURRENT_DATE,p.data_nascimento))) < 18 then
                        (select nome_contato from pessoa pai where pai.id = a.responsavel_financeiro_pessoa_id)
                    ELSE \"O próprio\"
                end as nome_responsavel,
                CASE
                    WHEN YEAR(FROM_DAYS(DATEDIFF(CURRENT_DATE,p.data_nascimento))) < 18 then
                        (select descricao from relacionamento_aluno ra where ra.id = a.responsavel_financeiro_relacionamento_aluno_id)
                    ELSE \"\"
                end as parentesco_responsavel,
                CASE
                    WHEN YEAR(FROM_DAYS(DATEDIFF(CURRENT_DATE,p.data_nascimento))) < 18 then
                        (select (CASE LENGTH(pai.cnpj_cpf)
                                    WHEN 14 AND INSTR(pai.cnpj_cpf, '-') THEN 
                                        CONCAT(
                                            LEFT(pai.cnpj_cpf, 3),
                                            '.',
                                            MID(pai.cnpj_cpf, 3, 4),
                                            '.',
                                            MID(pai.cnpj_cpf, 6, 3),
                                            '/',
                                            MID(pai.cnpj_cpf, 9, 4),
                                            '-',
                                            RIGHT(pai.cnpj_cpf, 2)
                                        )
                                    WHEN 11 THEN 
                                        CONCAT(
                                            LEFT(pai.cnpj_cpf, 3),
                                            '.',
                                            MID(pai.cnpj_cpf, 4, 3),
                                            '.',
                                            MID(pai.cnpj_cpf, 7, 3),
                                            '-',
                                            RIGHT(pai.cnpj_cpf, 2)
                                            )
                                            ELSE 
                                                pai.cnpj_cpf
                        END)
                        from pessoa pai where pai.id = a.responsavel_financeiro_pessoa_id)
                    ELSE \"\"
                end as cpf_responsavel,
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
                    ELSE \"\"
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
                    ELSE \"\"
                end as celular_responsavel,
                CASE
                    WHEN YEAR(FROM_DAYS(DATEDIFF(CURRENT_DATE,p.data_nascimento))) < 18 then
                        (select 
                            case length(REPLACE(telefone_profissional,' ',''))
                                when 11 then CONCAT(
                                    LEFT(REPLACE(telefone_profissional,' ',''), 2),
                                    ' ',
                                    MID(REPLACE(telefone_profissional,' ',''), 3, 5),
                                    '-',
                                    RIGHT(REPLACE(telefone_profissional,' ',''), 4)
                                )
                                when 10 then CONCAT(
                                    LEFT(REPLACE(telefone_profissional,' ',''), 2),
                                    ' ',
                                    MID(REPLACE(telefone_profissional,' ',''), 3, 4),
                                    '-',
                                    RIGHT(REPLACE(telefone_profissional,' ',''), 4)
                                )
                                else
                                    telefone_profissional
                                end
                        from pessoa pai where pai.id = a.responsavel_financeiro_pessoa_id)
                    ELSE \"\"
                end as fone_comercial_responsavel,
                MIN(tr.data_prorrogacao) as inadimplente_desde, 
                SUM(tr.valor_saldo_devedor) as total_vencido
                from titulo_receber tr
                    inner join aluno a on tr.aluno_id = a.id
                    inner join pessoa p on a.pessoa_id = p.id	
                    left join cidade c on p.cidade_id = c.id
                where 
                    tr.franqueada_id = ".$filtros['franqueada']." and DATE_FORMAT(tr.data_prorrogacao, '%Y-%m-%d') < CURDATE() and tr.situacao = 'PEN'  ".$andWhere." GROUP BY tr.aluno_id";
        
        $result = $this->getTotalInadimplentes($sql);
                                    
        $result['results'] = $this->managerRegistry->getConnection()->fetchAllAssociative($sql);
        
        return $result;
    }
    
    /**
     * Retorna a quantidade de inadimplentes e a soma total dos inadimplentes, é feito um SUM() na consulta que é monstada no método getInadimplentes().
     * Obs : É somado apenas os titulos_receber que estão marcados como 'PEN'(pendente).
     * 
     * @param string $sql
     * @return array
     */
    public function getTotalInadimplentes($sql)
    {
        $sqlTotal = "SELECT SUM(inadimplentes.total_vencido)  AS total_devedor, COUNT(inadimplentes.aluno_id) AS total_inadimplentes FROM (".$sql.") AS inadimplentes";
        $retorno['total'] = $this->managerRegistry->getConnection()->fetchAllAssociative($sqlTotal);
        return $retorno;
    }
}