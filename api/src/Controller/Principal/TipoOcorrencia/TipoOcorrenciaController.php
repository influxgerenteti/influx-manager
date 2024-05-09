<?php

namespace App\Controller\Principal\TipoOcorrencia;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use FOS\RestBundle\Controller\Annotations as FOSRest;
use Swagger\Annotations as SWG;
use App\Factory\ResponseFactory;
use App\Controller\Principal\Base\GenericController;
use FOS\RestBundle\Request\ParamFetcher;
use App\Facade\Principal\TipoOcorrenciaFacade;

/**
 *
 * @author        Dayan Freitas
 * @Route("/api")
 */
class TipoOcorrenciaController extends GenericController
{

    /**
     *
     * @var \App\Facade\Principal\TipoOcorrenciaFacade $tipoOcorrenciaFacade
     */
    private $tipoOcorrenciaFacade;

    /**
     * {@inheritdoc}
     */
    protected function constroiFacades()
    {
        // Para criar o LogFacade do GenericController
        parent::constroiFacades();
        $this->tipoOcorrenciaFacade = new TipoOcorrenciaFacade(parent::getManagerRegistry());
    }

    /**
     *
     * @SWG\Get(
     *     path="/api/tipo_ocorrencia/listar",
     *     summary="Listar os tipo de ocorrencia",
     *     description="Lista os tipos de ocorrencias do banco",
     *     produces={"application/json"},
     * @SWG\Response(
     *         response="200",
     *         description="Retorna os tipos de ocorrencias"
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
     *
     * @FOSRest\Get("/tipo_ocorrencia/listar")
     *
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function lista(ParamFetcher $request)
    {
        $parametros = $request->all();
        $mensagem   = "";
        $resultados = $this->tipoOcorrenciaFacade->listar($parametros);
        if ($resultados === false) {
            return ResponseFactory::badRequest([], $mensagem);
        }

        return ResponseFactory::ok($resultados);
    }

    /**
     *
     * @SWG\Get(
     *     path="/api/tipo_ocorrencia/{id}",
     *     summary="Buscar a tipo_ocorrencia",
     *     description="Busca a tipo_ocorrencia através da ID",
     *     produces={"application/json"},
     * @SWG\Response(
     *         response="200",
     *         description="Retorna a tipo_ocorrencia"
     *     ),
     * @SWG\Response(
     *         response="404",
     *         description="Ocorreu algum erro no servidor",
     *     )
     * )
     *
     * @FOSRest\Get("/tipo_ocorrencia/{id}")
     *
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function buscar($id)
    {
        $mensagemErro = "";
        $objetoORM    = $this->tipoOcorrenciaFacade->buscarPorId($mensagemErro, $id);
        if (is_null($objetoORM) === true) {
            return ResponseFactory::notFound([], "OBJETO ORM não encontrada.");
        }

        return ResponseFactory::ok($objetoORM);
    }

    /**
     *
     * @SWG\Post(
     *     path="/api/tipo_ocorrencia/criar",
     *     summary="Cria uma tipo_ocorrencia",
     *     description="Cria uma tipo de ocorrencia no banco",
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
     * @FOSRest\RequestParam(name="descricao", strict=true, nullable=false, allowBlank=false, description="Descricao")
     * @FOSRest\RequestParam(name="tipo", strict=true, nullable=true, allowBlank=true, description="Tipo")
     * @FOSRest\RequestParam(name="tipo_pai", strict=true, nullable=true, allowBlank=true, description="TipoOcorrenciaPai")
     * @FOSRest\RequestParam(name="situacao", strict=true, nullable=true, allowBlank=true, description="Situacao")
     * @FOSRest\RequestParam(name="franqueada", strict=true, nullable=true, allowBlank=true, description="Franqueada")
     *
     * @FOSRest\Post("/tipo_ocorrencia/criar")
     *
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function criar(ParamFetcher $request)
    {
        $parametros = $request->all();
        $mensagem   = "";
        // return ResponseFactory::ok($parametros);
        $objetoORM  = $this->tipoOcorrenciaFacade->criar( $mensagem, $parametros);
        if ((is_null($objetoORM) === true) || (empty($mensagem) === false)) {
            return ResponseFactory::conflict(["parametros" => $parametros], $mensagem);
        }

        return ResponseFactory::created(["objetoORM" => $objetoORM->getId()], "Registro criado com sucesso!");
    }

    /**
     *
     * @SWG\Post(
     *     path="/api/tipo_ocorrencia/atualizar/{id}",
     *     summary="Atualiza um tipo_ocorrencia",
     *     description="Atualiza um tipo_ocorrencia no banco",
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
     * @FOSRest\RequestParam(name="franqueada",    strict=true, nullable=true, description="ID da franqueada", requirements="\d+")
     * @FOSRest\RequestParam(name="descricao", strict=true, nullable=true, allowBlank=false, description="Descricao")
     * @FOSRest\RequestParam(name="tipo", strict=true, nullable=true, allowBlank=true, description="Tipo")
     * @FOSRest\RequestParam(name="tipo_pai", strict=true, nullable=true, allowBlank=true, description="TipoOcorrenciaPai")
     * @FOSRest\RequestParam(name="situacao", strict=true, nullable=true, allowBlank=true, description="Situacao")
     *
     *
     * @FOSRest\Post("/tipo_ocorrencia/atualizar/{id}")
     *
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function atualizar($id, ParamFetcher $request)
    {
        $parametros = $request->all();
        $mensagem   = "";
        $retorno    = $this->tipoOcorrenciaFacade->atualizar($mensagem, $id, $parametros);
        if ($retorno === false) {
            return ResponseFactory::badRequest(["parametros" => $parametros], $mensagem);
        } 
            
        if($retorno) {
            self::getEntityManager()->flush();
            return ResponseFactory::noContent([]);
        } else {
            return ResponseFactory::badRequest([], $mensagem);
        }
        
    }

    /**
     *
     * @SWG\Delete(
     *     path="/api/tipo_ocorrencia/remover/{id}",
     *     summary="Remove uma tipo_ocorrencia",
     *     description="Remove uma tipo_ocorrencia no banco",
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
     * @FOSRest\Delete("/tipo_ocorrencia/remover/{id}")
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
