<?php

namespace App\Repository\Principal;

use App\Entity\Principal\TipoVisibilidade;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Symfony\Bridge\Doctrine\RegistryInterface;
use App\Helper\VariaveisCompartilhadas;

/**
 * @method TipoVisibilidade|null find($id, $lockMode = null, $lockVersion = null)
 * @method TipoVisibilidade|null findOneBy(array $criteria, array $orderBy = null)
 * @method TipoVisibilidade[]    findAll()
 * @method TipoVisibilidade[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class TipoVisibilidadeRepository extends ServiceEntityRepository
{


    public function __construct(RegistryInterface $registry)
    {
        parent::__construct($registry, TipoVisibilidade::class);
    }

    /**
     * Monta query padrao
     *
     * @return \Doctrine\ORM\QueryBuilder
     */
    protected function montaQueryBase()
    {
        $queryBuilder = $this->createQueryBuilder("tvs");
        $queryBuilder->innerJoin("tvs.franqueada", "franPrincipal");
        return $queryBuilder;
    }

    /**
     * Retorna todos os registros
     *
     * @return array|NULL
     */
    public function buscarTodos()
    {
        $queryBuilder = $this->montaQueryBase();
        $queryBuilder->addSelect("franqueadaLogada");
        $queryBuilder->leftJoin("tvs.franqueada", "franqueadaLogada");
        $queryBuilder->where("franPrincipal.franqueadora = :filtroFranqueadora");
        $queryBuilder->orWhere("franqueadaLogada.id = :franqueadaId");
        $queryBuilder->setParameter("filtroFranqueadora", true);
        $queryBuilder->setParameter("franqueadaId", VariaveisCompartilhadas::$franqueadaID);

        return \App\Helper\FunctionHelper::retornaArrayNull($queryBuilder);
    }


}
