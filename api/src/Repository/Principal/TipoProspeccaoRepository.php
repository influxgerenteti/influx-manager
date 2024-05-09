<?php

namespace App\Repository\Principal;

use App\Entity\Principal\TipoProspeccao;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Symfony\Bridge\Doctrine\RegistryInterface;

/**
 * @method TipoProspeccao|null find($id, $lockMode = null, $lockVersion = null)
 * @method TipoProspeccao|null findOneBy(array $criteria, array $orderBy = null)
 * @method TipoProspeccao[]    findAll()
 * @method TipoProspeccao[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class TipoProspeccaoRepository extends ServiceEntityRepository
{


    public function __construct(RegistryInterface $registry)
    {
        parent::__construct($registry, TipoProspeccao::class);
    }

    /**
     * Monta a query base para TipoProspeccao
     *
     * @return \Doctrine\ORM\QueryBuilder
     */
    protected function montaQueryBase()
    {
        $queryBuilder = $this->createQueryBuilder("tp");
        $queryBuilder->addSelect("tptp");
        $queryBuilder->leftJoin("tp.tipo_pai_tipo_prospeccao", "tptp");
        return $queryBuilder;
    }

    /**
     * Filtra o TipoProspeccao por pagina
     *
     * @param array $parametros
     * @param number $pagina
     * @param number $numeroItensPorPagina
     *
     * @return \Knp\Component\Pager\Pagination\SlidingPagination
     */
    public function filtrarLivroPorPagina($parametros, $pagina=1, $numeroItensPorPagina=50)
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
        $queryBuilder->where("tp.id = :id");
        $queryBuilder->setParameter("id", $id);
        return $queryBuilder->getQuery()->getArrayResult();
    }


}
