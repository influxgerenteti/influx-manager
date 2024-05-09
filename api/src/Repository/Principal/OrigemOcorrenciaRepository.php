<?php

namespace App\Repository\Principal;

use App\Entity\Principal\OrigemOcorrencia;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Symfony\Bridge\Doctrine\RegistryInterface;

/**
 * @method OrigemOcorrencia|null find($id, $lockMode = null, $lockVersion = null)
 * @method OrigemOcorrencia|null findOneBy(array $criteria, array $orderBy = null)
 * @method OrigemOcorrencia[]    findAll()
 * @method OrigemOcorrencia[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class OrigemOcorrenciaRepository extends ServiceEntityRepository
{


    public function __construct(RegistryInterface $registry)
    {
        parent::__construct($registry, OrigemOcorrencia::class);
    }


}