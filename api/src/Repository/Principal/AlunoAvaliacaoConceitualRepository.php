<?php

namespace App\Repository\Principal;

use App\Entity\Principal\AlunoAvaliacaoConceitual;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Symfony\Bridge\Doctrine\RegistryInterface;

/**
 * @method AlunoAvaliacaoConceitual|null find($id, $lockMode = null, $lockVersion = null)
 * @method AlunoAvaliacaoConceitual|null findOneBy(array $criteria, array $orderBy = null)
 * @method AlunoAvaliacaoConceitual[]    findAll()
 * @method AlunoAvaliacaoConceitual[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class AlunoAvaliacaoConceitualRepository extends ServiceEntityRepository
{


    public function __construct(RegistryInterface $registry)
    {
        parent::__construct($registry, AlunoAvaliacaoConceitual::class);
    }


}
