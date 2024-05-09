<?php

namespace App\Repository\Principal;

use App\Entity\Principal\InteressadoAtividadeExtra;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Symfony\Bridge\Doctrine\RegistryInterface;

/**
 * @method InteressadoAtividadeExtra|null find($id, $lockMode = null, $lockVersion = null)
 * @method InteressadoAtividadeExtra|null findOneBy(array $criteria, array $orderBy = null)
 * @method InteressadoAtividadeExtra[]    findAll()
 * @method InteressadoAtividadeExtra[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class InteressadoAtividadeExtraRepository extends ServiceEntityRepository
{


    public function __construct(RegistryInterface $registry)
    {
        parent::__construct($registry, InteressadoAtividadeExtra::class);
    }


}
