<?php

namespace App\Repository\Importacao;

use App\Entity\Importacao\AlunoDiario;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Symfony\Bridge\Doctrine\RegistryInterface;

/**
 *
 * @method AlunoDiario|null find($id, $lockMode = null, $lockVersion = null)
 * @method AlunoDiario|null findOneBy(array $criteria, array $orderBy = null)
 * @method AlunoDiario[]    findAll()
 * @method AlunoDiario[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class AlunoDiarioRepository extends ServiceEntityRepository
{


    public function __construct(RegistryInterface $registry)
    {
        parent::__construct($registry, AlunoDiario::class);
    }


}
