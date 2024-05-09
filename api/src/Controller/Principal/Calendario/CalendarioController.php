<?php

namespace App\Controller\Principal\Calendario;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use FOS\RestBundle\Controller\Annotations as FOSRest;
use Swagger\Annotations as SWG;
use App\Factory\ResponseFactory;
use App\Controller\Principal\Base\GenericController;
use FOS\RestBundle\Request\ParamFetcher;
use App\Facade\Principal\CalendarioFacade;
use App\Helper\ConstanteParametros;
use DateTime;

/**
 *
 * @author        Luiz A Costa
 * @Route("/api")
 */
class CalendarioController extends GenericController
{

    /**
     *
     * @var \App\Facade\Principal\CalendarioFacade
     */
    private $calendarioFacade;

    /**
     * {@inheritdoc}
     */
    protected function constroiFacades()
    {
        // Para criar o LogFacade do GenericController
        parent::constroiFacades();
        $this->calendarioFacade = new CalendarioFacade(self::getManagerRegistry());
    }

    /**
     *
     * @SWG\Get(
     *     path="/api/calendario/listarAntigo",
     *     summary="Listar calendario",
     *     description="Lista as calendario do banco",
     *     tags={"Calendario"},
     *     produces={"application/json"},
     * @SWG\Response(
     *         response="200",
     *         description="Retorna os calendario"
     *     ),
     * @SWG\Response(
     *         response="400",
     *         description="Ocorreu algum erro no servidor",
     *     )
     * )
     *
     * @FOSRest\QueryParam(name="pagina",     strict=false, allowBlank=false, default="1", description="Pagina para realizar o scroll", requirements="\d+")
     * @FOSRest\QueryParam(name="franqueada", strict=true, nullable=true, allowBlank=true, description="ID da tabela Franqueada", requirements="\d+")
     * @FOSRest\QueryParam(name="calendario", strict=true, nullable=true, allowBlank=true, description="ID da tabela Calendario", requirements="\d+")
     * @FOSRest\QueryParam(name="ano",        strict=true, nullable=true, allowBlank=true, description="Ano do calendario", requirements="\d+")
     *
     * @FOSRest\Get("/calendario/listarAntigo")
     *
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function listaAntigo(ParamFetcher $request)
    {
        return ResponseFactory::badGateway([], "URL BLOQUEADA");
    }

    /**
     *
     * @SWG\Get(
     *     path="/api/calendario/listar",
     *     summary="Listar todos calendario",
     *     description="Lista todos calendario do banco",
     *     tags={"Calendario"},
     *     produces={"application/json"},
     * @SWG\Response(
     *         response="200",
     *         description="Retorna os calendario"
     *     ),
     * @SWG\Response(
     *         response="400",
     *         description="Ocorreu algum erro no servidor",
     *     )
     * )
     *
     * @FOSRest\QueryParam(name="franqueada", strict=true, nullable=true, allowBlank=true, description="ID da tabela Franqueada", requirements="\d+")
     * @FOSRest\QueryParam(name="ano",        strict=true, nullable=true, allowBlank=true, description="Ano do calendario", requirements="\d+")
     *
     * @FOSRest\Get("/calendario/listar")
     *
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function listar(ParamFetcher $request)
    {
        $parametros = $request->all();
        $mensagem   = "";
        $resultados = $this->calendarioFacade->listarTodos($parametros);
        if ($resultados === false) {
            return ResponseFactory::badRequest([], $mensagem);
        }

        return ResponseFactory::ok($resultados);
    }

    /**
     *
     * @SWG\Get(
     *     path="/api/calendario/buscaFeriadoBancario",
     *     summary="Busca uma data para verificar se é feriado bancário",
     *     description="Busca uma data para verificar se é feriado bancário",
     *     tags={"Calendario"},
     *     produces={"application/json"},
     * @SWG\Response(
     *         response="200",
     *         description="Retorna true se for feriado bancário e false se não for"
     *     ),
     * @SWG\Response(
     *         response="400",
     *         description="Ocorreu algum erro no servidor",
     *     )
     * )
     *
     * @FOSRest\QueryParam(name="franqueada",  strict=true, nullable=false, allowBlank=false, description="ID da franqueada", requirements="\d+")
     * @FOSRest\QueryParam(name="dataFeriado", strict=true, nullable=false, allowBlank=false, description="String da data")
     *
     * @FOSRest\Get("/calendario/buscaFeriadoBancario")
     *
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function buscaFeriadoBancario(ParamFetcher $request)
    {
        $mensagem     = '';
        $parametros   = $request->all();
        $dataAlt      = strtotime($parametros['dataFeriado']);
        $feriadoOuFds = true;
        while ($feriadoOuFds === true) {
            // se for sábado adiciona 2 dias, se for domingo adiciona 1 dia
            if (getdate($dataAlt)["wday"] === 6) {
                $dataAlt = strtotime("+2 day", $dataAlt);
            } else if (getdate($dataAlt)["wday"] === 0) {
                $dataAlt = strtotime("+1 day", $dataAlt);
            }

            $ymd = strval(getdate($dataAlt)["year"]) . '-' . strval(getdate($dataAlt)["mon"]) . '-' . strval(getdate($dataAlt)["mday"]);
            // verifica se é feriado no calendário da franqueada
            $objetoORM    = $this->calendarioFacade->verificaFeriadoBancarioPorData($parametros['franqueada'], $ymd);
            $feriadoOuFds = $objetoORM;
            if ($feriadoOuFds === true) {
                $dataAlt = strtotime("+1 day", $dataAlt);
            }
        }

        if (is_null($dataAlt) === true) {
            return ResponseFactory::notFound([], $mensagem);
        }

        // retorna Y-m-d
        return ResponseFactory::ok(strval(getdate($dataAlt)["year"]) . '-' . strval(getdate($dataAlt)["mon"]) . '-' . strval(getdate($dataAlt)["mday"]));
    }

    /**
     *
     * @SWG\Get(
     *     path="/api/calendario/{id}",
     *     summary="Buscar a calendario",
     *     description="Busca a calendario através da ID",
     *     tags={"Calendario"},
     *     produces={"application/json"},
     * @SWG\Response(
     *         response="200",
     *         description="Retorna a calendario"
     *     ),
     * @SWG\Response(
     *         response="404",
     *         description="Ocorreu algum erro no servidor",
     *     )
     * )
     *
     * @FOSRest\Get("/calendario/{id}")
     *
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function buscar($id)
    {
        $mensagem  = "";
        $objetoORM = $this->calendarioFacade->buscarPorId($mensagem, $id);
        if (is_null($objetoORM) === true) {
            return ResponseFactory::notFound([], $mensagem);
        }

        return ResponseFactory::ok($objetoORM);
    }

    /**
     *
     * @SWG\Post(
     *     path="/api/calendario/criar",
     *     summary="Cria uma calendario",
     *     description="Cria uma calendario no banco",
     *     tags={"Calendario"},
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
     * @FOSRest\RequestParam(name="franqueada",       strict=true, nullable=true, allowBlank=true, description="ID da franqueada", requirements="\d+")
     * @FOSRest\RequestParam(name="descricao",        strict=true, nullable=false, allowBlank=false, description="Descricao")
     * @FOSRest\RequestParam(name="data_inicial",     strict=true, nullable=false, allowBlank=false, description="Data Inicial")
     * @FOSRest\RequestParam(name="data_final",       strict=true, nullable=true, allowBlank=true, description="Data Final")
     * @FOSRest\RequestParam(name="feriado_bancario", strict=true, nullable=true, allowBlank=true, description="Feriado bancario", requirements="[0|1]")
     * @FOSRest\RequestParam(name="dia_letivo",       strict=true, nullable=true, allowBlank=true, description="Dia letivo", requirements="[0|1]")
     *
     * @FOSRest\Post("/calendario/criar")
     *
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function criar(ParamFetcher $request)
    {
        $parametros = $request->all();
        $mensagem   = "";
        $objetoORM  = $this->calendarioFacade->criar($mensagem, $parametros);
        if ((is_null($objetoORM) === true) || (empty($mensagem) === false)) {
            return ResponseFactory::conflict(["parametros" => $parametros], $mensagem);
        }

        return ResponseFactory::created(["objetoORM" => $objetoORM->getId()], "Registro criado com sucesso!");
    }

    /**
     *
     * @SWG\Patch(
     *     path="/api/calendario/atualizar/{id}",
     *     summary="Atualiza um calendario",
     *     description="Atualiza um calendario no banco",
     *     tags={"Calendario"},
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
     * @FOSRest\RequestParam(name="franqueada",       strict=true, nullable=true, allowBlank=true, description="ID da franqueada", requirements="\d+")
     * @FOSRest\RequestParam(name="descricao",        strict=true, nullable=false, allowBlank=false, description="Descricao")
     * @FOSRest\RequestParam(name="data_inicial",     strict=true, nullable=false, allowBlank=false, description="Data Inicial")
     * @FOSRest\RequestParam(name="data_final",       strict=true, nullable=true, allowBlank=true, description="Data Final")
     * @FOSRest\RequestParam(name="feriado_bancario", strict=true, nullable=true, allowBlank=true, description="Feriado bancario", requirements="[0|1]")
     * @FOSRest\RequestParam(name="dia_letivo",       strict=true, nullable=true, allowBlank=true, description="Dia letivo", requirements="[0|1]")
     *
     * @FOSRest\Patch("/calendario/atualizar/{id}")
     *
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function atualizar($id, ParamFetcher $request)
    {
        $parametros = $request->all();
        $mensagem   = "";
        $retorno    = $this->calendarioFacade->atualizar($mensagem, $id, $parametros);
        if ($retorno === false) {
            return ResponseFactory::badRequest(["parametros" => $parametros], $mensagem);
        }

        return ResponseFactory::noContent([]);
    }

    /**
     *
     * @SWG\Delete(
     *     path="/api/calendario/remover/{id}",
     *     summary="Remove uma calendario",
     *     description="Remove uma calendario no banco",
     *     tags={"Calendario"},
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
     * @FOSRest\Delete("/calendario/remover/{id}")
     *
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function excluir($id)
    {
        $mensagem = "";
        $retorno  = $this->calendarioFacade->remover($mensagem, $id);
        if ($retorno === false) {
            return ResponseFactory::badRequest([], $mensagem);
        }

        return ResponseFactory::ok([], "Excluido com sucesso");
    }


}
