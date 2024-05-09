<?php

namespace App\Controller\Principal\Cep;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use FOS\RestBundle\Controller\Annotations as FOSRest;
use Swagger\Annotations as SWG;
use App\Factory\ResponseFactory;
use App\Controller\Principal\Base\GenericController;
use FOS\RestBundle\Request\ParamFetcher;
use App\Facade\Principal\CepFacade;
use App\Helper\ConstanteParametros;

/**
 *
 * @author        Luiz A Costa
 * @Route("/api")
 */
class CepController extends GenericController
{

    /**
     *
     * @var \App\Facade\Principal\CepFacade
     */
    private $cepFacade;

    /**
     * {@inheritdoc}
     */
    protected function constroiFacades()
    {
        // Para criar o LogFacade do GenericController
        parent::constroiFacades();
        $this->cepFacade = new CepFacade(self::getManagerRegistry());
    }

    /**
     *
     * @SWG\Get(
     *     path="/api/cep/buscar/{cep}",
     *     summary="Buscar o cep",
     *     description="Busca o cep através do CEP informado",
     *     tags={"CEP WS"},
     *     produces={"application/json"},
     * @SWG\Response(
     *         response="200",
     *         description="Retorna a cep"
     *     ),
     * @SWG\Response(
     *         response="404",
     *         description="Ocorreu algum erro no servidor",
     *     )
     * )
     *
     * @FOSRest\Get("/cep/buscar/{cep}")
     *
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function buscar($cep)
    {
        $mensagemErro = "";
        $cep          = str_pad($cep, 8, "0", STR_PAD_LEFT);
        $objetoORM    = $this->cepFacade->buscarCep($mensagemErro, $cep);
        if (is_null($objetoORM) === true) {
            return ResponseFactory::ok(["error" => true, "mensagem" => $mensagemErro]);
        }

        return ResponseFactory::ok(
            [
                ConstanteParametros::CHAVE_CEP         => $objetoORM->getCep(),
                ConstanteParametros::CHAVE_ESTADO      => $objetoORM->getEstado()->getId(),
                ConstanteParametros::CHAVE_ESTADO_NOME => $objetoORM->getEstado()->getNome(),
                ConstanteParametros::CHAVE_ESTADO_UF   => $objetoORM->getEstado()->getSigla(),
                ConstanteParametros::CHAVE_CIDADE      => $objetoORM->getCidade()->getId(),
                ConstanteParametros::CHAVE_CIDADE_NOME => $objetoORM->getCidade()->getNome(),
                ConstanteParametros::CHAVE_BAIRRO      => $objetoORM->getBairro(),
                ConstanteParametros::CHAVE_RUA         => $objetoORM->getRua(),
            ]
        );
    }


}
