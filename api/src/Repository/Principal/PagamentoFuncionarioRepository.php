<?php

namespace App\Repository\Principal;

use App\Entity\Principal\PagamentoFuncionario;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Symfony\Bridge\Doctrine\RegistryInterface;
use App\Helper\ConstanteParametros;
use App\Helper\VariaveisCompartilhadas;

/**
 * @method PagamentoFuncionario|null find($id, $lockMode = null, $lockVersion = null)
 * @method PagamentoFuncionario|null findOneBy(array $criteria, array $orderBy = null)
 * @method PagamentoFuncionario[]    findAll()
 * @method PagamentoFuncionario[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class PagamentoFuncionarioRepository extends ServiceEntityRepository
{


    public function __construct(RegistryInterface $registry)
    {
        parent::__construct($registry, PagamentoFuncionario::class);
    }

    /**
     * Monta query principal
     *
     * @return \Doctrine\ORM\QueryBuilder
     */
    protected function montaQueryBase()
    {
        $queryBuilder = $this->createQueryBuilder("pp");
        $queryBuilder->addSelect("fran");
        $queryBuilder->addSelect("func");
        $queryBuilder->addSelect("tur");
        $queryBuilder->addSelect("mt");
        $queryBuilder->addSelect("liv");
        $queryBuilder->addSelect("liv");
        $queryBuilder->addSelect("cp");
        $queryBuilder->leftJoin("pp.franqueada", "fran");
        $queryBuilder->leftJoin("pp.funcionario", "func");
        $queryBuilder->leftJoin("pp.turma", "tur");
        $queryBuilder->leftJoin("tur.modalidade_turma", "mt");
        $queryBuilder->leftJoin("pp.livro", "liv");
        $queryBuilder->leftJoin("pp.licao", "lic");
        $queryBuilder->leftJoin("pp.conta_pagar", "cp");
        return $queryBuilder;
    }

    /**
     * Query para realizar fitlro de franqueada
     *
     * @param \Doctrine\ORM\QueryBuilder $queryBuilder
     */
    protected function filtrarFranqueada(&$queryBuilder)
    {
        $queryBuilder->where('fran.id = :franqueada');
        $queryBuilder->setParameter('franqueada', VariaveisCompartilhadas::$franqueadaID);
    }

    /**
     * Monta os filtros para realização das query
     *
     * @param \Doctrine\ORM\QueryBuilder $queryBuilder
     * @param array $parametros
     */
    protected function montaFiltros(&$queryBuilder, $parametros)
    {
        if ((isset($parametros[ConstanteParametros::CHAVE_FUNCIONARIO]) === true) &&(empty($parametros[ConstanteParametros::CHAVE_FUNCIONARIO]) === false)) {
            $queryBuilder->andWhere("func.id = :funcionarioId");
            $queryBuilder->setParameter("funcionarioId", $parametros[ConstanteParametros::CHAVE_FUNCIONARIO]);
        }

        if ((isset($parametros[ConstanteParametros::CHAVE_DATA_INICIO]) === true) &&(empty($parametros[ConstanteParametros::CHAVE_DATA_INICIO]) === false)) {
            $queryBuilder->andWhere("pp.data_criacao >= :dataInicio");
            $queryBuilder->setParameter("dataInicio", $parametros[ConstanteParametros::CHAVE_DATA_INICIO]);
        }

        if ((isset($parametros[ConstanteParametros::CHAVE_DATA_FIM]) === true) &&(empty($parametros[ConstanteParametros::CHAVE_DATA_FIM]) === false)) {
            $queryBuilder->andWhere("pp.data_criacao <= :dataFim");
            $queryBuilder->setParameter("dataFim", $parametros[ConstanteParametros::CHAVE_DATA_FIM]);
        }

        if ((isset($parametros[ConstanteParametros::CHAVE_MODALIDADE_TURMA]) === true) &&(empty($parametros[ConstanteParametros::CHAVE_MODALIDADE_TURMA]) === false)) {
            $queryBuilder->andWhere("mt.id = :modalidadeTurmaId");
            $queryBuilder->setParameter("modalidadeTurmaId", $parametros[ConstanteParametros::CHAVE_MODALIDADE_TURMA]);
        }

        if ((isset($parametros[ConstanteParametros::CHAVE_TIPOS]) === true) &&(empty($parametros[ConstanteParametros::CHAVE_TIPOS]) === false)) {
            $queryBuilder->andWhere("pp.tipo_pagamento IN (:tipoAtividades)");
            $queryBuilder->setParameter("tipoAtividades", $parametros[ConstanteParametros::CHAVE_TIPOS]);
        }
    }

    /**
     * Filtra os pagamentos de Funcionarioes por pagina
     *
     * @param array $parametros
     * @param number $pagina
     * @param number $numeroItensPorPagina
     *
     * @return \Knp\Component\Pager\Pagination\SlidingPagination
     */
    public function filtrarPagamentoFuncionarioPorPagina($parametros, $pagina=1, $numeroItensPorPagina=100)
    {
        $opcoes       = [];
        $queryBuilder = $this->montaQueryBase();
        $this->filtrarFranqueada($queryBuilder);
        $this->montaFiltros($queryBuilder, $parametros);

        if ((isset($parametros[ConstanteParametros::CHAVE_ORDENACAO_COLUNA]) === true)&&(is_null($parametros[ConstanteParametros::CHAVE_ORDENACAO_COLUNA]) === false)) {
            $queryBuilder->orderBy($parametros[ConstanteParametros::CHAVE_ORDENACAO_COLUNA], $parametros[ConstanteParametros::CHAVE_ORDENACAO_SORT]);
            $opcoes[\Knp\Component\Pager\Paginator::SORT_FIELD_PARAMETER_NAME]     = "~";
            $opcoes[\Knp\Component\Pager\Paginator::SORT_DIRECTION_PARAMETER_NAME] = "~";
        }

        return \App\Helper\FunctionHelper::montaPaginatorPaginacao($queryBuilder, $pagina, $numeroItensPorPagina, $opcoes);
    }


}
