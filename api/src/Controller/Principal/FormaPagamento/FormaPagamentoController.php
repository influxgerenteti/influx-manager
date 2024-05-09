<?php

namespace App\Controller\Principal\FormaPagamento;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use FOS\RestBundle\Controller\Annotations as FOSRest;
use Swagger\Annotations as SWG;
use App\Factory\ResponseFactory;
use App\Controller\Principal\Base\GenericController;
use FOS\RestBundle\Request\ParamFetcher;
use App\Facade\Principal\FormaPagamentoFacade;

/**
 *
 * @author        Luiz A Costa
 * @Route("/api")
 */
class FormaPagamentoController extends GenericController
{

    /**
     *
     * @var \App\Facade\Principal\FormaPagamentoFacade
     */
    private $formaPagamentoFacade;
    /**
     * {@inheritdoc}
     */
    protected function constroiFacades()
    {
        // Para criar o LogFacade do GenericController
        parent::constroiFacades();
        $this->formaPagamentoFacade = new FormaPagamentoFacade(self::getManagerRegistry());
    }

    /**
     *
     * @SWG\Get(
     *     path="/api/forma_pagamento/listar",
     *     summary="Listar forma_pagamento",
     *     description="Lista as forma_pagamento do banco",
     *     tags={"Forma de Pagamento"},
     *     produces={"application/json"},
     * @SWG\Response(
     *         response="200",
     *         description="Retorna os forma_pagamento"
     *     ),
     * @SWG\Response(
     *         response="400",
     *         description="Ocorreu algum erro no servidor",
     *     )
     * )
     *
     * @FOSRest\QueryParam(name="pagina",    strict=false, allowBlank=false, default="1", description="Pagina para realizar o scroll", requirements="\d+")
     * @FOSRest\QueryParam(name="descricao", strict=false, description="Descricao da forma")
     * @FOSRest\QueryParam(name="order",     strict=false, nullable=true,  description="Coluna de ordenação")
     * @FOSRest\QueryParam(name="direcao",   strict=false, nullable=true,  description="ASC|DESC")
     *
     * @FOSRest\Get("/forma_pagamento/listar")
     *
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function lista(ParamFetcher $request)
    {
        $parametros = $request->all();
        $resultados = $this->formaPagamentoFacade->listar($parametros);

        return ResponseFactory::ok($resultados);
    }

    /**
     *
     * @SWG\Get(
     *     path="/api/forma_pagamento/{id}",
     *     summary="Buscar a forma_pagamento",
     *     description="Busca a forma_pagamento através da ID",
     *     tags={"Forma de Pagamento"},
     *     produces={"application/json"},
     * @SWG\Response(
     *         response="200",
     *         description="Retorna a forma_pagamento"
     *     ),
     * @SWG\Response(
     *         response="404",
     *         description="Ocorreu algum erro no servidor",
     *     )
     * )
     *
     * @FOSRest\Get("/forma_pagamento/{id}")
     *
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function buscar($id)
    {
        $mensagemErro = "";
        $objetoORM    = $this->formaPagamentoFacade->buscarPorId($mensagemErro, $id);
        if (is_null($objetoORM) === true) {
            return ResponseFactory::notFound([], $mensagemErro);
        }

        return ResponseFactory::ok($objetoORM);
    }

    /**
     *
     * @SWG\Get(
     *     path="/api/forma_pagamento/buscar_descricao/{query}",
     *     summary="Buscar as formas pagamento por descrição",
     *     description="Busca as formas pagamento através da descrição",
     *     tags={"Forma de Pagamento"},
     *     produces={"application/json"},
     * @SWG\Response(
     *         response="200",
     *         description="Retorna a forma_pagamento"
     *     ),
     * @SWG\Response(
     *         response="404",
     *         description="Ocorreu algum erro no servidor",
     *     )
     * )
     *
     * @FOSRest\Get("/forma_pagamento/buscar_descricao/{query}")
     *
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function buscarPorDescricao ($query)
    {
        $formasPagamento = $this->formaPagamentoFacade->buscarPorDescricao($query);

        return ResponseFactory::ok($formasPagamento);
    }

    /**
     *
     * @SWG\Post(
     *     path="/api/forma_pagamento/criar",
     *     summary="Cria uma forma_pagamento",
     *     description="Cria uma forma_pagamento no banco",
     *     tags={"Forma de Pagamento"},
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
     * @FOSRest\RequestParam(name="descricao",           strict=true, nullable=false, allowBlank=false, description="Descricao da forma de pagamento")
     * @FOSRest\RequestParam(name="descricao_abreviada", strict=true, nullable=false, allowBlank=false, description="Descricao da forma de pagamento abreviada")
     * @FOSRest\RequestParam(name="forma_boleto",        strict=false, nullable=true, allowBlank=true, default="0", description="Forma de Boleto", requirements="[1|0]")
     * @FOSRest\RequestParam(name="forma_cheque",        strict=false, nullable=true, allowBlank=true, default="0", description="Forma de Cheque", requirements="[1|0]")
     * @FOSRest\RequestParam(name="forma_cartao",        strict=false, nullable=true, allowBlank=true, default="0", description="Forma de Cartao de crédito", requirements="[1|0]")
     * @FOSRest\RequestParam(name="forma_cartao_debito", strict=false, nullable=true, allowBlank=true, default="0", description="Forma de Cartao de débito", requirements="[1|0]")
     * @FOSRest\RequestParam(name="forma_transferencia", strict=false, nullable=true, allowBlank=true, default="0", description="Forma de transferência", requirements="[1|0]")
     * @FOSRest\RequestParam(name="liquidacao_imediata", strict=true, nullable=false, allowBlank=false, default="0", description="Liquidação imediata", requirements="[1|0]")
     *
     * @FOSRest\Post("/forma_pagamento/criar")
     *
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function criar(ParamFetcher $request)
    {
        $parametros = $request->all();
        $mensagem   = "";
        $objetoORM  = $this->formaPagamentoFacade->criar($mensagem, $parametros);
        if ((is_null($objetoORM) === true) || (empty($mensagem) === false)) {
            return ResponseFactory::conflict(["parametros" => $parametros], $mensagem);
        }

        return ResponseFactory::created(["objetoORM" => $objetoORM], "Registro criado com sucesso!");
    }

    /**
     *
     * @SWG\Patch(
     *     path="/api/forma_pagamento/atualizar/{id}",
     *     summary="Atualiza um forma_pagamento",
     *     description="Atualiza um forma_pagamento no banco",
     *     tags={"Forma de Pagamento"},
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
     * @FOSRest\RequestParam(name="descricao_abreviada", strict=false, nullable=true, description="Descricao da forma de pagamento abreviada")
     * @FOSRest\RequestParam(name="situacao",            strict=false, nullable=true, description="Situacao da forma de pagamento", requirements="[A|I]")
     * @FOSRest\RequestParam(name="forma_boleto",        strict=false, nullable=true, allowBlank=true, description="Forma de Boleto", requirements="[1|0]")
     * @FOSRest\RequestParam(name="forma_cheque",        strict=false, nullable=true, allowBlank=true, description="Forma de Cheque", requirements="[1|0]")
     * @FOSRest\RequestParam(name="forma_cartao",        strict=false, nullable=true, allowBlank=true, description="Forma de Cartao de crédito", requirements="[1|0]")
     * @FOSRest\RequestParam(name="forma_cartao_debito", strict=false, nullable=true, allowBlank=true, description="Forma de Cartao de débito", requirements="[1|0]")
     * @FOSRest\RequestParam(name="forma_transferencia", strict=false, nullable=true, allowBlank=true, description="Forma de transferência", requirements="[1|0]")
     * @FOSRest\RequestParam(name="liquidacao_imediata", strict=false, nullable=true, allowBlank=true, description="Liquidação imediata", requirements="[1|0]")
     *
     * @FOSRest\Patch("/forma_pagamento/atualizar/{id}")
     *
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function atualizar($id, ParamFetcher $request)
    {
        $parametros = $request->all();
        $mensagem   = "";
        $retorno    = $this->formaPagamentoFacade->atualizar($mensagem, $id, $parametros);
        if ($retorno === false) {
            return ResponseFactory::badRequest(["parametros" => $parametros], $mensagem);
        }

        return ResponseFactory::noContent([]);
    }


}
