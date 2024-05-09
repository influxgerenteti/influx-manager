<?php

namespace App\Repository\Principal;

use App\Entity\Principal\MetaFranqueadaHistorico;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Symfony\Bridge\Doctrine\RegistryInterface;

/**
 * @method MetaFranqueadaHistorico|null find($id, $lockMode = null, $lockVersion = null)
 * @method MetaFranqueadaHistorico|null findOneBy(array $criteria, array $orderBy = null)
 * @method MetaFranqueadaHistorico[]    findAll()
 * @method MetaFranqueadaHistorico[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class MetaFranqueadaHistoricoRepository extends ServiceEntityRepository
{


    public function __construct(RegistryInterface $registry)
    {
        parent::__construct($registry, MetaFranqueadaHistorico::class);
    }


}
