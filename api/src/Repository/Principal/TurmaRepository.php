<?php

namespace App\Repository\Principal;

use App\Entity\Principal\Horario;
use App\Entity\Principal\HorarioAula;
use App\Entity\Principal\Turma;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Symfony\Bridge\Doctrine\RegistryInterface;
use App\Helper\ConstanteParametros;
use App\Helper\VariaveisCompartilhadas;
use App\Helper\SituacoesSistema;
use DateTime;

/**
 * @method Turma|null find($id, $lockMode = null, $lockVersion = null)
 * @method Turma|null findOneBy(array $criteria, array $orderBy = null)
 * @method Turma[]    findAll()
 * @method Turma[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class TurmaRepository extends ServiceEntityRepository
{
    /**
     * @var Registry
     */
    private $registry;

    public function __construct(RegistryInterface $registry)
    {
        parent::__construct($registry, Turma::class);
        $this->registry = $registry;
    }

    /**
     * Monta relacionamentos da query
     *
     * @param \Doctrine\ORM\QueryBuilder $queryBuilder
     */
    protected function montaRelacionamentosQuery(&$queryBuilder)
    {
        $queryBuilder->join("t.curso", "cur");
        $queryBuilder->leftJoin("cur.servico", "ser");
        $queryBuilder->leftJoin("ser.plano_conta", "planoContaCurso");

        $queryBuilder->leftJoin("ser.itemFranqueadas", "servicoItemFranqueadas");
        $queryBuilder->leftJoin("servicoItemFranqueadas.franqueada", "servicoFranqueada", "WITH", "servicoFranqueada = :filtroFranqueada OR servicoFranqueada.franqueadora = 1");

        $queryBuilder->join('t.livro', 'l');
        $queryBuilder->leftJoin('l.proximo_livro', 'pl');
        $queryBuilder->join('t.horario', 'h');
        $queryBuilder->join('h.horarioAulas', 'ha');
        $queryBuilder->leftJoin('t.contratos', 'ctts');
        $queryBuilder->leftJoin('t.sala_franqueada', 'sf');
        $queryBuilder->leftJoin('t.turmaAulas', 'tma');
        $queryBuilder->leftJoin('tma.licao', 'lic');
        $queryBuilder->leftJoin('h.turmas', 'tmh');
        $queryBuilder->join('t.modalidade_turma', 'mt');
        $queryBuilder->leftJoin('sf.sala', 'sl');
        $queryBuilder->leftJoin('t.funcionario', 'func');
        $queryBuilder->leftJoin('t.valor_hora_linhas', 'vh');
        $queryBuilder->join('t.semestre', 'sem');
        $queryBuilder->join('l.item', 'i');
        $queryBuilder->join('i.plano_conta', 'planoConta');

        $queryBuilder->leftJoin("i.itemFranqueadas", "itemFranqueadas");
        $queryBuilder->leftJoin("itemFranqueadas.franqueada", "itemFranqueada", "WITH", "itemFranqueada = :filtroFranqueada OR itemFranqueada.franqueadora = 1");

        $queryBuilder->setParameter("filtroFranqueada", VariaveisCompartilhadas::$franqueadaID);
    }

    /**
     * Monta a query padrao
     *
     * @return \Doctrine\ORM\QueryBuilder
     */
    protected function montaQueryBase()
    {
        $queryBuilder = $this->createQueryBuilder('t');
        $queryBuilder->addSelect('t, tma, sem, func, l, pl, h,ha, tmh, sf, sl, mt, cur, ser, vh, i, planoConta, planoContaCurso, lic, ctts, servicoItemFranqueadas, servicoFranqueada, itemFranqueadas, itemFranqueada');
        $this->montaRelacionamentosQuery($queryBuilder);

        return $queryBuilder;
    }

    /**
     * Query para realizar fitlro de franqueada
     *
     * @param \Doctrine\ORM\QueryBuilder $queryBuilder
     */
    private function filtrarFranqueada(&$queryBuilder)
    {
        $queryBuilder->where('t.franqueada = :franqueada');
        $queryBuilder->setParameter('franqueada', VariaveisCompartilhadas::$franqueadaID);
    }

    /**
     * Monta filtros que possuem relacionamentos
     *
     * @param \Doctrine\ORM\QueryBuilder $queryBuilder
     * @param array $parametros
     */
    protected function montaFiltrosRelacionamentos(&$queryBuilder, $parametros)
    {
        if ((isset($parametros[ConstanteParametros::CHAVE_FRANQUEADA]) === true)&& (empty($parametros[ConstanteParametros::CHAVE_FRANQUEADA]) === false)) {
            $queryBuilder->andWhere("t.franqueada = :franqueada");
            $queryBuilder->setParameter("franqueada", $parametros[ConstanteParametros::CHAVE_FRANQUEADA]);
        }

        // if ((isset($parametros[ConstanteParametros::CHAVE_APENAS_SALA]) === true)&& (empty($parametros[ConstanteParametros::CHAVE_APENAS_SALA]) === false)) {
        // $queryBuilder->andWhere("sf.personal = :personal");
        // $queryBuilder->setParameter("personal", false);
        // $queryBuilder->andWhere("sf.situacao = :situacao");
        // $queryBuilder->setParameter("situacao", true);
        // }
        if ((isset($parametros[ConstanteParametros::CHAVE_HORARIO]) === true)&& (empty($parametros[ConstanteParametros::CHAVE_HORARIO]) === false)) {
            $queryBuilder->andWhere("t.horario = :horario");
            $queryBuilder->setParameter("horario", $parametros[ConstanteParametros::CHAVE_HORARIO]);
        }

        if ((isset($parametros[ConstanteParametros::CHAVE_MODALIDADE_TURMA]) === true)&& (empty($parametros[ConstanteParametros::CHAVE_MODALIDADE_TURMA]) === false)) {
            $queryBuilder->andWhere("t.modalidade_turma = :modalidadeTurma");
            $queryBuilder->setParameter("modalidadeTurma", $parametros[ConstanteParametros::CHAVE_MODALIDADE_TURMA]);
        }

        if ((isset($parametros[ConstanteParametros::CHAVE_SALA_FRANQUEADA]) === true)&& (empty($parametros[ConstanteParametros::CHAVE_SALA_FRANQUEADA]) === false)) {
            $queryBuilder->andWhere("sl.id = :salaFranqueada");
            $queryBuilder->setParameter("salaFranqueada", $parametros[ConstanteParametros::CHAVE_SALA_FRANQUEADA]);
        }

        if ((isset($parametros[ConstanteParametros::CHAVE_FUNCIONARIO]) === true)&& (empty($parametros[ConstanteParametros::CHAVE_FUNCIONARIO]) === false)) {
            $queryBuilder->andWhere("t.funcionario = :funcionario");
            $queryBuilder->setParameter("funcionario", $parametros[ConstanteParametros::CHAVE_FUNCIONARIO]);
        }

        if ((isset($parametros[ConstanteParametros::CHAVE_CURSO]) === true)&& (empty($parametros[ConstanteParametros::CHAVE_CURSO]) === false)) {
            $queryBuilder->andWhere("cur.id = :cursoId");
            $queryBuilder->setParameter("cursoId", $parametros[ConstanteParametros::CHAVE_CURSO]);
        }

        if ((isset($parametros[ConstanteParametros::CHAVE_LIVRO]) === true)&& (empty($parametros[ConstanteParametros::CHAVE_LIVRO]) === false)) {
                $queryBuilder->andWhere("t.livro = :livro");
                $queryBuilder->setParameter("livro", $parametros[ConstanteParametros::CHAVE_LIVRO]);
        }

        if ((isset($parametros[ConstanteParametros::CHAVE_SEMESTRE]) === true) && (empty($parametros[ConstanteParametros::CHAVE_SEMESTRE]) === false)) {
            $queryBuilder->andWhere("UPPER(t.descricao) LIKE :semestre");
            $queryBuilder->setParameter("semestre", "%" . strtoupper($parametros[ConstanteParametros::CHAVE_SEMESTRE]) . "%");
        }

        if ((isset($parametros[ConstanteParametros::CHAVE_TURMA]) === true) && (empty($parametros[ConstanteParametros::CHAVE_TURMA]) === false)) {
            $queryBuilder->andWhere("t.id <> :turmaId");
            $queryBuilder->setParameter("turmaId", $parametros[ConstanteParametros::CHAVE_TURMA]);
        }
    }

    /**
     * Monta os filtros passado pela requisicao
     *
     * @param \Doctrine\ORM\QueryBuilder $queryBuilder
     * @param array $parametros
     */
    protected function montaFiltros(&$queryBuilder, $parametros)
    {
        $queryBuilder->andWhere("t.excluido = :excluido");
        $queryBuilder->setParameter("excluido", false);

        if ((isset($parametros[ConstanteParametros::CHAVE_DESCRICAO]) === true) && (empty($parametros[ConstanteParametros::CHAVE_DESCRICAO]) === false)) {
            $queryBuilder->andWhere("UPPER(t.descricao) LIKE :descricao");
            $queryBuilder->setParameter("descricao", "%" . strtoupper($parametros[ConstanteParametros::CHAVE_DESCRICAO]) . "%");
        }

        if ((isset($parametros[ConstanteParametros::CHAVE_MODALIDADE_TURMA]) === true) && (empty($parametros[ConstanteParametros::CHAVE_MODALIDADE_TURMA]) === false)) {
            $queryBuilder->andWhere("t.modalidade_turma = :modalidadeTurma");
            $queryBuilder->setParameter("modalidadeTurma", $parametros[ConstanteParametros::CHAVE_MODALIDADE_TURMA]);
        }

        if ((isset($parametros[ConstanteParametros::CHAVE_SITUACAO]) === true) && (empty($parametros[ConstanteParametros::CHAVE_SITUACAO]) === false)) {
            $queryBuilder->andWhere("t.situacao IN (:situacaoTurma)");
            $queryBuilder->setParameter("situacaoTurma", $parametros[ConstanteParametros::CHAVE_SITUACAO]);
        }

        if ((isset($parametros[ConstanteParametros::CHAVE_DIA_SEMANA]) === true) && (empty($parametros[ConstanteParametros::CHAVE_DIA_SEMANA]) === false)) {
            $queryBuilder->andWhere("ha.dia_semana IN (:diaSemana)");
            $queryBuilder->setParameter("diaSemana", $parametros[ConstanteParametros::CHAVE_DIA_SEMANA]);
        }

        if ((isset($parametros[ConstanteParametros::CHAVE_DATA_INICIO]) === true)&&(empty($parametros[ConstanteParametros::CHAVE_DATA_INICIO]) === false)) {
            $dataObj = null;
            \App\Helper\FunctionHelper::formataCampoDateTimeJS($parametros[ConstanteParametros::CHAVE_DATA_INICIO], $dataObj);
            $queryBuilder->andWhere("t.data_inicio >= :dataInicial");
            $queryBuilder->setParameter("dataInicial", $dataObj);
        }

        if ((isset($parametros[ConstanteParametros::CHAVE_DATA_FIM]) === true)&&(empty($parametros[ConstanteParametros::CHAVE_DATA_FIM]) === false)) {
            $dataObj = null;
            \App\Helper\FunctionHelper::formataCampoDateTimeJS($parametros[ConstanteParametros::CHAVE_DATA_FIM], $dataObj);
            $queryBuilder->andWhere("t.data_inicio <= :dataFinal");
            $queryBuilder->setParameter("dataFinal", $dataObj);
        }

        $this->montaFiltrosRelacionamentos($queryBuilder, $parametros);
    }

    /**
     * Listar por página
     *
     * @param array $parametros             Parametros a serem inclusos no objeto
     * @param integer $numeroItensPorPagina Número limite de itens
     *
     * @return NULL|\App\Entity\Principal\Turma[]
     */
    public function listar ($parametros, $numeroItensPorPagina=9999)
    {
        if (isset($parametros[ConstanteParametros::CHAVE_PAGINA]) === true) {
            $pagina = $parametros[ConstanteParametros::CHAVE_PAGINA];
        } else {
            $pagina = 1;
        }

        $opcoes       = [];
        $queryBuilder = $this->createQueryBuilder("t");
        $queryBuilder->distinct(true);
        $subQueryContratosTurma = "SELECT COALESCE(COUNT(turmaCts.id),0) FROM App\Entity\Principal\Contrato turmaCts WHERE turmaCts.turma = t.id AND turmaCts.situacao = :SITUACAO_VIGENTE";
        $selectQuery            = "t.id as turmaId, t.descricao as turmaDescricao, t.situacao as situcao, mod.id as modalidadeTurmaId, t.maximo_alunos as maximoAlunos, t.data_inicio as dataInicioTurma, t.data_fim as dataFimTurma, t.situacao as situacaoTurma, func.apelido as funcionarioApelido, l.descricao as livroDescricao, l.id as livroId, cur.id as cursoId, ser.id as servicoId, sem.id as semestreId, sl.descricao as salaDescricao, h.descricao as horarioDescricao";
        $selectQuery           .= ", (" . $subQueryContratosTurma . ") as qtdContratoTurma";
        $queryBuilder->select($selectQuery);
        $queryBuilder->setParameter("SITUACAO_VIGENTE", "V");
        $queryBuilder->join("t.curso", "cur");
        $queryBuilder->join("t.modalidade_turma", "mod");
        $queryBuilder->leftJoin("cur.servico", "ser");
        $queryBuilder->join('t.livro', 'l');
        $queryBuilder->leftjoin('t.horario', 'h');
        $queryBuilder->leftjoin('h.horarioAulas', 'ha');
        $queryBuilder->leftJoin('t.sala_franqueada', 'sf');
        $queryBuilder->leftJoin('sf.sala', 'sl');
        $queryBuilder->leftJoin('t.funcionario', 'func');
        $queryBuilder->join('t.semestre', 'sem');
        $this->filtrarFranqueada($queryBuilder);
        $this->montaFiltros($queryBuilder, $parametros);
        $queryBuilder->orderBy('t.data_inicio', 'DESC');
        $queryBuilder->orderBy('t.id', 'DESC');

        if ((isset($parametros[ConstanteParametros::CHAVE_ORDENACAO_COLUNA]) === true)&&(is_null($parametros[ConstanteParametros::CHAVE_ORDENACAO_COLUNA]) === false)) {
            $queryBuilder->orderBy($parametros[ConstanteParametros::CHAVE_ORDENACAO_COLUNA], $parametros[ConstanteParametros::CHAVE_ORDENACAO_SORT]);
            $opcoes[\Knp\Component\Pager\Paginator::SORT_FIELD_PARAMETER_NAME]     = "~";
            $opcoes[\Knp\Component\Pager\Paginator::SORT_DIRECTION_PARAMETER_NAME] = "~";
        }


        return \App\Helper\FunctionHelper::montaPaginatorPaginacao($queryBuilder, $pagina, $numeroItensPorPagina, $opcoes);
    }

    /**
     * Buscar turmas para mostrar no SelectOption no Front-End
     * Retorno de turmas com um resumo de filtros, retornando apenas Descrição e ID
     * 
     * Por padrão é retornado as turmas em formação[FOR] e abertas[ABE]
     * Se passado o parâmetro situacao é filtrado pela situação passada
     * É obrigatório informar o ID da franqueada através dos parâmetros vindo da rota
     * 
     * @param array 
     */
    public function buscarTurmasSelectOptions($parametros)
    {   
        if(isset($parametros[ConstanteParametros::CHAVE_FRANQUEADA])){
            $franqueadaId = $parametros[ConstanteParametros::CHAVE_FRANQUEADA];
        }else{
            throw new \Exception('Erro! Necessário informar ID da franqueada!', 422);
        }
        if(isset($parametros[ConstanteParametros::CHAVE_SITUACAO]) === false || is_null(ConstanteParametros::CHAVE_SITUACAO) === true) {
            $parametros[ConstanteParametros::CHAVE_SITUACAO] = [SituacoesSistema::SITUACAO_TURMA_ABERTA, SituacoesSistema::SITUACAO_TURMA_EM_FORMACAO,];
        }
    
        $queryBuilder = $this->createQueryBuilder('t');
        $queryBuilder->select('t.id, t.descricao, t.situacao');
        $queryBuilder->distinct();
        $queryBuilder->where('t.franqueada = :franqueada');
        $queryBuilder->setParameter(':franqueada', $franqueadaId);
        
        $queryBuilder->andWhere('t.excluido = 0');
        $queryBuilder->andWhere('t.situacao IN (:situacoes)');
        $queryBuilder->setParameter('situacoes', $parametros[ConstanteParametros::CHAVE_SITUACAO]);
        $queryBuilder->orderBy('t.descricao');
        $result = $queryBuilder->getQuery()->getResult(\Doctrine\ORM\Query::HYDRATE_ARRAY);

        return $result;
    }

    /**
     * Busca as turmas da franqueada conforme parametros
     *
     * @param array $parametros
     * @param number $numeroItensPorPagina
     *
     * @return \Knp\Component\Pager\Pagination\SlidingPagination
     */
    public function buscarTurmas($parametros, $numeroItensPorPagina=30)
    {
        if (isset($parametros[ConstanteParametros::CHAVE_PAGINA]) === true) {
            $pagina = $parametros[ConstanteParametros::CHAVE_PAGINA];
        } else {
            $pagina = 1;
        }

        if (isset($parametros[ConstanteParametros::CHAVE_SITUACAO]) === false || is_null(ConstanteParametros::CHAVE_SITUACAO) === true) {
            $parametros[ConstanteParametros::CHAVE_SITUACAO] = [
                SituacoesSistema::SITUACAO_TURMA_ABERTA,
                SituacoesSistema::SITUACAO_TURMA_EM_FORMACAO,
            ];
        }

        $opcoes = [];

        $queryBuilder = $this->createQueryBuilder('t');
        $queryBuilder->distinct();

        $queryBuilder->select(
            "t.id as turmaId, 
                                t.descricao as turmaDescricao, 
                                func.apelido as apelidoFuncionario, 
                                sl.descricao as descricaoSala, 
                                t.maximo_alunos as qtdMaxAluno, 
                                l.descricao as nomeLivro, 
                                lic.descricao as nomeLicao, 
                                t.situacao as situacaoTurma"
        );

        $queryBuilder->join("t.curso", "cur");
        $queryBuilder->join('t.livro', 'l');
        $queryBuilder->join('t.horario', 'h');
        $queryBuilder->join('h.horarioAulas', 'ha');
        $queryBuilder->leftJoin('t.sala_franqueada', 'sf');
        $queryBuilder->leftJoin('sf.sala', 'sl');
        $queryBuilder->leftJoin('t.turmaAulas', 'tma');
        $queryBuilder->leftJoin('tma.licao', 'lic');
        $queryBuilder->leftJoin('t.funcionario', 'func');

        $this->filtrarFranqueada($queryBuilder);
        $this->montaFiltros($queryBuilder, $parametros);
        $queryBuilder->andWhere("tma.id = FIRST(SELECT tt.id FROM \App\Entity\Principal\TurmaAula tt WHERE tt.finalizada = 0 AND tt.franqueada = t.franqueada and tt.turma = t.id)");

       //
        if ((isset($parametros[ConstanteParametros::CHAVE_ORDENACAO_COLUNA]) === false)||(is_null($parametros[ConstanteParametros::CHAVE_ORDENACAO_COLUNA]) === true)) {
            $parametros[ConstanteParametros::CHAVE_ORDENACAO_COLUNA] = 't.descricao';
            $parametros[ConstanteParametros::CHAVE_ORDENACAO_SORT]   = 'ASC';
        }

        $queryBuilder->orderBy($parametros[ConstanteParametros::CHAVE_ORDENACAO_COLUNA], $parametros[ConstanteParametros::CHAVE_ORDENACAO_SORT]);
        $opcoes[\Knp\Component\Pager\Paginator::SORT_FIELD_PARAMETER_NAME]     = "~";
        $opcoes[\Knp\Component\Pager\Paginator::SORT_DIRECTION_PARAMETER_NAME] = "~";

        if (isset($parametros['buscar_todas']) === true && $parametros['buscar_todas'] === '1') {
            $resultado = $queryBuilder->getQuery()->getResult(\Doctrine\ORM\Query::HYDRATE_ARRAY);
            return $resultado;
        } else {
            return \App\Helper\FunctionHelper::montaPaginatorPaginacao($queryBuilder, $pagina, $numeroItensPorPagina, $opcoes);
        }
    }

       /**
     * Busca as turmas da franqueada conforme parametros
     *
     * @param array $parametros
     * @param number $numeroItensPorPagina
     *
     * @return \Knp\Component\Pager\Pagination\SlidingPagination
     */
    public function buscarTurmasComFiltroData($parametros)
    {
        if (isset($parametros[ConstanteParametros::CHAVE_SITUACAO]) === false || is_null(ConstanteParametros::CHAVE_SITUACAO) === true) {
            $parametros[ConstanteParametros::CHAVE_SITUACAO] = [
                SituacoesSistema::SITUACAO_TURMA_ABERTA,
                SituacoesSistema::SITUACAO_TURMA_EM_FORMACAO,
            ];
        }
        $queryBuilder = $this->createQueryBuilder('t');
        $queryBuilder->distinct();

        $queryBuilder->select(
            "t.id as turmaId, 
                                t.descricao as turmaDescricao, 
                                func.apelido as apelidoFuncionario, 
                                sl.descricao as descricaoSala, 
                                t.maximo_alunos as qtdMaxAluno, 
                                l.descricao as nomeLivro, 
                                lic.descricao as nomeLicao, 
                                t.data_inicio as dataInicia, 
                                t.data_fim as dataFinal, 
                                t.situacao as situacaoTurma"
        );

        $queryBuilder->join("t.curso", "cur");
        $queryBuilder->join('t.livro', 'l');
        $queryBuilder->join('t.horario', 'h');
        $queryBuilder->join('h.horarioAulas', 'ha');
        $queryBuilder->leftJoin('t.sala_franqueada', 'sf');
        $queryBuilder->leftJoin('sf.sala', 'sl');
        $queryBuilder->leftJoin('t.turmaAulas', 'tma');
        $queryBuilder->leftJoin('tma.licao', 'lic');
        $queryBuilder->leftJoin('t.funcionario', 'func');
        $this->filtrarFranqueada($queryBuilder);

        
        if ((isset($parametros[ConstanteParametros::CHAVE_TURMA]) === true)&&(empty($parametros[ConstanteParametros::CHAVE_TURMA]) === false)) {
            $queryBuilder->andWhere("t.id >= :turmaId");
            $queryBuilder->setParameter("turmaId", $parametros[ConstanteParametros::CHAVE_TURMA]);
        } else {
            if ((isset($parametros[ConstanteParametros::CHAVE_DATA_INICIAL]) === true)&&(empty($parametros[ConstanteParametros::CHAVE_DATA_INICIAL]) === false)) {
                $dataObj = null;
                \App\Helper\FunctionHelper::formataCampoDateTimeJS($parametros[ConstanteParametros::CHAVE_DATA_INICIAL], $dataObj);
                $dataObj = $dataObj->format('Y-m-d'). ' 00:00:01';
                $queryBuilder->andWhere("t.data_inicio >= :dataInicial");
                $queryBuilder->setParameter("dataInicial", $dataObj);
            }
            
            if ((isset($parametros[ConstanteParametros::CHAVE_DATA_FINAL]) === true)&&(empty($parametros[ConstanteParametros::CHAVE_DATA_FINAL]) === false)) {
                $dataObj = null;
                \App\Helper\FunctionHelper::formataCampoDateTimeJS($parametros[ConstanteParametros::CHAVE_DATA_FINAL], $dataObj);
                $dataObj = $dataObj->format('Y-m-d'). ' 23:23:59';
                $queryBuilder->andWhere("t.data_fim <= :dataFinal");
                $queryBuilder->setParameter("dataFinal", $dataObj);
            }       
        }

        // if ((isset($parametros[ConstanteParametros::CHAVE_ORDENACAO_COLUNA]) === false)||(is_null($parametros[ConstanteParametros::CHAVE_ORDENACAO_COLUNA]) === true)) {
        //     $parametros[ConstanteParametros::CHAVE_ORDENACAO_COLUNA] = 't.descricao';
        //     $parametros[ConstanteParametros::CHAVE_ORDENACAO_SORT]   = 'ASC';
        // }

        // $queryBuilder->orderBy($parametros[ConstanteParametros::CHAVE_ORDENACAO_COLUNA], $parametros[ConstanteParametros::CHAVE_ORDENACAO_SORT]);
        // $opcoes[\Knp\Component\Pager\Paginator::SORT_FIELD_PARAMETER_NAME]     = "~";
        // $opcoes[\Knp\Component\Pager\Paginator::SORT_DIRECTION_PARAMETER_NAME] = "~";

        $resultado = $queryBuilder->getQuery()->getResult(\Doctrine\ORM\Query::HYDRATE_ARRAY);
        return $resultado;
   }

    /**
     * Buscar turma por ID
     *
     * @param integer $id
     *
     * @return NULL|\App\Entity\Principal\Turma[]
     */
    public function buscarPorId ($id, $objetoORM=null)
    {
        $queryBuilder = $this->montaQueryBase();
        $this->filtrarFranqueada($queryBuilder);
        $queryBuilder->distinct(true);
        $queryBuilder->select(
            "cur.id as cursoId,
                                cur.descricao as cursoDescricao,
                                func.id as funcionarioId,
                                func.apelido as funcionarioApelido,
                                l.id as livroId,
                                l.descricao as livroDescricao,
                                mt.id as modalidadeId, 
                                mt.descricao as modalidadeDescricao,
                                sl.id as salaId,
                                sl.descricao as salaDescricao,
                                t.maximo_alunos as turmaMaximoAlunos,
                                t.data_inicio as turmaDataInicio,
                                t.data_fim as turmaDataFim,
                                t.situacao as turmaSituacao,
                                t.observacao as turmaObservacao,
                                t.id as turmaId, 
                                t.descricao as turmaDescricao, 
                                t.intensidade as turmaIntensidade,
                                h.id as horarioId,
                                h.descricao as horarioDescricao,
                                vh.descricao as valorHoraDescricao,
                                sem.id as semestreId"
        );
        $queryBuilder->andWhere('t.id = :id');
        $queryBuilder->setParameter('id', $id);

        return $queryBuilder->getQuery()->getOneOrNullResult(\Doctrine\ORM\Query::HYDRATE_ARRAY);
    }

    /**
     * Buscar alunos da turma pelo ID da turma
     *
     * @param integer $id
     * @param array $parametros
     *
     * @return NULL|\App\Entity\Principal\Aluno[]
     */
    public function buscarAlunosTurmaPorId ($id, $parametros)
    {
        $queryBuilder = $this->montaQueryBase();
        $this->filtrarFranqueada($queryBuilder);
        $queryBuilder->distinct(true);
        $queryBuilder->select(
            "ctts.sequencia_contrato as sequencia_contrato,
                                ctts.data_matricula as data_matricula,
                                ctts.tipo_contrato as tipo_contrato,
                                al.id as id,
                                pe.nome_contato as nome_contato,
                                pe.telefone_preferencial,
                                pe.data_nascimento as data_nascimento,
                                ctts.situacao as situacao_contrato"
        );
        $queryBuilder->leftJoin("ctts.aluno", "al");
        $queryBuilder->leftJoin("al.pessoa", "pe");
        $queryBuilder->andWhere('t.id = :id');

        if (isset($parametros[ConstanteParametros::CHAVE_BUSCAR_ENCERRADOS]) === true) {
            $queryBuilder->andWhere(
                $queryBuilder->expr()->orX(
                    $queryBuilder->expr()->eq("ctts.situacao", ':SITUACAO_VIGENTE'),
                    $queryBuilder->expr()->eq("ctts.situacao", ':SITUACAO_ENCERRADO')
                )
            );
            $queryBuilder->setParameter('SITUACAO_ENCERRADO', SituacoesSistema::SITUACAO_CONTRATO_ENCERRADO);
        } else {
            $queryBuilder->andWhere('ctts.situacao = :SITUACAO_VIGENTE');
        }

        $queryBuilder->setParameter('id', $id);
        $queryBuilder->setParameter('SITUACAO_VIGENTE', SituacoesSistema::SITUACAO_CONTRATO_VIGENTE);
        return $queryBuilder->getQuery()->getResult(\Doctrine\ORM\Query::HYDRATE_ARRAY);
    }

    /**
     * Verifica se a sala está ocupada em determinado horário
     *
     * @param \App\Entity\Principal\SalaFranqueada $salaFranqueada
     * @param \App\Entity\Principal\Horario $horario
     * @param int $id
     *
     * @return \App\Entity\Principal\Turma|null
     */
    public function salaEstaOcupadaNoHorario ($salaFranqueada, $horario, $dataInicio, $dataFim, $id=null)
    {
        $query = $this->createQueryBuilder('t')
            ->where('t.sala_franqueada = :sala')
            ->andWhere('t.horario = :horario')
            ->andWhere(':dataini BETWEEN t.data_inicio AND t.data_fim')
            ->andWhere(':datafim BETWEEN t.data_inicio AND t.data_fim')
            ->andWhere('t.franqueada = :franqueada')
            ->andWhere('t.excluido = :excluido')
            ->andWhere("t.situacao in (:situacao)");

        $params = [
            'sala'       => $salaFranqueada,
            'horario'    => $horario,
            'dataini'    => $dataInicio,
            'datafim'    => $dataFim,
            'franqueada' => VariaveisCompartilhadas::$franqueadaID,
            'excluido'   => 0,
            'situacao'   => [
                'ABE',
                'FOR',
            ],
        ];

        if (is_null($id) === false) {
            $query->andWhere('t.id != :id');
            $params[ConstanteParametros::CHAVE_ID] = $id;
        }

        $query->setParameters($params);

        return is_null($query->getQuery()->getOneOrNullResult()) === false;
    }

    /**
     * Verifica se o funcionário está ocupado em determinado horário
     *
     * @param \App\Entity\Principal\Funcionario $funcionario
     * @param \App\Entity\Principal\Horario $horario
     * @param int $id
     *
     * @return \App\Entity\Principal\Turma|null
     */
    public function funcionarioEstaOcupadoNoHorario ($funcionario, $horario, $id=null)
    {
        $query = $this->createQueryBuilder('t')
            ->where('t.funcionario = :funcionario')
            ->andWhere('t.horario = :horario')
            ->andWhere('t.franqueada = :franqueada')
            ->andWhere('t.excluido = :excluido')
            ->andWhere("t.situacao in (:situacao)");

        $params = [
            'funcionario' => $funcionario,
            'horario'     => $horario,
            'franqueada'  => VariaveisCompartilhadas::$franqueadaID,
            'excluido'    => 0,
            'situacao'    => [
                'ABE',
                'FOR',
            ],
        ];

        if (is_null($id) === false) {
            $query->andWhere('t.id != :id');
            $params[ConstanteParametros::CHAVE_ID] = $id;
        }

        $query->setParameters($params);

        return is_null($query->getQuery()->getOneOrNullResult()) === false;
    }

    /**
     * Buscar turmas que contenham a descrição
     *
     * @param string $descricao
     *
     * @return \App\Entity\Principal\Turma[]
     */
    public function buscarPorDescricao ($descricao)
    {
        $queryBuilder = $this->createQueryBuilder('turma');
        $queryBuilder->join('turma.franqueada', 'franqueada');
        $queryBuilder->where('UPPER(turma.descricao) LIKE :descricao');
        $queryBuilder->andWhere('franqueada.id = :franqueada');
        $queryBuilder->setParameter('descricao', "%$descricao%");
        $queryBuilder->setParameter('franqueada', VariaveisCompartilhadas::$franqueadaID);

        return $queryBuilder->getQuery()->getResult(\Doctrine\ORM\Query::HYDRATE_ARRAY);
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
        $queryBuilder = $this->createQueryBuilder("t");
        $queryBuilder->select('t.id');
        $queryBuilder->innerJoin('t.curso', 'c');
        $queryBuilder->innerJoin('c.idioma', 'i');
        $queryBuilder->innerJoin('t.livro', 'l');
        $queryBuilder->innerJoin('t.horario', 'h');
        $queryBuilder->leftJoin('t.funcionario', 'f');
        $queryBuilder->leftJoin('f.pessoa', 'p');
        $queryBuilder->leftJoin('t.sala_franqueada', 'sf');
        $queryBuilder->leftJoin('sf.sala', 's');

        $queryBuilder->andWhere("t.excluido = 0");

        $queryBuilder->andWhere('t.franqueada = :franqueada');
        $queryBuilder->setParameter('franqueada', VariaveisCompartilhadas::$franqueadaID);

        if (is_null($parametros[ConstanteParametros::CHAVE_CURSO]) === false) {
            $queryBuilder->andWhere('c = :curso');
            $queryBuilder->setParameter('curso', $parametros[ConstanteParametros::CHAVE_CURSO]);
        }

        if (is_null($parametros[ConstanteParametros::CHAVE_IDIOMA]) === false) {
            $queryBuilder->andWhere('i = :idioma');
            $queryBuilder->setParameter('idioma', $parametros[ConstanteParametros::CHAVE_IDIOMA]);
        }

        if (is_null($parametros[ConstanteParametros::CHAVE_LIVRO]) === false) {
            $queryBuilder->andWhere('l = :livro');
            $queryBuilder->setParameter('livro', $parametros[ConstanteParametros::CHAVE_LIVRO]);
        }

        if (is_null($parametros[ConstanteParametros::CHAVE_SITUACAO]) === false) {
            $situacao = explode(',', $parametros[ConstanteParametros::CHAVE_SITUACAO]);
            $queryBuilder->andWhere('t.situacao in (:situacao)');
            $queryBuilder->setParameter('situacao', implode("', '", $situacao));
        }

        if (is_null($parametros[ConstanteParametros::CHAVE_MODALIDADE]) === false) {
            $queryBuilder->andWhere('t.modalidade_turma = :modalidade');
            $queryBuilder->setParameter('modalidade', $parametros[ConstanteParametros::CHAVE_MODALIDADE]);
        }

        // Filtra e substitui a query para passar ao Jasper
        $sql = $queryBuilder->getQuery()->getSQL();

        $sql = preg_replace('/.*WHERE\s(.*)$/', '$1', $sql);

        // Seleciona somente os wheres
        $sql = preg_replace('/t0_/', 'turma', $sql);
        $sql = preg_replace('/c1_/', 'curso', $sql);
        $sql = preg_replace('/i2_/', 'idioma', $sql);
        $sql = preg_replace('/l3_/', 'livro', $sql);
        $sql = preg_replace('/h4_/', 'horario', $sql);
        $sql = preg_replace('/f5_/', 'funcionario', $sql);
        $sql = preg_replace('/p6_/', 'pessoa', $sql);
        $sql = preg_replace('/s7_/', 'sala_franqueada', $sql);
        $sql = preg_replace('/s8_/', 'sala', $sql);

        // Substituição de parâmetros
        $parameters = $queryBuilder->getParameters();
        foreach ($parameters as $parameter) {
            $param = $parameter->getValue();
            $sql   = preg_replace('/\?/', "'$param'", $sql, 1);
        }

        return $sql;
    }

    /**
     * Buscar dados filtrados da turma
     *
     * @param int $id
     * @param array $parametros
     *
     * @return array|NULL
     */
    public function buscarDadosTurmaTurmaAulaPorId($id, $parametros)
    {
        $queryBuilder = $this->createQueryBuilder('t');
        $queryBuilder->addSelect("cur");
        $queryBuilder->addSelect("ctts");
        $queryBuilder->addSelect("al");
        $queryBuilder->addSelect("pessAluno");
        $queryBuilder->addSelect("tma");
        $queryBuilder->addSelect("tmaLicao");
        $queryBuilder->addSelect("tmaFunc");
        $queryBuilder->addSelect("func");
        $queryBuilder->addSelect("liv");
        $queryBuilder->addSelect("slf");
        $queryBuilder->addSelect("sl");
        $queryBuilder->addSelect("ald");
        $queryBuilder->addSelect("alLicao");
        $queryBuilder->addSelect("alTma");
        $queryBuilder->addSelect("ala");
        $queryBuilder->addSelect("alac");
        $queryBuilder->addSelect("anl1");
        $queryBuilder->addSelect("ans1");
        $queryBuilder->addSelect("anw1");
        $queryBuilder->addSelect("anl2");
        $queryBuilder->addSelect("ans2");
        $queryBuilder->addSelect("anw2");
        $queryBuilder->addSelect("nmto");
        $queryBuilder->addSelect("nfo");
        $queryBuilder->addSelect("rmto");
        $queryBuilder->addSelect("rfto");
        $queryBuilder->leftJoin("t.curso", "cur");
        $queryBuilder->leftJoin("t.contratos", "ctts");
        $queryBuilder->leftJoin("ctts.aluno", "al");
        $queryBuilder->leftJoin("al.pessoa", "pessAluno");
        $queryBuilder->leftJoin("t.turmaAulas", "tma");
        $queryBuilder->leftJoin("tma.licao", "tmaLicao");
        $queryBuilder->leftJoin("tma.funcionario", "tmaFunc");
        $queryBuilder->leftJoin("t.funcionario", "func");
        $queryBuilder->leftJoin("t.livro", "liv");
        $queryBuilder->leftJoin("t.sala_franqueada", "slf");
        $queryBuilder->leftJoin("slf.sala", "sl");
        if ((isset($parametros[ConstanteParametros::CHAVE_TURMA_AULA]) === true) && (empty($parametros[ConstanteParametros::CHAVE_TURMA_AULA]) === false)) {
            $queryBuilder->join("al.alunoDiarios", "ald", "WITH", "ald.aluno = al AND ald.livro = liv AND ald.turma_aula = tma");
            $queryBuilder->andWhere("tma.id = :turmaAulaId");
            $queryBuilder->setParameter('turmaAulaId', $parametros[ConstanteParametros::CHAVE_TURMA_AULA]);
        } else {
            $queryBuilder->leftJoin("al.alunoDiarios", "ald", "WITH", "ald.aluno = al AND ald.livro = liv AND ald.turma_aula = tma");
        }

        $queryBuilder->leftJoin("ald.licao", "alLicao");
        $queryBuilder->leftJoin("ald.turma_aula", "alTma");
        $queryBuilder->leftJoin("al.alunoAvaliacaos", "ala", "WITH", "ala.aluno = al AND ala.livro = liv AND ala.turma = t");
        $queryBuilder->leftJoin("ala.nota_mid_term_oral", "nmto");
        $queryBuilder->leftJoin("ala.nota_final_oral", "nfo");
        $queryBuilder->leftJoin("ala.nota_retake_mid_term_oral", "rmto");
        $queryBuilder->leftJoin("ala.nota_retake_final_oral", "rfto");
        $queryBuilder->leftJoin("al.alunoAvaliacaoConceituals", "alac", "WITH", "alac.turma = t AND alac.aluno = al AND alac.livro = liv");
        $queryBuilder->leftJoin("alac.nota_listening_1", "anl1");
        $queryBuilder->leftJoin("alac.nota_speaking_1", "ans1");
        $queryBuilder->leftJoin("alac.nota_writing_1", "anw1");
        $queryBuilder->leftJoin("alac.nota_listening_2", "anl2");
        $queryBuilder->leftJoin("alac.nota_speaking_2", "ans2");
        $queryBuilder->leftJoin("alac.nota_writing_2", "anw2");
        $queryBuilder->andWhere('t.id = :id');
        $queryBuilder->setParameter('id', $id);
        return $queryBuilder->getQuery()->getOneOrNullResult(\Doctrine\ORM\Query::HYDRATE_ARRAY);

    }

       /**
     * Buscar dados filtrados da turma
     *
     * @param int $id
     * @param array $parametros
     *
     * @return array|NULL
     */
    public function buscarDadosTurmaTurmaAulaPorDataFuncionario($parametros)
    {
        $queryBuilder = $this->createQueryBuilder('t');
        $queryBuilder->addSelect("cur");
        $queryBuilder->addSelect("ctts");
        $queryBuilder->addSelect("al");
        $queryBuilder->addSelect("pessAluno");
        $queryBuilder->addSelect("tma");
        $queryBuilder->addSelect("tmaLicao");
        $queryBuilder->addSelect("tmaFunc");
        $queryBuilder->addSelect("func");
        $queryBuilder->addSelect("liv");
        $queryBuilder->addSelect("slf");
        $queryBuilder->addSelect("sl");
        $queryBuilder->addSelect("ald");
        $queryBuilder->addSelect("alLicao");
        $queryBuilder->addSelect("alTma");
        $queryBuilder->addSelect("ala");
        $queryBuilder->addSelect("alac");
        $queryBuilder->addSelect("anl1");
        $queryBuilder->addSelect("ans1");
        $queryBuilder->addSelect("anw1");
        $queryBuilder->addSelect("anl2");
        $queryBuilder->addSelect("ans2");
        $queryBuilder->addSelect("anw2");
        $queryBuilder->addSelect("nmto");
        $queryBuilder->addSelect("nfo");
        $queryBuilder->addSelect("rmto");
        $queryBuilder->addSelect("rfto");
        $queryBuilder->leftJoin("t.curso", "cur");
        $queryBuilder->leftJoin("t.contratos", "ctts");
        $queryBuilder->leftJoin("ctts.aluno", "al");
        $queryBuilder->leftJoin("al.pessoa", "pessAluno");
        $queryBuilder->leftJoin("t.turmaAulas", "tma");
        $queryBuilder->leftJoin("tma.licao", "tmaLicao");
        $queryBuilder->leftJoin("tma.funcionario", "tmaFunc");
        $queryBuilder->leftJoin("t.funcionario", "func");
        $queryBuilder->leftJoin("t.livro", "liv");
        $queryBuilder->leftJoin("t.sala_franqueada", "slf");
        $queryBuilder->leftJoin("slf.sala", "sl");
        $queryBuilder->leftJoin("al.alunoDiarios", "ald", "WITH", "ald.aluno = al AND ald.livro = liv AND ald.turma_aula = tma");

        $queryBuilder->leftJoin("ald.licao", "alLicao");
        $queryBuilder->leftJoin("ald.turma_aula", "alTma");
        $queryBuilder->leftJoin("al.alunoAvaliacaos", "ala", "WITH", "ala.aluno = al AND ala.livro = liv AND ala.turma = t");
        $queryBuilder->leftJoin("ala.nota_mid_term_oral", "nmto");
        $queryBuilder->leftJoin("ala.nota_final_oral", "nfo");
        $queryBuilder->leftJoin("ala.nota_retake_mid_term_oral", "rmto");
        $queryBuilder->leftJoin("ala.nota_retake_final_oral", "rfto");
        $queryBuilder->leftJoin("al.alunoAvaliacaoConceituals", "alac", "WITH", "alac.turma = t AND alac.aluno = al AND alac.livro = liv");
        $queryBuilder->leftJoin("alac.nota_listening_1", "anl1");
        $queryBuilder->leftJoin("alac.nota_speaking_1", "ans1");
        $queryBuilder->leftJoin("alac.nota_writing_1", "anw1");
        $queryBuilder->leftJoin("alac.nota_listening_2", "anl2");
        $queryBuilder->leftJoin("alac.nota_speaking_2", "ans2");
        $queryBuilder->leftJoin("alac.nota_writing_2", "anw2");

        $this->filtrarFranqueada($queryBuilder);

        if ((isset($parametros[ConstanteParametros::CHAVE_TURMA]) === true)&&(empty($parametros[ConstanteParametros::CHAVE_TURMA]) === false)) {
            $queryBuilder->andWhere("t.id = :turmaId");
            $queryBuilder->setParameter("turmaId", $parametros[ConstanteParametros::CHAVE_TURMA]);
        } else {
            if ((isset($parametros[ConstanteParametros::CHAVE_INSTRUTOR_FLAG]) === true)&&(empty($parametros[ConstanteParametros::CHAVE_INSTRUTOR_FLAG]) === false)) {
                $queryBuilder->andWhere("tma.funcionario = :instrutorId");
                $queryBuilder->setParameter("instrutorId", $parametros[ConstanteParametros::CHAVE_INSTRUTOR_FLAG]);
            }

            if ((isset($parametros[ConstanteParametros::CHAVE_DATA_INICIAL]) === true)&&(empty($parametros[ConstanteParametros::CHAVE_DATA_INICIAL]) === false)) {
                $dataIniArray =  explode("/", $parametros[ConstanteParametros::CHAVE_DATA_INICIAL]);
               $dataini = new DateTime($dataIniArray[2]. '-' . $dataIniArray[1] . '-' . $dataIniArray[0]);
                $dataObj = null;
               // \App\Helper\FunctionHelper::formataCampoDateTimeJS($parametros[ConstanteParametros::CHAVE_DATA_INICIAL], $dataini);
                $dataObj = $dataini->format('Y-m-d'). ' 00:00:01';
                $queryBuilder->andWhere("t.data_inicio >= :dataInicial");
                $queryBuilder->setParameter("dataInicial", $dataObj);
            }
            
            if ((isset($parametros[ConstanteParametros::CHAVE_DATA_FINAL]) === true)&&(empty($parametros[ConstanteParametros::CHAVE_DATA_FINAL]) === false)) {
                $dataFimArray =  explode("/", $parametros[ConstanteParametros::CHAVE_DATA_FINAL]);
                $dataFim = new DateTime($dataFimArray[2]. '-' . $dataFimArray[1] . '-' . $dataFimArray[0]);
                $dataObj = null;
              //  \App\Helper\FunctionHelper::formataCampoDateTimeJS($parametros[ConstanteParametros::CHAVE_DATA_FINAL], $dataObj);
                $dataObj = $dataFim->format('Y-m-d'). ' 23:23:59';
                $queryBuilder->andWhere("t.data_fim <= :dataFinal");
                $queryBuilder->setParameter("dataFinal", $dataObj);
            }       
        }

        return $queryBuilder->getQuery()->getResult(\Doctrine\ORM\Query::HYDRATE_ARRAY);

    }

          /**
     * Buscar dados filtrados da turma
     *
     * @param int $id
     * @param array $parametros
     *
     * @return array|NULL
     */
    public function buscarDadosTurmaTurmaAulaPorDataFuncionarioRelatorio($parametros)
    {
        $queryBuilder = $this->createQueryBuilder('t');
        $queryBuilder->addSelect("cur");
        $queryBuilder->addSelect("ctts");
        $queryBuilder->addSelect("al");
        $queryBuilder->addSelect("pessAluno");
        $queryBuilder->addSelect("tma");
        $queryBuilder->addSelect("tmaLicao");
        $queryBuilder->addSelect("tHorario");
        $queryBuilder->addSelect("tmaFunc");
        $queryBuilder->addSelect("func");
        $queryBuilder->addSelect("pessFunc");
        $queryBuilder->addSelect("aldFunc");        
        $queryBuilder->addSelect("liv");
        $queryBuilder->addSelect("slf");
        $queryBuilder->addSelect("sl");
        $queryBuilder->addSelect("ald");
        $queryBuilder->addSelect("alLicao");
        $queryBuilder->addSelect("alTma");
        $queryBuilder->addSelect("ala");
        $queryBuilder->addSelect("alac");
        $queryBuilder->addSelect("anl1");
        $queryBuilder->addSelect("ans1");
        $queryBuilder->addSelect("anw1");
        $queryBuilder->addSelect("anl2");
        $queryBuilder->addSelect("ans2");
        $queryBuilder->addSelect("anw2");
        $queryBuilder->addSelect("nmto");
        $queryBuilder->addSelect("nfo");
        $queryBuilder->addSelect("rmto");
        $queryBuilder->addSelect("rfto");                       
        $queryBuilder->leftJoin("t.curso", "cur");
        $queryBuilder->leftJoin("t.contratos", "ctts");
        $queryBuilder->leftJoin("ctts.aluno", "al" );
        $queryBuilder->leftJoin("al.pessoa", "pessAluno");
        $queryBuilder->leftJoin("t.turmaAulas", "tma");
        $queryBuilder->leftJoin("tma.licao", "tmaLicao");
        $queryBuilder->leftJoin("t.horario", "tHorario");
        $queryBuilder->leftJoin("tma.funcionario", "tmaFunc");
        $queryBuilder->leftJoin("t.funcionario", "func");
        $queryBuilder->leftJoin("func.pessoa", "pessFunc");
        $queryBuilder->leftJoin("t.livro", "liv");
        $queryBuilder->leftJoin("t.sala_franqueada", "slf");
        $queryBuilder->leftJoin("slf.sala", "sl");
        $queryBuilder->leftJoin("al.alunoDiarios", "ald", "WITH", "ald.aluno = al AND ald.livro = liv AND ald.turma_aula = tma");
        $queryBuilder->leftJoin("ald.funcionario", "aldFunc");
        $queryBuilder->leftJoin("ald.licao", "alLicao");
        $queryBuilder->leftJoin("ald.turma_aula", "alTma");
        $queryBuilder->leftJoin("al.alunoAvaliacaos", "ala", "WITH", "ala.aluno = al AND ala.livro = liv AND ala.turma = t");
        $queryBuilder->leftJoin("ala.nota_mid_term_oral", "nmto");
        $queryBuilder->leftJoin("ala.nota_final_oral", "nfo");
        $queryBuilder->leftJoin("ala.nota_retake_mid_term_oral", "rmto");
        $queryBuilder->leftJoin("ala.nota_retake_final_oral", "rfto");
        $queryBuilder->leftJoin("al.alunoAvaliacaoConceituals", "alac", "WITH", "alac.turma = t AND alac.aluno = al AND alac.livro = liv");
        $queryBuilder->leftJoin("alac.nota_listening_1", "anl1");
        $queryBuilder->leftJoin("alac.nota_speaking_1", "ans1");
        $queryBuilder->leftJoin("alac.nota_writing_1", "anw1");
        $queryBuilder->leftJoin("alac.nota_listening_2", "anl2");
        $queryBuilder->leftJoin("alac.nota_speaking_2", "ans2");
        $queryBuilder->leftJoin("alac.nota_writing_2", "anw2");

        $this->filtrarFranqueada($queryBuilder);        
        
        if (isset($parametros[ConstanteParametros::CHAVE_TURMA])) {
            $queryBuilder->andWhere("t.id in ({$parametros[ConstanteParametros::CHAVE_TURMA]})");
           // $queryBuilder->setParameter("turmaId", $parametros[ConstanteParametros::CHAVE_TURMA]);
        } else {
            return null;    
        }
        
        return $queryBuilder->getQuery()->getResult(\Doctrine\ORM\Query::HYDRATE_ARRAY);
        
    }

    /**
     * Consulta aulas de turmas dadas pelo funcionário
     *
     * @param array $parametros
     */
    public function consultaAulasTurmaFuncionarioParaPagamento($parametros)
    {
        $queryBuilder = $this->createQueryBuilder("t");
        $queryBuilder->select("funcionario, t, contrato, aula, diario, pagamento");

        $queryBuilder->join("t.franqueada", "fran");
        $queryBuilder->join("t.contratos", "contrato");
        $queryBuilder->join("t.turmaAulas", "aula");
        $queryBuilder->join("aula.alunoDiarios", "diario");
        $queryBuilder->join("diario.funcionario", "funcionario");
        $queryBuilder->leftJoin("aula.pagamentoTurmaAulas", "pagamento");

        $this->filtrarFranqueada($queryBuilder);
        $queryBuilder->andWhere("diario.funcionario = :funcionario");

        if (isset($parametros[ConstanteParametros::CHAVE_DATA_INICIO]) === true && empty($parametros[ConstanteParametros::CHAVE_DATA_INICIO]) === false) {
            $queryBuilder->andWhere("aula.data_aula >= :dataInicio");
            $queryBuilder->setParameter("dataInicio", $parametros[ConstanteParametros::CHAVE_DATA_INICIO]);
        }

        if (isset($parametros[ConstanteParametros::CHAVE_DATA_FIM]) === true && empty($parametros[ConstanteParametros::CHAVE_DATA_FIM]) === false) {
            $queryBuilder->andWhere("aula.data_aula <= :dataFim");
            $queryBuilder->setParameter("dataFim", $parametros[ConstanteParametros::CHAVE_DATA_FIM]);
        }

        $queryBuilder->setParameter("funcionario", $parametros[ConstanteParametros::CHAVE_FUNCIONARIO]);

        return \App\Helper\FunctionHelper::retornaResultados($queryBuilder, true);
    }

    /**
     * Busca informações dos alunos de uma turma
     *
     * @param string $mensagemErro
     * @param int $turmaId
     *
     * @return array
     */
    public function buscarDadosAlunosTurma(&$mensagemErro, $turmaId)
    {
        $queryBuilder = $this->createQueryBuilder("t");
        $queryBuilder->select(
            [
                'p.nome_contato as nome',
                "COALESCE(p.telefone_preferencial, p.telefone_contato, p.telefone_profissional, '') as fone",
                "p.data_nascimento as data_nascimento",
                "CASE WHEN a.situacao = 'ATI' then 'Ativo' WHEN a.situacao = 'INA' then 'Inativo' WHEN a.situacao = 'TRA' then 'Trancado' WHEN a.situacao = 'INT' then 'Interessado' ELSE '' END situacao",
            ]
        );
        $queryBuilder->join('t.contratos', 'c');
        $queryBuilder->join('c.aluno', 'a');
        $queryBuilder->join('a.pessoa', 'p');
        $queryBuilder->where('t.id = :turmaId');
        $queryBuilder->setParameter('turmaId', $turmaId);
        $queryBuilder->andWhere("c.situacao in ('V','E')");

        return $queryBuilder->getQuery()->getResult(\Doctrine\ORM\Query::HYDRATE_ARRAY);
    }

    /**
     * Retorna o array com os resultado do relatório das turma, identificando dentro de cada turma os alunos que a pertence
     *
     * @param array $parametros
     *
     * @return array
     */
    public function buscarDadosRelatorioTurmasExistentes($parametros)
    {
        $andWhere = "";
        
        if (key_exists(ConstanteParametros::CHAVE_INSTRUTOR_FLAG, $parametros) && !is_null($parametros[ConstanteParametros::CHAVE_INSTRUTOR_FLAG])) {
            $andWhere .= " AND t0_.funcionario_id = ".$parametros[ConstanteParametros::CHAVE_INSTRUTOR_FLAG]."";
        }
        if (key_exists(ConstanteParametros::CHAVE_CURSO, $parametros) && !is_null($parametros[ConstanteParametros::CHAVE_CURSO])) {
            $andWhere .= " AND t0_.curso_id = ".$parametros[ConstanteParametros::CHAVE_CURSO]."";
        }
        if (key_exists(ConstanteParametros::CHAVE_TURMA, $parametros) && !is_null($parametros[ConstanteParametros::CHAVE_TURMA])) {
            $andWhere .= " AND t0_.id = ".$parametros[ConstanteParametros::CHAVE_TURMA]."";
        }
        if (key_exists(ConstanteParametros::CHAVE_LIVRO, $parametros) && !is_null($parametros[ConstanteParametros::CHAVE_LIVRO])) {
            $andWhere .= " AND t0_.livro_id = ".$parametros[ConstanteParametros::CHAVE_LIVRO]."";
        }
        if (key_exists(ConstanteParametros::CHAVE_SITUACAO_TURMA, $parametros) && !is_null($parametros[ConstanteParametros::CHAVE_SITUACAO_TURMA])) {
            $situacao = implode("','", $parametros[ConstanteParametros::CHAVE_SITUACAO_TURMA]);
            $andWhere .= " AND t0_.situacao IN ('".$situacao."')";
        }
        
        if (key_exists(ConstanteParametros::CHAVE_MODALIDADE_TURMA, $parametros) && !is_null($parametros[ConstanteParametros::CHAVE_MODALIDADE_TURMA])) {
            $modalidade = implode("','", $parametros[ConstanteParametros::CHAVE_MODALIDADE_TURMA]);
            $andWhere .= " AND t0_.modalidade_turma_id IN ('".$modalidade."')";            
        }
        if (isset($parametros[ConstanteParametros::CHAVE_DIA_SEMANA])) {
            $diasSemana = implode("','", $parametros[ConstanteParametros::CHAVE_DIA_SEMANA]);
            $andWhere .= " AND h5_.dia_semana IN ('".$diasSemana."')";
            
        }
        if (isset($parametros[ConstanteParametros::CHAVE_SITUACAO_TURMA]) && count($parametros[ConstanteParametros::CHAVE_SITUACAO_TURMA]) > 0) {
            $situacoes = implode("', '", $parametros[ConstanteParametros::CHAVE_SITUACAO_TURMA]);
            $andWhere .= " AND t0_.situacao IN ('".$situacoes."')";
        }
        if (isset($parametros[ConstanteParametros::CHAVE_TURNOS])) {
 
            $subCondicionais = "";
 
            if (in_array('M', $parametros[ConstanteParametros::CHAVE_TURNOS]) === true) {
                $subCondicionais .= "(h5_.horario_inicio >= '03:00:00' AND h5_.horario_inicio < '15:00:00')";
            }
            
            if (in_array('T', $parametros[ConstanteParametros::CHAVE_TURNOS]) === true) {
                $subCondicionais .= "(h5_.horario_inicio >= '15:00:00' AND h5_.horario_inicio < '21:00:00' )";
            }
            
            if (in_array('N', $parametros[ConstanteParametros::CHAVE_TURNOS]) === true) {
                $subCondicionais .= "(h5_.horario_inicio >= '21:00:00' OR h5_.horario_inicio < '03:00:00' )";
            }
            
            $subCondicionais = str_replace(')(', ') OR (', $subCondicionais);
            $andWhere .= " AND (".$subCondicionais.")";            
        }
        
       $sql =   " 
                    SELECT t0_.descricao AS turma,
                        p1_.nome_contato AS instrutor,
                        c6_.descricao AS curso,
                        h2_.descricao AS horario,
                        l3_.descricao AS livro,
                        s4_.descricao AS sala,
                        h5_.dia_semana AS dia_semana,
                        DATE_FORMAT(h5_.horario_inicio, '%H:%i') AS horario_inicio,
                        DATE_FORMAT(t0_.data_inicio, '%d-%m-%Y') AS data_inicio,
                        DATE_FORMAT(t0_.data_fim, '%d-%m-%Y') AS data_fim,
                        (
                            SELECT 
                                cast(CONCAT('[', GROUP_CONCAT(DISTINCT JSON_OBJECT('id', a.id, 'nome', p.nome_contato, 'telefone', p.telefone_preferencial ,'situacao', 
                                a.situacao)), ']') AS JSON)  
                            FROM 
                                aluno a 
                            LEFT JOIN 
                                pessoa as p 
                            ON
                                p.id = a.pessoa_id
                            INNER JOIN 
                                contrato as c
                            ON 
                                c.aluno_id = a.id
                            WHERE 
                                c.turma_id = t0_.id and c.situacao = 'V' and a.situacao = 'ATI'
                        ) AS alunos
                        FROM turma t0_
                        INNER JOIN curso c6_ ON t0_.curso_id = c6_.id
                        INNER JOIN idioma i7_ ON c6_.idioma_id = i7_.id
                        INNER JOIN livro l3_ ON t0_.livro_id = l3_.id
                        LEFT JOIN horario h2_ ON t0_.horario_id = h2_.id
                        LEFT JOIN contrato c8_ ON t0_.id = c8_.turma_id
                        LEFT JOIN horario_aula h5_ ON h2_.id = h5_.horario_id
                        LEFT JOIN funcionario f9_ ON t0_.funcionario_id = f9_.id
                        LEFT JOIN pessoa p1_ ON f9_.pessoa_id = p1_.id
                        LEFT JOIN sala_franqueada s10_ ON t0_.sala_franqueada_id = s10_.id
                        LEFT JOIN sala s4_ ON s10_.sala_id = s4_.id
                        WHERE t0_.excluido = 0 
                        AND t0_.franqueada_id = ".VariaveisCompartilhadas::$franqueadaID." 
                         ".$andWhere." GROUP BY t0_.id
                ";
                        
        $data = $this->registry->getConnection()->fetchAllAssociative($sql);            
       
        $contTurmas = count($data);
        $contAlunos = 0;
        
        for($i=0; $i < $contTurmas; $i++){

            // Clonando principais dados de turmas
            $result['turmas'][$i] = $data[$i];

            // Remover as barras invertidas escapadas
            $stringLimpa = stripslashes($data[$i]['alunos']);
            
            // Converter a string em um array
            $result['turmas'][$i]['alunos'] = json_decode($stringLimpa, true);
            $result['turmas'][$i]['quantidade_alunos'] = ($result['turmas'][$i]['alunos']) ? count($result['turmas'][$i]['alunos']) : 0;
            
        }
        
        // Percorre as turmas e armazena a soma dos alunos presente nas turmas
        foreach($result['turmas'] as $turma){
            $contAlunos = $contAlunos + ($turma['quantidade_alunos']);
        }
        
        $result['media_aluno_por_turma'] = $contAlunos/$contTurmas;
        $result['total_alunos'] = $contAlunos;
        $result['total_turmas'] = $contTurmas;
       
        return $result;
    }

    public function buscarDadosRelatorioAlunosPorTurma($parametros)
    {
        $queryBuilder = $this->createQueryBuilder("turma")
            ->select([
                'turma.descricao as nome_turma',
                'turma.situacao',
                'turma.maximo_alunos',
                'liv.descricao as livro',
                'prof.nome_contato as professor',
                'sal.descricao as sala',
                'hor.descricao as horario',
                'COUNT(cont.id) as alunos'
            ])
            ->leftJoin('turma.livro', 'liv')
            ->leftJoin('turma.horario', 'hor')
            ->leftJoin('turma.sala_franqueada', 'sala_fran')
            ->leftJoin('turma.funcionario', 'func')
            ->leftJoin('func.pessoa', 'prof')
            ->leftJoin('turma.contratos', 'cont')
            ->leftJoin('sala_fran.sala', 'sal')
            ->groupBy('turma.id')
            ->andWhere('turma.franqueada = :franqueada')
            ->setParameter(':franqueada', $parametros[ConstanteParametros::CHAVE_FRANQUEADA])
            ->andWhere('turma.excluido = 0')
            ->andWhere("cont.situacao NOT IN ('C')")
            ->andWhere('turma.situacao IN (:situacao)')
            ->setParameter('situacao', ['ABE','FOR', 'ENC']);
        
        if (key_exists(ConstanteParametros::CHAVE_LIVRO, $parametros) && is_null($parametros[ConstanteParametros::CHAVE_LIVRO]) === false) {
            $queryBuilder->andWhere('liv = :livro');
            $queryBuilder->setParameter('livro', $parametros[ConstanteParametros::CHAVE_LIVRO]);
        }
        if (key_exists(ConstanteParametros::CHAVE_TURMA, $parametros) && is_null($parametros[ConstanteParametros::CHAVE_TURMA]) === false) {
            $queryBuilder->andWhere('turma = :turma');
            $queryBuilder->setParameter('turma', $parametros[ConstanteParametros::CHAVE_TURMA]);
        }
        if (key_exists(ConstanteParametros::CHAVE_HORARIO, $parametros) && is_null($parametros[ConstanteParametros::CHAVE_HORARIO]) === false) {
            $queryBuilder->andWhere('hor = :horario');
            $queryBuilder->setParameter('horario', $parametros[ConstanteParametros::CHAVE_HORARIO]);
        }
        if (key_exists(ConstanteParametros::CHAVE_SALA, $parametros) && is_null($parametros[ConstanteParametros::CHAVE_SALA]) === false) {
            $queryBuilder->andWhere('sal = :sala');
            $queryBuilder->setParameter('sala', $parametros[ConstanteParametros::CHAVE_SALA]);
        }
        if (key_exists(ConstanteParametros::CHAVE_INSTRUTOR_FLAG, $parametros) && is_null($parametros[ConstanteParametros::CHAVE_INSTRUTOR_FLAG]) === false) {
            $queryBuilder->andWhere('func = :instrutor');
            $queryBuilder->setParameter('instrutor', $parametros[ConstanteParametros::CHAVE_INSTRUTOR_FLAG]);
        }

        return $queryBuilder->getQuery()->getResult(); 
    }

    public function buscarDadosRelatorioMapaSalaTurma($parametros)
    {
        $queryBuilder = $this->createQueryBuilder('tur')
            ->select([
                'tur.descricao as turma',
                "date_format(tur.data_inicio, '%Y-%m-%d') as data_inicio",
                "date_format(tur.data_fim, '%Y-%m-%d') as data_termino",
                'hor_aula.dia_semana',
                "date_format(hor_aula.horario_inicio, '%H:%i') as horario_inicio",
                'cur.descricao as curso',
                'sal.descricao as sala',
                'sal.id as sala_id',
                'sal_fran.lotacao_maxima',
                'sal_fran.personal',
                'sal_fran.situacao'
            ])
            ->join('tur.sala_franqueada', 'sal_fran')
            ->join('sal_fran.sala', 'sal')
            ->join('tur.horario', 'hor')
            ->join('hor.horarioAulas', 'hor_aula')
            ->join('tur.curso', 'cur')
            ->where('tur.franqueada = :franqueada')
            ->andWhere('sal_fran.franqueada = :franqueada')
            ->setParameter('franqueada', $parametros[ConstanteParametros::CHAVE_FRANQUEADA])
            ->andWhere('tur.excluido = 0')
            ->groupBy('tur, hor_aula.dia_semana');

        if(isset($parametros[ConstanteParametros::CHAVE_TURMA])) {
            $queryBuilder->andWhere('tur = :turma')
                ->setParameter('turma', $parametros[ConstanteParametros::CHAVE_TURMA]);
        }
        if(isset($parametros[ConstanteParametros::CHAVE_SALA])) {
            $queryBuilder->andWhere('sal = :sala')
                ->setParameter('sala', $parametros[ConstanteParametros::CHAVE_SALA]);
        }
        if(isset($parametros[ConstanteParametros::CHAVE_DATA_INICIAL]) && !isset($parametros[ConstanteParametros::CHAVE_DATA_FINAL])) {
            $dataInicial = strtotime(str_replace("/", "-",$parametros[ConstanteParametros::CHAVE_DATA_INICIAL] . " 00:00:00"));
            $dataInicial = date('Y-m-d H:i:s', $dataInicial);
            $queryBuilder->andWhere('tur.data_fim >= :data_inicial')
                ->setParameter('data_inicial', $dataInicial);
        }
        if(isset($parametros[ConstanteParametros::CHAVE_DATA_FINAL]) && !isset($parametros[ConstanteParametros::CHAVE_DATA_INICIAL])) {
            $dataFinal = strtotime(str_replace("/", "-",$parametros[ConstanteParametros::CHAVE_DATA_FINAL] . " 23:59:59"));
            $dataFinal = date('Y-m-d H:i:s', $dataFinal);
            $queryBuilder->andWhere('tur.data_inicio <= :data_final')
                ->setParameter('data_final', $dataFinal);
        }
        if(isset($parametros[ConstanteParametros::CHAVE_DATA_INICIAL]) && isset($parametros[ConstanteParametros::CHAVE_DATA_FINAL])) {
            $dataInicial = strtotime(str_replace("/", "-",$parametros[ConstanteParametros::CHAVE_DATA_INICIAL] . " 00:00:00"));
            $dataInicial = date('Y-m-d H:i:s', $dataInicial);
            $dataFinal = strtotime(str_replace("/", "-",$parametros[ConstanteParametros::CHAVE_DATA_FINAL] . " 23:59:59"));
            $dataFinal = date('Y-m-d H:i:s', $dataFinal);
            $queryBuilder->andWhere('tur.data_inicio <= :data_final')
                ->andWhere('tur.data_fim >= :data_inicial')
                ->setParameter('data_inicial', $dataInicial)
                ->setParameter('data_final', $dataFinal);
        }

        return $queryBuilder->getQuery()->getResult();

    }
}
