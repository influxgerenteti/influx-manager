<?php

namespace App\Repository\Principal;

use App\Entity\Principal\ChecklistAtividade;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Symfony\Bridge\Doctrine\RegistryInterface;
use App\Helper\ConstanteParametros;
use App\Helper\VariaveisCompartilhadas;

/**
 * @method ChecklistAtividade|null find($id, $lockMode = null, $lockVersion = null)
 * @method ChecklistAtividade|null findOneBy(array $criteria, array $orderBy = null)
 * @method ChecklistAtividade[]    findAll()
 * @method ChecklistAtividade[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class ChecklistAtividadeRepository extends ServiceEntityRepository
{


    public function __construct(RegistryInterface $registry)
    {
        parent::__construct($registry, ChecklistAtividade::class);
    }

    /**
     * Monta QueryPrincipal
     *
     * @return \Doctrine\ORM\QueryBuilder
     */
    protected function montaQueryBase()
    {
        $queryBuilder = $this->createQueryBuilder("chka");
        return $queryBuilder;
    }

    /**
     * Filtra o ChecklistAtividade por pagina
     *
     * @param array $parametros
     * @param number $pagina
     * @param number $numeroItensPorPagina
     *
     * @return \Knp\Component\Pager\Pagination\SlidingPagination
     */
    public function filtraChecklistAtividadePorPagina($parametros, $pagina=1, $numeroItensPorPagina=500)
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
     * Monta a query para ser executada no relatório
     *
     * @param array $parametros
     *
     * @return string
     */
    public function prepararDadosRelatorio($parametros)
    {
        $queryBuilder = $this->createQueryBuilder("ca");
        $queryBuilder->select('ca.id');

        $queryBuilder->join('ca.papel', 'p');
        $queryBuilder->join('p.usuariosPapeis', 'up');

        $queryBuilder->join('up.funcionario', 'f');
        $queryBuilder->join('f.pessoa', 'pes');

        $queryBuilder->leftJoin('ca.checklistAtividadeRealizadas', 'car');

        $queryBuilder->andWhere("ca.situacao = 'A'");
        $queryBuilder->andWhere("f.consultor = 1");

        $queryBuilder->andWhere('ca.franqueada = :franqueada');
        $queryBuilder->setParameter('franqueada', VariaveisCompartilhadas::$franqueadaID);

        if (is_null($parametros["consultor"]) === false) {
            $queryBuilder->andWhere('f = :id');
            $queryBuilder->setParameter('id', $parametros["consultor"]);
        }

        if ((is_null($parametros["realizadas"]) === false) && ($parametros["realizadas"]) === '1') {
            $queryBuilder->andWhere('car.data_conclusao is not null');

            if (is_null($parametros["data_inicial"]) === false) {
                $queryBuilder->andWhere('car.data_conclusao >= :data_inicial');
                $queryBuilder->setParameter('data_inicial', $parametros["data_inicial"]);
            }

            if (is_null($parametros["data_final"]) === false) {
                $queryBuilder->andWhere('car.data_conclusao <= :data_final');
                $queryBuilder->setParameter('data_final', $parametros["data_final"]);
            }
        } else if ((is_null($parametros["pendentes"]) === false) && ($parametros["pendentes"]) === '1') {
            $queryBuilder->andWhere('car.data_conclusao is null');
        }

        // Filtra e substitui a query para passar ao Jasper
        $sql = $queryBuilder->getQuery()->getSQL();

        $sql = preg_replace('/.*WHERE\s(.*)$/', '$1', $sql);

        // Seleciona somente os wheres
        $sql = preg_replace('/c0_/', 'checklist_atividade', $sql);
        $sql = preg_replace('/c2_/', 'checklist_atividade_papel', $sql);
        $sql = preg_replace('/p1_/', 'papel', $sql);
        $sql = preg_replace('/u4_/', 'usuario_papel', $sql);
        $sql = preg_replace('/u3_/', 'usuario', $sql);
        $sql = preg_replace('/f5_/', 'funcionario', $sql);
        $sql = preg_replace('/p6_/', 'pessoa', $sql);
        $sql = preg_replace('/c7_/', 'checklist_atividade_realizada', $sql);

        // Substituição de parâmetros
        $parameters = $queryBuilder->getParameters();
        foreach ($parameters as $parameter) {
            $param = $parameter->getValue();
            $sql   = preg_replace('/\?/', "'$param'", $sql, 1);
        }

        return $sql;
    }


}
