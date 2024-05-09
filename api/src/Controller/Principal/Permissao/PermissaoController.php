<?php

namespace App\Controller\Principal\Permissao;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use FOS\RestBundle\Controller\Annotations as FOSRest;
use Swagger\Annotations as SWG;
use App\Factory\ResponseFactory;
use App\Controller\Principal\Base\GenericController;
use FOS\RestBundle\Request\ParamFetcher;
use App\Facade\Principal\PermissaoFacade;
use App\Helper\ConstanteParametros;
use App\Helper\RedisHelper;
use Symfony\Component\HttpFoundation\Request;

/**
 *
 * @author        Luiz A Costa
 * @Route("/api")
 */
class PermissaoController extends GenericController
{

    /**
     *
     * @var \App\Facade\Principal\PermissaoFacade
     */
    private $permissaoFacade;

    /**
     * {@inheritdoc}
     */
    protected function constroiFacades()
    {
        // Para criar o LogFacade do GenericController
        parent::constroiFacades();
        $this->permissaoFacade = new PermissaoFacade(self::getManagerRegistry());
    }

    /**
     *
     * @SWG\Get(
     *     path="/api/permissao/listar",
     *     summary="Listar permissao",
     *     description="Lista as permissao do banco",
     *     produces={"application/json"},
     * @SWG\Response(
     *         response="200",
     *         description="Retorna os permissao"
     *     ),
     * @SWG\Response(
     *         response="400",
     *         description="Ocorreu algum erro no servidor",
     *     )
     * )
     *
     * @FOSRest\QueryParam(name="papel",   strict=false, nullable=true, description="Papel", requirements="\d+")
     * @FOSRest\QueryParam(name="usuario", strict=false, nullable=true, description="Usuário", requirements="\d+")
     * @FOSRest\QueryParam(name="modulo",  strict=false, nullable=true, description="Módulo", requirements="\d+")
     *
     * @FOSRest\Get("/permissao/listar")
     *
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function lista(ParamFetcher $request)
    {
        $parametros = $request->all();
        $mensagem   = "";
        $resultados = $this->permissaoFacade->listar($parametros);
        if ($resultados === false) {
            return ResponseFactory::badRequest([], $mensagem);
        }

        return ResponseFactory::ok($resultados);
    }

    /**
     *
     * @SWG\Get(
     *     path="/api/permissao/modulo",
     *     summary="Buscar permissões do módulo atual passado pelo header URLModulo",
     *     description="Buscar permissões do módulo atual passado pelo header URLModulo",
     *     produces={"application/json"},
     * @SWG\Response(
     *         response="200",
     *         description="Retorna permissões"
     *     ),
     * @SWG\Response(
     *         response="404",
     *         description="Ocorreu algum erro no servidor",
     *     )
     * )
     *
     * @FOSRest\QueryParam(name="URLModulo", strict=false, nullable=true, description="Módulo")
     *
     * @FOSRest\Get("/permissao/modulo")
     *
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function buscarPorModulo(ParamFetcher $paramFetcher, Request $request)
    {
        $mensagemErro = "";
        $URLModulo    = $paramFetcher->get('URLModulo');

        if (is_null($URLModulo) === true) {
            $URLModulo = $request->headers->get('URLModulo');
        }

        $usuarioID = $request->headers->get('Authorization-User-ID');
        $objetoORM = $this->permissaoFacade->buscarPorModulo($mensagemErro, $URLModulo, $usuarioID);
        if (is_null($objetoORM) === true) {
            return ResponseFactory::notFound([], $mensagemErro);
        }

        return ResponseFactory::ok($objetoORM);
    }

    /**
     *
     * @SWG\Get(
     *     path="/api/permissao/{id}",
     *     summary="Buscar a permissao",
     *     description="Busca a permissao através da ID do papel",
     *     produces={"application/json"},
     * @SWG\Response(
     *         response="200",
     *         description="Retorna a cheque"
     *     ),
     * @SWG\Response(
     *         response="404",
     *         description="Ocorreu algum erro no servidor",
     *     )
     * )
     *
     * @FOSRest\Get("/permissao/{id}")
     *
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function buscar($id)
    {
        $mensagemErro = "";
        $objetoORM    = $this->permissaoFacade->buscarPorPapel($mensagemErro, $id);
        if (is_null($objetoORM) === true) {
            return ResponseFactory::notFound([], $mensagemErro);
        }

        return ResponseFactory::ok($objetoORM);
    }

    /**
     *
     * @SWG\Get(
     *     path="/api/permissao/usuario/{id}",
     *     summary="Buscar a permissao",
     *     description="Busca a permissao através da ID do usuario",
     *     produces={"application/json"},
     * @SWG\Response(
     *         response="200",
     *         description="Retorna a usuario"
     *     ),
     * @SWG\Response(
     *         response="404",
     *         description="Ocorreu algum erro no servidor",
     *     )
     * )
     *
     * @FOSRest\Get("/permissao/usuario/{id}")
     *
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function buscarPorUsuario($id)
    {
        $mensagemErro = "";
        $objetoORM    = $this->permissaoFacade->buscarPorUsuario($mensagemErro, $id);
        if (is_null($objetoORM) === true) {
            return ResponseFactory::notFound([], $mensagemErro);
        }

        return ResponseFactory::ok($objetoORM);
    }

    /**
     *
     * @SWG\Patch(
     *     path="/api/permissao/atualizar",
     *     summary="Atualiza permissões de usuários ou papeis",
     *     description="Atualiza permissões de usuários ou papeis",
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
     * @FOSRest\RequestParam(name="modulo",  strict=true, nullable=true, allowBlank=true, description="Módulo")
     * @FOSRest\RequestParam(name="papel",   strict=true, nullable=true, allowBlank=true, description="Papel")
     * @FOSRest\RequestParam(name="usuario", strict=true, nullable=true, allowBlank=true, description="Usuário")
     * @FOSRest\RequestParam(name="dados",   strict=true, nullable=true, allowBlank=true, description="Array de dados de permissao", map=true)
     *
     * @FOSRest\Patch("/permissao/atualizar")
     *
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function atualizar (ParamFetcher $request)
    {
        $parametros = $request->all();
        $mensagem   = "";
        $retorno    = false;

        //limpar cache
        RedisHelper::clearCache();

        if (is_null($parametros[ConstanteParametros::CHAVE_USUARIO]) === false) {
            $retorno = $this->permissaoFacade->atualizarUsuarioModulos($mensagem, $parametros);
        }

        if (is_null($parametros[ConstanteParametros::CHAVE_PAPEL]) === false) {
            $retorno = $this->permissaoFacade->atualizarPapelModulos($mensagem, $parametros);
        }

        if ($retorno === false) {
            return ResponseFactory::badRequest(["parametros" => $parametros], $mensagem);
        }

        return ResponseFactory::noContent([]);
    }


}
