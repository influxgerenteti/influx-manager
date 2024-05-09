<?php

namespace App\Controller\Principal\TituloPagar;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use FOS\RestBundle\Controller\Annotations as FOSRest;
use Swagger\Annotations as SWG;
use App\Factory\ResponseFactory;
use App\Controller\Principal\Base\GenericController;
use FOS\RestBundle\Request\ParamFetcher;
use App\Facade\Principal\TituloPagarFacade;

/**
 *
 * @author        Rodrigo de Souza Fernandes (GATI labs)
 * @Route("/api")
 */
class TituloPagarController extends GenericController
{

    /**
     *
     * @var \App\Facade\Principal\TituloPagarFacade
     */
    private $tituloPagarFacade;

    /**
     * {@inheritdoc}
     */
    protected function constroiFacades()
    {
        // Para criar o LogFacade do GenericController
        parent::constroiFacades();
        $this->tituloPagarFacade = new TituloPagarFacade(self::getManagerRegistry());
    }

    /**
     *
     * @SWG\Post(
     *     path="/api/titulo_pagar/calcular",
     *     summary="Calcular Títulos à Pagar",
     *     description="Calcular os títulos à pagar",
     *     tags={"Titulo Pagar"},
     *     consumes={"application/x-www-form-urlencoded"},
     *     produces={"application/json"},
     * @SWG\Response(
     *         response="200",
     *         description="Retorna os titulos à pagar calculados"
     *     ),
     * @SWG\Response(
     *         response="400",
     *         description="Ocorreu algum erro no servidor",
     *     )
     * )
     *
     * @FOSRest\RequestParam(name="condicao_pagamento", strict=true, allowBlank=false, description="Condição de Pagamento ID", requirements="\d+")
     * @FOSRest\RequestParam(name="valor_titulo",       strict=true, allowBlank=false, description="Valor do titulo", requirements="^\d{0,9}+\.?\d{0,2}?$")
     * @FOSRest\RequestParam(name="data_emissao",       strict=true, nullable=false, description="Data de Emissao")
     *
     * @FOSRest\Post("/titulo_pagar/calcular")
     *
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function calcular(ParamFetcher $request)
    {
        $parametros = $request->all();
        $mensagem   = "";
        $resultados = $this->tituloPagarFacade->calcular($mensagem, $parametros);
        if ($resultados === false) {
            return ResponseFactory::badRequest([], $mensagem);
        }

        return ResponseFactory::ok(
            [
                "total"    => count($resultados),
                "parcelas" => $resultados,
            ]
        );
    }

    /**
     *
     * @SWG\Get(
     *     path="/api/titulo_pagar/listar",
     *     summary="Listar titulo_pagar",
     *     description="Lista as titulo_pagar do banco",
     *     tags={"Titulo Pagar"},
     *     produces={"application/json"},
     * @SWG\Response(
     *         response="200",
     *         description="Retorna os titulo_pagar"
     *     ),
     * @SWG\Response(
     *         response="400",
     *         description="Ocorreu algum erro no servidor",
     *     )
     * )
     *
     * @FOSRest\QueryParam(name="franqueada",        strict=true, nullable=false, description="ID da franqueada", requirements="\d+")
     * @FOSRest\QueryParam(name="favorecido_pessoa", strict=false, nullable=true, description="ID de Favorecido Pessoa", requirements="\d+")
     * @FOSRest\QueryParam(name="pagina",            strict=false, nullable=true, default="1", description="Página para realizar o scroll", requirements="\d{0,2}")
     * @FOSRest\QueryParam(name="itensPorPagina",    strict=false, nullable=true, default="50", description="Quantidade de itens a ser exibidos por página", requirements="\d{0,2}")
     * @FOSRest\QueryParam(name="data_inicial",      strict=false, nullable=true, description="Data Inicial da Pesquisa")
     * @FOSRest\QueryParam(name="data_final",        strict=false, nullable=true, description="Data Final da Pesquisa")
     * @FOSRest\QueryParam(name="valor_inicial",     strict=false, nullable=true, description="Valor Inicial", requirements="^\d{0,7}+\.?\d{0,2}?$")
     * @FOSRest\QueryParam(name="valor_final",       strict=false, nullable=true, description="Valor Final", requirements="^\d{0,7}+\.?\d{0,2}?$")
     * @FOSRest\QueryParam(name="situacao",          strict=false, nullable=true, description="Situacao de Ativo ou inativo", requirements="(PEN|LIQ|BAI|SUB)")
     *
     * @FOSRest\Get("/titulo_pagar/listar")
     *
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function lista(ParamFetcher $request)
    {
        $parametros = $request->all();
        $mensagem   = "";
        $resultados = $this->tituloPagarFacade->listar($mensagem, $parametros);
        if (($resultados === false) || (empty($mensagem) === false)) {
            return ResponseFactory::badRequest(["parametros" => $parametros], $mensagem);
        }

        return ResponseFactory::ok($resultados);
    }

    /**
     * @SWG\Delete(
     *     path="/api/titulo_pagar/remover/{id}",
     *     summary="Remove um título a pagar",
     *     description="Remove um título a pagar",
     *     tags={"Titulo Pagar"},
     *     produces={"application/json"},
     * @SWG\Response(
     *         response="200",
     *         description="Retorna removido com sucesso"
     *     ),
     * @SWG\Response(
     *         response="400",
     *         description="Ocorreu algum erro no servidor",
     *     )
     * )
     *
     * @FOSRest\Delete("/titulo_pagar/remover/{id}")
     *
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function excluir ($id)
    {
        $mensagem = "";

        $tituloPagar = $this->tituloPagarFacade->removerPorID($mensagem, $id);
        if ($tituloPagar === false) {
            return ResponseFactory::badRequest([], $mensagem);
        }

        return ResponseFactory::ok([], "Excluído com sucesso");

    }


}
