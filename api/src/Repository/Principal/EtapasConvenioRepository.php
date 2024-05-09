<?php

namespace App\Repository\Principal;

use App\Entity\Principal\EtapasConvenio;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Symfony\Bridge\Doctrine\RegistryInterface;

/**
 * @method EtapasConvenio|null find($id, $lockMode = null, $lockVersion = null)
 * @method EtapasConvenio|null findOneBy(array $criteria, array $orderBy = null)
 * @method EtapasConvenio[]    findAll()
 * @method EtapasConvenio[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class EtapasConvenioRepository extends ServiceEntityRepository
{


    public function __construct(RegistryInterface $registry)
    {
        parent::__construct($registry, EtapasConvenio::class);
    }

    /**
     * Monta query base de busca
     *
     * @return \Doctrine\ORM\QueryBuilder
     */
    protected function montaQueryBase()
    {
        $queryBuilder = $this->createQueryBuilder("ec");
        $queryBuilder->addSelect("npw");
        $queryBuilder->leftJoin("ec.negociacao_parceria_workflow", 'npw');
        return $queryBuilder;
    }

    /**
     * Realiza o filtro de cheques por paginas
     *
     * @param array $parametros
     * @param number $pagina
     * @param number $numeroItensPorPagina
     *
     * @return \Knp\Component\Pager\Pagination\SlidingPagination
     */
    public function filtrarEtapasConvenioPorPagina($parametros, $pagina=1, $numeroItensPorPagina=50)
    {
        $opcoes       = [];
        $queryBuilder = $this->montaQueryBase();
        return \App\Helper\FunctionHelper::montaPaginatorPaginacao($queryBuilder, $pagina, $numeroItensPorPagina, $opcoes);
    }


}
