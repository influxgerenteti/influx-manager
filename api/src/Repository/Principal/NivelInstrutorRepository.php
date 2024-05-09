<?php

namespace App\Repository\Principal;

use App\Entity\Principal\NivelInstrutor;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Symfony\Bridge\Doctrine\RegistryInterface;

/**
 * @method NivelInstrutor|null find($id, $lockMode = null, $lockVersion = null)
 * @method NivelInstrutor|null findOneBy(array $criteria, array $orderBy = null)
 * @method NivelInstrutor[]    findAll()
 * @method NivelInstrutor[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class NivelInstrutorRepository extends ServiceEntityRepository
{


    public function __construct(RegistryInterface $registry)
    {
        parent::__construct($registry, NivelInstrutor::class);
    }

    /**
     * Monta query base para nivel de instrutor
     *
     * @return \Doctrine\ORM\QueryBuilder
     */
    protected function montaQueryBase()
    {
        $queryBuilder = $this->createQueryBuilder("ni");
        return $queryBuilder;
    }

    /**
     * Filtra Niveis de instrutor por pagina
     *
     * @param array $parametros
     * @param number $pagina
     * @param number $numeroItensPorPagina
     *
     * @return \Knp\Component\Pager\Pagination\SlidingPagination
     */
    public function filtrarNivelInstrutorPorPagina($parametros, $pagina=1, $numeroItensPorPagina=50)
    {
        $queryBuilder = $this->montaQueryBase();
        $queryBuilder->where("ni.situacao = :situacao");
        $queryBuilder->setParameter("situacao", "A");
        return \App\Helper\FunctionHelper::montaPaginatorPaginacao($queryBuilder, $pagina, $numeroItensPorPagina);
    }


}
