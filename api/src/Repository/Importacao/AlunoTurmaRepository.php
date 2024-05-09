<?php

namespace App\Repository\Importacao;

use App\Entity\Importacao\AlunoTurma;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Symfony\Bridge\Doctrine\RegistryInterface;

/**
 *
 * @method AlunoTurma|null find($id, $lockMode = null, $lockVersion = null)
 * @method AlunoTurma|null findOneBy(array $criteria, array $orderBy = null)
 * @method AlunoTurma[]    findAll()
 * @method AlunoTurma[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class AlunoTurmaRepository extends ServiceEntityRepository
{


    public function __construct(RegistryInterface $registry)
    {
        parent::__construct($registry, AlunoTurma::class);
    }


}
