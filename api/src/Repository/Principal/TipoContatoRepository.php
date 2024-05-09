<?php

namespace App\Repository\Principal;

use App\Entity\Principal\TipoContato;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Symfony\Bridge\Doctrine\RegistryInterface;

/**
 * @method TipoContato|null find($id, $lockMode = null, $lockVersion = null)
 * @method TipoContato|null findOneBy(array $criteria, array $orderBy = null)
 * @method TipoContato[]    findAll()
 * @method TipoContato[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class TipoContatoRepository extends ServiceEntityRepository
{


    public function __construct(RegistryInterface $registry)
    {
        parent::__construct($registry, TipoContato::class);
    }

    /**
     * Monta Query Base
     *
     * @return \Doctrine\ORM\QueryBuilder
     */
    protected function montaQueryBase()
    {
        $queryBuilder = $this->createQueryBuilder("tc");
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
    public function filtraTipoContatoPorPagina($parametros, $pagina=1, $numeroItensPorPagina=50)
    {
        $queryBuilder = $this->montaQueryBase();
        return \App\Helper\FunctionHelper::montaPaginatorPaginacao($queryBuilder, $pagina, $numeroItensPorPagina);
    }

    /**
     * Busca o Registro pela ID informada
     *
     * @param int $id
     *
     * @return array|NULL
     */
    public function buscarPorId($id)
    {
        $queryBuilder = $this->montaQueryBase();
        $queryBuilder->where("tc.id = :id");
        $queryBuilder->setParameter("id", $id);
        return \App\Helper\FunctionHelper::retornaArrayNull($queryBuilder, true);
    }


}
