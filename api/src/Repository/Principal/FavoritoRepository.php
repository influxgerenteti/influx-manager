<?php

namespace App\Repository\Principal;

use App\Entity\Principal\Favorito;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Symfony\Bridge\Doctrine\RegistryInterface;

/**
 *
 * @method Favorito|null find($id, $lockMode = null, $lockVersion = null)
 * @method Favorito|null findOneBy(array $criteria, array $orderBy = null)
 * @method Favorito[]    findAll()
 * @method Favorito[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class FavoritoRepository extends ServiceEntityRepository
{


    public function __construct(RegistryInterface $registry)
    {
        parent::__construct($registry, Favorito::class);
    }


}
