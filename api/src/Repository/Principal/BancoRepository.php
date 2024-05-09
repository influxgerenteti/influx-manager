<?php

namespace App\Repository\Principal;

use App\Entity\Principal\Banco;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Symfony\Bridge\Doctrine\RegistryInterface;
use App\Helper\ConstanteParametros;

/**
 *
 * @method Banco|null find($id, $lockMode = null, $lockVersion = null)
 * @method Banco|null findOneBy(array $criteria, array $orderBy = null)
 * @method Banco[]    findAll()
 * @method Banco[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class BancoRepository extends ServiceEntityRepository
{


    public function __construct(RegistryInterface $registry)
    {
        parent::__construct($registry, Banco::class);
    }

    /**
     * Busca os bancos atraves do codigo
     *
     * @param string $codigo Codigo do banco a ser pesquisado
     * @param int    $id ID do banco
     *
     * @return array|NULL
     */
    public function buscarBancoPorCodigo($codigo, $id=null)
    {
        $queryBuilder = $this->createQueryBuilder("b");
        $queryBuilder->where("b.codigo = :codigoBanco");

        if (is_null($id) === false) {
            $queryBuilder->andWhere("b.id != :id")
                ->setParameter("id", $id);
        }

        $queryBuilder->setParameter("codigoBanco", $codigo);
        return \App\Helper\FunctionHelper::retornaArrayNull($queryBuilder);
    }

    /**
     * Busca todos os bancos que possuirem a descricao informada
     *
     * @param string $descricao Descricao do banco a ser pesquisado
     * @param int    $id ID do banco
     *
     * @return array|NULL
     */
    public function buscarBancoPorDescricao($descricao, $id=null)
    {
        $queryBuilder = $this->createQueryBuilder("b");
        $queryBuilder->where("UPPER(b.descricao) LIKE :descricaoBanco");

        if (is_null($id) === false) {
            $queryBuilder->andWhere("b.id != :id")
                ->setParameter("id", $id);
        }

        $queryBuilder->setParameter("descricaoBanco", strtoupper($descricao));
        return \App\Helper\FunctionHelper::retornaArrayNull($queryBuilder);
    }

    /**
     * Busca todos os bancos cadastrados no sistema
     *
     * @param array $parametros
     * @param number $pagina Pagina para realizar a filtragem
     *
     * @return NULL|\App\Entity\Principal\Banco[]
     */
    public function filtrarBancosPorPagina($parametros, $pagina=1)
    {
        $opcoes = [];
        $numeroItensPorPagina = 50;
        $queryBuilder         = $this->createQueryBuilder("b");

        if ((isset($parametros[ConstanteParametros::CHAVE_ORDENACAO_COLUNA]) === true)&&(is_null($parametros[ConstanteParametros::CHAVE_ORDENACAO_COLUNA]) === false)) {
            $queryBuilder->orderBy($parametros[ConstanteParametros::CHAVE_ORDENACAO_COLUNA], $parametros[ConstanteParametros::CHAVE_ORDENACAO_SORT]);
            $opcoes[\Knp\Component\Pager\Paginator::SORT_FIELD_PARAMETER_NAME]     = "~";
            $opcoes[\Knp\Component\Pager\Paginator::SORT_DIRECTION_PARAMETER_NAME] = "~";
        }

        return \App\Helper\FunctionHelper::montaPaginatorPaginacao($queryBuilder, $pagina, $numeroItensPorPagina, $opcoes);
    }


}
