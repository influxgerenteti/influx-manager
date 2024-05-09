<?php

namespace App\Repository\Principal;

use App\Entity\Principal\PagamentoAtividadeExtra;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Symfony\Bridge\Doctrine\RegistryInterface;

/**
 * @method PagamentoAtividadeExtra|null find($id, $lockMode = null, $lockVersion = null)
 * @method PagamentoAtividadeExtra|null findOneBy(array $criteria, array $orderBy = null)
 * @method PagamentoAtividadeExtra[]    findAll()
 * @method PagamentoAtividadeExtra[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class PagamentoAtividadeExtraRepository extends ServiceEntityRepository
{


    public function __construct(RegistryInterface $registry)
    {
        parent::__construct($registry, PagamentoAtividadeExtra::class);
    }


}
