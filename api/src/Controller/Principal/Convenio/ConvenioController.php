<?php

namespace App\Controller\Principal\Convenio;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use FOS\RestBundle\Controller\Annotations as FOSRest;
use Swagger\Annotations as SWG;
use App\Factory\ResponseFactory;
use App\Controller\Principal\Base\GenericController;
use FOS\RestBundle\Request\ParamFetcher;
use App\Facade\Principal\ConvenioFacade;
use App\Helper\ConstanteParametros;
use App\Facade\Principal\FollowupConvenioFacade;
use App\Helper\VariaveisCompartilhadas;
use Symfony\Component\HttpFoundation\Request;

/**
 *
 * @author        Luiz A Costa
 * @Route("/api")
 */
class ConvenioController extends GenericController
{

    /**
     *
     * @var \App\Facade\Principal\ConvenioFacade
     */
    private $convenioFacade;

    /**
     *
     * @var \App\Facade\Principal\FollowupConvenioFacade
     */
    private $followupConvenioFacade;

    /**
     *
     * @var \App\Repository\Principal\UsuarioRepository $usuarioRepository
     */
    private $usuarioRepository;

    /**
     * {@inheritdoc}
     */
    protected function constroiFacades()
    {
        // Para criar o LogFacade do GenericController
        parent::constroiFacades();
        $this->convenioFacade         = new ConvenioFacade(self::getManagerRegistry());
        $this->followupConvenioFacade = new FollowupConvenioFacade(self::getManagerRegistry());
        $this->usuarioRepository      = self::getManagerRegistry()->getEntityManager()->getRepository(\App\Entity\Principal\Usuario::class);
    }

    /**
     *
     * @SWG\Get(
     *     path="/api/convenio/listar",
     *     summary="Listar convenio",
     *     description="Lista as convenio do banco",
     *     produces={"application/json"},
     * @SWG\Response(
     *         response="200",
     *         description="Retorna os convenio"
     *     ),
     * @SWG\Response(
     *         response="400",
     *         description="Ocorreu algum erro no servidor",
     *     )
     * )
     *
     * @FOSRest\QueryParam(name="pagina",                      strict=false, allowBlank=false, default="1", description="Pagina para realizar o scroll", requirements="\d+")
     * @FOSRest\QueryParam(name="franqueada",                  strict=true, requirements="\d+",     allowBlank=false, description="ID da Franqueada")
     * @FOSRest\QueryParam(name="pessoa",                      strict=false, nullable=true, requirements="\d+",     allowBlank=false, description="Empresa Pessoa ID")
     * @FOSRest\QueryParam(name="etapas_convenio",             strict=false, nullable=true, requirements="\d+",     allowBlank=false, description="ID da Etapas Convenio")
     * @FOSRest\QueryParam(name="consultor_funcionario",       strict=false, nullable=true, requirements="\d+",     allowBlank=false, description="ID do Consultor Funcionario")
     * @FOSRest\QueryParam(name="segmento_empresa_convenio",   strict=false, nullable=true, requirements="\d+",     allowBlank=false, description="ID do Segmento Empresa Convenio")
     * @FOSRest\QueryParam(name="nome_contato",                strict=false, nullable=true, description="Nome Contato")
     * @FOSRest\QueryParam(name="data_proximo_contato_de",     strict=false, nullable=true, description="Data Proximo Contato De")
     * @FOSRest\QueryParam(name="data_proximo_contato_ate",    strict=false, nullable=true, description="Data Proximo Contato Ate")
     * @FOSRest\QueryParam(name="horario_proximo_contato_de",  strict=false, nullable=true, description="Hora proximo Contato De")
     * @FOSRest\QueryParam(name="horario_proximo_contato_ate", strict=false, nullable=true, description="Hora proximo Contato Ate")
     * @FOSRest\QueryParam(name="usuario_franqueadora",        strict=false, nullable=true, allowBlank=true, description="Situacao do Interessado", requirements="(0|1)")
     * @FOSRest\QueryParam(name="situacao",                    strict=false, nullable=true, allowBlank=true, description="Situacao do Interessado")
     * @FOSRest\QueryParam(name="order",                       strict=false, nullable=true,  description="Coluna de ordenação")
     * @FOSRest\QueryParam(name="direcao",                     strict=false, nullable=true,  description="ASC|DESC")
     *
     * @FOSRest\Get("/convenio/listar")
     *
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function lista(ParamFetcher $request)
    {
        $parametros = $request->all();
        $mensagem   = "";
        $resultados = $this->convenioFacade->listar($parametros);
        if ($resultados === false) {
            return ResponseFactory::badRequest([], $mensagem);
        }

        return ResponseFactory::ok($resultados);
    }

    /**
     *
     * @SWG\Get(
     *     path="/api/convenio/listar_convenios_nacionais",
     *     summary="Listar convenio",
     *     description="Lista as convenio do banco",
     *     produces={"application/json"},
     * @SWG\Response(
     *         response="200",
     *         description="Retorna os convenio"
     *     ),
     * @SWG\Response(
     *         response="400",
     *         description="Ocorreu algum erro no servidor",
     *     )
     * )
     *
     * @FOSRest\QueryParam(name="pagina",               strict=false, allowBlank=false, default="1", description="Pagina para realizar o scroll", requirements="\d+")
     * @FOSRest\QueryParam(name="franqueada",           strict=true, requirements="\d+",     allowBlank=false, description="ID da Franqueada")
     * @FOSRest\QueryParam(name="cnpj",                 strict=false, nullable=true,  description="CNPJ")
     * @FOSRest\QueryParam(name="unidade_responsavel",  strict=false, nullable=true,  description="Franqueada responsavel")
     * @FOSRest\QueryParam(name="razao_social",         strict=false, nullable=true,  description="razao social")
     * @FOSRest\QueryParam(name="data_de_cadastro_de",  strict=false, nullable=true, allowBlank=false, description="Filtro por data de cadastro de")
     * @FOSRest\QueryParam(name="data_de_cadastro_ate", strict=false, nullable=true, allowBlank=false, description="Filtro por data de cadastro ate")
     * @FOSRest\QueryParam(name="tipo_abrangencia",     strict=false, nullable=true, requirements="(0|1|2|3)",     allowBlank=false, description="Empresa Pessoa ID")
     * @FOSRest\QueryParam(name="pessoa",               strict=false, nullable=true, requirements="\d+",     allowBlank=false, description="Empresa Pessoa ID")
     * @FOSRest\QueryParam(name="cidade",               strict=false, nullable=true, requirements="\d+",     allowBlank=false, description="Cidade ID")
     * @FOSRest\QueryParam(name="etapas_convenio",      strict=false, nullable=true, requirements="\d+",     allowBlank=false, description="Etapa do convenio ID")
     * @FOSRest\QueryParam(name="segmento_empresa",     strict=false, nullable=true, requirements="\d+",     allowBlank=false, description="Segmento empresa ID")
     * @FOSRest\QueryParam(name="situacao",             strict=false, nullable=true, allowBlank=false, description="Situacao")
     * @FOSRest\QueryParam(name="order",                strict=false, nullable=true,  description="Coluna de ordenação")
     * @FOSRest\QueryParam(name="direcao",              strict=false, nullable=true,  description="ASC|DESC")
     *
     * @FOSRest\Get("/convenio/listar_convenios_nacionais")
     *
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function listaConveniosNacionais(ParamFetcher $request)
    {
        $parametros = $request->all();
        $mensagem   = "";
        $resultados = $this->convenioFacade->listaConveniosNacionais($parametros);
        if ($resultados === false) {
            return ResponseFactory::badRequest([], $mensagem);
        }

        return ResponseFactory::ok($resultados);
    }

    /**
     *
     * @SWG\Get(
     *     path="/api/convenio/{id}",
     *     summary="Buscar a convenio",
     *     description="Busca a convenio através da ID",
     *     produces={"application/json"},
     * @SWG\Response(
     *         response="200",
     *         description="Retorna a convenio"
     *     ),
     * @SWG\Response(
     *         response="404",
     *         description="Ocorreu algum erro no servidor",
     *     )
     * )
     *
     * @FOSRest\Get("/convenio/{id}")
     *
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function buscar($id)
    {
        $mensagemErro = "";
        $usuarioORM   = null;

        $usuarioORM = $this->usuarioRepository->find(VariaveisCompartilhadas::$usuarioID);
        $parametros = [
            ConstanteParametros::CHAVE_USUARIO_FRANQUEADORA => $usuarioORM->isUsuarioPertenceFranqueadora(),
        ];
        $objetoORM  = $this->convenioFacade->buscarPorId($mensagemErro, $id, $parametros);
        if (is_null($objetoORM) === true) {
            return ResponseFactory::notFound([], $mensagemErro);
        }

        return ResponseFactory::ok($objetoORM);
    }


    /**
     *
     * @SWG\Get(
     *     path="/api/convenio/buscar/empresa/{nomeEmpresa}",
     *     summary="Buscar os convenios",
     *     description="Busca os convenios através do nome da empresa",
     *     tags={"Convenio"},
     *     produces={"application/json"},
     * @SWG\Response(
     *         response="200",
     *         description="Retorna uma lista de convenios"
     *     ),
     * @SWG\Response(
     *         response="404",
     *         description="Ocorreu algum erro no servidor",
     *     )
     * )
     *
     * @FOSRest\Get("/convenio/buscar/empresa/{nomeEmpresa}")
     *
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function buscarEmpresasPorNome($nomeEmpresa, ParamFetcher $paramFetcher)
    {
        $convenios = $this->convenioFacade->buscarEmpresaPorNome($nomeEmpresa);
        return ResponseFactory::ok($convenios);
    }


    /**
     *
     * @SWG\Post(
     *     path="/api/convenio/criar",
     *     summary="Cria uma convenio",
     *     description="Cria uma convenio no banco",
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
     * @FOSRest\RequestParam(name="franqueada",                                strict=true, allowBlank=false, requirements="\d+",      description="ID da Franqueada")
     * @FOSRest\RequestParam(name="pessoa",                                    strict=true, allowBlank=false, requirements="\d+",      description="Pessoa ID")
     * @FOSRest\RequestParam(name="segmento_empresa_convenio",                 strict=false, allowBlank=true, nullable=true, requirements="\d+",      description="SegmentoEmpresaConvenio ID")
     * @FOSRest\RequestParam(name="negociacao_parceria_workflow",strict=false, allowBlank=true, nullable=true, requirements="\d+",      description="Negociacao parceria workflow ID")
     * @FOSRest\RequestParam(name="etapas_convenio",                           strict=false, allowBlank=true, nullable=true, description="Etapas convenio ID")
     * @FOSRest\RequestParam(name="consultor_funcionario",                     strict=false, allowBlank=true, nullable=true, description="consultor responsavel ID")
     * @FOSRest\RequestParam(name="abrangencia_nacional",                      strict=false, allowBlank=true, nullable=true, requirements="(1|0)",      description="Abrangencia nacional", default="false")
     * @FOSRest\RequestParam(name="beneficiario_colaboradores",                strict=false, allowBlank=true, nullable=true, requirements="(1|0)",      description="Beneficiario Colaboradores")
     * @FOSRest\RequestParam(name="beneficiario_dependentes",                  strict=false, allowBlank=true, nullable=true, requirements="(1|0)",      description="Beneficiario Dependentes")
     * @FOSRest\RequestParam(name="beneficiario_associados",                   strict=false, allowBlank=true, nullable=true, requirements="(1|0)",      description="Beneficiario Associados")
     * @FOSRest\RequestParam(name="beneficiario_alunos",                       strict=false, allowBlank=true, nullable=true, requirements="(1|0)",      description="Beneficiario Alunos")
     * @FOSRest\RequestParam(name="beneficiario_estagiarios",                  strict=false, allowBlank=true, nullable=true, requirements="(1|0)",      description="Beneficiario Estagiários")
     * @FOSRest\RequestParam(name="beneficiario_terceiros",                    strict=false, allowBlank=true, nullable=true, requirements="(1|0)",      description="Beneficiario Terceiros")
     * @FOSRest\RequestParam(name="nome_contato",                              strict=true, nullable=false, allowBlank=false, description="Nome", requirements="^.{0,150}")
     * @FOSRest\RequestParam(name="email_contato",                             strict=false, nullable=true, allowBlank=true, description="Email de Contato", requirements="^.{0,255}")
     * @FOSRest\RequestParam(name="telefone_contato",                          strict=false, nullable=true, allowBlank=true, description="Telefone")
     * @FOSRest\RequestParam(name="telefone_contato_secundario",               strict=false, nullable=true, allowBlank=true, description="Telefone Secundario")
     * @FOSRest\RequestParam(name="data_proximo_contato",                      strict=false, nullable=true, allowBlank=true, description="Data de proximo contato")
     * @FOSRest\RequestParam(name="horario_proximo_contato",                   strict=false, nullable=true, allowBlank=true, description="Horario proximo contato")
     * @FOSRest\RequestParam(name="inscricao_municipal",                       strict=false, nullable=true, allowBlank=true, description="Inscricao Municipal")
     * @FOSRest\RequestParam(name="inscricao_estadual",                        strict=false, nullable=true, allowBlank=true, description="Inscricao Estadual")
     * @FOSRest\RequestParam(name="observacao",                                strict=true, nullable=true, allowBlank=true, description="Observacao")
     *
     * @FOSRest\FileParam(name="contrato", strict=false, requirements={"mimeTypes"={"application/pdf", "application/msword", "application/vnd.openxmlformats-officedocument.wordprocessingml.document"}})
     *
     * @FOSRest\Post("/convenio/criar")
     *
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function criar(ParamFetcher $paramFetcher, Request $request)
    {
        $parametros = $paramFetcher->all();
        $mensagem   = "";
        $usuarioId  = $request->headers->get('Authorization-User-ID');
        $objetoORM  = $this->convenioFacade->criar($mensagem, $usuarioId, $parametros);
        if ((is_null($objetoORM) === true) || (empty($mensagem) === false)) {
            return ResponseFactory::conflict(["parametros" => $parametros], $mensagem);
        }

        if ((isset($parametros[ConstanteParametros::CHAVE_OBSERVACAO]) === true) && (empty($parametros[ConstanteParametros::CHAVE_OBSERVACAO]) === false)) {
            $mensagemErrorFollowUp = "";
            $followUpData          = [
                ConstanteParametros::CHAVE_CONVENIO      => $objetoORM->getId(),
                ConstanteParametros::CHAVE_USUARIO       => $usuarioId,
                ConstanteParametros::CHAVE_TIPO_CONTATO  => null,
                ConstanteParametros::CHAVE_DATA_REGISTRO => new \DateTime(),
                ConstanteParametros::CHAVE_FOLLOW_UP     => $parametros[ConstanteParametros::CHAVE_OBSERVACAO],
            ];

            $objetoFollowUp = $this->followupConvenioFacade->criar($mensagemErrorFollowUp, $followUpData);
            if ((is_null($objetoFollowUp) === true) || (empty($mensagemErrorFollowUp) === false)) {
                return ResponseFactory::conflict(["parametros_follow_up" => $parametros], $mensagemErrorFollowUp);
            }
        }

        return ResponseFactory::created(["objetoORM" => $objetoORM->getId()], "Registro criado com sucesso!");
    }

    /**
     *
     * @SWG\Post(
     *     path="/api/convenio/atualizar/{id}",
     *     summary="Atualiza um convenio",
     *     description="Atualiza um convenio no banco",
     *     consumes={"application/form-data"},
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
     * @FOSRest\RequestParam(name="franqueada",                                strict=true, allowBlank=false, requirements="\d+",      description="ID da Franqueada")
     * @FOSRest\RequestParam(name="pessoa",                                    strict=true, allowBlank=false, requirements="\d+",      description="Pessoa ID")
     * @FOSRest\RequestParam(name="segmento_empresa_convenio",                 strict=false, allowBlank=true, nullable=true, requirements="\d+",      description="SegmentoEmpresaConvenio ID")
     * @FOSRest\RequestParam(name="negociacao_parceria_workflow",strict=false, allowBlank=true, nullable=true, requirements="\d+",      description="Negociacao parceria workflow ID")
     * @FOSRest\RequestParam(name="etapas_convenio",                           strict=false, allowBlank=true, nullable=true, description="Etapas convenio ID")
     * @FOSRest\RequestParam(name="consultor_funcionario",                     strict=false, allowBlank=true, nullable=true, description="consultor responsavel ID")
     * @FOSRest\RequestParam(name="abrangencia_nacional",                      strict=false, allowBlank=true, nullable=true, requirements="(1|0)",      description="Abrangencia nacional", default="false")
     * @FOSRest\RequestParam(name="beneficiario_colaboradores",                strict=false, allowBlank=true, nullable=true, requirements="(1|0)",      description="Beneficiario Colaboradores")
     * @FOSRest\RequestParam(name="beneficiario_dependentes",                  strict=false, allowBlank=true, nullable=true, requirements="(1|0)",      description="Beneficiario Dependentes")
     * @FOSRest\RequestParam(name="beneficiario_associados",                   strict=false, allowBlank=true, nullable=true, requirements="(1|0)",      description="Beneficiario Associados")
     * @FOSRest\RequestParam(name="beneficiario_alunos",                       strict=false, allowBlank=true, nullable=true, requirements="(1|0)",      description="Beneficiario Alunos")
     * @FOSRest\RequestParam(name="beneficiario_estagiarios",                  strict=false, allowBlank=true, nullable=true, requirements="(1|0)",      description="Beneficiario Estagiários")
     * @FOSRest\RequestParam(name="beneficiario_terceiros",                    strict=false, allowBlank=true, nullable=true, requirements="(1|0)",      description="Beneficiario Terceiros")
     * @FOSRest\RequestParam(name="nome_contato",                              strict=true, nullable=false, allowBlank=false, description="Nome", requirements="^.{0,150}")
     * @FOSRest\RequestParam(name="email_contato",                             strict=false, nullable=true, allowBlank=true, description="Email de Contato", requirements="^.{0,255}")
     * @FOSRest\RequestParam(name="telefone_contato",                          strict=false, nullable=true, allowBlank=true, description="Telefone")
     * @FOSRest\RequestParam(name="telefone_contato_secundario",               strict=false, nullable=true, allowBlank=true, description="Telefone Secundario")
     * @FOSRest\RequestParam(name="data_proximo_contato",                      strict=false, nullable=true, allowBlank=true, description="Data de proximo contato")
     * @FOSRest\RequestParam(name="horario_proximo_contato",                   strict=false, nullable=true, allowBlank=true, description="Horario proximo contato")
     * @FOSRest\RequestParam(name="inscricao_municipal",                       strict=false, nullable=true, allowBlank=true, description="Inscricao Municipal")
     * @FOSRest\RequestParam(name="inscricao_estadual",                        strict=false, nullable=true, allowBlank=true, description="Inscricao Estadual")
     * @FOSRest\RequestParam(name="fechar_convenio",                           strict=false, nullable=true, allowBlank=true, description="Flag para fechar o convenio")
     * @FOSRest\RequestParam(name="situacao",                                  strict=false, nullable=true, allowBlank=true, description="Situação")
     * @FOSRest\RequestParam(name="observacao",                                strict=true, nullable=true, allowBlank=true, description="Observacao")
     *
     * @FOSRest\FileParam(name="contrato", strict=false, requirements={"mimeTypes"={"application/pdf", "application/msword","application/vnd.openxmlformats-officedocument.wordprocessingml.document"}})
     *
     * @FOSRest\Post("/convenio/atualizar/{id}")
     *
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function atualizar($id, ParamFetcher $paramFetcher, Request $request)
    {
        $parametros = $paramFetcher->all();
        unset($parametros[ConstanteParametros::CHAVE_PESSOA]);
        $mensagem  = "";
        $usuarioId = $request->headers->get('Authorization-User-ID');
        $retorno   = $this->convenioFacade->atualizar($mensagem, $id, $usuarioId, $parametros);
        if ($retorno === false) {
            return ResponseFactory::badRequest(["parametros" => $parametros], $mensagem);
        }

        if ((isset($parametros[ConstanteParametros::CHAVE_OBSERVACAO]) === true) && (empty($parametros[ConstanteParametros::CHAVE_OBSERVACAO]) === false)) {
            $mensagemErrorFollowUp = "";
            $followUpData          = [
                ConstanteParametros::CHAVE_CONVENIO      => $id,
                ConstanteParametros::CHAVE_USUARIO       => $usuarioId,
                ConstanteParametros::CHAVE_TIPO_CONTATO  => null,
                ConstanteParametros::CHAVE_DATA_REGISTRO => new \DateTime(),
                ConstanteParametros::CHAVE_FOLLOW_UP     => $parametros[ConstanteParametros::CHAVE_OBSERVACAO],
            ];

            $objetoFollowUp = $this->followupConvenioFacade->criar($mensagemErrorFollowUp, $followUpData);
            if ((is_null($objetoFollowUp) === true) || (empty($mensagemErrorFollowUp) === false)) {
                return ResponseFactory::conflict(["parametros_follow_up" => $parametros], $mensagemErrorFollowUp);
            }
        }

        return ResponseFactory::noContent([]);
    }

    /**
     *
     * @SWG\Delete(
     *     path="/api/convenio/remover/{id}",
     *     summary="Remove uma convenio",
     *     description="Remove uma convenio no banco",
     *     produces={"application/json"},
     * @SWG\Response(
     *         response="200",
     *         description="Retorna removido com sucesso"
     *     ),
     * @SWG\Response(
     *         response="400",
     *         description="Ocorreu algum erro no servidor",
     *     )
     * )
     *
     * @FOSRest\Delete("/convenio/remover/{id}")
     *
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function excluir($id)
    {
        $mensagem = "";
        $retorno  = $this->convenioFacade->remover($mensagem, $id);
        if ($retorno === false) {
            return ResponseFactory::badRequest([], $mensagem);
        }

        return ResponseFactory::ok([], "Excluido com sucesso");
    }

    /**
     *
     * @SWG\Post(
     *     path="/api/convenio/inativar/{id}",
     *     summary="Inativa uma convenio",
     *     description="Seta um convenio como situação Inativo no banco",
     *     produces={"application/json"},
     * @SWG\Response(
     *         response="200",
     *         description="Retorna removido com sucesso"
     *     ),
     * @SWG\Response(
     *         response="400",
     *         description="Ocorreu algum erro no servidor",
     *     )
     * )
     *
     * @FOSRest\Post("/convenio/inativar/{id}")
     *
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function inativar($id)
    {
        $mensagem = "";
        $retorno  = $this->convenioFacade->inativar($mensagem, $id);
        if ($retorno === false) {
            return ResponseFactory::badRequest([], $mensagem);
        }

        return ResponseFactory::ok([], "Inativado com sucesso");
    }

    /**
     *
     * @SWG\Get(
     *     path="/api/convenio/buscar-nome-cpf/{query}",
     *     summary="Buscar convênio por nome",
     *     description="Busca convênios pelo nome",
     *     tags={"Convênio"},
     *     produces={"application/json"},
     * @SWG\Response(
     *         response="200",
     *         description="Retorna os convênios"
     *     ),
     * )
     *
     * @FOSRest\Get("/convenio/buscar-nome/{query}")
     *
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function buscarNome($query)
    {
        $convenios = $this->convenioFacade->buscarPorNome($query);

        return ResponseFactory::ok($convenios);
    }

    /**
     *
     * @SWG\Get(
     *     path="/api/convenio/buscar-ativos-nome/{query}",
     *     summary="Buscar convênio por nome",
     *     description="Busca convênios pelo nome",
     *     tags={"Convênio"},
     *     produces={"application/json"},
     * @SWG\Response(
     *         response="200",
     *         description="Retorna os convênios"
     *     ),
     * )
     * @FOSRest\QueryParam(name="franqueada", strict=true, allowBlank=false, requirements="\d+",      description="ID da Franqueada")
     *
     * @FOSRest\Get("/convenio/buscar-ativos-nome/{query}")
     *
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function buscarAtivosPorNome($query, ParamFetcher $paramFetcher)
    {
        $parametros = $paramFetcher->all();
        $convenios  = $this->convenioFacade->buscarAtivosPorNome($query, $parametros);

        return ResponseFactory::ok($convenios);
    }

    /**
     *
     * @SWG\Post(
     *     path="/api/convenio/followup/{id}",
     *     summary="Cria uma convenio",
     *     description="Cria uma convenio no banco",
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
     * @FOSRest\RequestParam(name="consultor_funcionario",          strict=false, allowBlank=true, nullable=true, requirements="\d+",      description="Funcionario ID")
     * @FOSRest\RequestParam(name="franqueada",                     strict=true, allowBlank=false, requirements="\d+",      description="ID da Franqueada")
     * @FOSRest\RequestParam(name="etapas_convenio",                strict=true, allowBlank=false, requirements="\d+",      description="EtapasConvenio ID")
     * @FOSRest\RequestParam(name="motivo_nao_fechamento_convenio", strict=false, allowBlank=true, nullable=true, requirements="\d+",      description="MotivoNaoFechamentoConvenio ID")
     * @FOSRest\RequestParam(name="justificativa_franqueadora",     strict=false, nullable=true, allowBlank=true, description="Justificativa Observadora")
     * @FOSRest\RequestParam(name="data_primeiro_atendimento",      strict=false, nullable=true, allowBlank=true, description="Data de primeiro atendimento")
     * @FOSRest\RequestParam(name="data_proximo_contato",           strict=false, nullable=true, allowBlank=true, description="Proximo contato")
     * @FOSRest\RequestParam(name="horario_proximo_contato",        strict=false, nullable=true, allowBlank=true, description="Horario do proximo contato")
     * @FOSRest\RequestParam(name="situacao",                       strict=true, nullable=false, allowBlank=false, description="Situacao", requirements="(PV|A|I|EN|NE|RF|SC)")
     *
     * @FOSRest\RequestParam(name="follow_ups", strict=true, nullable=true, allowBlank=true, description="Lista de followups a serem atrelados", map=true)
     *
     * @FOSRest\Post("/convenio/followup/{id}")
     *
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function followupConvenio($id, ParamFetcher $paramFetcher, Request $request)
    {
        $parametros = $paramFetcher->all();
        unset($parametros[ConstanteParametros::CHAVE_PESSOA]);
        $mensagem  = "";
        $usuarioId = $request->headers->get('Authorization-User-ID');
        $retorno   = $this->convenioFacade->followup($mensagem, $id, $usuarioId, $parametros);
        if ($retorno === false) {
            return ResponseFactory::badRequest(["parametros" => $parametros], $mensagem);
        }

        if ((isset($parametros[ConstanteParametros::CHAVE_FOLLOW_UPS]) === true)&&(empty($parametros[ConstanteParametros::CHAVE_FOLLOW_UPS]) === false)&&(count($parametros[ConstanteParametros::CHAVE_FOLLOW_UPS]) > 0)) {
            foreach ($parametros[ConstanteParametros::CHAVE_FOLLOW_UPS] as $followUpData) {
                $followUpData[ConstanteParametros::CHAVE_CONVENIO] = $id;
                $followUpData[ConstanteParametros::CHAVE_USUARIO]  = $usuarioId;

                $objetoFollowUp = $this->followupConvenioFacade->criar($mensagem, $followUpData);
                if ((is_null($objetoFollowUp) === true) || (empty($mensagem) === false)) {
                    return ResponseFactory::conflict(["parametros" => $parametros], $mensagem);
                }
            }
        }//end if

        return ResponseFactory::noContent([]);
    }


}
