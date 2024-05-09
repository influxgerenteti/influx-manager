<?php

namespace App\Repository\Principal;

use App\Entity\Principal\PlanejamentoLicao;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Symfony\Bridge\Doctrine\RegistryInterface;
use App\Helper\ConstanteParametros;

/**
 * @method PlanejamentoLicao|null find($id, $lockMode = null, $lockVersion = null)
 * @method PlanejamentoLicao|null findOneBy(array $criteria, array $orderBy = null)
 * @method PlanejamentoLicao[]    findAll()
 * @method PlanejamentoLicao[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class PlanejamentoLicaoRepository extends ServiceEntityRepository
{


    public function __construct(RegistryInterface $registry)
    {
        parent::__construct($registry, PlanejamentoLicao::class);
    }

    /**
     * Monta query PlanejamentoLicao
     *
     * @return \Doctrine\ORM\QueryBuilder
     */
    protected function montaQueryBase()
    {
        $queryBuilder = $this->createQueryBuilder("pl");
        $queryBuilder->addSelect("lic");
        $queryBuilder->leftJoin("pl.licaos", "lic");
        return $queryBuilder;
    }

    /**
     * Busca registro por Id
     *
     * @param int $id
     *
     * @return array
     */
    public function buscaPorId($id)
    {
        $queryBuilder = $this->montaQueryBase();
        $queryBuilder->andWhere("pl.id = :id");
        $queryBuilder->setParameter("id", $id);
        return \App\Helper\FunctionHelper::retornaArrayNull($queryBuilder, true);
    }

    /**
     * Filtra o planejamento licao por pagina
     *
     * @param array $parametros
     * @param number $pagina
     * @param number $numeroItensPorPagina
     *
     * @return \Knp\Component\Pager\Pagination\SlidingPagination
     */
    public function filtrarPlanejamentoPorPagina($parametros, $pagina=1, $numeroItensPorPagina=500)
    {
        $opcoes       = [];
        $queryBuilder = $this->montaQueryBase();
        if ((isset($parametros[ConstanteParametros::CHAVE_ORDENACAO_COLUNA]) === true)&&(is_null($parametros[ConstanteParametros::CHAVE_ORDENACAO_COLUNA]) === false)) {
            $queryBuilder->orderBy($parametros[ConstanteParametros::CHAVE_ORDENACAO_COLUNA], $parametros[ConstanteParametros::CHAVE_ORDENACAO_SORT]);
            $opcoes[\Knp\Component\Pager\Paginator::SORT_FIELD_PARAMETER_NAME]     = "~";
            $opcoes[\Knp\Component\Pager\Paginator::SORT_DIRECTION_PARAMETER_NAME] = "~";
        }

        return \App\Helper\FunctionHelper::montaPaginatorPaginacao($queryBuilder, $pagina, $numeroItensPorPagina, $opcoes);
    }

    /**
     * Busca o planejamentoLicao por descricao
     *
     * @param string $descricao
     *
     * @return mixed|\App\Entity\Principal\PlanejamentoLicao|NULL
     */
    public function buscarPlanejamentoPorDescricao($descricao)
    {
        $queryBuilder = $this->montaQueryBase();
        $queryBuilder->where("UPPER(pl.descricao) = :descricao");
        $queryBuilder->setParameter("descricao", strtoupper($descricao));
        return $queryBuilder->getQuery()->getResult();
    }


}
