<?php

namespace App\Repository\Importacao;

use App\Entity\Importacao\AlunoNota;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Symfony\Bridge\Doctrine\RegistryInterface;

/**
 *
 * @method AlunoNota|null find($id, $lockMode = null, $lockVersion = null)
 * @method AlunoNota|null findOneBy(array $criteria, array $orderBy = null)
 * @method AlunoNota[]    findAll()
 * @method AlunoNota[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class AlunoNotaRepository extends ServiceEntityRepository
{


    public function __construct(RegistryInterface $registry)
    {
        parent::__construct($registry, AlunoNota::class);
    }


}
