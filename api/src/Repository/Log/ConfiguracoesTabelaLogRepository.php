<?php

namespace App\Repository\Log;

use App\Entity\Log\ConfiguracoesTabelaLog;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Symfony\Bridge\Doctrine\RegistryInterface;

/**
 * @method ConfiguracoesTabelaLog|null find($id, $lockMode = null, $lockVersion = null)
 * @method ConfiguracoesTabelaLog|null findOneBy(array $criteria, array $orderBy = null)
 * @method ConfiguracoesTabelaLog[]    findAll()
 * @method ConfiguracoesTabelaLog[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class ConfiguracoesTabelaLogRepository extends ServiceEntityRepository
{


    public function __construct(RegistryInterface $registry)
    {
        parent::__construct($registry, ConfiguracoesTabelaLog::class);
    }


}
