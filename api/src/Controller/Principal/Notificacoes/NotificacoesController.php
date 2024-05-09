<?php

namespace App\Controller\Principal\Notificacoes;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use FOS\RestBundle\Controller\Annotations as FOSRest;
use Swagger\Annotations as SWG;
use App\Factory\ResponseFactory;
use App\Controller\Principal\Base\GenericController;
use FOS\RestBundle\Request\ParamFetcher;
use App\Facade\Principal\NotificacoesFacade;

/**
 *
 * @author        Luiz A Costa
 * @Route("/api")
 */
class NotificacoesController extends GenericController
{

    /**
     *
     * @var \App\Facade\Principal\NotificacoesFacade
     */
    private $notificacoesFacade;

    /**
     * {@inheritdoc}
     */
    protected function constroiFacades()
    {
        // Para criar o LogFacade do GenericController
        parent::constroiFacades();
        $this->notificacoesFacade = new NotificacoesFacade(self::getManagerRegistry());
    }

    /**
     *
     * @SWG\Get(
     *     path="/api/notificacoes/listar",
     *     summary="Listar notificacoes",
     *     description="Lista as notificacoes do banco",
     *     produces={"application/json"},
     * @SWG\Response(
     *         response="200",
     *         description="Retorna os notificacoes"
     *     ),
     * @SWG\Response(
     *         response="400",
     *         description="Ocorreu algum erro no servidor",
     *     )
     * )
     *
     * @FOSRest\QueryParam(name="franqueada",       strict=true, nullable=false, description="Franqueada", requirements="\d+")
     * @FOSRest\QueryParam(name="usuario",          strict=false, nullable=true, description="Usuario", requirements="\d+")
     * @FOSRest\QueryParam(name="data_prorrogacao", strict=false, nullable=true, description="Data Prorrogacao")
     * @FOSRest\QueryParam(name="is_lida",          strict=false, nullable=false, default="0", description="Filtrar mensagems lidas")
     *
     * @FOSRest\Get("/notificacoes/listar")
     *
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function lista(ParamFetcher $request)
    {
        $parametros = $request->all();
        $mensagem   = "";
        $resultados = $this->notificacoesFacade->listarTodasNotificacoes($parametros);
        if ($resultados === false) {
            return ResponseFactory::badRequest([], $mensagem);
        }

        return ResponseFactory::ok($resultados);
    }

    /**
     *
     * @SWG\Patch(
     *     path="/api/notificacoes/atualizar/{id}",
     *     summary="Atualiza um notificacoes",
     *     description="Atualiza um notificacoes no banco",
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
     * @FOSRest\RequestParam(name="data_prorrogacao", strict=false, nullable=false, allowBlank=false, description="Proxima data de prorrogacao")
     * @FOSRest\RequestParam(name="is_lida",          strict=false, nullable=false, allowBlank=true, description="Marca se ta lido ou nao")
     *
     * @FOSRest\Patch("/notificacoes/atualizar/{id}")
     *
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function atualizar($id, ParamFetcher $request)
    {
        $parametros = $request->all();
        $mensagem   = "";
        $retorno    = $this->notificacoesFacade->atualizar($mensagem, $id, $parametros);
        if ($retorno === false) {
            return ResponseFactory::badRequest(["parametros" => $parametros], $mensagem);
        }

        return ResponseFactory::noContent([]);
    }


}
