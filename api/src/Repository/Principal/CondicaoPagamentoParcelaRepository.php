<?php

namespace App\Repository\Principal;

use App\Entity\Principal\CondicaoPagamentoParcela;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Symfony\Bridge\Doctrine\RegistryInterface;

/**
 *
 * @method CondicaoPagamentoParcela|null find($id, $lockMode = null, $lockVersion = null)
 * @method CondicaoPagamentoParcela|null findOneBy(array $criteria, array $orderBy = null)
 * @method CondicaoPagamentoParcela[]    findAll()
 * @method CondicaoPagamentoParcela[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class CondicaoPagamentoParcelaRepository extends ServiceEntityRepository
{


    public function __construct(RegistryInterface $registry)
    {
        parent::__construct($registry, CondicaoPagamentoParcela::class);
    }


}
