<?php

namespace App\Repository\Principal;

use App\Entity\Principal\MidiaFranqueada;
use App\Helper\ConstanteParametros;
use App\Helper\VariaveisCompartilhadas;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Symfony\Bridge\Doctrine\RegistryInterface;

/**
 * @method MidiaFranqueada|null find($id, $lockMode = null, $lockVersion = null)
 * @method MidiaFranqueada|null findOneBy(array $criteria, array $orderBy = null)
 * @method MidiaFranqueada[]    findAll()
 * @method MidiaFranqueada[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class MidiaFranqueadaRepository extends ServiceEntityRepository
{


    public function __construct(RegistryInterface $registry)
    {
        parent::__construct($registry, MidiaFranqueada::class);
    }

    /**
     * Monta a query base
     */
    public function montarQueryBase()
    {
        $queryBuilder = $this->createQueryBuilder("mfran");
        $queryBuilder->addSelect("mfran, fran, mid, midfran");
        $queryBuilder->join("mfran.franqueada", "fran");
        $queryBuilder->join("mfran.midia", "mid");
        $queryBuilder->join("mid.franqueada", "midfran");

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


}
