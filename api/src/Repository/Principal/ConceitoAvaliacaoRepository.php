<?php

namespace App\Repository\Principal;

use App\Entity\Principal\ConceitoAvaliacao;
use App\Helper\ConstanteParametros;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Symfony\Bridge\Doctrine\RegistryInterface;
/**
 * @method ConceitoAvaliacao|null find($id, $lockMode = null, $lockVersion = null)
 * @method ConceitoAvaliacao|null findOneBy(array $criteria, array $orderBy = null)
 * @method ConceitoAvaliacao[]    findAll()
 * @method ConceitoAvaliacao[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class ConceitoAvaliacaoRepository extends ServiceEntityRepository
{


    public function __construct(RegistryInterface $registry)
    {
        parent::__construct($registry, ConceitoAvaliacao::class);
    }

    /**
     * Busca os registros da tabela utilizando left join
     *
     * @return \Doctrine\ORM\QueryBuilder
     */
    protected function montaQueryBase()
    {
        $queryBuilder = $this->createQueryBuilder("ca");
        return $queryBuilder;
    }

    /**
     * Filtra o tipo de contato por pagina
     *
     * @param array $parametros
     * @param number $pagina
     * @param number $numeroItensPorPagina
     *
     * @return \Knp\Component\Pager\Pagination\SlidingPagination
     */
    public function filtrarPorPagina ($parametros, $pagina=1, $numeroItensPorPagina=50)
    {

        $queryBuilder = $this->montaQueryBase();
        return \App\Helper\FunctionHelper::montaPaginatorPaginacao($queryBuilder, $pagina, $numeroItensPorPagina);
    }


}
