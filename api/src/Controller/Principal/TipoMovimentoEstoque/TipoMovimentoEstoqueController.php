<?php

namespace App\Controller\Principal\TipoMovimentoEstoque;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use FOS\RestBundle\Controller\Annotations as FOSRest;
use Swagger\Annotations as SWG;
use App\Factory\ResponseFactory;
use App\Controller\Principal\Base\GenericController;
use FOS\RestBundle\Request\ParamFetcher;

/**
 *
 * @author        Luiz A Costa
 * @Route("/api")
 */
class TipoMovimentoEstoqueController extends GenericController
{

    /**
     *
     * @var \App\Facade\Principal\TipoMovimentoEstoqueFacade
     */
    private $tipoMovimentoEstoqueFacade;

    /**
     * {@inheritdoc}
     */
    protected function constroiFacades()
    {
        // Para criar o LogFacade do GenericController
        parent::constroiFacades();
        $this->tipoMovimentoEstoqueFacade = new \App\Facade\Principal\TipoMovimentoEstoqueFacade(self::getManagerRegistry());
    }

    /**
     *
     * @SWG\Get(
     *     path="/api/tipo_movimento_estoque/listar",
     *     summary="Listar tipo_movimento_estoque",
     *     description="Lista as tipo_movimento_estoque do banco",
     *     tags={"Tipo Movimento Estoque"},
     *     produces={"application/json"},
     * @SWG\Response(
     *         response="200",
     *         description="Retorna os registros do TipoMovimentoEstoque"
     *     ),
     * @SWG\Response(
     *         response="400",
     *         description="Ocorreu algum erro no servidor",
     *     )
     * )
     *
     * @FOSRest\QueryParam(name="pagina",  strict=false, allowBlank=false, default="1", description="Pagina para realizar o scroll", requirements="\d{0,2}")
     * @FOSRest\QueryParam(name="order",   strict=false, nullable=true,  description="Coluna de ordenação")
     * @FOSRest\QueryParam(name="direcao", strict=false, nullable=true,  description="ASC|DESC")
     *
     * @FOSRest\Get("/tipo_movimento_estoque/listar")
     *
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function lista(ParamFetcher $request)
    {
        $parametros = $request->all();
        $mensagem   = "";
        $resultados = $this->tipoMovimentoEstoqueFacade->listar($parametros);
        if ($resultados === false) {
            return ResponseFactory::badRequest([], $mensagem);
        }

        return ResponseFactory::ok($resultados);
    }

    /**
     *
     * @SWG\Get(
     *     path="/api/tipo_movimento_estoque/{id}",
     *     summary="Buscar a tipo_movimento_estoque",
     *     description="Busca a tipo_movimento_estoque através da ID",
     *     tags={"Tipo Movimento Estoque"},
     *     produces={"application/json"},
     * @SWG\Response(
     *         response="200",
     *         description="Retorna o TipoMovimentoEstoque"
     *     ),
     * @SWG\Response(
     *         response="404",
     *         description="Ocorreu algum erro no servidor",
     *     )
     * )
     *
     * @FOSRest\Get("/tipo_movimento_estoque/{id}")
     *
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function buscar($id)
    {
        $mensagem  = "";
        $objetoORM = $this->tipoMovimentoEstoqueFacade->buscarPorId($mensagem, $id);
        if (is_null($objetoORM) === true) {
            return ResponseFactory::notFound([], "Tipo Movimento Estoque não encontrado.");
        }

        return ResponseFactory::ok($objetoORM);
    }

    /**
     *
     * @SWG\Post(
     *     path="/api/tipo_movimento_estoque/criar",
     *     summary="Cria um TipoMovimentoEstoque",
     *     description="Cria um TipoMovimentoEstoque no banco",
     *     tags={"Tipo Movimento Estoque"},
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
     * @FOSRest\RequestParam(name="descricao",      strict=true, nullable=false, allowBlank=true, description="Descricao do Tipo Movimento Estoque", requirements=".{0,35}+")
     * @FOSRest\RequestParam(name="tipo_movimento", strict=false, nullable=false, allowBlank=false, description="Tipo do movimento", requirements="[E|S]", default="E")
     *
     * @FOSRest\Post("/tipo_movimento_estoque/criar")
     *
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function criar(ParamFetcher $request)
    {
        $parametros = $request->all();
        $mensagem   = "";
        $objetoORM  = $this->tipoMovimentoEstoqueFacade->criar($mensagem, $parametros);
        if ((is_null($objetoORM) === true) || (empty($mensagem) === false)) {
            return ResponseFactory::conflict(["parametros" => $parametros], $mensagem);
        }

        return ResponseFactory::created(["objetoORM" => $objetoORM], "Registro criado com sucesso!");
    }

    /**
     *
     * @SWG\Patch(
     *     path="/api/tipo_movimento_estoque/atualizar/{id}",
     *     summary="Atualiza um TipoMovimentoEstoque",
     *     description="Atualiza um TipoMovimentoEstoque no banco",
     *     tags={"Tipo Movimento Estoque"},
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
     * @FOSRest\RequestParam(name="descricao",      strict=true, nullable=false, allowBlank=true, description="Descricao do Tipo Movimento Estoque", requirements=".{0,35}+")
     * @FOSRest\RequestParam(name="tipo_movimento", strict=false, nullable=false, allowBlank=false, description="Tipo do movimento estoque", requirements="[E|S]", default="E")
     * @FOSRest\RequestParam(name="situacao",       strict=false, nullable=false, allowBlank=false, description="Situacao do Tipo Movimento Estoque", requirements="[A|I]", default="A")
     *
     * @FOSRest\Patch("/tipo_movimento_estoque/atualizar/{id}")
     *
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function atualizar($id, ParamFetcher $request)
    {
        $parametros = $request->all();
        $mensagem   = "";
        $retorno    = $this->tipoMovimentoEstoqueFacade->atualizar($mensagem, $id, $parametros);
        if ($retorno === false) {
            return ResponseFactory::badRequest(["parametros" => $parametros], $mensagem);
        }

        return ResponseFactory::noContent([]);
    }

    /**
     *
     * @SWG\Patch(
     *     path="/api/tipo_movimento_estoque/atualizarSituacao/{id}",
     *     summary="Atualiza a situacao de um Tipo Movimento Estoque",
     *     description="Atualiza a situacao de um Tipo Movimento Estoque no banco de dados",
     *     tags={"Tipo Movimento Estoque"},
     *     produces={"application/json"},
     * @SWG\Response(
     *         response="200",
     *         description="Retorna ativado/desativado com sucesso"
     *     ),
     * @SWG\Response(
     *         response="400",
     *         description="Ocorreu algum erro no servidor",
     *     )
     * )
     * @FOSRest\RequestParam(name="situacao", strict=true, nullable=false, allowBlank=false, description="Situacao do Tipo Movimento Estoque", requirements="[A|I]")
     *
     * @FOSRest\Patch("/tipo_movimento_estoque/atualizarSituacao/{id}")
     *
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function atualizarSituacao($id, ParamFetcher $request)
    {
        $mensagem = "";
        $retorno  = $this->tipoMovimentoEstoqueFacade->atualizarSituacao($mensagem, $id, $request->get("situacao"));
        if ($retorno === false) {
            return ResponseFactory::badRequest([], $mensagem);
        }

        if ($request->get("situacao") === "A") {
            $mensagemSucesso = "ativado";
        } else {
            $mensagemSucesso = "desativado";
        }

        return ResponseFactory::ok([], "Tipo de movimento de estoque " . $mensagemSucesso . " com sucesso!");
    }


}
