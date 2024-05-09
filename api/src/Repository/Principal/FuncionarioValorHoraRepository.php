<?php

namespace App\Repository\Principal;

use App\Entity\Principal\FuncionarioValorHora;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Symfony\Bridge\Doctrine\RegistryInterface;

/**
 * @method FuncionarioValorHora|null find($id, $lockMode = null, $lockVersion = null)
 * @method FuncionarioValorHora|null findOneBy(array $criteria, array $orderBy = null)
 * @method FuncionarioValorHora[]    findAll()
 * @method FuncionarioValorHora[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class FuncionarioValorHoraRepository extends ServiceEntityRepository
{


    public function __construct(RegistryInterface $registry)
    {
        parent::__construct($registry, FuncionarioValorHora::class);
    }

    /**
     * Monta a query base para Livro
     *
     * @return \Doctrine\ORM\QueryBuilder
     */
    protected function montaQueryBase()
    {
        $queryBuilder = $this->createQueryBuilder("fuvaho");
        $queryBuilder->addSelect("vlh");
        $queryBuilder->addSelect("fu");
        $queryBuilder->leftJoin("fuvaho.valor_hora", "vlh");
        $queryBuilder->leftJoin("fuvaho.funcionario", "fu");
        return $queryBuilder;
    }

    /**
     * Filtra o Livro por pagina
     *
     * @param array $parametros
     * @param number $pagina
     * @param number $numeroItensPorPagina
     *
     * @return \Knp\Component\Pager\Pagination\SlidingPagination
     */
    public function filtrarFuncionarioValorHoraPorPagina($parametros, $pagina=1, $numeroItensPorPagina=50)
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
        $queryBuilder->where("fuvaho.id = :id");
        $queryBuilder->setParameter("id", $id);
        return $queryBuilder->getQuery()->getArrayResult();
    }


}
