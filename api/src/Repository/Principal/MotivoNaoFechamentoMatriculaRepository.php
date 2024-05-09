<?php

namespace App\Repository\Principal;

use App\Entity\Principal\MotivoNaoFechamentoMatricula;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Symfony\Bridge\Doctrine\RegistryInterface;
use App\Helper\ConstanteParametros;

/**
 * @method MotivoNaoFechamentoMatricula|null find($id, $lockMode = null, $lockVersion = null)
 * @method MotivoNaoFechamentoMatricula|null findOneBy(array $criteria, array $orderBy = null)
 * @method MotivoNaoFechamentoMatricula[]    findAll()
 * @method MotivoNaoFechamentoMatricula[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class MotivoNaoFechamentoMatriculaRepository extends ServiceEntityRepository
{


    public function __construct(RegistryInterface $registry)
    {
        parent::__construct($registry, MotivoNaoFechamentoMatricula::class);
    }

    /**
     * Monta a query base para MotivoNaoFechamentoMatricula
     *
     * @return \Doctrine\ORM\QueryBuilder
     */
    protected function montaQueryBase()
    {
        $queryBuilder = $this->createQueryBuilder("mnfm");
        return $queryBuilder;
    }

    /**
     * Filtra o MotivoNaoFechamentoMatricula por pagina
     *
     * @param array $parametros
     * @param number $pagina
     * @param number $numeroItensPorPagina
     *
     * @return \Knp\Component\Pager\Pagination\SlidingPagination
     */
    public function filtrarMotivoNaoFechamentoMatriculaPorPagina($parametros, $pagina=1, $numeroItensPorPagina=50)
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
     * Busca na base pelos registros ativos que possuam a descricao informada
     *
     * @param string $descricao
     *
     * @return array|mixed|\Doctrine\DBAL\Driver\Statement|NULL
     */
    public function buscaRegistroAtivoPorDescricao($descricao)
    {
        $queryBuilder = $this->montaQueryBase();
        $queryBuilder->where("UPPER(mnfm.descricao) = :descricao");
        $queryBuilder->setParameter("descricao", strtoupper($descricao));
        $queryBuilder->andWhere("mnfm.situacao = :situacao");
        $queryBuilder->setParameter("situacao", "A");
        return $queryBuilder->getQuery()->getArrayResult();
    }


}
