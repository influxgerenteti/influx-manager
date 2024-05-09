<?php

namespace App\Repository\Importacao;

use App\Entity\Importacao\BibliotecaObraReserva;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Symfony\Bridge\Doctrine\RegistryInterface;

/**
 *
 * @method BibliotecaObraReserva|null find($id, $lockMode = null, $lockVersion = null)
 * @method BibliotecaObraReserva|null findOneBy(array $criteria, array $orderBy = null)
 * @method BibliotecaObraReserva[]    findAll()
 * @method BibliotecaObraReserva[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class BibliotecaObraReservaRepository extends ServiceEntityRepository
{


    public function __construct(RegistryInterface $registry)
    {
        parent::__construct($registry, BibliotecaObraReserva::class);
    }


}
