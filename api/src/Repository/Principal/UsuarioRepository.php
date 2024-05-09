<?php
namespace App\Repository\Principal;

use App\Entity\Principal\Usuario;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Symfony\Bridge\Doctrine\RegistryInterface;
use App\Helper\ConstanteParametros;
use App\Helper\VariaveisCompartilhadas;
use App\Entity\Principal\Favorito;

/**
 *
 * @method Usuario|null find($id, $lockMode = null, $lockVersion = null)
 * @method Usuario|null findOneBy(array $criteria, array $orderBy = null)
 * @method Usuario[] findAll()
 * @method Usuario[] findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class UsuarioRepository extends ServiceEntityRepository
{


    public function __construct(RegistryInterface $registry)
    {
        parent::__construct($registry, Usuario::class);
    }

    /**
     * Monta a query de Relacionamento entre Usuario x Franqueada
     *
     * @return \Doctrine\ORM\QueryBuilder
     */
    protected function montarQueryUsuarioFranqueada()
    {
        $queryBuilder = $this->createQueryBuilder("u");
        $queryBuilder->addSelect("franqueadasAtribuidas");
        $queryBuilder->addSelect("p");
        $queryBuilder->addSelect("franPadrao");
        $queryBuilder->join("u.franqueadas", "franqueadasAtribuidas");
        $queryBuilder->join("u.franqueadas", "fran");
        $queryBuilder->leftJoin("u.franqueada_padrao", "franPadrao");
        $queryBuilder->leftJoin("u.papels", "p");
        return $queryBuilder;
    }

    /**
     * Query para realizar fitlro de franqueada
     *
     * @param \Doctrine\ORM\QueryBuilder $queryBuilder
     */
    private function filtrarFranqueada(&$queryBuilder)
    {
        $queryBuilder->andWhere('fran.id = :franqueada');
        $queryBuilder->setParameter('franqueada', VariaveisCompartilhadas::$franqueadaID);

        $queryBuilder->andWhere('fran.excluido = 0');
        $queryBuilder->andWhere('franqueadasAtribuidas.excluido = 0');
    }

    /**
     * Filtra o usuario por pagina
     *
     * @param array $parametros
     * @param integer $pagina
     *
     * @return \Doctrine\ORM\Tools\Pagination\Paginator
     */
    public function filtraUsuariosPorPagina($parametros, $pagina=1)
    {
        $numeroItensPorPagina = 150;
        $opcoes       = [];
        $queryBuilder = $this->montarQueryUsuarioFranqueada();
        $this->filtrarFranqueada($queryBuilder);

        if ((isset($parametros[ConstanteParametros::CHAVE_ORDENACAO_COLUNA]) === true)&&(is_null($parametros[ConstanteParametros::CHAVE_ORDENACAO_COLUNA]) === false)) {
            $queryBuilder->orderBy($parametros[ConstanteParametros::CHAVE_ORDENACAO_COLUNA], $parametros[ConstanteParametros::CHAVE_ORDENACAO_SORT]);
            $opcoes[\Knp\Component\Pager\Paginator::SORT_FIELD_PARAMETER_NAME]     = "~";
            $opcoes[\Knp\Component\Pager\Paginator::SORT_DIRECTION_PARAMETER_NAME] = "~";
        }

        return \App\Helper\FunctionHelper::montaPaginatorPaginacao($queryBuilder, $pagina, $numeroItensPorPagina, $opcoes);
    }

    /**
     * Busca permissao por usuario
     *
     * @param int $id
     *
     * @return array|NULL
     */
    public function buscarPermissaoPorUsuario($id)
    {
        $queryBuilder = $this->montarQueryUsuarioFranqueada();
        $queryBuilder->select("u.id, m.id as moduloId, m.nome as nomeModulo, m.url as urlNome, m.situacao as moduloSituacao, acs.id as acaoId, acs.descricao as nomeAcao");
        $queryBuilder->leftJoin("u.moduloUsuarioAcaos", "mua");
        $queryBuilder->leftJoin("mua.modulo", "m");
        $queryBuilder->leftJoin("mua.acao_sistema", "acs");
        $queryBuilder->where("u.id = :usuarioId");
        $queryBuilder->setParameter("usuarioId", $id);
        return \App\Helper\FunctionHelper::retornaArrayNull($queryBuilder);
    }

    /**
     * Busca menu por usuario
     *
     * @param int $id
     *
     * @return array|NULL
     */
    public function buscarMenuPorUsuario ($id)
    {
        $queryBuilder = $this->montarQueryUsuarioFranqueada();
        $queryBuilder->distinct();
        $queryBuilder->select("m.id as id, m.nome as nome, m.url as url, m.situacao as situacao, m.ordem as ordem, m.apenas_franqueadora as apenas_franqueadora, m.exibir_no_menu as exibir_no_menu, mp.id as modulo_pai_id, f.id as favorito_id");
        $queryBuilder->leftJoin("u.moduloUsuarioAcaos", "mua");
        $queryBuilder->leftJoin("mua.modulo", "m");
        $queryBuilder->leftJoin("mua.acao_sistema", "acao");
        $queryBuilder->leftJoin("m.modulo_pai", "mp", "WITH");
        $queryBuilder->leftJoin(Favorito::class, 'f', 'WITH', 'm.id = f.modulo AND f.usuario = :usuarioId');
        $queryBuilder->where("u.id = :usuarioId");
        $queryBuilder->andWhere("m.situacao = 'A'");
        $queryBuilder->andWhere("m.exibir_no_menu = 1");
        $queryBuilder->andWhere("acao.descricao = 'ACESSAR'");
        $queryBuilder->setParameter("usuarioId", $id);
        $queryBuilder->addOrderBy('m.ordem', 'desc');
        $queryBuilder->addOrderBy('m.nome', 'asc');
        return \App\Helper\FunctionHelper::retornaArrayNull($queryBuilder);
    }

        /**
     * Busca menu por usuario
     *
     * @param int $id
     *
     * @return array|NULL
     */
    public function buscarMenuRelatoriosPorUsuario ($id)
    {
        $queryBuilder = $this->montarQueryUsuarioFranqueada();
        $queryBuilder->distinct();
        $queryBuilder->select("m.id as id, m.nome as nome, m.url as url, m.situacao as situacao, m.ordem as ordem, m.apenas_franqueadora as apenas_franqueadora, m.exibir_no_menu as exibir_no_menu, mp.id as modulo_pai_id, f.id as favorito_id");
        $queryBuilder->leftJoin("u.moduloUsuarioAcaos", "mua");
        $queryBuilder->leftJoin("mua.modulo", "m");
        $queryBuilder->leftJoin("mua.acao_sistema", "acao");
        $queryBuilder->leftJoin("m.modulo_pai", "mp", "WITH");
        $queryBuilder->leftJoin(Favorito::class, 'f', 'WITH', 'm.id = f.modulo AND f.usuario = :usuarioId');
        $queryBuilder->where("u.id = :usuarioId");
        $queryBuilder->andWhere("m.situacao = 'A'");
        $queryBuilder->andWhere("m.exibir_como_relatorio = 1");
        $queryBuilder->andWhere("m.exibir_no_menu = 1");
        $queryBuilder->andWhere("acao.descricao = 'ACESSAR'");
        $queryBuilder->setParameter("usuarioId", $id);
        $queryBuilder->addOrderBy('m.ordem', 'desc');
        $queryBuilder->addOrderBy('m.nome', 'asc');

        return \App\Helper\FunctionHelper::retornaArrayNull($queryBuilder);
    }

    /**
     * Busca menu por papel
     *
     * @param int $id
     *
     * @return array|NULL
     */
    public function buscarMenuPorPapel ($id)
    {
        $queryBuilder = $this->montarQueryUsuarioFranqueada();
        $queryBuilder->distinct();
        $queryBuilder->select("m.id as id, m.nome as nome, m.url as url, m.situacao as situacao, m.ordem as ordem, m.apenas_franqueadora as apenas_franqueadora, m.exibir_no_menu as exibir_no_menu, mp.id as modulo_pai_id, f.id as favorito_id");
        $queryBuilder->leftJoin("p.moduloPapelAcao", "mpa");
        $queryBuilder->leftJoin("mpa.modulo", "m");
        $queryBuilder->leftJoin("mpa.acao_sistema", "acao");
        $queryBuilder->leftJoin("m.modulo_pai", "mp", "WITH");
        $queryBuilder->leftJoin(Favorito::class, 'f', 'WITH', 'm.id = f.modulo AND f.usuario = :usuarioId');
        $queryBuilder->where("u.id = :usuarioId");
        $queryBuilder->andWhere("m.situacao = 'A'");
        $queryBuilder->andWhere("m.exibir_no_menu = 1");
        $queryBuilder->andWhere("acao.descricao = 'ACESSAR'");
        $queryBuilder->setParameter("usuarioId", $id);
        $queryBuilder->addOrderBy('m.ordem', 'desc');
        $queryBuilder->addOrderBy('m.nome', 'asc');
        return \App\Helper\FunctionHelper::retornaArrayNull($queryBuilder);
    }

    /**
     * Busca o Usuario com as Franqueadas
     *
     * @param int $usuarioID Chave primaria usuario
     *
     * @return null|\App\Entity\Principal\Usuario
     */
    public function buscarUsuarioEFranqueadas($usuarioID, $retornoObjeto=false)
    {
        $queryBuilder = $this->montarQueryUsuarioFranqueada();
        $queryBuilder->andWhere("u.id = :usuarioId");
        $queryBuilder->setParameter("usuarioId", $usuarioID);
        $this->filtrarFranqueada($queryBuilder);

        if ($retornoObjeto === true) {
            $usuarioORM = $queryBuilder->getQuery()->getOneOrNullResult();
        } else {
            $usuarioORM = $queryBuilder->getQuery()->getOneOrNullResult(2);
        }

        return $usuarioORM;
    }

    /**
     * Busca usuários por nome
     *
     * @param string $nome
     * @param integer $franqueada
     *
     * @return \App\Entity\Principal\Usuario[]
     */
    public function buscarPorNome ($nome, $franqueada)
    {
        $queryBuilder = $this->createQueryBuilder('u')
            ->select('u')
            ->join('u.franqueadas', 'franqueada')
            ->where('u.nome LIKE :nome')
            ->andWhere('franqueada = :franqueada')
            ->setParameter('nome', "%$nome%")
            ->setParameter('franqueada', $franqueada)
            ->distinct();

        return $queryBuilder->getQuery()->getResult(\Doctrine\ORM\Query::HYDRATE_ARRAY);
    }


   

     


}
