<?php

namespace App\Reports\Repository;

use Doctrine\Persistence\ManagerRegistry;

class AtividadesChecklistRepository
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
            select distinct concat(pessoa.nome_contato, " (", funcionario.apelido, ")") as nome_consultor, 
                    checklist_atividade.id, 
                    checklist_atividade.descricao, 
                    checklist_atividade_realizada.data_conclusao,
                    case checklist_atividade.tipo_atividade
                        when 'D' then	
                            "Diária"
                        when 'S' then	
                            "Semanal"
                        when 'M' then	
                            "Mensal"
                        when 'A' then	
                            "Atemporal"		
                    end as tipo_atividade
                    
                from checklist_atividade
                
                inner join checklist_atividade_papel
                    on checklist_atividade.id = checklist_atividade_papel.checklist_atividade_id
                
                inner join papel
                    on checklist_atividade_papel.papel_id = papel.id
                
                inner join usuario_papel
                    on papel.id = usuario_papel.papel_id
                    
                inner join usuario
                    on usuario.id = usuario_papel.usuario_id
                    
                inner join funcionario
                    on funcionario.usuario_id = usuario.id
                
                inner join pessoa 
                    on funcionario.pessoa_id = pessoa.id 	
                
                left join checklist_atividade_realizada
                    on checklist_atividade_realizada.checklist_atividade_id = checklist_atividade.id
                    
                where $where
        SQL;

        return $this->managerRegistry->getConnection()->fetchAllAssociative($sql);
    }
}