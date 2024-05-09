<?php

namespace App\Controller\Principal\AgendaCompromisso;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use FOS\RestBundle\Controller\Annotations as FOSRest;
use Swagger\Annotations as SWG;
use App\Factory\ResponseFactory;
use App\Controller\Principal\Base\GenericController;
use FOS\RestBundle\Request\ParamFetcher;
use App\Facade\Principal\AgendaCompromissoFacade;
use Symfony\Component\HttpFoundation\Request;
use App\Helper\ConstanteParametros;

/**
 *
 * @author        Luiz A Costa
 * @Route("/api")
 */
class AgendaCompromissoController extends GenericController
{

    /**
     *
     * @var \App\Facade\Principal\AgendaCompromissoFacade
     */
    private $agendaCompromissoFacade;

    /**
     * {@inheritdoc}
     */
    protected function constroiFacades()
    {
        // Para criar o LogFacade do GenericController
        parent::constroiFacades();
        $this->agendaCompromissoFacade = new AgendaCompromissoFacade(self::getManagerRegistry());
    }

    /**
     *
     * @SWG\Get(
     *     path="/api/agenda_compromisso/verifica_disponibilidade",
     *     summary="verifica_disponibilidade",
     *     description="verifica_disponibilidade",
     *     produces={"application/json"},
     * @SWG\Response(
     *         response="200",
     *         description="Retorna os agenda_compromisso"
     *     ),
     * @SWG\Response(
     *         response="400",
     *         description="Ocorreu algum erro no servidor",
     *     )
     * )
     *
     * @FOSRest\QueryParam(name="funcionario",      strict=true, allowBlank=false, description="ID Funcionario", requirements="\d+")
     * @FOSRest\QueryParam(name="data_hora_inicio", strict=true, allowBlank=false, nullable=false, description="Data Hora Inicio")
     *
     * @FOSRest\Get("/agenda_compromisso/verifica_disponibilidade")
     *
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function verificaDisponibilidade(ParamFetcher $paramFetcher, Request $request)
    {
        $parametros = $paramFetcher->all();
        $resultados = $this->agendaCompromissoFacade->verificarDisponibilidadeFuncionario($parametros);

        return ResponseFactory::ok($resultados);
    }

    /**
     *
     * @SWG\Get(
     *     path="/api/agenda_compromisso/listar",
     *     summary="Listar agenda_compromisso",
     *     description="Lista as agenda_compromisso do banco",
     *     produces={"application/json"},
     * @SWG\Response(
     *         response="200",
     *         description="Retorna os agenda_compromisso"
     *     ),
     * @SWG\Response(
     *         response="400",
     *         description="Ocorreu algum erro no servidor",
     *     )
     * )
     *
     * @FOSRest\QueryParam(name="tipo",             strict=true, allowBlank=false, description="Filtro de tipos de agenda/compromisso", default="0", requirements="(0|1|2)")
     * @FOSRest\QueryParam(name="funcionario",      strict=false, allowBlank=false, description="Funcionário id", requirements="\d+")
     * @FOSRest\QueryParam(name="data_hora_inicio", strict=false, allowBlank=true, nullable=true, description="Data Inicio")
     * @FOSRest\QueryParam(name="data_hora_fim",    strict=false, allowBlank=true, nullable=true, description="Data Fim")
     *
     * @FOSRest\Get("/agenda_compromisso/listar")
     *
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function lista(ParamFetcher $paramFetcher, Request $request)
    {
        $parametros      = $paramFetcher->all();
        $usuarioLogadoId = $request->headers->get('Authorization-User-ID');
        $resultados      = $this->agendaCompromissoFacade->listar($parametros, $usuarioLogadoId);

        return ResponseFactory::ok($resultados);
    }

    /**
     *
     * @SWG\Get(
     *     path="/api/agenda_compromisso/{id}",
     *     summary="Buscar a agenda_compromisso",
     *     description="Busca a agenda_compromisso através da ID",
     *     produces={"application/json"},
     * @SWG\Response(
     *         response="200",
     *         description="Retorna a agenda_compromisso"
     *     ),
     * @SWG\Response(
     *         response="404",
     *         description="Ocorreu algum erro no servidor",
     *     )
     * )
     *
     * @FOSRest\Get("/agenda_compromisso/{id}")
     *
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function buscar($id)
    {
        $mensagemErro = "";
        $objetoORM    = $this->agendaCompromissoFacade->buscarPorId($mensagemErro, $id);
        if (is_null($objetoORM) === true) {
            return ResponseFactory::notFound([], $mensagemErro);
        }

        return ResponseFactory::ok($objetoORM);
    }

    /**
     *
     * @SWG\Post(
     *     path="/api/agenda_compromisso/criar",
     *     summary="Cria uma agenda_compromisso",
     *     description="Cria uma agenda_compromisso no banco",
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
     * @FOSRest\RequestParam(name="tipo_agendamento", strict=true, nullable=false, allowBlank=false, description="ID do tipo de agendamento", requirements="\d+")
     * @FOSRest\RequestParam(name="funcionario",      strict=true, nullable=false, allowBlank=false, description="ID do funcionário", requirements="\d+")
     * @FOSRest\RequestParam(name="franqueada",      strict=true, nullable=false, allowBlank=false, description="ID do franqueada", requirements="\d+")
     * @FOSRest\RequestParam(name="data_hora_inicio", strict=true, nullable=false, allowBlank=false, description="Data e hora do inicio do Agendamento")
     * @FOSRest\RequestParam(name="data_hora_fim",    strict=false, nullable=true, allowBlank=true, description="Data e hora do término do Agendamento")
     * @FOSRest\RequestParam(name="privado",          strict=true, nullable=false, allowBlank=false, description="Agenda é privada", requirements="(0|1)")
     * @FOSRest\RequestParam(name="titulo",           strict=true, nullable=false, allowBlank=false, default="", description="Titulo", requirements=".{0,255}")
     * @FOSRest\RequestParam(name="descricao",        strict=false, nullable=true, allowBlank=true, default="", description="Descrição da agenda")
     *
     * @FOSRest\Post("/agenda_compromisso/criar")
     *
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function criar(ParamFetcher $paramFetcher, Request $request)
    {
        $parametros = $paramFetcher->all();
        $parametros[ConstanteParametros::CHAVE_USUARIO]    = $request->headers->get('Authorization-User-ID');
      //  $parametros[ConstanteParametros::CHAVE_FRANQUEADA] = $request->headers->get('Franqueada');
        $mensagem  = "";
        $objetoORM = $this->agendaCompromissoFacade->criar($mensagem, $parametros);

        if ((is_null($objetoORM) === true) || (empty($mensagem) === false)) {
            return ResponseFactory::conflict(["parametros" => $parametros], $mensagem);
        }

        return ResponseFactory::created(["objetoORM" => $objetoORM->getId()], "Registro criado com sucesso!");
    }

    /**
     *
     * @SWG\Patch(
     *     path="/api/agenda_compromisso/alterar/{id}",
     *     summary="Atualiza um agenda_compromisso",
     *     description="Atualiza um agenda_compromisso no banco",
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
     * @FOSRest\RequestParam(name="tipo_agendamento", strict=true, nullable=false, allowBlank=false, description="ID do tipo de agendamento", requirements="\d+")
     * @FOSRest\RequestParam(name="funcionario",      strict=true, nullable=false, allowBlank=false, description="ID do funcionário", requirements="\d+")
     * @FOSRest\RequestParam(name="data_hora_inicio", strict=true, nullable=false, allowBlank=false, description="Data e hora do inicio do Agendamento")
     * @FOSRest\RequestParam(name="data_hora_fim",    strict=false, nullable=true, allowBlank=true, description="Data e hora do término do Agendamento")
     * @FOSRest\RequestParam(name="privado",          strict=true, nullable=false, allowBlank=false, description="Agenda é privada", requirements="(0|1)")
     * @FOSRest\RequestParam(name="titulo",           strict=true, nullable=false, allowBlank=false, default="", description="Titulo", requirements=".{0,255}")
     * @FOSRest\RequestParam(name="descricao",        strict=true, nullable=false, allowBlank=true, default="", description="Descrição da agenda")
     * @FOSRest\RequestParam(name="alterar_todos",    strict=true, nullable=false, allowBlank=true, default="1", description="Flag para alterar todos os compromissos relacionados", requirements="(0|1)")
     *
     * @FOSRest\Patch("/agenda_compromisso/alterar/{id}")
     *
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function atualizar($id, ParamFetcher $paramFetcher, Request $request)
    {
        $parametros = $paramFetcher->all();
        $mensagem   = "";
        $retorno    = $this->agendaCompromissoFacade->atualizar($mensagem, $id, $parametros);
        if ($retorno === false) {
            return ResponseFactory::badRequest(["parametros" => $parametros], $mensagem);
        }

        return ResponseFactory::noContent([]);
    }

    /**
     *
     * @SWG\Post(
     *     path="/api/agenda_compromisso/remover/{id}",
     *     summary="Remove uma agenda_compromisso",
     *     description="Remove uma agenda_compromisso no banco",
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
     * @FOSRest\RequestParam(name="alterar_todos", strict=true, nullable=false, allowBlank=true, default="1", description="Flag para alterar todos os compromissos relacionados", requirements="(0|1)")
     *
     * @FOSRest\Post("/agenda_compromisso/remover/{id}")
     *
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function excluir($id, ParamFetcher $paramFetcher)
    {
        $parametros = $paramFetcher->all();
        $mensagem   = "";
        $retorno    = $this->agendaCompromissoFacade->remover($mensagem, $id, (bool) $parametros[ConstanteParametros::CHAVE_FLAG_ALTERAR_TODOS]);
        if ($retorno === false) {
            return ResponseFactory::badRequest([], $mensagem);
        }

        return ResponseFactory::ok([], "Excluido com sucesso");
    }


}
