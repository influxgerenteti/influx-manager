<?php

namespace App\Controller\Principal\Semestre;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use FOS\RestBundle\Controller\Annotations as FOSRest;
use Swagger\Annotations as SWG;
use App\Factory\ResponseFactory;
use App\Controller\Principal\Base\GenericController;
use FOS\RestBundle\Request\ParamFetcher;
use App\Facade\Principal\SemestreFacade;

/**
 *
 * @author        Luiz A Costa
 * @Route("/api")
 */
class SemestreController extends GenericController
{

    /**
     *
     * @var \App\Facade\Principal\SemestreFacade
     */
    private $semestreFacade;

    /**
     * {@inheritdoc}
     */
    protected function constroiFacades()
    {
        // Para criar o LogFacade do GenericController
        parent::constroiFacades();
        $this->semestreFacade = new SemestreFacade(self::getManagerRegistry());
    }

    /**
     *
     * @SWG\Get(
     *     path="/api/semestre/listar",
     *     summary="Listar semestre",
     *     description="Lista as semestre do banco",
     *     produces={"application/json"},
     * @SWG\Response(
     *         response="200",
     *         description="Retorna os semestre"
     *     ),
     * @SWG\Response(
     *         response="400",
     *         description="Ocorreu algum erro no servidor",
     *     )
     * )
     *
     * @FOSRest\QueryParam(name="pagina",                 strict=false, allowBlank=false, default="1", description="Pagina para realizar o scroll", requirements="\d+")
     * @FOSRest\QueryParam(name="listar_proximos",        strict=false, allowBlank=false, default="0", description="Se deve ou não listar semestres passados", requirements="[0|1]")
     * @FOSRest\QueryParam(name="anterior_atual_proximo", strict=false, allowBlank=false, default="0", description="Listar semestres anterior, atual e próximo", requirements="[0|1]")
     *
     * @FOSRest\Get("/semestre/listar")
     *
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function lista(ParamFetcher $request)
    {
        $parametros = $request->all();
        $mensagem   = "";
        $resultados = $this->semestreFacade->listar($parametros, $mensagem);
        if ($resultados === false) {
            return ResponseFactory::badRequest([], $mensagem);
        }

        return ResponseFactory::ok($resultados);
    }

    /**
     *
     * @SWG\Get(
     *     path="/api/semestre/{id}",
     *     summary="Buscar a semestre",
     *     description="Busca a semestre através da ID",
     *     produces={"application/json"},
     * @SWG\Response(
     *         response="200",
     *         description="Retorna a semestre"
     *     ),
     * @SWG\Response(
     *         response="404",
     *         description="Ocorreu algum erro no servidor",
     *     )
     * )
     *
     * @FOSRest\Get("/semestre/{id}")
     *
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function buscar($id)
    {
        $mensagemErro = "";
        $objetoORM    = $this->semestreFacade->buscarPorId($mensagemErro, $id);
        if (is_null($objetoORM) === true) {
            return ResponseFactory::notFound([], $mensagemErro);
        }

        return ResponseFactory::ok($objetoORM);
    }

    /**
     *
     * @SWG\Post(
     *     path="/api/semestre/criar",
     *     summary="Cria uma semestre",
     *     description="Cria uma semestre no banco",
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
     * @FOSRest\RequestParam(name="descricao",    strict=true, nullable=true, allowBlank=false, description="descricao", requirements=".{0,20}")
     * @FOSRest\RequestParam(name="data_inicio",  strict=true, nullable=true, allowBlank=false, description="Data de inicio")
     * @FOSRest\RequestParam(name="data_termino", strict=true, nullable=true, allowBlank=false, description="Data de termino")
     *
     * @FOSRest\Post("/semestre/criar")
     *
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function criar(ParamFetcher $request)
    {
        $parametros = $request->all();
        $mensagem   = "";
        $objetoORM  = $this->semestreFacade->criar($mensagem, $parametros);
        if ((is_null($objetoORM) === true) || (empty($mensagem) === false)) {
            return ResponseFactory::conflict(["parametros" => $parametros], $mensagem);
        }

        return ResponseFactory::created(["id" => $objetoORM->getId()], "Registro criado com sucesso!");
    }

    /**
     *
     * @SWG\Patch(
     *     path="/api/semestre/atualizar/{id}",
     *     summary="Atualiza um semestre",
     *     description="Atualiza um semestre no banco",
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
     * @FOSRest\RequestParam(name="descricao",    strict=true, nullable=true, allowBlank=true, description="descricao", requirements=".{0,20}")
     * @FOSRest\RequestParam(name="data_inicio",  strict=true, nullable=true, allowBlank=true, description="Data de inicio")
     * @FOSRest\RequestParam(name="data_termino", strict=true, nullable=true, allowBlank=true, description="Data de termino")
     *
     * @FOSRest\Patch("/semestre/atualizar/{id}")
     *
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function atualizar($id, ParamFetcher $request)
    {
        $parametros = $request->all();
        $mensagem   = "";
        $retorno    = $this->semestreFacade->atualizar($mensagem, $id, $parametros);
        if ($retorno === false) {
            return ResponseFactory::badRequest(["parametros" => $parametros], $mensagem);
        }

        return ResponseFactory::created(["id" => $objetoORM->getId()], "Registro atualizado com sucesso!");
    }


}
