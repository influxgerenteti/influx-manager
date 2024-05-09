<?php

namespace App\Repository\Principal;

use App\Entity\Principal\Arquivos;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Symfony\Bridge\Doctrine\RegistryInterface;

/**
 * @method Arquivos|null find($id, $lockMode = null, $lockVersion = null)
 * @method Arquivos|null findOneBy(array $criteria, array $orderBy = null)
 * @method Arquivos[]    findAll()
 * @method Arquivos[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class ArquivosRepository extends ServiceEntityRepository
{


    public function __construct(RegistryInterface $registry)
    {
        parent::__construct($registry, Arquivos::class);
    }


}
