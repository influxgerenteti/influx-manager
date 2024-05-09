<?php

namespace App\Repository\Principal;

use App\Entity\Principal\FuncionarioDisponibilidade;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Symfony\Bridge\Doctrine\RegistryInterface;

/**
 * @method FuncionarioDisponibilidade|null find($id, $lockMode = null, $lockVersion = null)
 * @method FuncionarioDisponibilidade|null findOneBy(array $criteria, array $orderBy = null)
 * @method FuncionarioDisponibilidade[]    findAll()
 * @method FuncionarioDisponibilidade[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class FuncionarioDisponibilidadeRepository extends ServiceEntityRepository
{


    public function __construct(RegistryInterface $registry)
    {
        parent::__construct($registry, FuncionarioDisponibilidade::class);
    }

    /**
     * Monta a query base para Livro
     *
     * @return \Doctrine\ORM\QueryBuilder
     */
    protected function montaQueryBase()
    {
        $queryBuilder = $this->createQueryBuilder("fudi");
        $queryBuilder->addSelect("fu");
        $queryBuilder->leftJoin("fudi.funcionario", "fu");
        return $queryBuilder;
    }

    /**
     * Filtra o FuncionarioDisponibilidade por pagina
     *
     * @param array $parametros
     * @param number $pagina
     * @param number $numeroItensPorPagina
     *
     * @return \Knp\Component\Pager\Pagination\SlidingPagination
     */
    public function filtrarFuncionarioDisponibilidadePorPagina($parametros, $pagina=1, $numeroItensPorPagina=50)
    {
        $queryBuilder = $this->montaQueryBase();
        return \App\Helper\FunctionHelper::montaPaginatorPaginacao($queryBuilder, $pagina, $numeroItensPorPagina);
    }

    /**
     * Busca o registro pela chave primaria
     *
     * @param int $id
     *
     * @return array|NULL
     */
    public function buscarPorId($id)
    {
        $queryBuilder = $this->montaQueryBase();
        $queryBuilder->where("fudi.id = :id");
        $queryBuilder->setParameter("id", $id);
        return $queryBuilder->getQuery()->getArrayResult();
    }

    /**
     * Busca registros pelo funcionário
     *
     * @param int $funcionario
     *
     * @return array|NULL
     */
    public function buscarPorFuncionario ($funcionario)
    {
        $queryBuilder = $this->montaQueryBase();
        $queryBuilder->where("fu.id = :id");
        $queryBuilder->setParameter("id", $funcionario);
        return $queryBuilder->getQuery()->getArrayResult();
    }


}
