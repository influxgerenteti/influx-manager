<?php

namespace App\Repository\Principal;

use App\Entity\Principal\MetaFranqueada;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Symfony\Bridge\Doctrine\RegistryInterface;
use App\Helper\ConstanteParametros;
use App\Helper\VariaveisCompartilhadas;

/**
 * @method MetaFranqueada|null find($id, $lockMode = null, $lockVersion = null)
 * @method MetaFranqueada|null findOneBy(array $criteria, array $orderBy = null)
 * @method MetaFranqueada[]    findAll()
 * @method MetaFranqueada[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class MetaFranqueadaRepository extends ServiceEntityRepository
{


    public function __construct(RegistryInterface $registry)
    {
        parent::__construct($registry, MetaFranqueada::class);
    }

    public function buscarMetasFranquia($idFranquia, $parametros)
    {
        $query = $this->createQueryBuilder('metaFranqueada');
        $query->andWhere('metaFranqueada.franqueada = :franquia');
        $query->andWhere('metaFranqueada.mes = :mes');
        $query->andWhere('metaFranqueada.ano = :ano');

        $query->setParameter('franquia', $idFranquia);
        $query->setParameter('mes', $parametros[ConstanteParametros::CHAVE_MES]);
        $query->setParameter('ano', $parametros[ConstanteParametros::CHAVE_ANO]);

        return \App\Helper\FunctionHelper::retornaPrimeiroResultado($query, false);
    }


}
