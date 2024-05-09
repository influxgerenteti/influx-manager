<?php

namespace App\Repository\Log;

use App\Entity\Log\EmailLog;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Symfony\Bridge\Doctrine\RegistryInterface;

/**
 * @method EmailLog|null find($id, $lockMode = null, $lockVersion = null)
 * @method EmailLog|null findOneBy(array $criteria, array $orderBy = null)
 * @method EmailLog[]    findAll()
 * @method EmailLog[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class EmailLogRepository extends ServiceEntityRepository
{


    public function __construct(RegistryInterface $registry)
    {
        parent::__construct($registry, EmailLog::class);
    }


}
