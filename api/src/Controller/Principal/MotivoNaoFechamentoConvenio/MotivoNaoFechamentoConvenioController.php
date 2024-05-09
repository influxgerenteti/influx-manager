<?php

namespace App\Controller\Principal\MotivoNaoFechamentoConvenio;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use FOS\RestBundle\Controller\Annotations as FOSRest;
use Swagger\Annotations as SWG;
use App\Factory\ResponseFactory;
use App\Controller\Principal\Base\GenericController;
use FOS\RestBundle\Request\ParamFetcher;
use App\Facade\Principal\MotivoNaoFechamentoConvenioFacade;

/**
 *
 * @author        Luiz A Costa
 * @Route("/api")
 */
class MotivoNaoFechamentoConvenioController extends GenericController
{

    /**
     *
     * @var \App\Facade\Principal\MotivoNaoFechamentoConvenioFacade
     */
    private $motivoNaoFechamentoConvenioFacade;

    /**
     * {@inheritdoc}
     */
    protected function constroiFacades()
    {
        // Para criar o LogFacade do GenericController
        parent::constroiFacades();
        $this->motivoNaoFechamentoConvenioFacade = new MotivoNaoFechamentoConvenioFacade(self::getManagerRegistry());
    }

    /**
     *
     * @SWG\Get(
     *     path="/api/motivo_nao_fechamento_convenio/listar",
     *     summary="Listar motivo_nao_fechamento_convenio",
     *     description="Lista as motivo_nao_fechamento_convenio do banco",
     *     produces={"application/json"},
     * @SWG\Response(
     *         response="200",
     *         description="Retorna os motivo_nao_fechamento_convenio"
     *     ),
     * @SWG\Response(
     *         response="400",
     *         description="Ocorreu algum erro no servidor",
     *     )
     * )
     *
     * @FOSRest\QueryParam(name="pagina",    strict=false, allowBlank=false, default="1", description="Pagina para realizar o scroll", requirements="\d+")
     * @FOSRest\QueryParam(name="descricao", strict=false, nullable=true, allowBlank=true, description="Descricao do banco")
     * @FOSRest\QueryParam(name="order",     strict=false, nullable=true,  description="Coluna de ordenação")
     * @FOSRest\QueryParam(name="direcao",   strict=false, nullable=true,  description="ASC|DESC")
     *
     * @FOSRest\Get("/motivo_nao_fechamento_convenio/listar")
     *
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function lista(ParamFetcher $request)
    {
        $parametros = $request->all();
        $mensagem   = "";
        $resultados = $this->motivoNaoFechamentoConvenioFacade->listar($parametros);
        if ($resultados === false) {
            return ResponseFactory::badRequest([], $mensagem);
        }

        return ResponseFactory::ok($resultados);
    }

    /**
     *
     * @SWG\Get(
     *     path="/api/motivo_nao_fechamento_convenio/{id}",
     *     summary="Buscar a motivo_nao_fechamento_convenio",
     *     description="Busca a motivo_nao_fechamento_convenio através da ID",
     *     produces={"application/json"},
     * @SWG\Response(
     *         response="200",
     *         description="Retorna a motivo_nao_fechamento_convenio"
     *     ),
     * @SWG\Response(
     *         response="404",
     *         description="Ocorreu algum erro no servidor",
     *     )
     * )
     *
     * @FOSRest\Get("/motivo_nao_fechamento_convenio/{id}")
     *
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function buscar($id)
    {
        $mensagemErro = "";
        $objetoORM    = $this->motivoNaoFechamentoConvenioFacade->buscarPorId($mensagemErro, $id);
        if (is_null($objetoORM) === true) {
            return ResponseFactory::notFound([], $mensagemErro);
        }

        return ResponseFactory::ok($objetoORM);
    }

    /**
     *
     * @SWG\Post(
     *     path="/api/motivo_nao_fechamento_convenio/criar",
     *     summary="Cria uma motivo_nao_fechamento_convenio",
     *     description="Cria uma motivo_nao_fechamento_convenio no banco",
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
     * @FOSRest\RequestParam(name="descricao", strict=true, nullable=false, allowBlank=false, description="Descricao do Segmento")
     * @FOSRest\RequestParam(name="situacao",  strict=true, nullable=false, allowBlank=false, description="situacao", requirements="[A|I]", default="A")
     *
     * @FOSRest\Post("/motivo_nao_fechamento_convenio/criar")
     *
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function criar(ParamFetcher $request)
    {
        $parametros = $request->all();
        $mensagem   = "";
        $objetoORM  = $this->motivoNaoFechamentoConvenioFacade->criar($mensagem, $parametros);
        if ((is_null($objetoORM) === true) || (empty($mensagem) === false)) {
            return ResponseFactory::conflict(["parametros" => $parametros], $mensagem);
        }

        return ResponseFactory::created(["objetoORM" => $objetoORM], "Registro criado com sucesso!");
    }

    /**
     *
     * @SWG\Patch(
     *     path="/api/motivo_nao_fechamento_convenio/atualizar/{id}",
     *     summary="Atualiza um motivo_nao_fechamento_convenio",
     *     description="Atualiza um motivo_nao_fechamento_convenio no banco",
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
     * @FOSRest\RequestParam(name="descricao", strict=false, nullable=false, allowBlank=false, description="Descricao do Segmento")
     * @FOSRest\RequestParam(name="situacao",  strict=false, nullable=false, allowBlank=false, description="Situacao do banco", requirements="(A|I)", default="A")
     *
     * @FOSRest\Patch("/motivo_nao_fechamento_convenio/atualizar/{id}")
     *
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function atualizar($id, ParamFetcher $request)
    {
        $parametros = $request->all();
        $mensagem   = "";
        $retorno    = $this->motivoNaoFechamentoConvenioFacade->atualizar($mensagem, $id, $parametros);
        if ($retorno === false) {
            return ResponseFactory::badRequest(["parametros" => $parametros], $mensagem);
        }

        return ResponseFactory::noContent([]);
    }

    /**
     *
     * @SWG\Delete(
     *     path="/api/motivo_nao_fechamento_convenio/remover/{id}",
     *     summary="Remove uma motivo_nao_fechamento_convenio",
     *     description="Remove uma motivo_nao_fechamento_convenio no banco",
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
     * @FOSRest\Delete("/motivo_nao_fechamento_convenio/remover/{id}")
     *
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function excluir($id)
    {
        $mensagem = "";
        $retorno  = $this->motivoNaoFechamentoConvenioFacade->remover($mensagem, $id);
        if ($retorno === false) {
            return ResponseFactory::badRequest([], $mensagem);
        }

        return ResponseFactory::ok([], "Excluido com sucesso");
    }


}
