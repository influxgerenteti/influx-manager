<?php

namespace App\Repository\Importacao;

use App\Entity\Importacao\Caixa;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Symfony\Bridge\Doctrine\RegistryInterface;

/**
 * @method Caixa|null find($id, $lockMode = null, $lockVersion = null)
 * @method Caixa|null findOneBy(array $criteria, array $orderBy = null)
 * @method Caixa[]    findAll()
 * @method Caixa[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class CaixaRepository extends ServiceEntityRepository
{


    public function __construct(RegistryInterface $registry)
    {
        parent::__construct($registry, Caixa::class);
    }


}
