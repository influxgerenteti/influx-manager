<?php

namespace App\Repository\Principal;

use App\Entity\Principal\PagamentoReposicaoAula;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Symfony\Bridge\Doctrine\RegistryInterface;

/**
 * @method PagamentoReposicaoAula|null find($id, $lockMode = null, $lockVersion = null)
 * @method PagamentoReposicaoAula|null findOneBy(array $criteria, array $orderBy = null)
 * @method PagamentoReposicaoAula[]    findAll()
 * @method PagamentoReposicaoAula[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class PagamentoReposicaoAulaRepository extends ServiceEntityRepository
{


    public function __construct(RegistryInterface $registry)
    {
        parent::__construct($registry, PagamentoReposicaoAula::class);
    }


}
