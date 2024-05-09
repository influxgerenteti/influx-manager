<?php

namespace App\Repository\Principal;

use App\Entity\Principal\ValorHora;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Symfony\Bridge\Doctrine\RegistryInterface;
use App\Helper\ConstanteParametros;
use App\Helper\VariaveisCompartilhadas;

/**
 * @method ValorHora|null find($id, $lockMode = null, $lockVersion = null)
 * @method ValorHora|null findOneBy(array $criteria, array $orderBy = null)
 * @method ValorHora[]    findAll()
 * @method ValorHora[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class ValorHoraRepository extends ServiceEntityRepository
{


    public function __construct(RegistryInterface $registry)
    {
        parent::__construct($registry, ValorHora::class);
    }

    /**
     * Monta a query base para Valor Hora
     *
     * @return \Doctrine\ORM\QueryBuilder
     */
    protected function montaQueryBase()
    {
        $queryBuilder = $this->createQueryBuilder("vlh");
        $queryBuilder->addSelect("vlhl");
        $queryBuilder->addSelect("fr");
        $queryBuilder->addSelect("nivl");
        $queryBuilder->join("vlh.franqueada", "fr");
        $queryBuilder->leftJoin("vlh.valor_hora_linhas", "vlhl");
        $queryBuilder->leftJoin("vlh.nivel_instrutor", "nivl");

        return $queryBuilder;
    }

    /**
     * Query para realizar fitlro de franqueada
     *
     * @param \Doctrine\ORM\QueryBuilder $queryBuilder
     */
    private function filtrarFranqueada(&$queryBuilder)
    {
        $queryBuilder->where('fr = :franqueada');
        $queryBuilder->setParameter('franqueada', VariaveisCompartilhadas::$franqueadaID);
    }

    /**
     * Monta filtros
     *
     * @param \Doctrine\ORM\QueryBuilder $queryBuilder
     * @param array $parametros
     */
    protected function montaFiltros(&$queryBuilder, $parametros)
    {
        if ((isset($parametros[ConstanteParametros::CHAVE_NIVEL_INSTRUTOR]) === true) && (empty($parametros[ConstanteParametros::CHAVE_NIVEL_INSTRUTOR]) === false)) {
            $queryBuilder->andWhere("nivl.id = :nivel_instrutor_id");
            $queryBuilder->setParameter("nivel_instrutor_id", $parametros[ConstanteParametros::CHAVE_NIVEL_INSTRUTOR]);
        }

        if ((isset($parametros[ConstanteParametros::CHAVE_TIPO_PAGAMENTO]) === true) && (empty($parametros[ConstanteParametros::CHAVE_TIPO_PAGAMENTO]) === false)) {
            $queryBuilder->andWhere("vlhl.tipo_pagamento = :tipo_pagamento");
            $queryBuilder->setParameter("tipo_pagamento", $parametros[ConstanteParametros::CHAVE_TIPO_PAGAMENTO]);
        }

        if ((isset($parametros[ConstanteParametros::CHAVE_SITUACAO]) === true) && (empty($parametros[ConstanteParametros::CHAVE_SITUACAO]) === false)) {
            $queryBuilder->andWhere("vlh.tipo_pagamento = :situacao");
            $queryBuilder->setParameter("situacao", $parametros[ConstanteParametros::CHAVE_SITUACAO]);
        }
    }

    /**
     * Filtra o ValorHora por pagina
     *
     * @param array $parametros
     * @param number $pagina
     * @param number $numeroItensPorPagina
     *
     * @return array Valores hora
     */
    public function filtrarValorHoraPorPagina($parametros, $pagina=1, $numeroItensPorPagina=999)
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

    /**
     * Busca o registro pela chave primaria
     *
     * @param int $id
     *
     * @return array|NULL
     */
    public function buscarPorId($id)
    {
        $queryBuilder = $this->montaQueryBase();
        $this->filtrarFranqueada($queryBuilder);
        $queryBuilder->andWhere("vlh.id = :id");
        $queryBuilder->setParameter("id", $id);
        return $queryBuilder->getQuery()->getArrayResult();
    }


}
