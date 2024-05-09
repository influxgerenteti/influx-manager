<?php

namespace App\Controller\Principal\Indicadores;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use FOS\RestBundle\Controller\Annotations as FOSRest;
use Swagger\Annotations as SWG;
use App\Factory\ResponseFactory;
use App\Controller\Principal\Base\GenericController;
use FOS\RestBundle\Request\ParamFetcher;

/**
 *
 * @author        Marcelo A Naegeler
 * @Route("/api")
 */
class IndicadoresController extends GenericController
{

    /**
     *
     * @var \App\Facade\Principal\IndicadoresFacade
     */
    private $indicadoresFacade;


    /**
     * {@inheritdoc}
     */
    protected function constroiFacades()
    {
        // Para criar o LogFacade do GenericController
        parent::constroiFacades();
        $this->indicadoresFacade = new \App\Facade\Principal\IndicadoresFacade(self::getManagerRegistry());
    }

    /**
     *
     * @SWG\Get(
     *     path="/api/indicadores/listar",
     *     summary="Listar indicadores",
     *     description="Lista as indicadores do banco",
     *     produces={"application/json"},
     * @SWG\Response(
     *         response="200",
     *         description="Retorna os indicadores"
     *     ),
     * @SWG\Response(
     *         response="400",
     *         description="Ocorreu algum erro no servidor",
     *     )
     * )
     *
     * @FOSRest\QueryParam(name="ano",                      strict=false, allowBlank=false, description="Ano", requirements="\d{4}")
     * @FOSRest\QueryParam(name="mes",                      strict=false, allowBlank=false, description="Mês", requirements="\d{1,2}")
     * @FOSRest\QueryParam(name="franqueada_personalizada", strict=false, allowBlank=false, description="Franquia", requirements="\d+")
     * @FOSRest\QueryParam(name="funcionario",              strict=false, allowBlank=false, description="Funcionário", requirements="\d+")
     * @FOSRest\QueryParam(name="estado",                   strict=false, allowBlank=false, description="Estado", requirements="\d+")
     *
     * @FOSRest\Get("/indicadores/listar")
     *
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function lista(ParamFetcher $request)
    {
        $parametros = $request->all();
        $resultados = $this->indicadoresFacade->listar($parametros);

        return ResponseFactory::ok($resultados);
    }


}
