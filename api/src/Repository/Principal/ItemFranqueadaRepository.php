<?php

namespace App\Repository\Principal;

use App\Entity\Principal\ItemFranqueada;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Symfony\Bridge\Doctrine\RegistryInterface;

/**
 * @method ItemFranqueada|null find($id, $lockMode = null, $lockVersion = null)
 * @method ItemFranqueada|null findOneBy(array $criteria, array $orderBy = null)
 * @method ItemFranqueada[]    findAll()
 * @method ItemFranqueada[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class ItemFranqueadaRepository extends ServiceEntityRepository
{


    public function __construct(RegistryInterface $registry)
    {
        parent::__construct($registry, ItemFranqueada::class);
    }


}
