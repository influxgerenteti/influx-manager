<?php

namespace App\Controller\Principal\FollowupComercial;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use FOS\RestBundle\Controller\Annotations as FOSRest;
use Swagger\Annotations as SWG;
use App\Factory\ResponseFactory;
use App\Controller\Principal\Base\GenericController;
use FOS\RestBundle\Request\ParamFetcher;
use App\Facade\Principal\FollowupComercialFacade;
use App\Facade\Principal\ConvenioFacade;

/**
 *
 * @author        Luiz A Costa
 * @Route("/api")
 */
class FollowupComercialController extends GenericController
{

    /**
     *
     * @var \App\Facade\Principal\FollowupComercialFacade
     */
    private $followupComercialFacade;

    /**
     *
     * @var \App\Facade\Principal\ConvenioFacade
     */
    private $convenioFacade;

    /**
     * {@inheritdoc}
     */
    protected function constroiFacades()
    {
        // Para criar o LogFacade do GenericController
        parent::constroiFacades();
        $this->followupComercialFacade = new FollowupComercialFacade(self::getManagerRegistry());
        $this->convenioFacade          = new ConvenioFacade(self::getManagerRegistry());
    }

    /**
     *
     * @SWG\Get(
     *     path="/api/followup_comercial/listar",
     *     summary="Listar followup_comercial",
     *     description="Lista as followup_comercial do banco",
     *     produces={"application/json"},
     * @SWG\Response(
     *         response="200",
     *         description="Retorna os followup_comercial"
     *     ),
     * @SWG\Response(
     *         response="400",
     *         description="Ocorreu algum erro no servidor",
     *     )
     * )
     *
     * @FOSRest\QueryParam(name="pagina",                            strict=false, allowBlank=false,  default="1", description="Pagina para realizar o scroll", requirements="\d+")
     * @FOSRest\QueryParam(name="data_cadastro_de",                  strict=false, allowBlank=false,  description="Data de cadastro de")
     * @FOSRest\QueryParam(name="data_cadastro_ate",                 strict=false, allowBlank=false,  description="Data de cadastro ate")
     * @FOSRest\QueryParam(name="data_proximo_contato_de",           strict=false, allowBlank=false,  description="Data de cadastro de")
     * @FOSRest\QueryParam(name="data_proximo_contato_ate",          strict=false, allowBlank=false,  description="Data de cadastro ate")
     * @FOSRest\QueryParam(name="data_termino_contrato_de",          strict=false, allowBlank=false,  description="Data de cadastro de")
     * @FOSRest\QueryParam(name="data_termino_contrato_ate",         strict=false, allowBlank=false,  description="Data de cadastro ate")
     * @FOSRest\QueryParam(name="situacao_interessado",              strict=false, allowBlank=false,  description="Situações do Interessado", map=true)
     * @FOSRest\QueryParam(name="grau_interesse",                    strict=false, allowBlank=false,  description="Graus de Interesse", map=true)
     * @FOSRest\QueryParam(name="situacao_contrato",                 strict=false, allowBlank=false,  description="Situações do Contrato", map=true)
     * @FOSRest\QueryParam(name="situacao_aluno",                    strict=false, allowBlank=false,  description="Situações do Aluno", map=true)
     * @FOSRest\QueryParam(name="interessado",                       strict=false, allowBlank=false,  description="Id do interessado", requirements="\d+")
     * @FOSRest\QueryParam(name="conveniado",                        strict=false, allowBlank=false,  description="Id do interessado", requirements="\d+")
     * @FOSRest\QueryParam(name="tipo_lead",                         strict=false, allowBlank=false,  description="Tipo Lead", requirements="(A|R)")
     * @FOSRest\QueryParam(name="consultor_responsavel_funcionario", strict=false, allowBlank=false, description="Id do funcionario do proximo atendimento Interessado", requirements="\d+")
     * @FOSRest\QueryParam(name="responsavel_venda_funcionario",     strict=false, allowBlank=false, description="Id do funcionario do responsavel pela venda", requirements="\d+")
     * @FOSRest\QueryParam(name="tipo_followup_selecionado",         strict=false, allowBlank=false, description="Id do funcionario", requirements="(0|1|2|3)")
     *
     * @FOSRest\Get("/followup_comercial/listar")
     *
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function lista(ParamFetcher $request)
    {
        $parametros = $request->all();
        $mensagem   = "";
        $resultados = [];
        $resultados = $this->followupComercialFacade->listar($parametros);
        if ($resultados === false) {
            return ResponseFactory::badRequest([], $mensagem);
        }

        $resultadosConvenio = $this->convenioFacade->listarFollowup($parametros);
        if ($resultadosConvenio === false) {
            return ResponseFactory::badRequest([], $mensagem);
        }

        return ResponseFactory::ok(
            [
                "interessado_aluno" => $resultados,
                "convenios"         => $resultadosConvenio,
            ]
        );
    }

    /**
     *
     * @SWG\Get(
     *     path="/api/followup_comercial/{id}",
     *     summary="Buscar a followup_comercial",
     *     description="Busca a followup_comercial através da ID",
     *     produces={"application/json"},
     * @SWG\Response(
     *         response="200",
     *         description="Retorna a followup_comercial"
     *     ),
     * @SWG\Response(
     *         response="404",
     *         description="Ocorreu algum erro no servidor",
     *     )
     * )
     *
     * @FOSRest\Get("/followup_comercial/{id}")
     *
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function buscar($id)
    {
        $mensagem  = "";
        $objetoORM = $this->followupComercialFacade->buscarPorId($mensagem, $id);
        if (is_null($objetoORM) === true) {
            return ResponseFactory::notFound([], $mensagem);
        }

        return ResponseFactory::ok($objetoORM);
    }

    /**
     *
     * @SWG\Post(
     *     path="/api/followup_comercial/criar",
     *     summary="Cria uma followup_comercial",
     *     description="Cria uma followup_comercial no banco",
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
     * @FOSRest\RequestParam(name="exemplo_string",  strict=true, nullable=false, allowBlank=false, description="exemplo_string")
     * @FOSRest\RequestParam(name="exemplo_integer", strict=true, nullable=false, allowBlank=false, description="exemplo_integer", default="0")
     *
     * @FOSRest\Post("/followup_comercial/criar")
     *
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function criar(ParamFetcher $request)
    {
        $parametros = $request->all();
        $mensagem   = "";
        $objetoORM  = $this->followupComercialFacade->criar($mensagem, $parametros);
        if ((is_null($objetoORM) === true) || (empty($mensagem) === false)) {
            return ResponseFactory::conflict(["parametros" => $parametros], $mensagem);
        }

        return ResponseFactory::created(["objetoORM" => $objetoORM], "Registro criado com sucesso!");
    }

    /**
     *
     * @SWG\Patch(
     *     path="/api/followup_comercial/atualizar/{id}",
     *     summary="Atualiza um followup_comercial",
     *     description="Atualiza um followup_comercial no banco",
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
     * @FOSRest\RequestParam(name="exemplo_string",  strict=true, nullable=false, allowBlank=false, description="exemplo_string")
     * @FOSRest\RequestParam(name="exemplo_integer", strict=true, nullable=false, allowBlank=false, description="exemplo_integer", default="0")
     *
     * @FOSRest\Patch("/followup_comercial/atualizar/{id}")
     *
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function atualizar($id, ParamFetcher $request)
    {
        $parametros = $request->all();
        $mensagem   = "";
        $retorno    = $this->followupComercialFacade->atualizar($mensagem, $id, $parametros);
        if ($retorno === false) {
            return ResponseFactory::badRequest(["parametros" => $parametros], $mensagem);
        }

        return ResponseFactory::noContent([]);
    }

    /**
     *
     * @SWG\Delete(
     *     path="/api/followup_comercial/remover/{id}",
     *     summary="Remove uma followup_comercial",
     *     description="Remove uma followup_comercial no banco",
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
     * @FOSRest\Delete("/followup_comercial/remover/{id}")
     *
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function excluir($id)
    {
        $mensagem = "";
        $retorno  = $this->followupComercialFacade->remover($mensagem, $id);
        if ($retorno === false) {
            return ResponseFactory::badRequest([], $mensagem);
        }

        return ResponseFactory::ok([], "Excluido com sucesso");
    }


}
