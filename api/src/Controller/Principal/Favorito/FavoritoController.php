<?php

namespace App\Controller\Principal\Favorito;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use FOS\RestBundle\Controller\Annotations as FOSRest;
use Swagger\Annotations as SWG;
use App\Factory\ResponseFactory;
use App\Controller\Principal\Base\GenericController;
use FOS\RestBundle\Request\ParamFetcher;
use Symfony\Component\HttpFoundation\Request;

use App\Facade\Principal\FavoritoFacade;
/**
 *
 * @author        Marcelo Andre Naegeler
 * @Route("/api")
 */
class FavoritoController extends GenericController
{

    /**
     *
     * @var \App\Facade\Principal\FavoritoFacade $favoritoFacade
     */
    private $favoritoFacade;

    /**
     * {@inheritdoc}
     */
    protected function constroiFacades()
    {
        // Para criar o LogFacade do GenericController
        parent::constroiFacades();
        $this->favoritoFacade = new FavoritoFacade(self::getManagerRegistry());
    }

    /**
     *
     * @SWG\Get(
     *     path="/api/favorito/listar",
     *     summary="Listar os modulos favoritados do usuário para a franquia",
     *     description="Lista os modulos favoritados do usuário para a franquia",
     *     tags={"Favorito"},
     *     consumes={"application/x-www-form-urlencoded"},
     *     produces={"application/json"},
     *     @SWG\Response(
     *         response="200",
     *         description="Retorna os favoritos"
     *     ),
     *     @SWG\Response(
     *         response="400",
     *         description="Ocorreu algum erro no servidor",
     *     )
     * )
     *
     * @FOSRest\QueryParam(name="usuario_id",    strict=true, allowBlank=false, description="Usuário para buscar os favoritos", requirements="\d+")
     * @FOSRest\QueryParam(name="franqueada_id", strict=true, allowBlank=false, description="Franqueada para buscar os favoritos", requirements="\d+")
     *
     * @FOSRest\Get("/favorito/listar")
     *
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function listar (ParamFetcher $paramFetcher)
    {
        $usuario    = $paramFetcher->get('usuario');
        $franqueada = $paramFetcher->get('franqueada');
        $mensagem   = "";
        $favoritos  = $this->favoritoFacade->listar($mensagem, $usuario, $franqueada);

        if (empty($mensagem) === true) {
            return ResponseFactory::ok($favoritos);
        }

        return ResponseFactory::badRequest([], $mensagem);
    }


    /**
     *
     * @SWG\Post(
     *     path="/api/favorito/criar",
     *     summary="Cria um favorito do modulo para o usuário e franquia fornecidos",
     *     description="Cria um favorito do modulo para o usuário e franquia fornecidos",
     *     tags={"Favorito"},
     *     consumes={"application/x-www-form-urlencoded"},
     *     produces={"application/json"},
     *     @SWG\Response(
     *         response="201",
     *         description="Retorna criado com sucesso"
     *     ),
     *     @SWG\Response(
     *         response="202",
     *         description="Ocorreu algum erro no servidor",
     *     )
     * )
     *
     * @FOSRest\RequestParam(name="franqueada", strict=true, requirements="\d+", allowBlank=false, description="ID da Franqueada")
     * @FOSRest\RequestParam(name="modulo",     strict=true, requirements="\d+", allowBlank=false, description="ID do Modulo")
     *
     * @FOSRest\Post("/favorito/criar")
     *
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function criar (ParamFetcher $paramFetcher, Request $request)
    {
        $parametros = $paramFetcher->all();
        $mensagem   = "";
        $usuarioId  = $request->headers->get('Authorization-User-ID');

        $parametros['usuario'] = $usuarioId;

        $favorito = $this->favoritoFacade->criar($mensagem, $parametros);

        if (empty($mensagem) === true) {
            return ResponseFactory::ok($favorito->getId());
        }

        return ResponseFactory::badRequest([], $mensagem);
    }

    /**
     *
     * @SWG\Delete(
     *     path="/api/favorito/remover/{id}",
     *     summary="Remove um modulo favorito do usuário para a franquia",
     *     description="Remove um modulo favorito do usuário para a franquia",
     *     tags={"Favorito"},
     *     consumes={"application/x-www-form-urlencoded"},
     *     produces={"application/json"},
     *     @SWG\Response(
     *         response="204",
     *         description="Removido com sucesso"
     *     ),
     *     @SWG\Response(
     *         response="400",
     *         description="Ocorreu algum erro no servidor",
     *     )
     * )
     *
     * @FOSRest\Delete("/favorito/remover/{id}")
     *
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function remover ($id)
    {
        $mensagem = "";
        $removido = $this->favoritoFacade->remover($mensagem, $id);

        if ($removido === true) {
            return ResponseFactory::noContent([]);
        }

        return ResponseFactory::badRequest([], $mensagem);
    }


}
