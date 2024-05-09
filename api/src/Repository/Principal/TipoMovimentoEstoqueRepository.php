<?php

namespace App\Repository\Principal;

use App\Entity\Principal\TipoMovimentoEstoque;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Symfony\Bridge\Doctrine\RegistryInterface;
use App\Helper\ConstanteParametros;

/**
 *
 * @method TipoMovimentoEstoque|null find($id, $lockMode = null, $lockVersion = null)
 * @method TipoMovimentoEstoque|null findOneBy(array $criteria, array $orderBy = null)
 * @method TipoMovimentoEstoque[]    findAll()
 * @method TipoMovimentoEstoque[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class TipoMovimentoEstoqueRepository extends ServiceEntityRepository
{


    public function __construct(RegistryInterface $registry)
    {
        parent::__construct($registry, TipoMovimentoEstoque::class);
    }

    /**
     * Filtra o TipoMovimentoEstoque por pagina e numero de itens por pagina
     *
     * @param array $parametros
     * @param integer $pagina
     *
     * @return \App\Entity\Principal\TipoMovimentoEstoque[] Resultados em formato de array
     */
    public function filtrarTpMovimentoEstoquePorPagina($parametros, $pagina=1)
    {
        $numeroItensPorPagina = 50;
        $opcoes       = [];
        $queryBuilder = $this->createQueryBuilder("tpe");
        if ((isset($parametros[ConstanteParametros::CHAVE_ORDENACAO_COLUNA]) === true)&&(is_null($parametros[ConstanteParametros::CHAVE_ORDENACAO_COLUNA]) === false)) {
            $queryBuilder->orderBy($parametros[ConstanteParametros::CHAVE_ORDENACAO_COLUNA], $parametros[ConstanteParametros::CHAVE_ORDENACAO_SORT]);
            $opcoes[\Knp\Component\Pager\Paginator::SORT_FIELD_PARAMETER_NAME]     = "~";
            $opcoes[\Knp\Component\Pager\Paginator::SORT_DIRECTION_PARAMETER_NAME] = "~";
        }

        return \App\Helper\FunctionHelper::montaPaginatorPaginacao($queryBuilder, $pagina, $numeroItensPorPagina, $opcoes);
    }

    /**
     * Efetua uma busca no banco de dados atraves do campo "ID" e "Descricao"
     *
     * @param string $descricao Descricao a ser pesquisado no banco de dados
     * @param int $id           Id a ser pesquisado no banco de dados
     *
     * @return array|NULL
     */
    public function buscaPorDescricao($descricao, $id=null)
    {
        $queryBuilder = $this->createQueryBuilder("tpe");
        $queryBuilder->where("UPPER(tpe.descricao) LIKE :descricao");
        $queryBuilder->setParameter("descricao", strtoupper($descricao));
        if (is_null($id) === false) {
            $queryBuilder->andWhere("tpe.id != :id");
            $queryBuilder->setParameter("id", $id);
        }

        return \App\Helper\FunctionHelper::retornaArrayNull($queryBuilder);
    }


}
