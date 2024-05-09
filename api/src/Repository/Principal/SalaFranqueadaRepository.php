<?php

namespace App\Repository\Principal;

use App\Entity\Principal\SalaFranqueada;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Symfony\Bridge\Doctrine\RegistryInterface;
use App\Helper\ConstanteParametros;

/**
 * @method SalaFranqueada|null find($id, $lockMode = null, $lockVersion = null)
 * @method SalaFranqueada|null findOneBy(array $criteria, array $orderBy = null)
 * @method SalaFranqueada[]    findAll()
 * @method SalaFranqueada[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class SalaFranqueadaRepository extends ServiceEntityRepository
{


    public function __construct(RegistryInterface $registry)
    {
        parent::__construct($registry, SalaFranqueada::class);
    }

    /**
     * Monta a query base para Sala Franqueada
     *
     * @return \Doctrine\ORM\QueryBuilder
     */
    protected function montaQueryBase()
    {
        $queryBuilder = $this->createQueryBuilder("slf");
        $queryBuilder->addSelect("fr");
        $queryBuilder->addSelect("sl");
        $queryBuilder->leftJoin("slf.franqueada", "fr");
        $queryBuilder->leftJoin("slf.sala", "sl");

        return $queryBuilder;
    }

    /**
     * Filtra a sala franqueada por pagina
     *
     * @param array $parametros
     * @param number $pagina
     * @param number $numeroItensPorPagina
     *
     * @return \Knp\Component\Pager\Pagination\SlidingPagination
     */
    public function filtrarSalaFranqueadaPorPagina($parametros, $pagina=1, $numeroItensPorPagina=50)
    {
        $opcoes       = [];
        $queryBuilder = $this->montaQueryBase();

        if ((isset($parametros[ConstanteParametros::CHAVE_ORDENACAO_COLUNA]) === true)&&(is_null($parametros[ConstanteParametros::CHAVE_ORDENACAO_COLUNA]) === false)) {
            $queryBuilder->orderBy($parametros[ConstanteParametros::CHAVE_ORDENACAO_COLUNA], $parametros[ConstanteParametros::CHAVE_ORDENACAO_SORT]);
            $opcoes[\Knp\Component\Pager\Paginator::SORT_FIELD_PARAMETER_NAME]     = "~";
            $opcoes[\Knp\Component\Pager\Paginator::SORT_DIRECTION_PARAMETER_NAME] = "~";
        }

        return \App\Helper\FunctionHelper::montaPaginatorPaginacao($queryBuilder, $pagina, $numeroItensPorPagina, $opcoes);
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
        $queryBuilder->where("slf.id = :id");
        $queryBuilder->setParameter("id", $id);
        return $queryBuilder->getQuery()->getArrayResult();
    }

        /**
     * Busca o registro pela chave primaria
     *
     * @param int $id
     *
     * @return array|NULL
     */
    public function buscarFranqueadaPorId($id, $franqueada)
    {
        $queryBuilder = $this->montaQueryBase('slf')
        ->select('slf')
        ->where("slf.franqueada = :franqueada")
        ->setParameter("franqueada", $franqueada)
        ->andwhere("slf.id = :id")
        ->setParameter("id", $id)
        ->setMaxResults(1);
        return $queryBuilder->getQuery()->getOneOrNullResult();
    }


}
