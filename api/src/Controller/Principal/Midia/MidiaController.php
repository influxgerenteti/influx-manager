<?php

namespace App\Controller\Principal\Midia;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use FOS\RestBundle\Controller\Annotations as FOSRest;
use Swagger\Annotations as SWG;
use App\Factory\ResponseFactory;
use App\Controller\Principal\Base\GenericController;
use App\Facade\Principal\MidiaFacade;
use App\Facade\Principal\MidiaFranqueadaFacade;
use App\Helper\ConstanteParametros;
use App\Helper\VariaveisCompartilhadas;
use FOS\RestBundle\Request\ParamFetcher;

/**
 *
 * @author        Dayan Freitas
 * @Route("/api")
 */
class MidiaController extends GenericController
{
    /**
     *
     * @var \App\Facade\Principal\MidiaFranqueadaFacade
     */
    private $midiaFranqueadaFacade;

    /**
     *
     * @var \App\Facade\Principal\MidiaFacade
     */
    private $midiaFacade;


    /**
     * {@inheritdoc}
     */
    protected function constroiFacades()
    {
        parent::constroiFacades();
        $this->midiaFranqueadaFacade = new MidiaFranqueadaFacade(self::getManagerRegistry());
        $this->midiaFacade           = new MidiaFacade(self::getManagerRegistry());
    }

    /**
     *
     * @SWG\Get(
     *     path="/api/midia/listar",
     *     summary="Listar midia",
     *     description="Lista as midia do banco",
     *     produces={"application/json"},
     * @SWG\Response(
     *         response="200",
     *         description="Retorna os midia"
     *     ),
     * @SWG\Response(
     *         response="400",
     *         description="Ocorreu algum erro no servidor",
     *     )
     * )
     *
     * @FOSRest\QueryParam(name="pagina",       strict=false, allowBlank=false, default="1", description="Pagina para realizar o scroll", requirements="\d+")
     * @FOSRest\QueryParam(name="descricao",    strict=false, allowBlank=false, description="Filtro de Desricao")
     * @FOSRest\QueryParam(name="tipo",         strict=false, allowBlank=false, description="Filtro de tipo")
     * @FOSRest\QueryParam(name="situacao",     strict=false, allowBlank=false, description="Filtro de situacao")
     * @FOSRest\QueryParam(name="visibilidade", strict=false, allowBlank=false, description="Filtro de visibilidade", requirements="(0|1)")
     *
     * @FOSRest\Get("/midia/listar")
     *
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function lista(ParamFetcher $request)
    {
        $parametros = $request->all();
        $mensagem   = "";

        $resultados = $this->midiaFacade->listar($parametros);
        // $resultados = $this->midiaFranqueadaFacade->listar($parametros);
        if ($resultados === false) {
            return ResponseFactory::badRequest([], $mensagem);
        }

        return ResponseFactory::ok($resultados);
    }

    /**
     *
     * @SWG\Get(
     *     path="/api/midia/{id}",
     *     summary="Buscar a midia",
     *     description="Busca a midia através da ID",
     *     produces={"application/json"},
     * @SWG\Response(
     *         response="200",
     *         description="Retorna a midia"
     *     ),
     * @SWG\Response(
     *         response="404",
     *         description="Ocorreu algum erro no servidor",
     *     )
     * )
     *
     * @FOSRest\Get("/midia/{id}")
     *
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function buscar($id)
    {
        $objetoORM = $this->midiaFacade->buscarPorId($mensagem, $id);

        if (is_null($objetoORM) === true) {
            return ResponseFactory::notFound([], $mensagem);
        }

        return ResponseFactory::ok($objetoORM);
    }

    /**
     *
     * @SWG\Post(
     *     path="/api/midia/criar",
     *     summary="Cria uma midia",
     *     description="Cria uma midia no banco",
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
     * @FOSRest\RequestParam(name="descricao", strict=true, nullable=false, allowBlank=false, description="Descrição da midia")
     * @FOSRest\RequestParam(name="tipo",      strict=true, nullable=false, allowBlank=false, description="tipo", requirements="(MON|MOF|MLOC)")
     * @FOSRest\RequestParam(name="situacao",  strict=true, nullable=false, allowBlank=false, description="situacao", requirements="(A|I)")
     *
     * @FOSRest\Post("/midia/criar")
     *
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function criar(ParamFetcher $request)
    {
        $parametros = $request->all();
        $mensagem   = "";
        $objetoORM  = $this->midiaFacade->criar($mensagem, $parametros);
        if ((is_null($objetoORM) === true) || (empty($mensagem) === false)) {
            return ResponseFactory::conflict(["parametros" => $parametros], $mensagem);
        }

        $parametrosMidiaFranquedaFacade = [
            ConstanteParametros::CHAVE_MIDIA        => $objetoORM,
            ConstanteParametros::CHAVE_FRANQUEADA   => $objetoORM->getFranqueada(),
            ConstanteParametros::CHAVE_VISIBILIDADE => true,
        ];

        $objetoMidiaFranquedaORM = $this->midiaFranqueadaFacade->criar($mensagem, $parametrosMidiaFranquedaFacade);
        if ((is_null($objetoMidiaFranquedaORM) === true) || (empty($mensagem) === false)) {
            return ResponseFactory::conflict(["parametrosMidiaFranquedaFacade" => $parametros], $mensagem);
        }

        return ResponseFactory::created(["objetoORM" => $objetoORM->getId()], "Registro criado com sucesso!");
    }

    /**
     *
     * @SWG\Patch(
     *     path="/api/midia/atualizar/{id}",
     *     summary="Atualiza um midia",
     *     description="Atualiza um midia no banco",
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
     * @FOSRest\RequestParam(name="descricao",    strict=true, nullable=false, allowBlank=false, description="Descrição da midia")
     * @FOSRest\RequestParam(name="tipo",         strict=true, nullable=false, allowBlank=false, description="tipo", requirements="(MON|MOF|MLOC)")
     * @FOSRest\RequestParam(name="situacao",     strict=true, nullable=false, allowBlank=false, description="situacao", requirements="(A|I)")
     * @FOSRest\RequestParam(name="visibilidade", strict=false, nullable=true, allowBlank=true, description="Visibiliadade da franquia", requirements="(0|1)")
     *
     * @FOSRest\Patch("/midia/atualizar/{id}")
     *
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function atualizar($id, ParamFetcher $request)
    {

        $parametros = $request->all();
        $mensagem   = "";
        $retorno    = $this->midiaFacade->atualizar($mensagem, $id, $parametros);
        if ($retorno === false) {
            return ResponseFactory::badRequest(["parametros" => $parametros], $mensagem);
        }

        $parametrosMidiaFranquedaFacade = [
            ConstanteParametros::CHAVE_FRANQUEADA   => VariaveisCompartilhadas::$franqueadaID,
            ConstanteParametros::CHAVE_MIDIA        => $id,
            ConstanteParametros::CHAVE_VISIBILIDADE => $parametros[ConstanteParametros::CHAVE_VISIBILIDADE],
        ];

        $retornoMidiaFranqueada = $this->midiaFranqueadaFacade->atualizar($mensagem, $id, $parametrosMidiaFranquedaFacade);
        if ($retornoMidiaFranqueada === false) {
            return ResponseFactory::badRequest(["parametrosMidiaFranquedaFacade" => $parametrosMidiaFranquedaFacade], $mensagem);
        }

        return ResponseFactory::noContent([], "Registro atualizado com sucesso!");
    }

    /**
     *
     * @SWG\Delete(
     *     path="/api/midia/remover/{id}",
     *     summary="Remove uma midia",
     *     description="Remove uma midia no banco",
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
     * @FOSRest\Delete("/midia/remover/{id}")
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
