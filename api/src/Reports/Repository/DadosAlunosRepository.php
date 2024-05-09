<?php

namespace App\Reports\Repository;

use Doctrine\Persistence\ManagerRegistry;

class DadosAlunosRepository
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
                classificacao_aluno.nome as classificacao_aluno,
                escolaridade.descricao as escolaridade_aluno,
                date_format(pessoa.data_nascimento,'%d/%m/%Y') as dt_nasc,
                concat(YEAR(FROM_DAYS(DATEDIFF(CURRENT_DATE,pessoa.data_nascimento)))," anos") as idade,
                case 
                    when aluno.responsavel_financeiro_pessoa_id <> '' then
                        concat(responsavel_financeiro.nome_contato,' (', coalesce((select relacionamento_aluno.descricao from relacionamento_aluno where relacionamento_aluno.id = aluno.responsavel_financeiro_relacionamento_aluno_id)," -- parentesco não informado -- ") , ')') 
                    else
                        "O próprio"
                end as responsavel_financeiro,
                case 
                    when aluno.responsavel_didatico_pessoa_id <> '' then
                        concat(responsavel_didatico.nome_contato,' (', coalesce((select relacionamento_aluno.descricao 
                                                                                                                                from relacionamento_aluno 
                                                                                                                                where relacionamento_aluno.id = aluno.responsavel_didatico_relacionamento_aluno_id
                        )," -- parentesco não informado -- ") , ')') 
                    else
                        "O próprio"
                end as responsavel_didatico,
                aluno.cod_aluno_importado,
                REPLACE(substring(aluno.foto from 5),'/','\\') as foto,
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
                case aluno.emancipado
                    when 0 then	
                        "Não"
                    else
                        "Sim"
                end as emancipado,
                pessoa.nome_contato as nome_aluno,	
                estado.nome	as estado_nome,
                cidade.nome as cidade_nome,
                pessoa.bairro_endereco,
                pessoa.endereco,
                pessoa.numero_endereco,
                pessoa.complemento_endereco,
                (CASE LENGTH(pessoa.cep_endereco)
                    WHEN 8 THEN CONCAT(
                        LEFT(pessoa.cep_endereco, 2),
                        '.',
                    MID(pessoa.cep_endereco, 3, 3),
                    '-',
                    RIGHT(pessoa.cep_endereco, 3)
                )
                ELSE 
                    pessoa.cep_endereco
                END
                ) AS cep,	
                case 
                    WHEN pessoa.sexo = 'M' THEN 
                        "Masculino"
                    WHEN pessoa.sexo = 'F' THEN 
                        "Feminino"
                    WHEN pessoa.sexo = 'O' THEN 
                        "Outros"
                    ELSE 
                        "Não informado"
                END AS sexo,	
                (CASE LENGTH(pessoa.cnpj_cpf)
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
                pessoa.numero_identidade,
                case 
                    WHEN pessoa.estado_civil = 'S' THEN 
                        "Solteiro"
                    WHEN pessoa.estado_civil = 'C' THEN 
                        "Casado"
                    WHEN pessoa.estado_civil = 'D' THEN 
                        "Divorciado"
                    ELSE 
                        "Não informado"
                END AS estado_civil,
                case length(REPLACE(pessoa.telefone_preferencial,' ',''))
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
                end AS telefone,
                pessoa.email_preferencial as email,
                date_format(pessoa.data_cadastramento,'%d/%m/%Y') as data_cadastramento
            
            from aluno
            inner join classificacao_aluno
                on aluno.classificacao_aluno_id = classificacao_aluno.id
            inner join pessoa
                on aluno.pessoa_id = pessoa.id
            left join estado
                on pessoa.estado_id = estado.id
            left join cidade
                on pessoa.cidade_id = cidade.id	
            left join escolaridade
                on aluno.escolaridade_id = escolaridade.id
            left join pessoa responsavel_financeiro
                on aluno.responsavel_financeiro_pessoa_id = responsavel_financeiro.id
            left join pessoa responsavel_didatico
                on aluno.responsavel_didatico_pessoa_id = responsavel_didatico.id
            
            where $where
        SQL;

        return $this->managerRegistry->getConnection()->fetchAllAssociative($sql);
    }
}