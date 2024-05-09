<?php

namespace App\Controller\Principal\Interessado;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use FOS\RestBundle\Controller\Annotations as FOSRest;
use Swagger\Annotations as SWG;
use App\Factory\ResponseFactory;
use App\Controller\Principal\Base\GenericController;
use FOS\RestBundle\Request\ParamFetcher;
use App\Facade\Principal\InteressadoFacade;
use App\Facade\Principal\PessoaFacade;
use App\Facade\Principal\UsuarioFacade;
use App\Facade\Principal\OcorrenciaAcademicaFacade;
use App\Helper\ConstanteParametros;
use Symfony\Component\HttpFoundation\Request;
use App\Facade\Principal\FollowupComercialFacade;
use App\Facade\Principal\AgendaComercialFacade;
use App\Helper\SituacoesSistema;
use App\Facade\Principal\ConvenioFacade;

/**
 *
 * @author        Luiz A Costa
 * @Route("/api")
 */
class InteressadoController extends GenericController
{

    /**
     *
     * @var \App\Facade\Principal\InteressadoFacade
     */
    private $interessadoFacade;

    /**
     *
     * @var \App\Facade\Principal\PessoaFacade
     */
    private $pessoaFacade;

        /**
     *
     * @var \App\Facade\Principal\UsuarioFacade
     */
    private $usuarioFacade;

    /**
     *
     * @var \App\Facade\Principal\OcorrenciaAcademicaFacade
     */
    private $ocorrenciaAcademicaFacade;

    /**
     *
     * @var \App\Facade\Principal\FollowupComercialFacade
     */
    private $followupComercialFacade;

    /**
     *
     * @var \App\Facade\Principal\AgendaComercialFacade
     */
    private $agendaComercialFacade;

    /**
     *
     * @var \App\Facade\Principal\ConvenioFacade
     */
    private $convenioFacade;

    /**
     * {@inheritdoc}
     */
    protected function constroiFacades()
    {
        // Para criar o LogFacade do GenericController
        parent::constroiFacades();
        $this->interessadoFacade       = new InteressadoFacade(self::getManagerRegistry());
        $this->pessoaFacade            = new PessoaFacade(self::getManagerRegistry());
        $this->usuarioFacade            = new UsuarioFacade(self::getManagerRegistry());
        $this->followupComercialFacade = new FollowupComercialFacade(self::getManagerRegistry());
        $this->agendaComercialFacade   = new AgendaComercialFacade(self::getManagerRegistry());
        $this->convenioFacade          = new ConvenioFacade(self::getManagerRegistry());
        $this->ocorrenciaAcademicaFacade = new OcorrenciaAcademicaFacade(self::getManagerRegistry());
    }


    /**
     *
     * @SWG\Get(
     *     path="/api/interessado/listar_funil_vendas",
     *     summary="Listar interessado",
     *     description="Lista as interessado do banco",
     *     produces={"application/json"},
     * @SWG\Response(
     *         response="200",
     *         description="Retorna os interessado"
     *     ),
     * @SWG\Response(
     *         response="400",
     *         description="Ocorreu algum erro no servidor",
     *     )
     * )
     *
     * @FOSRest\QueryParam(name="franqueada",          strict=true, nullable=false, description="ID da franqueada", requirements="\d+")
     * @FOSRest\QueryParam(name="consultor_comercial", strict=false, nullable=true, description="ID do consultor específico para consulta", requirements="\d+")
     * @FOSRest\QueryParam(name="data_agendamento",    strict=false, nullable=true, description="Data de Agendamento", default="")
     *
     * @FOSRest\Get("/interessado/listar_funil_vendas")
     *
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function listaFunilVendas(ParamFetcher $paramFetcher, Request $request)
    {
        $parametros = $paramFetcher->all();
        $mensagem   = "";
        $usuarioId  = $request->headers->get('Authorization-User-ID');
        
       //buscar papel usuario
       if (!$parametros['consultor_comercial']) {
            $usuarioORM = $this->usuarioFacade->buscarUsuario($usuarioId);

            $papeis = $usuarioORM['papels'];
            $soConsultor = true;
            //verifica se ele é só consultor de vendas
            foreach ($papeis as $papel) {
            // var_dump($papel);
                if (($papel['id'] != 3) && ($papel['id'] != 11) ) {
                    $soConsultor = false; // Se encontrar um papel indesejado, retorna falso
                }
            }

            if ($soConsultor === true) {
                $parametros['consultor_comercial'] = $usuarioId;
            }
        }
        
        $resultados = $this->interessadoFacade->listaFunilVendas($usuarioId, $parametros);
        if ($resultados === false) {
            return ResponseFactory::badRequest([], $mensagem);
        }

        $resultadosInteressadoAtrasados = $this->interessadoFacade->listaFunilVendasAtrasado($usuarioId, $parametros);

        $resultadosOcorrenciasAcademicas = $this->ocorrenciaAcademicaFacade->listaFunilVendas($usuarioId, $parametros);
        if ($resultadosOcorrenciasAcademicas === false) {
            return ResponseFactory::badRequest([], $mensagem);
        }

        $resultadosOcorrenciasAcademicasAtrasado = $this->ocorrenciaAcademicaFacade->listaFunilVendasAtrasado($usuarioId, $parametros);

        $resultadosConvenio = $this->convenioFacade->listaFunilVendas($usuarioId, $parametros);
        if ($resultadosConvenio === false) {
            return ResponseFactory::badRequest([], $mensagem);
        }

        $resultadosConvenioAtrasados = $this->convenioFacade->listaFunilVendasAtrasado($usuarioId, $parametros);

        return ResponseFactory::ok(
            [
                "interessados"                  => $resultados,
                "convenios"                     => $resultadosConvenio,
                "ocorrenciasAcademicas"         => $resultadosOcorrenciasAcademicas,
                "interessadosAtrasados"         => $resultadosInteressadoAtrasados,
                "conveniosAtrasados"            => $resultadosConvenioAtrasados,
                "ocorrenciasAcademicasAtrasado" => $resultadosOcorrenciasAcademicasAtrasado,
            ]
        );
    }


    /**
     *
     * @SWG\Get(
     *     path="/api/interessado/checar_telefones_cadastrados",
     *     summary="Busca se algum dos telefones está cadastrado",
     *     description="Buscar se algum dos telefones passados está cadastrado em algum interessado ou pessoa",
     *     tags={"Interessado"},
     *     produces={"application/json"},
     * @SWG\Response(
     *         response="200",
     *         description="Retorna se já está cadastrado"
     *     ),
     * )
     *
     * @FOSRest\Get("/interessado/checar_telefones_cadastrados")
     *
     * @FOSRest\QueryParam(name="franqueada", strict=true, nullable=false, description="ID da franqueada", requirements="\d+")
     * @FOSRest\QueryParam(name="telefone",   strict=false, nullable=false, allowBlank=false, description="Lista de telefones", map=true)
     *
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function buscarTelefoneEstaCadastrado (ParamFetcher $request)
    {
        $parametros = $request->all();
        $mensagem   = "";
        $telefonesCadastrados = [];
        foreach ($parametros[ConstanteParametros::CHAVE_TELEFONE] as $telefone) {
            $params = [
                ConstanteParametros::CHAVE_TELEFONE   => $telefone,
                ConstanteParametros::CHAVE_FRANQUEADA => $parametros[ConstanteParametros::CHAVE_FRANQUEADA],
            ];
            if ($this->interessadoFacade->buscarTelefoneEstaCadastrado($mensagem, $params) === true) {
                $telefonesCadastrados[] = $telefone;
                continue;
            }

            if ($this->pessoaFacade->buscarTelefoneEstaCadastrado($mensagem, $params) === true) {
                $telefonesCadastrados[] = $telefone;
                continue;
            }
        }

        if (empty($mensagem) === false) {
            return ResponseFactory::badRequest([], $mensagem);
        }

        $response = ["telefones_cadastrados" => $telefonesCadastrados];

        return ResponseFactory::ok($response);
    }

    /**
     *
     * @SWG\Get(
     *     path="/api/interessado/listar",
     *     summary="Listar interessado",
     *     description="Lista as interessado do banco",
     *     produces={"application/json"},
     * @SWG\Response(
     *         response="200",
     *         description="Retorna os interessado"
     *     ),
     * @SWG\Response(
     *         response="400",
     *         description="Ocorreu algum erro no servidor",
     *     )
     * )
     *
     * @FOSRest\QueryParam(name="pagina",                      strict=false, allowBlank=false, default="1", description="Pagina para realizar o scroll", requirements="\d+")
     * @FOSRest\QueryParam(name="franqueada",                  strict=true, nullable=false, description="ID da franqueada", requirements="\d+")
     * @FOSRest\QueryParam(name="idioma",                      strict=false, nullable=true, description="ID do idioma", requirements="\d+")
     * @FOSRest\QueryParam(name="consultor",                   strict=false, nullable=true, description="ID do consultor", requirements="\d+")
     * @FOSRest\QueryParam(name="idade",                       strict=false, nullable=true, description="Idade", requirements="\d+")
     * @FOSRest\QueryParam(name="nome",                        strict=false, nullable=true, description="Nome do interessado")
     * @FOSRest\QueryParam(name="interessado",                 strict=false, nullable=true, description="Interessado", requirements="\d+")
     * @FOSRest\QueryParam(name="pessoa_indicou",              strict=false, nullable=true, description="pessoa_indicou")
     * @FOSRest\QueryParam(name="telefone",                    strict=false, nullable=true, description="Telefone")
     * @FOSRest\QueryParam(name="email",                       strict=false, nullable=true, description="Email")
     * @FOSRest\QueryParam(name="data_cadastro_de",            strict=false, nullable=true, description="Data Cadastro De")
     * @FOSRest\QueryParam(name="data_cadastro_ate",           strict=false, nullable=true, description="Data Cadastro Ate")
     * @FOSRest\QueryParam(name="data_validade_promocao_de",   strict=false, nullable=true, description="Data validade promoção de")
     * @FOSRest\QueryParam(name="data_validade_promocao_ate",  strict=false, nullable=true, description="Data validade promoção ate")
     * @FOSRest\QueryParam(name="data_proximo_contato_de",     strict=false, nullable=true, description="Data Proximo Contato De")
     * @FOSRest\QueryParam(name="data_proximo_contato_ate",    strict=false, nullable=true, description="Data Proximo Contato Ate")
     * @FOSRest\QueryParam(name="horario_proximo_contato_de",  strict=false, nullable=true, description="Hora proximo Contato De")
     * @FOSRest\QueryParam(name="horario_proximo_contato_ate", strict=false, nullable=true, description="Hora proximo Contato Ate")
     * @FOSRest\QueryParam(name="grau_interesse",              strict=false, nullable=true, allowBlank=true, description="Grau de interesse", map=true, requirements="(L|I|H)")
     * @FOSRest\QueryParam(name="tipo_lead",                   strict=false, nullable=true, allowBlank=true, description="Lista de idiomas selecionados", map=true, requirements="(A|R)")
     * @FOSRest\QueryParam(name="situacao",                    strict=false, nullable=true, allowBlank=true, description="Situacao do Interessado", map=true, requirements="(A|C|P)")
     * @FOSRest\QueryParam(name="tipo_contato",                strict=false, nullable=true, allowBlank=true, description="Forma de contato", requirements="\d+")
     * @FOSRest\QueryParam(name="tipo_prospeccao",             strict=false, nullable=true, allowBlank=true, description="Forma prospeccao", requirements="\d+")
     * @FOSRest\QueryParam(name="periodo_pretendido",          strict=false, nullable=true, allowBlank=true, description="Perido pretendido")
     * @FOSRest\QueryParam(name="workflow",                    strict=false, nullable=true, allowBlank=true, description="Etapa do funil", requirements="\d+")
     * @FOSRest\QueryParam(name="motivo_nao_fechamento",       strict=false, nullable=true, allowBlank=true, description="Motivo não fechamento da matricula", requirements="\d+")
     * @FOSRest\QueryParam(name="order",                       strict=false, nullable=true,  description="Coluna de ordenação")
     * @FOSRest\QueryParam(name="direcao",                     strict=false, nullable=true,  description="ASC|DESC")
     *
     * @FOSRest\Get("/interessado/listar")
     *
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function lista(ParamFetcher $request)
    {
        $parametros = $request->all();
        $mensagem   = "";
        $resultados = $this->interessadoFacade->listar($parametros, $mensagem);
        if ($resultados === false) {
            return ResponseFactory::badRequest([], $mensagem);
        }

        return ResponseFactory::ok($resultados);
    }

    /**
     *
     * @SWG\Get(
     *     path="/api/interessado/{id}",
     *     summary="Buscar a interessado",
     *     description="Busca a interessado através da ID",
     *     produces={"application/json"},
     * @SWG\Response(
     *         response="200",
     *         description="Retorna a interessado"
     *     ),
     * @SWG\Response(
     *         response="404",
     *         description="Ocorreu algum erro no servidor",
     *     )
     * )
     *
     * @FOSRest\Get("/interessado/{id}")
     *
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function buscar($id)
    {
        $mensagemErro = "";
        $objetoORM    = $this->interessadoFacade->buscarPorId($mensagemErro, $id);
        if (is_null($objetoORM) === true) {
            return ResponseFactory::notFound([], $mensagemErro);
        }

        return ResponseFactory::ok($objetoORM);
    }

    /**
     *
     * @SWG\Get(
     *     path="/api/interessado/buscar-nome/{query}",
     *     summary="Buscar interessado por nome",
     *     description="Busca interessado pelo nome",
     *     tags={"Interessado"},
     *     produces={"application/json"},
     * @SWG\Response(
     *         response="200",
     *         description="Retorna os interessados"
     *     ),
     * )
     *
     * @FOSRest\Get("/interessado/buscar-nome/{query}")
     *
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function buscarNome ($query)
    {
        $interessado = $this->interessadoFacade->buscarPorNome($query);

        return ResponseFactory::ok($interessado);
    }


    /**
     *
     * @SWG\Get(
     *     path="/api/interessado/buscar-nome-telefone/{query}",
     *     summary="Buscar interessado por nome ou telefone ",
     *     description="Busca interessado pelo nome ou telefone primario o secundario ",
     *     tags={"Interessado"},
     *     produces={"application/json"},
     * @SWG\Response(
     *         response="200",
     *         description="Retorna os interessados"
     *     ),
     * )
     *
     * @FOSRest\Get("/interessado/buscar-nome-telefone/{query}")
     *
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function buscarNomeTelefone ($query)
    {
        $interessado = $this->interessadoFacade->buscarPorNomeOuTelefone($query);

        return ResponseFactory::ok($interessado);
    }

    /**
     *
     * @SWG\Post(
     *     path="/api/interessado/criar",
     *     summary="Cria uma interessado",
     *     description="Cria uma interessado no banco",
     *     consumes={"application/x-www-form-urlencoded"},
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
     * @FOSRest\RequestParam(name="franqueada",                        strict=true, nullable=false, description="ID da franqueada", requirements="\d+")
     * @FOSRest\RequestParam(name="consultor_funcionario",             strict=true, nullable=false, allowBlank=true, description="Consultor Funcionario", requirements="\d+")
     * @FOSRest\RequestParam(name="pessoa_indicou",                    strict=false, nullable=true, allowBlank=true, description="pessoa_indicou")
     * @FOSRest\RequestParam(name="consultor_responsavel_funcionario", strict=false, nullable=true, allowBlank=true, description="Consultor Responsavel Funcionario", requirements="\d+")
     * @FOSRest\RequestParam(name="curso",                             strict=false, nullable=true, allowBlank=true, description="id do curso oferecido", requirements="\d+")
     * @FOSRest\RequestParam(name="idade",                             strict=false, nullable=true, allowBlank=true, description="Idade", requirements="^\d{0,3}")
     * @FOSRest\RequestParam(name="tipo_lead",                         strict=false, nullable=true, allowBlank=true, description="Tipo Lead", requirements="(A|R)")
     * @FOSRest\RequestParam(name="tipo_contato",                      strict=false, nullable=true, allowBlank=true, description="Id da mensagem tipo contato", requirements="\d+")
     * @FOSRest\RequestParam(name="tipo_prospeccao",                   strict=false, nullable=true, allowBlank=true, description="Id da mensagem tipo prospecção", requirements="\d+")
     * @FOSRest\RequestParam(name="nome",                              strict=true, nullable=false, allowBlank=false, description="Nome", requirements="^.{0,150}")
     * @FOSRest\RequestParam(name="email_contato",                     strict=false, nullable=true, allowBlank=true, description="Email de Contato", requirements="^.{0,255}")
     * @FOSRest\RequestParam(name="email_secundario",                  strict=false, nullable=true, allowBlank=true, description="Email Secundario", requirements="^.{0,255}")
     * @FOSRest\RequestParam(name="sexo",                              strict=false, nullable=true, allowBlank=true, description="Sexo", requirements="(M|F)")
     * @FOSRest\RequestParam(name="telefone_contato",                  strict=false, nullable=true, allowBlank=true, description="Telefone")
     * @FOSRest\RequestParam(name="telefone_secundario",               strict=false, nullable=true, allowBlank=true, description="Telefone Secundario")
     * @FOSRest\RequestParam(name="data_proximo_contato",              strict=false, nullable=true, allowBlank=true, description="Proximo contato")
     * @FOSRest\RequestParam(name="horario_proximo_contato",           strict=false, nullable=true, allowBlank=true, description="Horario do proximo contato")
     * @FOSRest\RequestParam(name="periodo_pretendido",                strict=false, nullable=true, allowBlank=true, description="Período pretendido")
     * @FOSRest\RequestParam(name="data_validade_promocao",            strict=false, nullable=true, allowBlank=true, description="Validade da promoção")
     *
     * @FOSRest\RequestParam(name="idiomas", strict=true, nullable=true, allowBlank=true, description="Lista de idiomas selecionados", map=true, requirements="\d+")
     *
     * @FOSRest\Post("/interessado/criar")
     *
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function criar(ParamFetcher $paramFetcher, Request $request)
    {
        $parametros = $paramFetcher->all();
        $mensagem   = "";
        $objetoORM  = $this->interessadoFacade->criar($mensagem, $parametros);

        if ((is_null($objetoORM) === true) || (empty($mensagem) === false)) {
            return ResponseFactory::conflict(["parametros" => $parametros], $mensagem);
        }

        return ResponseFactory::created(["objetoORM" => $objetoORM->getId()], "Registro criado com sucesso!");
    }

    /**
     *
     * @SWG\Patch(
     *     path="/api/interessado/atualizar/{id}",
     *     summary="Atualiza um interessado",
     *     description="Atualiza um interessado no banco",
     *     consumes={"application/x-www-form-urlencoded"},
     *     produces={"application/json"},
     * @SWG\Response(
     *         response="204",
     *         description="Retorna atualizado com sucesso"
     *     ),
     * @SWG\Response(
     *         response="400",
     *         description="Ocorreu algum erro no servidor",
     *     )
     * )
     *
     * @FOSRest\RequestParam(name="franqueada",                        strict=true, nullable=false, description="ID da franqueada", requirements="\d+")
     * @FOSRest\RequestParam(name="workflow_acao",                     strict=false, nullable=true, allowBlank=true, description="Workflow AcaoID", requirements="\d+")
     * @FOSRest\RequestParam(name="curso",                             strict=false, nullable=true, allowBlank=true, description="id do curso oferecido", requirements="\d+")
     * @FOSRest\RequestParam(name="consultor_funcionario",             strict=true, nullable=false, allowBlank=true, description="Consultor Funcionario", requirements="\d+")
     * @FOSRest\RequestParam(name="consultor_responsavel_funcionario", strict=false, nullable=true, allowBlank=true, description="Consultor Responsavel Funcionario", requirements="\d+")
     * @FOSRest\RequestParam(name="idade",                             strict=false, nullable=true, allowBlank=true, description="Idade", requirements="^\d{0,3}")
     * @FOSRest\RequestParam(name="tipo_lead",                         strict=false, nullable=true, allowBlank=true, description="Tipo Lead", requirements="(A|R)")
     * @FOSRest\RequestParam(name="tipo_contato",                      strict=false, nullable=true, allowBlank=true, description="Id da mensagem tipo contato", requirements="\d+")
     * @FOSRest\RequestParam(name="tipo_prospeccao",                   strict=false, nullable=true, allowBlank=true, description="Id da mensagem tipo prospecção", requirements="\d+")
     * @FOSRest\RequestParam(name="nome",                              strict=true, nullable=false, allowBlank=false, description="Nome", requirements="^.{0,150}")
     * @FOSRest\RequestParam(name="email_contato",                     strict=false, nullable=true, allowBlank=true, description="Email de Contato", requirements="^.{0,255}")
     * @FOSRest\RequestParam(name="email_secundario",                  strict=false, nullable=true, allowBlank=true, description="Email Secundario", requirements="^.{0,255}")
     * @FOSRest\RequestParam(name="sexo",                              strict=false, nullable=true, allowBlank=true, description="Sexo", requirements="(M|F)")
     * @FOSRest\RequestParam(name="telefone_contato",                  strict=false, nullable=true, allowBlank=true, description="Telefone")
     * @FOSRest\RequestParam(name="telefone_secundario",               strict=false, nullable=true, allowBlank=true, description="Telefone Secundario")
     * @FOSRest\RequestParam(name="data_proximo_contato",              strict=false, nullable=true, allowBlank=true, description="Proximo contato")
     * @FOSRest\RequestParam(name="horario_proximo_contato",           strict=false, nullable=true, allowBlank=true, description="Horario do proximo contato")
     * @FOSRest\RequestParam(name="periodo_pretendido",                strict=false, nullable=true, allowBlank=true, description="Período pretendido")
     * @FOSRest\RequestParam(name="data_validade_promocao",            strict=false, nullable=true, allowBlank=true, description="Validade da promoção")
     *
     * @FOSRest\RequestParam(name="idiomas", strict=true, nullable=true, allowBlank=true, description="Lista de idiomas selecionados", map=true, requirements="\d+")
     *
     * @FOSRest\Patch("/interessado/atualizar/{id}")
     *
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function atualizar($id, ParamFetcher $paramFetcher, Request $request)
    {
        $parametros = $paramFetcher->all();
        $mensagem   = "";
        $this->interessadoFacade->atualizar($mensagem, $id, $parametros);
           if ($mensagem != "") {
            return ResponseFactory::badRequest(["parametros" => $parametros], $mensagem);
        }
       
        return ResponseFactory::noContent([]);
    }

    /**
     *
     * @SWG\Patch(
     *     path="/api/interessado/followup/{id}",
     *     summary="Atualiza um followup",
     *     description="Atualiza um interessado no banco",
     *     consumes={"application/x-www-form-urlencoded"},
     *     produces={"application/json"},
     * @SWG\Response(
     *         response="204",
     *         description="Retorna atualizado com sucesso"
     *     ),
     * @SWG\Response(
     *         response="400",
     *         description="Ocorreu algum erro no servidor",
     *     )
     * )
     *
     * @FOSRest\RequestParam(name="franqueada",                        strict=true, nullable=false, description="ID da franqueada", requirements="\d+")
     * @FOSRest\RequestParam(name="formulario_follow_up",              strict=false, nullable=true, allowBlank=true, description="ID do formulario selecionado", requirements="\d+")
     * @FOSRest\RequestParam(name="motivo_nao_fechamento",             strict=false, nullable=true, allowBlank=true, description="ID do motivo matricula perdida", requirements="\d+")
     * @FOSRest\RequestParam(name="workflow_acao",                     strict=false, nullable=true, allowBlank=true, description="Workflow AcaoID", requirements="\d+")
     * @FOSRest\RequestParam(name="curso",                             strict=false, nullable=true, allowBlank=true, description="id do curso oferecido", requirements="\d+")
     * @FOSRest\RequestParam(name="consultor_funcionario",             strict=true, nullable=false, allowBlank=true, description="Consultor Funcionario", requirements="\d+")
     * @FOSRest\RequestParam(name="consultor_responsavel_funcionario", strict=false, nullable=true, allowBlank=true, description="Consultor Responsavel Funcionario", requirements="\d+")
     * @FOSRest\RequestParam(name="tipo_lead",                         strict=false, nullable=true, allowBlank=true, description="Tipo Lead", requirements="(A|R)")
     * @FOSRest\RequestParam(name="tipo_contato",                      strict=false, nullable=true, allowBlank=true, description="Id da mensagem tipo contato", requirements="\d+")
     * @FOSRest\RequestParam(name="tipo_prospeccao",                   strict=false, nullable=true, allowBlank=true, description="Id da mensagem tipo prospecção", requirements="\d+")
     * @FOSRest\RequestParam(name="nome",                              strict=true, nullable=false, allowBlank=false, description="Nome", requirements="^.{0,150}")
     * @FOSRest\RequestParam(name="email_contato",                     strict=false, nullable=true, allowBlank=true, description="Email de Contato", requirements="^.{0,255}")
     * @FOSRest\RequestParam(name="telefone_contato",                  strict=false, nullable=true, allowBlank=true, description="Telefone")
     * @FOSRest\RequestParam(name="data_proximo_contato",              strict=false, nullable=true, allowBlank=true, description="Proximo contato")
     * @FOSRest\RequestParam(name="periodo_pretendido",                strict=false, nullable=true, allowBlank=true, description="Período pretendido")
     * @FOSRest\RequestParam(name="horario_proximo_contato",           strict=false, nullable=true, allowBlank=true, description="Horario do proximo contato")
     * @FOSRest\RequestParam(name="data_validade_promocao",            strict=false, nullable=true, allowBlank=true, description="Validade da promoção")
     * @FOSRest\RequestParam(name="data_primeiro_atendimento",         strict=false, nullable=true, allowBlank=true, description="Data primeiro atendimento")
     * @FOSRest\RequestParam(name="grau_interesse",                    strict=false, nullable=true, allowBlank=true, description="Grau de interesse", requirements="(L|I|H)")
     * @FOSRest\RequestParam(name="pessoa_indicou",                    strict=false, nullable=true, allowBlank=true, description="id da pessoa que indicou")
     *
     * @FOSRest\RequestParam(name="idiomas",    strict=true, nullable=true, allowBlank=true, description="Lista de idiomas selecionados", map=true, requirements="\d+")
     * @FOSRest\RequestParam(name="follow_ups", strict=true, nullable=true, allowBlank=true, description="Lista de followups a serem atrelados", map=true)
     *
     * @FOSRest\Patch("/interessado/followup/{id}")
     *
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function followup($id, ParamFetcher $paramFetcher, Request $request)
    {
        $parametros          = $paramFetcher->all();
        $parametrosBackup    = $parametros;
        $parametrosOriginais = $parametros;
        $mensagem            = "";
        $usuarioId           = $request->headers->get('Authorization-User-ID');
        $franqueada          = $parametros[ConstanteParametros::CHAVE_FRANQUEADA];
        $retorno   = $this->interessadoFacade->atualizar($mensagem, $id, $parametros);
        $objetoORM = $parametros[ConstanteParametros::CHAVE_INTERESSADO];

        if ($retorno === false) {
            return ResponseFactory::badRequest(["parametros" => $parametros], $mensagem);
        }

        if ((($parametros[ConstanteParametros::CHAVE_TIPO_WORKFLOW] !== SituacoesSistema::WORKFLOW_MATRICULA_PERDIDA)&&($parametros[ConstanteParametros::CHAVE_TIPO_WORKFLOW] !== SituacoesSistema::WORKFLOW_MATRICULA_CONVERTIDO))
            && (is_null($parametros[ConstanteParametros::CHAVE_DATA_PROXIMO_CONTATO]) === false) && (is_null($parametros[ConstanteParametros::CHAVE_HORARIO_PROXIMO_CONTATO]) === false) && (isset($parametros[ConstanteParametros::CHAVE_WORKFLOW_ACAO]) === true) && (is_null($parametros[ConstanteParametros::CHAVE_WORKFLOW_ACAO]) === false)
        ) {
            $dadosAgendamento = [
                ConstanteParametros::CHAVE_DATA_PROXIMO_CONTATO    => $parametros[ConstanteParametros::CHAVE_DATA_PROXIMO_CONTATO],
                ConstanteParametros::CHAVE_HORARIO_PROXIMO_CONTATO => $parametros[ConstanteParametros::CHAVE_HORARIO_PROXIMO_CONTATO],
                ConstanteParametros::CHAVE_FUNCIONARIO             => $parametros[ConstanteParametros::CHAVE_CONSULTOR_RESPONSAVEL_FUNCIONARIO],
                ConstanteParametros::CHAVE_TIPO_AGENDAMENTO        => null,
                ConstanteParametros::CHAVE_WORKFLOW_ACAO           => $parametros[ConstanteParametros::CHAVE_WORKFLOW_ACAO],
                ConstanteParametros::CHAVE_INTERESSADO             => $id,
                ConstanteParametros::CHAVE_USUARIO                 => $usuarioId,
                ConstanteParametros::CHAVE_FRANQUEADA              => $franqueada,
                ConstanteParametros::CHAVE_INTERESSADO_ORM         => $objetoORM,
            ];

            $agendaComercial = $this->agendaComercialFacade->atualizarVindoInteressado($mensagem, $id, $dadosAgendamento);
            if ((empty($mensagem) === false) || (is_null($agendaComercial) === true)) {
                return ResponseFactory::conflict(["parametros" => $parametrosBackup], $mensagem);
            }

            $parametrosOriginais[ConstanteParametros::CHAVE_AGENDA_COMERCIAL] = $agendaComercial->getId();
        }

        $parametrosOriginais[ConstanteParametros::CHAVE_INTERESSADO]  = $id;
        $parametrosOriginais[ConstanteParametros::CHAVE_USUARIO]      = $usuarioId;
        $parametrosOriginais[ConstanteParametros::CHAVE_TIPO_CONTATO] = $objetoORM->getTipoContato();
        $parametrosOriginais[ConstanteParametros::CHAVE_CONSULTOR_FUNCIONARIO] = $objetoORM->getConsultorFuncionario();
        $parametrosOriginais[ConstanteParametros::CHAVE_TIPO_PROSPECCAO]       = $objetoORM->getTipoProspeccao();
        $parametrosOriginais[ConstanteParametros::CHAVE_TIPO_LEAD]        = $objetoORM->getTipoLead();
        $parametrosOriginais[ConstanteParametros::CHAVE_CURSO_PRETENDIDO] = $objetoORM->getCurso();
        $parametrosOriginais[ConstanteParametros::CHAVE_WORKFLOW]         = $objetoORM->getWorkflow();
        $parametrosOriginais[ConstanteParametros::CHAVE_WORKFLOW_ACAO]    = $objetoORM->getWorkflowAcao();
        $parametrosOriginais[ConstanteParametros::CHAVE_DATA_VALIDADE_PROMOCAO] = $objetoORM->getDataValidadePromocao();
        $parametrosOriginais[ConstanteParametros::CHAVE_PERIODO_PRETENDIDO]     = $objetoORM->getPeriodoPretendido();

        
        if (is_null($objetoORM->getMotivoNaoFechamento()) === false) {
            $parametrosOriginais[ConstanteParametros::CHAVE_DATA_MATRICULA_PERDIDA]          = $objetoORM->getDataMatriculaPerdida();
            $parametrosOriginais[ConstanteParametros::CHAVE_MOTIVO_NAO_FECHAMENTO_MATRICULA] = $objetoORM->getMotivoNaoFechamento();
        }

        if ($objetoORM->getSituacao() === SituacoesSistema::SITUACAO_INATIVO) {
            $objetoORM->setSituacao(SituacoesSistema::SITUACAO_ATIVO);
        }

        $objetoFollowUp = $this->followupComercialFacade->criar($mensagem, $parametrosOriginais);
        if ((is_null($objetoFollowUp) === true) || (empty($mensagem) === false)) {
            return ResponseFactory::conflict(["parametros" => $parametrosOriginais], $mensagem);
        }

        if (($parametros[ConstanteParametros::CHAVE_TIPO_WORKFLOW] === SituacoesSistema::WORKFLOW_MATRICULA_CONVERTIDO)||($parametros[ConstanteParametros::CHAVE_TIPO_WORKFLOW] === SituacoesSistema::WORKFLOW_MATRICULA_PERDIDA)) {
            $this->agendaComercialFacade->atualizaSituacaoDoInteressado($mensagem, $id);
            if (empty($mensagem) === false) {
                return ResponseFactory::conflict(["parametros" => $parametros], $mensagem);
            }
        }

        return ResponseFactory::noContent([]);
    }


}

