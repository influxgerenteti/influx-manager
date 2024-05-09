<?php

namespace App\Repository\Principal;

use App\Entity\Principal\HorarioAula;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Symfony\Bridge\Doctrine\RegistryInterface;

/**
 * @method HorarioAula|null find($id, $lockMode = null, $lockVersion = null)
 * @method HorarioAula|null findOneBy(array $criteria, array $orderBy = null)
 * @method HorarioAula[]    findAll()
 * @method HorarioAula[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class HorarioAulaRepository extends ServiceEntityRepository
{


    public function __construct(RegistryInterface $registry)
    {
        parent::__construct($registry, HorarioAula::class);
    }


}
