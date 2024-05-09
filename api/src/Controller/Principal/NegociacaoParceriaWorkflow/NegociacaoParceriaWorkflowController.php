<?php

namespace App\Controller\Principal\NegociacaoParceriaWorkflow;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use FOS\RestBundle\Controller\Annotations as FOSRest;
use Swagger\Annotations as SWG;
use App\Factory\ResponseFactory;
use App\Controller\Principal\Base\GenericController;
use FOS\RestBundle\Request\ParamFetcher;
use App\Facade\Principal\NegociacaoParceriaWorkflowFacade;

/**
 *
 * @author        Dayan Freitas
 * @Route("/api")
 */
class NegociacaoParceriaWorkflowController extends GenericController
{

    /**
     *
     * @var \App\Facade\Principal\NegociacaoParceriaWorkflowFacade
     */
    private $negociacaoParceriaWorkflowFacade;

    /**
     * {@inheritdoc}
     */
    protected function constroiFacades()
    {
        // Para criar o LogFacade do GenericController
        parent::constroiFacades();
        $this->negociacaoParceriaWorkflowFacade = new NegociacaoParceriaWorkflowFacade(self::getManagerRegistry());

    }

    /**
     *
     * @SWG\Get(
     *     path="/api/negociacao_parceria_workflow/listar",
     *     summary="Listar negociacao_parceria_workflow",
     *     description="Lista as negociacao_parceria_workflow do banco",
     *     produces={"application/json"},
     * @SWG\Response(
     *         response="200",
     *         description="Retorna os negociacao_parceria_workflow"
     *     ),
     * @SWG\Response(
     *         response="400",
     *         description="Ocorreu algum erro no servidor",
     *     )
     * )
     *
     * @FOSRest\QueryParam(name="pagina",     strict=false, allowBlank=false, default="1", description="Pagina para realizar o scroll", requirements="\d+")
     * @FOSRest\QueryParam(name="franqueada", strict=true, requirements="\d+",     allowBlank=false, description="ID da Franqueada")
     *
     * @FOSRest\QueryParam(name="order",   strict=false, nullable=true,  description="Coluna de ordenação")
     * @FOSRest\QueryParam(name="direcao", strict=false, nullable=true,  description="ASC|DESC")

     * @FOSRest\Get("/negociacao_parceria_workflow/listar")
     *
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function lista(ParamFetcher $request)
    {
        $parametros = $request->all();
        $mensagem   = "";
        $resultados = $this->negociacaoParceriaWorkflowFacade->listar($parametros);
        if ($resultados === false) {
            return ResponseFactory::badRequest([], $mensagem);
        }

        return ResponseFactory::ok($resultados);
    }

    /**
     *
     * @SWG\Get(
     *     path="/api/negociacao_parceria_workflow/{id}",
     *     summary="Buscar a negociacao_parceria_workflow",
     *     description="Busca a negociacao_parceria_workflow através da ID",
     *     produces={"application/json"},
     * @SWG\Response(
     *         response="200",
     *         description="Retorna a negociacao_parceria_workflow"
     *     ),
     * @SWG\Response(
     *         response="404",
     *         description="Ocorreu algum erro no servidor",
     *     )
     * )
     *
     * @FOSRest\Get("/negociacao_parceria_workflow/{id}")
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
     * @SWG\Post(
     *     path="/api/negociacao_parceria_workflow/criar",
     *     summary="Cria uma negociacao_parceria_workflow",
     *     description="Cria uma negociacao_parceria_workflow no banco",
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
     * @FOSRest\Post("/negociacao_parceria_workflow/criar")
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
     *     path="/api/negociacao_parceria_workflow/alterar/{id}",
     *     summary="Atualiza um negociacao_parceria_workflow",
     *     description="Atualiza um negociacao_parceria_workflow no banco",
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
     * @FOSRest\Patch("/negociacao_parceria_workflow/alterar/{id}")
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
     *     path="/api/negociacao_parceria_workflow/remover/{id}",
     *     summary="Remove uma negociacao_parceria_workflow",
     *     description="Remove uma negociacao_parceria_workflow no banco",
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
     * @FOSRest\Delete("/negociacao_parceria_workflow/remover/{id}")
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
