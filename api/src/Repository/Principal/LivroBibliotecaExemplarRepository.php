<?php

namespace App\Repository\Principal;

use App\Entity\Principal\LivroBibliotecaExemplar;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Symfony\Bridge\Doctrine\RegistryInterface;

/**
 * @method LivroBibliotecaExemplar|null find($id, $lockMode = null, $lockVersion = null)
 * @method LivroBibliotecaExemplar|null findOneBy(array $criteria, array $orderBy = null)
 * @method LivroBibliotecaExemplar[]    findAll()
 * @method LivroBibliotecaExemplar[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class LivroBibliotecaExemplarRepository extends ServiceEntityRepository
{


    public function __construct(RegistryInterface $registry)
    {
        parent::__construct($registry, LivroBibliotecaExemplar::class);
    }

    public function buscarExemplarPorCodigo ($codigo)
    {
        $queryBuilder = $this->createQueryBuilder('exemplar');
        $queryBuilder->andWhere('exemplar.codigo = :codigo');
        $queryBuilder->andWhere('exemplar.franqueada = :franqueada');
        $queryBuilder->setParameter('codigo', $codigo);
        $queryBuilder->setParameter('franqueada', \App\Helper\VariaveisCompartilhadas::$franqueadaID);
        $queryBuilder->setMaxResults(1);

        return $queryBuilder->getQuery()->getOneOrNullResult();
    }


}
