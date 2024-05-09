<?php

namespace App\Controller\Principal\AcaoSistema;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use FOS\RestBundle\Controller\Annotations as FOSRest;
use Swagger\Annotations as SWG;
use App\Factory\ResponseFactory;
use App\Controller\Principal\Base\GenericController;
use FOS\RestBundle\Request\ParamFetcher;
use App\Helper\ConstanteParametros;
use App\Facade\Principal\AcaoSistemaFacade;

/**
 *
 * @author        Luiz A Costa
 * @Route("/api")
 */
class AcaoSistemaController extends GenericController
{


    /**
     *
     * @var \App\Facade\Principal\AcaoSistemaFacade
     */
    private $acaoSistemaFacade;

    /**
     * {@inheritdoc}
     */
    protected function constroiFacades()
    {
        // Para criar o LogFacade do GenericController
        parent::constroiFacades();
        $this->acaoSistemaFacade = new AcaoSistemaFacade(self::getManagerRegistry());
    }

    /**
     *
     * @SWG\Get(
     *     path="/api/acao_sistema/listar",
     *     summary="Listar acao_sistema",
     *     description="Lista as acao_sistema do banco",
     *     produces={"application/json"},
     * @SWG\Response(
     *         response="200",
     *         description="Retorna os acao_sistema"
     *     ),
     * @SWG\Response(
     *         response="400",
     *         description="Ocorreu algum erro no servidor",
     *     )
     * )
     *
     * @FOSRest\QueryParam(name="pagina", strict=false, allowBlank=false, default="1", description="Pagina para realizar o scroll", requirements="\d+")
     *
     * @FOSRest\Get("/acao_sistema/listar")
     *
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function lista(ParamFetcher $request)
    {
        $parametros = $request->getParams();

        $retorno = $this->acaoSistemaFacade->listar($parametros);

        return ResponseFactory::ok($retorno);
    }

    /**
     *
     * @SWG\Get(
     *     path="/api/acao_sistema/{id}",
     *     summary="Buscar a acao_sistema",
     *     description="Busca a acao_sistema através da ID",
     *     produces={"application/json"},
     * @SWG\Response(
     *         response="200",
     *         description="Retorna a livro"
     *     ),
     * @SWG\Response(
     *         response="404",
     *         description="Ocorreu algum erro no servidor",
     *     )
     * )
     *
     * @FOSRest\Get("/acao_sistema/{id}")
     *
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function buscar($id)
    {
        $mensagem  = "";
        $objetoORM = $this->acaoSistemaFacade->buscarPorId($mensagem, $id);
        if (is_null($objetoORM) === true) {
            return ResponseFactory::notFound([], $mensagem);
        }

        return ResponseFactory::ok($objetoORM);
    }

    /**
     *
     * @SWG\Post(
     *     path="/api/acao_sistema/criar",
     *     summary="Cria uma acao_sistema",
     *     description="Cria uma acao_sistema no banco",
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
     * @FOSRest\RequestParam(name="descricao",               strict=true, nullable=false, allowBlank=false, description="Constante reconhecida pelo front")
     * @FOSRest\RequestParam(name="permissao_descricao",     strict=true, nullable=false, allowBlank=false, description="Descrição detalhada da permissão")
     * @FOSRest\RequestParam(name="solicita_login_superior", strict=true, nullable=false, allowBlank=false, description="Item", requirements="(0|1)", default="0")
     * @FOSRest\RequestParam(name="modulos",                 strict=true, nullable=true, allowBlank=false, description="Array de modulos", requirements="\d+", map=true)
     *
     * @FOSRest\Post("/acao_sistema/criar")
     *
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function criar(ParamFetcher $request)
    {
        $parametros = $request->all();
        $mensagem   = "";
        $objetoORM  = $this->acaoSistemaFacade->criar($mensagem, $parametros);
        if ((is_null($objetoORM) === true) || (empty($mensagem) === false)) {
            return ResponseFactory::conflict(["parametros" => $parametros], $mensagem);
        }

        return ResponseFactory::created(["objetoORM" => $objetoORM->getId()], "Registro criado com sucesso!");
    }

    /**
     *
     * @SWG\Patch(
     *     path="/api/acao_sistema/atualizar/{id}",
     *     summary="Atualiza um acao_sistema",
     *     description="Atualiza um acao_sistema no banco",
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
     * @FOSRest\RequestParam(name="descricao",               strict=true, nullable=true, allowBlank=false, description="Constante reconhecida pelo front")
     * @FOSRest\RequestParam(name="permissao_descricao",     strict=true, nullable=false, allowBlank=false, description="Descrição detalhada da permissão")
     * @FOSRest\RequestParam(name="solicita_login_superior", strict=true, nullable=true, allowBlank=false, description="Item", requirements="(0|1)", default="0")
     * @FOSRest\RequestParam(name="modulos",                 strict=true, nullable=true, allowBlank=false, description="Array de modulos", requirements="\d+", map=true)
     *
     * @FOSRest\Patch("/acao_sistema/atualizar/{id}")
     *
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function atualizar($id, ParamFetcher $request)
    {
        $parametros = $request->all();
        $mensagem   = "";
        $retorno    = $this->acaoSistemaFacade->atualizar($mensagem, $id, $parametros);
        if ($retorno === false) {
            return ResponseFactory::badRequest(["parametros" => $parametros], $mensagem);
        }

        return ResponseFactory::noContent([]);
    }


}
