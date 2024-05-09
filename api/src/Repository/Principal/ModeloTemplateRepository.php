<?php

namespace App\Repository\Principal;

use App\Entity\Principal\Franqueada;
use App\Entity\Principal\ModeloTemplate;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Symfony\Bridge\Doctrine\RegistryInterface;
use App\Helper\ConstanteParametros;
use App\Helper\VariaveisCompartilhadas;
use App\Helper\SituacoesSistema;

/**
 * @method ModeloTemplate|null find($id, $lockMode = null, $lockVersion = null)
 * @method ModeloTemplate|null findOneBy(array $criteria, array $orderBy = null)
 * @method ModeloTemplate[]    findAll()
 * @method ModeloTemplate[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class ModeloTemplateRepository extends ServiceEntityRepository
{


    public function __construct(RegistryInterface $registry)
    {
        parent::__construct($registry, ModeloTemplate::class);
    }

    /**
     * Monta query principal
     *
     * @return \Doctrine\ORM\QueryBuilder
     */
    protected function montaQueryBase()
    {
        $queryBuilder = $this->createQueryBuilder("mt");
        $queryBuilder->addSelect("fran");
        $queryBuilder->join("mt.franqueada", "fran");
        $queryBuilder->leftJoin(Franqueada::class, "franLog", "WITH", "franLog = :franqueadaId");
        return $queryBuilder;
    }

    /**
     * Query para realizar fitlro de franqueada
     *
     * @param \Doctrine\ORM\QueryBuilder $queryBuilder
     */
    protected function filtrarFranqueada(&$queryBuilder)
    {
        $queryBuilder->andWhere(
            $queryBuilder->expr()->orX(
                $queryBuilder->expr()->eq("fran.id", ":franqueadaId"),
                $queryBuilder->expr()->eq("fran.franqueadora", 1)
            )
        );
        // Regra atual: só mostra os modelos criados pela franqueadora! Se for pra tirar essa regra, tirar a linha abaixo e rever o código
        //Bloco abaixo é para as franqueadoras que precisam ter um layout de contrato especifico
        
        $franqueadasNaoPadraoImpressaoContrato = [8]; //só colocar o id das franqueadas que precisam de um layout diferente;

        if (!in_array(VariaveisCompartilhadas::$franqueadaID, $franqueadasNaoPadraoImpressaoContrato)) {
            $queryBuilder->andWhere("fran.franqueadora = 1");
        }

        $queryBuilder->setParameter('franqueadaId', VariaveisCompartilhadas::$franqueadaID);
    }

    /**
     * Monta os filtros
     *
     * @param array $parametros
     * @param \Doctrine\ORM\QueryBuilder $queryBuilder
     */
    protected function montaFiltros($parametros, &$queryBuilder)
    {
        if ((isset($parametros[ConstanteParametros::CHAVE_SITUACAO]) === true)&&(empty($parametros[ConstanteParametros::CHAVE_SITUACAO]) === false)) {
            $queryBuilder->andWhere(
                $queryBuilder->expr()->orX(
                    $queryBuilder->expr()->in("mt.situacao", ":situacao"),
                    $queryBuilder->expr()->eq("fran.franqueadora", 1)
                )
            );
            $queryBuilder->setParameter("situacao", $parametros[ConstanteParametros::CHAVE_SITUACAO]);
        }
    }

    /**
     * Filtra os registros por pagina
     *
     * @param array $parametros
     * @param number $pagina
     * @param number $numeroItensPorPagina
     *
     * @return \Knp\Component\Pager\Pagination\SlidingPagination
     */
    public function filtrarModeloTemplatePorPagina($parametros, $pagina=1, $numeroItensPorPagina=50)
    {
        $opcoes       = [];
        $queryBuilder = $this->montaQueryBase();

        $this->filtrarFranqueada($queryBuilder, $parametros);
        $this->montaFiltros($parametros, $queryBuilder);

        $queryBuilder->addSelect("mtf");
        $queryBuilder->leftJoin("mt.modelo_template_franqueadas", "mtf", "WITH", "mtf.franqueada = :franqueadaId");

        $queryBuilder->andWhere(
            $queryBuilder->expr()->orX(
                $queryBuilder->expr()->eq("franLog.franqueadora", 1),
                $queryBuilder->expr()->eq("mt.situacao", ":situacaoAtivo")
            )
        );
        $queryBuilder->setParameter("situacaoAtivo", SituacoesSistema::SITUACAO_ATIVO);

        if ((isset($parametros[ConstanteParametros::CHAVE_ORDENACAO_COLUNA]) === true)&&(is_null($parametros[ConstanteParametros::CHAVE_ORDENACAO_COLUNA]) === false)) {
            $queryBuilder->orderBy($parametros[ConstanteParametros::CHAVE_ORDENACAO_COLUNA], $parametros[ConstanteParametros::CHAVE_ORDENACAO_SORT]);
            $opcoes[\Knp\Component\Pager\Paginator::SORT_FIELD_PARAMETER_NAME]     = "~";
            $opcoes[\Knp\Component\Pager\Paginator::SORT_DIRECTION_PARAMETER_NAME] = "~";
        }


        return \App\Helper\FunctionHelper::montaPaginatorPaginacao($queryBuilder, $pagina, $numeroItensPorPagina, $opcoes);
    }


    /**
     * Busca o registro através da ID
     *
     * @param int $id
     *
     * @return array|NULL
     */
    public function buscarRegistroPorId($id)
    {
        $queryBuilder = $this->montaQueryBase();
        $queryBuilder->andWhere("mt.id = :modeloTemplateId");
        $queryBuilder->setParameter("modeloTemplateId", $id);
        $this->filtrarFranqueada($queryBuilder);

        return \App\Helper\FunctionHelper::retornaArrayNull($queryBuilder, true);
    }


}
