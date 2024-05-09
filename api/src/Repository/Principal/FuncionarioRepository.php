<?php

namespace App\Repository\Principal;

use App\Entity\Principal\Funcionario;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Symfony\Bridge\Doctrine\RegistryInterface;
use App\Helper\ConstanteParametros;
use App\Helper\VariaveisCompartilhadas;
use App\Entity\Principal\Conta;
use App\Helper\FunctionHelper;
use App\Helper\SituacoesSistema;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method Funcionario|null find($id, $lockMode = null, $lockVersion = null)
 * @method Funcionario|null findOneBy(array $criteria, array $orderBy = null)
 * @method Funcionario[]    findAll()
 * @method Funcionario[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class FuncionarioRepository extends ServiceEntityRepository
{
 /**
     * @var ManagerRegistry
     */
    private ManagerRegistry $managerRegistry;

    public function __construct(RegistryInterface $registry, ManagerRegistry $managerRegistry)
    {
        parent::__construct($registry, Funcionario::class);
        $this->managerRegistry = $managerRegistry;
    }

    /**
     * Monta a query base para Funcionario
     *
     * @return \Doctrine\ORM\QueryBuilder
     */
    protected function montaQueryBase()
    {
        $queryBuilder = $this->createQueryBuilder("func");
        $queryBuilder->addSelect("pes");
        $queryBuilder->addSelect("fran");
        $queryBuilder->addSelect("carg");
        $queryBuilder->addSelect("usu");
        $queryBuilder->addSelect("nivl");
        $queryBuilder->addSelect("banco");
        $queryBuilder->addSelect("gmf");
        $queryBuilder->addSelect("fvh");
        $queryBuilder->addSelect("cag");
        $queryBuilder->addSelect("vh");
        $queryBuilder->addSelect("vhnl");
        $queryBuilder->addSelect("est");
        $queryBuilder->addSelect("cid");
        $queryBuilder->addSelect("fdisp");
        $queryBuilder->join("func.franqueada", "fran");
        $queryBuilder->leftJoin("func.pessoa", "pes");
        $queryBuilder->leftJoin("func.cargo", "carg");
        $queryBuilder->leftJoin("func.usuario", "usu");
        $queryBuilder->leftJoin("func.nivel_instrutor", "nivl");
        $queryBuilder->leftJoin("func.banco", "banco");
        $queryBuilder->leftJoin("func.gestor_comercial_funcionario", "gmf");
        $queryBuilder->leftJoin("func.funcionarioValorHoras", "fvh");
        $queryBuilder->leftJoin("func.cargo", "cag");
        $queryBuilder->leftJoin("fvh.valor_hora", "vh");
        $queryBuilder->leftJoin("vh.nivel_instrutor", "vhnl");
        $queryBuilder->leftJoin("pes.estado", "est");
        $queryBuilder->leftJoin("pes.cidade", "cid");
        $queryBuilder->leftJoin("func.disponibilidades", "fdisp");

        return $queryBuilder;
    }

    /**
     * Query para realizar fitlro de franqueada
     *
     * @param \Doctrine\ORM\QueryBuilder $queryBuilder
     * @param integer $franqueadaId
     */
    private function filtrarFranqueada(&$queryBuilder, $franqueadaId=null)
    {
        if (is_null($franqueadaId) === true) {
            $franqueadaId = VariaveisCompartilhadas::$franqueadaID;
        }

        $queryBuilder->where('fran = :franqueada');
        $queryBuilder->setParameter('franqueada', $franqueadaId);
    }

    /**
     * Realiza o filtro de flags por funcionario
     *
     * @param \Doctrine\ORM\QueryBuilder $queryBuilder
     * @param array $parametros
     * @param array $extraParam
     *
     * @todo Terminar de forma dinamica a implementacao do $extraParam a ideia é<br>
     * extraParam = ["nome_campo_entidade" => "valor"]<br>
     * depois vier aqui na função e realizar o if
     */
    private function filtrarFlagsFuncionario(&$queryBuilder, $parametros, $extraParam=[])
    {
        if ((isset($parametros[ConstanteParametros::CHAVE_CONSULTOR_OU_GESTOR_COMERCIAL]) === true) && (is_null($parametros[ConstanteParametros::CHAVE_CONSULTOR_OU_GESTOR_COMERCIAL]) === false) && ($parametros[ConstanteParametros::CHAVE_CONSULTOR_OU_GESTOR_COMERCIAL] === '1')) {
            $queryBuilder->andWhere(
                $queryBuilder->expr()->orX(
                    $queryBuilder->expr()->eq('func.gestor_comercial', 1),
                    $queryBuilder->expr()->eq('func.consultor', 1)
                )
            );
        } else if ((isset($parametros[ConstanteParametros::CHAVE_INSTRUTOR_OU_COORDENADOR_PEDAGOGICO]) === true) && (is_null($parametros[ConstanteParametros::CHAVE_INSTRUTOR_OU_COORDENADOR_PEDAGOGICO]) === false) && ($parametros[ConstanteParametros::CHAVE_INSTRUTOR_OU_COORDENADOR_PEDAGOGICO] === '1')) {
            $queryBuilder->andWhere(
                $queryBuilder->expr()->orX(
                    $queryBuilder->expr()->eq('func.coordenador_pedagogico', 1),
                    $queryBuilder->expr()->eq('func.instrutor', 1),
                    $queryBuilder->expr()->eq('func.instrutor_personal', 1)
                )
            );
        } else {
            if ((isset($parametros[ConstanteParametros::CHAVE_GESTOR_COMERCIAL_FLAG]) === true) && (is_null($parametros[ConstanteParametros::CHAVE_GESTOR_COMERCIAL_FLAG]) === false)) {
                $queryBuilder->andWhere('func.gestor_comercial = :gestor_comercial');
                $queryBuilder->setParameter('gestor_comercial', $parametros[ConstanteParametros::CHAVE_GESTOR_COMERCIAL_FLAG] !== '0');
            }

            if ((isset($parametros[ConstanteParametros::CHAVE_CONSULTOR]) === true) && (is_null($parametros[ConstanteParametros::CHAVE_CONSULTOR]) === false)) {
                $queryBuilder->andWhere('func.consultor = :consultor');
                $queryBuilder->setParameter('consultor', $parametros[ConstanteParametros::CHAVE_CONSULTOR] !== '0');
            }

            if ((isset($parametros[ConstanteParametros::CHAVE_COORDENADOR_PEDAGOGICO]) === true) && (is_null($parametros[ConstanteParametros::CHAVE_COORDENADOR_PEDAGOGICO]) === false)) {
                $queryBuilder->andWhere('func.coordenador_pedagogico = :coordenador_pedagogico');
                $queryBuilder->setParameter('coordenador_pedagogico', $parametros[ConstanteParametros::CHAVE_COORDENADOR_PEDAGOGICO] !== '0');
            }
        }//end if

        if ((isset($parametros[ConstanteParametros::CHAVE_ATENDENTE_FLAG]) === true) && (is_null($parametros[ConstanteParametros::CHAVE_ATENDENTE_FLAG]) === false)) {
            $queryBuilder->andWhere('func.atendente = :atendente');
            $queryBuilder->setParameter('atendente', $parametros[ConstanteParametros::CHAVE_ATENDENTE_FLAG] !== '0');
        }

        if ((isset($parametros[ConstanteParametros::CHAVE_INSTRUTOR_FLAG]) === true) && (is_null($parametros[ConstanteParametros::CHAVE_INSTRUTOR_FLAG]) === false)) {
            $queryBuilder->andWhere('func.instrutor = :instrutor');
            $queryBuilder->setParameter('instrutor', $parametros[ConstanteParametros::CHAVE_INSTRUTOR_FLAG] !== '0');
        }

        if ((isset($parametros[ConstanteParametros::CHAVE_INSTRUTOR_PERSONAL_FLAG]) === true) && (is_null($parametros[ConstanteParametros::CHAVE_INSTRUTOR_PERSONAL_FLAG]) === false)) {
            $queryBuilder->andWhere('func.instrutor_personal = :instrutor_personal');
            $queryBuilder->setParameter('instrutor_personal', $parametros[ConstanteParametros::CHAVE_INSTRUTOR_PERSONAL_FLAG] !== '0');
        }
    }

    /**
     * Filtra o Funcionario por pagina
     *
     * @param array $parametros
     * @param number $pagina
     * @param number $numeroItensPorPagina
     *
     * @return \Knp\Component\Pager\Pagination\SlidingPagination
     */
    public function filtrarFuncionarioPorPagina($parametros, $pagina=1, $numeroItensPorPagina=50)
    {
        $opcoes       = [];
        $queryBuilder = $this->montaQueryBase();

        $franqueadaId = null;
        if (is_null($parametros[ConstanteParametros::CHAVE_FRANQUEADA_PERSONALIZADA]) === false && empty($parametros[ConstanteParametros::CHAVE_FRANQUEADA_PERSONALIZADA]) === false) {
            $franqueadaId = $parametros[ConstanteParametros::CHAVE_FRANQUEADA_PERSONALIZADA];
        }

        $this->filtrarFranqueada($queryBuilder, $franqueadaId);
        $this->filtrarFlagsFuncionario($queryBuilder, $parametros);

        if ((bool) $parametros[ConstanteParametros::CHAVE_APENAS_FUNCIONARIOS_ATIVOS] === true) {
            $queryBuilder->andWhere('func.data_demissao IS NULL');
        }

        if (is_null($parametros[ConstanteParametros::CHAVE_CARGO]) === false) {
            $queryBuilder->andWhere('carg = :cargo');
            $queryBuilder->setParameter('cargo', $parametros[ConstanteParametros::CHAVE_CARGO]);
        }

        if (is_null($parametros[ConstanteParametros::CHAVE_NIVEL_INSTRUTOR]) === false) {
            $queryBuilder->andWhere('nivl = :nivel_instrutor');
            $queryBuilder->setParameter('nivel_instrutor', $parametros[ConstanteParametros::CHAVE_NIVEL_INSTRUTOR]);
        }

        if (is_null($parametros[ConstanteParametros::CHAVE_TIPO_PAGAMENTO]) === false) {
            $queryBuilder->andWhere('func.tipo_pagamento IN (:tipo_pagamento)');
            $queryBuilder->setParameter('tipo_pagamento', explode(',', $parametros[ConstanteParametros::CHAVE_TIPO_PAGAMENTO]));
        }

        if (is_null($parametros[ConstanteParametros::CHAVE_CNPJ_CPF]) === false) {
            $queryBuilder->andWhere('pes.cnpj_cpf = :cnpj_cpf');
            $cpfCnpj = preg_replace('/[^\d]/', '', $parametros[ConstanteParametros::CHAVE_CNPJ_CPF]);
            $queryBuilder->setParameter('cnpj_cpf', $cpfCnpj);
        }

        if (is_null($parametros[ConstanteParametros::CHAVE_FUNCIONARIO]) === false) {
            $queryBuilder->andWhere('func.id = :funcionario');
            $queryBuilder->setParameter('funcionario', $parametros[ConstanteParametros::CHAVE_FUNCIONARIO]);
        }

        if (is_null($parametros[ConstanteParametros::CHAVE_APELIDO]) === false) {
            $queryBuilder->andWhere('func.apelido LIKE :apelido');
            $apelido = $parametros[ConstanteParametros::CHAVE_APELIDO];
            $queryBuilder->setParameter('apelido', "%$apelido%");
        }

        if (is_null($parametros[ConstanteParametros::CHAVE_USUARIO_EMAIL]) === false) {
            $queryBuilder->andWhere('usu.email LIKE :email_usuario');
            $email = $parametros[ConstanteParametros::CHAVE_USUARIO_EMAIL];
            $queryBuilder->setParameter('email_usuario', "%$email%");
        }

        if (is_null($parametros[ConstanteParametros::CHAVE_DATA_ADMISSAO]) === false) {
            $dataAdmissao        = FunctionHelper::formataCampoDateTimeJS($parametros[ConstanteParametros::CHAVE_DATA_ADMISSAO]);
            $dataAdmissaoInicial = (clone $dataAdmissao)->setTime(0, 0, 0);
            $dataAdmissaoFinal   = (clone $dataAdmissao)->setTime(23, 59, 59);

            $queryBuilder->andWhere('func.data_admissao >= :data_admissao_inicial');
            $queryBuilder->andWhere('func.data_admissao <= :data_admissao_final');
            $queryBuilder->setParameter('data_admissao_inicial', $dataAdmissaoInicial);
            $queryBuilder->setParameter('data_admissao_final', $dataAdmissaoFinal);
        }

        if (is_null($parametros[ConstanteParametros::CHAVE_DATA_DEMISSAO]) === false) {
            $dataDemissao        = FunctionHelper::formataCampoDateTimeJS($parametros[ConstanteParametros::CHAVE_DATA_DEMISSAO]);
            $dataDemissaoInicial = (clone $dataDemissao)->setTime(0, 0, 0);
            $dataDemissaoFinal   = (clone $dataDemissao)->setTime(23, 59, 59);

            $queryBuilder->andWhere('func.data_demissao >= :data_demissao_inicial');
            $queryBuilder->andWhere('func.data_demissao <= :data_demissao_final');
            $queryBuilder->setParameter('data_demissao_inicial', $dataDemissaoInicial);
            $queryBuilder->setParameter('data_demissao_final', $dataDemissaoFinal);
        }

        if ((isset($parametros[ConstanteParametros::CHAVE_ORDENACAO_COLUNA]) === true)&&(is_null($parametros[ConstanteParametros::CHAVE_ORDENACAO_COLUNA]) === false)) {
            $queryBuilder->orderBy($parametros[ConstanteParametros::CHAVE_ORDENACAO_COLUNA], $parametros[ConstanteParametros::CHAVE_ORDENACAO_SORT]);
            $opcoes[\Knp\Component\Pager\Paginator::SORT_FIELD_PARAMETER_NAME]     = "~";
            $opcoes[\Knp\Component\Pager\Paginator::SORT_DIRECTION_PARAMETER_NAME] = "~";
        }

        return \App\Helper\FunctionHelper::montaPaginatorPaginacao($queryBuilder, $pagina, $numeroItensPorPagina, $opcoes);
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
        $queryBuilder->andWhere("func.id = :id");
        $queryBuilder->setParameter("id", $id);

        return $queryBuilder->getQuery()->getOneOrNullResult(\Doctrine\ORM\Query::HYDRATE_ARRAY);
    }

    /**
     * Busca funcionários por nome
     *
     * @param string $nome
     *
     * @return \App\Entity\Principal\Funcionario[]
     */
    public function buscarPorNome($nome)
    {
        $queryBuilder = $this->montaQueryBase();
        $this->filtrarFranqueada($queryBuilder);
        $queryBuilder->andWhere('func.data_demissao IS NULL');
        $queryBuilder->andWhere('pes.nome_contato LIKE :nome');
        $queryBuilder->setParameter('nome', "%$nome%");
        $queryBuilder->distinct();

        return \App\Helper\FunctionHelper::retornaArrayNull($queryBuilder);
    }

    /**
     * Busca funcionario por flags
     *
     * @param array $parametros
     * @param array $extraParam
     *
     * @return array|NULL
     */
    public function buscaFuncionarioAtivosPorFlags($parametros, $extraParam=[])
    {
        $queryBuilder = $this->montaQueryBase();
        $queryBuilder->andWhere('func.data_demissao IS NULL');
        $this->filtrarFranqueada($queryBuilder);
        $this->filtrarFlagsFuncionario($queryBuilder, $parametros, $extraParam);
        $queryBuilder->andWhere("func.situacao = :situacao");
        $queryBuilder->setParameter("situacao", SituacoesSistema::SITUACAO_ATIVO);
        $queryBuilder->distinct();
        return \App\Helper\FunctionHelper::retornaArrayNull($queryBuilder);
    }

        /**
     * Busca instrutores Ativos por Franqueada
     *
     * @param int
     *
     * @return array|NULL
     */
    public function buscaInstrutoresAtivos($franqueada_id)
    {
        $queryBuilder = $this->createQueryBuilder("func");
        $queryBuilder->andWhere('func.data_demissao IS NULL');
      //  $this->filtrarFranqueada($queryBuilder);
      //  $this->filtrarFlagsFuncionario($queryBuilder, $parametros, $extraParam);
        $queryBuilder->andWhere("func.situacao = :situacao");
        $queryBuilder->setParameter("situacao", SituacoesSistema::SITUACAO_ATIVO);
        $queryBuilder->andWhere("func.franqueada = :franqueada");
        $queryBuilder->setParameter("franqueada", $franqueada_id);
        $queryBuilder->andWhere("func.instrutor = 1");
        $queryBuilder->distinct();
        return \App\Helper\FunctionHelper::retornaArrayNull($queryBuilder);
    }

            /**
     * Busca instrutores Ativos por Franqueada
     *
     * @param int
     *
     * @return array|NULL
     */
    public function buscarHorariosDisponiveisInstrutor($instrutor)
    {
        $sql = 'select
                f.id,
                p.nome_contato,
                f.pessoa_id ,
                fd.dia_semana,
                fd.hora_inicial,
                fd.hora_final
                from
                    funcionario f
                inner join funcionario_disponibilidade fd on	f.id = fd.funcionario_id
                LEFT join pessoa p on p.id = f.pessoa_id ';
       
        $sql = $sql . 'where f.id = '.$instrutor;

        // echo($sql);
        // die;
        return $this->managerRegistry->getConnection()->fetchAllAssociative($sql);
    }

    /**
     * Busca instrutores Ativos por Franqueada
     *
     * @param int
     * @param array $parametros
     *
     * @return array|NULL
     */
    public function buscarHorariosTurmaInstrutor($parametros)
    {
       date_default_timezone_set('America/Sao_Paulo');

            $data_inicial   = new \DateTime($parametros['data']);
            $data_final   = new \DateTime($parametros['data']);
        
       $data_inicial = $data_inicial->format("Y-m-d"). ' 00:00:01';
       $data_final = $data_final->format("Y-m-d"). ' 23:59:59';

        $sql = 'select  tur.id,
                f.apelido,
                tur.descricao,
                tur.data_inicio,
                tur.data_fim,
                ha.dia_semana,
                ha.horario_inicio 
        from funcionario f 
        inner join turma tur on tur.funcionario_id = f.id
        left join horario h on tur.horario_id = h.id 
        left join horario_aula ha on ha.horario_id = h.id ';
        $sql = $sql . ' where f.id = '.$parametros['instrutor_id']. ' and tur.situacao in ("ABE", "FOR")';
        $sql = $sql . ' and ha.dia_semana = "'.$parametros['dia_semana'].'"';

        $sql = $sql . ' AND (tur.data_inicio <= "'.$data_inicial.'" 
		                AND tur.data_fim >= "'.$data_final.'")';
                        
        return $this->managerRegistry->getConnection()->fetchAllAssociative($sql);
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
        $queryBuilder = $this->createQueryBuilder("func");
        $queryBuilder->select([
            'p.nome_contato',
            'c.descricao as cargo_funcionario',
            'u.nome',
            'p.sexo',
            'p.cnpj_cpf',
            'p.numero_identidade',
            'p.estado_civil',
            'p.telefone_preferencial',
            "concat(p.endereco,', ', p.numero_endereco, ', ', p.bairro_endereco, ', ', p.complemento_endereco, ', ', cid.nome, '-', est.sigla) as endereco_completo",
            'func.situacao',
            "date_format(p.data_cadastramento, '%Y-%m-%d') as data_cadastramento"
        ]);
        $queryBuilder->leftjoin('func.franqueada', 'f');
        $queryBuilder->leftjoin('func.cargo', 'c');
        $queryBuilder->leftjoin('func.pessoa', 'p');
        $queryBuilder->leftJoin('func.usuario', 'u');
        $queryBuilder->leftJoin('p.cidade', 'cid');
        $queryBuilder->leftJoin('p.estado', 'est');

        $queryBuilder->where('func.franqueada = :franqueada');
        $queryBuilder->setParameter('franqueada', VariaveisCompartilhadas::$franqueadaID);
        $queryBuilder->andWhere('func.data_demissao IS NULL');

        if ((isset($parametros[ConstanteParametros::CHAVE_SITUACAO]) === true)&&(is_null($parametros[ConstanteParametros::CHAVE_SITUACAO]) === false)) {
            $situacao = explode(',', $parametros[ConstanteParametros::CHAVE_SITUACAO]);
            $queryBuilder->andWhere("func.situacao IN (:situacao)");
            $queryBuilder->setParameter('situacao', implode("', '", $situacao));
        }

        if ((isset($parametros[ConstanteParametros::CHAVE_CARGO]) === true)&&(is_null($parametros[ConstanteParametros::CHAVE_CARGO]) === false)) {
            $queryBuilder->andWhere('c = :cargo');
            $queryBuilder->setParameter('cargo', $parametros[ConstanteParametros::CHAVE_CARGO]);
        }

        if ((isset($parametros[ConstanteParametros::CHAVE_FILTRO_FUNCIONARIO]) === true)&&(is_null($parametros[ConstanteParametros::CHAVE_FILTRO_FUNCIONARIO]) === false)) {
            $queryBuilder->andWhere('func = :funcionario');
            $queryBuilder->setParameter('funcionario', $parametros[ConstanteParametros::CHAVE_FILTRO_FUNCIONARIO]);
        }

        if ((isset($parametros[ConstanteParametros::CHAVE_DATA_ADMISSAO_INICIAL]) === true)&&(is_null($parametros[ConstanteParametros::CHAVE_DATA_ADMISSAO_INICIAL]) === false)) {
            $queryBuilder->andWhere('func.data_admissao >= :data_admissao_inicial');
            $queryBuilder->setParameter('data_admissao_inicial', $parametros[ConstanteParametros::CHAVE_DATA_ADMISSAO_INICIAL]);
        }

        if ((isset($parametros[ConstanteParametros::CHAVE_DATA_ADMISSAO_FINAL]) === true)&&(is_null($parametros[ConstanteParametros::CHAVE_DATA_ADMISSAO_FINAL]) === false)) {
            $queryBuilder->andWhere('func.data_admissao <= :data_admissao_final');
            $queryBuilder->setParameter('data_admissao_final', $parametros[ConstanteParametros::CHAVE_DATA_ADMISSAO_FINAL]);
        }

        if ((isset($parametros[ConstanteParametros::CHAVE_DATA_DEMISSAO_INICIAL]) === true)&&(is_null($parametros[ConstanteParametros::CHAVE_DATA_DEMISSAO_INICIAL]) === false)) {
            $queryBuilder->andWhere('func.data_demissao >= :data_demissao_inicial');
            $queryBuilder->setParameter('data_demissao_inicial', $parametros[ConstanteParametros::CHAVE_DATA_DEMISSAO_INICIAL]);
        }

        if ((isset($parametros[ConstanteParametros::CHAVE_DATA_DEMISSAO_FINAL]) === true)&&(is_null($parametros[ConstanteParametros::CHAVE_DATA_DEMISSAO_FINAL]) === false)) {
            $queryBuilder->andWhere('func.data_demissao <= :data_demissao_final');
            $queryBuilder->setParameter('data_demissao_final', $parametros[ConstanteParametros::CHAVE_DATA_DEMISSAO_FINAL]);
        }

        if ((isset($parametros[ConstanteParametros::CHAVE_DATA_ANIVERSARIO_DE]) === true)&&(is_null($parametros[ConstanteParametros::CHAVE_DATA_ANIVERSARIO_DE]) === false)) {
            $data = explode("/", $parametros[ConstanteParametros::CHAVE_DATA_ANIVERSARIO_DE]);
            $queryBuilder->andWhere('DAY(p.data_nascimento) >= :dia_aniversario_de');
            $queryBuilder->andWhere('MONTH(p.data_nascimento) >= :mes_aniversario_de');
            $queryBuilder->setParameter('dia_aniversario_de', $data[0]);
            $queryBuilder->setParameter('mes_aniversario_de', $data[1]);
        }

        if ((isset($parametros[ConstanteParametros::CHAVE_DATA_ANIVERSARIO_ATE]) === true)&&(is_null($parametros[ConstanteParametros::CHAVE_DATA_ANIVERSARIO_ATE]) === false)) {
            $data = explode("/", $parametros[ConstanteParametros::CHAVE_DATA_ANIVERSARIO_ATE]);
            $queryBuilder->andWhere('DAY(p.data_nascimento) <= :dia_aniversario_ate');
            $queryBuilder->andWhere('MONTH(p.data_nascimento) <= :mes_aniversario_ate');
            $queryBuilder->setParameter('dia_aniversario_ate', $data[0]);
            $queryBuilder->setParameter('mes_aniversario_ate', $data[1]);
        }

        if ((isset($parametros[ConstanteParametros::CHAVE_DATA_CADASTRO_DE]) === true)&&(is_null($parametros[ConstanteParametros::CHAVE_DATA_CADASTRO_DE]) === false)) {
            $queryBuilder->andWhere('p.data_cadastramento >= :data_cadastro_de');
            $queryBuilder->setParameter('data_cadastro_de', $parametros[ConstanteParametros::CHAVE_DATA_CADASTRO_DE]);
        }

        if ((isset($parametros[ConstanteParametros::CHAVE_DATA_CADASTRO_ATE]) === true)&&(is_null($parametros[ConstanteParametros::CHAVE_DATA_CADASTRO_ATE]) === false)) {
            $queryBuilder->andWhere('p.data_cadastramento <= :data_cadastro_ate');
            $queryBuilder->setParameter('data_cadastro_ate', $parametros[ConstanteParametros::CHAVE_DATA_CADASTRO_ATE]);
        }

        return $queryBuilder->getQuery()->getResult();
    }

    public function consultaAulasPersonalParaPagamento($parametros)
    {
        $queryBuilder = $this->createQueryBuilder("funcionario");
        $queryBuilder->select("funcionario, diario, aluno, pagamento");
        $queryBuilder->join("funcionario.franqueada", "fran");
        $queryBuilder->leftJoin("funcionario.alunoDiarioPersonals", "diario");
        $queryBuilder->leftJoin("diario.aluno", "aluno");
        $queryBuilder->leftJoin("diario.pagamentoAlunoDiarioPersonals", "pagamento");

        $this->filtrarFranqueada($queryBuilder);

        $queryBuilder->andWhere('funcionario.data_demissao IS NULL');
        $queryBuilder->andWhere("diario is not null");
        $queryBuilder->andWhere("diario.funcionario = :funcionario");

        $queryBuilder->andWhere("diario.data_aula >= :dataInicio");
        $queryBuilder->andWhere("diario.data_aula <= :dataFim");

        $queryBuilder->setParameter("funcionario", $parametros[ConstanteParametros::CHAVE_FUNCIONARIO]);
        $queryBuilder->setParameter("dataInicio", $parametros[ConstanteParametros::CHAVE_DATA_INICIO]);
        $queryBuilder->setParameter("dataFim", $parametros[ConstanteParametros::CHAVE_DATA_FIM]);
        $queryBuilder->orderBy("diario.data_aula", "ASC");
     
        return \App\Helper\FunctionHelper::retornaResultados($queryBuilder, true);
    }

    public function consultaPersonalInstrutorDiaSemana($parametros)
    {
        date_default_timezone_set('America/Sao_Paulo');

        $data_inicial   = new \DateTime($parametros['data']);
        $data_final   = new \DateTime($parametros['data']);
 
       $data_inicial = $data_inicial->format("Y-m-d"). ' 00:00:01';
       $data_final = $data_final->format("Y-m-d"). ' 23:59:59';

        $sql = "select  ap.id,
                        f.apelido,
                        ap.inicio,
                        CASE
                            WHEN DAYOFWEEK(ap.inicio) = 1 THEN 'DOM'
                            WHEN DAYOFWEEK(ap.inicio) = 2 THEN 'SEG'
                            WHEN DAYOFWEEK(ap.inicio) = 3 THEN 'TER'
                            WHEN DAYOFWEEK(ap.inicio) = 4 THEN 'QUA'
                            WHEN DAYOFWEEK(ap.inicio) = 5 THEN 'QUI'
                            WHEN DAYOFWEEK(ap.inicio) = 6 THEN 'SEX'
                            WHEN DAYOFWEEK(ap.inicio) = 7 THEN 'SAB'
                            ELSE 'The quantity is under 30'
                        END AS dia_semana	
                from funcionario f 
                LEFT JOIN agendamento_personal ap on ap.funcionario_id = f.id 
                where f.id = ".$parametros['instrutor_id']." and ap.finalizado = 0 
                AND (ap.inicio >= '".$data_inicial."' AND ap.inicio <= '".$data_final."')";
         
         return $this->managerRegistry->getConnection()->fetchAllAssociative($sql);
    }
    public function consultaAtivExtraInstrutorDiaSemana($parametros)
    {
        date_default_timezone_set('America/Sao_Paulo');

        $data_inicial   = new \DateTime($parametros['data']);
        $data_final   = new \DateTime($parametros['data']);
        $data_inicial = $data_inicial->format("Y-m-d"). ' 00:00:01';
        $data_final = $data_final->format("Y-m-d"). ' 23:59:59';

        $sql = "select  ae.descricao_atividade ,
                        f.apelido,
                        ae.data_hora_inicio,
                        ae.data_hora_fim,
                        CASE
                            WHEN DAYOFWEEK(ae.data_hora_inicio) = 1 THEN 'DOM'
                            WHEN DAYOFWEEK(ae.data_hora_inicio) = 2 THEN 'SEG'
                            WHEN DAYOFWEEK(ae.data_hora_inicio) = 3 THEN 'TER'
                            WHEN DAYOFWEEK(ae.data_hora_inicio) = 4 THEN 'QUA'
                            WHEN DAYOFWEEK(ae.data_hora_inicio) = 5 THEN 'QUI'
                            WHEN DAYOFWEEK(ae.data_hora_inicio) = 6 THEN 'SEX'
                            WHEN DAYOFWEEK(ae.data_hora_inicio) = 7 THEN 'SAB'
                        END AS dia_semana
                from funcionario f 
                LEFT JOIN atividade_extra_funcionario aef on aef.funcionario_id = f.id 
                LEFT JOIN atividade_extra ae on ae.id = aef.atividade_extra_id 
                where f.id = ".$parametros['instrutor_id']." and ae.situacao in ('P') 
                AND (ae.data_hora_inicio  >= '".$data_inicial."' AND ae.data_hora_fim  <= '".$data_final."')";


            return $this->managerRegistry->getConnection()->fetchAllAssociative($sql);
    }

    public function consultaAtividadesExtraParaPagamento($parametros)
    {
        $queryBuilder = $this->createQueryBuilder("funcionario");
        $queryBuilder->select("funcionario, atividade, aluno, pagamento");
        $queryBuilder->join("funcionario.franqueada", "fran");
        $queryBuilder->leftJoin("funcionario.atividadeExtrasPendentes", "atividade");
        $queryBuilder->leftJoin("atividade.alunoAtividadeExtras", "aluno");
        $queryBuilder->leftJoin("atividade.pagamentoAtividadeExtras", "pagamento");

        $this->filtrarFranqueada($queryBuilder);

        $queryBuilder->andWhere('funcionario.data_demissao IS NULL');
        $queryBuilder->andWhere("funcionario = :funcionario");

        $queryBuilder->andWhere("aluno.removido = false");
        $queryBuilder->andWhere("atividade is not null");
        $queryBuilder->andWhere("atividade.situacao = 'C'");
        $queryBuilder->andWhere("atividade.data_hora_inicio >= :dataInicio");
        $queryBuilder->andWhere("atividade.data_hora_inicio <= :dataFim");

        $queryBuilder->setParameter("dataInicio", $parametros[ConstanteParametros::CHAVE_DATA_INICIO]);
        $queryBuilder->setParameter("dataFim", $parametros[ConstanteParametros::CHAVE_DATA_FIM]);
        $queryBuilder->setParameter("funcionario", $parametros[ConstanteParametros::CHAVE_FUNCIONARIO]);

        return \App\Helper\FunctionHelper::retornaResultados($queryBuilder, true);
    }

    public function consultaReposicoesAulaParaPagamento($parametros)
    {
        $queryBuilder = $this->createQueryBuilder("funcionario");
        $queryBuilder->select("funcionario, reposicao, aluno, pagamento");
        $queryBuilder->join("funcionario.franqueada", "fran");
        $queryBuilder->leftJoin("funcionario.responsavelReposicaoAulas", "reposicao");
        $queryBuilder->leftJoin("reposicao.aluno", "aluno");
        $queryBuilder->leftJoin("reposicao.pagamentoReposicaoAulas", "pagamento");

        $this->filtrarFranqueada($queryBuilder);

        $queryBuilder->andWhere('funcionario.data_demissao IS NULL');
        $queryBuilder->andWhere("funcionario = :funcionario");

        $queryBuilder->andWhere("reposicao is not null");
        $queryBuilder->andWhere("reposicao.situacao = 'C'");

        $queryBuilder->andWhere("reposicao.data_hora_inicio >= :dataInicio");
        $queryBuilder->andWhere("reposicao.data_hora_inicio <= :dataFim");

        $queryBuilder->setParameter("dataInicio", $parametros[ConstanteParametros::CHAVE_DATA_INICIO]);
        $queryBuilder->setParameter("dataFim", $parametros[ConstanteParametros::CHAVE_DATA_FIM]);
        $queryBuilder->setParameter("funcionario", $parametros[ConstanteParametros::CHAVE_FUNCIONARIO]);

        return \App\Helper\FunctionHelper::retornaResultados($queryBuilder, true);
    }

    public function consultaBonusClassParaPagamento($parametros)
    {
        $queryBuilder = $this->createQueryBuilder("funcionario");
        $queryBuilder->select("funcionario, bonusClass, aluno, pagamento");
        $queryBuilder->join("funcionario.franqueada", "fran");
        $queryBuilder->leftJoin("funcionario.bonusClasses", "bonusClass");
        $queryBuilder->leftJoin("bonusClass.alunosBonusClasses", "aluno");
        $queryBuilder->leftJoin("bonusClass.pagamentoBonusClasses", "pagamento");

        $this->filtrarFranqueada($queryBuilder);

        $queryBuilder->andWhere('funcionario.data_demissao IS NULL');
        $queryBuilder->andWhere("funcionario = :funcionario");
        $queryBuilder->andWhere("bonusClass is not null");
        $queryBuilder->andWhere("bonusClass.situacao = 'CON'");
        $queryBuilder->andWhere("bonusClass.data_aula >= :dataInicio");
        $queryBuilder->andWhere("bonusClass.data_aula <= :dataFim");

        $queryBuilder->setParameter("dataInicio", $parametros[ConstanteParametros::CHAVE_DATA_INICIO]);
        $queryBuilder->setParameter("dataFim", $parametros[ConstanteParametros::CHAVE_DATA_FIM]);
        $queryBuilder->setParameter("funcionario", $parametros[ConstanteParametros::CHAVE_FUNCIONARIO]);

        return \App\Helper\FunctionHelper::retornaResultados($queryBuilder, true);
    }

    /**
     * Consulta o valor por hora do funcionário conforme a modalidade e número de contratos
     *
     * @param int $funcionarioId
     * @param string $modalidade
     * @param int $quantidadeContratos
     */
    public function consultaValorHoraFuncionario($funcionarioId, $modalidade, $quantidadeContratos)
    {
        $queryBuilder = $this->createQueryBuilder("funcionario");
        $queryBuilder->addSelect("valorHoraFuncionario, valorHora, valorHoraLinhasFuncionario, nivelInstrutor, valorHoraNivelInstrutor, valorHoraLinhasNivelInstrutor");
        $queryBuilder->join("funcionario.franqueada", "fran");
        $queryBuilder->leftJoin("funcionario.funcionarioValorHoras", "valorHoraFuncionario");
        $queryBuilder->leftJoin("valorHoraFuncionario.valor_hora", "valorHora");
        $queryBuilder->leftJoin("valorHora.valor_hora_linhas", "valorHoraLinhasFuncionario");

        $queryBuilder->leftJoin("funcionario.nivel_instrutor", "nivelInstrutor");
        $queryBuilder->leftJoin("nivelInstrutor.valorHoras", "valorHoraNivelInstrutor");
        $queryBuilder->leftJoin("valorHoraNivelInstrutor.valor_hora_linhas", "valorHoraLinhasNivelInstrutor");

        $this->filtrarFranqueada($queryBuilder);

        $queryBuilder->andWhere('funcionario.data_demissao IS NULL');
        $queryBuilder->andWhere("funcionario = :funcionario");
        $queryBuilder->andWhere(
            $queryBuilder->expr()->orX(
                $queryBuilder->expr()->andX(
                    $queryBuilder->expr()->eq('valorHora.franqueada', VariaveisCompartilhadas::$franqueadaID),
                    $queryBuilder->expr()->eq('valorHoraLinhasFuncionario.tipo_pagamento', 'funcionario.tipo_pagamento'),
                    $queryBuilder->expr()->eq('valorHoraLinhasFuncionario.tipo', "'$modalidade'"),
                    $queryBuilder->expr()->lte('valorHoraLinhasFuncionario.quantidade_alunos_minima', $quantidadeContratos),
                    $queryBuilder->expr()->gte('valorHoraLinhasFuncionario.quantidade_alunos_maxima', $quantidadeContratos)
                ),
                $queryBuilder->expr()->andX(
                    $queryBuilder->expr()->eq('valorHoraNivelInstrutor.franqueada', VariaveisCompartilhadas::$franqueadaID),
                    $queryBuilder->expr()->eq('valorHoraLinhasNivelInstrutor.tipo_pagamento', 'funcionario.tipo_pagamento'),
                    $queryBuilder->expr()->eq('valorHoraLinhasNivelInstrutor.tipo', "'$modalidade'"),
                    $queryBuilder->expr()->lte('valorHoraLinhasNivelInstrutor.quantidade_alunos_minima', $quantidadeContratos),
                    $queryBuilder->expr()->gte('valorHoraLinhasNivelInstrutor.quantidade_alunos_maxima', $quantidadeContratos)
                )
            )
        );

        $queryBuilder->setParameter("funcionario", $funcionarioId);

        $funcionario = \App\Helper\FunctionHelper::retornaPrimeiroResultado($queryBuilder, true);

        if (empty($funcionario['funcionarioValorHoras']) === false) {
            foreach ($funcionario['funcionarioValorHoras'] as $funcionarioValor) {
                $valorHora          = $funcionarioValor['valor_hora'];
                $mesmoTipoPagamento = $valorHora['valor_hora_linhas']['tipo_pagamento'] === $funcionario['tipo_pagamento'];
                $quantidadeCorreta  = $valorHora['valor_hora_linhas']['quantidade_alunos_minima'] <= $quantidadeContratos && $valorHora['valor_hora_linhas']['quantidade_alunos_maxima'] >= $quantidadeContratos;
                $mesmoTipo          = $valorHora['valor_hora_linhas']['tipo'] === $modalidade;
                if ($mesmoTipoPagamento === false || $quantidadeCorreta === false || $mesmoTipo === false) {
                    $funcionario['funcionarioValorHoras'] = [];
                }
            }
        }

        return $funcionario;
    }


}
