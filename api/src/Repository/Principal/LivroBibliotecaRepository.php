<?php

namespace App\Repository\Principal;

use App\Entity\Principal\LivroBiblioteca;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Symfony\Bridge\Doctrine\RegistryInterface;

/**
 * @method LivroBiblioteca|null find($id, $lockMode = null, $lockVersion = null)
 * @method LivroBiblioteca|null findOneBy(array $criteria, array $orderBy = null)
 * @method LivroBiblioteca[]    findAll()
 * @method LivroBiblioteca[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class LivroBibliotecaRepository extends ServiceEntityRepository
{


    public function __construct(RegistryInterface $registry)
    {
        parent::__construct($registry, LivroBiblioteca::class);
    }


}
