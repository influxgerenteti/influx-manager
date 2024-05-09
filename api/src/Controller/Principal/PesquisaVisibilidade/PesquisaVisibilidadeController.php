<?php

namespace App\Controller\Principal\PesquisaVisibilidade;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use FOS\RestBundle\Controller\Annotations as FOSRest;
use Swagger\Annotations as SWG;
use App\Facade\Principal\PesquisaVisibilidadeFacade;
use App\Factory\ResponseFactory;
use App\Controller\Principal\Base\GenericController;
use FOS\RestBundle\Request\ParamFetcher;

/**
 *
 * @author        Dayan Freitas
 * @Route("/api")
 */
class PesquisaVisibilidadeController extends GenericController
{


    /**
     *
     * @var \App\Facade\Principal\PesquisaVisibilidadeFacade
     */
    private $pesquisaVisibilidadeFacade;


    /**
     * {@inheritdoc}
     */
    protected function constroiFacades()
    {
        // Para criar o LogFacade do GenericController
        parent::constroiFacades();
        $this->pesquisaVisibilidadeFacade = new PesquisaVisibilidadeFacade(self::getManagerRegistry());

    }

    /**
     *
     * @SWG\Get(
     *     path="/api/pesquisa_visibilidade/listar",
     *     summary="Listar pesquisa_visibilidade",
     *     description="Lista as pesquisa_visibilidade do banco",
     *     produces={"application/json"},
     * @SWG\Response(
     *         response="200",
     *         description="Retorna os pesquisa_visibilidade"
     *     ),
     * @SWG\Response(
     *         response="400",
     *         description="Ocorreu algum erro no servidor",
     *     )
     * )
     *
     * @FOSRest\QueryParam(name="pagina",   strict=false, allowBlank=false, default="1", description="Pagina para realizar o scroll", requirements="\d+")
     * @FOSRest\QueryParam(name="nome",     strict=false, allowBlank=false, description="Filtrar por nome")
     * @FOSRest\QueryParam(name="tipo",     strict=false, allowBlank=false, description="Tipo")
     * @FOSRest\QueryParam(name="situacao", strict=false, allowBlank=false, description="Situacao")
     *
     * @FOSRest\QueryParam(name="order",   strict=false, nullable=true,  description="Coluna de ordenação")
     * @FOSRest\QueryParam(name="direcao", strict=false, nullable=true,  description="ASC|DESC")
     *
     * @FOSRest\Get("/pesquisa_visibilidade/listar")
     *
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function lista(ParamFetcher $request)
    {
        $parametros = $request->all();
        $mensagem   = "";
        $resultados = $this->pesquisaVisibilidadeFacade->listar($mensagem, $parametros);
        if ($resultados === false) {
            return ResponseFactory::badRequest([], $mensagem);
        }

        return ResponseFactory::ok($resultados);
    }

    /**
     *
     * @SWG\Get(
     *     path="/api/pesquisa_visibilidade/{id}",
     *     summary="Buscar a pesquisa_visibilidade",
     *     description="Busca a pesquisa_visibilidade através da ID",
     *     produces={"application/json"},
     * @SWG\Response(
     *         response="200",
     *         description="Retorna a pesquisa_visibilidade"
     *     ),
     * @SWG\Response(
     *         response="404",
     *         description="Ocorreu algum erro no servidor",
     *     )
     * )
     *
     * @FOSRest\Get("/pesquisa_visibilidade/{id}")
     *
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function buscar($id)
    {
        $objetoORM = null;
        $mensagem  = null;
        $objetoORM = $this->pesquisaVisibilidadeFacade->buscarPorId($mensagem, $id);
        if (is_null($objetoORM) === true)
            return ResponseFactory::notFound([], "Pesquisa de visibilidade não encontrada.");
        return ResponseFactory::ok($objetoORM);
    }

    /**
     *
     * @SWG\Post(
     *     path="/api/pesquisa_visibilidade/criar",
     *     summary="Cria uma pesquisa_visibilidade",
     *     description="Cria uma pesquisa_visibilidade no banco",
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
     * @FOSRest\RequestParam(name="nome",     strict=true, nullable=false, allowBlank=false, description="Nome da pesquisa de visibilidade")
     * @FOSRest\RequestParam(name="tipo",     strict=true, nullable=false, allowBlank=false, description="tipo")
     * @FOSRest\RequestParam(name="situacao", strict=true, nullable=false, allowBlank=false, description="situacao")
     *
     * @FOSRest\Post("/pesquisa_visibilidade/criar")
     *
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function criar(ParamFetcher $request)
    {
        $parametros = $request->all();
        $mensagem   = "";
        $objetoORM  = $this->pesquisaVisibilidadeFacade->criar($mensagem, $parametros);
        if ((is_null($objetoORM) === true) || (empty($mensagem) === false)) {
            return ResponseFactory::conflict(["parametros" => $parametros], $mensagem);
        }

        return ResponseFactory::created(["objetoORM" => $objetoORM->getId()], "Registro criado com sucesso!");
    }

    /**
     *
     * @SWG\Patch(
     *     path="/api/pesquisa_visibilidade/atualizar/{id}",
     *     summary="Atualiza um pesquisa_visibilidade",
     *     description="Atualiza um pesquisa_visibilidade no banco",
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
     * @FOSRest\RequestParam(name="nome",     strict=false, nullable=true, allowBlank=false, description="Nome da pesquisa de visibilidade")
     * @FOSRest\RequestParam(name="tipo",     strict=false, nullable=true, allowBlank=false, description="tipo")
     * @FOSRest\RequestParam(name="situacao", strict=false, nullable=true, allowBlank=false, description="situacao")
     *
     * @FOSRest\Patch("/pesquisa_visibilidade/atualizar/{id}")
     *
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function atualizar($id, ParamFetcher $request)
    {
        $parametros = $request->all();
        $mensagem   = "";
        $retorno    = $this->pesquisaVisibilidadeFacade->atualizar($mensagem, $id, $parametros);
        if (($retorno === false) || (empty($mensagem) === false)) {
            return ResponseFactory::badRequest(["parametros" => $parametros], $mensagem);
        }

        return ResponseFactory::noContent([]);
    }

    /**
     *
     * @SWG\Delete(
     *     path="/api/pesquisa_visibilidade/remover/{id}",
     *     summary="Remove uma pesquisa_visibilidade",
     *     description="Remove uma pesquisa_visibilidade no banco",
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
     * @FOSRest\Delete("/pesquisa_visibilidade/remover/{id}")
     *
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function excluir($id)
    {
        $mensagem = "";
        $retorno  = false;
        // TODO: True ou False
        if ($retorno === false) {
            return ResponseFactory::badRequest([], $mensagem);
        }

        return ResponseFactory::ok([], "Excluido com sucesso");
    }


}
