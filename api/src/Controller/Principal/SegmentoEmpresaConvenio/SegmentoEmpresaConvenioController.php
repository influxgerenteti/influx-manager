<?php

namespace App\Controller\Principal\SegmentoEmpresaConvenio;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use FOS\RestBundle\Controller\Annotations as FOSRest;
use Swagger\Annotations as SWG;
use App\Factory\ResponseFactory;
use App\Controller\Principal\Base\GenericController;
use FOS\RestBundle\Request\ParamFetcher;
use App\Facade\Principal\SegmentoEmpresaConvenioFacade;

/**
 *
 * @author        Luiz A Costa
 * @Route("/api")
 */
class SegmentoEmpresaConvenioController extends GenericController
{

    /**
     *
     * @var \App\Facade\Principal\SegmentoEmpresaConvenioFacade
     */
    private $segmentoEmpresaConvenioFacade;


    /**
     * {@inheritdoc}
     */
    protected function constroiFacades()
    {
        // Para criar o LogFacade do GenericController
        parent::constroiFacades();
        $this->segmentoEmpresaConvenioFacade = new SegmentoEmpresaConvenioFacade(self::getManagerRegistry());

    }

    /**
     *
     * @SWG\Get(
     *     path="/api/segmento_empresa_convenio/listar",
     *     summary="Listar segmento_empresa_convenio",
     *     description="Lista as segmento_empresa_convenio do banco",
     *     produces={"application/json"},
     * @SWG\Response(
     *         response="200",
     *         description="Retorna os segmento_empresa_convenio"
     *     ),
     * @SWG\Response(
     *         response="400",
     *         description="Ocorreu algum erro no servidor",
     *     )
     * )
     *
     * @FOSRest\QueryParam(name="pagina",    strict=false, allowBlank=false, default="1", description="Pagina para realizar o scroll", requirements="\d+")
     * @FOSRest\QueryParam(name="order",     strict=false, nullable=true,  description="Coluna de ordenação")
     * @FOSRest\QueryParam(name="direcao",   strict=false, nullable=true,  description="ASC|DESC")
     * @FOSRest\QueryParam(name="descricao", strict=false, nullable=true, allowBlank=true, description="Descricao do banco")
     *
     * @FOSRest\Get("/segmento_empresa_convenio/listar")
     *
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function lista(ParamFetcher $request)
    {
        $parametros = $request->all();
        $mensagem   = "";
        $resultados = $this->segmentoEmpresaConvenioFacade->listar($parametros);
        if ($resultados === false) {
            return ResponseFactory::badRequest([], $mensagem);
        }

        return ResponseFactory::ok($resultados);
    }

    /**
     *
     * @SWG\Get(
     *     path="/api/segmento_empresa_convenio/{id}",
     *     summary="Buscar a segmento_empresa_convenio",
     *     description="Busca a segmento_empresa_convenio através da ID",
     *     produces={"application/json"},
     * @SWG\Response(
     *         response="200",
     *         description="Retorna a segmento_empresa_convenio"
     *     ),
     * @SWG\Response(
     *         response="404",
     *         description="Ocorreu algum erro no servidor",
     *     )
     * )
     *
     * @FOSRest\Get("/segmento_empresa_convenio/{id}")
     *
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function buscar($id)
    {
        $mensagemErro = "";
        $objetoORM    = $this->segmentoEmpresaConvenioFacade->buscarPorId($mensagemErro, $id);
        if (is_null($objetoORM) === true) {
            return ResponseFactory::notFound([], $mensagemErro);
        }

        return ResponseFactory::ok($objetoORM);
    }

    /**
     *
     * @SWG\Post(
     *     path="/api/segmento_empresa_convenio/criar",
     *     summary="Cria uma segmento_empresa_convenio",
     *     description="Cria uma segmento_empresa_convenio no banco",
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
     *
     * @FOSRest\Post("/segmento_empresa_convenio/criar")
     *
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function criar(ParamFetcher $request)
    {
        $parametros = $request->all();
        $mensagem   = "";
        $objetoORM  = $this->segmentoEmpresaConvenioFacade->criar($mensagem, $parametros);
        if ((is_null($objetoORM) === true) || (empty($mensagem) === false)) {
            return ResponseFactory::conflict(["parametros" => $parametros], $mensagem);
        }

        return ResponseFactory::created(["objetoORM" => $objetoORM->getId()], "Registro criado com sucesso!");
    }

    /**
     *
     * @SWG\Patch(
     *     path="/api/segmento_empresa_convenio/atualizar/{id}",
     *     summary="Atualiza um segmento_empresa_convenio",
     *     description="Atualiza um segmento_empresa_convenio no banco",
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
     * @FOSRest\Patch("/segmento_empresa_convenio/atualizar/{id}")
     *
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function atualizar($id, ParamFetcher $request)
    {
        $parametros = $request->all();
        $mensagem   = "";
        $retorno    = $this->segmentoEmpresaConvenioFacade->atualizar($mensagem, $id, $parametros);
        if ($retorno === false) {
            return ResponseFactory::badRequest(["parametros" => $parametros], $mensagem);
        }

        return ResponseFactory::noContent([]);
    }

    /**
     *
     * @SWG\Delete(
     *     path="/api/segmento_empresa_convenio/remover/{id}",
     *     summary="Remove uma segmento_empresa_convenio",
     *     description="Remove uma segmento_empresa_convenio no banco",
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
     * @FOSRest\Delete("/segmento_empresa_convenio/remover/{id}")
     *
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function excluir($id)
    {
        $mensagem = "";
        $retorno  = $this->segmentoEmpresaConvenioFacade->remover($mensagem, $id);
        if ($retorno === false) {
            return ResponseFactory::badRequest([], $mensagem);
        }

        return ResponseFactory::ok([], "Excluido com sucesso");
    }


}
