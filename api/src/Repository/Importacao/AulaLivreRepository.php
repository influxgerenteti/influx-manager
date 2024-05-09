<?php

namespace App\Repository\Importacao;

use App\Entity\Importacao\AulaLivre;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Symfony\Bridge\Doctrine\RegistryInterface;

/**
 *
 * @method AulaLivre|null find($id, $lockMode = null, $lockVersion = null)
 * @method AulaLivre|null findOneBy(array $criteria, array $orderBy = null)
 * @method AulaLivre[]    findAll()
 * @method AulaLivre[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class AulaLivreRepository extends ServiceEntityRepository
{


    public function __construct(RegistryInterface $registry)
    {
        parent::__construct($registry, AulaLivre::class);
    }


}
