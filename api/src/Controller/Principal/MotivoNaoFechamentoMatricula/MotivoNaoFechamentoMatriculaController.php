<?php

namespace App\Controller\Principal\MotivoNaoFechamentoMatricula;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use FOS\RestBundle\Controller\Annotations as FOSRest;
use Swagger\Annotations as SWG;
use App\Factory\ResponseFactory;
use App\Controller\Principal\Base\GenericController;
use FOS\RestBundle\Request\ParamFetcher;
use App\Facade\Principal\MotivoNaoFechamentoMatriculaFacade;

/**
 *
 * @author        Luiz A Costa
 * @Route("/api")
 */
class MotivoNaoFechamentoMatriculaController extends GenericController
{

    /**
     *
     * @var \App\Facade\Principal\MotivoNaoFechamentoMatriculaFacade
     */
    private $motivoNaoFechamentoMatriculaFacade;

    /**
     * {@inheritdoc}
     */
    protected function constroiFacades()
    {
        // Para criar o LogFacade do GenericController
        parent::constroiFacades();
        $this->motivoNaoFechamentoMatriculaFacade = new MotivoNaoFechamentoMatriculaFacade(self::getManagerRegistry());
    }

    /**
     *
     * @SWG\Get(
     *     path="/api/motivo_nao_fechamento_matricula/listar",
     *     summary="Listar motivo_nao_fechamento_matricula",
     *     description="Lista as motivo_nao_fechamento_matricula do banco",
     *     tags={"Motivo Nao Fechamento Matricula"},
     *     produces={"application/json"},
     * @SWG\Response(
     *         response="200",
     *         description="Retorna os motivo_nao_fechamento_matricula"
     *     ),
     * @SWG\Response(
     *         response="400",
     *         description="Ocorreu algum erro no servidor",
     *     )
     * )
     *
     * @FOSRest\QueryParam(name="pagina", strict=false, allowBlank=false, default="1", description="Pagina para realizar o scroll", requirements="\d+")
     *
     * @FOSRest\Get("/motivo_nao_fechamento_matricula/listar")
     * @FOSRest\QueryParam(name="order",                       strict=false, nullable=true,  description="Coluna de ordenação")
     * @FOSRest\QueryParam(name="direcao",                     strict=false, nullable=true,  description="ASC|DESC")
     *
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function lista(ParamFetcher $request)
    {
        $parametros = $request->all();
        $mensagem   = "";
        $resultados = $this->motivoNaoFechamentoMatriculaFacade->listar($parametros);
        if ($resultados === false) {
            return ResponseFactory::badRequest([], $mensagem);
        }

        return ResponseFactory::ok($resultados);
    }

    /**
     *
     * @SWG\Get(
     *     path="/api/motivo_nao_fechamento_matricula/{id}",
     *     summary="Buscar a motivo_nao_fechamento_matricula",
     *     description="Busca a motivo_nao_fechamento_matricula através da ID",
     *     tags={"Motivo Nao Fechamento Matricula"},
     *     produces={"application/json"},
     * @SWG\Response(
     *         response="200",
     *         description="Retorna a motivo_nao_fechamento_matricula"
     *     ),
     * @SWG\Response(
     *         response="404",
     *         description="Ocorreu algum erro no servidor",
     *     )
     * )
     *
     * @FOSRest\Get("/motivo_nao_fechamento_matricula/{id}")
     *
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function buscar($id)
    {
        $mensagem  = "";
        $objetoORM = $this->motivoNaoFechamentoMatriculaFacade->buscarPorId($mensagem, $id);
        if (is_null($objetoORM) === true) {
            return ResponseFactory::notFound([], $mensagem);
        }

        return ResponseFactory::ok($objetoORM);
    }

    /**
     *
     * @SWG\Post(
     *     path="/api/motivo_nao_fechamento_matricula/criar",
     *     summary="Cria uma motivo_nao_fechamento_matricula",
     *     description="Cria uma motivo_nao_fechamento_matricula no banco",
     *     tags={"Motivo Nao Fechamento Matricula"},
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
     * @FOSRest\RequestParam(name="descricao", strict=true, nullable=false, allowBlank=true, description="Descricao do banco")
     *
     * @FOSRest\Post("/motivo_nao_fechamento_matricula/criar")
     *
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function criar(ParamFetcher $request)
    {
        $parametros = $request->all();
        $mensagem   = "";
        $objetoORM  = $this->motivoNaoFechamentoMatriculaFacade->criar($mensagem, $parametros);
        if ((is_null($objetoORM) === true) || (empty($mensagem) === false)) {
            return ResponseFactory::conflict(["parametros" => $parametros], $mensagem);
        }

        return ResponseFactory::created(["objetoORM" => $objetoORM->getId()], "Registro criado com sucesso!");
    }

    /**
     *
     * @SWG\Patch(
     *     path="/api/motivo_nao_fechamento_matricula/atualizar/{id}",
     *     summary="Atualiza um motivo_nao_fechamento_matricula",
     *     description="Atualiza um motivo_nao_fechamento_matricula no banco",
     *     tags={"Motivo Nao Fechamento Matricula"},
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
     * @FOSRest\RequestParam(name="descricao", strict=true, nullable=true, allowBlank=true, description="Descricao")
     * @FOSRest\RequestParam(name="situacao",  strict=true, nullable=true, allowBlank=true, description="Situacao", requirements="[A|I]")
     *
     * @FOSRest\Patch("/motivo_nao_fechamento_matricula/atualizar/{id}")
     *
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function atualizar($id, ParamFetcher $request)
    {
        $parametros = $request->all();
        $mensagem   = "";
        $retorno    = $this->motivoNaoFechamentoMatriculaFacade->atualizar($mensagem, $id, $parametros);
        if ($retorno === false) {
            return ResponseFactory::badRequest(["parametros" => $parametros], $mensagem);
        }

        return ResponseFactory::noContent([]);
    }

    /**
     *
     * @SWG\Delete(
     *     path="/api/motivo_nao_fechamento_matricula/remover/{id}",
     *     summary="Remove uma motivo_nao_fechamento_matricula",
     *     description="Remove uma motivo_nao_fechamento_matricula no banco",
     *     tags={"Motivo Nao Fechamento Matricula"},
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
     * @FOSRest\Delete("/motivo_nao_fechamento_matricula/remover/{id}")
     *
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function excluir($id)
    {
        $mensagem = "";
        $retorno  = $this->motivoNaoFechamentoMatriculaFacade->remover($mensagem, $id);
        if ($retorno === false) {
            return ResponseFactory::badRequest([], $mensagem);
        }

        return ResponseFactory::ok([], "Excluido com sucesso");
    }


}
