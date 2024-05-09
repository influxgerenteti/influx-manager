<?php

namespace App\Controller\$(namespace)\$(nomeController);

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use FOS\RestBundle\Controller\Annotations as FOSRest;
use Swagger\Annotations as SWG;
use App\Factory\ResponseFactory;
use App\Controller\Principal\Base\GenericController;
use FOS\RestBundle\Request\ParamFetcher;

/**
 *
 * @author        $(AUTOR)
 * @Route("/api")
 */
class $(nomeController)Controller extends GenericController
{
    

    /**
     * {@inheritdoc}
     */
    protected function constroiFacades()
    {
        // Para criar o LogFacade do GenericController
        parent::constroiFacades();        
    }

    /**
     *
     * @SWG\Get(
     *     path="/api/$(nomeRota)/listar",
     *     summary="Listar $(nomeRota)",
     *     description="Lista as $(nomeRota) do banco",
     *     produces={"application/json"},
     * @SWG\Response(
     *         response="200",
     *         description="Retorna os $(nomeRota)"
     *     ),
     * @SWG\Response(
     *         response="400",
     *         description="Ocorreu algum erro no servidor",
     *     )
     * )
     *
     * @FOSRest\QueryParam(name="pagina",         strict=false, allowBlank=false, default="1", description="Pagina para realizar o scroll", requirements="\d+")     
     *
     * @FOSRest\Get("/$(nomeRota)/listar")
     *
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function lista(ParamFetcher $request)
    {
        $parametros = $request->all();
        $mensagem   = "";
        $resultados = "";//TODO::seu face;
        if ($resultados === false) {
            return ResponseFactory::badRequest([], $mensagem);
        }
        
        return ResponseFactory::ok($resultados);
    }

    /**
     *
     * @SWG\Get(
     *     path="/api/$(nomeRota)/{id}",
     *     summary="Buscar a $(nomeRota)",
     *     description="Busca a $(nomeRota) através da ID",
     *     produces={"application/json"},
     * @SWG\Response(
     *         response="200",
     *         description="Retorna a $(nomeRota)"
     *     ),
     * @SWG\Response(
     *         response="404",
     *         description="Ocorreu algum erro no servidor",
     *     )
     * )
     *
     * @FOSRest\Get("/$(nomeRota)/{id}")
     *
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function buscar($id)
    {
        $objetoORM = null;//TODO: seu objeto ORM
        if (is_null($objetoORM) === true) {
            return ResponseFactory::notFound([], "OBJETO ORM não encontrada.");
        }

        return ResponseFactory::ok($objetoORM);
    }

    /**
     *
     * @SWG\Post(
     *     path="/api/$(nomeRota)/criar",
     *     summary="Cria uma $(nomeRota)",
     *     description="Cria uma $(nomeRota) no banco",
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
     * @FOSRest\RequestParam(name="exemplo_string",                         strict=true, nullable=false, allowBlank=false, description="exemplo_string")     
     * @FOSRest\RequestParam(name="exemplo_integer",   strict=true, nullable=false, allowBlank=false, description="exemplo_integer", default="0")
     *
     * @FOSRest\Post("/$(nomeRota)/criar")
     *
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function criar(ParamFetcher $request)
    {
        $parametros  = $request->all();
        $mensagem    = "";
        $objetoORM = "";//TODO: Seu objeto ORM
        if ((is_null($objetoORM) === true) || (empty($mensagem) === false)) {
            return ResponseFactory::conflict(["parametros" => $parametros], $mensagem);
        }

        return ResponseFactory::created(["objetoORM" => $objetoORM->getId()], "Registro criado com sucesso!");
    }

    /**
     *
     * @SWG\Patch(
     *     path="/api/$(nomeRota)/alterar/{id}",
     *     summary="Atualiza um $(nomeRota)",
     *     description="Atualiza um $(nomeRota) no banco",
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
     * @FOSRest\RequestParam(name="exemplo_string",                         strict=true, nullable=false, allowBlank=false, description="exemplo_string")     
     * @FOSRest\RequestParam(name="exemplo_integer",   strict=true, nullable=false, allowBlank=false, description="exemplo_integer", default="0")
     *
     * @FOSRest\Patch("/$(nomeRota)/alterar/{id}")
     *
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function atualizar($id, ParamFetcher $request)
    {
        $parametros = $request->all();
        $mensagem   = "";
        $retorno    = false;//TODO: True ou False
        if ($retorno === false) {
            return ResponseFactory::badRequest(["parametros" => $parametros], $mensagem);
        }

        return ResponseFactory::noContent([]);
    }

    /**
     *
     * @SWG\Delete(
     *     path="/api/$(nomeRota)/remover/{id}",
     *     summary="Remove uma $(nomeRota)",
     *     description="Remove uma $(nomeRota) no banco",
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
     * @FOSRest\Delete("/$(nomeRota)/remover/{id}")
     *
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function excluir($id)
    {
        $mensagem = "";
        $retorno  = false;//TODO: True ou False
        if ($retorno === false) {
            return ResponseFactory::badRequest([], $mensagem);
        }
        
        return ResponseFactory::ok([], "Excluido com sucesso");
    }


}
