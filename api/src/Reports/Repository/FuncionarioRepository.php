<?php

namespace App\Reports\Repository;

use Doctrine\Persistence\ManagerRegistry;

class FuncionarioRepository
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
                     pessoa.nome_contato,
                     usuario.nome as nome_usuario,
                     cargo.descricao,
                     pessoa.sexo,
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
                     pessoa.estado_civil,
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
                     pessoa.bairro_endereco,
                     pessoa.endereco,
                     pessoa.numero_endereco,
                     funcionario.situacao,
                     date_format(pessoa.data_cadastramento,'%d/%m/%Y') as data_cadastramento
             FROM funcionario 
             inner join pessoa
                on funcionario.pessoa_id = pessoa.id
             inner join	cargo
                on funcionario.cargo_id = cargo.id
            left join usuario
                on funcionario.usuario_id = usuario.id
            
            where $where
            
            order by pessoa.nome_contato
        SQL;

        return $this->managerRegistry->getConnection()->fetchAllAssociative($sql);
    }
}