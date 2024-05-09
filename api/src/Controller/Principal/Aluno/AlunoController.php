<?php

namespace App\Controller\Principal\Aluno;

use Symfony\Component\Routing\Annotation\Route;
use FOS\RestBundle\Controller\Annotations as FOSRest;
use Swagger\Annotations as SWG;
use App\Factory\ResponseFactory;
use App\Controller\Principal\Base\GenericController;
use FOS\RestBundle\Request\ParamFetcher;
use App\Facade\Principal\AlunoFacade;
use Symfony\Component\HttpFoundation\Request;
use App\Helper\ConstanteParametros;
use App\Helper\SituacoesSistema;
use App\Facade\Principal\OcorrenciaAcademicaFacade;
use App\Facade\Principal\OcorrenciaAcademicaDetalhesFacade;
use App\Facade\Principal\GenericItemFacade;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\ResponseHeaderBag;

/**
 *
 * @author        Luiz A Costa
 * @Route("/api")
 */
class AlunoController extends GenericController
{

    /**
     *
     * @var \App\Facade\Principal\AlunoFacade
     */
    private $alunoFacade;

    /**
     *
     * @var \App\Facade\Principal\GenericItemFacade
     */
    private $itemFacade;

    /**
     *
     * @var \App\Facade\Principal\OcorrenciaAcademicaFacade
     */
    private $ocorrenciaAcademicaFacade;

    /**
     *
     * @var \App\Facade\Principal\OcorrenciaAcademicaDetalhesFacade
     */
    private $ocorrenciaAcademicaDetalhesFacade;

    /**
     *
     * @var \App\Repository\Principal\ContratoRepository
     */
    private $contratoRepository;

    /**
     *
     * @var \App\Repository\Principal\TurmaRepository
     */
    private $turmaRepository;

    /**
     *
     * @var \App\Repository\Principal\TipoOcorrenciaRepository
     */
    private $tipoOcorrencia;

    /**
     * {@inheritdoc}
     */
    protected function constroiFacades()
    {
        // Para criar o LogFacade do GenericController
        parent::constroiFacades();
        $this->alunoFacade = new AlunoFacade(self::getManagerRegistry());
        $this->itemFacade  = new GenericItemFacade(self::getManagerRegistry());
        $this->ocorrenciaAcademicaFacade         = new OcorrenciaAcademicaFacade(self::getManagerRegistry());
        $this->ocorrenciaAcademicaDetalhesFacade = new OcorrenciaAcademicaDetalhesFacade(self::getManagerRegistry());
        $this->contratoRepository = self::getEntityManager()->getRepository(\App\Entity\Principal\Contrato::class);
        $this->turmaRepository    = self::getEntityManager()->getRepository(\App\Entity\Principal\Turma::class);
        $this->tipoOcorrencia     = self::getEntityManager()->getRepository(\App\Entity\Principal\TipoOcorrencia::class);
    }

    /**
     * Gera uma ocorrencia academica(monta parametros e cria objeto) com a situacao encerrada
     *
     * @param string $mensagemErro
     * @param int $franqueadaId
     * @param int $alunoId
     * @param int $usuarioId
     * @param int $tipoOcorrencia
     * @param string $obsevacaoOcorrencia
     *
     * @return boolean
     */
    private function gerarOcorrenciaAcademica(&$mensagemErro, $franqueadaId, $alunoId, $usuarioId, $tipoOcorrencia, $obsevacaoOcorrencia)
    {
        $tipoOcorrenciaORM = $this->tipoOcorrencia->findOneBy([ConstanteParametros::CHAVE_TIPO => $tipoOcorrencia]);
        if (is_null($tipoOcorrenciaORM) === true) {
            $mensagemErro .= "Não foi encontrado um TipoOcorrencia para o tipo informado.\n";
        }

        $retornaFuncionarioIdDoUsuario = $this->ocorrenciaAcademicaFacade->retornaFuncionarioIdDoUsuario($usuarioId);
        if (is_null($retornaFuncionarioIdDoUsuario) === true) {
            $mensagemErro .= "Não foi encontrado um funcionario cadastrado para o usuario informado.\n";
        }

        $bSuccesso = empty($mensagemErro) === true;
        if ($bSuccesso === true) {
            $parametrosOcorrenciaAcademica = [
                ConstanteParametros::CHAVE_FRANQUEADA             => $franqueadaId,
                ConstanteParametros::CHAVE_ALUNO                  => $alunoId,
                ConstanteParametros::CHAVE_USUARIO                => $usuarioId,
                ConstanteParametros::CHAVE_ORIGEM_OCORRENCIA_TIPO => SituacoesSistema::ORIGEM_OCORRENCIA_TRANSFERENCIA_TURMA,
                ConstanteParametros::CHAVE_FUNCIONARIO            => $retornaFuncionarioIdDoUsuario,
                ConstanteParametros::CHAVE_TIPO_OCORRENCIA        => $tipoOcorrenciaORM,
                ConstanteParametros::CHAVE_DATA_CONCLUSAO         => new \DateTime(),
                ConstanteParametros::CHAVE_SITUACAO               => SituacoesSistema::OCORRENCIA_ABERTA,
                ConstanteParametros::CHAVE_TEXTO                  => $obsevacaoOcorrencia,
            ];
            $ocorrenciaAcademicaORM        = $this->ocorrenciaAcademicaFacade->criar($mensagemErro, $parametrosOcorrenciaAcademica);
            if ((is_null($ocorrenciaAcademicaORM) === true) || (empty($mensagemErro) === false)) {
                $bSuccesso = false;
            } else {
                $ocorrenciaAcademicaDetalhesORM = $this->ocorrenciaAcademicaDetalhesFacade->criar($mensagemErro, $ocorrenciaAcademicaORM, $parametrosOcorrenciaAcademica, false);
                if ((is_null($ocorrenciaAcademicaDetalhesORM) === true) || (empty($mensagemErro) === false)) {
                    $bSuccesso = false;
                }
            }
        }//end if

        return $bSuccesso;
    }

    /**
     *
     * @SWG\Get(
     *     path="/api/aluno/gerar_lista_emails",
     *     summary="Gera arquivo de emails de alunos",
     *     description="Gera arquivo de emails de alunos",
     *     produces={"application/json"},
     * @SWG\Response(
     *         response="200",
     *         description="Retorna o arquivo"
     *     ),
     * @SWG\Response(
     *         response="400",
     *         description="Ocorreu algum erro no servidor",
     *     )
     * )
     *
     * @FOSRest\QueryParam(name="alunos",     strict=true, nullable=false, description="Id Alunos", map=true, requirements="\d+")
     * @FOSRest\QueryParam(name="franqueada", strict=true, allowBlank=false, nullable=false, description="Id Franqueada",requirements="\d+")
     *
     * @FOSRest\Get("/aluno/gerar_lista_emails")
     *
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function gerarListaEmails(ParamFetcher $request)
    {
        $parametros = $request->all();

        $arrayAlunosORM = $this->alunoFacade->buscarAlunosORM($parametros[ConstanteParametros::CHAVE_FRANQUEADA], $parametros[ConstanteParametros::CHAVE_ALUNOS]);

        if (($arrayAlunosORM !== null) && (count($arrayAlunosORM) > 0)) {
            $arquivoCSV = "";
            foreach ($arrayAlunosORM as $alunoORM) {
                $arquivoCSV .= $alunoORM->getPessoa()->getNomeContato() . ";";
                if (empty($alunoORM->getPessoa()->getEmailPreferencial()) === false) {
                    $arquivoCSV .= $alunoORM->getPessoa()->getEmailPreferencial() . ";\n";
                } else {
                    $arquivoCSV .= "E-MAIL PREFERENCIAL NÃO CADASTRADO;\n";
                }
            }

            $response          = new Response(utf8_decode($arquivoCSV));
            $dispositionHeader = $response->headers->makeDisposition(ResponseHeaderBag::DISPOSITION_ATTACHMENT, "Arquivo_Alunos_" . uniqid() . '.csv');
            $response->headers->set('Content-Type', 'text/csv; charset=utf-8');
            $response->headers->set('Pragma', 'public');
            $response->headers->set('Cache-Control', 'maxage=1');
            $response->headers->set('Content-Disposition', $dispositionHeader);
            return $response;
        } else {
            $response = new Response("<h2>Houve um erro:</h2><pre>Não há dados para realização da lista de e-mails dos alunos</pre>");
            $response->headers->set('Content-Type', 'text/html');
            return $response;
        }//end if
    }

    /**
     *
     * @SWG\Get(
     *     path="/api/aluno/listar",
     *     summary="Listar aluno",
     *     description="Lista as aluno do banco",
     *     tags={"Aluno"},
     *     produces={"application/json"},
     * @SWG\Response(
     *         response="200",
     *         description="Retorna os aluno"
     *     ),
     * @SWG\Response(
     *         response="400",
     *         description="Ocorreu algum erro no servidor",
     *     )
     * )
     *
     * @FOSRest\QueryParam(name="pagina",                        strict=false, allowBlank=false, default="1", description="Pagina para realizar o scroll", requirements="\d{0,2}")
     * @FOSRest\QueryParam(name="itensPorPagina",                strict=false, allowBlank=false, default="10", description="Quantidade de itens a ser exibido", requirements="\d{0,2}")
     * @FOSRest\QueryParam(name="order",                         strict=false, nullable=true,  description="Coluna de ordenação")
     * @FOSRest\QueryParam(name="direcao",                       strict=false, nullable=true,  description="ASC|DESC")
     * @FOSRest\QueryParam(name="aluno",                         strict=false, nullable=true,  description="Nome do aluno")
     * @FOSRest\QueryParam(name="telefone",                      strict=false, nullable=true,  description="Telefone do aluno")
     * @FOSRest\QueryParam(name="situacao",                      strict=false, nullable=true,  description="Situacao do aluno")
     * @FOSRest\QueryParam(name="cnpj_cpf",                      strict=false, nullable=true,  description="Cnpj ou cpf")
     * @FOSRest\QueryParam(name="pessoa_sexo",                   strict=false, nullable=true,  description="Sexo da pessoa")
     * @FOSRest\QueryParam(name="pessoa_estado_civil",           strict=false, nullable=true,  description="Estado civil da pessoa")
     * @FOSRest\QueryParam(name="responsavel_financeiro_pessoa", strict=false, nullable=true,  description="Responsável financeiro")
     * @FOSRest\QueryParam(name="responsavel_didatico_pessoa",   strict=false, nullable=true,  description="Responsável didático")
     * @FOSRest\QueryParam(name="emancipado",                    strict=false, nullable=true,  description="Aluno emancipado")
     * @FOSRest\QueryParam(name="classificacao_aluno",           strict=false, nullable=true,  description="Classificação do aluno")
     * @FOSRest\QueryParam(name="curso",                         strict=false, nullable=true,  description="Curso")
     * @FOSRest\QueryParam(name="data_cadastro_inicial",         strict=false, nullable=true,  description="Data de cadastro inical")
     * @FOSRest\QueryParam(name="data_cadastro_final",           strict=false, nullable=true,     description="Data de cadastro final")
     * @FOSRest\QueryParam(name="data_nascimento_inicial",       strict=false, nullable=true,  description="Data de nascimento inicial")
     * @FOSRest\QueryParam(name="data_nascimento_final",         strict=false, nullable=true,    description="Data de nascimento final")
     *
     * @FOSRest\Get("/aluno/listar")
     *
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function lista(ParamFetcher $request)
    {
        $parametros = $request->all();
        $mensagem   = "";
        $resultados = $this->alunoFacade->listar($mensagem, $parametros);
        if ($resultados === false) {
            return ResponseFactory::badRequest([], $mensagem);
        }

        return ResponseFactory::ok($resultados);
    }



    /**
     *
     * @SWG\Get(
     *     path="/api/aluno/listar-header",
     *     summary="Listar aluno",
     *     description="Lista as aluno do banco",
     *     tags={"Aluno"},
     *     produces={"application/json"},
     * @SWG\Response(
     *         response="200",
     *         description="Retorna os aluno"
     *     ),
     * @SWG\Response(
     *         response="400",
     *         description="Ocorreu algum erro no servidor",
     *     )
     * )
     *
     * @FOSRest\QueryParam(name="pagina",                        strict=false, allowBlank=false, default="1", description="Pagina para realizar o scroll", requirements="\d{0,2}")
     * @FOSRest\QueryParam(name="itensPorPagina",                strict=false, allowBlank=false, default="10", description="Quantidade de itens a ser exibido", requirements="\d{0,2}")
     * @FOSRest\QueryParam(name="order",                         strict=false, nullable=true,  description="Coluna de ordenação")
     * @FOSRest\QueryParam(name="direcao",                       strict=false, nullable=true,  description="ASC|DESC")
     * @FOSRest\QueryParam(name="aluno",                         strict=false, nullable=true,  description="Nome do aluno")
     * @FOSRest\QueryParam(name="telefone",                      strict=false, nullable=true,  description="Telefone do aluno")
     * @FOSRest\QueryParam(name="situacao",                      strict=false, nullable=true,  description="Situacao do aluno")
     * @FOSRest\QueryParam(name="cnpj_cpf",                      strict=false, nullable=true,  description="Cnpj ou cpf")
     * @FOSRest\QueryParam(name="pessoa_sexo",                   strict=false, nullable=true,  description="Sexo da pessoa")
     * @FOSRest\QueryParam(name="pessoa_estado_civil",           strict=false, nullable=true,  description="Estado civil da pessoa")
     * @FOSRest\QueryParam(name="responsavel_financeiro_pessoa", strict=false, nullable=true,  description="Responsável financeiro")
     * @FOSRest\QueryParam(name="responsavel_didatico_pessoa",   strict=false, nullable=true,  description="Responsável didático")
     * @FOSRest\QueryParam(name="emancipado",                    strict=false, nullable=true,  description="Aluno emancipado")
     * @FOSRest\QueryParam(name="classificacao_aluno",           strict=false, nullable=true,  description="Classificação do aluno")
     * @FOSRest\QueryParam(name="curso",                         strict=false, nullable=true,  description="Curso")
     * @FOSRest\QueryParam(name="data_cadastro_inicial",         strict=false, nullable=true,  description="Data de cadastro inical")
     * @FOSRest\QueryParam(name="data_cadastro_final",           strict=false, nullable=true,     description="Data de cadastro final")
     * @FOSRest\QueryParam(name="data_nascimento_inicial",       strict=false, nullable=true,  description="Data de nascimento inicial")
     * @FOSRest\QueryParam(name="data_nascimento_final",         strict=false, nullable=true,    description="Data de nascimento final")
     *
     * @FOSRest\Get("/aluno/listar-header")
     *
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function listaHeader(ParamFetcher $request)
    {
        $parametros = $request->all();
        $mensagem   = "";
        $resultados = $this->alunoFacade->listarHeader($mensagem, $parametros);
        if ($resultados === false) {
            return ResponseFactory::badRequest([], $mensagem);
        }

        return ResponseFactory::ok($resultados);
    }


    /**
     *
     * @SWG\Get(
     *     path="/api/aluno/{id}",
     *     summary="Buscar o aluno",
     *     description="Busca o aluno através da ID",
     *     tags={"Aluno"},
     *     produces={"application/json"},
     * @SWG\Response(
     *         response="200",
     *         description="Retorna o aluno"
     *     ),
     * @SWG\Response(
     *         response="404",
     *         description="Ocorreu algum erro no servidor",
     *     )
     * )
     *
     * @FOSRest\QueryParam(name="apenas_proxima_licao", strict=true, allowBlank=false, default="0", description="Traz apenas a proxima licao", requirements="(0|1)")
     *
     * @FOSRest\Get("/aluno/{id}")
     *
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function buscar($id, ParamFetcher $request)
    {
        $mensagem  = "";
        $objetoORM = $this->alunoFacade->buscarPorId($mensagem, $id, (bool) $request->get("apenas_proxima_licao"));
        if (is_null($objetoORM) === true) {
            return ResponseFactory::notFound([], $mensagem);
        }

        return ResponseFactory::ok(['item' => $objetoORM]);
    }

    /**
     *
     * @SWG\Get(
     *     path="/api/aluno/buscar-nome/{query}",
     *     summary="Buscar aluno por nome",
     *     description="Busca alunos pelo nome",
     *     tags={"Aluno"},
     *     produces={"application/json"},
     * @SWG\Response(
     *         response="200",
     *         description="Retorna os alunos"
     *     ),
     * )
     *
     * @FOSRest\Get("/aluno/buscar-nome/{query}")
     *
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function buscarNome ($query)
    {
        $alunos = $this->alunoFacade->buscarPorNome($query);

        return ResponseFactory::ok($alunos);
    }

    /**
     *
     * @SWG\Get(
     *     path="/api/aluno/buscar_pessoa_por_nome/{nome_pessoa}",
     *     summary="Buscar lista das pessoas dos alunos por nome",
     *     description="Busca alunos pelo nome",
     *     tags={"Aluno"},
     *     produces={"application/json"},
     * @SWG\Response(
     *         response="200",
     *         description="Retorna os alunos"
     *     ),
     * )
     *
     * @FOSRest\QueryParam(name="franqueada", strict=true, allowBlank=false, nullable=false, description="Id Franqueada",requirements="\d+")
     *
     * @FOSRest\Get("/aluno/buscar_pessoa_por_nome/{nome_pessoa}")
     *
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function buscarPessoaPorNome ($nome_pessoa, ParamFetcher $request)
    {
        $parametros = $request->all();
        $alunos     = $this->alunoFacade->buscarPessoaPorNome($nome_pessoa, $parametros[ConstanteParametros::CHAVE_FRANQUEADA]);

        return ResponseFactory::ok($alunos);
    }

    /**
     *
     * @SWG\Get(
     *      path="/api/aluno/buscar_nome_com_contrato/{query}",
     *      summary="Buscar aluno com contrato por nome",
     *      description="Buscar aluno com contrato por nome",
     *      tags={"Aluno"},
     *      produces={"application/json"},
     * @SWG\Response(
     *         response="200",
     *         description="Retorna os alunos"
     *     ),
     * )
     *
     * @FOSRest\QueryParam(name="buscar_apenas_contrato_ativo", strict=true, allowBlank=false, default="0", description="Apenas contrato ativo", requirements="(0|1)")
     *
     * @FOSRest\Get("/aluno/buscar_nome_com_contrato/{query}")
     *
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function buscarNomeComContrato ($query, ParamFetcher $paramFetcher)
    {
        $parametros = $paramFetcher->getParams();
        $aluno      = $this->alunoFacade->buscarAlunoPorNomeComContrato($query, $parametros);

        return ResponseFactory::ok($aluno);
    }

        /**
     *
     * @SWG\Get(
     *      path="/api/aluno/buscar_nome_com_contrato_todos/{query}",
     *      summary="Buscar aluno com contrato por nome",
     *      description="Buscar aluno com contrato por nome",
     *      tags={"Aluno"},
     *      produces={"application/json"},
     * @SWG\Response(
     *         response="200",
     *         description="Retorna os alunos"
     *     ),
     * )
     *
     * @FOSRest\QueryParam(name="buscar_apenas_contrato_ativo", strict=true, allowBlank=false, default="0", description="Apenas contrato ativo", requirements="(0|1)")
     *
     * @FOSRest\Get("/aluno/buscar_nome_com_contrato_todos/{query}")
     *
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function buscarNomeComContratoTodos ($query, ParamFetcher $paramFetcher)
    {
        $parametros = $paramFetcher->getParams();
        $aluno      = $this->alunoFacade->buscarAlunoPorNomeComContratoTodos($query, $parametros);

        return ResponseFactory::ok($aluno);
    }

    /**
     *
     * @SWG\Get(
     *      path="/api/aluno/buscar_nome_com_contrato_ativo/{query}",
     *      summary="Buscar aluno com contrato por nome",
     *      description="Buscar aluno com contrato por nome",
     *      tags={"Aluno"},
     *      produces={"application/json"},
     * @SWG\Response(
     *         response="200",
     *         description="Retorna os alunos"
     *     ),
     * )
     *
     * @FOSRest\QueryParam(name="buscar_apenas_contrato_ativo", strict=true, allowBlank=false, default="1", description="Apenas contrato ativo", requirements="(0|1)")
     *
     * @FOSRest\Get("/aluno/buscar_nome_com_contrato_ativo/{query}")
     *
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function buscarNomeComContratoAtivo ($query, ParamFetcher $paramFetcher)
    {
        $parametros = $paramFetcher->getParams();
        $aluno      = $this->alunoFacade->buscarAlunoPorNomeComContrato($query, $parametros);

        return ResponseFactory::ok($aluno);
    }

    /**
     *
     * @SWG\Get(
     *     path="/api/aluno/buscar-nome-cpf/{query}",
     *     summary="Buscar aluno por nome",
     *     description="Busca alunos pelo nome",
     *     tags={"Aluno"},
     *     produces={"application/json"},
     * @SWG\Response(
     *         response="200",
     *         description="Retorna os alunos"
     *     ),
     * )
     *
     * @FOSRest\Get("/aluno/buscar-nome-cpf/{query}")
     *
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function buscarNomeCpf ($query)
    {
        $alunos = $this->alunoFacade->buscarPorNomeCpf($query);

        return ResponseFactory::ok($alunos);
    }

    /**
     *
     * @SWG\Get(
     *     path="/api/aluno/buscar-cpf/{query}",
     *     summary="Buscar aluno por cpf",
     *     description="Busca alunos pelo cpf",
     *     tags={"Aluno"},
     *     produces={"application/json"},
     * @SWG\Response(
     *         response="200",
     *         description="Retorna os alunos"
     *     ),
     * )
     *
     * @FOSRest\Get("/aluno/buscar-cpf/{query}")
     *
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function buscarCpf ($query)
    {
        $alunos = $this->alunoFacade->buscarPorCpf($query);

        return ResponseFactory::ok($alunos);
    }

    /**
     *
     * @SWG\Post(
     *     path="/api/aluno/criar",
     *     summary="Cria um aluno",
     *     description="Cria um aluno no banco",
     *     tags={"Aluno"},
     *     consumes={"application/form-data"},
     *     produces={"application/json"},
     * @SWG\Response(
     *         response="201",
     *         description="Retorna criado com sucesso"
     *     ),
     * @SWG\Response(
     *         response="202",
     *         description="Ocorreu algum erro no servidor",
     *     )
     * )
     *
     * @FOSRest\FileParam(name="foto_arquivo",                                   strict=false, requirements={"mimeTypes"={"image/jpeg", "image/png"}})
     * @FOSRest\RequestParam(name="pessoa",                                      strict=true, nullable=false, description="ID do cadastro da tabela Pessoa", requirements="\d+")
     * @FOSRest\RequestParam(name="interessado",                                 strict=false, nullable=true, description="ID do cadastro da tabela Interessado", requirements="\d+")
     * @FOSRest\RequestParam(name="classificacao_aluno",                         strict=true, nullable=false, description="ID do cadastro da tabela Classificacao", requirements="\d+")
     * @FOSRest\RequestParam(name="escolaridade",                                strict=false, nullable=true, description="ID do cadastro da tabela Escolaridade", requirements="\d+")
     * @FOSRest\RequestParam(name="responsavel_financeiro_pessoa",               strict=false, nullable=true, description="ID do cadastro da tabela Pessoa", requirements="\d+")
     * @FOSRest\RequestParam(name="responsavel_financeiro_relacionamento_aluno", strict=false, nullable=true, description="ID do cadastro da tabela RelacionamentoAluno", requirements="\d+")
     * @FOSRest\RequestParam(name="responsavel_didatico_pessoa",                 strict=false, nullable=true, description="ID do cadastro da tabela Pessoa", requirements="\d+")
     * @FOSRest\RequestParam(name="responsavel_didatico_relacionamento_aluno",   strict=false, nullable=true, description="ID do cadastro da tabela RelacionamentoAluno", requirements="\d+")
     * @FOSRest\RequestParam(name="emancipado",                                  strict=true, nullable=false, description="Marca o aluno como emancipado", default="0", requirements="\d")
     *
     * @FOSRest\RequestParam(name="tipo_visibilidade", strict=true, nullable=false, description="Map de ID de midia", map=true)
     *
     * @FOSRest\Post("/aluno/criar")
     *
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function criar(ParamFetcher $paramFetcher)
    {
        $parametros = $paramFetcher->all();
        $parametros[ConstanteParametros::CHAVE_SITUACAO] = SituacoesSistema::ALUNO_INTERESSADO;
        $mensagem  = "";

        $pessoaId = $parametros[ConstanteParametros::CHAVE_PESSOA];
        $alunoORM = $this->alunoFacade->buscarPorPessoa($pessoaId);


        if($alunoORM == null){

            $alunoORM = $this->alunoFacade->criar($mensagem, $parametros);
            if ((is_null($alunoORM) === true) || (empty($mensagem) === false)) {
                return ResponseFactory::badRequest(["parametros" => $parametros], $mensagem);
            }
        }


        return ResponseFactory::created(["aluno" => $alunoORM->getId()], "Registro criado com sucesso!");
    }

    /**
     *
     * @SWG\Post(
     *     path="/api/aluno/atualizar/{id}",
     *     summary="Atualiza um aluno",
     *     description="Atualiza um aluno no banco",
     *     tags={"Aluno"},
     *     consumes={"application/form-data"},
     *     produces={"application/json"},
     * @SWG\Response(
     *         response="204",
     *         description="Retorna criado com sucesso"
     *     ),
     * @SWG\Response(
     *         response="400",
     *         description="Ocorreu algum erro no servidor",
     *     )
     * )
     *
     * @FOSRest\FileParam(name="foto_arquivo",                                   strict=false, nullable=true, requirements={"mimeTypes"={"image/jpeg", "image/jpg", "image/png"}})
     * @FOSRest\RequestParam(name="foto",                                        strict=false, nullable=true, description="Caminho da foto existente")
     * @FOSRest\RequestParam(name="classificacao_aluno",                         strict=false, nullable=true, description="ID do cadastro da tabela Classificacao", requirements="\d+")
     * @FOSRest\RequestParam(name="interessado",                                 strict=false, nullable=true, description="ID do cadastro da tabela Interessado", requirements="\d+")
     * @FOSRest\RequestParam(name="escolaridade",                                strict=false, nullable=true, description="ID do cadastro da tabela Escolaridade", requirements="\d+")
     * @FOSRest\RequestParam(name="responsavel_financeiro_pessoa",               strict=false, nullable=true, description="ID do cadastro da tabela Pessoa", requirements="\d+")
     * @FOSRest\RequestParam(name="responsavel_financeiro_relacionamento_aluno", strict=false, nullable=true, description="ID do cadastro da tabela RelacionamentoAluno", requirements="\d+")
     * @FOSRest\RequestParam(name="responsavel_didatico_pessoa",                 strict=false, nullable=true, description="ID do cadastro da tabela Pessoa", requirements="\d+")
     * @FOSRest\RequestParam(name="responsavel_didatico_relacionamento_aluno",   strict=false, nullable=true, description="ID do cadastro da tabela RelacionamentoAluno", requirements="\d+")
     * @FOSRest\RequestParam(name="emancipado",                                  strict=false, nullable=true, description="Marca o aluno como emancipado", default="0", requirements="\d")
     * @FOSRest\RequestParam(name="situacao",                                    strict=false, nullable=false, description="Situação do Aluno", default="ATI", requirements="(ATI|BOL|REN|INA|LEA|FOR|DES|TRA|CAN|MUD)")
     *
     * @FOSRest\RequestParam(name="tipo_visibilidade", strict=false, nullable=true, description="Map de ID de midia", map=true)
     *
     * @FOSRest\Post("/aluno/atualizar/{id}")
     *
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function atualizar($id, ParamFetcher $paramFetcher)
    {
        $parametros = $paramFetcher->all();
        $mensagem   = "";
        $retorno    = $this->alunoFacade->atualizar($mensagem, $id, $parametros);
        if (($retorno === false) || (empty($mensagem) === false)) {
            return ResponseFactory::badRequest(["parametros" => $parametros], $mensagem);
        }

        return ResponseFactory::noContent([]);
    }

    /**
     *
     * @SWG\Delete(
     *     path="/api/aluno/remover/{id}",
     *     summary="Remove um aluno",
     *     description="Remove um aluno no banco",
     *     tags={"Aluno"},
     *     produces={"application/json"},
     * @SWG\Response(
     *         response="200",
     *         description="Retorna excluido com sucesso"
     *     ),
     * @SWG\Response(
     *         response="400",
     *         description="Ocorreu algum erro no servidor",
     *     )
     * )
     *
     * @FOSRest\Delete("/aluno/remover/{id}")
     *
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function excluir($id)
    {
        $mensagem = "";
        $retorno  = $this->alunoFacade->remover($mensagem, $id);
        if ($retorno === false) {
            return ResponseFactory::badRequest([], $mensagem);
        }

        return ResponseFactory::ok([], "Excluido com sucesso");
    }

    /**
     *
     * @SWG\Post(
     *     path="/api/aluno/transfere_turma/{alunoId}",
     *     summary="Transfere um aluno",
     *     description="Transfere um aluno",
     *     tags={"Aluno"},
     *     consumes={"application/form-data"},
     *     produces={"application/json"},
     * @SWG\Response(
     *         response="200",
     *         description="Retorna Transferido com sucesso"
     *     ),
     * @SWG\Response(
     *         response="202",
     *         description="Ocorreu algum erro no servidor",
     *     )
     * )
     *
     * @FOSRest\RequestParam(name="ignora_validacao_qtd_max_alunos", strict=true, nullable=false, description="verifica se deve pular a validação de alunos", requirements="(0|1)", default="0")
     * @FOSRest\RequestParam(name="turma_destino",                   strict=true, nullable=false, description="turma de destino", requirements="\d+")
     * @FOSRest\RequestParam(name="franqueada",                      strict=true, nullable=false, description="Franqueada ID", requirements="\d+")
     * @FOSRest\RequestParam(name="aluno",                           strict=true, nullable=false, description="Aluno ID", requirements="\d+")
     *
     * @FOSRest\Post("/aluno/transfere_turma/{contratoId}")
     *
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function transfereAluno($contratoId, ParamFetcher $paramFetcher, Request $request)
    {
        $parametros       = $paramFetcher->all();
        $usuarioId        = $request->headers->get('Authorization-User-ID');
        $mensagem         = "";
        $contratoAlunoORM = $this->contratoRepository->find($contratoId);
        $turmaDestinoORM  = $this->turmaRepository->find($parametros[ConstanteParametros::CHAVE_TURMA_DESTINO]);
        $nomeTurmaAtual   = $contratoAlunoORM->getTurma()->getDescricao();
        $nomeTurmaDestino = $turmaDestinoORM->getDescricao();
        // $texto = "Aluno foi transferido para a turma com id:" . $parametros[ConstanteParametros::CHAVE_TURMA_DESTINO];
        $texto = "****ALTERAÇÃO DE TURMA****\nAluno(a) mudou da turma \"" . $nomeTurmaAtual . "\" para a turma \"" . $nomeTurmaDestino . "\".";
        if ($this->gerarOcorrenciaAcademica($mensagem, $parametros[ConstanteParametros::CHAVE_FRANQUEADA], $parametros[ConstanteParametros::CHAVE_ALUNO], $usuarioId, SituacoesSistema::TIPO_OCORRENCIA_TIPO_ITEM_TRANSFERENCIA_TURMAS, $texto) === false) {
            return ResponseFactory::badRequest(["alunoController_gerar-ocorrencia-academica" => $parametros], $mensagem);
        }

        $objetoORM = $this->alunoFacade->transfereAluno($mensagem, $contratoId, $parametros);
        if ((is_null($objetoORM) === true) || (empty($mensagem) === false)) {
            return ResponseFactory::badRequest(["parametros" => $parametros], $mensagem);
        }

        self::getEntityManager()->flush();

        return ResponseFactory::ok([], "Aluno transferido com sucesso!");
    }


}
