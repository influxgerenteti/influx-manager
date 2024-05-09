<?php

namespace App\Repository\Principal;

use App\Entity\Principal\Checklist;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Symfony\Bridge\Doctrine\RegistryInterface;
use App\Helper\ConstanteParametros;
use App\Helper\VariaveisCompartilhadas;

/**
 * @method Checklist|null find($id, $lockMode = null, $lockVersion = null)
 * @method Checklist|null findOneBy(array $criteria, array $orderBy = null)
 * @method Checklist[]    findAll()
 * @method Checklist[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class ChecklistRepository extends ServiceEntityRepository
{


    public function __construct(RegistryInterface $registry)
    {
        parent::__construct($registry, Checklist::class);
    }

    /**
     * Monta Query Principal
     *
     * @return \Doctrine\ORM\QueryBuilder
     */
    protected function montaQueryBase()
    {
        $queryBuilder = $this->createQueryBuilder("chk");
        $queryBuilder->addSelect("chka");
        $queryBuilder->leftJoin("chk.checklist_atividade", "chka");
        $queryBuilder->leftJoin("chka.franqueada", "fran");
        return $queryBuilder;
    }

    /**
     * Filtra os Checklist Por Pagina
     *
     * @param array $parametros
     * @param number $pagina
     * @param number $numeroItensPorPagina
     *
     * @return \Knp\Component\Pager\Pagination\SlidingPagination
     */
    public function filtraChecklistPorPagina($parametros, $pagina=1, $numeroItensPorPagina=50)
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
     * Busca o registro através da chave primaria
     *
     * @param int $id
     *
     * @return array|NULL
     */
    public function buscarPorId($id)
    {
        $queryBuilder = $this->montaQueryBase();
        $queryBuilder->where("fran.id = :franqueadaId");
        $queryBuilder->andWhere("chk.id = :checklistId");
        $queryBuilder->setParameter("franqueadaId", VariaveisCompartilhadas::$franqueadaID);
        $queryBuilder->setParameter("checklistId", $id);

        return \App\Helper\FunctionHelper::retornaArrayNull($queryBuilder, true);
    }

    /**
     * Busca as atividades através dos papeis do usuarios e franqueada
     *
     * @param array $papeis
     *
     * @return array|NULL
     */
    public function buscarPorPapeis($papeis)
    {
        $queryBuilder = $this->montaQueryBase();
        $queryBuilder->addSelect("fran");
        $queryBuilder->addSelect("p");
        $queryBuilder->join("chka.papel", "p");
        $queryBuilder->where("p.id IN (:papeisId)");
        $queryBuilder->andWhere(
            $queryBuilder->expr()->orX(
                $queryBuilder->expr()->eq("fran.franqueadora", 1),
                $queryBuilder->expr()->eq("fran.id", ":franqueadaId")
            )
        );
        $queryBuilder->setParameter("franqueadaId", VariaveisCompartilhadas::$franqueadaID);
        $queryBuilder->setParameter("papeisId", $papeis);

        return \App\Helper\FunctionHelper::retornaArrayNull($queryBuilder);
    }


}
