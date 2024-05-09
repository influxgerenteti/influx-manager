<?php

namespace App\Repository\Principal;

use App\Entity\Principal\SegmentoEmpresaConvenio;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Symfony\Bridge\Doctrine\RegistryInterface;
use App\Helper\ConstanteParametros;

/**
 * @method SegmentoEmpresaConvenio|null find($id, $lockMode = null, $lockVersion = null)
 * @method SegmentoEmpresaConvenio|null findOneBy(array $criteria, array $orderBy = null)
 * @method SegmentoEmpresaConvenio[]    findAll()
 * @method SegmentoEmpresaConvenio[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class SegmentoEmpresaConvenioRepository extends ServiceEntityRepository
{


    public function __construct(RegistryInterface $registry)
    {
        parent::__construct($registry, SegmentoEmpresaConvenio::class);
    }

    /**
     * Monta Query Padrao
     *
     * @return \Doctrine\ORM\QueryBuilder
     */
    protected function montaQueryBase()
    {
        $queryBuilder = $this->createQueryBuilder("sec");
        return $queryBuilder;
    }

    /**
     * Monta filtros
     *
     * @param array $parametros
     * @param \Doctrine\ORM\QueryBuilder $queryBuilder
     */
    protected function montaFiltros($parametros, &$queryBuilder)
    {
        if ((isset($parametros[ConstanteParametros::CHAVE_DESCRICAO]) === true)&&(empty($parametros[ConstanteParametros::CHAVE_DESCRICAO]) === false)) {
            $queryBuilder->andWhere("sec.descricao LIKE :descricao");
            $queryBuilder->setParameter("descricao", "%" . $parametros[ConstanteParametros::CHAVE_DESCRICAO] . "%");
        }
    }

    /**
     * Filta SegmentoEmpresaConvenio Por Pagina
     *
     * @param array $parametros
     * @param number $pagina
     * @param number $numeroItensPorPagina
     *
     * @return \Knp\Component\Pager\Pagination\SlidingPagination
     */
    public function filtrarSegmentoEmpresaConvenioPorPagina($parametros, $pagina=1, $numeroItensPorPagina=50)
    {
        $opcoes       = [];
        $queryBuilder = $this->montaQueryBase();
        $this->montaFiltros($parametros, $queryBuilder);
        if ((isset($parametros[ConstanteParametros::CHAVE_ORDENACAO_COLUNA]) === true)&&(is_null($parametros[ConstanteParametros::CHAVE_ORDENACAO_COLUNA]) === false)) {
            $queryBuilder->orderBy($parametros[ConstanteParametros::CHAVE_ORDENACAO_COLUNA], $parametros[ConstanteParametros::CHAVE_ORDENACAO_SORT]);
            $opcoes[\Knp\Component\Pager\Paginator::SORT_FIELD_PARAMETER_NAME]     = "~";
            $opcoes[\Knp\Component\Pager\Paginator::SORT_DIRECTION_PARAMETER_NAME] = "~";
        }

        return \App\Helper\FunctionHelper::montaPaginatorPaginacao($queryBuilder, $pagina, $numeroItensPorPagina, $opcoes);
    }


}
