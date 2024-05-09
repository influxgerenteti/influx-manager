<?php

namespace App\Repository\Principal;

use App\Entity\Principal\Cidade;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Symfony\Bridge\Doctrine\RegistryInterface;
use App\Helper\ConstanteParametros;

/**
 *
 * @method Cidade|null find($id, $lockMode = null, $lockVersion = null)
 * @method Cidade|null findOneBy(array $criteria, array $orderBy = null)
 * @method Cidade[]    findAll()
 * @method Cidade[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class CidadeRepository extends ServiceEntityRepository
{


    public function __construct(RegistryInterface $registry)
    {
        parent::__construct($registry, Cidade::class);
    }

    /**
     * Monta a query de base
     *
     * @return \Doctrine\ORM\QueryBuilder
     */
    protected function montaQueryBase()
    {
        $queryBuilder = $this->createQueryBuilder("cidade");
        $queryBuilder->addSelect("est");
        $queryBuilder->leftJoin("cidade.estado", "est");
        return $queryBuilder;
    }
    /**
     * Monta os filtros de busca com base nos parametros
     *
     * @param \Doctrine\ORM\QueryBuilder $queryBuilder
     * @param array $parametros
     */
    protected function montaFiltrosBusca(&$queryBuilder, $parametros)
    {
        if ((isset($parametros[ConstanteParametros::CHAVE_ESTADO]) === true) && (empty($parametros[ConstanteParametros::CHAVE_ESTADO]) === false)) {
            $queryBuilder->andWhere("est.id = :estadoId");
            $queryBuilder->setParameter("estadoId", $parametros[ConstanteParametros::CHAVE_ESTADO]);
        }

        if ((isset($parametros[ConstanteParametros::CHAVE_NOME]) === true) && (empty($parametros[ConstanteParametros::CHAVE_NOME]) === false)) {
            $queryBuilder->andWhere("cidade.nome = :nomeCidade");
            $queryBuilder->setParameter("nomeCidade", $parametros[ConstanteParametros::CHAVE_NOME]);
        }
    }

    /**
     * Filtra a cidade por pagina
     *
     * @param array $parametros Filtros a serem utilizados
     *
     * @return array
     */
    public function buscarCidades($parametros)
    {
        $queryBuilder = $this->montaQueryBase();
        $this->montaFiltrosBusca($queryBuilder, $parametros);
        return \App\Helper\FunctionHelper::retornaArrayNull($queryBuilder);
    }

    /**
     * Busca a cidade por id da chave primaria
     *
     * @param int $id
     *
     * @return null|\App\Entity\Principal\Cidade
     */
    public function buscaCidadePorId($id)
    {
        $queryBuilder = $this->montaQueryBase();
        $queryBuilder->where("cidade.id = :id");
        $queryBuilder->setParameter("id", $id);
        return $queryBuilder->getQuery()->getOneOrNullResult(2);
    }


}
