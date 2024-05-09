<?php

namespace App\Repository\Importacao;

use App\Entity\Importacao\Estagio;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Symfony\Bridge\Doctrine\RegistryInterface;

/**
 *
 * @method Estagio|null find($id, $lockMode = null, $lockVersion = null)
 * @method Estagio|null findOneBy(array $criteria, array $orderBy = null)
 * @method Estagio[]    findAll()
 * @method Estagio[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class EstagioRepository extends ServiceEntityRepository
{


    public function __construct(RegistryInterface $registry)
    {
        parent::__construct($registry, Estagio::class);
    }


}
