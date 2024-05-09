<?php

namespace App\Repository\Principal;

use App\Entity\Principal\Estado;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Symfony\Bridge\Doctrine\RegistryInterface;
use Doctrine\ORM\Query\Expr;
use App\Helper\ConstanteParametros;

/**
 *
 * @method Estado|null find($id, $lockMode = null, $lockVersion = null)
 * @method Estado|null findOneBy(array $criteria, array $orderBy = null)
 * @method Estado[]    findAll()
 * @method Estado[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class EstadoRepository extends ServiceEntityRepository
{


    public function __construct(RegistryInterface $registry)
    {
        parent::__construct($registry, Estado::class);
    }

    /**
     * Monta query base para consulta do banco de dados
     *
     * @return \Doctrine\ORM\QueryBuilder
     */
    protected function montaQueryBase()
    {
        $queryBuilder = $this->createQueryBuilder("est");
        $queryBuilder->addSelect("cidades");
        $queryBuilder->leftJoin("est.cidades", "cidades");
        return $queryBuilder;
    }
    /**
     * Monta os filtros de busca
     *
     * @param \Doctrine\ORM\QueryBuilder $queryBuilder
     * @param array $parametros
     */
    protected function montaFiltrosBusca(&$queryBuilder, $parametros)
    {
        if ((isset($parametros[ConstanteParametros::CHAVE_NOME]) === true)&&(empty($parametros[ConstanteParametros::CHAVE_DESCRICAO]) === false)) {
            $queryBuilder->andWhere("est.nome = :nomeEstado");
            $queryBuilder->setParameter("estadoNome", $parametros[ConstanteParametros::CHAVE_NOME]);
        }
    }

    /**
     * Filtra o estado por pagina
     *
     * @param array $parametros Filtros para serem realizados
     *
     * @return array
     */
    public function buscarEstados($parametros)
    {
        $queryBuilder = $this->montaQueryBase();
        $this->montaFiltrosBusca($queryBuilder, $parametros);
        return \App\Helper\FunctionHelper::retornaArrayNull($queryBuilder);
    }

    /**
     * Busca o estado na tabela atraves da id
     *
     * @param int $id
     *
     * @return NULL|\App\Entity\Principal\Estado
     */
    public function buscaEstadoPorId($id)
    {
        $queryBuilder = $this->montaQueryBase();
        $queryBuilder->where("est.id = :id");
        $queryBuilder->setParameter("id", $id);
        return $queryBuilder->getQuery()->getOneOrNullResult(2);
    }


}
