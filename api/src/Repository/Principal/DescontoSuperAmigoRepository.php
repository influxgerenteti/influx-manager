<?php

namespace App\Repository\Principal;

use App\Entity\Principal\DescontoSuperAmigo;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Symfony\Bridge\Doctrine\RegistryInterface;

/**
 * @method DescontoSuperAmigo|null find($id, $lockMode = null, $lockVersion = null)
 * @method DescontoSuperAmigo|null findOneBy(array $criteria, array $orderBy = null)
 * @method DescontoSuperAmigo[]    findAll()
 * @method DescontoSuperAmigo[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class DescontoSuperAmigoRepository extends ServiceEntityRepository
{


    public function __construct(RegistryInterface $registry)
    {
        parent::__construct($registry, DescontoSuperAmigo::class);
    }


}
