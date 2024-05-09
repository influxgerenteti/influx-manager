<?php

namespace App\Repository\Principal;

use App\Entity\Principal\ConvidadoAtividadeExtra;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Symfony\Bridge\Doctrine\RegistryInterface;

/**
 * @method ConvidadoAtividadeExtra|null find($id, $lockMode = null, $lockVersion = null)
 * @method ConvidadoAtividadeExtra|null findOneBy(array $criteria, array $orderBy = null)
 * @method ConvidadoAtividadeExtra[]    findAll()
 * @method ConvidadoAtividadeExtra[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class ConvidadoAtividadeExtraRepository extends ServiceEntityRepository
{


    public function __construct(RegistryInterface $registry)
    {
        parent::__construct($registry, ConvidadoAtividadeExtra::class);
    }


}
