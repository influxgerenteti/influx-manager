<?php

namespace App\Reports\Repository;

use Doctrine\Persistence\ManagerRegistry;

class InadValoresResumoRepository
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
            select tr.aluno_id,
                    (select count(tr2.numero_parcela_documento)
                        from titulo_receber tr2
                        where DATE_FORMAT(tr2.data_prorrogacao, '%Y-%m-%d') < CURDATE() 
                        and tr2.aluno_id = tr.aluno_id 
                    )  as qtd_parcelas_pendentes,
                    (concat(format((select count(tr3.numero_parcela_documento) 
                                                    from titulo_receber tr3 
                                                 where DATE_FORMAT(tr3.data_prorrogacao, '%Y-%m-%d') < CURDATE()
                                                    and tr3.franqueada_id = $franqueadaId)
                                                 /
                                                (select count(tr3.numero_parcela_documento) 
                                                    from titulo_receber tr3 
                                                 where tr3.franqueada_id = $franqueadaId
                                                )*100,2,'de_DE') , "%") 
                    )  as relacao_inadimp
            from titulo_receber tr
            
            where DATE_FORMAT(tr.data_prorrogacao, '%Y-%m-%d') < CURDATE()
                and tr.franqueada_id = $franqueadaId
            group by aluno_id
        SQL;

        return $this->managerRegistry->getConnection()->fetchAllAssociative($sql);
    }
}