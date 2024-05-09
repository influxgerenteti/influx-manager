<?php

namespace App\Repository\Principal;

use App\Entity\Principal\Idioma;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Symfony\Bridge\Doctrine\RegistryInterface;

/**
 * @method Idioma|null find($id, $lockMode = null, $lockVersion = null)
 * @method Idioma|null findOneBy(array $criteria, array $orderBy = null)
 * @method Idioma[]    findAll()
 * @method Idioma[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class IdiomaRepository extends ServiceEntityRepository
{


    public function __construct(RegistryInterface $registry)
    {
        parent::__construct($registry, Idioma::class);
    }

    /**
     * Filtra idiomas por pagina
     *
     * @param array $parametros
     * @param number $pagina
     * @param number $numeroItensPorPagina
     *
     * @return \Knp\Component\Pager\Pagination\SlidingPagination
     */
    public function filtraIdiomaPorPagina($parametros, $pagina=1, $numeroItensPorPagina=50)
    {
        $queryBuilder = $this->createQueryBuilder("idi");
        return \App\Helper\FunctionHelper::montaPaginatorPaginacao($queryBuilder, $pagina, $numeroItensPorPagina);
    }


}
