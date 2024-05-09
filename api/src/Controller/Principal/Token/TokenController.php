<?php

namespace App\Controller\Principal\Token;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Swagger\Annotations as SWG;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use FOS\RestBundle\Controller\Annotations as FOSRest;
use App\Factory\ResponseFactory;
use App\Controller\Principal\Base\GenericController;
use FOS\RestBundle\Request\ParamFetcher;

use App\Facade\Principal\TokenFacade;
use App\Facade\Principal\UsuarioFacade;
use Symfony\Component\HttpFoundation\Request;

/**
 *
 * @author        Marcelo Andre Naegeler
 * @Route("/api")
 */
class TokenController extends GenericController
{

    /**
     *
     * @var \App\Facade\Principal\TokenFacade $tokenFacade
     */
    private $tokenFacade;

    /**
     *
     * @var \App\Facade\Principal\UsuarioFacade $usuarioFacade
     */
    private $usuarioFacade;

    /**
     * {@inheritdoc}
     */
    protected function constroiFacades()
    {
        parent::constroiFacades();
        $this->usuarioFacade = new UsuarioFacade(self::getManagerRegistry());
        $this->tokenFacade   = new TokenFacade(self::getManagerRegistry());
    }

    /**
     *
     * @SWG\Get(
     *      path="/api/token/buscar",
     *      summary="Buscar um token",
     *      description="Busca um token através da hash.",
     *      tags={"Token"},
     *      consumes={"application/x-www-form-urlencoded"},
     *      produces={"application/json"},
     * @SWG\Response(
     *          response="200",
     *          description="Retorna os dados do Token"
     *      ),
     * @SWG\Response(
     *          response="404",
     *          description="Token não encontrado",
     *      )
     * )
     *
     * @FOSRest\QueryParam(name="token", strict=true, allowBlank=false, description="Hash do token para consulta", requirements="[\w\s]+")
     *
     * @FOSRest\Get("/token/buscar")
     *
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function buscar (ParamFetcher $paramFetcher)
    {
        $parametro = $paramFetcher->get('token');
        $mensagem  = "";
        $token     = $this->tokenFacade->buscarToken($mensagem, $parametro, true);

        if (empty($mensagem) === false) {
            return ResponseFactory::notFound(['mensagem' => $mensagem]);
        }

        return ResponseFactory::ok(['token' => $token]);
    }

    /**
     *
     * @SWG\Post(
     *      path="/api/token/setar-senha",
     *      summary="Atualiza a senha de usuário",
     *      description="Atualiza a senha do usuário vinculado ao token.",
     *      tags={"Token"},
     *      consumes={"application/x-www-form-urlencoded"},
     *      produces={"application/json"},
     * @SWG\Response(
     *          response="200",
     *          description="Senha atualizada com sucesso"
     *      ),
     * @SWG\Response(
     *          response="400",
     *          description="Ocorreu algum erro no servidor",
     *      )
     * )
     *
     * @FOSRest\RequestParam(name="token",          strict=true, allowBlank=false, description="Token de Acesso")
     * @FOSRest\RequestParam(name="senha",          strict=true, allowBlank=false, description="Senha de Acesso")
     * @FOSRest\RequestParam(name="confirmarSenha", strict=true, allowBlank=false, description="Confirmar Senha de Acesso")
     *
     * @FOSRest\Post("/token/setar-senha")
     *
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function setarSenha (ParamFetcher $paramFetcher, Request $requestHeader)
    {
        $parametros = $paramFetcher->all();
        $mensagem   = "";
        $token      = $this->tokenFacade->buscarToken($mensagem, $parametros['token']);
        $usuario    = $token->getUsuario();

        // if ($usuario->getEmail() !== $usuario->getFranqueadaPadrao()->getEmailDirecao()) {
        //     return ResponseFactory::badRequest([], "Seu e-mail não está cadastrado como diretor da franqueada do usuario.");
        // }

        $retorno = $this->usuarioFacade->atualizarSenhaPrimeiroAcesso($mensagem, $usuario->getId(), $parametros);

        if (empty($mensagem) === false) {
            return ResponseFactory::badRequest([], $mensagem);
        }

        $this->tokenFacade->removerToken($mensagem, $token);
        if (empty($mensagem) === false) {
            return ResponseFactory::badRequest([], $mensagem);
        }

        return ResponseFactory::ok([], "Senha atualizada com sucesso!");
    }


}
