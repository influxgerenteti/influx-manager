<?php

namespace App\Controller\Log\Log;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use FOS\RestBundle\Controller\Annotations as FOSRest;
use Swagger\Annotations as SWG;
use App\Factory\ResponseFactory;
use App\Controller\Principal\Base\GenericController;
use Symfony\Component\HttpFoundation\Request;
use FOS\RestBundle\Request\ParamFetcher;

/**
 *
 * @author        Luiz Antonio Costa
 * @Route("/api")
 */
class LogController extends GenericController
{


    /**
     *
     * @SWG\Get(
     *     path="/api/log/listar",
     *     summary="Listar Logs",
     *     description="Lista os logs do banco",
     *     tags={"Log"},
     *     consumes={"application/x-www-form-urlencoded"},
     *     produces={"application/json"},
     * @SWG\Response(
     *         response="200",
     *         description="Retorna os logs"
     *     ),
     * @SWG\Response(
     *         response="400",
     *         description="Ocorreu algum erro no servidor",
     *     )
     * )
     *
     * @FOSRest\QueryParam(name="pagina",     strict=true, requirements="\d{0,2}", default="1", allowBlank=false, description="Página para realizar o scroll")
     * @FOSRest\QueryParam(name="franqueada", strict=true, requirements="\d+",     allowBlank=false, description="ID da Franqueada")
     * @FOSRest\QueryParam(name="order",      strict=false, nullable=true,  description="Coluna de ordenação")
     * @FOSRest\QueryParam(name="direcao",    strict=false, nullable=true,  description="ASC|DESC")
     *
     * @FOSRest\Get("/log/listar")
     *
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function listarLogs(ParamFetcher $paramFetcher)
    {
        $parametros = $paramFetcher->all();
        $resultados = $this->getLogFacade()->listaLogs($parametros);
        return ResponseFactory::ok($resultados);
    }

    /**
     *
     * @SWG\Post(
     *     path="/api/log/criar",
     *     summary="Cria um Log",
     *     description="Cria um Log no banco",
     *     tags={"Log"},
     *     consumes={"application/x-www-form-urlencoded"},
     *     produces={"application/json"},
     * @SWG\Response(
     *         response="201",
     *         description="Retorna criado com sucesso"
     *     ),
     * @SWG\Response(
     *         response="409",
     *         description="Ocorreu algum erro no servidor",
     *     )
     * )
     *
     * @FOSRest\RequestParam(name="info_evento", strict=true, allowBlank=true, description="JSON de informações do evento", requirements=".+")
     * @FOSRest\RequestParam(name="tipo_evento", strict=true, allowBlank=true, description="Tipo do evento", requirements="[a-zA-Z]+")
     * @FOSRest\RequestParam(name="franqueada",  strict=true, allowBlank=true, description="ID da Franqueada", requirements="\d+")
     *
     * @FOSRest\Post("/log/criar")
     *
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function criarLog(Request $request, ParamFetcher $paramFetcher)
    {
        $parametros = $paramFetcher->all();
        $parametros["ip_origem"] = $request->getClientIp();
        $parametros["usuario"]   = $request->headers->get('Authorization-User-ID');
        $errorMsg     = "";
        $objetoCriado = $this->getLogFacade()->criarLog($errorMsg, $parametros);

        if (is_null($objetoCriado) === false) {
            return ResponseFactory::ok(["id" => $objetoCriado->getId()], "Criado com sucesso!");
        }

        return ResponseFactory::badRequest([], "Erro ao criar um novo log via URL, Erro:" . $errorMsg);
    }

    /**
     *
     * @SWG\Get(
     *      path="/api/log/{id}",
     *      summary="Buscar log",
     *      description="Buscar detalhes de determinado log.",
     *      tags={"Log"},
     *      consumes={"application/x-www-form-urlencoded"},
     *      produces={"application/json"},
     * @SWG\Response(
     *          response="200",
     *          description="Retorna o log"
     *      ),
     * @SWG\Response(
     *          response="400",
     *          description="Ocorreu algum erro no servidor",
     *      )
     * )
     *
     * @FOSRest\Get("/log/{id}")
     *
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function buscarLog ($id)
    {
        $mensagem = "";
        $log      = $this->getLogFacade()->buscarLog($mensagem, $id);

        if ($log === null) {
            return ResponseFactory::badRequest([], $mensagem);
        }

        return ResponseFactory::ok($log);
    }


}
