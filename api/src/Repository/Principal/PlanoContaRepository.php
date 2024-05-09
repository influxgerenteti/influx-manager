<?php

namespace App\Repository\Principal;

use App\Entity\Principal\PlanoConta;
use Doctrine\Common\Collections\Collection;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Symfony\Bridge\Doctrine\RegistryInterface;
use App\Helper\VariaveisCompartilhadas;

/**
 *
 * @method PlanoConta|null find($id, $lockMode = null, $lockVersion = null)
 * @method PlanoConta|null findOneBy(array $criteria, array $orderBy = null)
 * @method PlanoConta[]    findAll()
 * @method PlanoConta[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class PlanoContaRepository extends ServiceEntityRepository
{


    public function __construct(RegistryInterface $registry)
    {
        parent::__construct($registry, PlanoConta::class);
    }

    /**
     * Monta query padrao para consultas
     *
     * @return \Doctrine\ORM\QueryBuilder
     */
    protected function montaQueryBase()
    {
        $queryBuilder = $this->createQueryBuilder("pc");
        $queryBuilder->addSelect("pcp");
        $queryBuilder->addSelect("fran");
        $queryBuilder->leftJoin("pc.pai", "pcp");
        $queryBuilder->leftJoin("pc.franqueada", "fran");

        return $queryBuilder;
    }

    /**
     * Query para realizar fitlro de franqueada
     *
     * @param \Doctrine\ORM\QueryBuilder $queryBuilder
     */
    private function filtrarFranqueada(&$queryBuilder)
    {
        $queryBuilder->where(
            $queryBuilder->expr()->orX(
                $queryBuilder->expr()->isNull('fran', null),
                $queryBuilder->expr()->eq('fran.franqueadora', 1),
                $queryBuilder->expr()->eq('fran.id', ':franqueada')
            )
        );

        $queryBuilder->setParameter('franqueada', VariaveisCompartilhadas::$franqueadaID);
    }

    /**
     * Adiciona filtros na query com os parametros informados no array
     *
     * @param \Doctrine\ORM\QueryBuilder $queryBuilder
     * @param array $parametros
     */
    protected function montaFiltros(&$queryBuilder, $parametros)
    {
        $queryBuilder->andWhere("pc.situacao <> :pcSituacao");
        $queryBuilder->setParameter("pcSituacao", "R");
    }

    /**
     * Filtra o PlanoConta por pagina
     *
     * @param number $pagina               Numero da pagina
     * @param number $numeroItensPorPagina Numero de itens a serem trazidos na consulta
     *
     * @return \Knp\Component\Pager\Pagination\SlidingPagination Componente de retorno
     */
    public function filtrarPlanoContaPorPagina($pagina=1, $numeroItensPorPagina=50)
    {
        $queryBuilder = $this->montaQueryBase();
        $this->filtrarFranqueada($queryBuilder);
        $this->montaFiltros($queryBuilder, []);
        $queryBuilder->addOrderBy("pc.descricao", "ASC");
        $queryBuilder->addOrderBy('pc.pai', 'ASC');
        return \App\Helper\FunctionHelper::montaPaginatorPaginacao($queryBuilder, $pagina, $numeroItensPorPagina);
    }

    /**
     * Busca os Planos Conta
     *
     * @return Collection|\App\Entity\Principal\PlanoConta[]
     */
    public function buscarPlanosConta()
    {
        $queryBuilder = $this->montaQueryBase();
        $this->filtrarFranqueada($queryBuilder);
        $this->montaFiltros($queryBuilder, []);
        $queryBuilder->addOrderBy("pc.descricao", "ASC");
        $queryBuilder->addOrderBy('pc.pai', 'ASC');
        return \App\Helper\FunctionHelper::retornaArrayNull($queryBuilder);
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
        $queryBuilder->andWhere("pc.id = :id");
        $queryBuilder->setParameter("id", $id);
        return \App\Helper\FunctionHelper::retornaArrayNull($queryBuilder, true);
    }


}
