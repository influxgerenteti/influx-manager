<?php

namespace App\Repository\Principal;

use App\Entity\Principal\ChecklistAtividadeRealizada;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Symfony\Bridge\Doctrine\RegistryInterface;
use App\Helper\VariaveisCompartilhadas;
use App\Helper\ConstanteParametros;

/**
 * @method ChecklistAtividadeRealizada|null find($id, $lockMode = null, $lockVersion = null)
 * @method ChecklistAtividadeRealizada|null findOneBy(array $criteria, array $orderBy = null)
 * @method ChecklistAtividadeRealizada[]    findAll()
 * @method ChecklistAtividadeRealizada[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class ChecklistAtividadeRealizadaRepository extends ServiceEntityRepository
{


    public function __construct(RegistryInterface $registry)
    {
        parent::__construct($registry, ChecklistAtividadeRealizada::class);
    }

    /**
     * Monta query principal
     *
     * @return \Doctrine\ORM\QueryBuilder
     */
    protected function montaQueryBase()
    {
        $queryBuilder = $this->createQueryBuilder("chkar");
        $queryBuilder->addSelect("chk");
        $queryBuilder->addSelect("ch");
        $queryBuilder->leftJoin("chkar.checklist_atividade", "chk");
        $queryBuilder->leftJoin("chkar.checklist", "ch");
        $queryBuilder->leftJoin("chkar.franqueada", "fran");
        $queryBuilder->leftJoin("chkar.usuario", "u");
        return $queryBuilder;
    }

    /**
     * Monta os filtros conforme passado
     *
     * @param \Doctrine\ORM\QueryBuilder $queryBuilder
     * @param array $parametros
     */
    protected function montaFiltros(&$queryBuilder, $parametros)
    {
        if (count($parametros[ConstanteParametros::CHAVE_ATIVIDADES]) > 0) {
            $queryBuilder->andWhere("chk.id IN (:checklistAtividadeIds)");
            $queryBuilder->setParameter("checklistAtividadeIds", $parametros[ConstanteParametros::CHAVE_ATIVIDADES]);
        }

        if ($parametros[ConstanteParametros::CHAVE_FILTRO_DIARIO] === true) {
            $queryBuilder->andWhere(
                $queryBuilder->expr()->andX(
                    $queryBuilder->expr()->eq("chk.tipo_atividade", "'D'"),
                    $queryBuilder->expr()->gte("chkar.data_conclusao", "'" . $parametros[ConstanteParametros::CHAVE_DIA_ATUAL] . " 00:00:01'"),
                    $queryBuilder->expr()->lte("chkar.data_conclusao", "'" . $parametros[ConstanteParametros::CHAVE_DIA_ATUAL] . " 23:59:59'")
                )
            );
        }

        if ($parametros[ConstanteParametros::CHAVE_FILTRO_SEMANAL] === true) {
            $queryBuilder->andWhere(
                $queryBuilder->expr()->andX(
                    $queryBuilder->expr()->eq("chk.tipo_atividade", "'S'"),
                    $queryBuilder->expr()->eq("WEEK(chkar.data_conclusao, 1)", "'" . $parametros[ConstanteParametros::CHAVE_NUMERO_SEMANA] . "'"),
                    $queryBuilder->expr()->eq("YEAR(chkar.data_conclusao)", "'" . $parametros[ConstanteParametros::CHAVE_ANO] . "'")
                )
            );
        }

        if ($parametros[ConstanteParametros::CHAVE_FILTRO_MENSAL] === true) {
            $queryBuilder->andWhere(
                $queryBuilder->expr()->andX(
                    $queryBuilder->expr()->eq("chk.tipo_atividade", "'M'"),
                    $queryBuilder->expr()->eq("MONTH(chkar.data_conclusao)", "'" . $parametros[ConstanteParametros::CHAVE_NUMERO_MES] . "'"),
                    $queryBuilder->expr()->eq("YEAR(chkar.data_conclusao)", "'" . $parametros[ConstanteParametros::CHAVE_ANO] . "'")
                )
            );
        }

        if ($parametros[ConstanteParametros::CHAVE_FILTRO_ATEMPORAL] === true) {
            $queryBuilder->andWhere(
                $queryBuilder->expr()->andX(
                    $queryBuilder->expr()->eq("chk.tipo_atividade", "'A'")
                )
            );
        }
    }

    /**
     * Busca as atividades realizadas por usuario
     *
     * @param array $parametros
     *
     * @return array|NULL
     */
    public function buscarPorUsuario($parametros)
    {
        $queryBuilder = $this->montaQueryBase();
        $queryBuilder->where("fran.id = :franqueadaId");
        $queryBuilder->andWhere("u.id = :usuarioId");
        $queryBuilder->setParameter("franqueadaId", VariaveisCompartilhadas::$franqueadaID);
        $queryBuilder->setParameter("usuarioId", $parametros[ConstanteParametros::CHAVE_USUARIO]);
        $this->montaFiltros($queryBuilder, $parametros);
        return \App\Helper\FunctionHelper::retornaArrayNull($queryBuilder);
    }


}
