<?php

namespace App\Controller\Principal\WorkflowAcao;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use FOS\RestBundle\Controller\Annotations as FOSRest;
use Swagger\Annotations as SWG;
use App\Factory\ResponseFactory;
use App\Controller\Principal\Base\GenericController;
use FOS\RestBundle\Request\ParamFetcher;
use App\Facade\Principal\WorkflowAcaoFacade;

/**
 *
 * @author        Luiz A Costa
 * @Route("/api")
 */
class WorkflowAcaoController extends GenericController
{

    /**
     *
     * @var \App\Facade\Principal\WorkflowAcaoFacade
     */
    private $workflowAcaoFacade;

    /**
     * {@inheritdoc}
     */
    protected function constroiFacades()
    {
        // Para criar o LogFacade do GenericController
        parent::constroiFacades();
        $this->workflowAcaoFacade = new WorkflowAcaoFacade(self::getManagerRegistry());
    }

    /**
     *
     * @SWG\Get(
     *     path="/api/workflow_acao/listar",
     *     summary="Listar workflow_acao",
     *     description="Lista as workflow_acao do banco",
     *     produces={"application/json"},
     * @SWG\Response(
     *         response="200",
     *         description="Retorna os workflow_acao"
     *     ),
     * @SWG\Response(
     *         response="400",
     *         description="Ocorreu algum erro no servidor",
     *     )
     * )
     *
     * @FOSRest\QueryParam(name="pagina", strict=false, allowBlank=false, default="1", description="Pagina para realizar o scroll", requirements="\d+")
     *
     * @FOSRest\Get("/workflow_acao/listar")
     *
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function lista(ParamFetcher $request)
    {
        $parametros = $request->all();
        $mensagem   = "";
        $resultados = $this->workflowAcaoFacade->listar($parametros);
        if ($resultados === false) {
            return ResponseFactory::badRequest([], $mensagem);
        }

        return ResponseFactory::ok($resultados);
    }

    /**
     *
     * @SWG\Get(
     *     path="/api/workflow_acao/{id}",
     *     summary="Buscar a workflow_acao",
     *     description="Busca a workflow_acao através da ID",
     *     produces={"application/json"},
     * @SWG\Response(
     *         response="200",
     *         description="Retorna a workflow_acao"
     *     ),
     * @SWG\Response(
     *         response="404",
     *         description="Ocorreu algum erro no servidor",
     *     )
     * )
     *
     * @FOSRest\Get("/workflow_acao/{id}")
     *
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function buscar($id)
    {
        $mensagemErro = "";
        $objetoORM    = $this->workflowAcaoFacade->buscarPorId($mensagemErro, $id);
        if (is_null($objetoORM) === true) {
            return ResponseFactory::notFound([], $mensagemErro);
        }

        return ResponseFactory::ok($objetoORM);
    }

    /**
     *
     * @SWG\Get(
     *     path="/api/workflow_acao/buscar_por_workflow/{id}",
     *     summary="Buscar a workflow_acao por workflow",
     *     description="Busca a workflow_acao através da ID do workflow",
     *     produces={"application/json"},
     * @SWG\Response(
     *         response="200",
     *         description="Retorna a workflow_acao"
     *     ),
     * @SWG\Response(
     *         response="404",
     *         description="Ocorreu algum erro no servidor",
     *     )
     * )
     *
     * @FOSRest\Get("/workflow_acao/buscar_por_workflow/{id}")
     *
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function buscarPorWorkflowId($id)
    {
        $objetoORM = $this->workflowAcaoFacade->buscarPorWorkflowId($id);
        return ResponseFactory::ok($objetoORM);
    }

    /**
     *
     * @SWG\Post(
     *     path="/api/workflow_acao/criar",
     *     summary="Cria uma workflow_acao",
     *     description="Cria uma workflow_acao no banco",
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
     * @FOSRest\Post("/workflow_acao/criar")
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
     *     path="/api/workflow_acao/alterar/{id}",
     *     summary="Atualiza um workflow_acao",
     *     description="Atualiza um workflow_acao no banco",
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
     * @FOSRest\Patch("/workflow_acao/alterar/{id}")
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
     * @SWG\Delete(
     *     path="/api/workflow_acao/remover/{id}",
     *     summary="Remove uma workflow_acao",
     *     description="Remove uma workflow_acao no banco",
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
     * @FOSRest\Delete("/workflow_acao/remover/{id}")
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
