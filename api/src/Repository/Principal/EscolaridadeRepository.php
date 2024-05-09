<?php

namespace App\Repository\Principal;

use App\Entity\Principal\Escolaridade;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Symfony\Bridge\Doctrine\RegistryInterface;

/**
 * @method Escolaridade|null find($id, $lockMode = null, $lockVersion = null)
 * @method Escolaridade|null findOneBy(array $criteria, array $orderBy = null)
 * @method Escolaridade[]    findAll()
 * @method Escolaridade[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class EscolaridadeRepository extends ServiceEntityRepository
{


    public function __construct(RegistryInterface $registry)
    {
        parent::__construct($registry, Escolaridade::class);
    }

    /**
     * Monta a query base
     *
     * @return \Doctrine\ORM\QueryBuilder
     */
    protected function montaQueryBase()
    {
        $queryBuilder = $this->createQueryBuilder("esco");
        return $queryBuilder;
    }

    /**
     * Busca a escolaridade ativa
     *
     * @param array $parametros
     * @param number $pagina
     * @param number $numeroItensPorPagina
     *
     * @return \Knp\Component\Pager\Pagination\SlidingPagination
     */
    public function filtrarEscolaridadePorPagina($parametros, $pagina=1, $numeroItensPorPagina=50)
    {
        $queryBuilder = $this->montaQueryBase();
        $queryBuilder->where("esco.situacao = :situacao");
        $queryBuilder->setParameter("situacao", "A");
        return \App\Helper\FunctionHelper::montaPaginatorPaginacao($queryBuilder, $pagina, $numeroItensPorPagina);
    }


}
