<?php

namespace App\Controller\Principal\TransacaoCartao;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use FOS\RestBundle\Controller\Annotations as FOSRest;
use Swagger\Annotations as SWG;
use App\Factory\ResponseFactory;
use App\Controller\Principal\Base\GenericController;
use FOS\RestBundle\Request\ParamFetcher;
use App\Facade\Principal\TransacaoCartaoFacade;
use Symfony\Component\HttpFoundation\Request;
use App\Helper\ConstanteParametros;

/**
 *
 * @author        Luiz A Costa
 * @Route("/api")
 */
class TransacaoCartaoController extends GenericController
{

    /**
     *
     * @var \App\Facade\Principal\TransacaoCartaoFacade
     */
    private $transacaoCartaoFacade;

    /**
     * {@inheritdoc}
     */
    protected function constroiFacades()
    {
        // Para criar o LogFacade do GenericController
        parent::constroiFacades();
        $this->transacaoCartaoFacade = new TransacaoCartaoFacade(self::getManagerRegistry());
    }

    /**
     *
     * @SWG\Get(
     *     path="/api/transacao_cartao/listar",
     *     summary="Listar transacao_cartao",
     *     description="Lista as transacao_cartao do banco",
     *     produces={"application/json"},
     * @SWG\Response(
     *         response="200",
     *         description="Retorna os transacao_cartao"
     *     ),
     * @SWG\Response(
     *         response="400",
     *         description="Ocorreu algum erro no servidor",
     *     )
     * )
     *
     * @FOSRest\QueryParam(name="pagina",                  strict=false, allowBlank=false, default="1", description="Pagina para realizar o scroll", requirements="\d+")
     * @FOSRest\QueryParam(name="franqueada",              strict=false, allowBlank=false, description="Franqueada", requirements="\d+")
     * @FOSRest\QueryParam(name="numero_lancamento",       strict=false, allowBlank=false, description="Numero Lancamento")
     * @FOSRest\QueryParam(name="operadora_cartao",        strict=false, allowBlank=false, description="Operadora de Cartão", requirements="\d+")
     * @FOSRest\QueryParam(name="tipo_transacao",          strict=false, allowBlank=false, description="Tipo transacao", requirements="(D|C)")
     * @FOSRest\QueryParam(name="situacao",                strict=false, nullable=true, description="Situação", map=true)
     * @FOSRest\QueryParam(name="sacado_pessoa",           strict=false, allowBlank=false, description="Pessoa ID", requirements="\d+")
     * @FOSRest\QueryParam(name="aluno_pessoa",            strict=false, allowBlank=false, description="Pessoa ID", requirements="\d+")
     * @FOSRest\QueryParam(name="identificador",           strict=false, allowBlank=false, description="Identificador")
     * @FOSRest\QueryParam(name="valor_liquido_inicio",    strict=false, nullable=true, description="Valor Inicial", requirements="^\d{0,7}+\.?\d{0,2}?$")
     * @FOSRest\QueryParam(name="valor_liquido_fim",       strict=false, nullable=true, description="Valor Final", requirements="^\d{0,7}+\.?\d{0,2}?$")
     * @FOSRest\QueryParam(name="previsao_repasse_inicio", strict=false, nullable=true, description="Data Inicial de previsao repasse")
     * @FOSRest\QueryParam(name="previsao_repasse_fim",    strict=false, nullable=true, description="Data Final de previsao repasse")
     * @FOSRest\QueryParam(name="data_estorno_inicio",     strict=false, nullable=true, description="Data Inicial de estorno")
     * @FOSRest\QueryParam(name="data_estorno_fim",        strict=false, nullable=true, description="Data Final de estorno")
     * @FOSRest\QueryParam(name="order",                   strict=false, nullable=true,  description="Coluna de ordenação")
     * @FOSRest\QueryParam(name="direcao",                 strict=false, nullable=true,  description="ASC|DESC")
     *
     * @FOSRest\Get("/transacao_cartao/listar")
     *
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function lista(ParamFetcher $request)
    {
        $parametros = $request->all();
        $mensagem   = "";
        $resultados = $this->transacaoCartaoFacade->listar($parametros);
        if ($resultados === false) {
            return ResponseFactory::badRequest([], $mensagem);
        }

        return ResponseFactory::ok($resultados);
    }

    /**
     *
     * @SWG\Get(
     *     path="/api/transacao_cartao/{id}",
     *     summary="Buscar a transacao_cartao",
     *     description="Busca a transacao_cartao através da ID",
     *     produces={"application/json"},
     * @SWG\Response(
     *         response="200",
     *         description="Retorna a transacao_cartao"
     *     ),
     * @SWG\Response(
     *         response="404",
     *         description="Ocorreu algum erro no servidor",
     *     )
     * )
     *
     * @FOSRest\Get("/transacao_cartao/{id}")
     *
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function buscar($id)
    {
        $mensagemErro = "";
        $objetoORM    = $this->transacaoCartaoFacade->buscarPorId($mensagemErro, $id);
        if (is_null($objetoORM) === true) {
            return ResponseFactory::notFound([], $mensagemErro);
        }

        return ResponseFactory::ok($objetoORM);
    }

    /**
     *
     * @SWG\Post(
     *     path="/api/transacao_cartao/criar",
     *     summary="Cria uma transacao_cartao",
     *     description="Cria uma transacao_cartao no banco",
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
     * @FOSRest\RequestParam(name="franqueada",                    strict=true, nullable=false, description="ID da franqueada", requirements="\d+")
     * @FOSRest\RequestParam(name="titulo_receber",                strict=true, nullable=false, description="Titulo Receber", requirements="\d+")
     * @FOSRest\RequestParam(name="identificador",                 strict=true, nullable=false, allowBlank=false, description="Identificador", requirements="\d+")
     * @FOSRest\RequestParam(name="numero_lancamento",             strict=true, nullable=false, allowBlank=false, description="Numero Lancamento")
     * @FOSRest\RequestParam(name="valor_liquido",                 strict=true, nullable=false, allowBlank=false, description="Valor Liquido", requirements="^\d{0,7}+\.?\d{0,2}?$")
     * @FOSRest\RequestParam(name="taxa",                          strict=true, nullable=false, allowBlank=false, description="Taxa", requirements="^\d{0,7}+\.?\d{0,2}?$")
     * @FOSRest\RequestParam(name="previsao_repasse",              strict=true, nullable=true, allowBlank=true, description="Previsao de repase")
     * @FOSRest\RequestParam(name="data_estorno",                  strict=true, nullable=true, allowBlank=true, description="Data de Estorno")
     * @FOSRest\RequestParam(name="operadora_cartao",              strict=true, nullable=false, allowBlank=false, description="Operadora de Cartão", requirements="^.{0,3}")
     * @FOSRest\RequestParam(name="parcelamento_operadora_cartao", strict=true, nullable=false, allowBlank=false, description="Parcelamento Operadora de Cartão", requirements="^.{0,3}")
     * @FOSRest\RequestParam(name="tipo_transacao",                strict=true, nullable=false, allowBlank=false, description="Tipo transacao", requirements="(C|D)")
     *
     * @FOSRest\Post("/transacao_cartao/criar")
     *
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function criar(ParamFetcher $request)
    {
        $parametros = $request->all();
        $mensagem   = "";
        $objetoORM  = $this->transacaoCartaoFacade->criar($mensagem, $parametros);
        if ((is_null($objetoORM) === true) || (empty($mensagem) === false)) {
            return ResponseFactory::conflict(["parametros" => $parametros], $mensagem);
        }

        return ResponseFactory::created(["objetoORM" => $objetoORM->getId()], "Registro criado com sucesso!");
    }

    /**
     *
     * @SWG\Patch(
     *     path="/api/transacao_cartao/atualizar/{id}",
     *     summary="Atualiza um transacao_cartao",
     *     description="Atualiza um transacao_cartao no banco",
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
     * @FOSRest\RequestParam(name="numero_lancamento", strict=false, nullable=true, allowBlank=true, description="Numero Lancamento", requirements="\d+")
     * @FOSRest\RequestParam(name="previsao_repasse",  strict=false, nullable=true, allowBlank=true, description="Previsao de repase")
     *
     * @FOSRest\Patch("/transacao_cartao/atualizar/{id}")
     *
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function atualizar($id, ParamFetcher $request)
    {
        $parametros = $request->all();
        $mensagem   = "";
        $retorno    = $this->transacaoCartaoFacade->atualizar($mensagem, $id, $parametros);
        if ($retorno === false) {
            return ResponseFactory::badRequest(["parametros" => $parametros], $mensagem);
        }

        return ResponseFactory::noContent([]);
    }

    /**
     *
     * @SWG\Delete(
     *     path="/api/transacao_cartao/remover/{id}",
     *     summary="Remove uma transacao_cartao",
     *     description="Remove uma transacao_cartao no banco",
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
     * @FOSRest\Delete("/transacao_cartao/remover/{id}")
     *
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function excluir($id)
    {
        $mensagem = "";
        $retorno  = $this->transacaoCartaoFacade->remover($mensagem, $id);
        if ($retorno === false) {
            return ResponseFactory::badRequest([], $mensagem);
        }

        return ResponseFactory::ok([], "Excluido com sucesso");
    }

    /**
     *
     * @SWG\Post(
     *     path="/api/transacao_cartao/conciliar_varios/",
     *     summary="concilia uma ou mais transacao_cartao",
     *     description="concilia uma ou mais transacao_cartao no banco",
     *     consumes={"application/x-www-form-urlencoded"},
     *     produces={"application/json"},
     * @SWG\Response(
     *         response="201",
     *         description="Retorna conciliado com sucesso"
     *     ),
     * @SWG\Response(
     *         response="202",
     *         description="Ocorreu algum erro no servidor",
     *     )
     * )
     *
     * @FOSRest\RequestParam(name="franqueada", strict=true, nullable=false, description="ID da franqueada", requirements="\d+")
     * @FOSRest\RequestParam(name="transacoes", strict=false, nullable=false, description="Transações a serem conciliadas", map=true)
     *
     * @FOSRest\Post("/transacao_cartao/conciliar_varios/")
     *
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function conciliarVarios(ParamFetcher $request, Request $requestHeader)
    {
        $parametros = $request->all();
        $mensagem   = "";
        $parametros[ConstanteParametros::CHAVE_USUARIO] = $requestHeader->headers->get('Authorization-User-ID');

        foreach ($parametros[ConstanteParametros::CHAVE_TRANSACOES] as &$transacao) {
            $transacao[ConstanteParametros::CHAVE_USUARIO] = $parametros[ConstanteParametros::CHAVE_USUARIO];
            $objetoORM = $this->transacaoCartaoFacade->conciliar($mensagem, $transacao);

            if ((is_null($objetoORM) === true) || (empty($mensagem) === false)) {
                if (isset($parametros[ConstanteParametros::CHAVE_ERRO_DEADLOCK]) === true) {
                    $parametrosErro = [
                        ConstanteParametros::CHAVE_TIPO_EVENTO => \App\Facade\Log\LogFacade::$LOG_CREATE,
                        ConstanteParametros::CHAVE_IP_ORIGEM   => $requestHeader->getClientIp(),
                        ConstanteParametros::CHAVE_FRANQUEADA  => $parametros[ConstanteParametros::CHAVE_FRANQUEADA],
                        ConstanteParametros::CHAVE_USUARIO     => $parametros[ConstanteParametros::CHAVE_USUARIO],
                        ConstanteParametros::CHAVE_INFO_EVENTO => "Ocorreu um erro de Deadlock em:" . $requestHeader->getUri() . " \n Possivelmente 2 ou mais usuarios tentaram executar o update no mesmo registro, ao mesmo tempo.",
                    ];

                    $erroMsg = "";
                    self::getLogFacade()->criarLog($erroMsg, $parametrosErro);
                }

                return ResponseFactory::conflict(["parametros" => $transacao], $mensagem);
            }
        }//end foreach

        return ResponseFactory::created([], "Conciliação efetuada com sucesso!");
    }

    /**
     *
     * @SWG\Post(
     *     path="/api/transacao_cartao/estornar/{id}",
     *     summary="estorna uma transacao_cartao",
     *     description="estorna uma transacao_cartao no banco",
     *     consumes={"application/x-www-form-urlencoded"},
     *     produces={"application/json"},
     * @SWG\Response(
     *         response="201",
     *         description="Retorna estornado com sucesso"
     *     ),
     * @SWG\Response(
     *         response="202",
     *         description="Ocorreu algum erro no servidor",
     *     )
     * )
     *
     * @FOSRest\RequestParam(name="franqueada", strict=true, nullable=false, description="ID da franqueada", requirements="\d+")
     *
     * @FOSRest\Post("/transacao_cartao/estornar/{id}")
     *
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function estornar($id, ParamFetcher $request, Request $requestHeader)
    {
        $parametros = $request->all();
        $mensagem   = "";
        $parametros[ConstanteParametros::CHAVE_USUARIO] = $requestHeader->headers->get('Authorization-User-ID');

        $objetoORM = $this->transacaoCartaoFacade->estornar($mensagem, $id, $parametros);

        if ((is_null($objetoORM) === true) || (empty($mensagem) === false)) {
            if (isset($parametros[ConstanteParametros::CHAVE_ERRO_DEADLOCK]) === true) {
                $parametrosErro = [
                    ConstanteParametros::CHAVE_TIPO_EVENTO => \App\Facade\Log\LogFacade::$LOG_CREATE,
                    ConstanteParametros::CHAVE_IP_ORIGEM   => $requestHeader->getClientIp(),
                    ConstanteParametros::CHAVE_FRANQUEADA  => $parametros[ConstanteParametros::CHAVE_FRANQUEADA],
                    ConstanteParametros::CHAVE_USUARIO     => $parametros[ConstanteParametros::CHAVE_USUARIO],
                    ConstanteParametros::CHAVE_INFO_EVENTO => "Ocorreu um erro de Deadlock em:" . $requestHeader->getUri() . " \n Possivelmente 2 ou mais usuarios tentaram executar o update no mesmo registro, ao mesmo tempo.",
                ];

                $erroMsg = "";
                self::getLogFacade()->criarLog($erroMsg, $parametrosErro);
            }

            return ResponseFactory::conflict(["parametros" => $parametros], $mensagem);
        }

        $movimentoConta = null;
        if (isset($parametros[ConstanteParametros::CHAVE_MOVIMENTO_CONTA]) === true) {
            $movimentoConta = $parametros[ConstanteParametros::CHAVE_MOVIMENTO_CONTA]->getId();
        }

        return ResponseFactory::created(["movimentoContaORM" => $movimentoConta], "Estorno efetuado com sucesso!");
    }

    


}
