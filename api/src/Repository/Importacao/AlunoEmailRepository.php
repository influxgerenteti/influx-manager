<?php

namespace App\Repository\Importacao;

use App\Entity\Importacao\AlunoEmail;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Symfony\Bridge\Doctrine\RegistryInterface;

/**
 *
 * @method AlunoEmail|null find($id, $lockMode = null, $lockVersion = null)
 * @method AlunoEmail|null findOneBy(array $criteria, array $orderBy = null)
 * @method AlunoEmail[]    findAll()
 * @method AlunoEmail[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class AlunoEmailRepository extends ServiceEntityRepository
{


    public function __construct(RegistryInterface $registry)
    {
        parent::__construct($registry, AlunoEmail::class);
    }


}
