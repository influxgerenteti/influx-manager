<?php

namespace App\Controller\Principal\Estado;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use FOS\RestBundle\Controller\Annotations as FOSRest;
use Swagger\Annotations as SWG;
use App\Factory\ResponseFactory;
use App\Controller\Principal\Base\GenericController;
use FOS\RestBundle\Request\ParamFetcher;
use App\Facade\Principal\EstadoFacade;

/**
 *
 * @author        Luiz A Costa
 * @Route("/api")
 */
class EstadoController extends GenericController
{

    /**
     *
     * @var \App\Facade\Principal\EstadoFacade
     */
    private $estadoFacade;

    /**
     * {@inheritdoc}
     */
    protected function constroiFacades()
    {
        // Para criar o LogFacade do GenericController
        parent::constroiFacades();
        $this->estadoFacade = new EstadoFacade(self::getManagerRegistry());
    }

    /**
     *
     * @SWG\Get(
     *     path="/api/estado/listar",
     *     summary="Listar estado",
     *     description="Lista as estado do banco",
     *     tags={"Estado"},
     *     produces={"application/json"},
     * @SWG\Response(
     *         response="200",
     *         description="Retorna os estado"
     *     ),
     * @SWG\Response(
     *         response="400",
     *         description="Ocorreu algum erro no servidor",
     *     )
     * )
     *
     * @FOSRest\QueryParam(name="pagina", strict=false, allowBlank=false, default="1", description="Pagina para realizar o scroll", requirements="\d+")
     * @FOSRest\QueryParam(name="nome",   strict=false, allowBlank=false, description="Descricao Estado")
     *
     * @FOSRest\Get("/estado/listar")
     *
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function lista(ParamFetcher $request)
    {
        $parametros = $request->all();
        $mensagem   = "";
        $resultados = $this->estadoFacade->listar($parametros);
        if ($resultados === false) {
            return ResponseFactory::badRequest([], $mensagem);
        }

        return ResponseFactory::ok($resultados);
    }

    /**
     *
     * @SWG\Get(
     *     path="/api/estado/{id}",
     *     summary="Buscar a estado",
     *     description="Busca a estado através da ID",
     *     tags={"Estado"},
     *     produces={"application/json"},
     * @SWG\Response(
     *         response="200",
     *         description="Retorna a estado"
     *     ),
     * @SWG\Response(
     *         response="404",
     *         description="Ocorreu algum erro no servidor",
     *     )
     * )
     *
     * @FOSRest\Get("/estado/{id}")
     *
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function buscar($id)
    {
        $objetoORM = $this->estadoFacade->buscarPorId($id);
        if (is_null($objetoORM) === true) {
            return ResponseFactory::notFound([], "Não foi possivel encontrar um estado com a ID informada");
        }

        return ResponseFactory::ok($objetoORM);
    }


}
