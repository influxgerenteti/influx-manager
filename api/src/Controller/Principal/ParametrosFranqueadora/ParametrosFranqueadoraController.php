<?php

namespace App\Controller\Principal\ParametrosFranqueadora;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use FOS\RestBundle\Controller\Annotations as FOSRest;
use Swagger\Annotations as SWG;
use App\Factory\ResponseFactory;
use App\Controller\Principal\Base\GenericController;
use FOS\RestBundle\Request\ParamFetcher;
use App\Facade\Principal\ParametrosFranqueadoraFacade;

/**
 *
 * @author        Luiz A Costa
 * @Route("/api")
 */
class ParametrosFranqueadoraController extends GenericController
{

    /**
     *
     * @var \App\Facade\Principal\ParametrosFranqueadoraFacade
     */
    private $parametrosFranqueadoraFacade;
    /**
     * {@inheritdoc}
     */
    protected function constroiFacades()
    {
        // Para criar o LogFacade do GenericController
        parent::constroiFacades();
        $this->parametrosFranqueadoraFacade = new ParametrosFranqueadoraFacade(self::getManagerRegistry());
    }

    /**
     *
     * @SWG\Get(
     *     path="/api/parametros_franqueadora/listar",
     *     summary="Listar parametros_franqueadoras",
     *     description="Lista as parametros_franqueadoras do banco",
     *     tags={"Parametros Franqueadas"},
     *     produces={"application/json"},
     * @SWG\Response(
     *         response="200",
     *         description="Retorna os parametros_franqueadoras"
     *     ),
     * @SWG\Response(
     *         response="400",
     *         description="Ocorreu algum erro no servidor",
     *     )
     * )
     *
     * @FOSRest\Get("/parametros_franqueadora/listar")
     *
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function lista(ParamFetcher $request)
    {
        $objetoORM = $this->parametrosFranqueadoraFacade->buscarPorId();
        if (is_null($objetoORM) === true) {
            return ResponseFactory::notFound([], "Erro CRITICO! Parametro não encontrado na base de dados.");
        }

        return ResponseFactory::ok($objetoORM);
    }

    /**
     *
     * @SWG\Patch(
     *     path="/api/parametros_franqueadora/atualizar/{id}",
     *     summary="Atualiza um parametros_franqueadoras",
     *     description="Atualiza um parametros_franqueadoras no banco",
     *     tags={"Parametros Franqueadas"},
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
     * @FOSRest\RequestParam(name="dias_variacao_vencimento",        strict=true, nullable=true, description="Dias de variacao de Vencimento", requirements="\d{0,9}")
     * @FOSRest\RequestParam(name="percentual_variacao_valores",     strict=true, nullable=true, description="Percentual de variacao de valores", requirements="^\d{0,3}+\.?\d{0,2}?$")
     * @FOSRest\RequestParam(name="limite_dias_alteracao_documento", strict=true, nullable=true, description="Limite de dias de vencimento", requirements="\d{0,9}")
     * @FOSRest\RequestParam(name="numero_maximo_parcelas",          strict=true, nullable=true, description="Número máximo de parcelas para conta a Pagar e conta a Receber", requirements="\d{0,9}")
     * @FOSRest\RequestParam(name="dias_reativacao_interessado",     strict=true, nullable=true, description="Reativação do Interessado após X Dias", requirements="\d{0,9}")
     * @FOSRest\RequestParam(name="nota_corte_media",                strict=true, nullable=true, description="Nota referente a média aceitavel para geracao da ocorrencia", requirements="\d*\.?\d*$")
     * @FOSRest\RequestParam(name="nota_conceitual_corte_media",     strict=true, nullable=true, description="Conceito referente a media aceitaval para geracao da ocorrencia", requirements="\d*\.?\d*$")
     *
     * @FOSRest\Patch("/parametros_franqueadora/atualizar/{id}")
     *
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function atualizar($id, ParamFetcher $request)
    {
        $parametros = $request->all();
        $mensagem   = "";
        $retorno    = $this->parametrosFranqueadoraFacade->atualizar($mensagem, $id, $parametros);
        if ($retorno === false) {
            return ResponseFactory::badRequest(["parametros" => $parametros], $mensagem);
        }

        return ResponseFactory::noContent([]);
    }


}
