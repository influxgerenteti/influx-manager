<?php

namespace App\Repository\Principal;

use App\Entity\Principal\OperadoraCartao;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Symfony\Bridge\Doctrine\RegistryInterface;
use App\Helper\ConstanteParametros;

/**
 * @method OperadoraCartao|null find($id, $lockMode = null, $lockVersion = null)
 * @method OperadoraCartao|null findOneBy(array $criteria, array $orderBy = null)
 * @method OperadoraCartao[]    findAll()
 * @method OperadoraCartao[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class OperadoraCartaoRepository extends ServiceEntityRepository
{


    public function __construct(RegistryInterface $registry)
    {
        parent::__construct($registry, OperadoraCartao::class);
    }

    /**
     * Monta query principal
     *
     * @return \Doctrine\ORM\QueryBuilder
     */
    protected function montaQueryBase()
    {
        $queryBuilder = $this->createQueryBuilder("oc");
        $queryBuilder->addSelect("poc");
        $queryBuilder->addSelect("pp");
        $queryBuilder->addSelect("pl");
        $queryBuilder->leftJoin("oc.franqueada", "fran");
        $queryBuilder->leftJoin("oc.parcelamentoOperadoraCartaos", "poc");
        $queryBuilder->leftJoin("poc.plano_conta", "pl");
        $queryBuilder->leftJoin("poc.parcelaParcelamentos", "pp");

        return $queryBuilder;
    }

    /**
     * Query para realizar fitlro de franqueada
     *
     * @param \Doctrine\ORM\QueryBuilder $queryBuilder
     */
    private function filtrarFranqueada(&$queryBuilder)
    {
        $queryBuilder->andWhere('fran.id = :franqueada');
        $queryBuilder->setParameter('franqueada', \App\Helper\VariaveisCompartilhadas::$franqueadaID);
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
    public function filtrarOperadoraCartaoPorPagina($parametros, $pagina=1, $numeroItensPorPagina=50)
    {
        $opcoes       = [];
        $queryBuilder = $this->montaQueryBase();
        $this->filtrarFranqueada($queryBuilder);

        if ((isset($parametros[ConstanteParametros::CHAVE_ORDENACAO_COLUNA]) === true)&&(is_null($parametros[ConstanteParametros::CHAVE_ORDENACAO_COLUNA]) === false)) {
            $queryBuilder->orderBy($parametros[ConstanteParametros::CHAVE_ORDENACAO_COLUNA], $parametros[ConstanteParametros::CHAVE_ORDENACAO_SORT]);
            $opcoes[\Knp\Component\Pager\Paginator::SORT_FIELD_PARAMETER_NAME]     = "~";
            $opcoes[\Knp\Component\Pager\Paginator::SORT_DIRECTION_PARAMETER_NAME] = "~";
        }

        return \App\Helper\FunctionHelper::montaPaginatorPaginacao($queryBuilder, $pagina, $numeroItensPorPagina, $opcoes);
    }

    /**
     * Busca registro pela id
     *
     * @param int $id
     *
     * @return array|NULL
     */
    public function buscaRegistroPorId($id)
    {
        $queryBuilder = $this->montaQueryBase();
        $queryBuilder->where("oc.id = :idRegistro");
        $queryBuilder->setParameter("idRegistro", $id);
        return \App\Helper\FunctionHelper::retornaArrayNull($queryBuilder, true);
    }


}
