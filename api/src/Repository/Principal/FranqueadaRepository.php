<?php

namespace App\Repository\Principal;

use App\Entity\Principal\Franqueada;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Symfony\Bridge\Doctrine\RegistryInterface;
use App\Helper\ConstanteParametros;

/**
 *
 * @method Franqueada|null find($id, $lockMode = null, $lockVersion = null)
 * @method Franqueada|null findOneBy(array $criteria, array $orderBy = null)
 * @method Franqueada[]    findAll()
 * @method Franqueada[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class FranqueadaRepository extends ServiceEntityRepository
{


    public function __construct(RegistryInterface $registry)
    {
        parent::__construct($registry, Franqueada::class);
    }


    /**
     * Monta a query de Relacionamento entre Franqueada X Usuario
     *
     * @return \Doctrine\ORM\QueryBuilder
     */
    protected function montarQueryFranqueadaUsuario()
    {
        $queryBuilder = $this->createQueryBuilder("fran");
        $queryBuilder->addSelect("usuFran, p, r, c");
        $queryBuilder->leftJoin('fran.conta_padrao', 'c');
        $queryBuilder->leftJoin('fran.tipo_movimento_conta_pagar', 'p');
        $queryBuilder->leftJoin('fran.tipo_movimento_conta_receber', 'r');
        $queryBuilder->join("fran.usuarios", "usuFran");
        $queryBuilder->andWhere('fran.excluido = 0');

        return $queryBuilder;
    }

    /**
     * Filtra as franqueadas por pagina e numero de itens por pagina

     * @param array $parametros
     * @param integer $pagina
     * @param \App\Entity\Principal\Usuario $usuario
     * @param integer $numeroItensPorPagina
     *
     * @return array Retorna o resultado em array
     */
    public function filtraFranqueadasPorPagina($parametros, $pagina=1, \App\Entity\Principal\Usuario $usuario=null, $numeroItensPorPagina=60)
    {
        $opcoes       = [];
        $queryBuilder = $this->montarQueryFranqueadaUsuario();
        if ((isset($parametros[ConstanteParametros::CHAVE_ORDENACAO_COLUNA]) === true)&&(is_null($parametros[ConstanteParametros::CHAVE_ORDENACAO_COLUNA]) === false)) {
            $queryBuilder->orderBy($parametros[ConstanteParametros::CHAVE_ORDENACAO_COLUNA], $parametros[ConstanteParametros::CHAVE_ORDENACAO_SORT]);
            $opcoes[\Knp\Component\Pager\Paginator::SORT_FIELD_PARAMETER_NAME]     = "~";
            $opcoes[\Knp\Component\Pager\Paginator::SORT_DIRECTION_PARAMETER_NAME] = "~";
        }

        if (is_null($usuario) === false) {
            $queryBuilder->andWhere('usuFran = :usuario')
                ->setParameter('usuario', $usuario);
        }

        return \App\Helper\FunctionHelper::montaPaginatorPaginacao($queryBuilder, $pagina, $numeroItensPorPagina, $opcoes);
    }

    /**
     * Busca a Franqueada com os usuarios
     *
     * @param int $franqueadaID Chave primaria Franqueada
     *
     * @return NULL|\App\Entity\Principal\Franqueada
     */
    public function buscarFranqueadaEUsuarios($franqueadaID)
    {
        $franqueadaORM = $this->montarQueryFranqueadaUsuario();
        $franqueadaORM->addSelect('estado, cidade');
        $franqueadaORM->leftJoin('fran.estado', 'estado');
        $franqueadaORM->leftJoin('fran.cidade', 'cidade');
        $franqueadaORM->andWhere("fran.id = $franqueadaID");
        $resultados = $franqueadaORM->getQuery()->getOneOrNullResult(2);

        return $resultados;
    }

    /**
     * Busca a Franqueada com a conta padrão
     *
     * @param int $franqueadaID Chave primaria Franqueada
     *
     * @return NULL|\App\Entity\Principal\Franqueada
     */
    public function buscarFranqueadaEContaPadrao ($franqueadaID)
    {
        return $this->createQueryBuilder('fran')
            ->addSelect('contaPadrao')
            ->andWhere("fran.id = $franqueadaID")
            ->join('fran.conta_padrao', 'contaPadrao')
            ->andWhere('fran.excluido = 0')
            ->getQuery()
            ->getOneOrNullResult();
        ;
    }

    /**
     * Busca os parametros de uma franqueada
     *
     * @param int $id
     *
     * @return array|NULL
     */
    public function buscarParametrosFranqueada($id)
    {
        $queryBuilder = $this->createQueryBuilder("fran");
        $queryBuilder->select("fran");
        $queryBuilder->andWhere("fran.id = :franqueadaId");
        $queryBuilder->setParameter("franqueadaId", $id);
        $queryBuilder->andWhere('fran.excluido = 0');
        return \App\Helper\FunctionHelper::retornaArrayNull($queryBuilder, true);
    }

    /**
     * Busca as franquias com suas metas
     *
     * @param array $parametros
     * @param integer $pagina
     * @param integer $numeroItensPorPagina
     *
     * @return \App\Entity\Principal\Aluno[] Resultados em formato de array
     */
    public function buscarFranquiasComMetas($parametros, $pagina=1, $numeroItensPorPagina=50)
    {
        $opcoes = [];

        $queryBuilder = $this->createQueryBuilder("franqueada");
        $queryBuilder->addSelect("franqueada, estado, metaFranqueada");
        $queryBuilder->leftJoin(
            "franqueada.metaFranqueadas",
            "metaFranqueada",
            \Doctrine\ORM\Query\Expr\Join::WITH,
            "metaFranqueada.ano = :ano AND metaFranqueada.mes = :mes"
        );

        $queryBuilder->andWhere('franqueada.excluido = 0');

        $queryBuilder->join("franqueada.estado", "estado");

        if (isset($parametros[ConstanteParametros::CHAVE_ESTADO]) === true && is_null($parametros[ConstanteParametros::CHAVE_ESTADO]) === false && empty($parametros[ConstanteParametros::CHAVE_ESTADO]) === false) {
            $queryBuilder->andWhere("estado = :estado");
            $queryBuilder->setParameter('estado', $parametros[ConstanteParametros::CHAVE_ESTADO]);
        } else {
            $queryBuilder->andWhere("franqueada = :franqueada");
            $queryBuilder->setParameter('franqueada', \App\Helper\VariaveisCompartilhadas::$franqueadaID);
        }

        $queryBuilder->setParameter('ano', $parametros[ConstanteParametros::CHAVE_ANO]);
        $queryBuilder->setParameter('mes', $parametros[ConstanteParametros::CHAVE_MES]);

        if ((isset($parametros[ConstanteParametros::CHAVE_ORDENACAO_COLUNA]) === true)&&(is_null($parametros[ConstanteParametros::CHAVE_ORDENACAO_COLUNA]) === false)) {
            $queryBuilder->orderBy($parametros[ConstanteParametros::CHAVE_ORDENACAO_COLUNA], $parametros[ConstanteParametros::CHAVE_ORDENACAO_SORT]);
            $opcoes[\Knp\Component\Pager\Paginator::SORT_FIELD_PARAMETER_NAME]     = "~";
            $opcoes[\Knp\Component\Pager\Paginator::SORT_DIRECTION_PARAMETER_NAME] = "~";
        }

        return \App\Helper\FunctionHelper::montaPaginatorPaginacao($queryBuilder, $pagina, $numeroItensPorPagina, $opcoes);
    }


}
