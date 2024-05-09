<?php

namespace App\Repository\Principal;

use App\Entity\Principal\CreditosPersonal;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Symfony\Bridge\Doctrine\RegistryInterface;

/**
 * @method CreditosPersonal|null find($id, $lockMode = null, $lockVersion = null)
 * @method CreditosPersonal|null findOneBy(array $criteria, array $orderBy = null)
 * @method CreditosPersonal[]    findAll()
 * @method CreditosPersonal[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class CreditosPersonalRepository extends ServiceEntityRepository
{


    public function __construct(RegistryInterface $registry)
    {
        parent::__construct($registry, CreditosPersonal::class);
    }


}
