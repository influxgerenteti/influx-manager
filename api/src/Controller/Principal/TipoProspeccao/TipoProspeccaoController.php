<?php

namespace App\Controller\Principal\TipoProspeccao;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use FOS\RestBundle\Controller\Annotations as FOSRest;
use Swagger\Annotations as SWG;
use App\Factory\ResponseFactory;
use App\Controller\Principal\Base\GenericController;
use FOS\RestBundle\Request\ParamFetcher;
use App\Facade\Principal\TipoProspeccaoFacade;

/**
 *
 * @author        Luiz A Costa
 * @Route("/api")
 */
class TipoProspeccaoController extends GenericController
{

    /**
     *
     * @var \App\Facade\Principal\TipoProspeccaoFacade
     */
    private $tipoProspeccaoFacade;

    /**
     * {@inheritdoc}
     */
    protected function constroiFacades()
    {
        // Para criar o LogFacade do GenericController
        parent::constroiFacades();
        $this->tipoProspeccaoFacade = new TipoProspeccaoFacade(self::getManagerRegistry());
    }

    /**
     *
     * @SWG\Get(
     *     path="/api/tipo_prospeccao/listar",
     *     summary="Listar tipo_prospeccao",
     *     description="Lista as tipo_prospeccao do banco",
     *     tags={"Tipo Prospeccao"},
     *     produces={"application/json"},
     * @SWG\Response(
     *         response="200",
     *         description="Retorna os tipo_prospeccao"
     *     ),
     * @SWG\Response(
     *         response="400",
     *         description="Ocorreu algum erro no servidor",
     *     )
     * )
     *
     * @FOSRest\QueryParam(name="pagina", strict=false, allowBlank=false, default="1", description="Pagina para realizar o scroll", requirements="\d+")
     *
     * @FOSRest\Get("/tipo_prospeccao/listar")
     *
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function lista(ParamFetcher $request)
    {
        $parametros = $request->all();
        $mensagem   = "";
        $resultados = $this->tipoProspeccaoFacade->listar($parametros);
        if ($resultados === false) {
            return ResponseFactory::badRequest([], $mensagem);
        }

        return ResponseFactory::ok($resultados);
    }

    /**
     *
     * @SWG\Get(
     *     path="/api/tipo_prospeccao/{id}",
     *     summary="Buscar a tipo_prospeccao",
     *     description="Busca a tipo_prospeccao através da ID",
     *     tags={"Tipo Prospeccao"},
     *     produces={"application/json"},
     * @SWG\Response(
     *         response="200",
     *         description="Retorna a tipo_prospeccao"
     *     ),
     * @SWG\Response(
     *         response="404",
     *         description="Ocorreu algum erro no servidor",
     *     )
     * )
     *
     * @FOSRest\Get("/tipo_prospeccao/{id}")
     *
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function buscar($id)
    {
        $mensagem  = "";
        $objetoORM = $this->tipoProspeccaoFacade->buscarPorId($mensagem, $id);
        if (is_null($objetoORM) === true) {
            return ResponseFactory::notFound([], $mensagem);
        }

        return ResponseFactory::ok($objetoORM);
    }

    /**
     *
     * @SWG\Post(
     *     path="/api/tipo_prospeccao/criar",
     *     summary="Cria uma tipo_prospeccao",
     *     description="Cria uma tipo_prospeccao no banco",
     *     tags={"Tipo Prospeccao"},
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
     * @FOSRest\RequestParam(name="tipo_pai_tipo_prospeccao", strict=true, nullable=true, allowBlank=true, description="Tipo Prospeccao ID", requirements="\d+")
     * @FOSRest\RequestParam(name="descricao",                strict=true, nullable=false, allowBlank=false, description="Descricao")
     *
     * @FOSRest\Post("/tipo_prospeccao/criar")
     *
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function criar(ParamFetcher $request)
    {
        $parametros = $request->all();
        $mensagem   = "";
        $objetoORM  = $this->tipoProspeccaoFacade->criar($mensagem, $parametros);
        if ((is_null($objetoORM) === true) || (empty($mensagem) === false)) {
            return ResponseFactory::conflict(["parametros" => $parametros], $mensagem);
        }

        return ResponseFactory::created(["objetoORM" => $objetoORM], "Registro criado com sucesso!");
    }

    /**
     *
     * @SWG\Patch(
     *     path="/api/tipo_prospeccao/atualizar/{id}",
     *     summary="Atualiza um tipo_prospeccao",
     *     description="Atualiza um tipo_prospeccao no banco",
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
     * @FOSRest\RequestParam(name="tipo_pai_tipo_prospeccao", strict=true, nullable=true, allowBlank=true, description="Tipo Prospeccao ID", requirements="\d+")
     * @FOSRest\RequestParam(name="descricao",                strict=true, nullable=true, allowBlank=true, description="Descricao")
     *
     * @FOSRest\Patch("/tipo_prospeccao/atualizar/{id}")
     *
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function atualizar($id, ParamFetcher $request)
    {
        $parametros = $request->all();
        $mensagem   = "";
        $retorno    = $this->tipoProspeccaoFacade->atualizar($mensagem, $id, $parametros);
        if ($retorno === false) {
            return ResponseFactory::badRequest(["parametros" => $parametros], $mensagem);
        }

        return ResponseFactory::noContent([]);
    }

    /**
     *
     * @SWG\Delete(
     *     path="/api/tipo_prospeccao/remover/{id}",
     *     summary="Remove uma tipo_prospeccao",
     *     description="Remove uma tipo_prospeccao no banco",
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
     * @FOSRest\Delete("/tipo_prospeccao/remover/{id}")
     *
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function excluir($id)
    {
        $mensagem = "";
        $retorno  = $this->tipoProspeccaoFacade->remover($mensagem, $id);
        if ($retorno === false) {
            return ResponseFactory::badRequest([], $mensagem);
        }

        return ResponseFactory::ok([], "Excluido com sucesso");
    }


}
