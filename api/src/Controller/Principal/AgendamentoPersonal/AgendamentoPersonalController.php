<?php

namespace App\Controller\Principal\AgendamentoPersonal;

use Swagger\Annotations as SWG;
use App\Factory\ResponseFactory;
use App\Helper\ConstanteParametros;
use FOS\RestBundle\Request\ParamFetcher;
use Symfony\Component\HttpFoundation\Request;
use App\Controller\Principal\Base\GenericController;
use FOS\RestBundle\Controller\Annotations as FOSRest;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;

/**
 *
 * @author        Marcelo A Naegeler
 * @Route("/api")
 */
class AgendamentoPersonalController extends GenericController
{


    /**
     *
     * @var \App\Facade\Principal\agendamentoPersonalFacade
     */
    private $agendamentoPersonalFacade;

    /**
     * {@inheritdoc}
     */
    protected function constroiFacades()
    {
        // Para criar o LogFacade do GenericController
        parent::constroiFacades();
        $this->agendamentoPersonalFacade = new \App\Facade\Principal\AgendamentoPersonalFacade(self::getManagerRegistry());
    }

    /**
     *
     * @SWG\Get(
     *     path="/api/agendamento_personal/listar",
     *     summary="Listar agendamento_personal",
     *     description="Lista as agendamento_personal do banco",
     *     produces={"application/json"},
     * @SWG\Response(
     *         response="200",
     *         description="Retorna os agendamento_personal"
     *     ),
     * @SWG\Response(
     *         response="400",
     *         description="Ocorreu algum erro no servidor",
     *     )
     * )
     *
     * @FOSRest\QueryParam(name="pagina",          strict=false, allowBlank=false, default="1", description="Pagina para realizar o scroll", requirements="\d+")
     * @FOSRest\QueryParam(name="data",            strict=false, allowBlank=false, description="Data")
     * @FOSRest\QueryParam(name="aluno",           strict=false, allowBlank=false, description="AlunoID", requirements="\d+")
     * @FOSRest\QueryParam(name="sala_franqueada", strict=false, allowBlank=false, description="Sala", requirements="\d+")
     * @FOSRest\QueryParam(name="funcionario",     strict=false, allowBlank=false, description="Professor", requirements="\d+")
     * @FOSRest\QueryParam(name="diario_personal", strict=true, allowBlank=true, nullable=true, description="vem de diario personal", default="0", requirements="(0|1)")
     *
     * @FOSRest\Get("/agendamento_personal/listar")
     *
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function lista(ParamFetcher $request)
    {
        $parametros = $request->all();
        $mensagem   = "";
        if ((bool) $parametros[ConstanteParametros::CHAVE_DIARIO_PERSONAL] === true) {
            $resultados = $this->agendamentoPersonalFacade->listarTodosItens($parametros);
        } else {
            $resultados = $this->agendamentoPersonalFacade->listar($parametros);
        }

        if ($resultados === false) {
            return ResponseFactory::badRequest([], $mensagem);
        }

        return ResponseFactory::ok($resultados);
    }

       /**
     *
     * @SWG\Get(
     *     path="/api/agendamento_personal/consulta_disponibilidade",
     *     summary="Listar agendamento_personal",
     *     description="Lista as agendamento_personal do banco",
     *     produces={"application/json"},
     * @SWG\Response(
     *         response="200",
     *         description="Retorna os agendamento_personal"
     *     ),
     * @SWG\Response(
     *         response="400",
     *         description="Ocorreu algum erro no servidor",
     *     )
     * )
     *
     * @FOSRest\QueryParam(name="data_inicial",    strict=false, allowBlank=false, description="Data Inicial")
     * @FOSRest\QueryParam(name="data_final",      strict=false, allowBlank=false, description="Data Final")
     * @FOSRest\QueryParam(name="sala_franqueada", strict=false, allowBlank=false, description="Sala", requirements="\d+")
     * @FOSRest\QueryParam(name="funcionario",     strict=false, allowBlank=true, description="Professor", requirements="\d+")
     * @FOSRest\QueryParam(name="diario_personal", strict=true, allowBlank=true, nullable=true, description="vem de diario personal", default="0", requirements="(0|1)")
     *
     * @FOSRest\Get("/agendamento_personal/consulta_disponibilidade")
     *
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function consultaDisponibilidade(ParamFetcher $request)
    {
        $parametros = $request->all();
        $mensagem   = "";
      
         $resultados = $this->agendamentoPersonalFacade->consultaDisponibilidadePersonal($parametros);
 
        if ($resultados === false) {
            return ResponseFactory::badRequest([], $mensagem);
        }

        return ResponseFactory::ok($resultados);
    }

    /**
     *
     * @SWG\Get(
     *     path="/api/agendamento_personal/{id}",
     *     summary="Buscar a agendamento_personal",
     *     description="Busca a agendamento_personal através da ID",
     *     produces={"application/json"},
     * @SWG\Response(
     *         response="200",
     *         description="Retorna a agendamento_personal"
     *     ),
     * @SWG\Response(
     *         response="404",
     *         description="Ocorreu algum erro no servidor",
     *     )
     * )
     *
     * @FOSRest\Get("/agendamento_personal/{id}")
     *
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function buscar($id)
    {
        $objetoORM = null;
        // TODO: seu objeto ORM
        if (is_null($objetoORM) === true) {
            return ResponseFactory::notFound([], "OBJETO ORM não encontrada.");
        }

        return ResponseFactory::ok($objetoORM);
    }

    /**
     *
     * @SWG\Get(
     *     path="/api/agendamento_personal/buscar_por_contrato/{id}",
     *     summary="Buscar os agendamento_personal de acordo com o contrato",
     *     description="Busca o agendamento_personal através do id do contrato",
     *     produces={"application/json"},
     * @SWG\Response(
     *         response="200",
     *         description="Retorna os agendamento_personal do contrato"
     *     ),
     * @SWG\Response(
     *         response="404",
     *         description="Ocorreu algum erro no servidor",
     *     )
     * )
     *
     * @FOSRest\Get("/agendamento_personal/buscar_info_por_contrato/{id}")
     *
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function buscarInfoPorContrato($id)
    {
        $mensagemErro = '';
        $agendamentos = $this->agendamentoPersonalFacade->buscarInfoPorContrato($mensagemErro, $id);

        if (empty($mensagemErro) === false) {
            return ResponseFactory::conflict([], $mensagemErro);
        }

        return ResponseFactory::ok($agendamentos);
    }

    /**
     *
     * @SWG\Post(
     *     path="/api/agendamento_personal/criar",
     *     summary="Cria uma agendamento_personal",
     *     description="Cria uma agendamento_personal no banco",
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
     * @FOSRest\RequestParam(name="exemplo_string",  strict=true, nullable=false, allowBlank=false, description="exemplo_string")
     * @FOSRest\RequestParam(name="exemplo_integer", strict=true, nullable=false, allowBlank=false, description="exemplo_integer", default="0")
     *
     * @FOSRest\Post("/agendamento_personal/criar")
     *
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function criar(ParamFetcher $request)
    {
        $parametros = $request->all();
        $mensagem   = "";
        $objetoORM  = "";
        // TODO: Seu objeto ORM
        if ((is_null($objetoORM) === true) || (empty($mensagem) === false)) {
            return ResponseFactory::conflict(["parametros" => $parametros], $mensagem);
        }

        return ResponseFactory::created(["objetoORM" => $objetoORM->getId()], "Registro criado com sucesso!");
    }

    /**
     *
     * @SWG\Patch(
     *     path="/api/agendamento_personal/alterar/{id}",
     *     summary="Atualiza um agendamento_personal",
     *     description="Atualiza um agendamento_personal no banco",
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
     * @FOSRest\RequestParam(name="exemplo_string",  strict=true, nullable=false, allowBlank=false, description="exemplo_string")
     * @FOSRest\RequestParam(name="exemplo_integer", strict=true, nullable=false, allowBlank=false, description="exemplo_integer", default="0")
     *
     * @FOSRest\Patch("/agendamento_personal/alterar/{id}")
     *
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function atualizar($id, ParamFetcher $request)
    {
        $parametros = $request->all();
        $mensagem   = "";
        $retorno    = false;
        // TODO: True ou False
        if ($retorno === false) {
            return ResponseFactory::badRequest(["parametros" => $parametros], $mensagem);
        }

        return ResponseFactory::noContent([]);
    }

        /**
     *
     * @SWG\Patch(
     *     path="/api/agendamento_personal/reagendar_personal/{id}",
     *     summary="Atualiza um agendamento_personal",
     *     description="Atualiza um agendamento_personal no banco",
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
     * @FOSRest\RequestParam(name="aluno",                  strict=true, nullable=true, allowBlank=false, description="id do aluno")
     * @FOSRest\RequestParam(name="instrutor",              strict=true, nullable=true, allowBlank=false, description="id do aluno")
     * @FOSRest\RequestParam(name="data_aula",              strict=false, nullable=false, allowBlank=false, description="Data do Reagendmento")
     * @FOSRest\RequestParam(name="permanente",             strict=true, nullable=true, allowBlank=false, description="Alterar em todas as aulas?")
     * @FOSRest\RequestParam(name="franqueada",             strict=false, nullable=false, allowBlank=false, description="Alterar em todas as aulas?")
     * @FOSRest\RequestParam(name="sala_franqueada",        strict=false, nullable=false, allowBlank=false, description="Alterar em todas as aulas?")
     *
     * @FOSRest\Patch("/agendamento_personal/reagendar_personal/{id}")
     *
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function atualizarAgendamentoPersonal($id, ParamFetcher $paramFetcher, Request $request)
    {
        $parametros = $paramFetcher->all();
        
        $mensagem   = ""; 
    
        $retorno    = $this->agendamentoPersonalFacade->reagendarPersonal($mensagem, $parametros, $id);
     
        // TODO: True ou False
        if ($retorno === false) {
            return ResponseFactory::badRequest(["parametros" => $parametros], $mensagem);
        }
        
        $parametros[ConstanteParametros::CHAVE_USUARIO] = $request->headers->get('Authorization-User-ID');
       
        $parametrosLog = [
            ConstanteParametros::CHAVE_TIPO_EVENTO => \App\Facade\Log\LogFacade::$LOG_CREATE,
            ConstanteParametros::CHAVE_IP_ORIGEM   => $request->getClientIp(),
            ConstanteParametros::CHAVE_FRANQUEADA  => $parametros[ConstanteParametros::CHAVE_FRANQUEADA],
            ConstanteParametros::CHAVE_USUARIO     => $parametros[ConstanteParametros::CHAVE_USUARIO],
            ConstanteParametros::CHAVE_INFO_EVENTO => json_encode($parametros),
            ConstanteParametros::CHAVE_DADOS_ANTERIORES => $parametros[ConstanteParametros::CHAVE_DADOS_ANTERIORES],
            ConstanteParametros::CHAVE_DADOS_ATUAIS => $parametros[ConstanteParametros::CHAVE_DADOS_ATUAIS],
            ConstanteParametros::CHAVE_MODULO => "REAGENDAMENTO_PERSONAL",
        ];

        $erroMsg = "";

        self::getLogFacade()->criarLog($erroMsg, $parametrosLog);

        return ResponseFactory::ok([], "Agendamento alterado com sucesso");
    }

    /**
     *
     * @SWG\Delete(
     *     path="/api/agendamento_personal/remover/{id}",
     *     summary="Remove uma agendamento_personal",
     *     description="Remove uma agendamento_personal no banco",
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
     * @FOSRest\Delete("/agendamento_personal/remover/{id}")
     *
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function excluir($id)
    {
        $mensagem = "";
        $retorno  = false;
        // TODO: True ou False
        if ($retorno === false) {
            return ResponseFactory::badRequest([], $mensagem);
        }

        return ResponseFactory::ok([], "Excluido com sucesso");
    }


}
