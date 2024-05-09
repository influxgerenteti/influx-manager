<?php

namespace App\Repository\Principal;

use App\Entity\Principal\CabecalhoPagamento;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Symfony\Bridge\Doctrine\RegistryInterface;

/**
 * @method CabecalhoPagamento|null find($id, $lockMode = null, $lockVersion = null)
 * @method CabecalhoPagamento|null findOneBy(array $criteria, array $orderBy = null)
 * @method CabecalhoPagamento[]    findAll()
 * @method CabecalhoPagamento[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class CabecalhoPagamentoRepository extends ServiceEntityRepository
{


    public function __construct(RegistryInterface $registry)
    {
        parent::__construct($registry, CabecalhoPagamento::class);
    }


}
