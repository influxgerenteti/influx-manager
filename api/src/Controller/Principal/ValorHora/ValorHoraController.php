<?php

namespace App\Controller\Principal\ValorHora;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use FOS\RestBundle\Controller\Annotations as FOSRest;
use Swagger\Annotations as SWG;
use App\Factory\ResponseFactory;
use App\Controller\Principal\Base\GenericController;
use FOS\RestBundle\Request\ParamFetcher;
use App\Facade\Principal\ValorHoraFacade;

/**
 *
 * @author        Luiz A Costa
 * @Route("/api")
 */
class ValorHoraController extends GenericController
{

    /**
     *
     * @var \App\Facade\Principal\ValorHoraFacade
     */
    private $valorHoraFacade;

    /**
     * {@inheritdoc}
     */
    protected function constroiFacades()
    {
        // Para criar o LogFacade do GenericController
        parent::constroiFacades();
        $this->valorHoraFacade = new ValorHoraFacade(self::getManagerRegistry());
    }

    /**
     *
     * @SWG\Get(
     *     path="/api/valor_hora/listar",
     *     summary="Listar valor_hora",
     *     description="Lista as valor_hora do banco",
     *     tags={"Valor hora"},
     *     produces={"application/json"},
     * @SWG\Response(
     *         response="200",
     *         description="Retorna os valor_hora"
     *     ),
     * @SWG\Response(
     *         response="400",
     *         description="Ocorreu algum erro no servidor",
     *     )
     * )
     *
     * @FOSRest\QueryParam(name="pagina",          strict=false, allowBlank=false, default="1", description="Pagina para realizar o scroll", requirements="\d+")
     * @FOSRest\QueryParam(name="nivel_instrutor", strict=false, allowBlank=true, description="Filtro de nível de instrutor", requirements="\d+")
     * @FOSRest\QueryParam(name="tipo_pagamento",  strict=false, allowBlank=true, description="Filtro de tipo de pagamento", requirements="[H|M]")
     * @FOSRest\QueryParam(name="situacao",        strict=false, allowBlank=true, description="Situacao de Valor pagamento", requirements="[A|I]")
     *
     * @FOSRest\Get("/valor_hora/listar")
     *
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function lista(ParamFetcher $request)
    {
        $parametros = $request->all();
        $mensagem   = "";
        $resultados = $this->valorHoraFacade->listar($parametros);
        if ($resultados === false) {
            return ResponseFactory::badRequest([], $mensagem);
        }

        return ResponseFactory::ok($resultados);
    }

    /**
     *
     * @SWG\Get(
     *     path="/api/valor_hora/{id}",
     *     summary="Buscar a valor_hora",
     *     description="Busca a valor_hora através da ID",
     *     tags={"Valor hora"},
     *     produces={"application/json"},
     * @SWG\Response(
     *         response="200",
     *         description="Retorna a valor_hora"
     *     ),
     * @SWG\Response(
     *         response="404",
     *         description="Ocorreu algum erro no servidor",
     *     )
     * )
     *
     * @FOSRest\Get("/valor_hora/{id}")
     *
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function buscar($id)
    {
        $mensagem  = "";
        $objetoORM = $this->valorHoraFacade->buscarPorId($mensagem, $id);
        if (is_null($objetoORM) === true) {
            return ResponseFactory::notFound([], $mensagem);
        }

        return ResponseFactory::ok($objetoORM);
    }

    /**
     *
     * @SWG\Post(
     *     path="/api/valor_hora/criar",
     *     summary="Cria uma valor_hora",
     *     description="Cria uma valor_hora no banco",
     *     tags={"Valor hora"},
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
     * @FOSRest\RequestParam(name="franqueada",        strict=true, nullable=false, allowBlank=false, description="Franqueada", requirements="\d+")
     * @FOSRest\RequestParam(name="valor_hora_linhas", strict=true, nullable=false, allowBlank=false, description="Valor hora linhas", requirements="\d+")
     * @FOSRest\RequestParam(name="nivel_instrutor",   strict=true, nullable=false, allowBlank=false, description="Nivel Instrutor", requirements="\d+")
     * @FOSRest\RequestParam(name="valor",             strict=true, nullable=false, allowBlank=false, description="Valor", requirements="^\d{0,7}+\.?\d{0,2}?$")
     * @FOSRest\RequestParam(name="valor_extra",       strict=true, nullable=false, allowBlank=false, description="Valor Extra", default="0", requirements="^\d{0,7}+\.?\d{0,2}?$")
     *
     * @FOSRest\Post("/valor_hora/criar")
     *
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function criar(ParamFetcher $request)
    {
        $parametros = $request->all();
        $mensagem   = "";
        $objetoORM  = $this->valorHoraFacade->criar($mensagem, $parametros);
        if ((is_null($objetoORM) === true) || (empty($mensagem) === false)) {
            return ResponseFactory::conflict(["parametros" => $parametros], $mensagem);
        }

        return ResponseFactory::created(["objetoORM" => $objetoORM->getId()], "Registro criado com sucesso!");
    }

    /**
     *
     * @SWG\Patch(
     *     path="/api/valor_hora/atualizar/{id}",
     *     summary="Atualiza um valor_hora",
     *     description="Atualiza um valor_hora no banco",
     *     tags={"Valor hora"},
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
     * @FOSRest\RequestParam(name="franqueada",        strict=false, nullable=false, allowBlank=false, description="Franqueada", requirements="\d+")
     * @FOSRest\RequestParam(name="valor_hora_linhas", strict=false, nullable=false, allowBlank=false, description="Valor hora linhas", requirements="\d+")
     * @FOSRest\RequestParam(name="nivel_instrutor",   strict=false, nullable=false, allowBlank=false, description="Nivel Instrutor", requirements="\d+")
     * @FOSRest\RequestParam(name="valor",             strict=false, nullable=false, allowBlank=false, description="Valor", requirements="^\d{0,7}+\.?\d{0,2}?$")
     * @FOSRest\RequestParam(name="valor_extra",       strict=true, nullable=false, allowBlank=false, description="Valor Extra", default="0", requirements="^\d{0,7}+\.?\d{0,2}?$")
     * @FOSRest\RequestParam(name="situacao",          strict=false, nullable=false, allowBlank=false, description="Situacao", requirements="[A|I]")
     *
     * @FOSRest\Patch("/valor_hora/atualizar/{id}")
     *
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function atualizar($id, ParamFetcher $request)
    {
        $parametros = $request->all();
        $mensagem   = "";
        $retorno    = $this->valorHoraFacade->atualizar($mensagem, $id, $parametros);
        if ($retorno === false) {
            return ResponseFactory::badRequest(["parametros" => $parametros], $mensagem);
        }

        return ResponseFactory::noContent([]);
    }

    /**
     *
     * @SWG\Delete(
     *     path="/api/valor_hora/remover/{id}",
     *     summary="Remove uma valor_hora",
     *     description="Remove uma valor_hora no banco",
     *     tags={"Valor hora"},
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
     * @FOSRest\Delete("/valor_hora/remover/{id}")
     *
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function excluir($id)
    {
        $mensagem = "";
        $retorno  = $this->valorHoraFacade->remover($mensagem, $id);
        if ($retorno === false) {
            return ResponseFactory::badRequest([], $mensagem);
        }

        return ResponseFactory::ok([], "Excluido com sucesso");
    }


}
