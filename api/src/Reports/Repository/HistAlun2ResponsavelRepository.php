<?php

namespace App\Reports\Repository;

use Doctrine\Persistence\ManagerRegistry;

class HistAlun2ResponsavelRepository
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
            SELECT 
                    pessoa.nome_contato,
                    pessoa.endereco,
                    pessoa.bairro_endereco as bairro,
                    case length(pessoa.cep_endereco)
                        when 8 then CONCAT(
                            LEFT(pessoa.cep_endereco, 2),
                            '.',
                            MID(pessoa.cep_endereco, 3, 3),
                            '-',
                            RIGHT(pessoa.cep_endereco, 3)
                        )
                        else pessoa.cep_endereco
                    end as cep,
                    cidade.nome as cidade,
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
                    end as fone,
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
                    end as fone_contato,
                    case length(REPLACE(pessoa.telefone_profissional,' ',''))
                        when 11 then CONCAT(
                            LEFT(REPLACE(pessoa.telefone_profissional,' ',''), 2),
                            ' ',
                            MID(REPLACE(pessoa.telefone_profissional,' ',''), 3, 5),
                            '-',
                            RIGHT(REPLACE(pessoa.telefone_profissional,' ',''), 4)
                        )
                        when 10 then CONCAT(
                            LEFT(REPLACE(pessoa.telefone_profissional,' ',''), 2),
                            ' ',
                            MID(REPLACE(pessoa.telefone_profissional,' ',''), 3, 4),
                            '-',
                            RIGHT(REPLACE(pessoa.telefone_profissional,' ',''), 4)
                        )
                        else
                            pessoa.telefone_profissional
                    end as fone_comercial,
                    pessoa.email_preferencial as email,
                    pessoa.numero_identidade as rg,
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
                    END as cpf
             FROM aluno 
             inner join pessoa
                on aluno.responsavel_financeiro_pessoa_id = pessoa.id
             left join cidade 
                on pessoa.cidade_id = cidade.id
             inner join contrato
                on contrato.aluno_id = aluno.id 	
            
            where contrato.franqueada_id = $franqueadaId
                and aluno.id = $alunoId
            limit 1	
        SQL;

        return $this->managerRegistry->getConnection()->fetchAllAssociative($sql);
    }
}