<?php

namespace App\Repository\Principal;

use App\Entity\Principal\EmprestimoBiblioteca;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Symfony\Bridge\Doctrine\RegistryInterface;

/**
 * @method EmprestimoBiblioteca|null find($id, $lockMode = null, $lockVersion = null)
 * @method EmprestimoBiblioteca|null findOneBy(array $criteria, array $orderBy = null)
 * @method EmprestimoBiblioteca[]    findAll()
 * @method EmprestimoBiblioteca[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class EmprestimoBibliotecaRepository extends ServiceEntityRepository
{


    public function __construct(RegistryInterface $registry)
    {
        parent::__construct($registry, EmprestimoBiblioteca::class);
    }

    public function buscarEmprestimosAbertosComOExemplar ($exemplar)
    {
        $queryBuilder = $this->createQueryBuilder('emprestimo');
        $queryBuilder->join('emprestimo.livro_biblioteca_exemplar', 'exemplar');
        $queryBuilder->andWhere('exemplar = :exemplar');
        $queryBuilder->andWhere('emprestimo.devolvido = :devolvido');
        $queryBuilder->setParameter('devolvido', false);
        $queryBuilder->setParameter('exemplar', $exemplar);
        $queryBuilder->setMaxResults(1);

        return $queryBuilder->getQuery()->getOneOrNullResult();
    }


}
