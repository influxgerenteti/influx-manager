<?php

namespace App\Repository\Principal;

use App\Entity\Principal\Contrato;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Symfony\Bridge\Doctrine\RegistryInterface;
use App\Helper\ConstanteParametros;
use App\Helper\VariaveisCompartilhadas;
use App\Helper\SituacoesSistema;
use Doctrine\Persistence\ManagerRegistry;
use PhpParser\Node\Stmt\Const_;

/**
 * @method Contrato|null find($id, $lockMode = null, $lockVersion = null)
 * @method Contrato|null findOneBy(array $criteria, array $orderBy = null)
 * @method Contrato[]    findAll()
 * @method Contrato[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class ContratoRepository extends ServiceEntityRepository
{
    /**
    * @var ManagerRegistry
     */
    private ManagerRegistry $managerRegistry;

    public function __construct(RegistryInterface $registry, ManagerRegistry $managerRegistry)
    {
        $this->managerRegistry = $managerRegistry;
        parent::__construct($registry, Contrato::class);
    }

    /**
     * Monta query padrao
     *
     * @return \Doctrine\ORM\QueryBuilder
     */
    protected function montaQueryBase()
    {
        $queryBuilder = $this->createQueryBuilder("ct");
        $queryBuilder->addSelect("al");
        $queryBuilder->addSelect("tm");
        $queryBuilder->addSelect("lv");
        $queryBuilder->addSelect("rvf");
        $queryBuilder->addSelect("rcf");
        $queryBuilder->addSelect("rfp");
        $queryBuilder->addSelect("pess");
        $queryBuilder->addSelect("cur");
        $queryBuilder->addSelect("hor");
        $queryBuilder->addSelect("sem");
        $queryBuilder->addSelect("cont");
        $queryBuilder->addSelect("cr");
        $queryBuilder->addSelect("cri");
        $queryBuilder->addSelect("itens");
        $queryBuilder->addSelect("planoConta");
        $queryBuilder->addSelect("titulos");
        $queryBuilder->addSelect("tituloFormaCobranca");
        $queryBuilder->addSelect("tituloFormaRecebimento");
        $queryBuilder->addSelect("tituloConta");
        $queryBuilder->addSelect("tituloTransacoesCartao");
        $queryBuilder->addSelect("tituloTransferenciasBancarias");
        $queryBuilder->addSelect("tituloBoletos");
        $queryBuilder->addSelect("tituloCheques");
        $queryBuilder->addSelect("contratoCurso");
        $queryBuilder->addSelect("livv");
        $queryBuilder->addSelect("contratoCursoIdioma");
        $queryBuilder->addSelect("operadoraCartao");
        $queryBuilder->addSelect("parcelamentoOperadoraCartao");
        $queryBuilder->addSelect("mdtm");
        $queryBuilder->addSelect("agp");
        $queryBuilder->addSelect("slf");
        $queryBuilder->addSelect("func");
        $queryBuilder->addSelect("cdp");
        $queryBuilder->addSelect("movimentoDollar");
        $queryBuilder->addSelect("atvDollar");

        $queryBuilder->leftJoin("ct.aluno", "al");
        $queryBuilder->leftJoin("ct.turma", "tm");
        $queryBuilder->leftJoin("ct.livro", "lv");
        $queryBuilder->leftJoin("ct.curso", "cur");
        $queryBuilder->leftJoin("ct.modalidade_turma", "mdtm");
        $queryBuilder->leftJoin("al.contratos", "cont");
        $queryBuilder->leftJoin("ct.responsavel_venda_funcionario", "rvf");
        $queryBuilder->leftJoin("ct.responsavel_carteira_funcionario", "rcf");
        $queryBuilder->leftJoin("ct.responsavel_financeiro_pessoa", "rfp");
        $queryBuilder->leftJoin("ct.agendamentoPersonals", "agp");
        $queryBuilder->leftJoin("agp.sala_franqueada", "slf");
        $queryBuilder->leftJoin("agp.funcionario", "func");
        $queryBuilder->leftJoin("ct.creditosPersonal", "cdp");
        $queryBuilder->leftJoin("al.pessoa", "pess");
        $queryBuilder->leftJoin("tm.horario", "hor");
        $queryBuilder->leftJoin("ct.semestre", "sem");
        $queryBuilder->leftJoin("ct.contratoContaReceber", "cr");
        $queryBuilder->leftJoin("cr.itemsContaReceber", "cri");
        $queryBuilder->leftJoin("cri.item", "itens");
        $queryBuilder->leftJoin("cri.plano_conta", "planoConta");
        $queryBuilder->leftJoin("cr.tituloRecebers", "titulos");
        $queryBuilder->leftJoin("ct.movimentoDollars", "movimentoDollar", "WITH", "al.id = movimentoDollar.aluno");
        $queryBuilder->leftJoin("movimentoDollar.atividade_dollar", "atvDollar");
        $queryBuilder->leftJoin("titulos.forma_cobranca", "tituloFormaCobranca");
        $queryBuilder->leftJoin("titulos.forma_recebimento", "tituloFormaRecebimento");
        $queryBuilder->leftJoin("titulos.conta", "tituloConta");
        $queryBuilder->leftJoin("titulos.boletos", "tituloBoletos");
        $queryBuilder->leftJoin("titulos.transacoes_cartao", "tituloTransacoesCartao");
        $queryBuilder->leftJoin("titulos.transferencias_bancarias", "tituloTransferenciasBancarias");
        $queryBuilder->leftJoin("titulos.cheques", "tituloCheques");
        $queryBuilder->leftJoin("tituloTransacoesCartao.operadora_cartao", "operadoraCartao");
        $queryBuilder->leftJoin("tituloTransacoesCartao.parcelamento_operadora_cartao", "parcelamentoOperadoraCartao");
        $queryBuilder->leftJoin("cont.curso", "contratoCurso");
        $queryBuilder->leftJoin("cont.livro", "livv");
        $queryBuilder->leftJoin("contratoCurso.idioma", "contratoCursoIdioma");


        return $queryBuilder;
    }

     /**
     * Monta query padrao sem Financeiro
     *
     * @return \Doctrine\ORM\QueryBuilder
     */
    protected function montaQueryBaseReduzido()
    {
    //    $queryBuilder = $this->getEntityManager()->createQueryBuilder();
    //  $queryBuilder->select('partial ct.{id,codigo_matricula_importado,sequencia_contrato ,situacao ,tipo_contrato ,data_contrato ,
    //                         data_matricula ,data_inicio_contrato ,data_termino_contrato,observacao ,excluido ,bolsista ,familiar_desconto ,
    //                         motivo_cancelamento ,sponte_id}')
    //                         ->from($this->_entityName, 'ct');

    $queryBuilder = $this->createQueryBuilder("ct");
                    $queryBuilder->addSelect("al");
                    $queryBuilder->addSelect("tm");
                    $queryBuilder->addSelect("lv");
                    $queryBuilder->addSelect("rvf");
                    $queryBuilder->addSelect("rcf");
                    $queryBuilder->addSelect("rfp");
                    $queryBuilder->addSelect("pess");
                    $queryBuilder->addSelect("cur");
                    $queryBuilder->addSelect("hor");
                    $queryBuilder->addSelect("sem");
                    $queryBuilder->addSelect("cont");
                    
            
                    $queryBuilder->leftJoin("ct.aluno", "al");
                    $queryBuilder->leftJoin("ct.turma", "tm");
                    $queryBuilder->leftJoin("ct.livro", "lv");
                    $queryBuilder->leftJoin("ct.curso", "cur");
                    $queryBuilder->leftJoin("ct.modalidade_turma", "mdtm");
                    $queryBuilder->leftJoin("al.contratos", "cont");
                    $queryBuilder->leftJoin("ct.responsavel_venda_funcionario", "rvf");
                    $queryBuilder->leftJoin("ct.responsavel_carteira_funcionario", "rcf");
                    $queryBuilder->leftJoin("ct.responsavel_financeiro_pessoa", "rfp");
                    $queryBuilder->leftJoin("ct.agendamentoPersonals", "agp");
                    $queryBuilder->leftJoin("agp.sala_franqueada", "slf");
                    $queryBuilder->leftJoin("agp.funcionario", "func");
                    $queryBuilder->leftJoin("ct.creditosPersonal", "cdp");
                    $queryBuilder->leftJoin("al.pessoa", "pess");
                    $queryBuilder->leftJoin("tm.horario", "hor");
                    $queryBuilder->leftJoin("ct.semestre", "sem");
                    

         return $queryBuilder;

    }

    /**
     * Query para realizar fitlro de franqueada
     *
     * @param \Doctrine\ORM\QueryBuilder $queryBuilder
     */
    private function filtrarFranqueada(&$queryBuilder)
    {
        $queryBuilder->where('ct.franqueada = :franqueada');
        $queryBuilder->setParameter('franqueada', VariaveisCompartilhadas::$franqueadaID);
    }

    /**
     * Monta filtros de Relacionamentos
     *
     * @param \Doctrine\ORM\QueryBuilder $queryBuilder
     * @param array $parametros
     */
    protected function montaFiltrosDeRelacoes(&$queryBuilder, $parametros)
    {
        if ((isset($parametros[ConstanteParametros::CHAVE_ALUNO]) === true)&&(empty($parametros[ConstanteParametros::CHAVE_ALUNO]) === false)) {
            $queryBuilder->andWhere("ct.aluno = :alunoId");
            $queryBuilder->setParameter("alunoId", $parametros[ConstanteParametros::CHAVE_ALUNO]);
        }

        if ((isset($parametros[ConstanteParametros::CHAVE_TURMA]) === true)&&(empty($parametros[ConstanteParametros::CHAVE_TURMA]) === false)) {
            $queryBuilder->andWhere("ct.turma = :turmaId");
            $queryBuilder->setParameter("turmaId", $parametros[ConstanteParametros::CHAVE_TURMA]);
        }

        if ((isset($parametros[ConstanteParametros::CHAVE_LIVRO]) === true)&&(empty($parametros[ConstanteParametros::CHAVE_LIVRO]) === false)) {
            $queryBuilder->andWhere("lv.id = :livroId");
            $queryBuilder->setParameter("livroId", $parametros[ConstanteParametros::CHAVE_LIVRO]);
        }

        if ((isset($parametros[ConstanteParametros::CHAVE_RESPONSAVEL_VENDA_FUNCIONARIO]) === true)&&(empty($parametros[ConstanteParametros::CHAVE_RESPONSAVEL_VENDA_FUNCIONARIO]) === false)) {
            $queryBuilder->andWhere("ct.responsavel_venda_funcionario = :respoVendaFuncionario");
            $queryBuilder->setParameter("respoVendaFuncionario", $parametros[ConstanteParametros::CHAVE_RESPONSAVEL_VENDA_FUNCIONARIO]);
        }

        if ((isset($parametros[ConstanteParametros::CHAVE_RESPONSAVEL_CARTEIRA_FUNCIONARIO]) === true)&&(empty($parametros[ConstanteParametros::CHAVE_RESPONSAVEL_CARTEIRA_FUNCIONARIO]) === false)) {
            $queryBuilder->andWhere("ct.responsavel_carteira_funcionario = :respoCarteiraFuncionario");
            $queryBuilder->setParameter("respoCarteiraFuncionario", $parametros[ConstanteParametros::CHAVE_RESPONSAVEL_CARTEIRA_FUNCIONARIO]);
        }
    }

    /**
     * Monta filtros de datas
     *
     * @param \Doctrine\ORM\QueryBuilder $queryBuilder
     * @param array $parametros
     */
    protected function montaFiltroDatas(&$queryBuilder, $parametros)
    {
        if ((isset($parametros[ConstanteParametros::CHAVE_DATA_INICIO_CONTRATO_INICIO]) === true)&&(empty($parametros[ConstanteParametros::CHAVE_DATA_INICIO_CONTRATO_INICIO]) === false)) {
            $queryBuilder->andWhere("ct.data_inicio_contrato >= :dataInicioContratoInicio");
            $queryBuilder->setParameter("dataInicioContratoInicio", $parametros[ConstanteParametros::CHAVE_DATA_INICIO_CONTRATO_INICIO]);
        }

        if ((isset($parametros[ConstanteParametros::CHAVE_DATA_INICIO_CONTRATO_FIM]) === true)&&(empty($parametros[ConstanteParametros::CHAVE_DATA_INICIO_CONTRATO_FIM]) === false)) {
            $queryBuilder->andWhere("ct.data_inicio_contrato <= :dataInicioContratoFim");
            $queryBuilder->setParameter("dataInicioContratoFim", $parametros[ConstanteParametros::CHAVE_DATA_INICIO_CONTRATO_FIM]);
        }

        if ((isset($parametros[ConstanteParametros::CHAVE_DATA_TERMINO_CONTRATO_INICIO]) === true)&&(empty($parametros[ConstanteParametros::CHAVE_DATA_TERMINO_CONTRATO_INICIO]) === false)) {
            $queryBuilder->andWhere("ct.data_termino_contrato >= :dataTerminoContratoInicio");
            $queryBuilder->setParameter("dataTerminoContratoInicio", $parametros[ConstanteParametros::CHAVE_DATA_TERMINO_CONTRATO_INICIO]);
        }

        if ((isset($parametros[ConstanteParametros::CHAVE_DATA_TERMINO_CONTRATO_FIM]) === true)&&(empty($parametros[ConstanteParametros::CHAVE_DATA_TERMINO_CONTRATO_FIM]) === false)) {
            $queryBuilder->andWhere("ct.data_termino_contrato <= :dataTerminoContratoFim");
            $queryBuilder->setParameter("dataTerminoContratoFim", $parametros[ConstanteParametros::CHAVE_DATA_TERMINO_CONTRATO_FIM]);
        }
    }


    /**
     * Monta os Filtros de busca
     *
     * @param \Doctrine\ORM\QueryBuilder $queryBuilder
     * @param array $parametros
     */
    protected function montaFiltros(&$queryBuilder, $parametros)
    {
        $queryBuilder->where("ct.franqueada = :franqueadaId");
        $params = ["franqueadaId" => VariaveisCompartilhadas::$franqueadaID];

        if ((isset($parametros[ConstanteParametros::CHAVE_NUMERO_CONTRATO]) === true)&&(empty($parametros[ConstanteParametros::CHAVE_NUMERO_CONTRATO]) === false)) {
            $ctData     = explode("/", $parametros[ConstanteParametros::CHAVE_NUMERO_CONTRATO]);
            $alunoId    = $ctData[0];
            $sequencial = $ctData[1];
            $queryBuilder->andWhere(
                $queryBuilder->expr()->andX(
                    $queryBuilder->expr()->eq("ct.aluno", ":alunoId"),
                    $queryBuilder->expr()->eq("ct.sequencia_contrato", ":seqContrato")
                )
            );

            $params["alunoId"]     = $alunoId;
            $params["seqContrato"] = $sequencial;
        }

        if ((isset($parametros[ConstanteParametros::CHAVE_SITUACAO]) === true)&&(empty($parametros[ConstanteParametros::CHAVE_SITUACAO]) === false)) {
            $queryBuilder->andWhere("ct.situacao IN (:situacao)");
            $params["situacao"] = $parametros[ConstanteParametros::CHAVE_SITUACAO];
        }

        $queryBuilder->setParameters($params);

        $this->montaFiltrosDeRelacoes($queryBuilder, $parametros);
        $this->montaFiltroDatas($queryBuilder, $parametros);
    }

    /**
     * Monta Funcao de paginacao do contrato
     *
     * @param array $parametros
     * @param number $pagina
     * @param number $numeroItensPorPagina
     *
     * @return \Knp\Component\Pager\Pagination\SlidingPagination
     */
    public function filtrarContratoPorPagina($parametros, $pagina=1, $numeroItensPorPagina=50)
    {
        $opcoes       = [];
        $queryBuilder = $this->montaQueryBase();
        $this->montaFiltros($queryBuilder, $parametros);
        if ((isset($parametros[ConstanteParametros::CHAVE_ORDENACAO_COLUNA]) === true)&&(is_null($parametros[ConstanteParametros::CHAVE_ORDENACAO_COLUNA]) === false)) {
            $queryBuilder->orderBy($parametros[ConstanteParametros::CHAVE_ORDENACAO_COLUNA], $parametros[ConstanteParametros::CHAVE_ORDENACAO_SORT]);
            $opcoes[\Knp\Component\Pager\Paginator::SORT_FIELD_PARAMETER_NAME]     = "~";
            $opcoes[\Knp\Component\Pager\Paginator::SORT_DIRECTION_PARAMETER_NAME] = "~";
        }
       
        return \App\Helper\FunctionHelper::montaPaginatorPaginacao($queryBuilder, $pagina, $numeroItensPorPagina, $opcoes);
    }


     /**
     * Monta Funcao de paginacao do contratos Novo
     *
     * @param array $parametros
     * @param number $pagina
     * @param number $numeroItensPorPagina
     *
     * @return \Knp\Component\Pager\Pagination\SlidingPagination
     */
    public function filtrarContratosPorPagina($parametros, $pagina=1, $numeroItensPorPagina=50)
    {
        $opcoes       = [];
        $queryBuilder = $this->montaQueryBaseReduzido();
        $this->montaFiltros($queryBuilder, $parametros);
        if ((isset($parametros[ConstanteParametros::CHAVE_ORDENACAO_COLUNA]) === true)&&(is_null($parametros[ConstanteParametros::CHAVE_ORDENACAO_COLUNA]) === false)) {
            $queryBuilder->orderBy($parametros[ConstanteParametros::CHAVE_ORDENACAO_COLUNA], $parametros[ConstanteParametros::CHAVE_ORDENACAO_SORT]);
            $opcoes[\Knp\Component\Pager\Paginator::SORT_FIELD_PARAMETER_NAME]     = "~";
            $opcoes[\Knp\Component\Pager\Paginator::SORT_DIRECTION_PARAMETER_NAME] = "~";
        }

        $data = \App\Helper\FunctionHelper::montaPaginatorPaginacao($queryBuilder, $pagina, $numeroItensPorPagina, $opcoes); 

        return $data;
    }

     /**
     * Retorna o registro atraves da ID
     *
     * @param int $id
     *
     * @return array|NULL
     */
    public function buscarPorId($id)
    {
        $queryBuilder = $this->montaQueryBase();
        $this->filtrarFranqueada($queryBuilder);
        $queryBuilder->andWhere("ct.id = :ctId");
        // $queryBuilder->where("ct.id = :ctId");
        $queryBuilder->setParameter("ctId", $id);
        $queryBuilder->orderBy("titulos.numero_parcela_documento");
        $queryBuilder->orderBy('cri.id', 'ASC');

        return $queryBuilder->getQuery()->getOneOrNullResult(\Doctrine\ORM\Query::HYDRATE_ARRAY);
    }

    /**
     * Retorna o ultimo sequencial gerado para o aluno
     *
     * @param int $alunoId
     *
     * @return number
     */
    public function buscarUltimoSequencial($alunoId)
    {
        $queryBuilder = $this->montaQueryBase();
        $queryBuilder->select($queryBuilder->expr()->countDistinct("ct.id"));
        $queryBuilder->where("al.id = :alunoId");
        $queryBuilder->setParameter("alunoId", $alunoId);
        $count = $queryBuilder->getQuery()->getSingleScalarResult();
        return ($count + 1);
    }

    /**
     * Busca os dados para realização da geração da mala direta
     *
     * @param array $parametros
     *
     * @return array|NULL
     */
    public function buscarDadosMalaDireta($parametros)
    {
        //var_dump($parametros);
       // die;
        $queryBuilder = $this->createQueryBuilder("ct");
        $queryBuilder->select(
            [
                'alPessoa.nome_contato as aluno',
                'alPessoa.email_preferencial as email_preferencial',
                'alPessoa.telefone_preferencial as telefone_preferencial',
                'rf.nome_contato as responsavel_financeiro',
                'cur.descricao as curso',
                'lv.descricao as livro'
            ]
        );

        $queryBuilder->leftJoin("ct.turma", "tm");
        $queryBuilder->leftJoin("ct.aluno", "al");
        $queryBuilder->leftJoin("ct.curso", "cur");
        $queryBuilder->leftJoin("ct.livro", "lv");
        $queryBuilder->leftJoin("al.responsavel_financeiro_pessoa", "rf");
        $queryBuilder->leftJoin("al.pessoa", "alPessoa");

        $this->filtrarFranqueada($queryBuilder);
        $queryBuilder->andWhere("ct.situacao IN ('V', 'E')");
        $this->montaFiltrosDeRelacoes($queryBuilder, $parametros);
        $this->montaFiltroDatas($queryBuilder, $parametros);

        if ((int) $parametros[ConstanteParametros::CHAVE_TIPO_RESPONSAVEL] === 1) {
            $queryBuilder->andWhere("alPessoa.nome_contato != rf.nome_contato");
            $queryBuilder->andWhere("rf.id IS NOT NULL");
        }
        if ((int) $parametros[ConstanteParametros::CHAVE_TIPO_RESPONSAVEL] === 2) {
            $queryBuilder->andWhere("alPessoa.nome_contato = rf.nome_contato");
            $queryBuilder->orWhere("rf.id IS NULL");
        }

        return $queryBuilder->getQuery()->getResult();
    }

    /**
     * Monta a query para ser executada no relatório
     *
     * @param array $parametros
     * @param string $origem = 1: Quantidade de alunos por turma, 2: Matriculas (Vendas) por periodo
     *
     * @return string
     */
    public function prepararDadosRelatorio($parametros, $origem)
    {
        if ($origem === 1) {
            $queryBuilder = $this->createQueryBuilder("c");
            $queryBuilder->select('c.id');
            $queryBuilder->innerjoin('c.turma', 't');
            $queryBuilder->innerjoin('t.livro', 'l');
            $queryBuilder->innerjoin('t.horario', 'h');
            $queryBuilder->leftJoin('t.sala_franqueada', 'sf');
            $queryBuilder->leftJoin('sf.sala', 's');
            $queryBuilder->leftJoin('t.funcionario', 'f');
            $queryBuilder->leftJoin('f.pessoa', 'p');

            $queryBuilder->where('c.excluido = 0');
            $queryBuilder->andwhere('t.excluido = 0');
            $queryBuilder->andwhere('c.franqueada = :franqueada');
            $queryBuilder->andwhere('t.franqueada = :franqueadaa');
            $queryBuilder->andwhere('h.franqueada = :franqueadaaa');
            $queryBuilder->setParameter('franqueada', VariaveisCompartilhadas::$franqueadaID);
            $queryBuilder->setParameter('franqueadaa', VariaveisCompartilhadas::$franqueadaID);
            $queryBuilder->setParameter('franqueadaaa', VariaveisCompartilhadas::$franqueadaID);

            // Filtra e substitui a query para passar ao Jasper
            $sql = $queryBuilder->getQuery()->getSQL();
            $sql = preg_replace('/.*WHERE\s(.*)$/', '$1', $sql);

            // Seleciona somente os wheres
            $sql = preg_replace('/c0_/', 'contrato', $sql);
            $sql = preg_replace('/t1_/', 'turma', $sql);
            $sql = preg_replace('/l2_/', 'livro', $sql);
            $sql = preg_replace('/h3_/', 'horario', $sql);
            $sql = preg_replace('/s4_/', 'sala_franqueada', $sql);
            $sql = preg_replace('/s5_/', 'sala', $sql);
            $sql = preg_replace('/f6_/', 'funcionario', $sql);
            $sql = preg_replace('/p7_/', 'pessoa', $sql);

            // Substituição de parâmetros
            $parameters = $queryBuilder->getParameters();
            foreach ($parameters as $parameter) {
                $param = $parameter->getValue();
                $sql   = preg_replace('/\?/', "'$param'", $sql, 1);
            }
        } else {
            $queryBuilder = $this->createQueryBuilder("c");
            $queryBuilder->select('c.id');
            $queryBuilder->innerjoin('c.contratoContaReceber', 'cr');
            $queryBuilder->innerjoin('cr.tituloRecebers', 'tr');
            $queryBuilder->innerjoin('tr.forma_recebimento', 'fp');
            $queryBuilder->innerjoin('c.responsavel_venda_funcionario', 'f');
            $queryBuilder->innerjoin('c.aluno', 'a');
            $queryBuilder->leftJoin('a.interessados', 'i');

            $queryBuilder->where('c.franqueada = :franqueada');
            $queryBuilder->setParameter('franqueada', VariaveisCompartilhadas::$franqueadaID);

            if (is_null($parametros[ConstanteParametros::CHAVE_DATA_INICIAL]) === false) {
                $queryBuilder->andWhere('c.data_matricula >= :data_inicial');
                $queryBuilder->setParameter('data_inicial', $parametros[ConstanteParametros::CHAVE_DATA_INICIAL]);
            }

            if (is_null($parametros[ConstanteParametros::CHAVE_DATA_FINAL]) === false) {
                $queryBuilder->andWhere('c.data_matricula <= :data_final');
                $queryBuilder->setParameter('data_final', $parametros[ConstanteParametros::CHAVE_DATA_FINAL]);
            }

            if (is_null($parametros[ConstanteParametros::CHAVE_TIPO_LEAD]) === false) {
                $queryBuilder->andWhere('i.tipo_lead = :tipo_matricula');
                $queryBuilder->setParameter('tipo_matricula', $parametros[ConstanteParametros::CHAVE_TIPO_LEAD]);
            }

            $queryBuilder->andWhere('f.id = :funcionario');
            $queryBuilder->setParameter('funcionario', $parametros[ConstanteParametros::CHAVE_FUNCIONARIO]);

            // Filtra e substitui a query para passar ao Jasper
            $sql = $queryBuilder->getQuery()->getSQL();
            $sql = preg_replace('/.*WHERE\s(.*)$/', '$1', $sql);

            // Seleciona somente os wheres
            $sql = preg_replace('/c0_/', 'c', $sql);
            $sql = preg_replace('/c1_/', 'cr', $sql);
            $sql = preg_replace('/t2_/', 'tr', $sql);
            $sql = preg_replace('/f3_/', 'fp', $sql);
            $sql = preg_replace('/f4_/', 'f', $sql);
            $sql = preg_replace('/a5_/', 'a', $sql);
            $sql = preg_replace('/i6_/', 'i', $sql);

            // Substituição de parâmetros
            $parameters = $queryBuilder->getParameters();
            foreach ($parameters as $parameter) {
                $param = $parameter->getValue();
                $sql   = preg_replace('/\?/', "'$param'", $sql, 1);
            }
        }//end if

        return $sql;
    }

    /**
     * Monta os Filtros de busca
     *
     * @param \Doctrine\ORM\QueryBuilder $queryBuilder
     * @param array $parametros
     */
    protected function filtrosPersonal(&$queryBuilder, $parametros)
    {
        if ((isset($parametros[ConstanteParametros::CHAVE_AGENDAMENTO_PERSONAL]) === true) && (empty($parametros[ConstanteParametros::CHAVE_AGENDAMENTO_PERSONAL]) === false)) {
            $queryBuilder->andWhere("agcp.id = :agendamentoPersonalId");
            $queryBuilder->setParameter("agendamentoPersonalId", $parametros[ConstanteParametros::CHAVE_AGENDAMENTO_PERSONAL]);
        }
    }

    /**
     * Busca de diario personal
     *
     * @param int $contratoId
     * @param array $parametros
     *
     * @return array|NULL
     */
    public function buscarDiarioPersonal($contratoId, $parametros)
    {
        $queryBuilder = $this->createQueryBuilder("ct");
        $queryBuilder->addSelect("agcp");
        $queryBuilder->addSelect("reagendamentos");
        $queryBuilder->addSelect("lv");
        $queryBuilder->addSelect("cdtp");
        $queryBuilder->addSelect("func");
        $queryBuilder->addSelect("pfunc");
        $queryBuilder->addSelect("adp");
        $queryBuilder->addSelect("adpl");
        $queryBuilder->addSelect("ladp");
        $queryBuilder->addSelect("func2");
        $queryBuilder->addSelect("pfunc2");
        $queryBuilder->addSelect("alunoPersonal");
        $queryBuilder->addSelect("pessoaPersonal");
        $queryBuilder->addSelect("slf");
        $queryBuilder->addSelect("sl");
        $queryBuilder->leftJoin("ct.franqueada", "fran");
        $queryBuilder->leftJoin("ct.agendamentoPersonals", "agcp");
        $queryBuilder->leftJoin("ct.livro", "lv");
        $queryBuilder->leftJoin("ct.aluno", "alunoPersonal");
        $queryBuilder->leftJoin("ct.creditosPersonal", "cdtp");
        $queryBuilder->leftJoin("alunoPersonal.pessoa", "pessoaPersonal");
        $queryBuilder->leftJoin("agcp.datasReagendamentoPersonals", "reagendamentos");
        $queryBuilder->leftJoin("agcp.sala_franqueada", "slf");
        $queryBuilder->leftJoin("agcp.alunoDiarioPersonal", "adp");
        $queryBuilder->leftJoin("agcp.funcionario", "func");
        $queryBuilder->leftJoin("func.pessoa", "pfunc");
        $queryBuilder->leftJoin("slf.sala", "sl");
        $queryBuilder->leftJoin("adp.livro", "ladp");
        $queryBuilder->leftJoin("adp.aluno_diario_personal_licao", "adpl");
        $queryBuilder->leftJoin("adp.funcionario", "func2");
        $queryBuilder->leftJoin("func2.pessoa", "pfunc2");
        // $queryBuilder->leftJoin("alunoPersonal.alunoAvaliacaos", "alunoAvaliacao", "WITH", "alunoAvaliacao.turma IS NULL AND alunoAvaliacao.personal = '1'");
        // $queryBuilder->leftJoin("alunoPersonal.alunoAvaliacaoConceituals", "alunoAvaliacaoConceitual", "WITH", "alunoAvaliacaoConceitual.turma IS NULL AND alunoAvaliacaoConceitual.personal = '1'");
        // $queryBuilder->leftJoin("alunoAvaliacao.nota_mid_term_oral", "nmto");
        // $queryBuilder->leftJoin("alunoAvaliacao.nota_final_oral", "nfo");
        // $queryBuilder->leftJoin("alunoAvaliacao.nota_retake_mid_term_oral", "nrmto");
        // $queryBuilder->leftJoin("alunoAvaliacao.nota_retake_final_oral", "nrfo");
        $this->filtrarFranqueada($queryBuilder);
        $queryBuilder->andWhere("ct.id = :contratoId");
        $queryBuilder->setParameter("contratoId", $contratoId);
        $this->filtrosPersonal($queryBuilder, $parametros);
        return \App\Helper\FunctionHelper::retornaArrayNull($queryBuilder, true);
    }

    /**
     * Busca todos os contratos ativos através da turma
     *
     * @param int $turmaId
     *
     * @return array|NULL
     */
    public function buscarContratosAtivosComDollarPorTurma($turmaId)
    {
        $queryBuilder = $this->montaQueryBase();
        $this->filtrarFranqueada($queryBuilder);
        $queryBuilder->andWhere("tm.id = :turmaId");
        $queryBuilder->andWhere("ct.situacao = :situacaoContrato");
        $queryBuilder->setParameter("turmaId", $turmaId);
        $queryBuilder->setParameter("situacaoContrato", SituacoesSistema::SITUACAO_CONTRATO_VIGENTE);
        return \App\Helper\FunctionHelper::retornaArrayNull($queryBuilder);
    }

    /**
     * Busca a quantidade de contratos vigentes na turma
     *
     * @param int $id
     *
     * @return array|null
     */
    public function quantidadeContratosVigentesTurma ($id)
    {
        $queryBuilder = $this->createQueryBuilder('c');
        $queryBuilder->join('c.turma', 't');
        $queryBuilder->where('t.id = :id');
        // $queryBuilder->andWhere('t.id = c.contrato_id');
        $queryBuilder->andWhere('c.situacao = :SITUACAO_VIGENTE');
        $queryBuilder->select('t.id');
        $queryBuilder->select('COUNT(c) as qtde');
        $queryBuilder->groupBy('t.id');
        $queryBuilder->setParameter("SITUACAO_VIGENTE", "V");
        $queryBuilder->setParameter("id", $id);

        return $queryBuilder->getQuery()->getResult(\Doctrine\ORM\Query::HYDRATE_ARRAY);
    }


    /**
     * Filtra o relatório de matricula e rematricula contratos
     *
     * @param array $parametros
     * @param \Doctrine\ORM\QueryBuilder $queryBuilder
     *
     * @return array
     */
    private function filtrosRelatorioMatriculaRematriculaContratos($parametros, &$queryBuilder)
    {

        $queryBuilder->andWhere('c.franqueada = :franqueada');
        $queryBuilder->setParameter('franqueada', (int) VariaveisCompartilhadas::$franqueadaID);

        if (isset($parametros[ConstanteParametros::CHAVE_TIPO_MOVIMENTACAO]) === true && count($parametros[ConstanteParametros::CHAVE_TIPO_MOVIMENTACAO]) > 0) {
            $queryBuilder->andWhere("c.tipo_contrato in ('" . implode("', '", $parametros[ConstanteParametros::CHAVE_TIPO_MOVIMENTACAO]) . "')");
        }

        if (isset($parametros[ConstanteParametros::CHAVE_DATA_CRIACAO_DE]) === true && empty($parametros[ConstanteParametros::CHAVE_DATA_CRIACAO_DE]) === false) {
            $queryBuilder->andWhere('c.data_contrato >= :data_criacao_de');
            $queryBuilder->setParameter('data_criacao_de', $parametros[ConstanteParametros::CHAVE_DATA_CRIACAO_DE]);
        }

        if (isset($parametros[ConstanteParametros::CHAVE_DATA_CRIACAO_ATE]) === true && empty($parametros[ConstanteParametros::CHAVE_DATA_CRIACAO_ATE]) === false) {
            $queryBuilder->andWhere('c.data_contrato <= :data_criacao_ate');
            $queryBuilder->setParameter('data_criacao_ate', $parametros[ConstanteParametros::CHAVE_DATA_CRIACAO_ATE]);
        }

        if (isset($parametros[ConstanteParametros::CHAVE_LIVRO]) === true && empty($parametros[ConstanteParametros::CHAVE_LIVRO]) === false) {
            $queryBuilder->andWhere('l.id = :livro');
            $queryBuilder->setParameter('livro', (int) $parametros[ConstanteParametros::CHAVE_LIVRO]);
        }

        if (isset($parametros[ConstanteParametros::CHAVE_CURSO]) === true && empty($parametros[ConstanteParametros::CHAVE_CURSO]) === false) {
            $queryBuilder->andWhere('c.curso = :curso');
            $queryBuilder->setParameter('curso', (int) $parametros[ConstanteParametros::CHAVE_CURSO]);
        }

        if (isset($parametros[ConstanteParametros::CHAVE_IDIOMA]) === true && empty($parametros[ConstanteParametros::CHAVE_IDIOMA]) === false) {
            $queryBuilder->andWhere('u.idioma = :idioma');
            $queryBuilder->setParameter('idioma', (int) $parametros[ConstanteParametros::CHAVE_IDIOMA]);
        }

        if (isset($parametros[ConstanteParametros::CHAVE_FRANQUEADA]) === true && empty($parametros[ConstanteParametros::CHAVE_FRANQUEADA]) === false) {
            $queryBuilder->andWhere('c.franqueada = :franqueada');
            $queryBuilder->setParameter('franqueada', (int) $parametros[ConstanteParametros::CHAVE_FRANQUEADA]);
        }

        if (isset($parametros[ConstanteParametros::CHAVE_RESPONSAVEL_CARTEIRA]) === true && empty($parametros[ConstanteParametros::CHAVE_RESPONSAVEL_CARTEIRA]) === false) {
            $queryBuilder->andWhere('rcf.id = :responsavelCarteira');
            $queryBuilder->setParameter('responsavelCarteira', (int) $parametros[ConstanteParametros::CHAVE_RESPONSAVEL_CARTEIRA]);
        }

        if (isset($parametros[ConstanteParametros::CHAVE_SITUACAO_ALUNO]) === true && count($parametros[ConstanteParametros::CHAVE_SITUACAO_ALUNO]) > 0) {
            $queryBuilder->andWhere("a.situacao in ('" . implode("', '", $parametros[ConstanteParametros::CHAVE_SITUACAO_ALUNO]) . "')");
        }

        if (isset($parametros[ConstanteParametros::CHAVE_INSTRUTOR_FLAG]) === true && empty($parametros[ConstanteParametros::CHAVE_INSTRUTOR_FLAG]) === false) {
            // Se for personal, ignora nesse momento pra filtrar quando for puxar o instrutor personal, no facade
            $queryBuilder->andWhere(
                $queryBuilder->expr()->orX(
                    $queryBuilder->expr()->eq('if.id', $parametros[ConstanteParametros::CHAVE_INSTRUTOR_FLAG]),
                    $queryBuilder->expr()->eq('m.tipo', "'" . SituacoesSistema::MODALIDADE_PERSONAL . "'")
                )
            );
        }
    }

    /**
     * Busca os dados do relatório de matrículas e rematrículas de contratos
     *
     * @param string $mensagem
     * @param array $parametros
     *
     * @return array
     */
    public function buscarDadosRelatorioMatriculaRematriculaContratos(&$mensagem, $parametros)
    {
        $queryBuilder = $this->createQueryBuilder("c");
        $selects      = [
            "date_format(c.data_contrato, '%d/%m/%Y') as data_contrato",
            "p.nome_contato as nome_aluno",
            "(CASE  WHEN c.tipo_contrato = 'M' THEN 'Matricula'
                WHEN c.tipo_contrato = 'R' THEN 'Rematricula'
                ELSE ''
                END) as tipo_movimentacao",
            "l.descricao as livro",
            "CASE WHEN m.tipo = '" . SituacoesSistema::MODALIDADE_PERSONAL . "' THEN 'Personal' ELSE u.descricao END curso",
            "CASE WHEN m.tipo = '" . SituacoesSistema::MODALIDADE_PERSONAL . "' THEN 'Personal' ELSE i.descricao END idioma",
            "if.apelido instrutor",
            "(CASE  WHEN a.situacao = 'ATI' THEN 'Ativo'
                WHEN a.situacao = 'INA' THEN 'Inativo'
                WHEN a.situacao = 'TRA' THEN 'Trancado'
                ELSE ''
                END) as situacao_aluno",
            "rcf.apelido as responsavel_carteira",
            "m.tipo as tipo_modalidade",
            "c.id contrato_id",
        ];

        $queryBuilder->select($selects);
        $queryBuilder->join('c.aluno', 'a');
        $queryBuilder->join('a.pessoa', 'p');
        $queryBuilder->leftJoin('c.semestre', 's');
        $queryBuilder->leftJoin('c.livro', 'l');
        $queryBuilder->leftJoin('c.responsavel_carteira_funcionario', 'rcf');
        $queryBuilder->leftJoin('c.modalidade_turma', 'm');
        $queryBuilder->leftJoin('c.curso', 'u');
        $queryBuilder->leftJoin('u.idioma', 'i');
        $queryBuilder->leftJoin('c.turma', 't', 'WITH', 't.excluido = 0');
        $queryBuilder->leftJoin('t.funcionario', 'if');
        $queryBuilder->orderBy("date_format(c.data_contrato, '%Y/%m/%d')", 'ASC');
        $queryBuilder->addOrderBy('p.nome_contato', 'ASC');

        $this->filtrosRelatorioMatriculaRematriculaContratos($parametros, $queryBuilder);

        return $queryBuilder->getQuery()->getResult(\Doctrine\ORM\Query::HYDRATE_ARRAY);
    }

    /**
     * Filtra o relatório de movimentação contratos
     *
     * @param array $parametros
     * @param \Doctrine\ORM\QueryBuilder $queryBuilder
     *
     * @return array
     */
    private function filtrosRelatorioMovimentacaoContratos($parametros, &$queryBuilder)
    {

        $queryBuilder->andWhere('c.franqueada = :franqueada');
        $queryBuilder->setParameter('franqueada', (int) VariaveisCompartilhadas::$franqueadaID);

        if (isset($parametros[ConstanteParametros::CHAVE_DATA_CRIACAO_DE]) === true && empty($parametros[ConstanteParametros::CHAVE_DATA_CRIACAO_DE]) === false) {
            $queryBuilder->andWhere('c.data_contrato >= :data_criacao_de');
            $queryBuilder->setParameter('data_criacao_de', $parametros[ConstanteParametros::CHAVE_DATA_CRIACAO_DE]);
        }

        if (isset($parametros[ConstanteParametros::CHAVE_DATA_CRIACAO_ATE]) === true && empty($parametros[ConstanteParametros::CHAVE_DATA_CRIACAO_ATE]) === false) {
            $queryBuilder->andWhere('c.data_contrato <= :data_criacao_ate');
            $queryBuilder->setParameter('data_criacao_ate', $parametros[ConstanteParametros::CHAVE_DATA_CRIACAO_ATE]);
        }

        if (isset($parametros[ConstanteParametros::CHAVE_DATA_INICIO_CONTRATO_DE]) === true && empty($parametros[ConstanteParametros::CHAVE_DATA_INICIO_CONTRATO_DE]) === false) {
            $queryBuilder->andWhere('c.data_inicio_contrato >= :data_inicio_de');
            $queryBuilder->setParameter('data_inicio_de', $parametros[ConstanteParametros::CHAVE_DATA_INICIO_CONTRATO_DE]);
        }

        if (isset($parametros[ConstanteParametros::CHAVE_DATA_TERMINO_CONTRATO_ATE]) === true && empty($parametros[ConstanteParametros::CHAVE_DATA_TERMINO_CONTRATO_ATE]) === false) {
            $queryBuilder->andWhere('c.data_termino_contrato <= :data_termino_ate');
            $queryBuilder->setParameter('data_termino_ate', $parametros[ConstanteParametros::CHAVE_DATA_TERMINO_CONTRATO_ATE]);
        }

        if (isset($parametros[ConstanteParametros::CHAVE_SITUACAO_CONTRATO]) === true && count($parametros[ConstanteParametros::CHAVE_SITUACAO_CONTRATO]) > 0) {
            $queryBuilder->andWhere("c.situacao in ('" . implode("', '", $parametros[ConstanteParametros::CHAVE_SITUACAO_CONTRATO]) . "')");
        }
    }

    /**
     * Busca dados para gerar o relatório de Situação de Contrato
     * 
     */
    public function buscarDadosRelatorioSituacaoContrato($parametros){
        $queryBuilder = $this->createQueryBuilder("c");
        
        $selects = 
            [
                "c.id AS contrato",
                "pe.nome_contato AS aluno", 
                "livr.descricao AS livro", 
                "cur.descricao AS curso",  
                "idio.descricao AS idioma", 
                "func.apelido AS instrutor", 
                "(CASE  WHEN c.tipo_contrato = 'M' THEN 'Matricula' WHEN c.tipo_contrato = 'R' THEN 'Rematricula' ELSE '' END) as tipo_movimentacao",
                "(CASE  WHEN al.situacao = 'ATI' THEN 'Ativo'  WHEN al.situacao = 'INA' THEN 'Inativo' WHEN al.situacao = 'TRA' THEN 'Trancado'               ELSE ''
                END) as situacao_aluno",
                "(CASE  WHEN c.situacao = 'V' THEN 'Vigente' 
                WHEN c.situacao = 'E' THEN 'Encerrado' 
                WHEN c.situacao = 'R' THEN 'Rescindido' 
                WHEN c.situacao = 'T' THEN 'Trancado' 
                WHEN c.situacao = 'C' THEN 'Cancelado'  ELSE '' END) as situacao_contrato",
                "smstr.descricao AS semestre",
            ];
        
        if($parametros[ConstanteParametros::CHAVE_MOSTRAR_MOTIVO_CANCELAMENTO]){
            array_push($selects, "c.motivo_cancelamento AS motivo_cancelamento");
        }
     
        $queryBuilder->select($selects)
        ->innerJoin('c.aluno', 'al')
        ->innerJoin('al.pessoa','pe')
        ->leftJoin('c.livro','livr')
        ->leftJoin('c.curso','cur')
        ->leftJoin('cur.idioma','idio')
        ->leftJoin('c.turma','turm')
        ->leftJoin('turm.funcionario','func')
        ->innerJoin('c.franqueada', 'fran')
        ->innerJoin('c.semestre','smstr')
        ->where('fran.id = :franqueada')
        ->setParameter(':franqueada',$parametros[ConstanteParametros::CHAVE_FRANQUEADA])
        ->orderBy('pe.nome_contato');

        if(key_exists(ConstanteParametros::CHAVE_INSTRUTOR_FLAG, $parametros) && !empty($parametros[ConstanteParametros::CHAVE_INSTRUTOR_FLAG])){
            $queryBuilder->andWhere("func.id = :instrutor");
            $queryBuilder->setParameter("instrutor", $parametros[ConstanteParametros::CHAVE_INSTRUTOR_FLAG]);
        }
        if(key_exists(ConstanteParametros::CHAVE_IDIOMA, $parametros) && !empty($parametros[ConstanteParametros::CHAVE_IDIOMA])){
            $queryBuilder->andWhere("idio.id = :idioma");
            $queryBuilder->setParameter("idioma", $parametros[ConstanteParametros::CHAVE_IDIOMA]);
        }
        if(key_exists(ConstanteParametros::CHAVE_LIVRO, $parametros) && !empty($parametros[ConstanteParametros::CHAVE_LIVRO])){
            $queryBuilder->andWhere("livr.id = :livro");
            $queryBuilder->setParameter("livro", $parametros[ConstanteParametros::CHAVE_LIVRO]);
        }
        if(key_exists(ConstanteParametros::CHAVE_SEMESTRE, $parametros) && !empty($parametros[ConstanteParametros::CHAVE_SEMESTRE])){
            $queryBuilder->andWhere("smstr.id = :semestre");
            $queryBuilder->setParameter("semestre", $parametros[ConstanteParametros::CHAVE_SEMESTRE]);
        }
        if(key_exists(ConstanteParametros::CHAVE_TIPO_MOVIMENTACAO, $parametros) && !empty($parametros[ConstanteParametros::CHAVE_TIPO_MOVIMENTACAO])){
            $queryBuilder->andWhere("c.tipo_contrato = :tipo_movimentacao");
            $queryBuilder->setParameter("tipo_movimentacao", $parametros[ConstanteParametros::CHAVE_TIPO_MOVIMENTACAO]);
        }
        if(key_exists(ConstanteParametros::CHAVE_RESPONSAVEL_CARTEIRA, $parametros) && !empty($parametros[ConstanteParametros::CHAVE_RESPONSAVEL_CARTEIRA])){
            $queryBuilder->andWhere("c.responsavel_carteira_funcionario = :responsavel_carteira");
            $queryBuilder->setParameter("responsavel_carteira", $parametros[ConstanteParametros::CHAVE_RESPONSAVEL_CARTEIRA]);
        }
        if(key_exists(ConstanteParametros::CHAVE_CURSO, $parametros) && !empty($parametros[ConstanteParametros::CHAVE_CURSO])){
            $queryBuilder->andWhere("cur.id = :curso");
            $queryBuilder->setParameter("curso", $parametros[ConstanteParametros::CHAVE_CURSO]);
        }
        if(key_exists(ConstanteParametros::CHAVE_SITUACAO_CONTRATO, $parametros) && !empty($parametros[ConstanteParametros::CHAVE_SITUACAO_CONTRATO])){
            $queryBuilder->andWhere("c.situacao IN (:situacao)");
            $queryBuilder->setParameter(":situacao", $parametros[ConstanteParametros::CHAVE_SITUACAO_CONTRATO]);
        }
        if(key_exists(ConstanteParametros::CHAVE_SITUACAO_ALUNO, $parametros) && !empty($parametros[ConstanteParametros::CHAVE_SITUACAO_ALUNO])){
            $queryBuilder->andWhere("al.situacao IN (:situacao_aluno)");
            $queryBuilder->setParameter(":situacao_aluno", $parametros[ConstanteParametros::CHAVE_SITUACAO_ALUNO]);
        }

       
        if(key_exists(ConstanteParametros::CHAVE_DATA_CONTRATO_CANCELAMENTO_DE, $parametros) && !empty($parametros[ConstanteParametros::CHAVE_DATA_CONTRATO_CANCELAMENTO_DE])
        && key_exists(ConstanteParametros::CHAVE_DATA_CONTRATO_CANCELAMENTO_ATE, $parametros) && !empty($parametros[ConstanteParametros::CHAVE_DATA_CONTRATO_CANCELAMENTO_ATE])) {
        
            $dataCanceladoDe = strtotime(str_replace("/", "-",$parametros[ConstanteParametros::CHAVE_DATA_CONTRATO_CANCELAMENTO_DE] . " 00:00:00"));
            $dataCanceladoDe = date('Y-m-d H:i:s', $dataCanceladoDe);
            $dataCanceladoAte = strtotime(str_replace("/", "-",$parametros[ConstanteParametros::CHAVE_DATA_CONTRATO_CANCELAMENTO_ATE] . " 23:59:59"));
            $dataCanceladoAte = date('Y-m-d H:i:s', $dataCanceladoAte);
        
            
            $queryBuilder->andWhere("c.data_cancelamento >= :data_cancelamento_de");
            $queryBuilder->setParameter('data_cancelamento_de', $dataCanceladoDe);
   
            $queryBuilder->andWhere("c.data_cancelamento <= :data_cancelamento_ate");
            $queryBuilder->setParameter('data_cancelamento_ate', $dataCanceladoAte);
        
        } else {
            if(key_exists(ConstanteParametros::CHAVE_DATA_INICIO_CONTRATO_DE, $parametros) && !empty($parametros[ConstanteParametros::CHAVE_DATA_INICIO_CONTRATO_DE])) {
                $dataInicioContrato = strtotime(str_replace("/", "-",$parametros[ConstanteParametros::CHAVE_DATA_INICIO_CONTRATO_DE] . " 00:00:00"));
                $dataInicioContrato = date('Y-m-d H:i:s', $dataInicioContrato);
                
                $queryBuilder->andWhere("c.data_inicio_contrato >= :data_inicio_contrato");
                $queryBuilder->setParameter('data_inicio_contrato', $dataInicioContrato);
            }
            if(key_exists(ConstanteParametros::CHAVE_DATA_TERMINO_CONTRATO_DE, $parametros) && !empty($parametros[ConstanteParametros::CHAVE_DATA_TERMINO_CONTRATO_DE])) {
                $dataTerminoContrato = strtotime(str_replace("/", "-",$parametros[ConstanteParametros::CHAVE_DATA_TERMINO_CONTRATO_DE] . " 23:59:59"));
                $dataTerminoContrato = date('Y-m-d H:i:s', $dataTerminoContrato);
            
                $queryBuilder->andWhere("c.data_termino_contrato <= :data_termino_contrato");
                $queryBuilder->setParameter('data_termino_contrato', $dataTerminoContrato);
            }

            if(key_exists(ConstanteParametros::CHAVE_DATA_CRIACAO_DE, $parametros) && !empty($parametros[ConstanteParametros::CHAVE_DATA_CRIACAO_DE])) {
                $dataCriacaoInicial = strtotime(str_replace("/", "-",$parametros[ConstanteParametros::CHAVE_DATA_CRIACAO_DE] . " 00:00:00"));
                $dataCriacaoInicial = date('Y-m-d H:i:s', $dataCriacaoInicial);
                
                $queryBuilder->andWhere("c.data_contrato >= :data_criacao_inicial");
                $queryBuilder->setParameter('data_criacao_inicial', $dataCriacaoInicial);
            }
            if(key_exists(ConstanteParametros::CHAVE_DATA_CRIACAO_ATE, $parametros) && !empty($parametros[ConstanteParametros::CHAVE_DATA_CRIACAO_ATE])) {
                $dataCriacaoFinal = strtotime(str_replace("/", "-",$parametros[ConstanteParametros::CHAVE_DATA_CRIACAO_ATE] . " 23:59:59"));
                $dataCriacaoFinal = date('Y-m-d H:i:s', $dataCriacaoFinal);
                
                $queryBuilder->andWhere("c.data_contrato <= :data_criacao_final");
                $queryBuilder->setParameter('data_criacao_final', $dataCriacaoFinal);
            }
            
        }
            return $queryBuilder->getQuery()->getResult(\Doctrine\ORM\Query::HYDRATE_ARRAY);
    }

    /**
     * Busca os dados do relatório de movimentação de contratos
     *
     * @param string $mensagem
     * @param array $parametros
     *
     * @return array
     */
    public function buscarDadosRelatorioMovimentacaoContratos(&$mensagem, $parametros)
    {
        $queryBuilder = $this->createQueryBuilder("c");
        $selects      = [
            "CONCAT(a.id, '/', c.sequencia_contrato) AS numero_contrato",
            "p.nome_contato as nome_aluno",
            "date_format(c.data_contrato, '%d/%m/%Y') as criado_em",
            "date_format(c.data_inicio_contrato, '%d/%m/%Y') as data_inicio",
            "date_format(c.data_termino_contrato, '%d/%m/%Y') as data_termino",
            "(CASE  WHEN c.situacao = 'V' THEN 'Vigente'
                WHEN c.situacao = 'E' THEN 'Encerrado'
                WHEN c.situacao = 'R' THEN 'Rescindido'
                WHEN c.situacao = 'C' THEN 'Cancelado'
                WHEN c.situacao = 'T' THEN 'Trancado'
                ELSE ''
                END) as situacao_contrato",
            "m.descricao modalidade_turma",
            "CASE WHEN m.tipo = '" . SituacoesSistema::MODALIDADE_PERSONAL . "' THEN 'Personal' ELSE t.descricao END turma",
            "c.motivo_cancelamento",
        ];

        $queryBuilder->select($selects);
        $queryBuilder->join('c.aluno', 'a');
        $queryBuilder->join('a.pessoa', 'p');
        $queryBuilder->leftJoin('c.modalidade_turma', 'm');
        $queryBuilder->leftJoin('c.turma', 't', 'WITH', 't.excluido = 0');
        $queryBuilder->orderBy("date_format(c.data_contrato, '%Y/%m/%d')", 'ASC');
        $queryBuilder->addOrderBy('p.nome_contato', 'ASC');

        $this->filtrosRelatorioMovimentacaoContratos($parametros, $queryBuilder);

        return $queryBuilder->getQuery()->getResult(\Doctrine\ORM\Query::HYDRATE_ARRAY);
    }

    public function buscarDadosRelatorioMatriculas($parametros = null) {

        $where = '';
        
        if(isset($parametros['semestre'])){
            $where .= (' AND contrato.semestre_id = ' . $parametros['semestre']);
        }else{
            $dataDesde = date('Y-m-d', strtotime(isset($parametros['data_inicial']) ? str_replace('/', '-', $parametros['data_inicial']) : "-1 months"));
            $dataAte = date('Y-m-d', strtotime(isset($parametros['data_final']) ? str_replace('/', '-', $parametros['data_final']) : "today"));
        
            $where = " AND contrato.data_matricula >= '" . $dataDesde . "' AND contrato.data_matricula <= '" . $dataAte . "'";
        } 

        $where .= isset($parametros['idioma']) ? (' AND curso.idioma_id = ' . $parametros['idioma']) : '';
        $where .= isset($parametros['curso']) ? (' AND curso.id = ' . $parametros['curso']) : '';
        $where .= isset($parametros['estagio']) ? (' AND turma.livro_id = ' . $parametros['estagio']) : '';
        $where .= isset($parametros['turma']) ? (' AND turma.modalidade_turma_id = ' . $parametros['turma']) : '';
        $where .= isset($parametros['aluno']) ? (' AND aluno.id = ' . $parametros['aluno'] ) : '';
        $where .= isset($parametros['franqueada']) ? (' AND contrato.franqueada_id = ' . $parametros['franqueada'] ) : '';
        $where .= isset($parametros['livro']) ? (' AND turma.livro_id = ' . $parametros['livro'] ) : '';

        if(isset($parametros['situacaoMatricula'])) {
            $situacao = " AND ( contrato.situacao LIKE '" . implode("' OR contrato.situacao LIKE '" ,$parametros['situacaoMatricula']) . "' ) ";
            $where .= $situacao;
        }

        // if(isset($parametros['matricula']) || isset($parametros['rematricula']) || isset($parametros['transferencias'])) {
        //     $tipoContrato = '';
        //     $tipoContrato .= isset($parametros['matricula']) ? (" contrato.tipo_contrato LIKE 'M'" ) : '';
        //     $tipoContrato .= isset($parametros['rematricula']) ? ( ($tipoContrato ? ' OR' : '') . " contrato.tipo_contrato LIKE 'R'" ) : '';
        //     $tipoContrato .= isset($parametros['transferencias']) ? ( ($tipoContrato ? ' OR' : '') . " contrato.tipo_contrato LIKE 'T'" ) : '';
        //     $tipoContrato = ' AND ( ' . $tipoContrato . ' ) ';
        //     $where .= $tipoContrato;
        // }

        if($parametros['tipo_contrato'] == 'R' || $parametros['tipo_contrato'] == 'M') {
            $tipoContrato = '';
            $tipoContrato .= " contrato.tipo_contrato LIKE '".$parametros['tipo_contrato']."'" ;
            $tipoContrato = ' AND ( ' . $tipoContrato . ' ) ';
            $where .= $tipoContrato;
        }

        if(isset($parametros['orderBy'])) {
            $where .= ' ORDER BY ' . $parametros['orderBy'] . ' ';
            $where .= $parametros['orderDesc'];
        }

        $sql = "
        SELECT
            contrato.id as contrato_id,
            contrato.sequencia_contrato as sequencia_contrato,
            turma.descricao as turma,
            DATE_FORMAT(data_matricula, '%d/%m/%Y') as data_matricula,
            pessoa.nome_contato as aluno,
            idioma.descricao as idioma,
            func.nome_contato as funcionario,
            contrato.situacao as situacao,
            professor.nome_contato as professor,
            livro.descricao as livro
        FROM
            contrato
        LEFT JOIN aluno
            ON aluno.id = contrato.aluno_id
        LEFT JOIN pessoa
            ON pessoa.id = aluno.pessoa_id
        LEFT JOIN curso
            ON contrato.curso_id = curso.id
        LEFT JOIN idioma
            ON curso.idioma_id = idioma.id
        LEFT JOIN turma
            ON contrato.turma_id = turma.id
        LEFT JOIN conta_receber
            ON conta_receber.contrato_id = contrato.id
        LEFT JOIN item_conta_receber
            ON item_conta_receber.conta_receber_id = conta_receber.id
        LEFT JOIN plano_conta
            ON item_conta_receber.plano_conta_id = plano_conta.id
        LEFT JOIN funcionario
            ON conta_receber.vendedor_funcionario_id = funcionario.id
        LEFT JOIN pessoa as func
            ON func.id = funcionario.pessoa_id
        LEFT JOIN funcionario as funcionario_professor
            ON turma.funcionario_id = funcionario_professor.id
        LEFT JOIN pessoa as professor
            ON funcionario_professor.pessoa_id = professor.id
        LEFT JOIN livro
            ON turma.livro_id = livro.id
        WHERE
            contrato.excluido = 0
            AND 
            plano_conta.id = 38
            " . $where;

        return $this->managerRegistry
            ->getConnection()
            ->prepare($sql)
            ->executeQuery()
            ->fetchAllAssociative();
    }

    public function buscarDadosRelatorioSaldoHoras($parametros) {

        $query = $this->createQueryBuilder("c")
            ->select([
                "date_format(c.data_contrato, '%Y-%m-%d') as data_contrato",
                "date_format(c.data_inicio_contrato, '%Y-%m-%d') as data_inicio_contrato",
                "date_format(c.data_termino_contrato, '%Y-%m-%d') as data_termino_contrato",
                'liv.descricao as livro',
                'pes.nome_contato as aluno',
                'cre.quantidade as qnt_creditos',
                'cre.saldo as saldo_creditos'
            ])
            ->leftJoin('c.livro', 'liv')
            ->leftJoin('c.aluno', 'alu')
            ->leftJoin('alu.pessoa', 'pes')
            ->leftJoin('c.creditosPersonal', 'cre')
            ->where('c.franqueada = :franqueada')
            ->setParameter("franqueada", $parametros[ConstanteParametros::CHAVE_FRANQUEADA])
            ->andWhere('c.modalidade_turma IN (2,3)');

        if(key_exists(ConstanteParametros::CHAVE_MODALIDADE_TURMA, $parametros) && !empty($parametros[ConstanteParametros::CHAVE_MODALIDADE_TURMA])){
            $query->andWhere("c.modalidade_turma in (:modalidade_turma)");
            $query->setParameter("modalidade_turma", $parametros[ConstanteParametros::CHAVE_MODALIDADE_TURMA]);
        }

        if(key_exists(ConstanteParametros::CHAVE_LIVRO, $parametros) && !empty($parametros[ConstanteParametros::CHAVE_LIVRO])){
            $query->andWhere("liv.id = :livro");
            $query->setParameter("livro", $parametros[ConstanteParametros::CHAVE_LIVRO]);
        }

        if(key_exists(ConstanteParametros::CHAVE_DATA_INICIAL, $parametros) && !empty($parametros[ConstanteParametros::CHAVE_DATA_INICIAL])) {
            $dataInicial = strtotime(str_replace("/", "-",$parametros[ConstanteParametros::CHAVE_DATA_INICIAL] . " 00:00:00"));
            $dataInicial = date('Y-m-d H:i:s', $dataInicial);
            $query->andWhere("c.data_contrato >= :data_inicial");
            $query->setParameter('data_inicial', $dataInicial);
        }

        if(key_exists(ConstanteParametros::CHAVE_DATA_FINAL, $parametros) && !empty($parametros[ConstanteParametros::CHAVE_DATA_FINAL])) {
            $dataFinal = strtotime(str_replace("/", "-",$parametros[ConstanteParametros::CHAVE_DATA_FINAL] . " 23:59:59"));
            $dataFinal = date('Y-m-d H:i:s', $dataFinal);
            $query->andWhere("c.data_contrato <= :data_final");
            $query->setParameter('data_final', $dataFinal);
        }
        
        return $query->getQuery()->getResult();
    }

    public function buscarDadosRelatorioValoresTurma($parametros) {

        $where = '';
        
        $where = " and c0_.franqueada_id = ". $parametros[ConstanteParametros::CHAVE_FRANQUEADA];
        $where .= isset($parametros['turma']) ? (' AND tur.id = ' . $parametros['turma']) : '';
        $where .= isset($parametros['curso']) ? (' AND cur.id = ' . $parametros['curso']) : '';
        $where .= isset($parametros['livro']) ? (' AND tur.livro_id = ' . $parametros['livro']) : '';
        $where .= isset($parametros['idioma']) ? (' AND cur.idioma_id = ' . $parametros['idioma']) : '';
        
        if (isset($parametros[ConstanteParametros::CHAVE_SITUACAO_CONTRATO])) {
            $where .= " and c0_.situacao in ('" . implode("', '", $parametros[ConstanteParametros::CHAVE_SITUACAO_CONTRATO]) . "')";
        }

            $sql = "
                SELECT
                tur.descricao as turma,
                l.descricao as livro,
                cur.descricao as curso ,
                pes.nome_contato as nome_aluno,
                cr.id as cr_id,
                tr.id as tr_id,
                tr.valor_item as tr_valor_item,
                tr.valor_parcela_sem_desconto as tr_valor_parcela_sem_desconto,
                tr.valor_saldo_devedor as tr_saldo_devedor,
                tr.valor_parcela_sem_desconto - tr.valor_saldo_devedor as tr_valor_pago,
                c0_.situacao as situacao_contrato,
                tr.numero_parcela_documento,
                parcelas.matricula,
                parcelas.mensalidade,
                parcelas.material
            FROM
                contrato c0_
            LEFT JOIN turma tur ON c0_.turma_id = tur.id
            LEFT JOIN livro l on l.id = tur.livro_id 
            LEFT JOIN curso cur on tur.curso_id = cur.id 
            LEFT JOIN aluno al ON c0_.aluno_id = al.id
            LEFT JOIN pessoa pes ON    al.pessoa_id = pes.id
            LEFT JOIN conta_receber cr on cr.contrato_id = c0_.id
            LEFT JOIN titulo_receber tr on  tr.conta_receber_id = cr.id
            LEFT JOIN (SELECT
                temp.id,
                SUM(temp.matricula) as matricula,
                SUM(temp.mensalidade) as mensalidade,
                SUM(temp.material) as material
                FROM
                (SELECT
                    CASE WHEN tipo_item_id = 2 THEN numero_parcelas END as matricula,
                    CASE WHEN tipo_item_id > 2 THEN numero_parcelas END as mensalidade,
                    CASE WHEN tipo_item_id = 1 THEN numero_parcelas END as material,
                    cr.id
                FROM
                conta_receber cr
                JOIN item_conta_receber icr ON cr.id = icr.conta_receber_id
                JOIN item ON icr.item_id = item.id) as temp
                GROUP BY temp.id) AS parcelas ON cr.id = parcelas.id
            WHERE tur.descricao <> '' ".$where .
            " GROUP BY c0_.aluno_id  order by tur.id ";

            $retorno = $this->managerRegistry->getConnection()->fetchAllAssociative($sql);
            
            /*
                //$turmas = [];
                
                //$contador = count($retorno);
                
                
                for ($i = 0; $i < $contador; $i++) 
                {
                    $turma = [];
                    $alunos = [];
                    $nomeTurma = '';
                    $turma = [
                        'turma' => $retorno[$i]['turma']
                    ];
                    while ($nomeTurma == $retorno[$i]['turma'] || $nomeTurma == '') {
                        if ($i == $contador -1 ) {
                            break;
                        }  
                        $nomeTurma = $retorno[$i]['turma'];
                        $nMatricula = $retorno[$i]['matricula'];
                        $nMensalidade = $retorno[$i]['mensalidade'];
                        $nMaterial = $retorno[$i]['material'];
                                        
                        $alunos = [
                        'nome_Aluno' => $retorno[$i]['nome_aluno'],
                        'parcelas_Curso' => $retorno[$i]['mensalidade']
                        ];
                        
                        $valorTotal = 0;                    
                        $valorDevedor = 0;                    
                        $valorPago = 0;
                                
                        for ($j = 0; $j < $nMatricula; $j++) {
                            // planoMensalidade
                            if ($parametros['planoMensalidade'] == 1) {
                                $valorTotal = $valorTotal +  $retorno[$i]['tr_valor_parcela_sem_desconto']; 
                                $valorDevedor = $valorDevedor +  $retorno[$i]['tr_saldo_devedor']; 
                                $valorPago = $valorPago +  $retorno[$i]['tr_valor_pago']; 
                            }
                        
                            if ($i < $contador -1 ) {
                                $i++;
                            } else {
                                break;
                            }
                        }
                        
                        for ($j = 0; $j < $nMensalidade; $j++) {
                            $valorTotal = $valorTotal +  $retorno[$i]['tr_valor_parcela_sem_desconto']; 
                            $valorDevedor = $valorDevedor +  $retorno[$i]['tr_saldo_devedor']; 
                            $valorPago = $valorPago +  $retorno[$i]['tr_valor_pago']; 
                            if ($i < $contador -1 ) {
                                $i++;
                            } else {
                                break;
                            }
                        }
                        
                        for ($j = 0; $j < $nMaterial; $j++) {
                            // materialDidatico
                            if ($parametros['materialDidatico'] == 1) {
                                $valorTotal = $valorTotal +  $retorno[$i]['tr_valor_parcela_sem_desconto']; 
                                $valorDevedor = $valorDevedor +  $retorno[$i]['tr_saldo_devedor']; 
                                $valorPago = $valorPago +  $retorno[$i]['tr_valor_pago']; 
                            }
                        
                            if ($i < $contador -1 ) {
                                $i++;
                            } else {
                                break;
                            }
                        }
                        $alunos['Valor_Total'] =  $valorTotal;
                        $alunos['Valor_Devedor'] =  $valorDevedor;
                        $alunos['Valor_Pago'] =  $valorPago;

                        $turma['alunos'][] = $alunos;

                    }

                    $turmas['turmas'][] = $turma;

                    if ($i == $contador -1 ) {
                        break;
                    }                   
                }
                
                return $turmas;
                
            */
            
            return $retorno;
    }

    public function buscarDadosRelatorioMatriculaVenda($parametros)
    {

        $where = '';

        $where = "c.franqueada_id = :filtro_franqueada";

        $dataDesde = date('Y-m-d', strtotime(isset($parametros['data_inicial']) ? str_replace('/', '-', $parametros['data_inicial']) : "-1 months"));
        $dataAte = date('Y-m-d', strtotime(isset($parametros['data_final']) ? str_replace('/', '-', $parametros['data_final']) : "today"));
        $where .= " AND c.data_matricula >= :filtro_data_desde AND c.data_matricula <= :filtro_data_ate";

        if(isset($parametros[ConstanteParametros::CHAVE_RESPONSAVEL_VENDA_FUNCIONARIO])) {
            $where .= ' AND c.responsavel_venda_funcionario_id = :filtro_responsavel_venda ';
        }

        if (isset($parametros[ConstanteParametros::CHAVE_TIPO_CONTATO])) {
            $where .= ' AND i.tipo_lead = :filtro_tipo_contato ';
        }

        $sql = 
        " 
        SELECT
            p.nome_contato as responsavel_venda,
            p2.nome_contato as consultor_responsavel,
            i.tipo_lead as tipo_contato,
            c.data_matricula,
            icr.valor as taxa_matricula,
            icr.percentual_desconto,
            f.tipo_pagamento,
            IF(i.tipo_prospeccao_id = 1, 'Sim', 'Não') as superamigos
        FROM influx_crm_prod.contrato AS c
        INNER JOIN influx_crm_prod.conta_receber AS cr ON cr.contrato_id = c.id and c.sequencia_contrato = 1
        INNER JOIN influx_crm_prod.item_conta_receber AS icr ON icr.conta_receber_id = cr.id
        INNER JOIN influx_crm_prod.funcionario AS f ON c.responsavel_venda_funcionario_id = f.id
        INNER JOIN influx_crm_prod.pessoa AS p ON f.pessoa_id = p.id
        INNER JOIN influx_crm_prod.aluno AS a ON c.aluno_id = a.id
        LEFT JOIN influx_crm_prod.interessado AS i ON i.aluno_id = a.id
        INNER JOIN influx_crm_prod.funcionario AS f2 ON i.consultor_funcionario_id = f2.id
        INNER JOIN influx_crm_prod.pessoa AS p2 ON f2.pessoa_id = p2.id
        WHERE ".$where ."
        AND icr.item_id = 46
        AND i.tipo_lead is not null
        ORDER BY c.responsavel_venda_funcionario_id DESC";

        $conn = $this->managerRegistry->getConnection();

        $stmt = $conn->prepare($sql);
        $stmt->bindValue("filtro_franqueada", $parametros[ConstanteParametros::CHAVE_FRANQUEADA]);
        $stmt->bindValue("filtro_data_desde", $dataDesde);
        $stmt->bindValue("filtro_data_ate", $dataAte);
        if(isset($parametros[ConstanteParametros::CHAVE_RESPONSAVEL_VENDA_FUNCIONARIO])) {
            $stmt->bindValue("filtro_responsavel_venda", $parametros[ConstanteParametros::CHAVE_RESPONSAVEL_VENDA_FUNCIONARIO]);
        };
        if(isset($parametros[ConstanteParametros::CHAVE_TIPO_CONTATO])) {
            $stmt->bindValue("filtro_tipo_contato", $parametros[ConstanteParametros::CHAVE_TIPO_CONTATO]);
        };
        
        return $stmt->executeQuery()->fetchAllAssociative();
    }

    public function buscarDadosRelatorioRetencaoAlunos($parametros){
        $queryBuilder = $this->createQueryBuilder('con')
            ->select([
                'pro_pes.nome_contato as professor',
                'alu_pes.nome_contato as aluno',
                'alu.id as aluno_id',
                'tur.id as turma_id',
                'liv.id as livro_id',
                'tur.descricao as turma',
                'liv.descricao as livro',
                'cur.descricao as curso',
                'con.sequencia_contrato as sequencia_contrato',
                'con.motivo_cancelamento as motivo_cancelamento',
                'con.tipo_contrato as tipo_contrato',
                'con.situacao as situacao_contrato',
                "date_format(con.data_inicio_contrato, '%Y-%m-%d') as data_inicio_contrato",
                "date_format(con.data_termino_contrato, '%Y-%m-%d') as data_termino_contrato",
                'con_post.sequencia_contrato as sequencia_contrato_posterior',
                'con_post.motivo_cancelamento as motivo_cancelamento_posterior',
                'con_post.tipo_contrato as tipo_contrato_posterior',
                'con_post.situacao as situacao_contrato_posterior',
                "date_format(con_post.data_inicio_contrato, '%Y-%m-%d') as data_inicio_contrato_posterior",
                "date_format(con_post.data_termino_contrato, '%Y-%m-%d') as data_termino_contrato_posterior",
            ])
            ->leftJoin('con.aluno', 'alu')
            ->leftJoin('alu.pessoa', 'alu_pes')
            ->leftJoin('con.turma', 'tur')
            ->leftJoin('con.livro', 'liv')
            ->leftJoin('con.curso', 'cur')
            ->leftJoin('tur.funcionario', 'pro')
            ->leftJoin('pro.pessoa', 'pro_pes')
            ->leftJoin('App\Entity\Principal\Contrato', 'con_post', 'WITH', 'con_post.aluno = con.aluno AND con_post.sequencia_contrato = (con.sequencia_contrato + 1)')
            ->where('con.franqueada = :franqueada')
            ->setParameter('franqueada', $parametros[ConstanteParametros::CHAVE_FRANQUEADA]);

        if(isset($parametros[ConstanteParametros::CHAVE_DATA_INICIAL])) {
            $dataInicial = strtotime(str_replace("/", "-",$parametros[ConstanteParametros::CHAVE_DATA_INICIAL] . " 00:00:00"));
            $dataInicial = date('Y-m-d H:i:s', $dataInicial);
            $queryBuilder->andWhere('tur.data_inicio >= :data_inicial')
                ->setParameter('data_inicial', $dataInicial);
        }
        if(isset($parametros[ConstanteParametros::CHAVE_DATA_FINAL])) {
            $dataFinal = strtotime(str_replace("/", "-",$parametros[ConstanteParametros::CHAVE_DATA_FINAL] . " 23:59:59"));
            $dataFinal = date('Y-m-d H:i:s', $dataFinal);
            $queryBuilder->andWhere('tur.data_inicio <= :data_final')
                ->setParameter('data_final', $dataFinal);
        }
        if(isset($parametros[ConstanteParametros::CHAVE_INSTRUTOR_FLAG])) {
            $queryBuilder->andWhere('tur.funcionario = :instrutor')
                ->setParameter('instrutor', $parametros[ConstanteParametros::CHAVE_INSTRUTOR_FLAG]);
        }
        if(isset($parametros[ConstanteParametros::CHAVE_LIVRO])) {
            $queryBuilder->andWhere('liv = :livro')
                ->setParameter('livro', $parametros[ConstanteParametros::CHAVE_LIVRO]);
        }
        if(isset($parametros[ConstanteParametros::CHAVE_MODALIDADE_TURMA])) {
            $queryBuilder->andWhere('tur.modalidade_turma = :modalidade')
                ->setParameter('modalidade', $parametros[ConstanteParametros::CHAVE_MODALIDADE_TURMA]);
        }
        if(isset($parametros[ConstanteParametros::CHAVE_TURMA])) {
            $queryBuilder->andWhere('tur = :turma')
                ->setParameter('turma', $parametros[ConstanteParametros::CHAVE_TURMA]);
        }
        
        return $queryBuilder->getQuery()->getResult();
    }


   /**
     * Buscar por Chave e da o aceite
     *
     * @param string $chave
     *
     * @return boolean
     */
    public function aceitarContrato($chave)
    {
        $queryBuilder = $this->montaQueryBase();
        $queryBuilder->where("ct.chave_aceite = :chave");
        $queryBuilder->setParameter("chave", $chave);

        $a = $queryBuilder->getQuery()->getSql();
        $contrato = $queryBuilder->getQuery()->getOneOrNullResult();
        if($contrato == null){
            echo "Contrato não encontrado.";
            return false;
        }
        $contrato->setData_aceite(new \DateTime());
        try {
            $this->getEntityManager()->persist($contrato);
            $this->getEntityManager()->flush();
           
        } catch (\Exception $e) {
            return false;
        }
        

        return true;
    }
}
