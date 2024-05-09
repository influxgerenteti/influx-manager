<?php

namespace App\Repository\Importacao;

use App\Entity\Importacao\BibliotecaAutor;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Symfony\Bridge\Doctrine\RegistryInterface;

/**
 *
 * @method BibliotecaAutor|null find($id, $lockMode = null, $lockVersion = null)
 * @method BibliotecaAutor|null findOneBy(array $criteria, array $orderBy = null)
 * @method BibliotecaAutor[]    findAll()
 * @method BibliotecaAutor[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class BibliotecaAutorRepository extends ServiceEntityRepository
{


    public function __construct(RegistryInterface $registry)
    {
        parent::__construct($registry, BibliotecaAutor::class);
    }


}
