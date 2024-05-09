<?php

namespace App\Repository\Principal;

use App\Entity\Principal\Horario;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Symfony\Bridge\Doctrine\RegistryInterface;
use App\Helper\ConstanteParametros;
use App\Helper\VariaveisCompartilhadas;

/**
 * @method Horario|null find($id, $lockMode = null, $lockVersion = null)
 * @method Horario|null findOneBy(array $criteria, array $orderBy = null)
 * @method Horario[]    findAll()
 * @method Horario[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class HorarioRepository extends ServiceEntityRepository
{


    public function __construct(RegistryInterface $registry)
    {
        parent::__construct($registry, Horario::class);
    }


    /**
     * Monta a query base para Horario
     *
     * @return \Doctrine\ORM\QueryBuilder
     */
    protected function montaQueryBase()
    {
        $queryBuilder = $this->createQueryBuilder("hr");
        $queryBuilder->addSelect("hra");
        $queryBuilder->addSelect("turma");
        $queryBuilder->join('hr.franqueada', 'franqueada');
        $queryBuilder->leftJoin("hr.horarioAulas", "hra");
        $queryBuilder->leftJoin("hr.turmas", "turma");


        return $queryBuilder;
    }

    /**
     * Query para realizar fitlro de franqueada
     *
     * @param \Doctrine\ORM\QueryBuilder $queryBuilder
     */
    private function filtrarFranqueada(&$queryBuilder)
    {
        $queryBuilder->where('franqueada = :franqueada');
        $queryBuilder->setParameter('franqueada', VariaveisCompartilhadas::$franqueadaID);
    }

    /**
     * Filtra o Horario por pagina
     *
     * @param array $parametros
     * @param number $pagina
     * @param number $numeroItensPorPagina
     *
     * @return \Knp\Component\Pager\Pagination\SlidingPagination
     */
    public function filtrarHorarioPorPagina($parametros, $pagina=1, $numeroItensPorPagina=50)
    {
        $opcoes       = [];
        $queryBuilder = $this->montaQueryBase();
        $this->filtrarFranqueada($queryBuilder);

        if ((isset($parametros[ConstanteParametros::CHAVE_ORDENACAO_COLUNA]) === true)&&(is_null($parametros[ConstanteParametros::CHAVE_ORDENACAO_COLUNA]) === false)) {
            $queryBuilder->orderBy($parametros[ConstanteParametros::CHAVE_ORDENACAO_COLUNA], $parametros[ConstanteParametros::CHAVE_ORDENACAO_SORT]);
            $opcoes[\Knp\Component\Pager\Paginator::SORT_FIELD_PARAMETER_NAME]     = "~";
            $opcoes[\Knp\Component\Pager\Paginator::SORT_DIRECTION_PARAMETER_NAME] = "~";
        }

        return \App\Helper\FunctionHelper::montaPaginatorPaginacao($queryBuilder, $pagina, $numeroItensPorPagina, $opcoes);
    }

    /**
     * Busca todos os horários filtrado por franqueada
     *
     * @param array $parametros
     *
     * @return array|NULL
     */
    public function buscarTodos($parametros)
    {
        $em = $this->getEntityManager();
        $opcoes       = [];

        $franqueada_id = VariaveisCompartilhadas::$franqueadaID;
        $queryTurmas = "SELECT 
            fran.nome,
            t.id,
            t.intensidade,
            t.maximo_alunos,
            t.descricao,
            t.data_inicio,
            t.data_fim,
            t.observacao,
            t.excluido,
            t.descricao,
            t.horario_id 
            from franqueada as fran 
            inner join turma as t on t.franqueada_id = fran.id    
            where fran.id  = {$franqueada_id}";



        $resultTurmas = $em->getConnection()->fetchAllAssociative($queryTurmas);
        

        $queryHorarios = "SELECT 
            h.id,
            fran.id as franquia_id,
            fran.nome,
            h.descricao,
            h.situacao
            from franqueada as fran 
            inner join horario as h on h.franqueada_id = fran.id    
            where fran.id  = {$franqueada_id}";
  
        $resultHorarios = $em->getConnection()->fetchAllAssociative($queryHorarios);
      

        $queryHorariosDeAula = "SELECT 
            ha.id, h.id as horario_id, ha.dia_semana, ha.horario_inicio, h.franqueada_id as franqueada_id, h.situacao 
            FROM horario_aula as ha 
            inner join horario as h on ha.horario_id = h.id 
            where h.franqueada_id = {$franqueada_id}";

        $resultHorariosDeAula = $em->getConnection()->fetchAllAssociative($queryHorariosDeAula);
        
        $registros = [];
        foreach ($resultHorarios as $horario ) {
            $registros[$horario['id']] = $horario;
            $registros[$horario['id']]['horarioAulas'] = [];
            $registros[$horario['id']]['turmas'] = [];
        }

        foreach ($resultHorariosDeAula as $horarioDeAula ) {           
            $registros[$horarioDeAula['horario_id']]['horarioAulas'][] = $horarioDeAula;
        }
        foreach ($resultTurmas as $turma ) {           
            $registros[$turma['horario_id']]['turmas'][] = $turma;
        }

        $resultados = [];

        foreach ($registros as $registro ) {           
            $resultados[] = $registro;
        }

        return $resultados;

        // var_dump($registros);
        // die;

        // // $opcoes["horarioAulas"] = $resultHorarios;

        // $queryBuilder = $this->montaQueryBase();
        // $this->filtrarFranqueada($queryBuilder);

        // if ((isset($parametros[ConstanteParametros::CHAVE_ORDENACAO_COLUNA]) === true)&&(is_null($parametros[ConstanteParametros::CHAVE_ORDENACAO_COLUNA]) === false)) {
        //     $queryBuilder->orderBy($parametros[ConstanteParametros::CHAVE_ORDENACAO_COLUNA], $parametros[ConstanteParametros::CHAVE_ORDENACAO_SORT]);
        //     $opcoes[\Knp\Component\Pager\Paginator::SORT_FIELD_PARAMETER_NAME]     = "~";
        //     $opcoes[\Knp\Component\Pager\Paginator::SORT_DIRECTION_PARAMETER_NAME] = "~";
        // }

        // return \App\Helper\FunctionHelper::retornaArrayNull($queryBuilder);
    }

    /**
     * Busca o registro pela chave primaria
     *
     * @param int $id
     *
     * @return array|NULL
     */
    public function buscarPorId($id)
    {
        $queryBuilder = $this->montaQueryBase();
        $this->filtrarFranqueada($queryBuilder);
        $queryBuilder->andWhere("hr.id = :id");
        $queryBuilder->setParameter("id", $id);

        return \App\Helper\FunctionHelper::retornaArrayNull($queryBuilder, true);
    }

    /**
     * Retorna os objetos do horario com horario aula ordenado
     *
     * @param int $id
     *
     * @return \App\Entity\Principal\Horario
     */
    public function retoraComHorarioAulaOrdenado($id)
    {
        $queryBuilder = $this->createQueryBuilder("hr");
        $queryBuilder->addSelect("hra");
        $queryBuilder->leftJoin("hr.horarioAulas", "hra");
        $queryBuilder->andWhere("hr.id = :id");
        $queryBuilder->setParameter("id", $id);
        return $queryBuilder->getQuery()->getOneOrNullResult();
    }


}
