<?php

namespace App\Repository\Principal;

use App\Entity\Principal\AgendamentoPersonal;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Symfony\Bridge\Doctrine\RegistryInterface;
use App\Helper\VariaveisCompartilhadas;
use App\Helper\SituacoesSistema;
use App\Helper\ConstanteParametros;
use \Carbon\Carbon;
use \Carbon\CarbonImmutable;
use DateTime;

/**
 * @method AgendamentoPersonal|null find($id, $lockMode = null, $lockVersion = null)
 * @method AgendamentoPersonal|null findOneBy(array $criteria, array $orderBy = null)
 * @method AgendamentoPersonal[]    findAll()
 * @method AgendamentoPersonal[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class AgendamentoPersonalRepository extends ServiceEntityRepository
{

    /**
     * @var Registry
     */
    private $registry;

    public function __construct(RegistryInterface $registry)
    {
        parent::__construct($registry, AgendamentoPersonal::class);
        $this->registry = $registry;
    }
   

    /**
     * Monta query principal
     *
     * @return \Doctrine\ORM\QueryBuilder
     */
    protected function montaQueryBase()
    {

        $queryBuilder = $this->createQueryBuilder('a');
        $queryBuilder->addSelect('contrato');
        $queryBuilder->addSelect('aluno');
        $queryBuilder->addSelect('pessoa');
        $queryBuilder->addSelect('funcionario');
        $queryBuilder->addSelect('livro');
        $queryBuilder->addSelect('sala_franqueada');
        $queryBuilder->addSelect('sala');
        $queryBuilder->addSelect('drpa');

        $queryBuilder->join('a.contrato', 'contrato');
        $queryBuilder->join('contrato.aluno', 'aluno');
        $queryBuilder->join('contrato.livro', 'livro');
        $queryBuilder->join('aluno.pessoa', 'pessoa');
        $queryBuilder->join('a.funcionario', 'funcionario');
        $queryBuilder->join('a.sala_franqueada', 'sala_franqueada');
        $queryBuilder->join('sala_franqueada.sala', 'sala');
        $queryBuilder->leftJoin('a.datasReagendamentoPersonals', 'drpa');

        return $queryBuilder;
    }

    /**
     * Query para realizar fitlro de franqueada
     *
     * @param \Doctrine\ORM\QueryBuilder $queryBuilder
     */
    private function filtrarFranqueada(&$queryBuilder)
    {
        $queryBuilder->andWhere('a.franqueada = :franqueada');
        $queryBuilder->setParameter('franqueada', VariaveisCompartilhadas::$franqueadaID);
    }

    /**
     * Monta filtros data de/até
     *
     * @param \Doctrine\ORM\QueryBuilder $queryBuilder
     * @param array $parametros
     */
    private function montaFiltroDatas(&$queryBuilder, $parametros)
    {
        $queryBuilder->andWhere(
            $queryBuilder->expr()->orX(
                $queryBuilder->expr()->andX(
                    // Ou não teve reagendamento e considera que o agendamento esteja na mesma semana/ano da data pesquisada;
                    $queryBuilder->expr()->eq("a.reagendado", "0"),
                    $queryBuilder->expr()->orX(
                        $queryBuilder->expr()->andX(
                            $queryBuilder->expr()->eq("WEEK(a.inicio)", "WEEK(:semanaInicio)"),
                            $queryBuilder->expr()->eq("YEAR(a.inicio)", "YEAR(:semanaInicio)")
                        ),
                        $queryBuilder->expr()->andX(
                            $queryBuilder->expr()->eq("WEEK(a.inicio)", "WEEK(:semanaFim)"),
                            $queryBuilder->expr()->eq("YEAR(a.inicio)", "YEAR(:semanaFim)")
                        )
                    )
                ),
                $queryBuilder->expr()->andX(
                    // Ou teve reagendamento e considera que o ultimo reagendamento feito esteja na mesma semana/ano da data pesquisada;
                    $queryBuilder->expr()->eq("a.reagendado", "1"),
                    $queryBuilder->expr()->eq("drpa.ultimo_reagendamento", "1"),
                    $queryBuilder->expr()->orX(
                        $queryBuilder->expr()->andX(
                            $queryBuilder->expr()->eq("WEEK(drpa.data_reagendada)", "WEEK(:semanaInicio)"),
                            $queryBuilder->expr()->eq("YEAR(drpa.data_reagendada)", "YEAR(:semanaInicio)")
                        ),
                        $queryBuilder->expr()->andX(
                            $queryBuilder->expr()->eq("WEEK(drpa.data_reagendada)", "WEEK(:semanaFim)"),
                            $queryBuilder->expr()->eq("YEAR(drpa.data_reagendada)", "YEAR(:semanaFim)")
                        )
                    )
                )
            )
        );

        $dataParam = CarbonImmutable::parse($parametros[ConstanteParametros::CHAVE_DATA]);

        if ($dataParam->weekday() === SituacoesSistema::DIA_SEMANA_SEGUNDA) {
            $dataInicio = Carbon::parse($dataParam);
        } else if ($dataParam->weekday() === SituacoesSistema::DIA_SEMANA_DOMINGO) {
            $dataInicio = Carbon::parse($dataParam->next('monday'));
        } else {
            $dataInicio = Carbon::parse($dataParam->previous('monday'));
        }

        if ($dataParam->weekday() === SituacoesSistema::DIA_SEMANA_SABADO) {
            $dataFim = Carbon::parse($dataParam);
        } else {
            $dataFim = Carbon::parse($dataParam->next('friday'));
        }

        // Timezone
        $dataInicio->setTime($dataParam->hour, 0, 0);
        // Timezone
        $dataFim->setTime((23 - $dataParam->hour), 59, 59);

        $queryBuilder->setParameter('semanaInicio', $dataInicio->format('Y-m-d H:i:s'));
        $queryBuilder->setParameter('semanaFim', $dataFim->format('Y-m-d H:i:s'));
    }


    /**
     * Monta filtros
     *
     * @param \Doctrine\ORM\QueryBuilder $queryBuilder
     * @param array $parametros
     */
    private function montarFiltros(&$queryBuilder, $parametros)
    {
        $queryBuilder->andWhere("contrato.situacao in ('V')");
        $queryBuilder->andWhere("a.finalizado = 0");
        if (isset($parametros[ConstanteParametros::CHAVE_ALUNO]) === true && empty($parametros[ConstanteParametros::CHAVE_ALUNO]) === false) {
            $queryBuilder->andWhere('aluno.id = :aluno');
            $queryBuilder->setParameter('aluno', $parametros[ConstanteParametros::CHAVE_ALUNO]);
        }

        if (isset($parametros[ConstanteParametros::CHAVE_FUNCIONARIO]) === true && empty($parametros[ConstanteParametros::CHAVE_FUNCIONARIO]) === false) {
            $queryBuilder->andWhere('funcionario.id = :funcionario');
            $queryBuilder->setParameter('funcionario', $parametros[ConstanteParametros::CHAVE_FUNCIONARIO]);
        }

        if (isset($parametros[ConstanteParametros::CHAVE_SALA_FRANQUEADA]) === true && empty($parametros[ConstanteParametros::CHAVE_SALA_FRANQUEADA]) === false) {
            $queryBuilder->andWhere('a.sala_franqueada = :sala_franqueada');
            $queryBuilder->setParameter('sala_franqueada', $parametros[ConstanteParametros::CHAVE_SALA_FRANQUEADA]);
        }
    }

    /**
     * Lista os agendamentos para personal
     *
     * @param array $parametros
     *
     * @return array
     */
    public function listar($parametros)
    {
        $queryBuilder = $this->montaQueryBase();
        $this->filtrarFranqueada($queryBuilder);
        $this->montaFiltroDatas($queryBuilder, $parametros);
        $this->montarFiltros($queryBuilder, $parametros);

        return \App\Helper\FunctionHelper::retornaResultados($queryBuilder);
    }

    /**
     * Monta filtro de data customizado para personal
     *
     * @param \Doctrine\ORM\QueryBuilder $queryBuilder
     * @param array $parametros
     */
    protected function filtroDeDataDoPersonal ($queryBuilder, $parametros)
    {
        $queryBuilder->andWhere("contrato.situacao in ('V','E')");
        if ((isset($parametros[ConstanteParametros::CHAVE_DATA]) === true)&&(empty($parametros[ConstanteParametros::CHAVE_DATA]) === false)) {
            $dataArray = explode("T", $parametros[ConstanteParametros::CHAVE_DATA]);

            $queryBuilder->andWhere(
                $queryBuilder->expr()->orX(
                    $queryBuilder->expr()->andX(
                        $queryBuilder->expr()->gte("a.inicio", ":dataInicio"),
                        $queryBuilder->expr()->lte("a.inicio", ":dataFim"),
                        $queryBuilder->expr()->eq("a.reagendado", "0")
                    ),
                    $queryBuilder->expr()->andX(
                        $queryBuilder->expr()->gte("drpa.data_reagendada", ":dataInicio"),
                        $queryBuilder->expr()->lte("drpa.data_reagendada", ":dataFim"),
                        $queryBuilder->expr()->eq("a.reagendado", "1"),
                        $queryBuilder->expr()->eq("drpa.ultimo_reagendamento", "1")
                    )
                )
            );

            $queryBuilder->setParameter('dataInicio', $dataArray[0] . " 00:00:01");
            $queryBuilder->setParameter('dataFim', $dataArray[0] . " 23:59:59");
        } else {
            $queryBuilder->andWhere(
                $queryBuilder->expr()->orX(
                    $queryBuilder->expr()->andX(
                        $queryBuilder->expr()->eq("YEARWEEK(a.inicio, 1)", "YEARWEEK(NOW(), 1)"),
                        $queryBuilder->expr()->eq("a.reagendado", "0")
                    ),
                    $queryBuilder->expr()->andX(
                        $queryBuilder->expr()->eq("YEARWEEK(drpa.data_reagendada, 1)", "YEARWEEK(NOW(), 1)"),
                        $queryBuilder->expr()->eq("a.reagendado", "1"),
                        $queryBuilder->expr()->eq("drpa.ultimo_reagendamento", "1")
                    )
                )
            );
        }//end if
    }

       /**
     * Monta filtro de data customizado para personal
     *
     * @param \Doctrine\ORM\QueryBuilder $queryBuilder
     * @param array $parametros
     */
    protected function filtroDeDataDoPersonalDisponibilidade ($queryBuilder, $parametros)
    {
        $queryBuilder->andWhere("contrato.situacao in ('V','E')");
        if ((isset($parametros[ConstanteParametros::CHAVE_DATA_INICIAL]) === true)
                &&(empty($parametros[ConstanteParametros::CHAVE_DATA_INICIAL]) === false)
                    &&(isset($parametros[ConstanteParametros::CHAVE_DATA_FINAL]) === true)
                        &&(empty($parametros[ConstanteParametros::CHAVE_DATA_FINAL]) === false)) {                            
          
                            $dataInicial = explode("T", $parametros[ConstanteParametros::CHAVE_DATA_INICIAL]);
                            $dataFinal = explode("T", $parametros[ConstanteParametros::CHAVE_DATA_FINAL]);
                                   
                $queryBuilder->andWhere(
                $queryBuilder->expr()->orX(
                    $queryBuilder->expr()->andX(
                        $queryBuilder->expr()->gte("a.inicio", ":dataInicio"),
                        $queryBuilder->expr()->lte("a.inicio", ":dataFim"),
                        $queryBuilder->expr()->eq("a.reagendado", "0")
                    ),
                    $queryBuilder->expr()->andX(
                        $queryBuilder->expr()->gte("drpa.data_reagendada", ":dataInicio"),
                        $queryBuilder->expr()->lte("drpa.data_reagendada", ":dataFim"),
                        $queryBuilder->expr()->eq("a.reagendado", "1"),
                        $queryBuilder->expr()->eq("drpa.ultimo_reagendamento", "1")
                    )
                )
            );

            $queryBuilder->setParameter('dataInicio', $dataInicial[0] . " 00:00:01");
            $queryBuilder->setParameter('dataFim', $dataFinal[0] . " 23:59:59");
        } else {
            $queryBuilder->andWhere(
                $queryBuilder->expr()->orX(
                    $queryBuilder->expr()->andX(
                        $queryBuilder->expr()->eq("YEARWEEK(a.inicio, 1)", "YEARWEEK(NOW(), 1)"),
                        $queryBuilder->expr()->eq("a.reagendado", "0")
                    ),
                    $queryBuilder->expr()->andX(
                        $queryBuilder->expr()->eq("YEARWEEK(drpa.data_reagendada, 1)", "YEARWEEK(NOW(), 1)"),
                        $queryBuilder->expr()->eq("a.reagendado", "1"),
                        $queryBuilder->expr()->eq("drpa.ultimo_reagendamento", "1")
                    )
                )
            );
        }//end if
    }

     /**
      * Filtra a agendamentoPersonal por pagina
      *
      * @param array $parametros
      * @param number $pagina
      * @param number $numeroItensPorPagina
      *
      * @return \Knp\Component\Pager\Pagination\SlidingPagination
      */
    public function filtrarAgendamentoPersonalPorPagina($parametros, $pagina=1, $numeroItensPorPagina=50)
    {
        $opcoes       = [];
        $queryBuilder = $this->montaQueryBase();
        $this->filtrarFranqueada($queryBuilder);
        $this->filtroDeDataDoPersonal($queryBuilder, $parametros);
        $this->montarFiltros($queryBuilder, $parametros);

        if ((isset($parametros[ConstanteParametros::CHAVE_ORDENACAO_COLUNA]) === true)&&(is_null($parametros[ConstanteParametros::CHAVE_ORDENACAO_COLUNA]) === false)) {
            $queryBuilder->orderBy($parametros[ConstanteParametros::CHAVE_ORDENACAO_COLUNA], $parametros[ConstanteParametros::CHAVE_ORDENACAO_SORT]);
            $opcoes[\Knp\Component\Pager\Paginator::SORT_FIELD_PARAMETER_NAME]     = "~";
            $opcoes[\Knp\Component\Pager\Paginator::SORT_DIRECTION_PARAMETER_NAME] = "~";
        }

        return \App\Helper\FunctionHelper::montaPaginatorPaginacao($queryBuilder, $pagina, $numeroItensPorPagina, $opcoes);
    }

      
    /**
      * Filtra a agendamentoPersonal por pagina
      *
      * @param array $parametros
      * @param number $pagina
      * @param number $numeroItensPorPagina
      *
      * @return \Knp\Component\Pager\Pagination\SlidingPagination
      */
      public function consultaAgendamentoPersonalDisponibilidade($parametros, $pagina=1, $numeroItensPorPagina=99999)
      {
          $opcoes       = [];
          $queryBuilder = $this->montaQueryBase();
          $this->filtrarFranqueada($queryBuilder);
          $this->filtroDeDataDoPersonalDisponibilidade($queryBuilder, $parametros);
          $this->montarFiltros($queryBuilder, $parametros);
      
          if ((isset($parametros[ConstanteParametros::CHAVE_ORDENACAO_COLUNA]) === true)&&(is_null($parametros[ConstanteParametros::CHAVE_ORDENACAO_COLUNA]) === false)) {
              $queryBuilder->orderBy($parametros[ConstanteParametros::CHAVE_ORDENACAO_COLUNA], $parametros[ConstanteParametros::CHAVE_ORDENACAO_SORT]);
              $opcoes[\Knp\Component\Pager\Paginator::SORT_FIELD_PARAMETER_NAME]     = "~";
              $opcoes[\Knp\Component\Pager\Paginator::SORT_DIRECTION_PARAMETER_NAME] = "~";
          }
  
          return \App\Helper\FunctionHelper::montaPaginatorPaginacao($queryBuilder, $pagina, $numeroItensPorPagina, $opcoes);
      }

    /**
     * Busca historico de diario de personal por contrato
     *
     * @param int $contratoId
     *
     * @return array|NULL
     * 
     */
    public function buscarHistoricoPersonalPorContrato($contratoId)
    {
        $queryBuilder = $this->createQueryBuilder("agp");
        $queryBuilder->addSelect("al");
        $queryBuilder->addSelect("ct");
        $queryBuilder->addSelect("slf");
        $queryBuilder->addSelect("sl");
        $queryBuilder->addSelect("func");
        $queryBuilder->addSelect("liv");
        $queryBuilder->addSelect("funcPessoa");
        $queryBuilder->addSelect("adpl");
        $queryBuilder->innerJoin("agp.alunoDiarioPersonal", "al");
        $queryBuilder->innerJoin("agp.contrato", "ct");
        $queryBuilder->innerJoin("agp.franqueada", "fran");
        $queryBuilder->innerJoin("al.sala_franqueada", "slf");
        $queryBuilder->innerJoin("al.funcionario", "func");
        $queryBuilder->innerJoin("al.livro", "liv");
        $queryBuilder->innerJoin("al.aluno_diario_personal_licao", "adpl");
        $queryBuilder->innerJoin("func.pessoa", "funcPessoa");
        $queryBuilder->innerJoin("slf.sala", "sl");
        $queryBuilder->andWhere("ct.id = :contratoId");
        $queryBuilder->andWhere("fran.id = :franqueadaId");
        $queryBuilder->setParameter("contratoId", $contratoId);
        $queryBuilder->setParameter("franqueadaId", VariaveisCompartilhadas::$franqueadaID);

        return \App\Helper\FunctionHelper::retornaArrayNull($queryBuilder);
    }

    /**
     * Buscar Licoes já aplicadas para o contrato
     *
     * @param int $contratoId
     *
     * @return array|NULL
     */
    public function buscarLicoesAplicadasPorContrato($contratoId)
    {
        $queryBuilder = $this->createQueryBuilder("agp");
        $queryBuilder->select("agp.id as id, CONCAT(ANY_VALUE(DATE_FORMAT(al.data_aula,'%d/%m/%Y')), ': ', GROUP_CONCAT(lic.descricao SEPARATOR ', ')) as descricao, ct.id as contratoId");
        $queryBuilder->innerJoin("agp.alunoDiarioPersonal", "al");
        $queryBuilder->innerJoin("agp.contrato", "ct");
        $queryBuilder->innerJoin("agp.franqueada", "fran");
        $queryBuilder->innerJoin("al.aluno_diario_personal_licao", "lic");
        $queryBuilder->andWhere("ct.id = :contratoId");
        $queryBuilder->andWhere("fran.id = :franqueadaId");
        $queryBuilder->andWhere("agp.finalizado = :bFinalizado");
        $queryBuilder->setParameter("bFinalizado", true);
        $queryBuilder->setParameter("contratoId", $contratoId);
        $queryBuilder->setParameter("franqueadaId", VariaveisCompartilhadas::$franqueadaID);
        $queryBuilder->addGroupBy('agp.id');
        return \App\Helper\FunctionHelper::retornaArrayNull($queryBuilder);
    }

    /**
     * Buscar a quantidade de datas futuras do contrato do mesmo dia da semana
     *
     * @param int $contratoId
     *
     * @return array|NULL
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function buscarQtdadeAgendasAbertasPeloDiaSemana($contratoId, $dataAg)
    {
        //select para retornar a quantidade de datas futuras do contrato do mesmo dia da semana
 
        $sql = "SELECT id from agendamento_personal ap 
                WHERE contrato_id = {$contratoId}
                and DAYOFWEEK(inicio) = DAYOFWEEK('{$dataAg}')
                and inicio >= '{$dataAg}'
                    and finalizado = 0
                order by inicio";
          
        $qtdadeDias = $this->registry->getConnection()->fetchAllAssociative($sql);

        return $qtdadeDias;
    }

       /**
     * Buscar Licoes já aplicadas para o contrato
     *
     * @param int $qtdDias
     *
     * @return array|NULL
     */
    public function buscarDisponibilidadeNasSalasNosHorariosPersonal($qtdDias, $parametros)
    {
        //select para retornar a quantidade de datas futuras do contrato do mesmo dia da semana
        $dataAg = $parametros['data_aula'];

        $sql = "SELECT ap.inicio, count(*) as qtd_agendamentos
                    FROM agendamento_personal ap
                    WHERE ap.finalizado = 0
                    AND ap.franqueada_id = {$parametros['franqueada']}
                    AND ap.sala_franqueada_id = 1 
                    AND DAYOFWEEK(ap.inicio) = DAYOFWEEK('{$dataAg}')
                    AND HOUR(ap.inicio) = HOUR('{$dataAg}')
                    AND MINUTE(ap.inicio) = MINUTE('{$dataAg}')
                    AND ap.inicio >= '{$dataAg}'
                    AND DATEDIFF(ap.inicio, '{$dataAg}') <= 7 * {$qtdDias}
                    AND (SELECT COUNT(*)
                        FROM agendamento_personal as a2
                        WHERE DAYOFWEEK(a2.inicio) = DAYOFWEEK('{$dataAg}')
                            AND HOUR(a2.inicio) = HOUR('{$dataAg}')
                            AND MINUTE(a2.inicio) = MINUTE('{$dataAg}')
                            AND a2.inicio >= '{$dataAg}')
                    GROUP by ap.inicio 
                    HAVING qtd_agendamentos > 2";
          
        $diasLivres = $this->registry->getConnection()->fetchAllAssociative($sql);

        return count($diasLivres);
    }

    public function buscarDadosRelatorioAulasDesmarcadas($parametros) {
        $queryBuilder = $this->createQueryBuilder('agenda')
            ->select([
                'pessoa.nome_contato as nome_aluno',
                'livro.descricao as nome_livro',
                "date_format(agenda.inicio, '%Y-%m-%d %H:%i') as data_agenda"
            ])
            ->leftJoin("agenda.alunoDiarioPersonal", 'diario')
            ->leftJoin('agenda.contrato', 'contrato')
            ->leftJoin('contrato.aluno', 'aluno')
            ->leftJoin('contrato.livro', 'livro')
            ->leftJoin('aluno.pessoa', 'pessoa')
            ->where('agenda.franqueada = :franqueada')
            ->setParameter('franqueada', VariaveisCompartilhadas::$franqueadaID)
            ->andWhere('diario.id is null');
        
        if(isset($parametros[ConstanteParametros::CHAVE_DATA_FINAL])) {
            $dataInicial = strtotime(str_replace("/", "-",$parametros[ConstanteParametros::CHAVE_DATA_INICIAL] . " 00:00:00"));
            $dataInicial = date('Y-m-d H:i:s', $dataInicial);
            $queryBuilder->andWhere("agenda.inicio >= :data_inicial");
            $queryBuilder->setParameter('data_inicial', $dataInicial);
        }

        if(isset($parametros[ConstanteParametros::CHAVE_DATA_FINAL])) {
            $dataFinal = strtotime(str_replace("/", "-",$parametros[ConstanteParametros::CHAVE_DATA_FINAL] . " 23:59:59"));
            $dataFinal = date('Y-m-d H:i:s', $dataFinal);
            $queryBuilder->andWhere("agenda.inicio <= :data_final");
            $queryBuilder->setParameter('data_final', $dataFinal);
        }

        if(isset($parametros[ConstanteParametros::CHAVE_ALUNO])) {
            $queryBuilder->andWhere('contrato.aluno = :filtro_aluno')
                ->setParameter('filtro_aluno', $parametros[ConstanteParametros::CHAVE_ALUNO]);
        }

        return $queryBuilder->getQuery()->getResult();
    }
}
