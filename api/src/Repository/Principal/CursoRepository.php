<?php

namespace App\Repository\Principal;

use App\Entity\Principal\Curso;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Symfony\Bridge\Doctrine\RegistryInterface;
use App\Helper\ConstanteParametros;
use App\Helper\VariaveisCompartilhadas;

/**
 * @method Curso|null find($id, $lockMode = null, $lockVersion = null)
 * @method Curso|null findOneBy(array $criteria, array $orderBy = null)
 * @method Curso[]    findAll()
 * @method Curso[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class CursoRepository extends ServiceEntityRepository
{


    public function __construct(RegistryInterface $registry)
    {
        parent::__construct($registry, Curso::class);
    }

    /**
     * Monta Query Base
     *
     * @return \Doctrine\ORM\QueryBuilder
     */
    protected function montaQueryBase($parametros)
    {
        $queryBuilder = $this->createQueryBuilder("curso");
        $queryBuilder->addSelect("i");
        $queryBuilder->addSelect("serv");
        $queryBuilder->addSelect("tm");
        $queryBuilder->addSelect("itemFranqueadas");
        $queryBuilder->join("curso.servico", "serv");
        $queryBuilder->join("curso.modalidade_turma", "tm");
        $queryBuilder->leftJoin("curso.idioma", "i");
        $queryBuilder->leftJoin("serv.itemFranqueadas", "itemFranqueadas", "WITH", "itemFranqueadas.franqueada = :filtroFranqueada");

        $queryBuilder->setParameter("filtroFranqueada", VariaveisCompartilhadas::$franqueadaID);

        if ($parametros['situacao'] != 'T') {
            $queryBuilder->andWhere("curso.situacao = :situacao");
            $queryBuilder->setParameter("situacao", $parametros['situacao']);
        }

        
        $queryBuilder->orderBy('curso.descricao', 'ASC');
        return $queryBuilder;
    }

    /**
     * Busca o registro pela id
     *
     * @param int $id
     *
     * @return array|NULL
     */
    public function buscaRegistroPorId($id)
    {
        $queryBuilder = $this->montaQueryBase();
        $queryBuilder->where("curso.id = :id");
        $queryBuilder->setParameter("id", $id);
        return \App\Helper\FunctionHelper::retornaArrayNull($queryBuilder, true);
    }


    /**
     * Filtra os cursos
     *
     * @param array $parametros
     * @param number $numeroItensPorPagina
     *
     * @return \Knp\Component\Pager\Pagination\SlidingPagination
     */
    public function filtrarCurso($parametros, $numeroItensPorPagina=50)
    {
        $opcoes       = [];
        $queryBuilder = $this->montaQueryBase($parametros);

        if (isset($parametros['buscar_todas']) === true && $parametros['buscar_todas'] === true) {
            return  $queryBuilder->getQuery()->getResult(\Doctrine\ORM\Query::HYDRATE_ARRAY);
        }

        if (isset($parametros[ConstanteParametros::CHAVE_PAGINA]) === true) {
            $pagina = $parametros[ConstanteParametros::CHAVE_PAGINA];
        } else {
            $pagina = 1;
        }

        if ((isset($parametros[ConstanteParametros::CHAVE_ORDENACAO_COLUNA]) === true)&&(is_null($parametros[ConstanteParametros::CHAVE_ORDENACAO_COLUNA]) === false)) {
            $queryBuilder->orderBy($parametros[ConstanteParametros::CHAVE_ORDENACAO_COLUNA], $parametros[ConstanteParametros::CHAVE_ORDENACAO_SORT]);
            $opcoes[\Knp\Component\Pager\Paginator::SORT_FIELD_PARAMETER_NAME]     = "~";
            $opcoes[\Knp\Component\Pager\Paginator::SORT_DIRECTION_PARAMETER_NAME] = "~";
        }

        $retorno = \App\Helper\FunctionHelper::montaPaginatorPaginacao($queryBuilder, $pagina, $numeroItensPorPagina, $opcoes);

        return [
            ConstanteParametros::CHAVE_TOTAL => $retorno->getTotalItemCount(),
            ConstanteParametros::CHAVE_ITENS => $retorno->getItems(),
        ];
    }

    /**
     * Busca um curso pela ID
     *
     * @param integer $id
     *
     * @return array|null
     */
    public function buscarPorId($id)
    {
        $queryBuilder = $this->createQueryBuilder('c');
        $queryBuilder->addSelect('i');
        $queryBuilder->addSelect('s');
        $queryBuilder->addSelect('mt');
        $queryBuilder->join('c.servico', 's');
        $queryBuilder->join('c.idioma', 'i');
        $queryBuilder->join('c.modalidade_turma', 'mt');
        $queryBuilder->where('c.id = :curso');
        $queryBuilder->setParameter('curso', $id);

        return \App\Helper\FunctionHelper::retornaArrayNull($queryBuilder, true);
    }


}
