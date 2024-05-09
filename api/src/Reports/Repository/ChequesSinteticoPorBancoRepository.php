<?php

namespace App\Reports\Repository;

use Doctrine\Persistence\ManagerRegistry;

class ChequesSinteticoPorBancoRepository
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
            select distinct(cheque.banco)
            from cheque
            where ($where)
            order by cheque.banco
        SQL;

        return $this->managerRegistry->getConnection()->fetchAllAssociative($sql);
    }


}
