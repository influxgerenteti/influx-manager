<?php

namespace App\Controller\Importacao\Sponte;

use App\Service\SponteRequestDataService;
use FOS\RestBundle\Controller\Annotations as FOSRest;
use App\Factory\ResponseFactory;
use FOS\RestBundle\Request\ParamFetcher;



class SponteToManagerController 
{


    private SponteRequestDataService $requestDataService;

    public function __construct(
        SponteRequestDataService $requestDataService
    ) {
        $this->requestDataService = $requestDataService;
        
    }
    

   

    /**
     *
     * @FOSRest\RequestParam(name="id_sponte",  strict=true, nullable=false, allowBlank=false, description="ID da escola no manager", requirements="\d+")
     * @FOSRest\RequestParam(name="step", strict=true, nullable=true, allowBlank=true, description="step to force", requirements="\d+")
     * @FOSRest\Post("/integracao/escola/importar")
     *
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function escola_importar(ParamFetcher $request)
    {
         $time_init      = microtime(true);
        // $caminhoArquivo = $request->get("arquivo")->getPathName();
        
        $parametros     = $request->all();
        $escola = $parametros['id_sponte'];
        $step = 0;
        if(isset($parametros['step'])){
            $step = $parametros['step'];
        }
      
        $mensagemErro   = "";
        $this->requestDataService->import($escola,$step);
        $retorno = true;
        if ($retorno === false) {
            return ResponseFactory::internalServerError([], "erro ");
        }

        if (empty($mensagemErro) === false) {
            return ResponseFactory::created([], " erro ".$mensagemErro);
        }

        $time_end = microtime(true);
        return ResponseFactory::created([], "Concluido com sucesso. !<br> Tempo:" . ($time_end - $time_init));
    }


}
