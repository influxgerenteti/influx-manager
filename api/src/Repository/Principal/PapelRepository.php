<?php
namespace App\Repository\Principal;

use App\Entity\Principal\Papel;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Symfony\Bridge\Doctrine\RegistryInterface;
use App\Helper\ConstanteParametros;

/**
 *
 * @method Papel|null find($id, $lockMode = null, $lockVersion = null)
 * @method Papel|null findOneBy(array $criteria, array $orderBy = null)
 * @method Papel[] findAll()
 * @method Papel[] findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class PapelRepository extends ServiceEntityRepository
{


    public function __construct(RegistryInterface $registry)
    {
        parent::__construct($registry, Papel::class);
    }

    /**
     * Monta QueryBase
     *
     * @return \Doctrine\ORM\QueryBuilder
     */
    protected function montaQueryBase()
    {
        $queryBuilder = $this->createQueryBuilder("p");
        $queryBuilder->leftJoin("p.moduloPapelAcao", "mpa");
        $queryBuilder->leftJoin("mpa.modulo", "m");
        $queryBuilder->leftJoin("mpa.acao_sistema", "acs");
        $queryBuilder->andWhere("p.oculto = false");

        return $queryBuilder;
    }

    /**
     * Adiciona os filtros na query
     *
     * @param \Doctrine\ORM\QueryBuilder $queryBuilder
     * @param array $parametros
     */
    protected function montaFiltros(&$queryBuilder, $parametros)
    {
        if ((isset($parametros[ConstanteParametros::CHAVE_PAPEL]) === true)&&(empty($parametros[ConstanteParametros::CHAVE_PAPEL]) === false)) {
            $queryBuilder->andWhere("p.id = :papelId");
            $queryBuilder->setParameter("papelId", $parametros[ConstanteParametros::CHAVE_PAPEL]);
        }

        if ((isset($parametros[ConstanteParametros::CHAVE_MODULO]) === true)&&(empty($parametros[ConstanteParametros::CHAVE_MODULO]) === false)) {
            $queryBuilder->andWhere("m.id = :moduloId");
            $queryBuilder->setParameter("moduloId", $parametros[ConstanteParametros::CHAVE_MODULO]);
        }

        if ((isset($parametros[ConstanteParametros::CHAVE_ACAO_SISTEMA]) === true)&&(empty($parametros[ConstanteParametros::CHAVE_ACAO_SISTEMA]) === false)) {
            $queryBuilder->andWhere("acs.id = :acaoSistemaId");
            $queryBuilder->setParameter("acaoSistemaId", $parametros[ConstanteParametros::CHAVE_ACAO_SISTEMA]);
        }
    }

    /**
     * Filtra os papeis por pagina
     *
     * @param array $parametros
     * @param number $pagina
     * @param number $numeroItensPorPagina
     *
     * @return \Knp\Component\Pager\Pagination\SlidingPagination
     */
    public function filtrarPapelPorPagina($parametros, $pagina=1, $numeroItensPorPagina=50)
    {
        $queryBuilder = $this->montaQueryBase();
        $queryBuilder->addSelect('m');
        $queryBuilder->addSelect('acs');
        // $queryBuilder->addSelect('acs');
        $this->montaFiltros($queryBuilder, $parametros);
        return \App\Helper\FunctionHelper::montaPaginatorPaginacao($queryBuilder, $pagina, $numeroItensPorPagina);
    }

    /**
     * Busca todos os papeis
     *
     * @param array $parametros
     * @param number $pagina
     * @param number $numeroItensPorPagina
     *
     * @return \Knp\Component\Pager\Pagination\SlidingPagination
     */
    public function buscarTodosPapeis($parametros, $pagina=1, $numeroItensPorPagina=50)
    {
        $queryBuilder = $this->montaQueryBase();
        $queryBuilder->select("p.id, p.descricao");
        $queryBuilder->distinct(true);
        return \App\Helper\FunctionHelper::montaPaginatorPaginacao($queryBuilder, $pagina, $numeroItensPorPagina);
    }

    /**
     * Busca o registro pela id do papel
     *
     * @param int $id
     *
     * @return array|NULL
     */
    public function buscar($id)
    {
        $queryBuilder = $this->createQueryBuilder("p");
        $queryBuilder->where("p.id = :papelId");
        $queryBuilder->setParameter("papelId", $id);
        return \App\Helper\FunctionHelper::retornaArrayNull($queryBuilder, true);
    }

    /**
     * Busca o registro pela id do papel
     *
     * @param int $id
     *
     * @return array|NULL
     */
    public function buscarPorPapel($id)
    {
        $queryBuilder = $this->montaQueryBase();
        $queryBuilder->select("p.id, p.descricao as nomePapel, m.id as moduloId, m.nome as nomeModulo, m.url as urlNome, m.situacao as moduloSituacao, acs.id as acaoId, acs.descricao as nomeAcao");
        $queryBuilder->where("p.id = :papelId");
        $queryBuilder->setParameter("papelId", $id);
        return \App\Helper\FunctionHelper::retornaArrayNull($queryBuilder);
    }


}
