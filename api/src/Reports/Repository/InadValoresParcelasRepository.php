<?php

namespace App\Reports\Repository;

use Doctrine\Persistence\ManagerRegistry;

class InadValoresParcelasRepository
{

    /**
     * @var ManagerRegistry
     */
    private ManagerRegistry $managerRegistry;

    public function __construct(ManagerRegistry $managerRegistry)
    {
        $this->managerRegistry = $managerRegistry;
    }

    public function get($alunoId)
    {
        $sql = <<<SQL
            select
                numero_parcela_documento,
                observacao,
                valor_original,
                date_format(data_prorrogacao,"%d/%m/%Y")  as data_vencimento,
                TIMESTAMPDIFF(DAY,data_prorrogacao,now()) as atraso,
                taxa_juro_dia*valor_original/100*(TIMESTAMPDIFF(DAY,data_prorrogacao,now()))+taxa_multa as juros_multa
                
            from titulo_receber
            
            where aluno_id = $alunoId
            and DATE_FORMAT(data_prorrogacao, '%Y-%m-%d') < CURDATE()
        SQL;

        return $this->managerRegistry->getConnection()->fetchAllAssociative($sql);
    }
}