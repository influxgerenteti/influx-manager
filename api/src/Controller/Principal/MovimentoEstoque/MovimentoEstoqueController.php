<?php

namespace App\Controller\Principal\MovimentoEstoque;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use FOS\RestBundle\Controller\Annotations as FOSRest;
use Swagger\Annotations as SWG;
use App\Factory\ResponseFactory;
use App\Controller\Principal\Base\GenericController;
use FOS\RestBundle\Request\ParamFetcher;
use App\Facade\Principal\MovimentoEstoqueFacade;
use App\Helper\ConstanteParametros;
use Symfony\Component\HttpFoundation\Request;

/**
 *
 * @author        Luiz A Costa
 * @Route("/api")
 */
class MovimentoEstoqueController extends GenericController
{

    /**
     *
     * @var \App\Facade\Principal\MovimentoEstoqueFacade
     */
    private $movimentoEstoqueFacade;

    /**
     * {@inheritdoc}
     */
    protected function constroiFacades()
    {
        // Para criar o LogFacade do GenericController
        parent::constroiFacades();
        $this->movimentoEstoqueFacade = new MovimentoEstoqueFacade(self::getManagerRegistry());
    }

    /**
     *
     * @SWG\Post(
     *     path="/api/movimento_estoque/criar",
     *     summary="Cria uma movimento_estoque",
     *     description="Cria uma movimento_estoque no banco",
     *     consumes={"application/x-www-form-urlencoded"},
     *     produces={"application/json"},
     * @SWG\Response(
     *         response="201",
     *         description="Retorna criado com sucesso"
     *     ),
     * @SWG\Response(
     *         response="202",
     *         description="Ocorreu algum erro no servidor",
     *     )
     * )
     *
     * @FOSRest\RequestParam(name="item",                   strict=true, nullable=false, description="Item ID", requirements="\d+")
     * @FOSRest\RequestParam(name="tipo_movimento_estoque", strict=false, nullable=false, description="Tipo Movimento Estoque ID", requirements="\d+")
     * @FOSRest\RequestParam(name="saldo_estoque",          strict=true, nullable=false, description="Saldo Atual", requirements="^\d{0,9}+\.?\d{0,6}?$")
     * @FOSRest\RequestParam(name="observacao",             strict=true, nullable=false, allowBlank=true, description="Observacao")
     * @FOSRest\RequestParam(name="tipo",                   strict=true, nullable=false, allowBlank=false, description="Tipo de nota", requirements="(AE|AS)", default="AE")
     *
     * @FOSRest\Post("/movimento_estoque/criar")
     *
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function criar(ParamFetcher $paramFetcher, Request $request)
    {
        $parametros = $paramFetcher->all();
        $usuarioId  = $request->headers->get('Authorization-User-ID');
        $mensagem   = "";
        $objetoORM  = $this->movimentoEstoqueFacade->criarMovimentoPelaTelaItem($mensagem, $parametros[ConstanteParametros::CHAVE_ITEM], $usuarioId, $parametros[ConstanteParametros::CHAVE_TIPO_MOVIMENTO_ESTOQUE], $parametros[ConstanteParametros::CHAVE_TIPO], $parametros[ConstanteParametros::CHAVE_SALDO_ESTOQUE], $parametros[ConstanteParametros::CHAVE_OBSERVACAO]);
        if ((is_null($objetoORM) === true) || (empty($mensagem) === false)) {
            return ResponseFactory::conflict(["parametros" => $parametros], $mensagem);
        }

        return ResponseFactory::created(["objetoORM" => $objetoORM->getId()], "Registro criado com sucesso!");
    }


}
