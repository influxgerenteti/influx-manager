<?php

namespace App\Controller\Principal\Retorno;

use Symfony\Component\Routing\Annotation\Route;
use FOS\RestBundle\Controller\Annotations as FOSRest;
use Swagger\Annotations as SWG;
use App\Factory\ResponseFactory;
use App\Controller\Principal\Base\GenericController;
use App\Facade\Principal\RetornoFacade;
use FOS\RestBundle\Request\ParamFetcher;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 *
 * @author        Rodrigo de Souza Fernandes (GATI labs)
 * @Route("/api")
 */
class RetornoController extends GenericController
{

    /**
     *
     * @var \App\Facade\Principal\RetornoFacade
     */
    private $retornoFacade;

    /**
     * {@inheritdoc}
     */
    protected function constroiFacades()
    {
        // Para criar o LogFacade do GenericController
        parent::constroiFacades();
        $this->retornoFacade = new RetornoFacade(self::getManagerRegistry());
    }

    /**
     *
     * @SWG\Post(
     *     path="/api/retorno/importar",
     *     summary="Importar o arquivo de retorno",
     *     description="Importar o arquivo de retorno",
     *     tags={"Importacao dos boletos"},
     *     consumes={"application/form-data"},
     *     produces={"application/json"},
     * @SWG\Response(
     *         response="201",
     *         description="Retorna os boletos importados"
     *     ),
     * @SWG\Response(
     *         response="202",
     *         description="Ocorreu algum erro no servidor",
     *     )
     * )
     *
     * @FOSRest\FileParam(name="arquivo",       requirements={"mimeTypes"={"text/plain"}}, strict=true)
     * @FOSRest\RequestParam(name="franqueada", strict=true, nullable=false, allowBlank=false, description="ID da Franqueada", requirements="\d+")
     *
     * @FOSRest\Post("/retorno/importar")
     *
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function importar(ParamFetcher $request, Request $requestHeader)
    {
        $caminhoArquivo = $request->get("arquivo")->getPathName();
        $parametros     = $request->all();
        $mensagem       = "";
        $boletosNE      = [];

        $boletosORM = $this->retornoFacade->importarRetorno($requestHeader, $caminhoArquivo, $parametros, $mensagem, $boletosNE);
        if (empty($mensagem) === false) {
            return ResponseFactory::conflict($parametros, $mensagem);
        }

        return ResponseFactory::created(["boletos" => $boletosORM, "boletosNE" => $boletosNE], "Registros atualizados com sucesso!");
    }


}
