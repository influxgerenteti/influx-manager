<?php

namespace App\EventSubscriber;

use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpKernel\Event\GetResponseEvent;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\RouterInterface;
use Doctrine\Common\Persistence\ManagerRegistry;
use Predis\Client as RedisClient;

use App\BO\Principal\PermissaoBO;
use App\Facade\Principal\UsuarioAcessoFacade;
use App\Factory\GeneralORMFactory;
use App\Helper\ConstanteParametros;
use App\Helper\VariaveisCompartilhadas;

class TokenSubscriber implements EventSubscriberInterface
{

    /**
     *
     * @var \Doctrine\ORM\EntityManager $entityManager
     */
    private $entityManager;

    /**
     *
     * @var \App\Facade\Principal\UsuarioAcessoFacade $usuarioAcessoFacade
     */
    private $usuarioAcessoFacade;

    /**
     *
     * @var \App\BO\Principal\PermissaoBO
     */
    private $permissaoBO;

    /**
     *
     * @var \Symfony\Component\Routing\Router
     */
    private $router;

    /**
     *
     * @var \Predis\Client
     */
    private $redisClient;

    /**
     *
     * @var array Rotas que podem ser acessadas sem autenticação
     */
    private $rotasAbertas = [
        "/api/usuario/login"                        => true,
        "/api/contrato/aceitar/{chave}"              => true,
        "/api/contrato/enviar/{id}"              => true,
        "/api/usuario/enviar-email-redefinir-senha" => true,
        "/api/relatorios/teste"                     => true,
        "/api/recibo/gerar_recibo"                  => true,
        "/api/token/buscar"                         => true,
        "/api/token/setar-senha"                    => true,
        "/integracao/escola/importar"                    => true,
        // "/api/magic"                                => true,
    ];

    private $rotasGerais = [
        "/api/log/criar"                      => true,
        "/api/favorito/criar"                 => true,
        "/api/favorito/remover/{id}"          => true,
        "/api/mensagens_ajuda/listar"         => true,
        "/api/notificacoes/listar"            => true,
        "/api/modulo/listar-menu"             => true,
        "/api/franqueada/listar"              => true,
        "/api/permissao/modulo"               => true,
        "/api/usuario/validar-acesso/usuario" => true,
        "/api/metadata"                       => true,
    ];

    /**
     * Verifica se a rota eh alguma relacionada com a documentacao do sistema
     *
     * @param string $rota Rota que esta sendo acessada
     *
     * @return boolean true|false
     */
    private function verificaRotasDocs($rota)
    {
        return (preg_match("/\/api\/doc/", $rota) === 1);
    }

    /**
     * Verifica as rotas liberadas pelo sistema
     *
     * @param string $rota Rota que esta sendo acessada
     *
     * @return boolean true|false
     */
    private function verificaRotasLiberadas($rota)
    {
        $rotaAberta = isset($this->rotasAbertas[$rota]);
        $rotaDocs   = $this->verificaRotasDocs($rota);
        return (($rotaAberta === true)  || ($rotaDocs === true));
    }

    private function verificaRotasGerais($rota)
    {
        return isset($this->rotasGerais[$rota]);
    }

    function __construct(ManagerRegistry $mr, RouterInterface $router)
    {
        $this->usuarioAcessoFacade = new UsuarioAcessoFacade($mr);
        $this->permissaoBO         = new PermissaoBO($this->usuarioAcessoFacade->getEntityManager());
        $this->entityManager       = $this->usuarioAcessoFacade->getEntityManager();
        $this->router = $router;

        $this->redisClient = new RedisClient("redis://" . getenv("REDIS_HOST") . ":" . getenv("REDIS_PORT"));
    }

    /**
     * Valida cada requisição, se a rota não for de acesso livre,
     * valida-se o token de acesso do usuário
     *
     * @param GetResponseEvent $event
     *
     * @return void
     */
    public function onKernelRequest(GetResponseEvent $event)
    {
        if ($event->isMasterRequest() === false) {
            return;
        }

        $request  = $event->getRequest();
        $rota     = $request->getPathInfo();
        $nomeRota = $this->router->match($rota);

        // Consulta a rota no Redis, se não exitir, busca na RouteCollection e salva no Redis
        $rota = $this->redisClient->get($nomeRota["_route"]);
        if (is_null($rota) === true) {
            $objetoRouter = $this->router->getRouteCollection()->get($nomeRota["_route"]);
            $rota         = $objetoRouter->getPath();
            $this->redisClient->set($nomeRota["_route"], $rota);
        }

        if ($this->verificaRotasLiberadas($rota) === true) {
            return;
        }

        $token    = $request->headers->get('Authorization');
        $modulo   = $request->headers->get('URLModulo');
        $moduloID = $request->headers->get('Modulo');

        $franqueadaID = $request->headers->get('Franqueada');

        if ((is_null($franqueadaID) === true) || (empty($franqueadaID) === true)) {
            $franqueadaID = $request->get('franqueada');
        }

        VariaveisCompartilhadas::$franqueadaID = $franqueadaID;

        if ((is_null($token) === true) || (empty($token) === true)) {
            $token = $request->query->get('Authorization');
        }

        if (is_null($modulo) === true) {
            $modulo = $request->query->get('URLModulo');
        }

        // caso não tenha a ID do módulo no header, busca na query param
        if (is_null($moduloID) === true) {
            $moduloID = $request->query->get('Modulo');
            $request->headers->set('Modulo', $moduloID);
        }

        $entity      = $request->get('entity');
        $searchRoute = $rota;

        // echo($moduloID);
        // die;

        // caso ainda não tenha conseguido o módulo de alguma forma, executa a validação pelo nome do controller
        if (is_null($moduloID) === true) {
            

            $moduloRepository = $this->entityManager->getRepository(\App\Entity\Principal\Modulo::class);
            
            $searchRoute = $modulo;
            
            $moduloORM        = $moduloRepository->findOneBy([ConstanteParametros::CHAVE_URL => $searchRoute]);

            
            if(is_null($moduloORM)){
                if (is_null($entity) === true) {
                    // Tenta pegar o controller da rota como fallback de entidade
                    $controller = $nomeRota['_controller'];
                    $entity     = preg_replace('/App\\\\Controller\\\\(\w+)\\\\\w+\\\\(\w+)Controller.*/i', 'App\\\Entity\\\$1\\\$2', $controller);
                }
                $moduloORM        = $moduloRepository->findOneBy([ConstanteParametros::CHAVE_ENTITY => $entity]);               
               
            }
            
            if (is_null($moduloORM) === false) {
                $request->headers->set('Modulo', $moduloORM->getId());
                $moduloID    = $moduloORM->getId();
                // if (is_null($moduloORM)){
                //     $searchRoute = '/api/magic';
                // }
              
            }
            else{
                $res  = new Response();
                $body = ["mensagem" => "Modulo do sistema não encontrado {$modulo}-{$searchRoute} rota {$rota}."];
                $res->setStatusCode(403);
                $res->setContent(json_encode($body));
                $res->prepare($request);
                $event->setResponse($res);
                return;
            }
            
            
        }

        // if (getenv('GRAVAR_MODULOS') !== false) {
        //     $moduloRepository = $this->entityManager->getRepository(\App\Entity\Principal\Modulo::class);

        //     if (is_null($modulo) === false) {
        //         $moduloORM = $moduloRepository->findOneBy([ConstanteParametros::CHAVE_URL => $modulo]);
        //     }

        //     if (is_null($moduloID) === false) {
        //         $moduloORM = $moduloRepository->find($moduloID);
        //     }

        //     $urlSistemaRepository = $this->entityManager->getRepository(\App\Entity\Principal\UrlSistema::class);
        //     $urlSistemaORM        = $urlSistemaRepository->findOneBy([ConstanteParametros::CHAVE_URL_SISTEMA => $rota]);
        //     if (is_null($urlSistemaORM) === true) {
        //         $urlSistemaORM = GeneralORMFactory::criar(\App\Entity\Principal\UrlSistema::class, true, [ConstanteParametros::CHAVE_URL_SISTEMA => $rota]);
        //         $this->entityManager->persist($urlSistemaORM);
        //         $this->entityManager->flush();
        //     }

        //     if (is_null($moduloORM) === false) {
        //         $request->headers->set('Modulo', $moduloORM->getId());
        //         $moduloID = $moduloORM->getId();
        //         if ($moduloORM->getUrlSistemas()->contains($urlSistemaORM) === false) {
        //             $moduloORM->addUrlSistema($urlSistemaORM);
        //             $this->entityManager->flush();
        //         }
        //     }
        // }//end if

        $mensagem = "";

        // $bMenuApenasFranqueadora = null;
        $usuarioAcesso           = $this->usuarioAcessoFacade->validarTokenAcesso($mensagem, $token);


        $validaHorarioDeTrabalho = true;


        if (empty($usuarioAcesso) === false) {
            $usuario   = $usuarioAcesso->getUsuario();
            $usuarioId = $usuario->getId();
            VariaveisCompartilhadas::$usuarioID = $usuarioId;

            $papeis = $usuario->getPapels();
            foreach ($papeis as $papel) {
                if ($papel->getDescricao() === 'Franqueado') {
                    $validaHorarioDeTrabalho = false;
                    break;
                }
            }

            
            if($validaHorarioDeTrabalho){
                $horarioValido = false;

                $dataAtual    = \Carbon\Carbon::now('America/Sao_Paulo');

                $diaSemana    = $dataAtual->locale('pt_BR')->shortDayName;
                $diaSemana    = preg_replace(array("/(á|à|ã|â|ä)/","/(Á|À|Ã|Â|Ä)/","/(é|è|ê|ë)/","/(É|È|Ê|Ë)/","/(í|ì|î|ï)/","/(Í|Ì|Î|Ï)/","/(ó|ò|õ|ô|ö)/","/(Ó|Ò|Õ|Ô|Ö)/","/(ú|ù|û|ü)/","/(Ú|Ù|Û|Ü)/","/(ñ)/","/(Ñ)/"),explode(" ","a A e E i I o O u U n N"),$diaSemana);
                $diaSemana    = strtoupper($diaSemana);

                $horario      = $dataAtual->format('H:i:s');

                $funcionarios = $usuario->getFuncionarios();
                if(count($funcionarios) <= 0){
                    //usuário não é um funcionario
                    $horarioValido = true;
                }
                foreach ($funcionarios as $funcionario) {
                    $disponibilidades = $funcionario->getDisponibilidades();
                    if(count($disponibilidades) <= 0){
                        //usuário não cumpre jornada
                        $horarioValido = true;
                    }
                    foreach ($disponibilidades as $disponibilidade) {
                        $mesmoDia        = $disponibilidade->getDiaSemana() === $diaSemana;
                        $horaInicial     = \Carbon\Carbon::parse($disponibilidade->getHoraInicial())->timezone('America/Sao_Paulo')->format('H:i:s');
                        $horaFinal       = \Carbon\Carbon::parse($disponibilidade->getHoraFinal())->timezone('America/Sao_Paulo')->format('H:i:s');
                        $dentroDoHorario = $horaInicial <= $horario && $horaFinal >= $horario;
                        if ($mesmoDia === true && $dentroDoHorario === true) {
                            $horarioValido = true;
                            break;
                        }
                    }

                }

                if ($horarioValido === false) {
                    $mensagem = "Não é permitido acesso ao sistema fora do horário de trabalho";
    
                    $res  = new Response();
                    $body = ["mensagem" => $mensagem];
                    $res->setStatusCode(401);
                    $res->setContent(json_encode($body));
                    $res->prepare($request);
                    $event->setResponse($res);
                    return;
                }
            }
           
            

            

            /*
             * Switch para agrupar um módulo na permissão de outro. Exemplo, não faz sentido ter uma permissão
             * pra titulos a receber que seja diferente da permissão de contas a receber, então quando for
             * modulo 76, vai verificar se tem permissão no modulo 44
             */

            switch ($moduloID) {
                case 76:
                $moduloID = 44;
                    break;
            }

            // $bPermissaoPorPapel   = $this->permissaoBO->isPermitidoPorPapel($request->getMethod(), $usuarioId, $searchRoute, $moduloID);
            $possuiPermissao = $this->permissaoBO->possuiPermissao($request->getMethod(), $searchRoute, $moduloID);
           
            if(!$possuiPermissao){           
                $methodo = $request->getMethod();
                $res  = new Response();
                $body = ["mensagem" => "Usuário não possui permissão. Metodo: {$methodo} Rota: {$searchRoute}  ModuloId: {$moduloID}."];
                $res->setStatusCode(403);
                $res->setContent(json_encode($body));
                $res->prepare($request);
                $event->setResponse($res);
                return;
            }

            if(!$possuiPermissao){
                $methodo = $request->getMethod();
                $nomeModulo ="Desconhecido";
                if (is_null($moduloID) === false) {
                      $moduloORM = $moduloRepository->find($moduloID);
                      $nomeModulo = $moduloORM->getNome();
                }
                $acaoSistema = $this->permissaoBO->acaoSistemaPeloMetodo($request->getMethod())->getDescricao();

                $res  = new Response();
                $body = ["mensagem" => "Usuário não possui permissão. Metodo: {$methodo} Rota: {$searchRoute}  ModuloId: {$moduloID} Modulo:{$nomeModulo} Ação:{$acaoSistema}."];
                                
                $res->setStatusCode(403);
                $res->setContent(json_encode($body));
                $res->prepare($request);
                $event->setResponse($res);
                return;
            }

            $request->headers->set('Authorization-User-ID', $usuarioId);
            return;

            // if (strcmp($rota, "/api/franqueada/{id}") === 0) {
            //     $bUsuarioPossuiFranqueadaAtribuida = $this->permissaoBO->isUsuarioPossuiFranqueadaAtribuida($usuarioId, $request->get("id"));
            // } else {
            //     $bUsuarioPossuiFranqueadaAtribuida = $this->permissaoBO->isUsuarioPossuiFranqueadaAtribuida($usuarioId, VariaveisCompartilhadas::$franqueadaID);
            // }

            // $bPertenceFranqueadora   = $usuario->isUsuarioPertenceFranqueadora();
            // $bMenuApenasFranqueadora = null;
            // if (is_null($moduloORM) === false) {
            //     $bMenuApenasFranqueadora = $moduloORM->getApenasFranqueadora();
            // }

            // if (isset($_SESSION) === false) {
            //     if (session_start() === true) {
            //         if (is_null($franqueadaID) === false) {
            //             $_SESSION[ConstanteParametros::CHAVE_FRANQUEADA] = $franqueadaID;
            //         } else {
            //             $_SESSION[ConstanteParametros::CHAVE_FRANQUEADA] = $request->headers->get("Usuario-Franqueada");
            //         }

            //         $_SESSION[ConstanteParametros::CHAVE_IP_ORIGEM] = $request->getClientIp();
            //         $_SESSION[ConstanteParametros::CHAVE_USUARIO]   = $usuarioId;
            //     }
            // }

            
            // $possuiPermissaoModulo = ($bPermissaoPorUsuario === true);
            // || ($bPermissaoPorPapel === true);
            // if (($this->verificaRotasGerais($rota) === false) && (($bUsuarioPossuiFranqueadaAtribuida === false) || ($possuiPermissaoModulo === false) || ($bPertenceFranqueadora === false && ((is_null($bMenuApenasFranqueadora) === false) && ($bMenuApenasFranqueadora === true))))) {
            //     $mensagem .= "\n";

             
                
            //      $nomeModulo = $moduloORM ? $moduloORM->getNome() : "";

               
               

            //     if ($bUsuarioPossuiFranqueadaAtribuida === false) {
            //         $mensagem .= "\nUsuario não possui acesso a franqueada solicitada, atribuida ao seu usuario.";
            //     }
            //     else                
            //     if ($possuiPermissaoModulo === false) {
                    

            //         $mensagem .= "\nUsuario não possui acesso à esta ação no modulo $nomeModulo.";
            //     }
            //     else
            //     if ((is_null($bMenuApenasFranqueadora) === false) && ($bMenuApenasFranqueadora === true)) {
                    
            //         $mensagem .= "\nApenas a franqueadora pode executar esta ação no modulo $nomeModulo da rota $rota.";
            //     }

            //     // messagem de erro de chamada
            //     if ($nomeModulo === "") {
            //         $mensagem .= "\n se esta usando o postman não esqueça de incluir o header URLModulo";

            //      }
                

            //     $res  = new Response();
            //     $body = ["mensagem" => $mensagem];
            //     $res->setStatusCode(403);
            //     $res->setContent(json_encode($body));
            //     $res->prepare($request);
            //     $event->setResponse($res);
            // }//end if

            // return;
        }//end if

        $res  = new Response();
        $body = ['mensagem' => $mensagem];
        $res->setStatusCode(401);
        $res->setContent(json_encode($body));
        $res->prepare($request);
        $event->setResponse($res);
        return;
    }

    public static function getSubscribedEvents()
    {
        return ['kernel.request' => 'onKernelRequest'];
    }


}
