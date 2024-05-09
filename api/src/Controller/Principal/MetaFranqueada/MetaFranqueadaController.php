<?php

namespace App\Controller\Principal\MetaFranqueada;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use FOS\RestBundle\Controller\Annotations as FOSRest;
use Swagger\Annotations as SWG;
use App\Factory\ResponseFactory;
use App\Controller\Principal\Base\GenericController;
use App\Facade\Principal\MetaFranqueadaFacade;
use FOS\RestBundle\Request\ParamFetcher;

/**
 *
 * @author        Marcelo A Naegeler
 * @Route("/api")
 */
class MetaFranqueadaController extends GenericController
{


    /**
     *
     * @var \App\Facade\Principal\MetaFranqueadaFacade
     */
    private $metaFranqueadaFacade;

    /**
     * {@inheritdoc}
     */
    protected function constroiFacades()
    {
        // Para criar o LogFacade do GenericController
        parent::constroiFacades();
        $this->metaFranqueadaFacade = new MetaFranqueadaFacade(self::getManagerRegistry());
    }

    /**
     *
     * @SWG\Get(
     *     path="/api/meta_franqueada/listar",
     *     summary="Listar meta_franqueada",
     *     description="Lista as meta_franqueada do banco",
     *     produces={"application/json"},
     * @SWG\Response(
     *         response="200",
     *         description="Retorna os meta_franqueada"
     *     ),
     * @SWG\Response(
     *         response="400",
     *         description="Ocorreu algum erro no servidor",
     *     )
     * )
     *
     * @FOSRest\QueryParam(name="pagina", strict=false, allowBlank=false, default="1", description="Pagina para realizar o scroll", requirements="\d+")
     * @FOSRest\QueryParam(name="mes",    strict=false, allowBlank=false, default="1", description="Mês", requirements="\d+")
     * @FOSRest\QueryParam(name="ano",    strict=false, allowBlank=false, description="Ano", requirements="\d+")
     * @FOSRest\QueryParam(name="estado", strict=false, description="Estado", requirements="\d+")
     *
     * @FOSRest\Get("/meta_franqueada/listar")
     *
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function lista(ParamFetcher $request)
    {
        $parametros = $request->all();
        $mensagem   = "";
        $resultados = $this->metaFranqueadaFacade->listar($parametros);

        if ($resultados === false) {
            return ResponseFactory::badRequest([], $mensagem);
        }

        return ResponseFactory::ok($resultados);
    }

    /**
     *
     * @SWG\Patch(
     *     path="/api/meta_franqueada/alterar/{id}",
     *     summary="Atualiza um meta_franqueada",
     *     description="Atualiza um meta_franqueada no banco",
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
     * @FOSRest\RequestParam(name="mes",                 strict=true, nullable=false, allowBlank=false, description="Mês")
     * @FOSRest\RequestParam(name="ano",                 strict=true, nullable=false, allowBlank=false, description="Ano")
     * @FOSRest\RequestParam(name="meta_1",              strict=true, nullable=true, allowBlank=true, description="Meta 1")
     * @FOSRest\RequestParam(name="meta_2",              strict=true, nullable=true, allowBlank=true, description="Meta 2")
     * @FOSRest\RequestParam(name="meta_3",              strict=true, nullable=true, allowBlank=true, description="Meta 3")
     * @FOSRest\RequestParam(name="meta_franqueadora_1", strict=true, nullable=true, allowBlank=true, description="Meta 1 Franqueadora")
     * @FOSRest\RequestParam(name="meta_franqueadora_2", strict=true, nullable=true, allowBlank=true, description="Meta 2 Franqueadora")
     * @FOSRest\RequestParam(name="meta_franqueadora_3", strict=true, nullable=true, allowBlank=true, description="Meta 3 Franqueadora")
     *
     * @FOSRest\Patch("/meta_franqueada/alterar/{id}")
     *
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function atualizar($id, ParamFetcher $request)
    {
        $parametros = $request->all();
        $mensagem   = "";
        $retorno    = $this->metaFranqueadaFacade->atualizar($mensagem, $id, $parametros);
        if ($retorno === false) {
            return ResponseFactory::badRequest(["parametros" => $parametros], $mensagem);
        }

        return ResponseFactory::noContent([]);
    }


}
