<?php

namespace App\Repository\Importacao;

use App\Entity\Importacao\Aluno;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Symfony\Bridge\Doctrine\RegistryInterface;

/**
 *
 * @method Aluno|null find($id, $lockMode = null, $lockVersion = null)
 * @method Aluno|null findOneBy(array $criteria, array $orderBy = null)
 * @method Aluno[]    findAll()
 * @method Aluno[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class AlunoRepository extends ServiceEntityRepository
{


    public function __construct(RegistryInterface $registry)
    {
        parent::__construct($registry, Aluno::class);
    }


}
