<?php

namespace App\Repository\Principal;

use App\Entity\Principal\AtividadeDollar;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Symfony\Bridge\Doctrine\RegistryInterface;
use App\Helper\ConstanteParametros;

/**
 *
 * @method AtividadeDollar|null find($id, $lockMode = null, $lockVersion = null)
 * @method AtividadeDollar|null findOneBy(array $criteria, array $orderBy = null)
 * @method AtividadeDollar[]    findAll()
 * @method AtividadeDollar[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class AtividadeDollarRepository extends ServiceEntityRepository
{


    public function __construct(RegistryInterface $registry)
    {
        parent::__construct($registry, AtividadeDollar::class);
    }

    /**
     * Busca todas as Atividades Dollar que possuirem a descricao informada
     *
     * @param string $descricao Descricao da Atividade Dollar a ser pesquisada
     * @param int    $id ID da Atividade Dollar
     *
     * @return array|NULL
     */
    public function buscarAtividadeDollarPorDescricao($descricao, $id=null)
    {
        $queryBuilder = $this->createQueryBuilder("ad");
        $queryBuilder->where("UPPER(ad.descricao) LIKE :descricaoAtividadeDollar");

        if (is_null($id) === false) {
            $queryBuilder->andWhere("ad.id != :id")
                ->setParameter("id", $id);
        }

        $queryBuilder->setParameter("descricaoAtividadeDollar", strtoupper($descricao));
        return \App\Helper\FunctionHelper::retornaArrayNull($queryBuilder);
    }

    /**
     * Busca todas as Atividades Dollar cadastradas no sistema
     *
     * @param array $parametros
     * @param number $pagina Pagina para realizar a filtragem
     *
     * @return NULL|\App\Entity\Principal\AtividadeDollar[]
     */
    public function filtrarAtividadesDollarPorPagina($parametros, $pagina=1)
    {
        $opcoes = [];
        $numeroItensPorPagina = 50;
        $queryBuilder         = $this->createQueryBuilder("ad");

        if ((isset($parametros[ConstanteParametros::CHAVE_ORDENACAO_COLUNA]) === true)&&(is_null($parametros[ConstanteParametros::CHAVE_ORDENACAO_COLUNA]) === false)) {
            $queryBuilder->orderBy($parametros[ConstanteParametros::CHAVE_ORDENACAO_COLUNA], $parametros[ConstanteParametros::CHAVE_ORDENACAO_SORT]);
            $opcoes[\Knp\Component\Pager\Paginator::SORT_FIELD_PARAMETER_NAME]     = "~";
            $opcoes[\Knp\Component\Pager\Paginator::SORT_DIRECTION_PARAMETER_NAME] = "~";
        }

        return \App\Helper\FunctionHelper::montaPaginatorPaginacao($queryBuilder, $pagina, $numeroItensPorPagina, $opcoes);
    }


}
