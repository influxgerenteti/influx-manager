<?php

namespace App\Repository\Principal;

use App\Entity\Principal\Livro;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Symfony\Bridge\Doctrine\RegistryInterface;
use App\Helper\ConstanteParametros;
use App\Helper\VariaveisCompartilhadas;

/**
 * @method Livro|null find($id, $lockMode = null, $lockVersion = null)
 * @method Livro|null findOneBy(array $criteria, array $orderBy = null)
 * @method Livro[]    findAll()
 * @method Livro[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class LivroRepository extends ServiceEntityRepository
{


    public function __construct(RegistryInterface $registry)
    {
        parent::__construct($registry, Livro::class);
    }

    /**
     * Monta a query base para Livro
     *
     * @return \Doctrine\ORM\QueryBuilder
     */
    protected function montaQueryBase()
    {
        $queryBuilder = $this->createQueryBuilder("li");
        $queryBuilder->addSelect("idio");
        $queryBuilder->addSelect("cu2");
        $queryBuilder->addSelect("it");
        $queryBuilder->addSelect("pli");
        $queryBuilder->addSelect("pl");
        $queryBuilder->addSelect("plan");
        $queryBuilder->addSelect("lics");
        $queryBuilder->addSelect("itemFranqueadas");
        $queryBuilder->leftJoin("li.curso", "cu");
        $queryBuilder->leftJoin("li.curso", "cu2");
        $queryBuilder->leftJoin("cu.idioma", "idio");
        $queryBuilder->leftJoin("li.item", "it");
        $queryBuilder->leftJoin("li.planejamento_licao", "pli");
        $queryBuilder->leftJoin("pli.licaos", "lics");
        $queryBuilder->leftJoin("li.proximo_livro", "pl");
        $queryBuilder->leftJoin("it.plano_conta", "plan");
        $queryBuilder->leftJoin("it.itemFranqueadas", "itemFranqueadas", "WITH", "itemFranqueadas.franqueada = :filtroFranqueada");

        $queryBuilder->setParameter("filtroFranqueada", VariaveisCompartilhadas::$franqueadaID);

        return $queryBuilder;
    }

    /**
     * Monta a query base para Livro
     *
     * @param \Doctrine\ORM\QueryBuilder $queryBuilder
     * @param array $parametros
     */
    protected function montaFiltros(&$queryBuilder, $parametros)
    {
        $bAdicionadoWhere = false;
        if ((isset($parametros[ConstanteParametros::CHAVE_DESCRICAO]) === true) && (empty($parametros[ConstanteParametros::CHAVE_DESCRICAO]) === false)) {
            $bAdicionadoWhere = true;
            $queryBuilder->where("UPPER(li.descricao) LIKE :descricao");
            $queryBuilder->setParameter("descricao", "%" . strtoupper($parametros[ConstanteParametros::CHAVE_DESCRICAO]) . "%");
        }

        if ((isset($parametros[ConstanteParametros::CHAVE_IDIOMA]) === true) && (empty($parametros[ConstanteParametros::CHAVE_IDIOMA]) === false)) {
            $wherePart = "idio.id = :idioma";
            if ($bAdicionadoWhere === true) {
                $queryBuilder->andWhere($wherePart);
            } else {
                $bAdicionadoWhere = true;
                $queryBuilder->where($wherePart);
            }

            $queryBuilder->setParameter("idioma", $parametros[ConstanteParametros::CHAVE_IDIOMA]);
        }

        if ((isset($parametros[ConstanteParametros::CHAVE_CURSO]) === true) && (empty($parametros[ConstanteParametros::CHAVE_CURSO]) === false)) {
            $wherePart = "cu.id = :curso";
            if ($bAdicionadoWhere === true) {
                $queryBuilder->andWhere($wherePart);
            } else {
                $bAdicionadoWhere = true;
                $queryBuilder->where($wherePart);
            }

            $queryBuilder->setParameter("curso", $parametros[ConstanteParametros::CHAVE_CURSO]);
        }
    }

    /**
     * Filtra o Livro por pagina
     *
     * @param array $parametros
     * @param number $pagina
     * @param number $numeroItensPorPagina
     *
     * @return \Knp\Component\Pager\Pagination\SlidingPagination
     */
    public function filtrarLivroPorPagina($parametros, $pagina=1, $numeroItensPorPagina=500)
    {
        $opcoes       = [];
        $queryBuilder = $this->montaQueryBase();

        if ((isset($parametros[ConstanteParametros::CHAVE_ORDENACAO_COLUNA]) === true)&&(is_null($parametros[ConstanteParametros::CHAVE_ORDENACAO_COLUNA]) === false)) {
            $queryBuilder->orderBy($parametros[ConstanteParametros::CHAVE_ORDENACAO_COLUNA], $parametros[ConstanteParametros::CHAVE_ORDENACAO_SORT]);
            $opcoes[\Knp\Component\Pager\Paginator::SORT_FIELD_PARAMETER_NAME]     = "~";
            $opcoes[\Knp\Component\Pager\Paginator::SORT_DIRECTION_PARAMETER_NAME] = "~";
        }

        $this->montaFiltros($queryBuilder, $parametros);

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
        $queryBuilder->where("li.id = :id");
        $queryBuilder->setParameter("id", $id);
        return $queryBuilder->getQuery()->getOneOrNullResult(\Doctrine\ORM\Query::HYDRATE_ARRAY);
    }


}
