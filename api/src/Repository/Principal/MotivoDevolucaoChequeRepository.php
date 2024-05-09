<?php

namespace App\Repository\Principal;

use App\Entity\Principal\MotivoDevolucaoCheque;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Symfony\Bridge\Doctrine\RegistryInterface;

/**
 * @method MotivoDevolucaoCheque|null find($id, $lockMode = null, $lockVersion = null)
 * @method MotivoDevolucaoCheque|null findOneBy(array $criteria, array $orderBy = null)
 * @method MotivoDevolucaoCheque[]    findAll()
 * @method MotivoDevolucaoCheque[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class MotivoDevolucaoChequeRepository extends ServiceEntityRepository
{


    public function __construct(RegistryInterface $registry)
    {
        parent::__construct($registry, MotivoDevolucaoCheque::class);
    }

    /**
     * Monta a query base para Motivo Devolução Cheque
     *
     * @return \Doctrine\ORM\QueryBuilder
     */
    protected function montaQueryBase()
    {
        $queryBuilder = $this->createQueryBuilder("mdc");
        return $queryBuilder;
    }

    /**
     * Filtra o Motivo Devolução Cheque por pagina
     *
     * @param number $pagina
     * @param number $numeroItensPorPagina
     *
     * @return \Knp\Component\Pager\Pagination\SlidingPagination
     */
    public function filtrarMotivoDevolucaoChequePorPagina($pagina=1, $numeroItensPorPagina=50)
    {
        $queryBuilder = $this->montaQueryBase();
        $queryBuilder->where("mdc.situacao = :situacao");
        $queryBuilder->setParameter("situacao", "A");
        return \App\Helper\FunctionHelper::montaPaginatorPaginacao($queryBuilder, $pagina, $numeroItensPorPagina);
    }


}
