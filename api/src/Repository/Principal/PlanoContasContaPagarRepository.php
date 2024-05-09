<?php

namespace App\Repository\Principal;

use App\Entity\Principal\PlanoContasContaPagar;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Symfony\Bridge\Doctrine\RegistryInterface;

/**
 * @method PlanoContasContaPagar|null find($id, $lockMode = null, $lockVersion = null)
 * @method PlanoContasContaPagar|null findOneBy(array $criteria, array $orderBy = null)
 * @method PlanoContasContaPagar[]    findAll()
 * @method PlanoContasContaPagar[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class PlanoContasContaPagarRepository extends ServiceEntityRepository
{


    public function __construct(RegistryInterface $registry)
    {
        parent::__construct($registry, PlanoContasContaPagar::class);
    }


}
