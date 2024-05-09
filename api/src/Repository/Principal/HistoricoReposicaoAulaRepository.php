<?php

namespace App\Repository\Principal;

use App\Entity\Principal\HistoricoReposicaoAula;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Symfony\Bridge\Doctrine\RegistryInterface;

/**
 * @method HistoricoReposicaoAula|null find($id, $lockMode = null, $lockVersion = null)
 * @method HistoricoReposicaoAula|null findOneBy(array $criteria, array $orderBy = null)
 * @method HistoricoReposicaoAula[]    findAll()
 * @method HistoricoReposicaoAula[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class HistoricoReposicaoAulaRepository extends ServiceEntityRepository
{


    public function __construct(RegistryInterface $registry)
    {
        parent::__construct($registry, HistoricoReposicaoAula::class);
    }


}
