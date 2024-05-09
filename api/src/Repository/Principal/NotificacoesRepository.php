<?php

namespace App\Repository\Principal;

use App\Entity\Principal\Notificacoes;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Symfony\Bridge\Doctrine\RegistryInterface;
use App\Helper\ConstanteParametros;

/**
 * @method Notificacoes|null find($id, $lockMode = null, $lockVersion = null)
 * @method Notificacoes|null findOneBy(array $criteria, array $orderBy = null)
 * @method Notificacoes[]    findAll()
 * @method Notificacoes[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class NotificacoesRepository extends ServiceEntityRepository
{


    public function __construct(RegistryInterface $registry)
    {
        parent::__construct($registry, Notificacoes::class);
    }

    /**
     * Monta Query Builder
     *
     * @return \Doctrine\ORM\QueryBuilder
     */
    protected function montaQueryBase()
    {
        $queryBuilder = $this->createQueryBuilder("nf");
        $queryBuilder->addSelect("u");
        $queryBuilder->addSelect("fran");
        $queryBuilder->leftJoin("nf.usuario", "u");
        $queryBuilder->leftJoin("nf.franqueada", "fran");

        return $queryBuilder;
    }

    /**
     * Monta os filtros na qwuery
     *
     * @param array $parametros
     * @param \Doctrine\ORM\QueryBuilder $queryBuilder
     */
    protected function montaFiltros($parametros, &$queryBuilder)
    {
        if ((isset($parametros[ConstanteParametros::CHAVE_USUARIO]) === true)&&(empty($parametros[ConstanteParametros::CHAVE_USUARIO]) === false)) {
            $queryBuilder->andWhere("u.id = :usuarioId");
            $queryBuilder->setParameter("usuarioId", $parametros[ConstanteParametros::CHAVE_USUARIO]);
        }

        // Comentado pois futuramente pode ser que solicitem a filtragem por franqueada
        // if((isset($parametros[ConstanteParametros::CHAVE_FRANQUEADA])===true)&&(empty($parametros[ConstanteParametros::CHAVE_FRANQUEADA])===false)) {
        // $queryBuilder->andWhere("fran.id = :franqueadaId");
        // $queryBuilder->setParameter("franqueadaId", $parametros[ConstanteParametros::CHAVE_FRANQUEADA]);
        // }
        if ((isset($parametros[ConstanteParametros::CHAVE_DATA_PRORROGACAO]) === true)&&(empty($parametros[ConstanteParametros::CHAVE_DATA_PRORROGACAO]) === false)) {
            $queryBuilder->andWhere("nf.data_prorrogacao <= :dataProrrogacao");
            $queryBuilder->setParameter("dataProrrogacao", $parametros[ConstanteParametros::CHAVE_DATA_PRORROGACAO]);
        }

        if (isset($parametros[ConstanteParametros::CHAVE_IS_LIDA]) === true) {
            $queryBuilder->andWhere("nf.is_lida = :isLida");
            $queryBuilder->setParameter("isLida", $parametros[ConstanteParametros::CHAVE_IS_LIDA]);
        }

    }

    /**
     * Filtra as notificacoes
     *
     * @param array $parametros
     *
     * @return array|NULL
     */
    public function filtraNotificacoes($parametros)
    {
        $queryBuilder = $this->montaQueryBase();
        $this->montaFiltros($parametros, $queryBuilder);
        $queryBuilder->select("nf.id, nf.mensagem, nf.is_lida, nf.data_notificacao, nf.data_prorrogacao, nf.class_css, fran.id as franqueada_id, fran.nome as franqueada_nome");
        return \App\Helper\FunctionHelper::retornaArrayNull($queryBuilder);
    }


}
