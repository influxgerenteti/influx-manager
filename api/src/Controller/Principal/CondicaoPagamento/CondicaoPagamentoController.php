<?php

namespace App\Controller\Principal\CondicaoPagamento;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use FOS\RestBundle\Controller\Annotations as FOSRest;
use Swagger\Annotations as SWG;
use App\Factory\ResponseFactory;
use App\Controller\Principal\Base\GenericController;
use FOS\RestBundle\Request\ParamFetcher;
use App\Facade\Principal\CondicaoPagamentoFacade;
use App\Facade\Principal\CondicaoPagamentoParcelaFacade;

/**
 *
 * @author        Luiz A Costa
 * @Route("/api")
 */
class CondicaoPagamentoController extends GenericController
{
    /**
     *
     * @var \App\Facade\Principal\CondicaoPagamentoFacade
     */
    private $condicaoPagamentoFacade;

    /**
     *
     * @var \App\Facade\Principal\CondicaoPagamentoParcelaFacade
     */
    private $condicaoPagamentoParcelaFacade;

    /**
     * {@inheritdoc}
     */
    protected function constroiFacades()
    {
        // Para criar o LogFacade do GenericController
        parent::constroiFacades();
        $this->condicaoPagamentoFacade        = new CondicaoPagamentoFacade(self::getManagerRegistry());
        $this->condicaoPagamentoParcelaFacade = new CondicaoPagamentoParcelaFacade(self::getManagerRegistry());
    }

    /**
     *
     * @SWG\Get(
     *     path="/api/condicao_pagamento/listar",
     *     summary="Listar Condicao de Pagamento",
     *     description="Lista as Condicao de Pagamento do banco",
     *     produces={"application/json"},
     * @SWG\Response(
     *         response="200",
     *         description="Retorna os Condicao de Pagamento"
     *     ),
     * @SWG\Response(
     *         response="400",
     *         description="Ocorreu algum erro no servidor",
     *     )
     * )
     *
     * @FOSRest\QueryParam(name="pagina", strict=false, allowBlank=false, default="1", description="Pagina para realizar o scroll", requirements="\d+")
     *
     * @FOSRest\Get("/condicao_pagamento/listar")
     *
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function lista(ParamFetcher $request)
    {
        $parametros = $request->all();
        $mensagem   = "";
        $resultados = $this->condicaoPagamentoFacade->listar($parametros);
        if ($resultados === false) {
            return ResponseFactory::badRequest([], $mensagem);
        }

        return ResponseFactory::ok($resultados);
    }

    /**
     *
     * @SWG\Get(
     *     path="/api/condicao_pagamento/{id}",
     *     summary="Buscar a Condicao de Pagamento",
     *     description="Busca a Condicao de Pagamento através da ID",
     *     produces={"application/json"},
     * @SWG\Response(
     *         response="200",
     *         description="Retorna a Condicao de Pagamento"
     *     ),
     * @SWG\Response(
     *         response="404",
     *         description="Ocorreu algum erro no servidor",
     *     )
     * )
     *
     * @FOSRest\Get("/condicao_pagamento/{id}")
     *
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function buscar($id)
    {
        $mensagemErro = "";
        $objetoORM    = $this->condicaoPagamentoFacade->buscarPorId($mensagemErro, $id);
        if (is_null($objetoORM) === true) {
            return ResponseFactory::notFound([], $mensagemErro);
        }

        return ResponseFactory::ok($objetoORM);
    }

    /**
     *
     * @SWG\Post(
     *     path="/api/condicao_pagamento/criar",
     *     summary="Cria uma CondicaoPagamento e CondicaoPagamentoParcela",
     *     description="Cria uma CondicaoPagamento e CondicaoPagamentoParcela no banco",
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
     * @FOSRest\RequestParam(name="descricao",           strict=true, nullable=false, allowBlank=false, description="Parcelas")
     * @FOSRest\RequestParam(name="quantidade_parcelas", strict=true, nullable=false, allowBlank=false, description="Quantidade de Parcelas")
     * @FOSRest\RequestParam(map=true,                   name="parcelas",                         strict=true, nullable=false, allowBlank=false, description="Array de Parcelas")
     *
     * @FOSRest\Post("/condicao_pagamento/criar")
     *
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function criar(ParamFetcher $request)
    {
        $parametros = $request->all();
        $mensagem   = "";
        $objetoORM  = $this->condicaoPagamentoFacade->criar($mensagem, $parametros);
        if ((is_null($objetoORM) === true) || (empty($mensagem) === false)) {
            return ResponseFactory::conflict(["parametros" => $parametros], $mensagem);
        }

        $this->condicaoPagamentoParcelaFacade->criar($mensagem, $objetoORM, $parametros);
        if (empty($mensagem) === false) {
            return ResponseFactory::conflict(["parametros" => $parametros], $mensagem);
        }

        return ResponseFactory::created(["objetoORM" => $objetoORM], "Registro criado com sucesso!");
    }


}
