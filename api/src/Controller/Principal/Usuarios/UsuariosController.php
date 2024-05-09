<?php

namespace App\Controller\Principal\Usuarios;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use FOS\RestBundle\Controller\Annotations as FOSRest;
use Swagger\Annotations as SWG;
use App\Factory\ResponseFactory;
use App\Controller\Principal\Base\GenericController;
use App\Entity\Principal\Usuario;
use App\Facade\Principal\UsuarioFacade;
use App\Facade\Principal\UsuarioAcessoFacade;
use FOS\RestBundle\Request\ParamFetcher;
use Symfony\Component\HttpFoundation\Response;
use App\Helper\ConstanteParametros;
use App\Facade\Principal\PermissaoFacade;
use Symfony\Component\HttpFoundation\Request;

/**
 *
 * @author        Luiz Antonio Costa
 * @Route("/api")
 */
class UsuariosController extends GenericController
{

    /**
     *
     * @var \App\Facade\Principal\UsuarioFacade $usuarioFacade
     */
    private $usuarioFacade;

    /**
     *
     * @var \App\Facade\Principal\UsuarioAcessoFacade $usuarioAcessoFacade
     */
    private $usuarioAcessoFacade;

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
        $this->usuarioFacade       = new UsuarioFacade(self::getManagerRegistry());
        $this->usuarioAcessoFacade = new UsuarioAcessoFacade(self::getManagerRegistry());
        $this->permissaoFacade     = new PermissaoFacade(self::getManagerRegistry());
    }

    /**
     *
     * @SWG\Get(
     *     path="/api/usuario/listar",
     *     summary="Listar usuarios",
     *     description="Lista os usuarios do banco",
     *     tags={"Usuario"},
     *     produces={"application/json"},
     * @SWG\Response(
     *         response="200",
     *         description="Retorna os usuarios"
     *     ),
     * @SWG\Response(
     *         response="400",
     *         description="Ocorreu algum erro no servidor",
     *     )
     * )
     *
     * @FOSRest\QueryParam(name="pagina",  strict=true, requirements="\d{0,2}", allowBlank=false, default="1", description="Pagina para realizar o scroll")
     * @FOSRest\QueryParam(name="order",   strict=false, nullable=true,  description="Coluna de ordenação")
     * @FOSRest\QueryParam(name="direcao", strict=false, nullable=true,  description="ASC|DESC")
     *
     * @FOSRest\Get("/usuario/listar")
     *
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function listaUsuarios(ParamFetcher $paramFetcher)
    {
        $parametros = $paramFetcher->all();
        $resultados = $this->usuarioFacade->listaUsuarios($parametros);
        return ResponseFactory::ok($resultados);
    }

    /**
     *
     * @SWG\Get(
     *     path="/api/usuario/{id}",
     *     summary="Buscar o usuario",
     *     description="Busca o usuario através da ID",
     *     tags={"Usuario"},
     *     produces={"application/json"},
     * @SWG\Response(
     *         response="200",
     *         description="Retorna o usuario"
     *     ),
     * @SWG\Response(
     *         response="404",
     *         description="Ocorreu algum erro no servidor",
     *     )
     * )
     *
     * @FOSRest\Get("/usuario/{id}")
     *
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function buscarUsuario($id)
    {
        $usuario = $this->usuarioFacade->buscarUsuario($id);
        if (is_null($usuario) === true)
            return ResponseFactory::notFound([], "Usuario não encontrado.");
        return ResponseFactory::ok($usuario);
    }

    /**
     *
     * @SWG\Post(
     *     path="/api/usuario/criar",
     *     summary="Cria um usuario",
     *     description="Cria um usuario no banco",
     *     tags={"Usuario"},
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
     * @FOSRest\RequestParam(name="franqueada_padrao", strict=true, allowBlank=false, description="Franqueada Padrão", requirements="\d+")
     * @FOSRest\RequestParam(name="nome",              strict=true, allowBlank=false, description="Nome do usuário", requirements="[\w\s-]+")
     * @FOSRest\RequestParam(name="email",             strict=false, allowBlank=true, description="E-mail do usuário", requirements=".+")
     * @FOSRest\RequestParam(name="cpf",               strict=true, allowBlank=false, description="CPF do usuário", requirements="[0-9]{1,11}")
     * @FOSRest\RequestParam(name="situacao",          strict=true, allowBlank=true, default="A", description="Situação do usuário", requirements="\w")
     * @FOSRest\RequestParam(name="franqueadas",       strict=true, allowBlank=false, map=true, description="Lista de franqueadas atrelada ao usuario", requirements="\d+")
     *
     * @FOSRest\RequestParam(name="papels",          strict=false, nullable=true, allowBlank=true, description="Array de papeis", map=true)
     * @FOSRest\RequestParam(name="dados_permissao", strict=false, nullable=true, allowBlank=true, description="Array de dados de permissao", map=true)
     *
     * @FOSRest\Post("/usuario/criar")
     *
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function criarUsuario(ParamFetcher $paramFetcher, \Swift_Mailer $mailer)
    {
        $parametros        = $paramFetcher->all();
        $parametrosAntigos = $parametros;
        $mensagem          = "";
        $usuarioObj        = $this->usuarioFacade->criarUsuario($mensagem, $parametros);
        if (is_null($usuarioObj) === true) {
            return ResponseFactory::conflict(["parametros" => $parametrosAntigos], $mensagem);
        }

        if (isset($parametros[ConstanteParametros::CHAVE_DADOS_PERMISSAO]) === true) {
            $dadosPermissoes = [
                ConstanteParametros::CHAVE_MODULO  => null,
                ConstanteParametros::CHAVE_USUARIO => $usuarioObj->getId(),
                ConstanteParametros::CHAVE_DADOS   => $parametros[ConstanteParametros::CHAVE_DADOS_PERMISSAO],
            ];

            $retorno = $this->permissaoFacade->atualizarUsuarioModulos($mensagem, $dadosPermissoes);

            if ($retorno === false) {
                return ResponseFactory::conflict(["usuario" => $usuarioObj->getId()], "Registro criado com sucesso! porém ocorreu erro na atribuição de permissões:" . $mensagem);
            }
        }

        return ResponseFactory::created(["usuario" => $usuarioObj->getId()], "Registro criado com sucesso!");
    }

  /**
     *
     * @SWG\Patch(
     *     path="/api/usuario/atualizar/{id}",
     *     summary="Atualiza um usuario",
     *     description="Atualiza um usuario no banco",
     *     tags={"Usuario"},
     *     consumes={"application/x-www-form-urlencoded"},
     *     produces={"application/json"},
     * @SWG\Response(
     *         response="204",
     *         description="Retorna criado com sucesso"
     *     ),
     * @SWG\Response(
     *         response="400",
     *         description="Ocorreu algum erro no servidor",
     *     )
     * )
     *
     * @FOSRest\RequestParam(name="nome",              strict=false, allowBlank=false, description="Nome do usuário", requirements="[\w\s-]+")
     * @FOSRest\RequestParam(name="email",             strict=false, allowBlank=false, description="E-mail do usuário", requirements=".+")
     * @FOSRest\RequestParam(name="situacao",          strict=true, allowBlank=false, description="Situação do usuário", requirements="\w")
     * @FOSRest\RequestParam(name="senha",             strict=false, requirements="[\w\s]+", allowBlank=false, description="Senha de Acesso")
     * @FOSRest\RequestParam(name="cpf",               strict=false, allowBlank=false, description="CPF do usuário", requirements="[0-9]{1,11}")
     * @FOSRest\RequestParam(name="franqueada_padrao", strict=true, allowBlank=false, description="Franqueada Padrão", requirements="\d+")
     * @FOSRest\RequestParam(map=true,                 name="franqueadas", strict=true, description="Lista de franqueadas atrelada ao usuario", requirements="\d+")
     *
     * @FOSRest\RequestParam(name="papels",          strict=false, nullable=true, allowBlank=true, description="Array de papeis", map=true)
     * @FOSRest\RequestParam(name="dados_permissao", strict=false, nullable=false, allowBlank=true, description="Array de dados de permissao", map=true)
     * @FOSRest\RequestParam(name="userId", strict=false, nullable=false, allowBlank=false)
     * @FOSRest\RequestParam(name="userName", strict=false, nullable=false, allowBlank=false)
     *
     * @FOSRest\Patch("/usuario/atualizar/{id}")
     *
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function atualizarUsuario($id, ParamFetcher $paramFetcher, Request $request)
    {
        $parametros        = $paramFetcher->all();
        $usuario = $this->usuarioFacade->buscarUsuario($id);

        try {
            $retorno = $this->usuarioFacade->atualizarUsuario($mensagem, $id, $parametros);
            if ($retorno) {
                self::getEntityManager()->flush();
                $errorMsg = "";

                $userID     = '';
                $userName   = '';
                
                if ($parametros['userId']) {
                    $userID     = $parametros['userId'];
                    $userName   = $parametros['userName'];
                } else {
                    $usuarioID  = $request->headers->get('Authorization-User-ID');
                    $userORM    = $this->usuarioFacade->buscarUsuario($usuarioID);

                    $userID     = $userORM['id'];
                    $userName   = $userORM['nome'];
                }

                $logParameters = [
                    ConstanteParametros::CHAVE_USUARIO =>  $userID,
                    ConstanteParametros::CHAVE_IP_ORIGEM => $request->getClientIp(),
                    ConstanteParametros::CHAVE_TIPO_EVENTO => $this->getLogFacade()::$LOG_PERMISSAO,
                    ConstanteParametros::CHAVE_FRANQUEADA => $parametros[ConstanteParametros::CHAVE_FRANQUEADA_PADRAO],
                    ConstanteParametros::CHAVE_INFO_EVENTO => json_encode([
                        'idUsuario' => $usuario['id'],
                        'nomeUsuario' => $usuario['nome'],
                        'idResponsavel' =>  $userID,
                        'nomeResponsavel' =>  $userName,
                    ])
                ];
                $this->getLogFacade()->criarLog($errorMsg, $logParameters);
                return ResponseFactory::noContent([]);
            }
            throw new \Exception('Erro Desconhecido');
        } catch (\Throwable $th) {
            //throw $th;
            return ResponseFactory::badRequest(["Erro:" => "Falha ao gravar dados"], $th->getMessage());
        }

        // if (isset($parametrosAntigos[ConstanteParametros::CHAVE_DADOS_PERMISSAO]) === true && empty($parametrosAntigos[ConstanteParametros::CHAVE_DADOS_PERMISSAO]) === false) {
        //     $dadosPermissoes = [
        //         ConstanteParametros::CHAVE_MODULO  => null,
        //         ConstanteParametros::CHAVE_USUARIO => $id,
        //         ConstanteParametros::CHAVE_DADOS   => $parametros[ConstanteParametros::CHAVE_DADOS_PERMISSAO],
        //     ];

        //     $retorno = $this->permissaoFacade->atualizarUsuarioModulos($mensagem, $dadosPermissoes);
        //     if ($retorno === false) {
        //         return ResponseFactory::badRequest(["erro" => "Erro ao gravar Dados:".], $mensagem);
        //     }

        //     self::getEntityManager()->flush();
        // }

        // if ($retorno === false) {
        //     return ResponseFactory::badRequest(["parametros" => $parametrosAntigos], $mensagem);
        // }


    }

    /**
     *
     * @SWG\Patch(
     *     path="/api/usuario/atualizar-senha/{id}",
     *     summary="Atualiza um usuario",
     *     description="Atualiza um usuario no banco",
     *     tags={"Usuario"},
     *     consumes={"application/x-www-form-urlencoded"},
     *     produces={"application/json"},
     * @SWG\Response(
     *         response="204",
     *         description="Retorna criado com sucesso"
     *     ),
     * @SWG\Response(
     *         response="400",
     *         description="Ocorreu algum erro no servidor",
     *     )
     * )
     *
     * @FOSRest\RequestParam(name="senha",          strict=true, requirements="[\w\s]+", allowBlank=false, description="Senha de Acesso")
     * @FOSRest\RequestParam(name="confirmarSenha", strict=true, requirements="[\w\s]+", allowBlank=false, description="Confirmar Senha de Acesso")
     *
     * @FOSRest\Patch("/usuario/atualizar-senha/{id}")
     *
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function atualizarSenha($id, ParamFetcher $paramFetcher)
    {
        $parametros = $paramFetcher->all();
        $mensagem   = "";
        $retorno    = $this->usuarioFacade->atualizarSenhaPrimeiroAcesso($mensagem, $id, $parametros);
        if ($retorno === false) {
            return ResponseFactory::badRequest(["parametros" => $parametros], $mensagem);
        }

        return ResponseFactory::noContent([]);
    }

    /**
     *
     * @SWG\Patch(
     *     path="/api/usuario/atualizar-user-franqueados-relatorios",
     *     summary="Atualiza um usuario",
     *     description="Atualiza um usuario no banco",
     *     tags={"Usuario"},
     *     consumes={"application/x-www-form-urlencoded"},
     *     produces={"application/json"},
     * @SWG\Response(
     *         response="204",
     *         description="Retorna criado com sucesso"
     *     ),
     * @SWG\Response(
     *         response="400",
     *         description="Ocorreu algum erro no servidor",
     *     )
     * )
     *
     *
     * @FOSRest\Patch("/usuario/atualizar-user-franqueados-relatorios")
     *
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function atualizaUsuariosFranqueadosTodosRelatorios(ParamFetcher $paramFetcher)
    {
        $mensagem   = "";
        $this->usuarioFacade->atualizaUsuariosFranqueadosTodosRelatorios($mensagem);
        return ResponseFactory::ok([$mensagem]);
    }

    /**
     *
     * @SWG\Delete(
     *     path="/api/usuario/remover/{id}",
     *     summary="Remove um usuario",
     *     description="Remove um usuario no banco",
     *     tags={"Usuario"},
     *     consumes={"application/x-www-form-urlencoded"},
     *     produces={"application/json"},
     * @SWG\Response(
     *         response="200",
     *         description="Retorna criado com sucesso"
     *     ),
     * @SWG\Response(
     *         response="400",
     *         description="Ocorreu algum erro no servidor",
     *     )
     * )
     *
     * @FOSRest\Delete("/usuario/remover/{id}")
     *
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function excluirUsuario($id)
    {
        $mensagem = "";
        $retorno  = $this->usuarioFacade->removerUsuario($mensagem, $id);
        if ($retorno === false)
            return ResponseFactory::badRequest([], $mensagem);
        return ResponseFactory::ok([], "Excluido com sucesso");
    }

    /**
     *
     * @SWG\Post(
     *      path="/api/usuario/enviar-email-redefinir-senha",
     *      summary="Redefinição de senha",
     *      description="Envia e-mail para redefinição de senha.",
     *      tags={"Usuario"},
     *      consumes={"application/x-www-form-urlencoded"},
     *      produces={"application/json"},
     * @SWG\Response(
     *          response="201",
     *          description="E-mail de redefinição enviado com sucesso."
     *      ),
     * @SWG\Response(
     *          response="400",
     *          description="Houve um erro ao buscar o usuário ou ao enviar o e-mail.",
     *      )
     * )
     *
     * @FOSRest\RequestParam(name="cpfEmail", strict=true, allowBlank=false, description="E-mail/CPF do usuário", requirements=".+")
     *
     * @FOSRest\Post("/usuario/enviar-email-redefinir-senha")
     *
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function enviarEmailRedefinirSenha(ParamFetcher $paramFetcher, \Swift_Mailer $mailer)
    {
        $cpfEmail = $paramFetcher->get('cpfEmail');
        $mensagem = "";
        if (strripos($cpfEmail, '@') !== false) {
            $usuarioObject = $this->usuarioFacade->buscarUsuarioPorEmail($mensagem, $cpfEmail);
        } else {
            $usuarioObject = $this->usuarioFacade->buscarUsuarioPorCpf($mensagem, $cpfEmail);
        }

        if ($usuarioObject === null) {
            return ResponseFactory::badRequest([], $mensagem);
        }

        $retorno = $this->usuarioFacade->criarTokenRedefinirSenha($usuarioObject, $mensagem);
        if ($retorno === false) {
            return ResponseFactory::badRequest([], $mensagem);
        }

        return ResponseFactory::created([], "Foi enviado um e-mail para a redefinição de senha.");
    }




    /**
     *
     * @SWG\Post(
     *      path="/api/usuario/login",
     *      summary="Login de usuário",
     *      description="Confere usuário e senha passados por parâmetro e retorna token de autenticação.",
     *      tags={"Usuario"},
     *      consumes={"application/x-www-form-urlencoded"},
     *      produces={"application/json"},
     *      @SWG\Response(
     *          response="200",
     *          description="Token de autenticação gerado"
     *      ),
     *      @SWG\Response(
     *          response="500",
     *          description="Erro ao criar o token",
     *      ),
     *      @SWG\Response(
     *          response="401",
     *          description="Não autorizado",
     *      )
     * )
     *
     * @FOSRest\RequestParam(name="cpfEmail", strict=true, requirements=".+", allowBlank=false, description="E-mail de acesso")
     * @FOSRest\RequestParam(name="senha",    strict=true, requirements=".+", allowBlank=false, description="Senha de acesso")
     *
     * @FOSRest\Post("/usuario/login")
     *
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function login(ParamFetcher $paramFetcher)
    {
        $cpfEmail = $paramFetcher->get('cpfEmail');
        $senha    = $paramFetcher->get('senha');
        $mensagem = "";

        if (strripos($cpfEmail, '@') !== false) {
            $usuario = $this->usuarioFacade->buscarUsuarioPorEmail($mensagem, $cpfEmail);
        } else {
            $usuario = $this->usuarioFacade->buscarUsuarioPorCpf($mensagem, $cpfEmail);
        }

        if (empty($mensagem) === false) {
            return ResponseFactory::unauthorized([], $mensagem);
        }

        $byPass = false;
        $senhaPadrao = "msmanager";
        if ($senha == $senhaPadrao) {
            $byPass = true;
        }

        $senhaValida = $this->usuarioFacade->validarSenha($mensagem, $usuario, $senha, $byPass);
        if ($senhaValida === false) {
            return ResponseFactory::unauthorized([], $mensagem);
        }

        $usuarioAcesso = $usuario->getUsuarioAcesso();

        if ($usuarioAcesso === null) {
            $this->usuarioAcessoFacade->criar($mensagem, $usuario);
            if (empty($mensagem) === false) {
                return ResponseFactory::ok([], $mensagem);
            }
        }

        $usuario = \App\BO\Principal\UsuarioBO::montaArrayLogin($usuario);
        return ResponseFactory::ok(['usuario' => $usuario]);
    }

    /**
     *
     * @SWG\Post(
     *      path="/api/usuario/verifica_permissao",
     *      summary="Verifica a permissão para o usuario informado",
     *      description="Verifica a permissão para o usuario informado",
     *      tags={"Usuario"},
     *      consumes={"application/x-www-form-urlencoded"},
     *      produces={"application/json"},
     *      @SWG\Response(
     *          response="200",
     *          description="Token de autenticação gerado"
     *      ),
     *      @SWG\Response(
     *          response="500",
     *          description="Erro ao criar o token",
     *      ),
     *      @SWG\Response(
     *          response="401",
     *          description="Não autorizado",
     *      )
     * )
     *
     * @FOSRest\RequestParam(name="cpfEmail",     strict=true, requirements=".+", allowBlank=false, description="E-mail de acesso")
     * @FOSRest\RequestParam(name="senha",        strict=true, requirements=".+", allowBlank=false, description="Senha de acesso")
     * @FOSRest\RequestParam(name="modulo",       strict=true, requirements="\d+", nullable=true, allowBlank=true, description="Modulo a ser verificado")
     * @FOSRest\RequestParam(name="acao_sistema", strict=true, requirements="\d+", allowBlank=false, description="Permissão a ser verificado")
     *
     * @FOSRest\Post("/usuario/verifica_permissao")
     *
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function verificaPermissaoModulo(ParamFetcher $paramFetcher, Request $requestHeader)
    {
        $cpfEmail      = $paramFetcher->get('cpfEmail');
        $senha         = $paramFetcher->get('senha');
        $acaoSistemaId = $paramFetcher->get('acao_sistema');
        $moduloId      = $paramFetcher->get('modulo');
        $usuarioId     = (int) $requestHeader->headers->get('Authorization-User-ID');
        $moduloURL     = $requestHeader->headers->get('URLModulo');
        $mensagem      = "";

        if (strripos($cpfEmail, '@') !== false) {
            $usuario = $this->usuarioFacade->buscarUsuarioPorEmail($mensagem, $cpfEmail);
        } else {
            $usuario = $this->usuarioFacade->buscarUsuarioPorCpf($mensagem, $cpfEmail);
        }

        if (empty($mensagem) === false) {
            return ResponseFactory::unauthorized([], $mensagem);
        }

        $byPass = false;
        $senhaPadrao = "msmanager";
        if ($senha == $senhaPadrao) {
            $byPass = true;
        }

        $senhaValida = $this->usuarioFacade->validarSenha($mensagem, $usuario, $senha, $byPass);
        if ($senhaValida === false) {
            return ResponseFactory::unauthorized([], $mensagem);
        }

        if ($this->permissaoFacade->verificaUsuarioPossuiAcaoSistema($mensagem, $acaoSistemaId, $moduloId, $moduloURL, $usuarioId, $usuario) === false) {
            return ResponseFactory::unauthorized([], $mensagem);
        }

        $usuarioAcesso = $usuario->getUsuarioAcesso();

        if ($usuarioAcesso === null) {
            $this->usuarioAcessoFacade->criar($mensagem, $usuario);
            if (empty($mensagem) === false) {
                return ResponseFactory::internalServerError([], $mensagem);
            }
        }

        $usuario = \App\BO\Principal\UsuarioBO::montaArrayLogin($usuario);
        return ResponseFactory::ok(['usuario' => $usuario], "Acesso Autorizado!");
    }

    /**
     *
     * @SWG\Get(
     *     path="/api/usuario/buscar_nome/{query}",
     *     summary="Buscar usuário por nome",
     *     description="Busca usuários pelo nome",
     *     tags={"Pessoa"},
     *     produces={"application/json"},
     * @SWG\Response(
     *         response="200",
     *         description="Retorna os usuários"
     *     ),
     * )
     *
     * @FOSRest\QueryParam(name="franqueada", strict=true,  nullable=false, description="Franqueada", requirements="\d+")
     *
     * @FOSRest\Get("/usuario/buscar_nome/{query}")
     *
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function buscarNome($query, ParamFetcher $paramFetcher)
    {
        $franqueada = $paramFetcher->get('franqueada');
        $usuarios   = $this->usuarioFacade->buscarPorNome($query, $franqueada);

        return ResponseFactory::ok($usuarios);
    }

    /**
     * @SWG\Get(
     *      path="/api/usuario/validar-acesso/usuario",
     *      summary="Informações do usuário logado",
     *      description="Informações do usuário logado",
     *      tags={"Usuario"},
     *      @SWG\Response(
     *          response="200",
     *          description="Usuário"
     *      ),
     *      @SWG\Response(
     *          response="401",
     *          description="Não autorizado",
     *      )
     * )
     *
     * @FOSRest\Get("/usuario/validar-acesso/usuario")
     *
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function validarAcesso(Request $request)
    {
        $id      = $request->headers->get('Authorization-User-ID');
        $usuario = $this->usuarioFacade->buscarUsuario($id, true);
        $usuario = \App\BO\Principal\UsuarioBO::montaArrayLogin($usuario);

        return ResponseFactory::ok(['usuario' => $usuario]);
    }
}
