<?php

namespace App\Controller\Principal\Remessa;

use Symfony\Component\Routing\Annotation\Route;
use FOS\RestBundle\Controller\Annotations as FOSRest;
use Swagger\Annotations as SWG;
use App\Factory\ResponseFactory;
use App\Controller\Principal\Base\GenericController;
use App\Facade\Principal\RemessaFacade;
use FOS\RestBundle\Request\ParamFetcher;
use Symfony\Component\HttpFoundation\Request;
use App\Helper\ConstanteParametros;
use App\Helper\SituacoesSistema;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\ResponseHeaderBag;
use Symfony\Component\HttpFoundation\BinaryFileResponse;

/**
 *
 * @author        Rodrigo de Souza Fernandes (GATI labs)
 * @Route("/api")
 */
class RemessaController extends GenericController
{

    /**
     *
     * @var \App\Facade\Principal\RemessaFacade
     */
    private $remessaFacade;

    /**
     * {@inheritdoc}
     */
    protected function constroiFacades()
    {
        // Para criar o LogFacade do GenericController
        parent::constroiFacades();
        $this->remessaFacade = new RemessaFacade(self::getManagerRegistry());
    }

    /**
     *
     * @SWG\Get(
     *     path="/api/remessa/imprimir",
     *     summary="Imprimir o arquivo de remessa",
     *     description="Imprime o arquivo de remessa",
     *     produces={"application/json"},
     * @SWG\Response(
     *         response="200",
     *         description="Retorna o arquivo de remessa"
     *     ),
     * @SWG\Response(
     *         response="400",
     *         description="Ocorreu algum erro no servidor",
     *     )
     * )
     *
     * @FOSRest\QueryParam(name="boletos", strict=true, nullable=false, description="Id Boletos", map=true, requirements="\d+")
     *
     * @FOSRest\Get("/remessa/imprimir")
     *
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function imprimir(ParamFetcher $paramFetcher)
    {
        $parametros = $paramFetcher->all();
        $mensagem   = "";
        // $date = new \DateTime();
        $filename = $this->remessaFacade->imprimirRemessa($parametros, $mensagem);
        if (is_null($filename) === true) {
            return ResponseFactory::badRequest([], $mensagem);
        }

        $file = $this->file($filename);
        $file->deleteFileAfterSend(true);
        return $file;
    }


}
