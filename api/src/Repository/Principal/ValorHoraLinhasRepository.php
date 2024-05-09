<?php

namespace App\Repository\Principal;

use App\Entity\Principal\ValorHoraLinhas;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Symfony\Bridge\Doctrine\RegistryInterface;

/**
 * @method ValorHoraLinhas|null find($id, $lockMode = null, $lockVersion = null)
 * @method ValorHoraLinhas|null findOneBy(array $criteria, array $orderBy = null)
 * @method ValorHoraLinhas[]    findAll()
 * @method ValorHoraLinhas[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class ValorHoraLinhasRepository extends ServiceEntityRepository
{


    public function __construct(RegistryInterface $registry)
    {
        parent::__construct($registry, ValorHoraLinhas::class);
    }

    /**
     * Monta a query base para ValorHoraLinhas
     *
     * @return \Doctrine\ORM\QueryBuilder
     */
    protected function montaQueryBase()
    {
        $queryBuilder = $this->createQueryBuilder("vlhl");
        return $queryBuilder;
    }
    /**
     * Filtra o ValorHoraLinhas por pagina
     *
     * @param array $parametros
     * @param number $pagina
     * @param number $numeroItensPorPagina
     *
     * @return \Knp\Component\Pager\Pagination\SlidingPagination
     */
    public function filtrarValorHoraLinhasPorPagina($parametros, $pagina=1, $numeroItensPorPagina=999)
    {
        $queryBuilder = $this->montaQueryBase();
        $queryBuilder->where("vlhl.situacao = :situacao");
        $queryBuilder->setParameter("situacao", "A");
        return \App\Helper\FunctionHelper::montaPaginatorPaginacao($queryBuilder, $pagina, $numeroItensPorPagina);
    }


}
