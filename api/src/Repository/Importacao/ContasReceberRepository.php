<?php

namespace App\Repository\Importacao;

use App\Entity\Importacao\ContasReceber;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Symfony\Bridge\Doctrine\RegistryInterface;

/**
 * @method ContasReceber|null find($id, $lockMode = null, $lockVersion = null)
 * @method ContasReceber|null findOneBy(array $criteria, array $orderBy = null)
 * @method ContasReceber[]    findAll()
 * @method ContasReceber[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class ContasReceberRepository extends ServiceEntityRepository
{


    public function __construct(RegistryInterface $registry)
    {
        parent::__construct($registry, ContasReceber::class);
    }


}
