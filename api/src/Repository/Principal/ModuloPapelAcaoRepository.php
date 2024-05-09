<?php

namespace App\Repository\Principal;

use App\Entity\Principal\ModuloPapelAcao;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Symfony\Bridge\Doctrine\RegistryInterface;

/**
 * @method ModuloPapelAcao|null find($id, $lockMode = null, $lockVersion = null)
 * @method ModuloPapelAcao|null findOneBy(array $criteria, array $orderBy = null)
 * @method ModuloPapelAcao[]    findAll()
 * @method ModuloPapelAcao[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class ModuloPapelAcaoRepository extends ServiceEntityRepository
{


    public function __construct(RegistryInterface $registry)
    {
        parent::__construct($registry, ModuloPapelAcao::class);
    }

    /**
     * Monta Query padrao
     *
     * @return \Doctrine\ORM\QueryBuilder
     */
    protected function montaQueryBase()
    {
        $queryBuilder = $this->createQueryBuilder("mpa");
        $queryBuilder->addSelect("mdl");
        $queryBuilder->addSelect("urlsis");
        $queryBuilder->addSelect("ppl");
        $queryBuilder->addSelect("act");
        $queryBuilder->leftJoin("mpa.modulo", "mdl");
        $queryBuilder->leftJoin("mdl.urlSistemas", "urlsis");
        $queryBuilder->leftJoin("mpa.papel", "ppl");
        $queryBuilder->leftJoin("mpa.acao_sistema", "act");
        return $queryBuilder;
    }

    /**
     * Busca as permissoes por rota e modulo
     *
     * @param string $rota
     * @param int $moduloId
     * @param int $acaoId
     *
     * @return NULL|\App\Entity\Principal\ModuloPapelAcao
     */
    public function buscaPorRotaModuloAcao($rota, $moduloId, $acaoId)
    {
        $queryBuilder = $this->montaQueryBase();
        $queryBuilder->where("urlsis.url_sistema = :rotaString");
        $queryBuilder->andWhere("mdl.id = :moduloId");
        $queryBuilder->andWhere("act.id = :acaoId");
        $queryBuilder->setParameters(
            [
                "rotaString" => $rota,
                "moduloId"   => $moduloId,
                "acaoId"     => $acaoId,
            ]
        );

        return $queryBuilder->getQuery()->getResult();
    }

    /**
     * Busca as permissoes módulo e ação
     *
     * @param int $moduloId
     * @param int $acaoId
     *
     * @return NULL|\App\Entity\Principal\ModuloPapelAcao
     */
    public function buscaPorModuloAcao($moduloId, $acaoId)
    {
        $queryBuilder = $this->montaQueryBase();
        $queryBuilder->where("mdl.id = :moduloId");
        $queryBuilder->andWhere("act.id = :acaoId");
        $queryBuilder->setParameters(
            [
                "moduloId" => $moduloId,
                "acaoId"   => $acaoId,
            ]
        );

        return $queryBuilder->getQuery()->getResult();
    }

/**
     * Busca as permissoes módulo e ação
     *
     * @param int $moduloId
     * @param int $acaoId
     *
     * @return NULL|\App\Entity\Principal\ModuloPapelAcao
     */
    public function buscaPermissaoPorModuloAcao($moduloId, $acaoId)
    {
        $queryBuilder = $this->montaQueryBase();
        $queryBuilder->where("mdl.id = :moduloId");
        $queryBuilder->andWhere("act.id = :acaoId");
        $queryBuilder->setParameters(
            [
                "moduloId" => $moduloId,
                "acaoId"   => $acaoId,
            ]
        );

        return $queryBuilder->getQuery()->getResult();
    }

}

