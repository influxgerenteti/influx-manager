<?php

namespace App\Repository\Principal;

use App\Entity\Principal\Cep;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Symfony\Bridge\Doctrine\RegistryInterface;

/**
 * @method Cep|null find($id, $lockMode = null, $lockVersion = null)
 * @method Cep|null findOneBy(array $criteria, array $orderBy = null)
 * @method Cep[]    findAll()
 * @method Cep[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class CepRepository extends ServiceEntityRepository
{


    public function __construct(RegistryInterface $registry)
    {
        parent::__construct($registry, Cep::class);
    }


}
