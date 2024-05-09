<?php

namespace App\Repository\Principal;

use App\Entity\Principal\ParcelamentoOperadoraCartao;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Symfony\Bridge\Doctrine\RegistryInterface;
use App\Helper\VariaveisCompartilhadas;

/**
 * @method ParcelamentoOperadoraCartao|null find($id, $lockMode = null, $lockVersion = null)
 * @method ParcelamentoOperadoraCartao|null findOneBy(array $criteria, array $orderBy = null)
 * @method ParcelamentoOperadoraCartao[]    findAll()
 * @method ParcelamentoOperadoraCartao[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class ParcelamentoOperadoraCartaoRepository extends ServiceEntityRepository
{


    public function __construct(RegistryInterface $registry)
    {
        parent::__construct($registry, ParcelamentoOperadoraCartao::class);
    }

    /**
     * Monta Query Principal
     *
     * @return \Doctrine\ORM\QueryBuilder
     */
    protected function montaQueryBase()
    {
        $queryBuilder = $this->createQueryBuilder("poc");
        $queryBuilder->addSelect("pp");
        $queryBuilder->leftJoin("poc.parcelaParcelamentos", "pp");
        $queryBuilder->leftJoin("poc.operadora_cartao", "op");
        $queryBuilder->leftJoin("op.franqueada", "fran");

        return $queryBuilder;
    }

    /**
     *
     *
     * @param int $id
     *
     * @return array|NULL
     */
    public function buscarRegistroPorId($id)
    {
        $queryBuilder = $this->montaQueryBase();
        $queryBuilder->andWhere("poc.id = :id");
        $queryBuilder->setParameter("id", $id);
        $queryBuilder->andWhere("fran.id = :franqueadaId");
        $queryBuilder->setParameter("id", VariaveisCompartilhadas::$franqueadaID);
        return \App\Helper\FunctionHelper::retornaArrayNull($queryBuilder, true);
    }


}
