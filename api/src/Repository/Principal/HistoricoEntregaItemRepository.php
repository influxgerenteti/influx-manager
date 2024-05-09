<?php

namespace App\Repository\Principal;

use App\Entity\Principal\HistoricoEntregaItem;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Symfony\Bridge\Doctrine\RegistryInterface;

/**
 * @method HistoricoEntregaItem|null find($id, $lockMode = null, $lockVersion = null)
 * @method HistoricoEntregaItem|null findOneBy(array $criteria, array $orderBy = null)
 * @method HistoricoEntregaItem[]    findAll()
 * @method HistoricoEntregaItem[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class HistoricoEntregaItemRepository extends ServiceEntityRepository
{


    public function __construct(RegistryInterface $registry)
    {
        parent::__construct($registry, HistoricoEntregaItem::class);
    }


}
