<?php

namespace App\Repository\Principal;

use App\Entity\Principal\UrlSistema;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Symfony\Bridge\Doctrine\RegistryInterface;

/**
 * @method UrlSistema|null find($id, $lockMode = null, $lockVersion = null)
 * @method UrlSistema|null findOneBy(array $criteria, array $orderBy = null)
 * @method UrlSistema[]    findAll()
 * @method UrlSistema[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class UrlSistemaRepository extends ServiceEntityRepository
{


    public function __construct(RegistryInterface $registry)
    {
        parent::__construct($registry, UrlSistema::class);
    }


}
