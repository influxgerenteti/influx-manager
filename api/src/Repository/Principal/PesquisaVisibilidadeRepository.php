<?php

namespace App\Repository\Principal;

use App\Entity\Principal\PesquisaVisibilidade;
use App\Helper\ConstanteParametros;
use App\Helper\VariaveisCompartilhadas;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Symfony\Bridge\Doctrine\RegistryInterface;

/**
 * @method PesquisaVisibilidade|null find($id, $lockMode = null, $lockVersion = null)
 * @method PesquisaVisibilidade|null findOneBy(array $criteria, array $orderBy = null)
 * @method PesquisaVisibilidade[]    findAll()
 * @method PesquisaVisibilidade[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class PesquisaVisibilidadeRepository extends ServiceEntityRepository
{


    public function __construct(RegistryInterface $registry)
    {
        parent::__construct($registry, PesquisaVisibilidade::class);
    }

    /**
     * Monta a query base
     */
    public function montarQueryBase()
    {
        $queryBuilder = $this->createQueryBuilder("pv");
        $queryBuilder->addSelect("pv, fran");
        $queryBuilder->join("pv.franqueada", "fran");

        return $queryBuilder;
    }

    /**
     * Query para realizar fitlro de franqueada
     *
     * @param \Doctrine\ORM\QueryBuilder $queryBuilder
     */
    private function filtrarFranqueada(&$queryBuilder)
    {
        $queryBuilder->andWhere(
            $queryBuilder->expr()->orX(
                $queryBuilder->expr()->eq('fran.franqueadora', true),
                $queryBuilder->expr()->eq('fran.id', ':franqueada')
            )
        );

        $queryBuilder->setParameter('franqueada', VariaveisCompartilhadas::$franqueadaID);
    }

    /**
     * Filtros por parametros
     *
     * @param array $parametros
     * @param \Doctrine\ORM\QueryBuilder $queryBuilder
     */
    private function montarFiltros ($parametros, &$queryBuilder)
    {

        if ($this->checkIsIssetAndIsNotNull($parametros, ConstanteParametros::CHAVE_NOME) === true) {
            $queryBuilder->andWhere("pv.nome LIKE :nome");
            $queryBuilder->setParameter("nome", "%" . $parametros[ConstanteParametros::CHAVE_NOME] . "%");
        }

        if ($this->checkIsIssetAndIsNotNull($parametros, ConstanteParametros::CHAVE_TIPO) === true) {
            $queryBuilder->andWhere("pv.tipo = :tipo");
            $queryBuilder->setParameter("tipo", $parametros[ConstanteParametros::CHAVE_TIPO]);
        }

        if ($this->checkIsIssetAndIsNotNull($parametros, ConstanteParametros::CHAVE_SITUACAO) === true) {
            $queryBuilder->andWhere("pv.situacao IN (:situacao)");
            $queryBuilder->setParameter("situacao", $parametros[ConstanteParametros::CHAVE_SITUACAO]);
        }
    }

    private function checkIsIssetAndIsNotNull ($parametros, $var)
    {
        if ((isset($parametros[$var]) === true)&&(empty($parametros[$var]) === false)) {
            return true;
        }

        return false;
    }

    /**
     * Filtra a pesquisa de visibilidade por pagina e numero de itens por pagina
     *
     * @param array $parametros Parametros enviados pela requisicao
     * @param int $pagina numero da pagina
     * @param int $numeroItensPorPagina numero de itens por pagina
     *
     * @return \App\Entity\Principal\PesquisaVisibilidade[] Resultados em formato de array
     */
    public function filtrarPorPagina($parametros, $pagina=1, $numeroItensPorPagina=50)
    {
        $opcoes       = [];
        $queryBuilder = $this->montarQueryBase();
        $this->filtrarFranqueada($queryBuilder);
        $this->montarFiltros($parametros, $queryBuilder);

        return \App\Helper\FunctionHelper::montaPaginatorPaginacao($queryBuilder, $pagina, $numeroItensPorPagina, $opcoes);
    }

    /**
     * Busca pessoa por ID
     *
     * @param integer $id
     * @param boolean $retornarObjeto Se deve retornar como objeto
     *
     * @return \App\Entity\Principal\PesquisaVisibilidade|null
     */
    public function buscarPorId ($id, $retornarObjeto=false)
    {
        $queryBuilder = $this->montarQueryBase();
        $queryBuilder->andWhere('pv.id = :id');
        $queryBuilder->setParameter('id', $id);

        if ($retornarObjeto === false) {
            $hydrate = \Doctrine\ORM\Query::HYDRATE_ARRAY;
        } else {
            $hydrate = \Doctrine\ORM\Query::HYDRATE_OBJECT;
        }

        return $queryBuilder->getQuery()->getOneOrNullResult($hydrate);

    }


}
