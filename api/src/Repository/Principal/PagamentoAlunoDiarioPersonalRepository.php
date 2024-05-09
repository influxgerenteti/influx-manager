<?php

namespace App\Repository\Principal;

use App\Entity\Principal\PagamentoAlunoDiarioPersonal;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Symfony\Bridge\Doctrine\RegistryInterface;

/**
 * @method PagamentoAlunoDiarioPersonal|null find($id, $lockMode = null, $lockVersion = null)
 * @method PagamentoAlunoDiarioPersonal|null findOneBy(array $criteria, array $orderBy = null)
 * @method PagamentoAlunoDiarioPersonal[]    findAll()
 * @method PagamentoAlunoDiarioPersonal[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class PagamentoAlunoDiarioPersonalRepository extends ServiceEntityRepository
{


    public function __construct(RegistryInterface $registry)
    {
        parent::__construct($registry, PagamentoAlunoDiarioPersonal::class);
    }


}
