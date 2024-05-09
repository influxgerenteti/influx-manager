<?php

namespace App\Repository\Principal;

use App\Entity\Principal\HistoricoItem;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Symfony\Bridge\Doctrine\RegistryInterface;

/**
 * @method HistoricoItem|null find($id, $lockMode = null, $lockVersion = null)
 * @method HistoricoItem|null findOneBy(array $criteria, array $orderBy = null)
 * @method HistoricoItem[]    findAll()
 * @method HistoricoItem[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class HistoricoItemRepository extends ServiceEntityRepository
{


    public function __construct(RegistryInterface $registry)
    {
        parent::__construct($registry, HistoricoItem::class);
    }


}
