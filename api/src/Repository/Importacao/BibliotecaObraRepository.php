<?php

namespace App\Repository\Importacao;

use App\Entity\Importacao\BibliotecaObra;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Symfony\Bridge\Doctrine\RegistryInterface;

/**
 *
 * @method BibliotecaObra|null find($id, $lockMode = null, $lockVersion = null)
 * @method BibliotecaObra|null findOneBy(array $criteria, array $orderBy = null)
 * @method BibliotecaObra[]    findAll()
 * @method BibliotecaObra[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class BibliotecaObraRepository extends ServiceEntityRepository
{


    public function __construct(RegistryInterface $registry)
    {
        parent::__construct($registry, BibliotecaObra::class);
    }


}
