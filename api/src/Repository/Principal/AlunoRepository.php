<?php

namespace App\Repository\Principal;

use App\Entity\Principal\Aluno;
use App\Entity\Principal\Interessado;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Symfony\Bridge\Doctrine\RegistryInterface;
use App\Helper\ConstanteParametros;
use App\Helper\VariaveisCompartilhadas;
use App\Helper\SituacoesSistema;
use Doctrine\Persistence\ManagerRegistry;

/**
 *
 * @method Aluno|null find($id, $lockMode = null, $lockVersion = null)
 * @method Aluno|null findOneBy(array $criteria, array $orderBy = null)
 * @method Aluno[]    findAll()
 * @method Aluno[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class AlunoRepository extends ServiceEntityRepository
{
    /**
    * @var ManagerRegistry
     */
    private ManagerRegistry $managerRegistry;


    public function __construct(RegistryInterface $registry, ManagerRegistry $managerRegistry)
    {
        $this->managerRegistry = $managerRegistry;
        parent::__construct($registry, Aluno::class);
    }

    /**
     * Query para realizar a busca de alunos
     *
     * @param boolean $apenasProximaLicao
     *
     * @return \Doctrine\ORM\QueryBuilder
     */
    private function buscarAluno($apenasProximaLicao=false)
    {
        $queryBuilder = $this->createQueryBuilder("a");
        $queryBuilder->addSelect("p, ca, cid, est, esco, rfp, rfra, rdp, rdra, ctts, cur, idi, tur, func, liv, pl, tma, lic, sf, sl, alunoAvaliacao, aAlunoAvaliacao, aaTurma, livroContrato, avnmto, avnfo, avnrmto, avnrfo, tv");
        $queryBuilder->join("a.pessoa", "p");
        $queryBuilder->join("p.franqueadas", "pessoaFranqueadas");
        $queryBuilder->leftJoin("a.tipo_visibilidade", "tv");
        $queryBuilder->leftJoin("a.contratos", "ctts");
        $queryBuilder->leftJoin("ctts.curso", "cur");
        $queryBuilder->leftJoin("ctts.turma", "tur");
        $queryBuilder->leftJoin("ctts.livro", "livroContrato");
        $queryBuilder->leftJoin("cur.idioma", "idi");
        $queryBuilder->leftJoin("tur.funcionario", "func");
        $queryBuilder->leftJoin("tur.livro", "liv");
        $queryBuilder->leftJoin("liv.planejamento_licao", "pl");
        $queryBuilder->leftJoin("tur.sala_franqueada", "sf");
        if ($apenasProximaLicao === true) {
            $queryBuilder->leftJoin("tur.turmaAulas", "tma", "WITH", "tma.finalizada = :turmaFinalizada");
            $queryBuilder->setParameter("turmaFinalizada", false);
        } else {
            $queryBuilder->leftJoin("tur.turmaAulas", "tma");
        }

        $queryBuilder->leftJoin("tma.licao", "lic");
        $queryBuilder->leftJoin('sf.sala', 'sl');
        $queryBuilder->leftJoin("a.classificacao_aluno", "ca");
        $queryBuilder->leftJoin("a.escolaridade", "esco");
        $queryBuilder->leftJoin("a.responsavel_financeiro_pessoa", "rfp");
        $queryBuilder->leftJoin("a.responsavel_financeiro_relacionamento_aluno", "rfra");
        $queryBuilder->leftJoin("a.responsavel_didatico_pessoa", "rdp");
        $queryBuilder->leftJoin("a.responsavel_didatico_relacionamento_aluno", "rdra");
        $queryBuilder->leftJoin("tur.alunoAvaliacaos", "alunoAvaliacao", "WITH", "alunoAvaliacao.livro = tur.livro");
        $queryBuilder->leftJoin("a.alunoAvaliacaos", "aAlunoAvaliacao", "WITH", "aAlunoAvaliacao.turma = ctts.turma AND aAlunoAvaliacao.livro = tur.livro");
        $queryBuilder->leftJoin("aAlunoAvaliacao.nota_mid_term_oral", "avnmto");
        $queryBuilder->leftJoin("aAlunoAvaliacao.nota_final_oral", "avnfo");
        $queryBuilder->leftJoin("aAlunoAvaliacao.nota_retake_mid_term_oral", "avnrmto");
        $queryBuilder->leftJoin("aAlunoAvaliacao.nota_retake_final_oral", "avnrfo");
        $queryBuilder->leftJoin("aAlunoAvaliacao.turma", "aaTurma");
        $queryBuilder->leftJoin("ca.franqueada", "fra");
        $queryBuilder->leftJoin("p.cidade", "cid");
        $queryBuilder->leftJoin("p.estado", "est");

        return $queryBuilder;
    }

     /**
     * Query para realizar a busca de alunos
     *
     * @param boolean $apenasProximaLicao
     *
     * @return \Doctrine\ORM\QueryBuilder
     */
    private function buscarAlunoHeader()
    {
       
        $queryBuilder = $this->createQueryBuilder("a");
        $queryBuilder->addSelect("p, ca, rfp, rdp, ctts, cur");
        $queryBuilder->join("a.pessoa", "p");
        $queryBuilder->join("p.franqueadas", "pessoaFranqueadas");
        $queryBuilder->leftJoin("a.contratos", "ctts");
        $queryBuilder->leftJoin("ctts.curso", "cur");
        $queryBuilder->leftJoin("a.classificacao_aluno", "ca");
        $queryBuilder->leftJoin("a.responsavel_financeiro_pessoa", "rfp");
        $queryBuilder->leftJoin("a.responsavel_didatico_pessoa", "rdp");
        return $queryBuilder;
    }

    /**
     * @return NULL|\App\Entity\Principal\Aluno
     */
    public function buscarPessoaPorNome($nome, $franqueadaId)
    {
        $queryBuilder = $this->createQueryBuilder("a");
        $queryBuilder->addSelect("p");
        $queryBuilder->join("a.pessoa", "p");
        $queryBuilder->join("p.franqueadas", "f");

        $queryBuilder->where("UPPER(p.nome_contato) like :nome");
        $queryBuilder->setParameter("nome", '%' . strtoupper($nome) . '%');

        $queryBuilder->andWhere("f.id = :franqueadaId");
        $queryBuilder->setParameter("franqueadaId", $franqueadaId);
        $result = $queryBuilder->getQuery()->getResult(\Doctrine\ORM\Query::HYDRATE_ARRAY);

        $pessoas = [];

        foreach ($result as $res) {
            $pessoas[] = $res["pessoa"];
        }

        return $pessoas;
    }



    /**
     * Query para realizar fitlro de franqueada
     *
     * @param \Doctrine\ORM\QueryBuilder $queryBuilder
     */
    private function filtrarFranqueada(&$queryBuilder)
    {
        $queryBuilder->where("pessoaFranqueadas = :franqueada");
        $queryBuilder->setParameter('franqueada', VariaveisCompartilhadas::$franqueadaID);
    }

    /**
     * Monta filtros data de/ate
     *
     * @param \Doctrine\ORM\QueryBuilder $queryBuilder
     * @param array $parametros
     */
    protected function montaFiltroDatas(&$queryBuilder, $parametros)
    {
        if ((isset($parametros[ConstanteParametros::CHAVE_DATA_CADASTRO_INICIAL]) === true)&&(empty($parametros[ConstanteParametros::CHAVE_DATA_CADASTRO_INICIAL]) === false)) {
            $queryBuilder->andWhere("p.data_cadastramento >= :dataCadastroDe");
            $queryBuilder->setParameter("dataCadastroDe", $parametros[ConstanteParametros::CHAVE_DATA_CADASTRO_INICIAL]);
        }

        if ((isset($parametros[ConstanteParametros::CHAVE_DATA_CADASTRO_FINAL]) === true)&&(empty($parametros[ConstanteParametros::CHAVE_DATA_CADASTRO_FINAL]) === false)) {
            $queryBuilder->andWhere("p.data_cadastramento <= :dataCadastroAte");
            $queryBuilder->setParameter("dataCadastroAte", $parametros[ConstanteParametros::CHAVE_DATA_CADASTRO_FINAL]);
        }

        if ((isset($parametros[ConstanteParametros::CHAVE_DATA_NASCIMENTO_INICIAL]) === true)&&(empty($parametros[ConstanteParametros::CHAVE_DATA_NASCIMENTO_INICIAL]) === false)) {
            $queryBuilder->andWhere("p.data_nascimento >= :dataNascDe");
            $queryBuilder->setParameter("dataNascDe", $parametros[ConstanteParametros::CHAVE_DATA_NASCIMENTO_INICIAL]);
        }

        if ((isset($parametros[ConstanteParametros::CHAVE_DATA_NASCIMENTO_FINAL]) === true)&&(empty($parametros[ConstanteParametros::CHAVE_DATA_NASCIMENTO_FINAL]) === false)) {
            $queryBuilder->andWhere("p.data_nascimento <= :dataNascAte");
            $queryBuilder->setParameter("dataNascAte", $parametros[ConstanteParametros::CHAVE_DATA_NASCIMENTO_FINAL]);
        }

    }

    /**
     * Adiciona os filtros na query
     *
     * @param array $parametros
     * @param \Doctrine\ORM\QueryBuilder $queryBuilder
     */
    private function montaFiltros($parametros, &$queryBuilder)
    {
         $queryBuilder->andWhere("a.excluido = 0 ");
         $queryBuilder->andWhere("a.situacao != :alunoSituacaoInteressao");
         $queryBuilder->setParameter('alunoSituacaoInteressao', SituacoesSistema::ALUNO_INTERESSADO);
        
        if ((isset($parametros[ConstanteParametros::CHAVE_ALUNO]) === true)&&(empty($parametros[ConstanteParametros::CHAVE_ALUNO]) === false)) {
            $queryBuilder->andWhere("a.id = :idAluno");
            $queryBuilder->setParameter("idAluno", $parametros[ConstanteParametros::CHAVE_ALUNO]);
        }

        if ((isset($parametros[ConstanteParametros::CHAVE_TELEFONE]) === true)&&(empty($parametros[ConstanteParametros::CHAVE_TELEFONE]) === false)) {
            $queryBuilder->andWhere("p.telefone_preferencial LIKE :telefonePreferencial");
            $queryBuilder->setParameter("telefonePreferencial", "%" . $parametros[ConstanteParametros::CHAVE_TELEFONE] . "%");
        }

        if ((isset($parametros[ConstanteParametros::CHAVE_SITUACAO]) === true)&&(empty($parametros[ConstanteParametros::CHAVE_SITUACAO]) === false)) {
            $queryBuilder->andWhere("a.situacao  = :alunoSituacao");
            $queryBuilder->setParameter("alunoSituacao", $parametros[ConstanteParametros::CHAVE_SITUACAO]);
        }

        if ((isset($parametros[ConstanteParametros::CHAVE_CNPJ_CPF]) === true)&&(empty($parametros[ConstanteParametros::CHAVE_CNPJ_CPF]) === false)) {
            $queryBuilder->andWhere("p.cnpj_cpf  = :cnpj_cpf");
            $queryBuilder->setParameter("cnpj_cpf", $parametros[ConstanteParametros::CHAVE_CNPJ_CPF]);
        }

        if ((isset($parametros[ConstanteParametros::CHAVE_PESSOA_SEXO]) === true)&&(empty($parametros[ConstanteParametros::CHAVE_PESSOA_SEXO]) === false)) {
            $queryBuilder->andWhere("p.sexo  = :pessoaSexo");
            $queryBuilder->setParameter("pessoaSexo", $parametros[ConstanteParametros::CHAVE_PESSOA_SEXO]);
        }

        if ((isset($parametros[ConstanteParametros::CHAVE_PESSOA_ESTADO_CIVIL]) === true)&&(empty($parametros[ConstanteParametros::CHAVE_PESSOA_ESTADO_CIVIL]) === false)) {
            $queryBuilder->andWhere("p.estado_civil  = :estadoCivil");
            $queryBuilder->setParameter("estadoCivil", $parametros[ConstanteParametros::CHAVE_PESSOA_ESTADO_CIVIL]);
        }

        if ((isset($parametros[ConstanteParametros::CHAVE_RESPONSAVEL_FINANCEIRO_PESSOA]) === true)&&(empty($parametros[ConstanteParametros::CHAVE_RESPONSAVEL_FINANCEIRO_PESSOA]) === false)) {
            $queryBuilder->andWhere("rfp.id = :responsavelFinanceiro");
            $queryBuilder->setParameter("responsavelFinanceiro", $parametros[ConstanteParametros::CHAVE_RESPONSAVEL_FINANCEIRO_PESSOA]);
        }

        if ((isset($parametros[ConstanteParametros::CHAVE_RESPONSAVEL_DIDATICO_PESSOA]) === true)&&(empty($parametros[ConstanteParametros::CHAVE_RESPONSAVEL_DIDATICO_PESSOA]) === false)) {
            $queryBuilder->andWhere("rdp.id = :responsavelDidatico");
            $queryBuilder->setParameter("responsavelDidatico", $parametros[ConstanteParametros::CHAVE_RESPONSAVEL_DIDATICO_PESSOA]);
        }

        if ((isset($parametros[ConstanteParametros::CHAVE_EMANCIPADO]) === true)&&(empty($parametros[ConstanteParametros::CHAVE_EMANCIPADO]) === false)) {
            $queryBuilder->andWhere("a.emancipado IN (:emancipado)");
            $queryBuilder->setParameter("emancipado", $parametros[ConstanteParametros::CHAVE_EMANCIPADO]);
        }

        if ((isset($parametros[ConstanteParametros::CHAVE_CLASSIFICACAO_ALUNO]) === true)&&(empty($parametros[ConstanteParametros::CHAVE_CLASSIFICACAO_ALUNO]) === false)) {
            $queryBuilder->andWhere("ca.id = :classificacaoAluno");
            $queryBuilder->setParameter("classificacaoAluno", $parametros[ConstanteParametros::CHAVE_CLASSIFICACAO_ALUNO]);
        }

        if ((isset($parametros[ConstanteParametros::CHAVE_CURSO]) === true)&&(empty($parametros[ConstanteParametros::CHAVE_CURSO]) === false)) {
            $queryBuilder->andWhere("cur.id = :cursoAluno");
            $queryBuilder->setParameter("cursoAluno", $parametros[ConstanteParametros::CHAVE_CURSO]);
        }

        $this->montaFiltroDatas($queryBuilder, $parametros);
    }

    /**
     * Filtra os alunos por pagina e numero de itens por pagina
     *
     * @param array $parametros
     * @param integer $pagina
     * @param integer $numeroItensPorPagina
     *
     * @return \App\Entity\Principal\Aluno[] Resultados em formato de array
     */
    public function filtrarAlunosPorPagina($parametros, $pagina=1, $numeroItensPorPagina=50)
    {
        $opcoes       = [];
        $queryBuilder = $this->buscarAluno();
        $this->filtrarFranqueada($queryBuilder);
        $this->montaFiltros($parametros, $queryBuilder);

        if ((isset($parametros[ConstanteParametros::CHAVE_ORDENACAO_COLUNA]) === true)&&(is_null($parametros[ConstanteParametros::CHAVE_ORDENACAO_COLUNA]) === false)) {
            $queryBuilder->orderBy($parametros[ConstanteParametros::CHAVE_ORDENACAO_COLUNA], $parametros[ConstanteParametros::CHAVE_ORDENACAO_SORT]);
            $opcoes[\Knp\Component\Pager\Paginator::SORT_FIELD_PARAMETER_NAME]     = "~";
            $opcoes[\Knp\Component\Pager\Paginator::SORT_DIRECTION_PARAMETER_NAME] = "~";
        }

        return \App\Helper\FunctionHelper::montaPaginatorPaginacao($queryBuilder, $pagina, $numeroItensPorPagina, $opcoes);
    }

      /**
     * Filtra os alunos por pagina e numero de itens por pagina
     *
     * @param array $parametros
     * @param integer $pagina
     * @param integer $numeroItensPorPagina
     *
     * @return \App\Entity\Principal\Aluno[] Resultados em formato de array
     */
    public function filtrarAlunosPorPaginaHeader($parametros, $pagina=1, $numeroItensPorPagina=50)
    {
        $opcoes       = [];
        $queryBuilder = $this->buscarAlunoHeader();

        $this->filtrarFranqueada($queryBuilder);
        $this->montaFiltros($parametros, $queryBuilder);

        if ((isset($parametros[ConstanteParametros::CHAVE_ORDENACAO_COLUNA]) === true)&&(is_null($parametros[ConstanteParametros::CHAVE_ORDENACAO_COLUNA]) === false)) {
            $queryBuilder->orderBy($parametros[ConstanteParametros::CHAVE_ORDENACAO_COLUNA], $parametros[ConstanteParametros::CHAVE_ORDENACAO_SORT]);
            $opcoes[\Knp\Component\Pager\Paginator::SORT_FIELD_PARAMETER_NAME]     = "~";
            $opcoes[\Knp\Component\Pager\Paginator::SORT_DIRECTION_PARAMETER_NAME] = "~";
        }

        return \App\Helper\FunctionHelper::montaPaginatorPaginacao($queryBuilder, $pagina, $numeroItensPorPagina, $opcoes);
    }

    /**
     * Busca o aluno pela chave primaria
     *
     * @param integer $id Chave primaria do aluno
     * @param boolean $apenasProximaLicao
     *
     * @return array|NULL
     */
    public function buscarPorId($id, $apenasProximaLicao=false)
    {
        $queryBuilder = $this->buscarAluno($apenasProximaLicao);
        // $this->filtrarFranqueada($queryBuilder);
        $queryBuilder->andWhere("a.id = :id");
        $queryBuilder->setParameter("id", $id);

        return $queryBuilder->getQuery()->getOneOrNullResult(\Doctrine\ORM\Query::HYDRATE_ARRAY);
    }

    /**
     * Busca alunos por nome
     *
     * @param string $query nome a ser buscado
     *
     * @return array|NULL
     */
    public function buscaAlunosPorNome ($query)
    {
        $queryBuilder = $this->buscarAluno();
        $queryBuilder->addSelect("it");
        $queryBuilder->leftjoin("a.interessados", "it");
        $this->filtrarFranqueada($queryBuilder);
        $queryBuilder->andWhere("a.excluido = 0");
        $queryBuilder->andWhere("p.nome_contato LIKE :nome");
        $queryBuilder->setParameter("nome", "%$query%");
        return $queryBuilder->getQuery()->getResult(\Doctrine\ORM\Query::HYDRATE_ARRAY);
    }

    /**
     * Busca alunos por nome com contrato
     *
     * @param string $query nome a ser buscado
     * @param array $parametros Lista de parametros
     *
     * @return array|NULL
     */
    public function buscaAlunosPorNomeComContrato ($query, $parametros)
    {
        $queryBuilder = $this->buscarAluno();
        $this->filtrarFranqueada($queryBuilder);
        $queryBuilder->andWhere("a.excluido = 0");
        $queryBuilder->andWhere(
            $queryBuilder->expr()->orX(
                $queryBuilder->expr()->like("p.nome_contato", ":nomeCpf"),
                $queryBuilder->expr()->like("p.cnpj_cpf", ":nomeCpf")
            )
        );
        $queryBuilder->setParameter("nomeCpf", "%$query%");
        $queryBuilder->andWhere("ctts IS NOT NULL");
        if ((bool) $parametros[ConstanteParametros::CHAVE_BUSCAR_APENAS_CONTRATO_ATIVO] === true) {
            $queryBuilder->andWhere("ctts.situacao = :situacaoContrato");
            $queryBuilder->setParameter("situacaoContrato", SituacoesSistema::SITUACAO_CONTRATO_VIGENTE);
        }

        return $queryBuilder->getQuery()->getResult(\Doctrine\ORM\Query::HYDRATE_ARRAY);
    }

        /**
     * Busca alunos por nome com contrato
     *
     * @param string $query nome a ser buscado
     * @param array $parametros Lista de parametros
     *
     * @return array|NULL
     */
    public function buscaAlunosPorNomeComContratoTodos ($query, $parametros)
    {
        $queryBuilder = $this->buscarAluno();
        $this->filtrarFranqueada($queryBuilder);
        $queryBuilder->andWhere("a.excluido = 0");
        $queryBuilder->andWhere(
            $queryBuilder->expr()->orX(
                $queryBuilder->expr()->like("p.nome_contato", ":nomeCpf"),
                $queryBuilder->expr()->like("p.cnpj_cpf", ":nomeCpf")
            )
        );
        $queryBuilder->setParameter("nomeCpf", "%$query%");
        $queryBuilder->andWhere("ctts IS NOT NULL");
        // if ((bool) $parametros[ConstanteParametros::CHAVE_BUSCAR_APENAS_CONTRATO_ATIVO] === true) {
         $queryBuilder->andWhere("ctts.situacao <> 'C'");
        //     $queryBuilder->setParameter("situacaoContrato", SituacoesSistema::SITUACAO_CONTRATO_VIGENTE);
        // }

        return $queryBuilder->getQuery()->getResult(\Doctrine\ORM\Query::HYDRATE_ARRAY);
    }

    /**
     * Busca alunos por nome ou cpf
     *
     * @param string $query nome ou cpf a ser buscado
     *
     * @return array|NULL
     */
    public function buscaAlunosPorNomeCpf ($query)
    {
        $queryBuilder = $this->buscarAluno();
        $this->filtrarFranqueada($queryBuilder);
        $queryBuilder->andWhere("a.excluido = 0");       
        $queryBuilder->andWhere(
            $queryBuilder->expr()->orX(
                $queryBuilder->expr()->like("p.nome_contato", ":nomeCpf"),
                $queryBuilder->expr()->like("p.cnpj_cpf", ":nomeCpf")
            )
        );
        $queryBuilder->andWhere("p.tipo_pessoa = 'F'");
        $queryBuilder->setParameter("nomeCpf", "%$query%");
        return $queryBuilder->getQuery()->getResult(\Doctrine\ORM\Query::HYDRATE_ARRAY);
    }

    /**
     * Busca alunos por cpf
     *
     * @param string $query cpf a ser buscado
     *
     * @return array|NULL
     */
    public function buscaAlunosPorCpf ($query)
    {
        $queryBuilder = $this->buscarAluno();
        $this->filtrarFranqueada($queryBuilder);
        $queryBuilder->andWhere("a.excluido = 0");       
        $queryBuilder->andWhere("p.cnpj_cpf LIKE :cpf");
        $queryBuilder->setParameter("cpf", "%$query%");
        return $queryBuilder->getQuery()->getResult(\Doctrine\ORM\Query::HYDRATE_ARRAY);
    }

    

     /**
     * Busca aluno por pessoa
     *
     * @param string $pessoaId
     *
     * @return mixed|\App\Entity\Principal\Aluno|NULL
     */
    public function buscaAlunoPorPessoa($pessoaId)
    {
        $queryBuilder = $this->createQueryBuilder("a");
        $queryBuilder->join("a.pessoa", "p");
        $queryBuilder->where('a.excluido = 0');       
        $queryBuilder->andWhere("p.id = :pessoaId");
        $queryBuilder->setParameter("pessoaId", $pessoaId);

       
        return $queryBuilder->getQuery()->getOneOrNullResult(\Doctrine\ORM\Query::HYDRATE_OBJECT);
    }

    /**
     * Busca todos os alunos com o titulo pendente
     *
     * @return mixed|\App\Entity\Principal\Aluno|NULL
     */
    public function buscaAlunosTitulosPendentes()
    {
        $queryBuilder = $this->createQueryBuilder("al");
        $queryBuilder->addSelect("tr");
        $queryBuilder->addSelect("p");
        $queryBuilder->leftJoin("al.pessoa", "p");
        $queryBuilder->leftJoin("al.alunoTituloRecebers", "tr");
        $queryBuilder->andWhere("tr.situacao = :situacao");
        $queryBuilder->setParameter("situacao", SituacoesSistema::SITUACAO_PENDENTE);
        return $queryBuilder->getQuery()->getResult();
    }

    /**
     * Buscar todos os alunos através da franqueada
     *
     * @param int $franqueadaId
     * @param array $listaAlunos
     *
     * @return \App\Entity\Principal\Aluno[]
     */
    public function buscarTodosAlunosORMPorFranqueada($franqueadaId, $listaAlunos=[])
    {
        $queryBuilder = $this->buscarAluno();
        $queryBuilder->leftJoin("a.alunoContaReceber", "acr");
        $queryBuilder->leftJoin("acr.tituloRecebers", "tr");
        $queryBuilder->where("pessoaFranqueadas = :franqueada");
        $queryBuilder->andWhere("a.excluido = 0");
        $queryBuilder->andWhere("ctts.situacao = :situacaoContrato");
        $queryBuilder->andWhere("tr.lembrete_envio = :lembreteBoo");
        $queryBuilder->setParameter('franqueada', $franqueadaId);
        $queryBuilder->setParameter("situacaoContrato", SituacoesSistema::SITUACAO_CONTRATO_VIGENTE);
        $queryBuilder->setParameter('lembreteBoo', false);
        if (count($listaAlunos) > 0) {
            $queryBuilder->andWhere("a.id IN (:alunosId)");
            $queryBuilder->setParameter('alunosId', $listaAlunos);
        }

        return $queryBuilder->getQuery()->getResult(\Doctrine\ORM\Query::HYDRATE_OBJECT);
    }

    /**
     * Busca o aluno por id com o contrato vigente
     *
     * @param int $id
     *
     * @return NULL|\App\Entity\Principal\Aluno
     */
    public function buscarAlunoPorIdComContratoVigente($id)
    {
        $queryBuilder = $this->buscarAluno();
        $queryBuilder->andWhere("a.id = :id");
        $queryBuilder->andWhere("a.excluido = 0");
        $queryBuilder->andWhere("ctts.situacao = :situacaoContrato");
        $queryBuilder->setParameter("id", $id);
        $queryBuilder->setParameter("situacaoContrato", SituacoesSistema::SITUACAO_CONTRATO_VIGENTE);
        return $queryBuilder->getQuery()->getOneOrNullResult();
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
        $queryBuilder = $this->createQueryBuilder("a");
        $queryBuilder->select('a.id');
        $queryBuilder->innerjoin('a.classificacao_aluno', 'ca');
        $queryBuilder->innerjoin('a.pessoa', 'p');
        $queryBuilder->leftJoin('p.estado', 'e');
        $queryBuilder->leftJoin('p.cidade', 'c');
        $queryBuilder->leftJoin('a.escolaridade', 'esc');
        $queryBuilder->leftJoin('a.responsavel_financeiro_pessoa', 'resp_fin');
        $queryBuilder->leftJoin('a.responsavel_didatico_pessoa', 'resp_did');

        $queryBuilder->where('a.excluido = 0');
        $queryBuilder->andwhere('ca.franqueada = :franqueada');
        $queryBuilder->setParameter('franqueada', VariaveisCompartilhadas::$franqueadaID);

        if (is_null($parametros[ConstanteParametros::CHAVE_ALUNO]) === false) {
            $queryBuilder->andWhere('a = :aluno');
            $queryBuilder->setParameter('aluno', $parametros[ConstanteParametros::CHAVE_ALUNO]);
        }

        // Filtra e substitui a query para passar ao Jasper
        $sql = $queryBuilder->getQuery()->getSQL();

        $sql = preg_replace('/.*WHERE\s(.*)$/', '$1', $sql);

        // Seleciona somente os wheres
        $sql = preg_replace('/a0_/', 'aluno', $sql);
        $sql = preg_replace('/c1_/', 'classificacao_aluno', $sql);
        $sql = preg_replace('/p2_/', 'pessoa', $sql);
        $sql = preg_replace('/e3_/', 'estado', $sql);
        $sql = preg_replace('/c4_/', 'cidade', $sql);
        $sql = preg_replace('/e5_/', 'escolaridade', $sql);
        $sql = preg_replace('/p6_/', 'responsavel_financeiro', $sql);
        $sql = preg_replace('/p7_/', 'responsavel_didatico', $sql);

        // Substituição de parâmetros
        $parameters = $queryBuilder->getParameters();
        foreach ($parameters as $parameter) {
            $param = $parameter->getValue();
            $sql   = preg_replace('/\?/', "'$param'", $sql, 1);
        }

        return $sql;
    }

    /**
     * Busca as avaliacoes do aluno por contrato
     *
     * @param int $contratoId
     * @param array $parametros Parametros da busca
     *
     * @return array|NULL
     */
    public function buscaAvaliacoesPorContratoId($contratoId, $parametros=[])
    {
        $queryBuilder = $this->createQueryBuilder("al");
        $queryBuilder->addSelect("alunoAvaliacao");
        $queryBuilder->addSelect("alunoAvaliacaoConceitual");
        $queryBuilder->addSelect("anl1");
        $queryBuilder->addSelect("ans1");
        $queryBuilder->addSelect("anw1");
        $queryBuilder->addSelect("anl2");
        $queryBuilder->addSelect("ans2");
        $queryBuilder->addSelect("anw2");
        $queryBuilder->addSelect("nmto");
        $queryBuilder->addSelect("nfo");
        $queryBuilder->addSelect("nrmto");
        $queryBuilder->addSelect("nrfo");
        $queryBuilder->addSelect("pp");
        $queryBuilder->addSelect("ct");
        $queryBuilder->leftJoin("al.contratos", "ct");

        $condicionalAlunoAvaliacao           = "alunoAvaliacao.turma IS NULL AND alunoAvaliacao.personal = '1'";
        $condicionalAlunoAvaliacaoConceitual = "alunoAvaliacaoConceitual.turma IS NULL AND alunoAvaliacaoConceitual.personal = '1'";
        if (isset($parametros[ConstanteParametros::CHAVE_LIVRO]) === true && is_null($parametros[ConstanteParametros::CHAVE_LIVRO]) === false) {
            $condicionalAlunoAvaliacao           .= " AND alunoAvaliacao.livro = :livroId";
            $condicionalAlunoAvaliacaoConceitual .= " AND alunoAvaliacaoConceitual.livro = :livroId";
            $queryBuilder->setParameter("livroId", $parametros[ConstanteParametros::CHAVE_LIVRO]);
        }

        $queryBuilder->leftJoin("al.alunoAvaliacaos", "alunoAvaliacao", "WITH", $condicionalAlunoAvaliacao);
        $queryBuilder->leftJoin("al.alunoAvaliacaoConceituals", "alunoAvaliacaoConceitual", "WITH", $condicionalAlunoAvaliacaoConceitual);

        $queryBuilder->leftJoin("alunoAvaliacaoConceitual.nota_listening_1", "anl1");
        $queryBuilder->leftJoin("alunoAvaliacaoConceitual.nota_speaking_1", "ans1");
        $queryBuilder->leftJoin("alunoAvaliacaoConceitual.nota_writing_1", "anw1");
        $queryBuilder->leftJoin("alunoAvaliacaoConceitual.nota_listening_2", "anl2");
        $queryBuilder->leftJoin("alunoAvaliacaoConceitual.nota_speaking_2", "ans2");
        $queryBuilder->leftJoin("alunoAvaliacaoConceitual.nota_writing_2", "anw2");
        $queryBuilder->innerJoin("ct.franqueada", "fran");
        $queryBuilder->leftJoin("alunoAvaliacao.nota_mid_term_oral", "nmto");
        $queryBuilder->leftJoin("alunoAvaliacao.nota_final_oral", "nfo");
        $queryBuilder->leftJoin("alunoAvaliacao.nota_retake_mid_term_oral", "nrmto");
        $queryBuilder->leftJoin("alunoAvaliacao.nota_retake_final_oral", "nrfo");
        $queryBuilder->innerJoin("al.pessoa", "pp");
        $queryBuilder->andWhere("fran.id = :franqueadaId");
        $queryBuilder->andWhere("ct.id = :contratoId");
        $queryBuilder->setParameter("franqueadaId", VariaveisCompartilhadas::$franqueadaID);
        $queryBuilder->setParameter("contratoId", $contratoId);

        return \App\Helper\FunctionHelper::retornaArrayNull($queryBuilder, true);
    }


    /**
     * Busca as avaliacoes por turma de acordo com os parametros
     *
     * @param int $turmaId
     * @param array $parametros Parametros da busca
     *
     * @return array|NULL
     */
    public function listarAvaliacoesPorTurma($turmaId, $parametros)
    {
        $queryBuilder = $this->createQueryBuilder("al");
        $queryBuilder->addSelect("alunoAvaliacao");
        $queryBuilder->addSelect("alunoAvaliacaoConceitual");
        $queryBuilder->addSelect("anl1");
        $queryBuilder->addSelect("ans1");
        $queryBuilder->addSelect("anw1");
        $queryBuilder->addSelect("anl2");
        $queryBuilder->addSelect("ans2");
        $queryBuilder->addSelect("anw2");
        $queryBuilder->addSelect("nmto");
        $queryBuilder->addSelect("nfo");
        $queryBuilder->addSelect("nrmto");
        $queryBuilder->addSelect("nrfo");
        $queryBuilder->addSelect("pp");
        $queryBuilder->addSelect("ct");
        $queryBuilder->join("al.pessoa", "pp");
        $queryBuilder->join("al.contratos", "ct");
        $queryBuilder->join("ct.franqueada", "fran");
        $queryBuilder->join("ct.turma", "ttm");

        $withAlunoAvaliacao           = "alunoAvaliacao.turma IS NOT NULL AND alunoAvaliacao.personal = '0'";
        $withAlunoAvaliacaoConceitual = "alunoAvaliacaoConceitual.turma IS NOT NULL AND alunoAvaliacaoConceitual.personal = '0'";
        if (isset($parametros[ConstanteParametros::CHAVE_LIVRO]) === true && is_null($parametros[ConstanteParametros::CHAVE_LIVRO]) === false) {
            $withAlunoAvaliacao           .= " AND alunoAvaliacao.livro = :livroId AND alunoAvaliacao.turma = :turmaId ";
            $withAlunoAvaliacaoConceitual .= " AND alunoAvaliacaoConceitual.livro = :livroId AND alunoAvaliacaoConceitual.turma = :turmaId ";
            $queryBuilder->setParameter("livroId", $parametros[ConstanteParametros::CHAVE_LIVRO]);
        }

        $queryBuilder->leftJoin("al.alunoAvaliacaos", "alunoAvaliacao", "WITH", $withAlunoAvaliacao);
        $queryBuilder->leftJoin("al.alunoAvaliacaoConceituals", "alunoAvaliacaoConceitual", "WITH", $withAlunoAvaliacaoConceitual);

        $queryBuilder->leftJoin("alunoAvaliacaoConceitual.nota_listening_1", "anl1");
        $queryBuilder->leftJoin("alunoAvaliacaoConceitual.nota_speaking_1", "ans1");
        $queryBuilder->leftJoin("alunoAvaliacaoConceitual.nota_writing_1", "anw1");
        $queryBuilder->leftJoin("alunoAvaliacaoConceitual.nota_listening_2", "anl2");
        $queryBuilder->leftJoin("alunoAvaliacaoConceitual.nota_speaking_2", "ans2");
        $queryBuilder->leftJoin("alunoAvaliacaoConceitual.nota_writing_2", "anw2");
        $queryBuilder->leftJoin("alunoAvaliacao.nota_mid_term_oral", "nmto");
        $queryBuilder->leftJoin("alunoAvaliacao.nota_final_oral", "nfo");
        $queryBuilder->leftJoin("alunoAvaliacao.nota_retake_mid_term_oral", "nrmto");
        $queryBuilder->leftJoin("alunoAvaliacao.nota_retake_final_oral", "nrfo");
        $queryBuilder->where("fran.id = :franqueadaId");
        $queryBuilder->andWhere("ttm.id = :turmaId");
        $queryBuilder->andWhere("ct.situacao in ('V', 'E', 'R')");
        $queryBuilder->setParameter("franqueadaId", VariaveisCompartilhadas::$franqueadaID);
        $queryBuilder->setParameter("turmaId", $turmaId);

 
        return \App\Helper\FunctionHelper::retornaArrayNull($queryBuilder);
    }

    /**
     * Busca os dados do relatório de titulos
     *
     * @param string $mensagem
     * @param array $parametros
     *
     * @return array
     */
    public function buscarDadosRelatorioSituacaoAlunos(&$mensagem, $parametros)
    {
        $queryBuilder = $this->createQueryBuilder("a");
        $queryBuilder->select(
            [
                "p.id as id", "p.nome_contato as nome",
                "p.email_preferencial as email","p.telefone_preferencial as fone",
                "(CASE  WHEN a.situacao = 'ATI' THEN 'Ativo'
                        WHEN a.situacao = 'INA' THEN 'Inativo'
                        WHEN a.situacao = 'TRA' THEN 'Trancado'
                        ELSE ''
                        END) as situacao",
            ]
        );
        $queryBuilder->join('a.pessoa', 'p');
        $queryBuilder->join("p.franqueadas", "pessoa_franqueada");
        $queryBuilder->where("a.situacao <> 'INT'");

        $queryBuilder->andWhere('pessoa_franqueada = :franqueada');
        $queryBuilder->setParameter('franqueada', VariaveisCompartilhadas::$franqueadaID);

        if (isset($parametros[ConstanteParametros::CHAVE_SITUACAO]) === true && empty($parametros[ConstanteParametros::CHAVE_SITUACAO]) === false) {
            $queryBuilder->andWhere("a.situacao in(:situacoes)");
            $queryBuilder->setParameter('situacoes', $parametros[ConstanteParametros::CHAVE_SITUACAO]);
        }

        return $queryBuilder->getQuery()->getResult(\Doctrine\ORM\Query::HYDRATE_ARRAY);
    }

    public function buscarDadosRelatorioDescontos($parametros = null) {

        // Caso não seja enviado o semestre da pesquisa, é configurado para buscar pelo id 9 = 2023/01
        $semestreId = $parametros['semestre'] ? intval($parametros['semestre']) : 9;

        $compararSemestres = "";
        $selectSemestre = "";
        $where = "";

        if(isset($parametros['compararSemestre']) && $parametros['compararSemestre']) {
            $selectSemestre = ", sub.desconto as sub_desconto, sub.sub_semestre as sub_semestre, sub.sub_contrato_id as sub_contrato_id";
            $compararSemestres = "
            LEFT JOIN (
                SELECT
                    aluno.id as aid,
                    contrato.id as sub_contrato_id,
                    ( ( item_conta_receber.valor_desconto / item_conta_receber.valor_item ) * 100 ) as desconto,
                    semestre.descricao as sub_semestre
                FROM aluno
                    JOIN contrato
                        ON contrato.aluno_id = aluno.id
                    JOIN semestre
                        ON semestre.id = contrato.semestre_id
                    JOIN conta_receber
                        ON conta_receber.contrato_id = contrato.id
                    JOIN item_conta_receber
                        ON item_conta_receber.conta_receber_id = conta_receber.id
                WHERE
                    contrato.situacao NOT IN('C','T') AND
                    contrato.excluido = 0 AND
                    item_conta_receber.plano_conta_id = 41 AND
                    semestre.id = ". ($semestreId - 1) ."
            ) as sub
                    ON sub.aid = a1_.id
            ";
        }

        if(isset($parametros['franqueada'])){
            $where .= ' AND c3_.franqueada_id = ' . $parametros['franqueada'];
            $where .= ' AND pf.franqueada_id = ' . $parametros['franqueada'];
        }
        if(isset($parametros['formaPagamento'])){
            $where .= ' AND f5_.id = ' . $parametros['formaPagamento'];
        }
        if(isset($parametros['modalidade'])) {
            $where .= ' AND c3_.bolsista IN ("' . implode('","', explode(',', $parametros['modalidade'])) . '")';
        }
        if(isset($parametros['situacao'])) {
            $where .= ' AND a1_.situacao IN ("' . implode('","', explode(',', $parametros['situacao'])) . '")';
        }

        $sql = "
        SELECT
            a1_.id                                              AS aluno_id,
            p0_.nome_contato                                    AS nome,
            ( CASE
                WHEN a1_.situacao = 'ATI' THEN 'Ativo'
                WHEN a1_.situacao = 'INA' THEN 'Inativo'
                WHEN a1_.situacao = 'TRA' THEN 'Trancado'
                ELSE '' END
            )                                                   AS situacao,
            ( CASE
                WHEN c3_.bolsista = 0 THEN 'Regular'
                WHEN c3_.bolsista = 1 THEN 'Bolsista'
                ELSE '' END
            )                                                   AS modalidade,
            s2_.descricao                                       AS semestre,
            c3_.id                                              AS contrato_id,
            i4_.numero_sequencia                                AS sequencia,
            i4_.valor                                           AS valor_final,
            i4_.valor_item                                      AS valor_original,
            f5_.descricao                                       AS forma_pagamento,
            ( ( i4_.valor_desconto / i4_.valor_item ) * 100 )   AS desconto
            " . $selectSemestre . "
        FROM   aluno a1_
        INNER JOIN pessoa p0_
                ON a1_.pessoa_id = p0_.id
        INNER JOIN pessoa_franqueada as pf
                ON pf.pessoa_id = p0_.id
        INNER JOIN contrato c3_
                ON a1_.id = c3_.aluno_id
        INNER JOIN semestre s2_
                ON c3_.semestre_id = s2_.id
        INNER JOIN conta_receber c7_
                ON c3_.id = c7_.contrato_id
        INNER JOIN item_conta_receber i4_
                ON c7_.id = i4_.conta_receber_id
        INNER JOIN forma_pagamento f5_
                ON i4_.forma_pagamento_id = f5_.id
        INNER JOIN item i6_
                ON i4_.item_id = i6_.id
            ". $compararSemestres ."
        WHERE  a1_.excluido = 0
            AND c3_.situacao <> 'C'
            AND c3_.situacao <> 'T'
            AND c3_.excluido = 0
            AND i4_.plano_conta_id = 41
            AND ( ( i4_.valor_desconto / i4_.valor_item ) * 100 ) > 0
            AND s2_.id = ". $semestreId . $where . ";";

        return $this->managerRegistry->getConnection()->fetchAllAssociative($sql);
    }

    public function buscarDadosRelatorioMatriculaRenovar($parametros = null) {
        
        $where = '';
        $where .= isset($parametros['franqueada']) ? ' AND contrato.franqueada_id = ' . $parametros['franqueada'] : '';

        $where .= isset($parametros['tipo_turma']) ? ' AND modalidade_turma.tipo IN ("' . implode('", "' ,$parametros['tipo_turma']) . '")' : '';

        if(isset($parametros['orderBy'])) {
            $where .= ' ORDER BY ' . $parametros['orderBy'];
            $where .= ' ' . isset($parametros['orderDesc']) ? $parametros['orderDesc'] : 'ASC';
        }

        $sql = '
        SELECT
            CONCAT(aluno.id, "/", contrato.sequencia_contrato) as contrato,
            DATE_FORMAT(contrato.data_contrato, "%Y/%m/%d") as data_contrato,
            DATE_FORMAT(contrato.data_inicio_contrato, "%Y/%m/%d") as data_inicio_contrato,
            DATE_FORMAT(contrato.data_termino_contrato, "%Y/%m/%d") as data_termino_contrato,
            pessoa.nome_contato,
            pessoa.telefone_preferencial,
            pessoa.email_preferencial as email,
            turma.descricao as turma
        FROM
            contrato
        LEFT JOIN
            aluno ON contrato.aluno_id = aluno.id
        LEFT JOIN
            pessoa ON aluno.pessoa_id = pessoa.id
        LEFT JOIN
            turma ON contrato.turma_id = turma.id
        LEFT JOIN
            modalidade_turma ON contrato.modalidade_turma_id = modalidade_turma.id
        LEFT JOIN
            (
                SELECT
                    ct.id,
                    ct.aluno_id,
                    ct.semestre_id
                FROM
                    contrato as ct
                WHERE
                    ct.situacao LIKE "V"
                    AND
                    ct.tipo_contrato LIKE "R"
            ) AS renovado
            ON
                renovado.aluno_id = contrato.aluno_id
                AND
                renovado.semestre_id = (contrato.semestre_id + 1)
        WHERE
            data_termino_contrato >= current_date()
            AND
            data_termino_contrato < CAST(current_date() + INTERVAL 2 MONTH AS DATE)
            AND
            renovado.id IS NULL
        ' . $where;
        return $this->managerRegistry->getConnection()->fetchAllAssociative($sql);
    }

    public function buscarDadosRelatorioCompromissoAprendizado($parametros) {
        $sql = <<< SQL
            SELECT 
                alu.id AS aluno_id,
                pes.nome_contato AS aluno,
                pes.telefone_preferencial AS contato,
                alu.id AS aluno_id,
                cur.descricao AS curso,
                tur.descricao AS turma,
                pesFun.nome_contato AS professor,
                liv.descricao AS livro,
                DATE_FORMAT(con.data_inicio_contrato, '%Y-%m-%d') AS data_inicio_contrato,
                aluAva.nota_retake_mid_term_escrita,
                aluAva.nota_mid_term_composition,
                aluAva.nota_retake_final_escrita,
                aluAva.nota_final_composition,
                aluAva.nota_mid_term_escrita,
                aluAva.nota_final_escrita,
                aluAva.nota_mid_term_test,
                aluAva.nota_final_test,
                Count(
                    CASE WHEN aluDia.atividade_ca = 'E' THEN 1 ELSE null END
                ) AS atividade_ca_entregue,
                Count(
                    CASE WHEN aluDia.atividade_ca = 'NE' THEN 1 ELSE null END
                ) AS atividade_ca_nao_entregue,
                Count(
                    CASE WHEN aluDia.atividade_ca = 'EA' THEN 1 ELSE null END
                ) AS atividade_ca_atrasada,
                Count(
                    CASE WHEN aluDia.atividade_ce = 'E' THEN 1 ELSE null END
                ) AS atividade_ce_entregue,
                Count(
                    CASE WHEN aluDia.atividade_ce = 'NE' THEN 1 ELSE null END
                ) AS atividade_ce_nao_entregue,
                Count(
                    CASE WHEN aluDia.atividade_ce = 'EA' THEN 1 ELSE null END
                ) AS atividade_ce_atrasada,
                Count(
                    CASE WHEN aluDia.presenca = 'P' THEN 1 ELSE null END
                ) AS presencas, 
                Count(
                    CASE WHEN aluDia.presenca = 'F' THEN 1 ELSE null END
                ) AS faltas, 
                Count(
                    CASE WHEN aluDia.presenca = 'R' THEN 1 ELSE null END
                ) AS reposicoes,
                Count(aluDia.id) AS aulas
            FROM
                aluno alu
            INNER JOIN contrato con ON alu.id = con.aluno_id
            INNER JOIN pessoa pes ON alu.pessoa_id = pes.id
            INNER JOIN pessoa_franqueada pesFra ON pes.id = pesFra.pessoa_id
            INNER JOIN franqueada fra ON fra.id = pesFra.franqueada_id
            INNER JOIN curso cur ON con.curso_id = cur.id
            INNER JOIN turma tur ON con.turma_id = tur.id
            INNER JOIN livro liv ON tur.livro_id = liv.id
            LEFT JOIN funcionario fun ON tur.funcionario_id = fun.id
            LEFT JOIN pessoa pesFun ON pesFun.id = fun.pessoa_id
            LEFT JOIN aluno_avaliacao aluAva ON aluAva.aluno_id = alu.id
                AND aluAva.turma_id = tur.id
            LEFT JOIN turma_aula turAul ON tur.id = turAul.turma_id
            LEFT JOIN aluno_diario aluDia ON turAul.id = aluDia.turma_aula_id
                AND aluDia.aluno_id = alu.id
                AND liv.id = aluDia.livro_id
            WHERE
                pes.excluido = 0
                AND con.excluido = 0
                AND con.situacao = 'V'
                AND fra.id = :franqueada
        SQL;

        $groupSql = <<< SQL
            GROUP BY
                tur.id, alu.id, liv.id
        SQL;

        $params['franqueada'] = $parametros[ConstanteParametros::CHAVE_FRANQUEADA];

        if(isset($parametros[ConstanteParametros::CHAVE_ALUNO])) {
            $params['aluno_param'] = $parametros[ConstanteParametros::CHAVE_ALUNO];
            $sql .= ' AND alu.id = :aluno_param ';
        }
        if(isset($parametros[ConstanteParametros::CHAVE_CURSO])) {
            $params['curso_param'] = $parametros[ConstanteParametros::CHAVE_CURSO];
            $sql .= ' AND con.curso_id = :curso_param ';
        }
        if(isset($parametros[ConstanteParametros::CHAVE_SEMESTRE])) {
            $params['semestre_param'] = $parametros[ConstanteParametros::CHAVE_SEMESTRE];
            $sql .= ' AND con.semestre_id = :semestre_param ';
        }
        if(isset($parametros[ConstanteParametros::CHAVE_PROFESSOR])) {
            $params['professor_param'] = $parametros[ConstanteParametros::CHAVE_PROFESSOR];
            $sql .= ' AND tur.funcionario_id = :professor_param ';
        }
        if(isset($parametros[ConstanteParametros::CHAVE_DATA_INICIAL])) {
            $dataInicial = strtotime(str_replace("/", "-",$parametros[ConstanteParametros::CHAVE_DATA_INICIAL] . " 00:00:00"));
            $params['data_inicial_param'] = date('Y-m-d H:i:s', $dataInicial);
            $sql .= ' AND con.data_inicio_contrato >= :data_inicial_param ';
        }
        if(isset($parametros[ConstanteParametros::CHAVE_DATA_FINAL])) {
            $dataFinal = strtotime(str_replace("/", "-",$parametros[ConstanteParametros::CHAVE_DATA_FINAL] . " 23:59:59"));
            $params['data_final_param'] = date('Y-m-d H:i:s', $dataFinal);
            $sql .= ' AND con.data_inicio_contrato <= :data_final_param ';
        }

        $entityManager = $this->getEntityManager();

        $statement = $entityManager->getConnection()->prepare($sql . $groupSql);
        foreach($params as $key => $value) {
            $statement->bindValue($key, $value);
        }
        return $statement->executeQuery()->fetchAllAssociative();
    }
}
