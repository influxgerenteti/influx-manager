<?php

namespace App\Repository\Principal;

use App\Entity\Principal\MotivoNaoFechamentoConvenio;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Symfony\Bridge\Doctrine\RegistryInterface;
use App\Helper\ConstanteParametros;

/**
 * @method MotivoNaoFechamentoConvenio|null find($id, $lockMode = null, $lockVersion = null)
 * @method MotivoNaoFechamentoConvenio|null findOneBy(array $criteria, array $orderBy = null)
 * @method MotivoNaoFechamentoConvenio[]    findAll()
 * @method MotivoNaoFechamentoConvenio[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class MotivoNaoFechamentoConvenioRepository extends ServiceEntityRepository
{


    public function __construct(RegistryInterface $registry)
    {
        parent::__construct($registry, MotivoNaoFechamentoConvenio::class);
    }

    /**
     * Monta query padrao
     *
     * @return \Doctrine\ORM\QueryBuilder
     */
    protected function montaQueryBase()
    {
        $queryBuilder = $this->createQueryBuilder("mnfc");
        return $queryBuilder;
    }

    /**
     * Monta Filtros
     *
     * @param array $parametros
     * @param \Doctrine\ORM\QueryBuilder $queryBuilder
     */
    protected function montaFiltros($parametros, &$queryBuilder)
    {
        if ((isset($parametros[ConstanteParametros::CHAVE_DESCRICAO]) === true)&&(empty($parametros[ConstanteParametros::CHAVE_DESCRICAO]) === false)) {
            $queryBuilder->andWhere("mnfc.descricao LIKE :descricao");
            $queryBuilder->setParameter("descricao", "%" . $parametros[ConstanteParametros::CHAVE_DESCRICAO] . "%");
        }
    }

    /**
     * Filtra os MotivoNaoFechamentoConvenio
     *
     * @param array $parametros
     * @param number $pagina
     * @param number $numeroItensPorPagina
     *
     * @return \Knp\Component\Pager\Pagination\SlidingPagination
     */
    public function filtrarMotivoNaoFechamentoConvenioPorPagina($parametros, $pagina=1, $numeroItensPorPagina=50)
    {
        $opcoes       = [];
        $queryBuilder = $this->montaQueryBase();
        $this->montaFiltros($parametros, $queryBuilder, $opcoes);
        if ((isset($parametros[ConstanteParametros::CHAVE_ORDENACAO_COLUNA]) === true)&&(is_null($parametros[ConstanteParametros::CHAVE_ORDENACAO_COLUNA]) === false)) {
            $queryBuilder->orderBy($parametros[ConstanteParametros::CHAVE_ORDENACAO_COLUNA], $parametros[ConstanteParametros::CHAVE_ORDENACAO_SORT]);
            $opcoes[\Knp\Component\Pager\Paginator::SORT_FIELD_PARAMETER_NAME]     = "~";
            $opcoes[\Knp\Component\Pager\Paginator::SORT_DIRECTION_PARAMETER_NAME] = "~";
        }

        return \App\Helper\FunctionHelper::montaPaginatorPaginacao($queryBuilder, $pagina, $numeroItensPorPagina, $opcoes);
    }


}
