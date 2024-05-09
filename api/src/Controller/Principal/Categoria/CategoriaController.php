<?php

namespace App\Controller\Principal\Categoria;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use FOS\RestBundle\Controller\Annotations as FOSRest;
use Swagger\Annotations as SWG;
use App\Factory\ResponseFactory;
use App\Controller\Principal\Base\GenericController;
use FOS\RestBundle\Request\ParamFetcher;
use App\Facade\Principal\CategoriaFacade;

/**
 *
 * @author        Luiz A Costa
 * @Route("/api")
 */
class CategoriaController extends GenericController
{

    /**
     *
     * @var \App\Facade\Principal\CategoriaFacade
     */
    private $categoriaFacade;

    /**
     * {@inheritdoc}
     */
    protected function constroiFacades()
    {
        // Para criar o LogFacade do GenericController
        parent::constroiFacades();
        $this->categoriaFacade = new CategoriaFacade(self::getManagerRegistry());
    }

    /**
     *
     * @SWG\Get(
     *     path="/api/categoria/listar",
     *     summary="Listar categoria",
     *     description="Lista as categorias do banco",
     *     tags={"Categoria"},
     *     produces={"application/json"},
     * @SWG\Response(
     *         response="200",
     *         description="Retorna as categorias"
     *     ),
     * @SWG\Response(
     *         response="400",
     *         description="Ocorreu algum erro no servidor",
     *     )
     * )
     *
     * @FOSRest\QueryParam(name="pagina", strict=false, requirements="\d{0,2}", allowBlank=false, default="1", description="Pagina para realizar o scroll")
     *
     * @FOSRest\Get("/categoria/listar")
     *
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function lista(ParamFetcher $paramFetcher)
    {
        $parametros = $paramFetcher->all();
        $resultados = $this->categoriaFacade->listar($parametros);
        return ResponseFactory::ok($resultados);
    }

    /**
     *
     * @SWG\Get(
     *     path="/api/categoria/{id}",
     *     summary="Buscar a categoria",
     *     description="Busca a categoria através da ID",
     *     tags={"Categoria"},
     *     produces={"application/json"},
     * @SWG\Response(
     *         response="200",
     *         description="Retorna a categoria"
     *     ),
     * @SWG\Response(
     *         response="404",
     *         description="Ocorreu algum erro no servidor",
     *     )
     * )
     *
     * @FOSRest\Get("/categoria/{id}")
     *
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function buscar($id)
    {
        $mensagem  = "";
        $objetoORM = $this->categoriaFacade->buscarPorId($mensagem, $id);
        if (is_null($objetoORM) === true)
            return ResponseFactory::notFound([], "Categoria não encontrada.");
        return ResponseFactory::ok($objetoORM);
    }

    /**
     *
     * @SWG\Post(
     *     path="/api/categoria/criar",
     *     summary="Cria uma categoria",
     *     description="Cria uma categoria no banco",
     *     tags={"Categoria"},
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
     * @FOSRest\RequestParam(name="nome", strict=true, nullable=false, allowBlank=true, description="Nome da categoria", requirements="[\w\s]+")
     *
     * @FOSRest\Post("/categoria/criar")
     *
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function criar(ParamFetcher $request)
    {
        $parametros = $request->all();
        $mensagem   = "";
        $objetoORM  = $this->categoriaFacade->criar($mensagem, $parametros);
        if ((is_null($objetoORM) === true) || (empty($mensagem) === false)) {
            return ResponseFactory::conflict(["parametros" => $parametros], $mensagem);
        }

        return ResponseFactory::created(["objetoORM" => $objetoORM], "Registro criado com sucesso!");
    }

    /**
     *
     * @SWG\Patch(
     *     path="/api/categoria/atualizar/{id}",
     *     summary="Atualiza uma categoria",
     *     description="Atualiza uma categoria no banco",
     *     tags={"Categoria"},
     *     consumes={"application/x-www-form-urlencoded"},
     *     produces={"application/json"},
     * @SWG\Response(
     *         response="204",
     *         description="Retorna criado com sucesso"
     *     ),
     * @SWG\Response(
     *         response="400",
     *         description="Ocorreu algum erro no servidor",
     *     )
     * )
     *
     * @FOSRest\RequestParam(name="nome",     strict=true, nullable=false, allowBlank=true, description="Nome da categoria", requirements="[\w\s]+")
     * @FOSRest\RequestParam(name="exclusao", strict=true, nullable=false, allowBlank=false, description="Flag de exclusao", default="0", requirements="\d")
     *
     * @FOSRest\Patch("/categoria/atualizar/{id}")
     *
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function atualizar($id, ParamFetcher $paramFetcher)
    {
        $parametros = $paramFetcher->all();
        $mensagem   = "";
        $retorno    = $this->categoriaFacade->atualizar($mensagem, $id, $parametros);
        if ($retorno === false) {
            return ResponseFactory::badRequest(["parametros" => $parametros], $mensagem);
        }

        return ResponseFactory::noContent([]);
    }

    /**
     *
     * @SWG\Delete(
     *     path="/api/categoria/remover/{id}",
     *     summary="Remove uma categoria",
     *     description="Remove uma categoria no banco",
     *     tags={"Categoria"},
     *     produces={"application/json"},
     * @SWG\Response(
     *         response="200",
     *         description="Retorna criado com sucesso"
     *     ),
     * @SWG\Response(
     *         response="400",
     *         description="Ocorreu algum erro no servidor",
     *     )
     * )
     *
     * @FOSRest\Delete("/categoria/remover/{id}")
     *
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function excluir($id)
    {
        $mensagem = "";
        $retorno  = $this->categoriaFacade->remover($mensagem, $id);
        if ($retorno === false) {
            return ResponseFactory::badRequest([], $mensagem);
        }

        return ResponseFactory::ok([], "Excluido com sucesso");
    }


}
