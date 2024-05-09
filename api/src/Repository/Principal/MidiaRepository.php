<?php

namespace App\Repository\Principal;

use App\Entity\Principal\Midia;
use App\Helper\ConstanteParametros;
use App\Helper\VariaveisCompartilhadas;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Symfony\Bridge\Doctrine\RegistryInterface;

/**
 * @method Midia|null find($id, $lockMode = null, $lockVersion = null)
 * @method Midia|null findOneBy(array $criteria, array $orderBy = null)
 * @method Midia[]    findAll()
 * @method Midia[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class MidiaRepository extends ServiceEntityRepository
{


    public function __construct(RegistryInterface $registry)
    {
        parent::__construct($registry, Midia::class);
    }

    /**
     * Monta a query base
     */
    public function montarQueryBase()
    {
        $queryBuilder = $this->createQueryBuilder("m");
        $queryBuilder->addSelect("m, mfran, fran, mfranqueada");
        $queryBuilder->join("m.franqueada", "fran");
        $queryBuilder->leftJoin("m.midiaFranqueadas", "mfran");
        $queryBuilder->leftJoin("mfran.franqueada", "mfranqueada");

        return $queryBuilder;
    }


    /**
     * Query para realizar filtro por franqueada
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

        $queryBuilder->andWhere(
            $queryBuilder->expr()->orX(
                $queryBuilder->expr()->eq('mfranqueada.franqueadora', true),
                $queryBuilder->expr()->eq('mfranqueada.id', ':franqueada')
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

        if ($this->checkIsIssetAndIsNotNull($parametros, ConstanteParametros::CHAVE_DESCRICAO) === true) {
            $queryBuilder->andWhere("m.descricao LIKE :descricao");
            $queryBuilder->setParameter("descricao", "%" . $parametros[ConstanteParametros::CHAVE_DESCRICAO] . "%");
        }

        if ($this->checkIsIssetAndIsNotNull($parametros, ConstanteParametros::CHAVE_TIPO) === true) {
            $queryBuilder->andWhere("m.tipo = :tipo");
            $queryBuilder->setParameter("tipo", $parametros[ConstanteParametros::CHAVE_TIPO]);
        }

        if ($this->checkIsIssetAndIsNotNull($parametros, ConstanteParametros::CHAVE_SITUACAO) === true) {
            $queryBuilder->andWhere("m.situacao IN (:situacao)");
            $queryBuilder->setParameter("situacao", $parametros[ConstanteParametros::CHAVE_SITUACAO]);
        }
    }

    /**
     * Filtra a midia franqueada por pagina e numero de itens por pagina
     *
     * @param array $parametros Parametros enviados pela requisicao
     * @param int $pagina numero da pagina
     * @param int $numeroItensPorPagina numero de itens por pagina
     *
     * @return \App\Entity\Principal\MidiaFranqueada[] Resultados em formato de array
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
     * Verifica de so paramentro esta inserido e não nulo
     *
     * @param array $parametros Lista de parametros
     * @param string $var varivel para ser encontrada
     *
     * @return boolean
     */
    private function checkIsIssetAndIsNotNull ($parametros, $var)
    {
        if ((isset($parametros[$var]) === true)&&(empty($parametros[$var]) === false)) {
            return true;
        }

        return false;
    }

    /**
     * Busca o midia pela chave primaria
     *
     * @param integer $id Chave primaria da midia
     *
     * @return array|NULL
     */
    public function buscarPorId($id)
    {
        $queryBuilder = $this->montarQueryBase();
        $this->filtrarFranqueada($queryBuilder);
        $queryBuilder->andWhere("m.id = :id");
        $queryBuilder->setParameter("id", $id);

        return $queryBuilder->getQuery()->getOneOrNullResult(\Doctrine\ORM\Query::HYDRATE_ARRAY);
    }


}
