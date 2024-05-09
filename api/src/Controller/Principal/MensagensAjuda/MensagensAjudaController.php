<?php

namespace App\Controller\Principal\MensagensAjuda;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use FOS\RestBundle\Controller\Annotations as FOSRest;
use Swagger\Annotations as SWG;
use App\Factory\ResponseFactory;
use App\Controller\Principal\Base\GenericController;
use FOS\RestBundle\Request\ParamFetcher;
use Symfony\Component\HttpFoundation\Request;
use App\Facade\Principal\MensagensAjudaFacade;

/**
 *
 * @author        Augusto
 * @Route("/api")
 */
class MensagensAjudaController extends GenericController
{

    /**
     *
     * @var \App\Facade\Principal\MensagensAjudaFacade $mensagensAjudaFacade
     */
    private $mensagensAjudaFacade;

    /**
     * {@inheritdoc}
     */
    protected function constroiFacades()
    {
        // Para criar o LogFacade do GenericController
        parent::constroiFacades();
        $this->mensagensAjudaFacade = new MensagensAjudaFacade(self::getManagerRegistry());
    }

    /**
     *
     * @SWG\Get(
     *     path="/api/mensagens_ajuda/listar",
     *     summary="Listar mensagens_ajuda",
     *     description="Lista as mensagens_ajuda do banco",
     *     produces={"application/json"},
     * @SWG\Response(
     *         response="200",
     *         description="Retorna os mensagens_ajuda"
     *     ),
     * @SWG\Response(
     *         response="400",
     *         description="Ocorreu algum erro no servidor",
     *     )
     * )
     *
     * @FOSRest\QueryParam(name="pagina", strict=false, allowBlank=false, default="1", description="Pagina para realizar o scroll", requirements="\d+")
     *
     * @FOSRest\Get("/mensagens_ajuda/listar")
     *
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function lista (Request $request)
    {
        $modulo     = $request->headers->get('URLModulo');
        $resultados = $this->mensagensAjudaFacade->listar($modulo);
        return ResponseFactory::ok($resultados);
    }


}
