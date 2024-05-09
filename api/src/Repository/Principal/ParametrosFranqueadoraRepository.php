<?php

namespace App\Repository\Principal;

use App\Entity\Principal\ParametrosFranqueadora;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Symfony\Bridge\Doctrine\RegistryInterface;

/**
 *
 * @method ParametrosFranqueadora|null find($id, $lockMode = null, $lockVersion = null)
 * @method ParametrosFranqueadora|null findOneBy(array $criteria, array $orderBy = null)
 * @method ParametrosFranqueadora[]    findAll()
 * @method ParametrosFranqueadora[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class ParametrosFranqueadoraRepository extends ServiceEntityRepository
{


    public function __construct(RegistryInterface $registry)
    {
        parent::__construct($registry, ParametrosFranqueadora::class);
    }

    /**
     * Busca o primeiro registro da tabela
     *
     * @return \App\Entity\Principal\ParametrosFranqueadora|null
     */
    public function buscarPrimeiro ()
    {
        $qb = $this->createQueryBuilder('pf');
        return \App\Helper\FunctionHelper::retornaArrayNull($qb, true);
    }


}
