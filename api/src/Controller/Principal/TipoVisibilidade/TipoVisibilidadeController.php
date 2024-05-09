<?php

namespace App\Controller\Principal\TipoVisibilidade;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use FOS\RestBundle\Controller\Annotations as FOSRest;
use Swagger\Annotations as SWG;
use App\Factory\ResponseFactory;
use App\Controller\Principal\Base\GenericController;
use FOS\RestBundle\Request\ParamFetcher;
use App\Facade\Principal\TipoVisibilidadeFacade;

/**
 *
 * @author        Luiz A Costa
 * @Route("/api")
 */
class TipoVisibilidadeController extends GenericController
{

    /**
     *
     * @var \App\Facade\Principal\TipoVisibilidadeFacade
     */
    protected $tipoVisibilidadeFacade;

    /**
     * {@inheritdoc}
     */
    protected function constroiFacades()
    {
        // Para criar o LogFacade do GenericController
        parent::constroiFacades();
        $this->tipoVisibilidadeFacade = new TipoVisibilidadeFacade(self::getManagerRegistry());
    }

    /**
     *
     * @SWG\Get(
     *     path="/api/tipo_visibilidade/listar",
     *     summary="Listar tipo_visibilidade",
     *     description="Lista as tipo_visibilidade do banco",
     *     produces={"application/json"},
     * @SWG\Response(
     *         response="200",
     *         description="Retorna os tipo_visibilidade"
     *     ),
     * @SWG\Response(
     *         response="400",
     *         description="Ocorreu algum erro no servidor",
     *     )
     * )
     *
     * @FOSRest\QueryParam(name="pagina", strict=false, allowBlank=false, default="1", description="Pagina para realizar o scroll", requirements="\d+")
     *
     * @FOSRest\Get("/tipo_visibilidade/listar")
     *
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function lista(ParamFetcher $request)
    {
        $parametros = $request->all();
        $mensagem   = "";
        $resultados = "";
        // TODO::seu face;
        if ($resultados === false) {
            return ResponseFactory::badRequest([], $mensagem);
        }

        return ResponseFactory::ok($resultados);
    }

    /**
     *
     * @SWG\Get(
     *     path="/api/tipo_visibilidade/buscar_todos",
     *     summary="Listar tipo_visibilidade",
     *     description="Lista as tipo_visibilidade do banco",
     *     produces={"application/json"},
     * @SWG\Response(
     *         response="200",
     *         description="Retorna os tipo_visibilidade"
     *     ),
     * @SWG\Response(
     *         response="400",
     *         description="Ocorreu algum erro no servidor",
     *     )
     * )
     *
     * @FOSRest\Get("/tipo_visibilidade/buscar_todos")
     *
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function buscarTodos()
    {
        $resultados = $this->tipoVisibilidadeFacade->buscarTodos();
        return ResponseFactory::ok($resultados);
    }

    /**
     *
     * @SWG\Get(
     *     path="/api/tipo_visibilidade/{id}",
     *     summary="Buscar a tipo_visibilidade",
     *     description="Busca a tipo_visibilidade através da ID",
     *     produces={"application/json"},
     * @SWG\Response(
     *         response="200",
     *         description="Retorna a tipo_visibilidade"
     *     ),
     * @SWG\Response(
     *         response="404",
     *         description="Ocorreu algum erro no servidor",
     *     )
     * )
     *
     * @FOSRest\Get("/tipo_visibilidade/{id}")
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
     *     path="/api/tipo_visibilidade/criar",
     *     summary="Cria uma tipo_visibilidade",
     *     description="Cria uma tipo_visibilidade no banco",
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
     * @FOSRest\RequestParam(name="franqueada", strict=true, nullable=false, allowBlank=false, description="Franqueada", requirements="\d+")
     * @FOSRest\RequestParam(name="descricao",  strict=true, nullable=false, allowBlank=false, description="Descricao")
     * @FOSRest\RequestParam(name="tipo",       strict=true, nullable=false, allowBlank=false, description="Tipo da Visibilidade", requirements=".{0,3}")
     *
     * @FOSRest\Post("/tipo_visibilidade/criar")
     *
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function criar(ParamFetcher $request)
    {
        $parametros = $request->all();
        $mensagem   = "";
        $objetoORM  = $this->tipoVisibilidadeFacade->criar($mensagem, $parametros);
        if ((is_null($objetoORM) === true) || (empty($mensagem) === false)) {
            return ResponseFactory::conflict(["parametros" => $parametros], $mensagem);
        }

        return ResponseFactory::created(["objetoORM" => $objetoORM->getId()], "Registro criado com sucesso!");
    }

    /**
     *
     * @SWG\Patch(
     *     path="/api/tipo_visibilidade/alterar/{id}",
     *     summary="Atualiza um tipo_visibilidade",
     *     description="Atualiza um tipo_visibilidade no banco",
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
     * @FOSRest\RequestParam(name="descricao", strict=true, nullable=false, allowBlank=false, description="Descricao")
     * @FOSRest\RequestParam(name="tipo",      strict=false, nullable=false, allowBlank=false, description="Tipo da Visibilidade", requirements=".{0,3}")
     * @FOSRest\RequestParam(name="situacao",  strict=true, nullable=false, allowBlank=false, description="Situação", requirements="(A|I)")
     *
     * @FOSRest\Patch("/tipo_visibilidade/alterar/{id}")
     *
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function atualizar($id, ParamFetcher $request)
    {
        $parametros = $request->all();
        $mensagem   = "";
        $retorno    = $this->tipoVisibilidadeFacade->atualizar($mensagem, $id, $parametros);
        if ($retorno === false) {
            return ResponseFactory::badRequest(["parametros" => $parametros], $mensagem);
        }

        return ResponseFactory::noContent([]);
    }

    /**
     *
     * @SWG\Delete(
     *     path="/api/tipo_visibilidade/remover/{id}",
     *     summary="Remove uma tipo_visibilidade",
     *     description="Remove uma tipo_visibilidade no banco",
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
     * @FOSRest\Delete("/tipo_visibilidade/remover/{id}")
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
