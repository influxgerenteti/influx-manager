<?php

namespace App\Reports\Repository;

use Doctrine\Persistence\ManagerRegistry;

class InteressadosPeriodoRepository
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
                interessado.nome,
                interessado.idade,
                livro.descricao as descricao_livro,
                CASE
                    WHEN interessado.periodo_pretendido = 'M' THEN 
                        "Manhã"
                    WHEN interessado.periodo_pretendido = 'T' THEN 
                        "Tarde"
                    WHEN interessado.periodo_pretendido = 'N' THEN 
                        "Noite"
                    WHEN interessado.periodo_pretendido = 'S' THEN 
                        "Sábado"
                    ELSE 
                        "---"
                END AS periodo_pretendido,	
                (select 'Sim' from interessado_atividade_extra where atividade_extra_id = interessado.id) as realizou_nivelamento
            from interessado
            
            left join interessado_idioma 
                on interessado.id = interessado_idioma.interessado_id
            left join idioma
                on interessado_idioma.idioma_id = idioma.id
            
            left join interessado_atividade_extra
                on interessado.id = interessado_atividade_extra.interessado_id
            left join livro
                on interessado_atividade_extra.livro_id = livro.id
            where $where
            order by interessado.nome
        SQL;

        return $this->managerRegistry->getConnection()->fetchAllAssociative($sql);
    }
}