<?php

namespace App\Repository\Principal;

use App\Entity\Principal\PagamentoTurmaAula;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Symfony\Bridge\Doctrine\RegistryInterface;

/**
 * @method PagamentoTurmaAula|null find($id, $lockMode = null, $lockVersion = null)
 * @method PagamentoTurmaAula|null findOneBy(array $criteria, array $orderBy = null)
 * @method PagamentoTurmaAula[]    findAll()
 * @method PagamentoTurmaAula[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class PagamentoTurmaAulaRepository extends ServiceEntityRepository
{


    public function __construct(RegistryInterface $registry)
    {
        parent::__construct($registry, PagamentoTurmaAula::class);
    }


}
