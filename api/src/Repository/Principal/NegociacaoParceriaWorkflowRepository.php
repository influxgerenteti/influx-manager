<?php

namespace App\Repository\Principal;

use App\Entity\Principal\NegociacaoParceriaWorkflow;
use App\Helper\ConstanteParametros;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Symfony\Bridge\Doctrine\RegistryInterface;

/**
 * @method NegociacaoParceriaWorkflow|null find($id, $lockMode = null, $lockVersion = null)
 * @method NegociacaoParceriaWorkflow|null findOneBy(array $criteria, array $orderBy = null)
 * @method NegociacaoParceriaWorkflow[]    findAll()
 * @method NegociacaoParceriaWorkflow[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class NegociacaoParceriaWorkflowRepository extends ServiceEntityRepository
{


    public function __construct(RegistryInterface $registry)
    {
        parent::__construct($registry, NegociacaoParceriaWorkflow::class);
    }

    /**
     * Monta query principal
     *
     * @return \Doctrine\ORM\QueryBuilder
     */
    protected function montaQueryBase()
    {
        $queryBuilder = $this->createQueryBuilder("npw");
        $queryBuilder->addSelect("npw");

        return $queryBuilder;
    }

    /**
     * Filtra os registros por pagina
     *
     * @param array $parametros
     * @param number $pagina
     * @param number $numeroItensPorPagina
     *
     * @return \Knp\Component\Pager\Pagination\SlidingPagination
     */
    public function filtrarConvenioPorPagina ($parametros, $pagina, $numeroItensPorPagina=50)
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


}
