<?php

namespace App\Controller\Principal\IndisponibilidadePersonal;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use FOS\RestBundle\Controller\Annotations as FOSRest;
use Swagger\Annotations as SWG;
use App\Factory\ResponseFactory;
use App\Controller\Principal\Base\GenericController;
use FOS\RestBundle\Request\ParamFetcher;
use App\Facade\Principal\IndisponibilidadePersonalFacade;

/**
 *
 * @author        Marcelo A Naegeler
 * @Route("/api")
 */
class IndisponibilidadePersonalController extends GenericController
{

    /**
     *
     * @var \App\Facade\Principal\IndisponibilidadePersonalFacade
     */
    private $indisponibilidadePersonalFacade;

    /**
     * {@inheritdoc}
     */
    protected function constroiFacades()
    {
        // Para criar o LogFacade do GenericController
        parent::constroiFacades();
        $this->indisponibilidadePersonalFacade = new IndisponibilidadePersonalFacade(self::getManagerRegistry());
    }

    /**
     *
     * @SWG\Get(
     *     path="/api/indisponibilidade_personal/listar",
     *     summary="Listar indisponibilidade personal",
     *     description="Lista as indisponibilidade personal do banco",
     *     tags={"IndisponibilidadePersonal"},
     *     produces={"application/json"},
     * @SWG\Response(
     *         response="200",
     *         description="Retorna a lista de indisponibilidades personal"
     *     ),
     * @SWG\Response(
     *         response="400",
     *         description="Ocorreu algum erro no servidor",
     *     )
     * )
     *
     * @FOSRest\QueryParam(name="pagina", strict=false, allowBlank=false, default="1", description="Pagina para realizar o scroll", requirements="\d+")
     * @FOSRest\QueryParam(name="data",   strict=false, nullable=true, allowBlank=false, description="Filtro data")
     *
     * @FOSRest\Get("/indisponibilidade_personal/listar")
     *
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function lista(ParamFetcher $request)
    {
        $parametros = $request->all();
        $mensagem   = "";

        $resultados = $this->indisponibilidadePersonalFacade->listar($parametros);
        if ($resultados === false) {
            return ResponseFactory::badRequest([], $mensagem);
        }

        return ResponseFactory::ok($resultados);
    }

    /**
     *
     * @SWG\Get(
     *     path="/api/indisponibilidade_personal/{id}",
     *     summary="Buscar a indisponibilidade personal",
     *     description="Busca a indisponibilidade personal através da ID",
     *     tags={"IndisponibilidadePersonal"},
     *     produces={"application/json"},
     * @SWG\Response(
     *         response="200",
     *         description="Retorna a indisponibilidade personal"
     *     ),
     * @SWG\Response(
     *         response="404",
     *         description="Ocorreu algum erro no servidor",
     *     )
     * )
     *
     * @FOSRest\Get("/indisponibilidade_personal/{id}")
     *
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function buscar($id)
    {
        $mensagemErro = "";
        $objetoORM    = $this->indisponibilidadePersonalFacade->buscarPorId($mensagemErro, $id);
        if (is_null($objetoORM) === true) {
            return ResponseFactory::notFound([], $mensagemErro);
        }

        return ResponseFactory::ok($objetoORM);
    }

    /**
     *
     * @SWG\Post(
     *     path="/api/indisponibilidade_personal/criar",
     *     summary="Cria uma indisponibilidade personal",
     *     description="Cria uma indisponibilidade personal no banco",
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
     * @FOSRest\RequestParam(name="data_inicio", strict=true, nullable=false, allowBlank=false, description="Data de início")
     * @FOSRest\RequestParam(name="data_fim",    strict=true, nullable=false, allowBlank=false, description="Data de fim")
     * @FOSRest\RequestParam(name="hora_inicio", strict=true, nullable=false, allowBlank=false, description="Hora de início")
     * @FOSRest\RequestParam(name="hora_fim",    strict=true, nullable=false, allowBlank=false, description="Hora de fim")
     * @FOSRest\RequestParam(name="dia_semana",  strict=true, nullable=false, allowBlank=false, description="Dia da semana", requirements="\d")
     *
     * @FOSRest\Post("/indisponibilidade_personal/criar")
     *
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function criar(ParamFetcher $request)
    {
        $parametros = $request->all();
        $mensagem   = "";
        $objetoORM  = $this->indisponibilidadePersonalFacade->criar($mensagem, $parametros);
        if ((is_null($objetoORM) === true) || (empty($mensagem) === false)) {
            return ResponseFactory::conflict(["parametros" => $parametros], $mensagem);
        }

        return ResponseFactory::created(["id" => $objetoORM->getId()], "Registro criado com sucesso!");
    }

    /**
     *
     * @SWG\Patch(
     *     path="/api/indisponibilidade_personal/atualizar/{id}",
     *     summary="Atualiza um indisponibilidadePersonal",
     *     description="Atualiza um indisponibilidadePersonal no banco",
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
     * @FOSRest\RequestParam(name="data_inicio", strict=false, nullable=false, allowBlank=false, description="Data de início")
     * @FOSRest\RequestParam(name="data_fim",    strict=false, nullable=false, allowBlank=false, description="Data de fim")
     * @FOSRest\RequestParam(name="hora_inicio", strict=false, nullable=false, allowBlank=false, description="Hora de início", requirements="\d{2}:\d{2}")
     * @FOSRest\RequestParam(name="hora_fim",    strict=false, nullable=false, allowBlank=false, description="Hora de fim", requirements="\d{2}:\d{2}")
     * @FOSRest\RequestParam(name="dia_semana",  strict=false, nullable=false, allowBlank=false, description="Dia da semana", requirements="\d")
     *
     * @FOSRest\Patch("/indisponibilidade_personal/atualizar/{id}")
     *
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function atualizar($id, ParamFetcher $request)
    {
        $parametros = $request->all();
        $mensagem   = "";
        $retorno    = $this->indisponibilidadePersonalFacade->atualizar($mensagem, $id, $parametros);
        if ($retorno === false) {
            return ResponseFactory::badRequest(["parametros" => $parametros], $mensagem);
        }

        return ResponseFactory::noContent([]);
    }

    /**
     *
     * @SWG\Delete(
     *     path="/api/indisponibilidade_personal/remover/{id}",
     *     summary="Remove uma indisponibilidadePersonal",
     *     description="Remove uma indisponibilidadePersonal no banco",
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
     * @FOSRest\Delete("/indisponibilidade_personal/remover/{id}")
     *
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function excluir($id)
    {
        $mensagem = "";
        $retorno  = $this->indisponibilidadePersonalFacade->remover($mensagem, $id);
        if ($retorno === false) {
            return ResponseFactory::badRequest([], $mensagem);
        }

        return ResponseFactory::ok([], "Removido com sucesso");
    }


}
