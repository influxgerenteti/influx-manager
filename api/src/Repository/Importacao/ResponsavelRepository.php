<?php

namespace App\Repository\Importacao;

use App\Entity\Importacao\Responsavel;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Symfony\Bridge\Doctrine\RegistryInterface;

/**
 * @method Responsavel|null find($id, $lockMode = null, $lockVersion = null)
 * @method Responsavel|null findOneBy(array $criteria, array $orderBy = null)
 * @method Responsavel[]    findAll()
 * @method Responsavel[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class ResponsavelRepository extends ServiceEntityRepository
{


    public function __construct(RegistryInterface $registry)
    {
        parent::__construct($registry, Responsavel::class);
    }


}
