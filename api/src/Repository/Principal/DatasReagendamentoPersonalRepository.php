<?php

namespace App\Repository\Principal;

use App\Entity\Principal\DatasReagendamentoPersonal;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Symfony\Bridge\Doctrine\RegistryInterface;

/**
 * @method DatasReagendamentoPersonal|null find($id, $lockMode = null, $lockVersion = null)
 * @method DatasReagendamentoPersonal|null findOneBy(array $criteria, array $orderBy = null)
 * @method DatasReagendamentoPersonal[]    findAll()
 * @method DatasReagendamentoPersonal[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class DatasReagendamentoPersonalRepository extends ServiceEntityRepository
{


    public function __construct(RegistryInterface $registry)
    {
        parent::__construct($registry, DatasReagendamentoPersonal::class);
    }


}
