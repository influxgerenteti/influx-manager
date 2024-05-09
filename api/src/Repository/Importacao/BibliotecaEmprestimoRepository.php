<?php

namespace App\Repository\Importacao;

use App\Entity\Importacao\BibliotecaEmprestimo;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Symfony\Bridge\Doctrine\RegistryInterface;

/**
 *
 * @method BibliotecaEmprestimo|null find($id, $lockMode = null, $lockVersion = null)
 * @method BibliotecaEmprestimo|null findOneBy(array $criteria, array $orderBy = null)
 * @method BibliotecaEmprestimo[]    findAll()
 * @method BibliotecaEmprestimo[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class BibliotecaEmprestimoRepository extends ServiceEntityRepository
{


    public function __construct(RegistryInterface $registry)
    {
        parent::__construct($registry, BibliotecaEmprestimo::class);
    }


}
