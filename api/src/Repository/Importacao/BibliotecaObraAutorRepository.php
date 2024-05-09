<?php

namespace App\Repository\Importacao;

use App\Entity\Importacao\BibliotecaObraAutor;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Symfony\Bridge\Doctrine\RegistryInterface;

/**
 *
 * @method BibliotecaObraAutor|null find($id, $lockMode = null, $lockVersion = null)
 * @method BibliotecaObraAutor|null findOneBy(array $criteria, array $orderBy = null)
 * @method BibliotecaObraAutor[]    findAll()
 * @method BibliotecaObraAutor[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class BibliotecaObraAutorRepository extends ServiceEntityRepository
{


    public function __construct(RegistryInterface $registry)
    {
        parent::__construct($registry, BibliotecaObraAutor::class);
    }


}
