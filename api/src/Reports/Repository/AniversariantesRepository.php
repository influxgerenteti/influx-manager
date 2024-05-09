<?php

namespace App\Reports\Repository;

use Doctrine\Persistence\ManagerRegistry;

class AniversariantesRepository
{
    /**
     * @var ManagerRegistry
     */
    private ManagerRegistry $managerRegistry;

    public function __construct(ManagerRegistry $managerRegistry)
    {
        $this->managerRegistry = $managerRegistry;
    }

    public function get($condition)
    {
        $sql = <<<SQL
            select  pessoa.id,
                    pessoa.nome_contato,
                    date_format(pessoa.data_nascimento,'%d/%m/%Y') as data_nascimento,
                    YEAR(FROM_DAYS(DATEDIFF(CURRENT_DATE,pessoa.data_nascimento))) as idade,
                    CASE
                        WHEN (select count(id) from aluno where pessoa.id = aluno.responsavel_financeiro_pessoa_id) > 0 THEN 
                            (select concat(relacionamento_aluno.descricao,' (',(select pss.nome_contato from pessoa pss where pss.id = aluno.pessoa_id),')') from aluno inner join relacionamento_aluno 
                                on relacionamento_aluno.id = aluno.responsavel_financeiro_relacionamento_aluno_id 
                             where pessoa.id = aluno.responsavel_financeiro_pessoa_id)
                        WHEN (select count(id) from funcionario where pessoa.id = funcionario.pessoa_id) > 0 THEN 
                            'Funcionário'
                        WHEN (select count(contrato.id) from contrato inner join aluno on contrato.aluno_id = aluno.id where pessoa.id = aluno.pessoa_id) > 0 THEN 
                            (select 
                                case
                                    when contrato.situacao = 'V' then
                                        'Aluno'
                                    else
                                        'Ex-aluno'
                                end from contrato inner join aluno on contrato.aluno_id = aluno.id 
                             where pessoa.id = aluno.pessoa_id)
                        ELSE 
                            ''
                    END AS representa 
                    
                from pessoa
                 left join aluno 
                    on aluno.pessoa_id = pessoa.id
                left join contrato
                    on contrato.aluno_id = aluno.id
                left join funcionario
                    on funcionario.pessoa_id = pessoa.id
                left join pessoa_franqueada 
                    on pessoa.id = pessoa_franqueada.pessoa_id 
                left join franqueada  
                    on franqueada.id = pessoa_franqueada.franqueada_id	
                
                where $condition
                
                order by  month(pessoa.data_nascimento), day(pessoa.data_nascimento)
        SQL;

        return $this->managerRegistry->getConnection()->fetchAllAssociative($sql);
    }


}
