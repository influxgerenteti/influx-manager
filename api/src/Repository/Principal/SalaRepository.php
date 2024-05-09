<?php

namespace App\Repository\Principal;

use App\Entity\Principal\Sala;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\Query\Expr;
use Symfony\Bridge\Doctrine\RegistryInterface;
use App\Helper\ConstanteParametros;
use App\Helper\SituacoesSistema;
use App\Helper\VariaveisCompartilhadas;

/**
 * @method Sala|null find($id, $lockMode = null, $lockVersion = null)
 * @method Sala|null findOneBy(array $criteria, array $orderBy = null)
 * @method Sala[]    findAll()
 * @method Sala[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class SalaRepository extends ServiceEntityRepository
{


    public function __construct(RegistryInterface $registry)
    {
        parent::__construct($registry, Sala::class);
    }

    /**
     * Monta a query base para Sala
     *
     * @return \Doctrine\ORM\QueryBuilder
     */
    protected function montaQueryBase()
    {
        $queryBuilder = $this->createQueryBuilder("sal");
        $queryBuilder->addSelect("slf");
        $queryBuilder->leftJoin("sal.salaFranqueadas", "slf", "WITH", "slf.franqueada = :franqueada");

        $queryBuilder->setParameter('franqueada', VariaveisCompartilhadas::$franqueadaID);
        return $queryBuilder;
    }

    /**
     * Filtra o Sala por pagina
     *
     * @param array $parametros
     * @param number $pagina
     * @param number $numeroItensPorPagina
     *
     * @return \Knp\Component\Pager\Pagination\SlidingPagination
     */
    public function filtrarSalaPorPagina($parametros, $pagina=1, $numeroItensPorPagina=50)
    {
        $opcoes       = [];
        $queryBuilder = $this->montaQueryBase();

        if ((isset($parametros[ConstanteParametros::CHAVE_ORDENACAO_COLUNA]) === true)&&(is_null($parametros[ConstanteParametros::CHAVE_ORDENACAO_COLUNA]) === false)) {
            $queryBuilder->orderBy($parametros[ConstanteParametros::CHAVE_ORDENACAO_COLUNA], $parametros[ConstanteParametros::CHAVE_ORDENACAO_SORT]);
            $opcoes[\Knp\Component\Pager\Paginator::SORT_FIELD_PARAMETER_NAME]     = "~";
            $opcoes[\Knp\Component\Pager\Paginator::SORT_DIRECTION_PARAMETER_NAME] = "~";
        }

        return \App\Helper\FunctionHelper::montaPaginatorPaginacao($queryBuilder, $pagina, $numeroItensPorPagina, $opcoes);
    }

    /**
     * Busca Registro pela id
     *
     * @param int $id
     *
     * @return array
     */
    public function buscarSalaPorId($id)
    {
        $queryBuilder = $this->montaQueryBase();
        $queryBuilder->andWhere("sal.id = :salaId");
        $queryBuilder->setParameter("salaId", $id);
        return \App\Helper\FunctionHelper::retornaArrayNull($queryBuilder, true);
    }

    /**
     * Retorna as salas juntamente com os dados da sala na franqueada
     *
     * @param array $parametros
     * @param int $franqueada
     * @param number $pagina
     * @param number $numeroItensPorPagina
     *
     * @return \App\Entity\Principal\Sala[]
     */
    public function buscaSalaESalaFranqueada ($parametros, $franqueada, $pagina=1, $numeroItensPorPagina=50)
    {
        $opcoes       = [];
        $queryBuilder = $this->montaQueryBase();

        if ((isset($parametros[ConstanteParametros::CHAVE_APENAS_SALA_ATIVA]) === true) && ((bool) $parametros[ConstanteParametros::CHAVE_APENAS_SALA_ATIVA] === true)) {
            $queryBuilder->andWhere("slf.situacao = :situacao");
            $queryBuilder->setParameter("situacao", SituacoesSistema::SITUACAO_ATIVO);
        }
        
        if (!empty($parametros[ConstanteParametros::CHAVE_PERSONAL])) {
            $queryBuilder->andWhere("slf.personal = :personal");
            $queryBuilder->setParameter("personal", $parametros[ConstanteParametros::CHAVE_PERSONAL]);
        }

        $queryBuilder->select("sal.id, sal.descricao,slf.id as salaFranqueadaId ,slf.lotacao_maxima, slf.personal, slf.situacao");
        if ((isset($parametros[ConstanteParametros::CHAVE_ORDENACAO_COLUNA]) === true)&&(is_null($parametros[ConstanteParametros::CHAVE_ORDENACAO_COLUNA]) === false)) {
            $queryBuilder->orderBy($parametros[ConstanteParametros::CHAVE_ORDENACAO_COLUNA], $parametros[ConstanteParametros::CHAVE_ORDENACAO_SORT]);
            $opcoes[\Knp\Component\Pager\Paginator::SORT_FIELD_PARAMETER_NAME]     = "~";
            $opcoes[\Knp\Component\Pager\Paginator::SORT_DIRECTION_PARAMETER_NAME] = "~";
            $opcoes[\Knp\Component\Pager\Paginator::DISTINCT] = false;
        }

        return \App\Helper\FunctionHelper::montaPaginatorPaginacao($queryBuilder, $pagina, $numeroItensPorPagina, $opcoes);
    }


}
