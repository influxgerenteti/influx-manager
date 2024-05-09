<?php

namespace App\Repository\Principal;

use App\Entity\Principal\SistemaAvaliacao;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Symfony\Bridge\Doctrine\RegistryInterface;

/**
 * @method SistemaAvaliacao|null find($id, $lockMode = null, $lockVersion = null)
 * @method SistemaAvaliacao|null findOneBy(array $criteria, array $orderBy = null)
 * @method SistemaAvaliacao[]    findAll()
 * @method SistemaAvaliacao[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class SistemaAvaliacaoRepository extends ServiceEntityRepository
{


    public function __construct(RegistryInterface $registry)
    {
        parent::__construct($registry, SistemaAvaliacao::class);
    }

    /**
     * Monta query Sistema Avaliacao
     *
     * @return \Doctrine\ORM\QueryBuilder
     */
    protected function montaQueryBase()
    {
        $queryBuilder = $this->createQueryBuilder("sa");
        $queryBuilder->addSelect("ca");
        $queryBuilder->addSelect("cccq");
        $queryBuilder->leftJoin("sa.conceito_aprovacao", "ca");
        $queryBuilder->leftJoin("sa.conceito_corte_compromisso_qualidade", "cccq");
        return $queryBuilder;
    }

    /**
     * Filtra o Sistema Avaliacao por pagina
     *
     * @param array $parametros
     * @param number $pagina
     * @param number $numeroItensPorPagina
     *
     * @return \Knp\Component\Pager\Pagination\SlidingPagination
     */
    public function filtrarSistemaAvaliacaoPorPagina($parametros, $pagina=1, $numeroItensPorPagina=50)
    {
        $queryBuilder = $this->montaQueryBase();
        return \App\Helper\FunctionHelper::montaPaginatorPaginacao($queryBuilder, $pagina, $numeroItensPorPagina);
    }


}
