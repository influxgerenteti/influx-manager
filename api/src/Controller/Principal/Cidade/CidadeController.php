<?php

namespace App\Controller\Principal\Cidade;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use FOS\RestBundle\Controller\Annotations as FOSRest;
use Swagger\Annotations as SWG;
use App\Factory\ResponseFactory;
use App\Controller\Principal\Base\GenericController;
use FOS\RestBundle\Request\ParamFetcher;
use App\Facade\Principal\CidadeFacade;

/**
 *
 * @author        Luiz A Costa
 * @Route("/api")
 */
class CidadeController extends GenericController
{
    /**
     *
     * @var \App\Facade\Principal\CidadeFacade
     */
    private $cidadeFacade;

    /**
     * {@inheritdoc}
     */
    protected function constroiFacades()
    {
        // Para criar o LogFacade do GenericController
        parent::constroiFacades();
        $this->cidadeFacade = new CidadeFacade(self::getManagerRegistry());
    }

    /**
     *
     * @SWG\Get(
     *     path="/api/cidade/listar",
     *     summary="Listar cidade",
     *     description="Lista as cidade do banco",
     *     tags={"Cidade"},
     *     produces={"application/json"},
     * @SWG\Response(
     *         response="200",
     *         description="Retorna os cidade"
     *     ),
     * @SWG\Response(
     *         response="400",
     *         description="Ocorreu algum erro no servidor",
     *     )
     * )
     *
     * @FOSRest\QueryParam(name="pagina", strict=false, allowBlank=false, default="1", description="Pagina para realizar o scroll", requirements="\d+")
     * @FOSRest\QueryParam(name="estado", strict=true, allowBlank=false, description="Estado ID", requirements="\d+")
     * @FOSRest\QueryParam(name="nome",   strict=false, allowBlank=false, description="Descricao Cidade")
     *
     * @FOSRest\Get("/cidade/listar")
     *
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function lista(ParamFetcher $request)
    {
        $parametros = $request->all();
        $mensagem   = "";
        $resultados = $this->cidadeFacade->listar($parametros);
        if ($resultados === false) {
            return ResponseFactory::badRequest([], $mensagem);
        }

        return ResponseFactory::ok($resultados);
    }

    /**
     *
     * @SWG\Get(
     *     path="/api/cidade/{id}",
     *     summary="Buscar a cidade",
     *     description="Busca a cidade através da ID",
     *     tags={"Cidade"},
     *     produces={"application/json"},
     * @SWG\Response(
     *         response="200",
     *         description="Retorna a cidade"
     *     ),
     * @SWG\Response(
     *         response="404",
     *         description="Ocorreu algum erro no servidor",
     *     )
     * )
     *
     * @FOSRest\Get("/cidade/{id}")
     *
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function buscar($id)
    {
        $objetoORM = $this->cidadeFacade->buscarPorId($id);
        if (is_null($objetoORM) === true) {
            return ResponseFactory::notFound([], "Não foi possivel encontrar uma cidade com a ID informada");
        }

        return ResponseFactory::ok($objetoORM);
    }


}
