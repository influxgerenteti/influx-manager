<?php

namespace App\Repository\Importacao;

use App\Entity\Importacao\BibliotecaExemplares;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Symfony\Bridge\Doctrine\RegistryInterface;

/**
 *
 * @method BibliotecaExemplares|null find($id, $lockMode = null, $lockVersion = null)
 * @method BibliotecaExemplares|null findOneBy(array $criteria, array $orderBy = null)
 * @method BibliotecaExemplares[]    findAll()
 * @method BibliotecaExemplares[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class BibliotecaExemplaresRepository extends ServiceEntityRepository
{


    public function __construct(RegistryInterface $registry)
    {
        parent::__construct($registry, BibliotecaExemplares::class);
    }


}
