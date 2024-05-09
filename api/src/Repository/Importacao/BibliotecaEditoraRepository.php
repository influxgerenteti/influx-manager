<?php

namespace App\Repository\Importacao;

use App\Entity\Importacao\BibliotecaEditora;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Symfony\Bridge\Doctrine\RegistryInterface;

/**
 *
 * @method BibliotecaEditora|null find($id, $lockMode = null, $lockVersion = null)
 * @method BibliotecaEditora|null findOneBy(array $criteria, array $orderBy = null)
 * @method BibliotecaEditora[]    findAll()
 * @method BibliotecaEditora[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class BibliotecaEditoraRepository extends ServiceEntityRepository
{


    public function __construct(RegistryInterface $registry)
    {
        parent::__construct($registry, BibliotecaEditora::class);
    }


}
