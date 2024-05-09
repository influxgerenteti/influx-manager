<?php

namespace App\Repository\Principal;

use App\Entity\Principal\WorkflowAcao;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Symfony\Bridge\Doctrine\RegistryInterface;
use App\Helper\ConstanteParametros;

/**
 * @method WorkflowAcao|null find($id, $lockMode = null, $lockVersion = null)
 * @method WorkflowAcao|null findOneBy(array $criteria, array $orderBy = null)
 * @method WorkflowAcao[]    findAll()
 * @method WorkflowAcao[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class WorkflowAcaoRepository extends ServiceEntityRepository
{


    public function __construct(RegistryInterface $registry)
    {
        parent::__construct($registry, WorkflowAcao::class);
    }

    /**
     * Monta Query principal
     *
     * @return \Doctrine\ORM\QueryBuilder
     */
    protected function montaQueryBase()
    {
        $queryBuilder = $this->createQueryBuilder("wfa");
        $queryBuilder->addSelect("wf");
        $queryBuilder->addSelect("dwf");
        $queryBuilder->leftJoin("wfa.workflow", "wf");
        $queryBuilder->leftJoin("wfa.destino_workflow", "dwf");
        return $queryBuilder;
    }

    /**
     * Filtra WorkflowAcao por pagina
     *
     * @param array $parametros
     * @param number $pagina
     * @param number $numeroItensPorPagina
     *
     * @return \Knp\Component\Pager\Pagination\SlidingPagination
     */
    public function filtrarWorkflowAcaoPorPagina($parametros, $pagina=1, $numeroItensPorPagina=50)
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
     * Filtra o registro por ID
     *
     * @param int $id
     *
     * @return array|NULL
     */
    public function buscarRegistroPorId($id)
    {
        $queryBuilder = $this->montaQueryBase();
        $queryBuilder->where("wfa.id = :id");
        $queryBuilder->setParameter("id", $id);
        return \App\Helper\FunctionHelper::retornaArrayNull($queryBuilder, true);
    }

    /**
     * Busca os workflowAcao através do workflowId
     *
     * @param int $workflowId
     *
     * @return array|NULL
     */
    public function buscarTodosWorkflowAcaoPorWorkflowId($workflowId)
    {
        $queryBuilder = $this->montaQueryBase();
        $queryBuilder->where("wf.id = :workflowId");
        $queryBuilder->setParameter("workflowId", $workflowId);
        return \App\Helper\FunctionHelper::retornaArrayNull($queryBuilder);
    }


}
