<?php

namespace App\Repository\Principal;

use App\Entity\Principal\MovimentoEstoque;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Symfony\Bridge\Doctrine\RegistryInterface;

/**
 *
 * @method MovimentoEstoque|null find($id, $lockMode = null, $lockVersion = null)
 * @method MovimentoEstoque|null findOneBy(array $criteria, array $orderBy = null)
 * @method MovimentoEstoque[]    findAll()
 * @method MovimentoEstoque[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class MovimentoEstoqueRepository extends ServiceEntityRepository
{


    public function __construct(RegistryInterface $registry)
    {
        parent::__construct($registry, MovimentoEstoque::class);
    }


}
