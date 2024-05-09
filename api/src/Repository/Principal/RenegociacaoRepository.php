<?php

namespace App\Repository\Principal;

use App\Entity\Principal\Renegociacao;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Symfony\Bridge\Doctrine\RegistryInterface;
use App\Helper\ConstanteParametros;

/**
 * @method Renegociacao|null find($id, $lockMode = null, $lockVersion = null)
 * @method Renegociacao|null findOneBy(array $criteria, array $orderBy = null)
 * @method Renegociacao[]    findAll()
 * @method Renegociacao[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class RenegociacaoRepository extends ServiceEntityRepository
{


    public function __construct(RegistryInterface $registry)
    {
        parent::__construct($registry, Renegociacao::class);
    }

    /**
     * Query para realizar filtro de franqueada
     *
     * @param \Doctrine\ORM\QueryBuilder $queryBuilder
     */
    private function filtrarFranqueada(&$queryBuilder)
    {
        $queryBuilder->where("franqueada = :franqueada");
        $queryBuilder->setParameter("franqueada", \App\Helper\VariaveisCompartilhadas::$franqueadaID);
    }

    /**
     * Filtrar as renegociações por pagina
     *
     * @param array $parametros
     * @param number $numeroItensPorPagina
     *
     * @return \Knp\Component\Pager\Pagination\SlidingPagination
     */
    public function listar($parametros, $numeroItensPorPagina=50)
    {
        $opcoes       = [];
        $queryBuilder = $this->createQueryBuilder('renegociacao');
        $queryBuilder->join('renegociacao.franqueada', 'franqueada');
        $this->filtrarFranqueada($queryBuilder);

        if ((isset($parametros[ConstanteParametros::CHAVE_ORDENACAO_COLUNA]) === true)&&(is_null($parametros[ConstanteParametros::CHAVE_ORDENACAO_COLUNA]) === false)) {
            $queryBuilder->orderBy($parametros[ConstanteParametros::CHAVE_ORDENACAO_COLUNA], $parametros[ConstanteParametros::CHAVE_ORDENACAO_SORT]);
            $opcoes[\Knp\Component\Pager\Paginator::SORT_FIELD_PARAMETER_NAME] = "~";
        }

        $resultatosPaginados = \App\Helper\FunctionHelper::montaPaginatorPaginacao($queryBuilder, $parametros[ConstanteParametros::CHAVE_PAGINA], $numeroItensPorPagina, $opcoes);

        return [
            ConstanteParametros::CHAVE_TOTAL => $resultatosPaginados->getTotalItemCount(),
            ConstanteParametros::CHAVE_ITENS => $resultatosPaginados->getItems(),
        ];
    }


}
