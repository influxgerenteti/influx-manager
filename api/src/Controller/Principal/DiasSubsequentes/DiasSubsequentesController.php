<?php

namespace App\Controller\Principal\DiasSubsequentes;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use FOS\RestBundle\Controller\Annotations as FOSRest;
use Swagger\Annotations as SWG;
use App\Factory\ResponseFactory;
use App\Controller\Principal\Base\GenericController;
use FOS\RestBundle\Request\ParamFetcher;
use App\Facade\Principal\DiasSubsequentesFacade;

/**
 *
 * @author        Luiz A Costa
 * @Route("/api")
 */
class DiasSubsequentesController extends GenericController
{

    /**
     *
     * @var \App\Facade\Principal\DiasSubsequentesFacade
     */
    private $diasSubsequentesFacade;

    /**
     * {@inheritdoc}
     */
    protected function constroiFacades()
    {
        // Para criar o LogFacade do GenericController
        parent::constroiFacades();
        $this->diasSubsequentesFacade = new DiasSubsequentesFacade(self::getManagerRegistry());
    }

    /**
     *
     * @SWG\Get(
     *     path="/api/dias_subsequentes/listar",
     *     summary="Listar dias_subsequentes",
     *     description="Lista as dias_subsequentes do banco",
     *     produces={"application/json"},
     * @SWG\Response(
     *         response="200",
     *         description="Retorna os dias_subsequentes"
     *     ),
     * @SWG\Response(
     *         response="400",
     *         description="Ocorreu algum erro no servidor",
     *     )
     * )
     *
     * @FOSRest\Get("/dias_subsequentes/listar")
     *
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function lista(ParamFetcher $request)
    {
        $resultados = $this->diasSubsequentesFacade->listarTodosDiasSubsequentes();

        return ResponseFactory::ok($resultados);
    }

    /**
     *
     * @SWG\Get(
     *     path="/api/dias_subsequentes/buscar/",
     *     summary="Buscar a dias_subsequentes",
     *     description="Busca a dias_subsequentes através da ID",
     *     produces={"application/json"},
     * @SWG\Response(
     *         response="200",
     *         description="Retorna a dias_subsequentes"
     *     ),
     * @SWG\Response(
     *         response="404",
     *         description="Ocorreu algum erro no servidor",
     *     )
     * )
     *
     * @FOSRest\QueryParam(name="franqueada", strict=true, nullable=false, description="ID da franqueada", requirements="\d+")
     *
     * @FOSRest\Get("/dias_subsequentes/buscar/")
     *
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function buscar(ParamFetcher $request)
    {
        $mensagemErro = "";
        $objetoORM    = $this->diasSubsequentesFacade->buscarPorFranqueada($mensagemErro, $request->get("franqueada", true));
        if (is_null($objetoORM) === true) {
            return ResponseFactory::notFound([], $mensagemErro);
        }

        return ResponseFactory::ok($objetoORM);
    }

    /**
     *
     * @SWG\Patch(
     *     path="/api/dias_subsequentes/atualizar/{id}",
     *     summary="Atualiza um dias_subsequentes",
     *     description="Atualiza um dias_subsequentes no banco",
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
     * @FOSRest\RequestParam(name="dias_subsequentes", strict=true, nullable=false, allowBlank=false, description="Dias Subsequentes selecionados", map=true, requirements="\d+")
     *
     * @FOSRest\Patch("/dias_subsequentes/atualizar/{id}")
     *
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function atualizar($id, ParamFetcher $request)
    {
        $parametros = $request->all();
        $mensagem   = "";
        $retorno    = $this->diasSubsequentesFacade->atualizar($mensagem, $id, $parametros);
        if ($retorno === false) {
            return ResponseFactory::badRequest(["parametros" => $parametros], $mensagem);
        }

        return ResponseFactory::noContent([]);
    }


}
