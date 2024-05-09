<?php

namespace App\Repository\Principal;

use App\Entity\Principal\Modulo;
use App\Entity\Principal\Favorito;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Symfony\Bridge\Doctrine\RegistryInterface;
use App\Helper\ConstanteParametros;

/**
 *
 * @method Modulo|null find($id, $lockMode = null, $lockVersion = null)
 * @method Modulo|null findOneBy(array $criteria, array $orderBy = null)
 * @method Modulo[]    findAll()
 * @method Modulo[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class ModuloRepository extends ServiceEntityRepository
{


    public function __construct(RegistryInterface $registry)
    {
        parent::__construct($registry, Modulo::class);
    }

    /**
     * Busca todos os módulos sem trazer os dados do modulo_pai relacionado
     *
     * @param array $parametros
     * @param integer $numeroItensPorPagina número de itens por página
     *
     * @return array
     */
    public function buscarTodosSemRelacaoComPai ($parametros, $numeroItensPorPagina=999999999)
    {
        $pagina       = $parametros[ConstanteParametros::CHAVE_PAGINA];
        $opcoes       = [];
        $queryBuilder = $this->createQueryBuilder('m')
            ->addSelect('m', 'mdp')
            ->leftJoin("m.modulo_pai", "mdp")
            ->where("m.situacao != 'R'");

        if ((isset($parametros[ConstanteParametros::CHAVE_ORDENACAO_COLUNA]) === true)&&(is_null($parametros[ConstanteParametros::CHAVE_ORDENACAO_COLUNA]) === false)) {
            $queryBuilder->orderBy($parametros[ConstanteParametros::CHAVE_ORDENACAO_COLUNA], $parametros[ConstanteParametros::CHAVE_ORDENACAO_SORT]);
            $opcoes[\Knp\Component\Pager\Paginator::SORT_FIELD_PARAMETER_NAME]     = "~";
            $opcoes[\Knp\Component\Pager\Paginator::SORT_DIRECTION_PARAMETER_NAME] = "~";
        }

        return \App\Helper\FunctionHelper::montaPaginatorPaginacao($queryBuilder, $pagina, $numeroItensPorPagina, $opcoes);
    }

    /**
     * Busca todos os módulos ativos sem trazer os dados do modulo_pai relacionado.
     * TODO: Fazer filtro por franqueada também (adicionar `f.franqueada_id = :franqueada` ao Join com Favorito).
     *
     * @param int $usuarioId
     *
     * @return array
     */
    public function buscarAtivosSemRelacaoComPai ($usuarioId=null)
    {
        $qb = $this->createQueryBuilder('m')
            ->select('m.id as id, m.nome as nome, m.url as url, m.situacao as situacao, p.id as modulo_pai_id, f.id as favorito_id, m.ordem as ordem')
            ->leftJoin('m.modulo_pai', 'p', 'WITH')
            ->leftJoin(Favorito::class, 'f', 'WITH', 'm.id = f.modulo AND f.usuario = :usuario')
            ->where("m.situacao = 'A'")
            ->orderBy('m.modulo_pai', 'asc')
            ->orderBy('m.ordem', 'desc')
            ->addOrderBy('m.nome', 'asc')
            ->setParameter('usuario', $usuarioId)
            ->getQuery();
        return $qb->getScalarResult();
    }

    /**
     * Buscar módulo pela ID. Traz a quantidade de filhos que possui.
     *
     * @param int $id
     *
     * @return mixed|null
     */
    public function buscarModulo ($id=null)
    {
        $queryBuilder = $this->createQueryBuilder('m')
            ->addSelect("p")
            ->addSelect("aSs")
            ->leftJoin("m.modulo_pai", "p")
            ->leftJoin(Modulo::class, 'a', 'WITH', 'a.id = m.modulo_pai')
            ->leftJoin("m.acaoSistemas", "aSs")
            ->where('m.id = :id')
            ->groupBy('m.id, aSs.id')
            ->setParameter('id', $id);

        try {
            return \App\Helper\FunctionHelper::retornaArrayNull($queryBuilder, true);
        } catch (\Exception $e) {
            return $e->getMessage();
        }
    }

    /**
     * Busca todos os modulos do sistema
     *
     * @return array|NULL
     */
    public function buscarModulosSemPai()
    {
        $queryBuilder = $this->createQueryBuilder("m");
        $queryBuilder->where("m.modulo_pai IS NOT NULL");
        $queryBuilder->orderBy("m.nome", "ASC");
        return \App\Helper\FunctionHelper::retornaArrayNull($queryBuilder);
    }

    /**
     * Retorna modulos de pai
     *
     * @return array|null
     */
    public function buscarModulosPai()
    {
        $queryBuilder = $this->createQueryBuilder("m");
        $queryBuilder->select("m.id, m.nome, m.ordem, m.url, m.situacao, p.id as modulo_pai_id");
        $queryBuilder->leftJoin("m.modulo_pai", "p");
        $queryBuilder->where("m.modulo_pai IS NULL");
        $queryBuilder->andWhere("m.situacao = 'A'");
        $queryBuilder->orderBy("m.ordem", "DESC");
        $queryBuilder->addOrderBy("m.nome", "ASC");
        return \App\Helper\FunctionHelper::retornaArrayNull($queryBuilder);
    }

    /**
     * Busca as permissoes por módulo
     *
     * @param array $parametros
     *
     * @return array|NULL
     */
    public function buscarPermissaoPorModulo($parametros)
    {
        $queryBuilder = $this->createQueryBuilder("m");
        $queryBuilder->addSelect("aSs");
        // $queryBuilder->addSelect("mpa");
        $queryBuilder->addSelect("mua");
        $queryBuilder->leftJoin("m.acaoSistemas", "aSs");
        // $queryBuilder->leftJoin("m.modulo_papel_acao", "mpa", "WITH", "m.id = mpa.modulo AND aSs.id = mpa.acao_sistema AND mpa.papel IN (:papeisIds)");
        $queryBuilder->leftJoin("m.moduloUsuarioAcaos", "mua", "WITH", "m.id = mua.modulo AND aSs.id = mua.acao_sistema AND mua.usuario = :usuarioId");

        $queryBuilder->where("m.id = :moduloId");
        $queryBuilder->setParameter("moduloId", $parametros[ConstanteParametros::CHAVE_MODULO]);
        // $queryBuilder->setParameter("papeisIds", $parametros[ConstanteParametros::CHAVE_PAPEL]);
        $queryBuilder->setParameter("usuarioId", $parametros[ConstanteParametros::CHAVE_USUARIO]);

        return \App\Helper\FunctionHelper::retornaArrayNull($queryBuilder, true);
    }

    /**
     * Busca as módulos por papel com ações de sistema
     *
     * @param array $parametros
     *
     * @return array|NULL
     */
    public function buscarModuloPorPapel($parametros)
    {
        $queryBuilder = $this->createQueryBuilder("m");
        $queryBuilder->addSelect("aSs");
        $queryBuilder->addSelect("mpa");
        $queryBuilder->addSelect("mp");
        $queryBuilder->leftJoin("m.modulo_pai", "mp");
        $queryBuilder->leftJoin("m.acaoSistemas", "aSs");
        $queryBuilder->leftJoin("m.modulo_papel_acao", "mpa", "WITH", "m.id = mpa.modulo AND aSs.id = mpa.acao_sistema AND (mpa.papel = :papelId OR mpa.papel IS NULL)");
        $queryBuilder->leftJoin("mpa.papel", "p");
        $queryBuilder->setParameter("papelId", $parametros[ConstanteParametros::CHAVE_PAPEL]);

        if (is_null($parametros[ConstanteParametros::CHAVE_MODULO]) === false) {
            $queryBuilder->andWhere("m.id = :moduloId");
            $queryBuilder->setParameter("moduloId", $parametros[ConstanteParametros::CHAVE_MODULO]);
        }

        $queryBuilder->orderBy("m.ordem", "DESC");
        $queryBuilder->addOrderBy("m.nome", "ASC");

        return \App\Helper\FunctionHelper::retornaArrayNull($queryBuilder);
    }

    /**
     * Busca as módulos por usuário com ações de sistema
     *
     * @param array $parametros
     *
     * @return array|NULL
     */
    public function buscarModuloPorUsuario($parametros)
    {
        $queryBuilder = $this->createQueryBuilder("m");
        $queryBuilder->addSelect("aSs");
        $queryBuilder->addSelect("mua");
        $queryBuilder->addSelect("mp");
        $queryBuilder->leftJoin("m.modulo_pai", "mp");
        $queryBuilder->leftJoin("m.acaoSistemas", "aSs");
        $queryBuilder->leftJoin("m.moduloUsuarioAcaos", "mua", "WITH", "m.id = mua.modulo AND aSs.id = mua.acao_sistema AND mua.usuario = :usuarioId");
        $queryBuilder->leftJoin("mua.usuario", "u");
        $queryBuilder->setParameter("usuarioId", $parametros[ConstanteParametros::CHAVE_USUARIO]);

        if (is_null($parametros[ConstanteParametros::CHAVE_MODULO]) === false) {
            $queryBuilder->andWhere("m.id = :moduloId");
            $queryBuilder->setParameter("moduloId", $parametros[ConstanteParametros::CHAVE_MODULO]);
        }

        $queryBuilder->orderBy("m.ordem", "DESC");
        $queryBuilder->addOrderBy("m.nome", "ASC");

        return \App\Helper\FunctionHelper::retornaArrayNull($queryBuilder);
    }


}
