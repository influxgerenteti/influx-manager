<?php

namespace App\Repository\Principal;

use App\Entity\Principal\TipoItem;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Symfony\Bridge\Doctrine\RegistryInterface;
use App\Helper\ConstanteParametros;

/**
 * @method TipoItem|null find($id, $lockMode = null, $lockVersion = null)
 * @method TipoItem|null findOneBy(array $criteria, array $orderBy = null)
 * @method TipoItem[]    findAll()
 * @method TipoItem[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class TipoItemRepository extends ServiceEntityRepository
{


    public function __construct(RegistryInterface $registry)
    {
        parent::__construct($registry, TipoItem::class);
    }

    /**
     * Monta Query principal
     *
     * @return \Doctrine\ORM\QueryBuilder
     */
    protected function montaQueryBase()
    {
        $queryBuilder = $this->createQueryBuilder("tp");
        $queryBuilder->addSelect("oca");
        $queryBuilder->leftJoin("tp.tipo_ocorrencia", "oca");

        return $queryBuilder;
    }

    /**
     * Monta os filtros passado pela requisicao
     *
     * @param \Doctrine\ORM\QueryBuilder $queryBuilder
     * @param array $parametros
     */
    protected function montaFiltros(&$queryBuilder, $parametros)
    {
        if ((isset($parametros[ConstanteParametros::CHAVE_TIPO]) === true) && (empty($parametros[ConstanteParametros::CHAVE_TIPO]) === false)) {
            $queryBuilder->andWhere("tp.tipo IN (:tipoItem)");
            $queryBuilder->setParameter("tipoItem", $parametros[ConstanteParametros::CHAVE_TIPO]);
        }
    }

    /**
     * Filtra os Tipos de item por pagina
     *
     * @param array $parametros
     * @param number $pagina
     * @param number $numeroItensPorPagina
     *
     * @return \Knp\Component\Pager\Pagination\SlidingPagination
     */
    public function filtrarTipoItemPorPagina($parametros, $pagina=1, $numeroItensPorPagina=9999)
    {
        $opcoes       = [];
        $queryBuilder = $this->montaQueryBase();
        $this->montaFiltros($queryBuilder, $parametros);

        if ((isset($parametros[ConstanteParametros::CHAVE_ORDENACAO_COLUNA]) === true)&&(is_null($parametros[ConstanteParametros::CHAVE_ORDENACAO_COLUNA]) === false)) {
            $queryBuilder->orderBy($parametros[ConstanteParametros::CHAVE_ORDENACAO_COLUNA], $parametros[ConstanteParametros::CHAVE_ORDENACAO_SORT]);
            $opcoes[\Knp\Component\Pager\Paginator::SORT_FIELD_PARAMETER_NAME]     = "~";
            $opcoes[\Knp\Component\Pager\Paginator::SORT_DIRECTION_PARAMETER_NAME] = "~";
        }

        return \App\Helper\FunctionHelper::montaPaginatorPaginacao($queryBuilder, $pagina, $numeroItensPorPagina, $opcoes);
    }

    /**
     * Busca o registro por ID
     *
     * @param int $id
     *
     * @return array|NULL
     */
    public function buscarRegistroPorId($id)
    {
        $queryBuilder = $this->montaQueryBase();
        $queryBuilder->andWhere("tp.id = :id");
        $queryBuilder->setParameter("id", $id);
        return \App\Helper\FunctionHelper::retornaArrayNull($queryBuilder, true);
    }


}
