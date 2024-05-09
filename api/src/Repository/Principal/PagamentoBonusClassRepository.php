<?php

namespace App\Repository\Principal;

use App\Entity\Principal\PagamentoBonusClass;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Symfony\Bridge\Doctrine\RegistryInterface;

/**
 * @method PagamentoBonusClass|null find($id, $lockMode = null, $lockVersion = null)
 * @method PagamentoBonusClass|null findOneBy(array $criteria, array $orderBy = null)
 * @method PagamentoBonusClass[]    findAll()
 * @method PagamentoBonusClass[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class PagamentoBonusClassRepository extends ServiceEntityRepository
{


    public function __construct(RegistryInterface $registry)
    {
        parent::__construct($registry, PagamentoBonusClass::class);
    }


}
