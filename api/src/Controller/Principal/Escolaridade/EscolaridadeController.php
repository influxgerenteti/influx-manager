<?php

namespace App\Controller\Principal\Escolaridade;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use FOS\RestBundle\Controller\Annotations as FOSRest;
use Swagger\Annotations as SWG;
use App\Factory\ResponseFactory;
use App\Controller\Principal\Base\GenericController;
use FOS\RestBundle\Request\ParamFetcher;
use App\Facade\Principal\EscolaridadeFacade;

/**
 *
 * @author        Luiz A Costa
 * @Route("/api")
 */
class EscolaridadeController extends GenericController
{
    /**
     *
     * @var \App\Facade\Principal\EscolaridadeFacade
     */
    private $escolaridadeFacade;

    /**
     * {@inheritdoc}
     */
    protected function constroiFacades()
    {
        // Para criar o LogFacade do GenericController
        parent::constroiFacades();
        $this->escolaridadeFacade = new EscolaridadeFacade(self::getManagerRegistry());
    }

    /**
     *
     * @SWG\Get(
     *     path="/api/escolaridade/listar",
     *     summary="Listar escolaridade",
     *     description="Lista as escolaridade do banco",
     *     tags={"Escolaridade"},
     *     produces={"application/json"},
     * @SWG\Response(
     *         response="200",
     *         description="Retorna os escolaridade"
     *     ),
     * @SWG\Response(
     *         response="400",
     *         description="Ocorreu algum erro no servidor",
     *     )
     * )
     *
     * @FOSRest\QueryParam(name="pagina", strict=false, allowBlank=false, default="1", description="Pagina para realizar o scroll", requirements="\d+")
     *
     * @FOSRest\Get("/escolaridade/listar")
     *
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function lista(ParamFetcher $request)
    {
        $parametros = $request->all();
        $mensagem   = "";
        $resultados = $this->escolaridadeFacade->listar($parametros);
        if ($resultados === false) {
            return ResponseFactory::badRequest([], $mensagem);
        }

        return ResponseFactory::ok($resultados);
    }

    /**
     *
     * @SWG\Get(
     *     path="/api/escolaridade/{id}",
     *     summary="Buscar a escolaridade",
     *     description="Busca a escolaridade através da ID",
     *     tags={"Escolaridade"},
     *     produces={"application/json"},
     * @SWG\Response(
     *         response="200",
     *         description="Retorna a escolaridade"
     *     ),
     * @SWG\Response(
     *         response="404",
     *         description="Ocorreu algum erro no servidor",
     *     )
     * )
     *
     * @FOSRest\Get("/escolaridade/{id}")
     *
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function buscar($id)
    {
        $mensagem  = "";
        $objetoORM = $this->escolaridadeFacade->buscarPorId($mensagem, $id);
        if (is_null($objetoORM) === true) {
            return ResponseFactory::notFound([], $mensagem);
        }

        return ResponseFactory::ok($objetoORM);
    }


}
