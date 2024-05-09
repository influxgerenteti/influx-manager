<?php

namespace App\Repository\Principal;

use App\Entity\Principal\DiasSubsequentes;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Symfony\Bridge\Doctrine\RegistryInterface;

/**
 * @method DiasSubsequentes|null find($id, $lockMode = null, $lockVersion = null)
 * @method DiasSubsequentes|null findOneBy(array $criteria, array $orderBy = null)
 * @method DiasSubsequentes[]    findAll()
 * @method DiasSubsequentes[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class DiasSubsequentesRepository extends ServiceEntityRepository
{


    public function __construct(RegistryInterface $registry)
    {
        parent::__construct($registry, DiasSubsequentes::class);
    }

    /**
     * Monta QueryBase para diasSubsequentes partindo da Franqueada
     *
     * @return \Doctrine\ORM\QueryBuilder
     */
    protected function montaQueryBaseFranqueada()
    {
        $queryBuilder = $this->_em->createQueryBuilder();
        $queryBuilder->select("fr");
        $queryBuilder->addSelect("dsq");
        $queryBuilder->from(\App\Entity\Principal\Franqueada::class, "fr");
        $queryBuilder->leftJoin("fr.diasSubsequentes", "dsq");

        return $queryBuilder;
    }

    /**
     * Monta QueryBase para listar DiasSubsequentes
     *
     * @return \Doctrine\ORM\QueryBuilder
     */
    protected function montaQueryBase()
    {
        $queryBuilder = $this->createQueryBuilder("dsq");
        return $queryBuilder;
    }

    /**
     * Busca os dias subsequentes atraves da franqueada
     *
     * @param int $idFranqueada
     *
     * @return array|NULL
     */
    public function buscaDiasSubsequentesPorFranqueada($idFranqueada)
    {
        $queryBuilder = $this->montaQueryBaseFranqueada();
        $queryBuilder->where("fr.id = :idFranqueada");
        $queryBuilder->setParameter("idFranqueada", $idFranqueada);

        return \App\Helper\FunctionHelper::retornaArrayNull($queryBuilder, true);
    }

    /**
     * Busca todos os DiasSubsequentes da Base
     *
     * @return array|NULL
     */
    public function buscaDiasSubsequentes()
    {
        $queryBuilder = $this->montaQueryBase();
        return \App\Helper\FunctionHelper::retornaArrayNull($queryBuilder);
    }


}
