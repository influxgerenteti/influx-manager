<?php

namespace App\Repository\Importacao;

use App\Entity\Importacao\FollowUp;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Symfony\Bridge\Doctrine\RegistryInterface;

/**
 *
 * @method FollowUp|null find($id, $lockMode = null, $lockVersion = null)
 * @method FollowUp|null findOneBy(array $criteria, array $orderBy = null)
 * @method FollowUp[]    findAll()
 * @method FollowUp[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class FollowUpRepository extends ServiceEntityRepository
{


    public function __construct(RegistryInterface $registry)
    {
        parent::__construct($registry, FollowUp::class);
    }


}
