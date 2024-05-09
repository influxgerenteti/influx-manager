<?php

namespace App\Repository\Principal;

use App\Entity\Principal\AcaoSistema;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Symfony\Bridge\Doctrine\RegistryInterface;

/**
 * @method AcaoSistema|null find($id, $lockMode = null, $lockVersion = null)
 * @method AcaoSistema|null findOneBy(array $criteria, array $orderBy = null)
 * @method AcaoSistema[]    findAll()
 * @method AcaoSistema[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class AcaoSistemaRepository extends ServiceEntityRepository
{


    public function __construct(RegistryInterface $registry)
    {
        parent::__construct($registry, AcaoSistema::class);
    }

    /**
     * Monta query base
     *
     * @return \Doctrine\ORM\QueryBuilder
     */
    protected function montaQueryBase()
    {
        $queryBuilder = $this->createQueryBuilder("ass");
        $queryBuilder->addSelect("m");
        $queryBuilder->leftJoin("ass.modulo", "m");
        return $queryBuilder;
    }

    /**
     * Realiza a listagem de acoes do sistema paginado
     *
     * @param array $parametros
     * @param number $pagina
     * @param number $numeroItensPorPagina
     *
     * @return \Knp\Component\Pager\Pagination\SlidingPagination
     */
    public function filtraAcaoSistemaPorPagina($parametros, $pagina=1, $numeroItensPorPagina=50)
    {
        $queryBuilder = $this->montaQueryBase();
        return \App\Helper\FunctionHelper::montaPaginatorPaginacao($queryBuilder, $pagina, $numeroItensPorPagina);
    }

    /**
     * Busca AcaoSistema através da chave primaria
     *
     * @param int $id
     *
     * @return array|NULL
     */
    public function buscaAcaoSistemaPorId($id)
    {
        $queryBuilder = $this->montaQueryBase();
        $queryBuilder->andWhere("ass.id = :acaoSistemaId");
        $queryBuilder->setParameter("acaoSistemaId", $id);
        return \App\Helper\FunctionHelper::retornaArrayNull($queryBuilder, true);
    }

    /**
     * Busca as acoesSistema através do moduloId
     *
     * @param int $moduloId
     *
     * @return array|NULL
     */
    public function buscaAcaoSistemasPorModulo($moduloId)
    {
        $queryBuilder = $this->montaQueryBase();
        $queryBuilder->andWhere("m.id = :moduloId");
        $queryBuilder->setParameter("moduloId", $moduloId);
        return \App\Helper\FunctionHelper::retornaArrayNull($queryBuilder);
    }


}
