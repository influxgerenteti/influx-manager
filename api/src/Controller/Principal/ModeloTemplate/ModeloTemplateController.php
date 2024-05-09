<?php

namespace App\Controller\Principal\ModeloTemplate;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use FOS\RestBundle\Controller\Annotations as FOSRest;
use Swagger\Annotations as SWG;
use App\Factory\ResponseFactory;
use App\Controller\Principal\Base\GenericController;
use FOS\RestBundle\Request\ParamFetcher;
use App\Facade\Principal\ModeloTemplateFacade;
use App\Helper\ConstanteParametros;

/**
 *
 * @author        Luiz A Costa
 * @Route("/api")
 */
class ModeloTemplateController extends GenericController
{

    /**
     *
     * @var \App\Facade\Principal\ModeloTemplateFacade
     */
    private $modeloTemplateFacade;

    /**
     * {@inheritdoc}
     */
    protected function constroiFacades()
    {
        // Para criar o LogFacade do GenericController
        parent::constroiFacades();
        $this->modeloTemplateFacade = new ModeloTemplateFacade(self::getManagerRegistry());
    }

    /**
     *
     * @SWG\Get(
     *     path="/api/modelo_template/listar",
     *     summary="Listar modelo_template",
     *     description="Lista as modelo_template do banco",
     *     produces={"application/json"},
     * @SWG\Response(
     *         response="200",
     *         description="Retorna os modelo_template"
     *     ),
     * @SWG\Response(
     *         response="400",
     *         description="Ocorreu algum erro no servidor",
     *     )
     * )
     *
     * @FOSRest\QueryParam(name="pagina",     strict=false, allowBlank=false, default="1", description="Pagina para realizar o scroll", requirements="\d+")
     * @FOSRest\QueryParam(name="order",      strict=false, nullable=true,  description="Coluna de ordenação")
     * @FOSRest\QueryParam(name="direcao",    strict=false, nullable=true,  description="ASC|DESC")
     * @FOSRest\QueryParam(name="situacao",   strict=false, nullable=true, description="Situacao do template do contrato")
     * @FOSRest\QueryParam(name="franqueada", strict=true, nullable=false, allowBlank=false, description="ID da Franqeada", requirements="\d+")
     *
     * @FOSRest\Get("/modelo_template/listar")
     *
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function lista(ParamFetcher $request)
    {
        $parametros = $request->all();
        $mensagem   = "";
        $resultados = $this->modeloTemplateFacade->listar($parametros);
        if ($resultados === false) {
            return ResponseFactory::badRequest([], $mensagem);
        }

        return ResponseFactory::ok($resultados);
    }

    /**
     *
     * @SWG\Get(
     *     path="/api/modelo_template/{id}",
     *     summary="Buscar a modelo_template",
     *     description="Busca a modelo_template através da ID",
     *     produces={"application/json"},
     * @SWG\Response(
     *         response="200",
     *         description="Retorna a modelo_template"
     *     ),
     * @SWG\Response(
     *         response="404",
     *         description="Ocorreu algum erro no servidor",
     *     )
     * )
     *
     * @FOSRest\Get("/modelo_template/{id}")
     *
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function buscar($id)
    {
        $mensagemErro = "";
        $objetoORM    = $this->modeloTemplateFacade->buscarPorId($mensagemErro, $id);
        if (is_null($objetoORM) === true) {
            return ResponseFactory::notFound([], $mensagemErro);
        }

        return ResponseFactory::ok($objetoORM);
    }

    /**
     *
     * @SWG\Post(
     *     path="/api/modelo_template/criar",
     *     summary="Cria uma modelo_template",
     *     description="Cria uma modelo_template no banco",
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
     * @FOSRest\RequestParam(name="franqueada",    strict=true, nullable=false, allowBlank=false, description="ID da Franqueada", requirements="\d+")
     * @FOSRest\RequestParam(name="descricao",     strict=true, nullable=false, allowBlank=false, description="Descricao do Modelo", requirements=".{0,255}")
     * @FOSRest\RequestParam(name="tipo_template", strict=true, nullable=false, allowBlank=false, description="Tipo do Modelo", requirements=".{0,255}")
     * @FOSRest\RequestParam(name="modelo_html",   strict=true, nullable=false, allowBlank=false, description="Modelo em HTML")
     *
     * @FOSRest\Post("/modelo_template/criar")
     *
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function criar(ParamFetcher $request)
    {
        $parametros = $request->all();
        $mensagem   = "";
        $objetoORM  = $this->modeloTemplateFacade->criar($mensagem, $parametros);
        if ((is_null($objetoORM) === true) || (empty($mensagem) === false)) {
            return ResponseFactory::conflict(["parametros" => $parametros], $mensagem);
        }

        return ResponseFactory::created(["objetoORM" => $objetoORM->getId()], "Registro criado com sucesso!");
    }

    /**
     *
     * @SWG\Patch(
     *     path="/api/modelo_template/atualizar/{id}",
     *     summary="Atualiza um modelo_template",
     *     description="Atualiza um modelo_template no banco",
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
     * @FOSRest\RequestParam(name="franqueada",    strict=true, nullable=false, allowBlank=false, description="ID da Franqueada", requirements="\d+")
     * @FOSRest\RequestParam(name="descricao",     strict=true, nullable=false, allowBlank=true, description="Descricao do Modelo", requirements=".{0,255}")
     * @FOSRest\RequestParam(name="tipo_template", strict=true, nullable=false, allowBlank=false, description="Tipo do Modelo", requirements=".{0,255}")
     * @FOSRest\RequestParam(name="modelo_html",   strict=true, nullable=false, allowBlank=true, description="Modelo em HTML")
     * @FOSRest\RequestParam(name="situacao",      strict=false, nullable=false, allowBlank=false, description="Situacao do banco", requirements="(A|I)", default="A")
     *
     * @FOSRest\Patch("/modelo_template/atualizar/{id}")
     *
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function atualizar($id, ParamFetcher $request)
    {
        $parametros = $request->all();
        $mensagem   = "";
        $retorno    = $this->modeloTemplateFacade->atualizar($mensagem, $id, $parametros);
        if ($retorno === false) {
            return ResponseFactory::badRequest(["parametros" => $parametros], $mensagem);
        }

        return ResponseFactory::noContent([]);
    }

    /**
     *
     * @SWG\Delete(
     *     path="/api/modelo_template/remover/{id}",
     *     summary="Remove uma modelo_template",
     *     description="Remove uma modelo_template no banco",
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
     * @FOSRest\Delete("/modelo_template/remover/{id}")
     *
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function excluir($id)
    {
        $mensagem = "";
        $retorno  = $this->modeloTemplateFacade->remover($mensagem, $id);
        if ($retorno === false) {
            return ResponseFactory::badRequest([], $mensagem);
        }

        return ResponseFactory::ok([], "Excluido com sucesso");
    }


    /**
     *
     * @SWG\Patch(
     *     path="/api/modelo_template/alterar_situacao/{id}",
     *     summary="Altera o template para inativo ou ativo",
     *     description="Atualiza um modelo_template no banco",
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
     * @FOSRest\RequestParam(name="franqueada",                 strict=true, nullable=false, allowBlank=false, description="ID da Franqueada", requirements="\d+")
     * @FOSRest\RequestParam(name="modelo_template_franqueada", strict=true, nullable=true, allowBlank=true, description="ID do modelo template franqueada caso exista", requirements="\d+")
     * @FOSRest\RequestParam(name="situacao",                   strict=false, nullable=false, allowBlank=false, description="Situacao do banco", requirements="(A|I)", default="A")
     *
     * @FOSRest\Patch("/modelo_template/alterar_situacao/{id}")
     *
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function alterarSituacao($id, ParamFetcher $request)
    {
        $parametros = $request->all();
        $mensagem   = "";
        $parametros[ConstanteParametros::CHAVE_MODELO_TEMPLATE] = $id;
        $retorno = $this->modeloTemplateFacade->alterarSituacao($mensagem, $parametros);
        if ($retorno === false) {
            return ResponseFactory::badRequest(["parametros" => $parametros], $mensagem);
        }

        return ResponseFactory::ok($retorno, "Situação alterada com sucesso");
    }


}
