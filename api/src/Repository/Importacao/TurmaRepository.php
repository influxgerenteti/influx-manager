<?php

namespace App\Repository\Importacao;

use App\Entity\Importacao\Turma;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Symfony\Bridge\Doctrine\RegistryInterface;

/**
 *
 * @method Turma|null find($id, $lockMode = null, $lockVersion = null)
 * @method Turma|null findOneBy(array $criteria, array $orderBy = null)
 * @method Turma[]    findAll()
 * @method Turma[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class TurmaRepository extends ServiceEntityRepository
{


    public function __construct(RegistryInterface $registry)
    {
        parent::__construct($registry, Turma::class);
    }


}
