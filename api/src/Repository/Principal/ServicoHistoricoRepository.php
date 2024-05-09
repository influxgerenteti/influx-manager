<?php

namespace App\Repository\Principal;

use App\Entity\Principal\ServicoHistorico;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Symfony\Bridge\Doctrine\RegistryInterface;

/**
 * @method ServicoHistorico|null find($id, $lockMode = null, $lockVersion = null)
 * @method ServicoHistorico|null findOneBy(array $criteria, array $orderBy = null)
 * @method ServicoHistorico[]    findAll()
 * @method ServicoHistorico[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class ServicoHistoricoRepository extends ServiceEntityRepository
{


    public function __construct(RegistryInterface $registry)
    {
        parent::__construct($registry, ServicoHistorico::class);
    }


}
