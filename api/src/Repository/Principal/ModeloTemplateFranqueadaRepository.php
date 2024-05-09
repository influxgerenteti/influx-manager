<?php

namespace App\Repository\Principal;

use App\Entity\Principal\ModeloTemplateFranqueada;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Symfony\Bridge\Doctrine\RegistryInterface;

/**
 * @method ModeloTemplateFranqueada|null find($id, $lockMode = null, $lockVersion = null)
 * @method ModeloTemplateFranqueada|null findOneBy(array $criteria, array $orderBy = null)
 * @method ModeloTemplateFranqueada[]    findAll()
 * @method ModeloTemplateFranqueada[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class ModeloTemplateFranqueadaRepository extends ServiceEntityRepository
{


    public function __construct(RegistryInterface $registry)
    {
        parent::__construct($registry, ModeloTemplateFranqueada::class);
    }


}
