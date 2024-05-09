<?php

namespace App\Repository\Importacao;

use App\Entity\Importacao\ContratoAulaLivre;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Symfony\Bridge\Doctrine\RegistryInterface;

/**
 * @method ContratoAulaLivre|null find($id, $lockMode = null, $lockVersion = null)
 * @method ContratoAulaLivre|null findOneBy(array $criteria, array $orderBy = null)
 * @method ContratoAulaLivre[]    findAll()
 * @method ContratoAulaLivre[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class ContratoAulaLivreRepository extends ServiceEntityRepository
{


    public function __construct(RegistryInterface $registry)
    {
        parent::__construct($registry, ContratoAulaLivre::class);
    }


}
