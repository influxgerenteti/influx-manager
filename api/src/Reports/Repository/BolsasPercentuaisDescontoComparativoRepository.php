<?php

namespace App\Reports\Repository;

use Doctrine\Persistence\ManagerRegistry;

class BolsasPercentuaisDescontoComparativoRepository
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
    }

}