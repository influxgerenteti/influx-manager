<?php

namespace App\Repository\Principal;

use App\Entity\Principal\ParcelaParcelamento;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Symfony\Bridge\Doctrine\RegistryInterface;

/**
 * @method ParcelaParcelamento|null find($id, $lockMode = null, $lockVersion = null)
 * @method ParcelaParcelamento|null findOneBy(array $criteria, array $orderBy = null)
 * @method ParcelaParcelamento[]    findAll()
 * @method ParcelaParcelamento[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class ParcelaParcelamentoRepository extends ServiceEntityRepository
{


    public function __construct(RegistryInterface $registry)
    {
        parent::__construct($registry, ParcelaParcelamento::class);
    }


    /**
     * Monta Query Principal
     *
     * @return \Doctrine\ORM\QueryBuilder
     */
    protected function montaQueryBase()
    {
        $queryBuilder = $this->createQueryBuilder("pp");
        $queryBuilder->addSelect("pp");

        return $queryBuilder;
    }

    /**
     * Busca o registro pelo id do parcelamento fornecido, podendo buscar todos ou apenas 1
     *
     * @param int $parcelamentoOperadoraCartaoId
     * @param bool $retornaTodos
     *
     * @return array|NULL
     */
    public function buscarRegistroPorParcelamentoOperadoraCartao($parcelamentoOperadoraCartaoId, $retornaTodos=true)
    {
        $queryBuilder = $this->montaQueryBase();
        $queryBuilder->leftJoin("pp.parcelamento_operadora_cartao", "poc");
        $queryBuilder->andWhere("poc.id = :id");
        $queryBuilder->setParameter("id", $parcelamentoOperadoraCartaoId);
        if ($retornaTodos === true) {
            return \App\Helper\FunctionHelper::retornaArrayNull($queryBuilder, true);
        } else {
            return \App\Helper\FunctionHelper::retornaPrimeiroResultado($queryBuilder, true);
        }
    }


}
