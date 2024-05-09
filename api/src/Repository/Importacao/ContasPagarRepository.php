<?php

namespace App\Repository\Importacao;

use App\Entity\Importacao\ContasPagar;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Symfony\Bridge\Doctrine\RegistryInterface;

/**
 * @method ContasPagar|null find($id, $lockMode = null, $lockVersion = null)
 * @method ContasPagar|null findOneBy(array $criteria, array $orderBy = null)
 * @method ContasPagar[]    findAll()
 * @method ContasPagar[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class ContasPagarRepository extends ServiceEntityRepository
{


    public function __construct(RegistryInterface $registry)
    {
        parent::__construct($registry, ContasPagar::class);
    }


}
