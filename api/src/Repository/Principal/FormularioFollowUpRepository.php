<?php

namespace App\Repository\Principal;

use App\Entity\Principal\FormularioFollowUp;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Symfony\Bridge\Doctrine\RegistryInterface;
use App\Helper\ConstanteParametros;

/**
 * @method FormularioFollowUp|null find($id, $lockMode = null, $lockVersion = null)
 * @method FormularioFollowUp|null findOneBy(array $criteria, array $orderBy = null)
 * @method FormularioFollowUp[]    findAll()
 * @method FormularioFollowUp[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class FormularioFollowUpRepository extends ServiceEntityRepository
{


    public function __construct(RegistryInterface $registry)
    {
        parent::__construct($registry, FormularioFollowUp::class);
    }

    /**
     * Monta query base
     *
     * @return \Doctrine\ORM\QueryBuilder
     */
    protected function montaQueryBase()
    {
        $queryBuilder = $this->createQueryBuilder("ffup");
        $queryBuilder->addSelect("usu");
        $queryBuilder->addSelect("ffupc");
        $queryBuilder->leftJoin("ffup.usuario_alteracao", "usu");
        $queryBuilder->leftJoin("ffup.formularioFollowUpCampos", "ffupc");

        return $queryBuilder;
    }

    /**
     * Monta filtros
     *
     * @param array $parametros
     * @param \Doctrine\ORM\QueryBuilder $queryBuilder
     */
    protected function montaFiltros($parametros, &$queryBuilder)
    {
        if ((isset($parametros[ConstanteParametros::CHAVE_FOLLOW_UP_INICIAL]) === true) && ($parametros[ConstanteParametros::CHAVE_FOLLOW_UP_INICIAL] !== "")) {
            $queryBuilder->andWhere("ffup.follow_up_inicial = :fpInicial");
            $queryBuilder->setParameter("fpInicial", $parametros[ConstanteParametros::CHAVE_FOLLOW_UP_INICIAL]);
        }

        if ((isset($parametros[ConstanteParametros::CHAVE_SITUACAO]) === true)&&(empty($parametros[ConstanteParametros::CHAVE_SITUACAO]) === false)) {
            $queryBuilder->andWhere("ffup.situacao IN (:situacao)");
            $queryBuilder->setParameter("situacao", $parametros[ConstanteParametros::CHAVE_SITUACAO]);
        }

        if ((isset($parametros[ConstanteParametros::CHAVE_FILTRO_TIPO_FORMULARIO]) === true)&&(empty($parametros[ConstanteParametros::CHAVE_FILTRO_TIPO_FORMULARIO]) === false)) {
            $queryBuilder->andWhere("ffup.tipo_formulario IN (:tipoFormulario)");
            $queryBuilder->setParameter("tipoFormulario", $parametros[ConstanteParametros::CHAVE_FILTRO_TIPO_FORMULARIO]);
        }
    }

    /**
     * Filtrar por pagina
     *
     * @param array $parametros
     * @param number $pagina
     * @param number $numeroItensPorPagina
     *
     * @return \Knp\Component\Pager\Pagination\SlidingPagination
     */
    public function filtrarFormularioFollowUpPorPagina($parametros, $pagina=1, $numeroItensPorPagina=50)
    {
        $queryBuilder = $this->montaQueryBase();
        $this->montaFiltros($parametros, $queryBuilder);
        if (((bool) $parametros[ConstanteParametros::CHAVE_APENAS_PRIMEIRO]) === true) {
            $numeroItensPorPagina = 1;
        }

        return \App\Helper\FunctionHelper::montaPaginatorPaginacao($queryBuilder, $pagina, $numeroItensPorPagina);
    }

    /**
     * Retorna array com o primeiro registro
     *
     * @param int $id
     *
     * @return array|NULL
     */
    public function buscarRegistroPorId($id)
    {
        $queryBuilder = $this->montaQueryBase();
        $queryBuilder->where("ffup.id = :formularioId");
        $queryBuilder->setParameter("formularioId", $id);

        return \App\Helper\FunctionHelper::retornaArrayNull($queryBuilder, true);
    }


}
