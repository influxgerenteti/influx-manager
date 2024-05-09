<?php

namespace App\Controller\Principal\SalaFranqueada;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use FOS\RestBundle\Controller\Annotations as FOSRest;
use Swagger\Annotations as SWG;
use App\Factory\ResponseFactory;
use App\Controller\Principal\Base\GenericController;
use FOS\RestBundle\Request\ParamFetcher;
use App\Facade\Principal\SalaFranqueadaFacade;

/**
 *
 * @author        Luiz A Costa
 * @Route("/api")
 */
class SalaFranqueadaController extends GenericController
{

    /**
     *
     * @var \App\Facade\Principal\SalaFranqueadaFacade
     */
    private $salaFranqueadaFacade;

    /**
     * {@inheritdoc}
     */
    protected function constroiFacades()
    {
        // Para criar o LogFacade do GenericController
        parent::constroiFacades();
        $this->salaFranqueadaFacade = new SalaFranqueadaFacade(self::getManagerRegistry());
    }

    /**
     *
     * @SWG\Get(
     *     path="/api/sala_franqueada/listar",
     *     summary="Listar sala_franqueada",
     *     description="Lista as sala_franqueada do banco",
     *     produces={"application/json"},
     * @SWG\Response(
     *         response="200",
     *         description="Retorna os sala_franqueada"
     *     ),
     * @SWG\Response(
     *         response="400",
     *         description="Ocorreu algum erro no servidor",
     *     )
     * )
     *
     * @FOSRest\QueryParam(name="pagina",  strict=false, allowBlank=false, default="1", description="Pagina para realizar o scroll", requirements="\d+")
     * @FOSRest\QueryParam(name="order",   strict=false, nullable=true,  description="Coluna de ordenação")
     * @FOSRest\QueryParam(name="direcao", strict=false, nullable=true,  description="ASC|DESC")
     *
     * @FOSRest\Get("/sala_franqueada/listar")
     *
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function lista(ParamFetcher $request)
    {
        $parametros = $request->all();
        $mensagem   = "";
        $resultados = $this->salaFranqueadaFacade->listar($parametros);
        if ($resultados === false) {
            return ResponseFactory::badRequest([], $mensagem);
        }

        return ResponseFactory::ok($resultados);
    }

    /**
     *
     * @SWG\Get(
     *     path="/api/sala_franqueada/{id}",
     *     summary="Buscar a sala_franqueada",
     *     description="Busca a sala_franqueada através da ID",
     *     produces={"application/json"},
     * @SWG\Response(
     *         response="200",
     *         description="Retorna a sala_franqueada"
     *     ),
     * @SWG\Response(
     *         response="404",
     *         description="Ocorreu algum erro no servidor",
     *     )
     * )
     *
     * @FOSRest\Get("/sala_franqueada/{id}")
     *
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function buscar($id)
    {
        $mensagem  = "";
        $objetoORM = $this->salaFranqueadaFacade->buscarPorId($mensagem, $id);
        if (is_null($objetoORM) === true) {
            return ResponseFactory::notFound([], $mensagem);
        }

        return ResponseFactory::ok($objetoORM);
    }

    /**
     *
     * @SWG\Post(
     *     path="/api/sala_franqueada/criar",
     *     summary="Cria uma sala_franqueada",
     *     description="Cria uma sala_franqueada no banco",
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
     * @FOSRest\RequestParam(name="sala",           strict=true, nullable=false, allowBlank=false, description="Sala", requirements="\d+")
     * @FOSRest\RequestParam(name="franqueada",     strict=true, nullable=false, allowBlank=false, description="Franqueada", requirements="\d+")
     * @FOSRest\RequestParam(name="lotacao_maxima", strict=true, nullable=false, allowBlank=false, description="Lotacao Maxima", requirements="\d+", default="0")
     * @FOSRest\RequestParam(name="personal",       strict=true, nullable=false, allowBlank=false, description="Personal", requirements="[0|1]", default="0")
     * @FOSRest\RequestParam(name="situacao",       strict=true, nullable=false, allowBlank=false, description="Situação", requirements="[A|I]", default="I")
     *
     * @FOSRest\Post("/sala_franqueada/criar")
     *
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function criar(ParamFetcher $request)
    {
        $parametros = $request->all();
        $mensagem   = "";
        $objetoORM  = $this->salaFranqueadaFacade->criar($mensagem, $parametros);
        if ((is_null($objetoORM) === true) || (empty($mensagem) === false)) {
            return ResponseFactory::conflict(["parametros" => $parametros], $mensagem);
        }

        return ResponseFactory::created(["objetoORM" => $objetoORM->getId()], "Registro criado com sucesso!");
    }

    /**
     *
     * @SWG\Patch(
     *     path="/api/sala_franqueada/atualizar/{id}",
     *     summary="Atualiza um sala_franqueada",
     *     description="Atualiza um sala_franqueada no banco",
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
     * @FOSRest\RequestParam(name="sala",           strict=false, nullable=true, allowBlank=false, description="Sala", requirements="\d+")
     * @FOSRest\RequestParam(name="franqueada",     strict=false, nullable=true, allowBlank=false, description="Franqueada", requirements="\d+")
     * @FOSRest\RequestParam(name="lotacao_maxima", strict=false, nullable=false, allowBlank=false, description="Lotacao Maxima", requirements="\d+", default="0")
     * @FOSRest\RequestParam(name="personal",       strict=false, nullable=false, allowBlank=false, description="Personal", requirements="[0|1]")
     * @FOSRest\RequestParam(name="situacao",       strict=false, nullable=false, allowBlank=false, description="Situacao", requirements="[A|I]")
     *
     * @FOSRest\Patch("/sala_franqueada/atualizar/{id}")
     *
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function atualizar($id, ParamFetcher $request)
    {
        $parametros = $request->all();
        $mensagem   = "";
        $retorno    = $this->salaFranqueadaFacade->atualizar($mensagem, $id, $parametros);
        if ($retorno === false) {
            return ResponseFactory::badRequest(["parametros" => $parametros], $mensagem);
        }

        return ResponseFactory::noContent([]);
    }

    /**
     *
     * @SWG\Delete(
     *     path="/api/sala_franqueada/remover/{id}",
     *     summary="Remove uma sala_franqueada",
     *     description="Remove uma sala_franqueada no banco",
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
     * @FOSRest\Delete("/sala_franqueada/remover/{id}")
     *
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function excluir($id)
    {
        $mensagem = "";
        $retorno  = $this->salaFranqueadaFacade->remover($mensagem, $id);
        if ($retorno === false) {
            return ResponseFactory::badRequest([], $mensagem);
        }

        return ResponseFactory::ok([], "Excluido com sucesso");
    }


}
