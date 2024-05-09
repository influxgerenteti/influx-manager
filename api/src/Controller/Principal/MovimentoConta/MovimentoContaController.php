<?php

namespace App\Controller\Principal\MovimentoConta;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use FOS\RestBundle\Controller\Annotations as FOSRest;
use Swagger\Annotations as SWG;
use App\Factory\ResponseFactory;
use App\Controller\Principal\Base\GenericController;
use FOS\RestBundle\Request\ParamFetcher;
use Symfony\Component\HttpFoundation\Request;
use App\Helper\ConstanteParametros;
use App\Helper\SituacoesSistema;
use App\Helper\LockHelper;
use App\Facade\Principal\MovimentoContaFacade;
use App\Facade\Principal\TipoMovimentoContaFacade;
use App\Facade\Principal\FranqueadaFacade;
use App\Facade\Principal\ContaFacade;

/**
 *
 * @author        Rodrigo de Souza Fernandes (GATI labs)
 * @Route("/api")
 */
class MovimentoContaController extends GenericController
{
     /**
     *
     * @var \App\Facade\Principal\ContaFacade
     */
    private $contaFacade;

    /**
     *
     * @var \App\Facade\Principal\MovimentoContaFacade
     */
    private $movimentoContaFacade;

    /**
     *
     * @var \App\Facade\Principal\FranqueadaFacade
     */
    private $franqueadaFacade;

    /**
     *
     * @var \App\Facade\Principal\TipoMovimentoContaFacade
     */
    private $tipoMovimentoContaFacade;

    /**
     *
     * @var \App\Helper\LockHelper;
     */
    private $lockHelper;

    /**
     * {@inheritdoc}
     */
    protected function constroiFacades()
    {
        // Para criar o LogFacade do GenericController
        parent::constroiFacades();
        $this->contaFacade              = new ContaFacade(self::getManagerRegistry());
        $this->movimentoContaFacade     = new MovimentoContaFacade(self::getManagerRegistry());
        $this->franqueadaFacade         = new FranqueadaFacade(self::getManagerRegistry());
        $this->tipoMovimentoContaFacade = new TipoMovimentoContaFacade(self::getManagerRegistry());
        $this->lockHelper = new LockHelper();
    }

    /**
     *
     * @SWG\Get(
     *     path="/api/movimento_conta/listar",
     *     summary="Listar MovimentoConta",
     *     description="Lista as MovimentoConta do banco",
     *     produces={"application/json"},
     * @SWG\Response(
     *         response="200",
     *         description="Retorna os MovimentoConta"
     *     ),
     * @SWG\Response(
     *         response="400",
     *         description="Ocorreu algum erro no servidor",
     *     )
     * )
     *
     * @FOSRest\QueryParam(name="franqueada",             strict=true, allowBlank=false, description="Franqueada", requirements="\d+")
     * @FOSRest\QueryParam(name="conta",                  strict=true, allowBlank=true, nullable=true, description="Conta", requirements="\d+")
     * @FOSRest\QueryParam(name="pagina",                 strict=false, allowBlank=false, default="1", description="Pagina para realizar o scroll", requirements="\d{0,2}")
     * @FOSRest\QueryParam(name="mes",                    strict=false, allowBlank=true, description="Mês", requirements="\d+")
     * @FOSRest\QueryParam(name="ano",                    strict=false, allowBlank=true, description="Ano", requirements="\d+")
     * @FOSRest\QueryParam(name="conciliado",             strict=false, allowBlank=true, description="Conciliado", map=true)
     * @FOSRest\QueryParam(name="tipo",                   strict=false, allowBlank=true, description="Tipo (entrada ou saída)")
     * @FOSRest\QueryParam(name="data_lancamento_inicio", strict=false, allowBlank=true, description="Data do lançamento início", requirements=".*")
     * @FOSRest\QueryParam(name="data_lancamento_fim",    strict=false, allowBlank=true, description="Data do lançamento fim", requirements=".*")
     * @FOSRest\QueryParam(name="valor_lancamento_de",    strict=false, allowBlank=true, description="Valor do lançamento início", requirements="[\d\.]*")
     * @FOSRest\QueryParam(name="valor_lancamento_ate",   strict=false, allowBlank=true, description="Valor do lançamento fim", requirements="[\d\.]*")
     * @FOSRest\QueryParam(name="forma_pagamento",        strict=false, allowBlank=true, description="Forma de pagamento", requirements="\d+")
     * @FOSRest\QueryParam(name="usuario",                strict=false, allowBlank=true, description="Usuário", requirements="\d+")
     * @FOSRest\QueryParam(name="numero_lancamento",      strict=false, allowBlank=true, description="Número do lançamento", requirements=".*")
     * @FOSRest\QueryParam(name="numero_cheque_cartao",   strict=false, allowBlank=true, description="Número do Cheque/Cartão", requirements=".*")
     * @FOSRest\QueryParam(name="titulo",   strict=false, allowBlank=true, description="Número do Título", requirements="\d+")
     * @FOSRest\QueryParam(name="categoria",              strict=false, allowBlank=true, description="Categoria (Planos de conta)", requirements=".*")
     * @FOSRest\QueryParam(name="origem",                 strict=false, allowBlank=true, description="Origem (favorecido ou fornecedor)", requirements="\d*")
     *
     * @FOSRest\Get("/movimento_conta/listar")
     *
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function lista(ParamFetcher $request)
    {
        $parametros = $request->all();
        $mensagem   = "";
        
        $resultados = $this->movimentoContaFacade->listar($parametros);
        if ($resultados === false) {
            return ResponseFactory::badRequest([], "Ocorreu algum erro inesperado.");
        }

        return ResponseFactory::ok($resultados);
    }

    /**
     *
     * @SWG\Get(
     *     path="/api/movimento_conta/{id}",
     *     summary="Buscar a MovimentoConta",
     *     description="Busca a MovimentoConta através da ID",
     *     produces={"application/json"},
     * @SWG\Response(
     *         response="200",
     *         description="Retorna a MovimentoConta"
     *     ),
     * @SWG\Response(
     *         response="404",
     *         description="Ocorreu algum erro no servidor",
     *     )
     * )
     *
     * @FOSRest\Get("/movimento_conta/{id}")
     *
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function buscar($id)
    {
        $mensagem  = "";
        $objetoORM = $this->movimentoContaFacade->buscarPorId($mensagem, $id);
        if (is_null($objetoORM) === true) {
            return ResponseFactory::notFound([], "MovimentoConta não encontrado.");
        }

        return ResponseFactory::ok($objetoORM);
    }

    /**
     *
     * @SWG\Get(
     *     path="/api/movimento_conta/buscar_aluno_fornecedor_com_movimento",
     *     summary="Buscar alunos e fornecedores com movimento",
     *     description="Buscar alunos e fornecedores com movimento através do nome_contato",
     *     produces={"application/json"},
     * @SWG\Response(
     *         response="200",
     *         description="Retorna a(s) Pessoa(s)"
     *     ),
     * @SWG\Response(
     *         response="404",
     *         description="Ocorreu algum erro no servidor",
     *     )
     * )
     *
     * @FOSRest\QueryParam(name="franqueada", strict=true, nullable=true, description="Franqueada", requirements="\d+")
     * @FOSRest\QueryParam(name="conta",      strict=true, nullable=true, description="Conta", requirements="\d+")
     *
     * @FOSRest\Get("/movimento_conta/buscar_aluno_fornecedor_com_movimento/{nome_contato}")
     *
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function buscarAlunoFornecedorComMovimento($nome_contato, ParamFetcher $request)
    {
        $parametros = $request->all();
        $mensagem   = "";
        $parametros[ConstanteParametros::CHAVE_NOME_CONTATO] = $nome_contato;
        $objetoORM = $this->movimentoContaFacade->buscarAlunoFornecedorComMovimento($mensagem, $parametros);
        if (is_null($objetoORM) === true) {
            return ResponseFactory::notFound([], "MovimentoConta não encontrado.");
        }

        return ResponseFactory::ok($objetoORM);
    }

    /**
     *
     * @SWG\Post(
     *     path="/api/movimento_conta/criar",
     *     summary="Cria uma MovimentoConta",
     *     description="Cria uma MovimentoConta no banco",
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
     * @FOSRest\RequestParam(name="franqueada",            strict=true, nullable=false, description="Franqueada", requirements="\d+")
     * @FOSRest\RequestParam(name="conta",                 strict=false, nullable=true, description="Conta", requirements="\d+")
     * @FOSRest\RequestParam(name="tipo_movimento_conta",  strict=false, nullable=true, description="Tipo Movimento Conta", requirements="\d+")
     * @FOSRest\RequestParam(name="titulo_pagar",          strict=false, nullable=true, description="Titulo Pagar", requirements="\d+")
     * @FOSRest\RequestParam(name="titulo_receber",        strict=false, nullable=true, description="Titulo Receber", requirements="\d+")
     * @FOSRest\RequestParam(name="forma_pagamento",       strict=true, nullable=false, description="Forma de pagamento", requirements="\d+")
     * @FOSRest\RequestParam(name="data_movimento",        strict=false, nullable=true, description="Data de movimento")
     * @FOSRest\RequestParam(name="data_deposito",         strict=false, nullable=true, description="Data do deposito")
     * @FOSRest\RequestParam(name="valor_montante",        strict=true, nullable=false, description="Valor do montante", requirements="^-?\d{0,13}+\.?\d{0,2}?$")
     * @FOSRest\RequestParam(name="valor_lancamento",      strict=true, nullable=false, description="Valor do lancamento", requirements="^-?\d{0,13}+\.?\d{0,2}?$")
     * @FOSRest\RequestParam(name="valor_juros",           strict=false, nullable=true, description="Valor de juros", requirements="^-?\d{0,7}+\.?\d{0,2}?$")
     * @FOSRest\RequestParam(name="valor_multa",           strict=false, nullable=true, description="Valor de multa", requirements="^-?\d{0,13}+\.?\d{0,2}?$")
     * @FOSRest\RequestParam(name="valor_desconto",        strict=false, nullable=true, description="Valor de desconto", requirements="^-?\d{0,7}+\.?\d{0,2}?$")
     * @FOSRest\RequestParam(name="valor_diferenca_baixa", strict=false, nullable=true, description="Valor baixado", requirements="^-?\d{0,13}+\.?\d{0,2}?$")
     * @FOSRest\RequestParam(name="observacao",            strict=false, allowBlank=true, description="Observação", requirements=".*")
     * @FOSRest\RequestParam(name="conciliado",            strict=true, nullable=false, default="S", description="Observação", requirements="[S|N]")
     * @FOSRest\RequestParam(name="operacao",              strict=true, nullable=false, default="C", description="Conciliado", requirements="[C|D|T]")
     * @FOSRest\RequestParam(name="numero_documento",      strict=false, allowBlank=true, description="Número do documento", requirements=".*")
     *
     * @FOSRest\Post("/movimento_conta/criar")
     *
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function criar(ParamFetcher $request, Request $requestHeader)
    {
        $parametros = $request->all();
        $mensagem   = "";
        $parametros[ConstanteParametros::CHAVE_USUARIO] = $requestHeader->headers->get('Authorization-User-ID');

        if (empty($parametros[ConstanteParametros::CHAVE_CONTA]) === true) {
            $franqueadaORM = $this->franqueadaFacade->buscarFranqueadaEContaPadrao($parametros[ConstanteParametros::CHAVE_FRANQUEADA]);
            $parametros[ConstanteParametros::CHAVE_CONTA] = $franqueadaORM->getContaPadrao()->getId();
        }

        if ((is_null($parametros[ConstanteParametros::CHAVE_TITULO_PAGAR]) === false) && (is_null($parametros[ConstanteParametros::CHAVE_TITULO_RECEBER]) === false)) {
            return ResponseFactory::conflict(["parametros" => $parametros], "Envie somente um título a pagar ou um a receber.");
        }

        if (is_null($parametros[ConstanteParametros::CHAVE_OPERACAO]) === true) {
            $parametros[ConstanteParametros::CHAVE_OPERACAO] = SituacoesSistema:: OPERACAO_CREDITO;
            if ((is_null($parametros[ConstanteParametros::CHAVE_TITULO_PAGAR]) === false) && (is_null($parametros[ConstanteParametros::CHAVE_TITULO_RECEBER]) === true)) {
                $parametros[ConstanteParametros::CHAVE_OPERACAO] = SituacoesSistema::OPERACAO_DEBITO;
            }
        }

        if ((is_null($parametros[ConstanteParametros::CHAVE_TIPO_MOVIMENTO_CONTA]) === true) || (empty($parametros[ConstanteParametros::CHAVE_TIPO_MOVIMENTO_CONTA]) === true)) {
            $parametros[ConstanteParametros::CHAVE_TIPO_MOVIMENTO_CONTA] = $this->tipoMovimentoContaFacade->buscarPorPropriedades([ConstanteParametros::CHAVE_MC_TIPO_OPERACAO => $parametros[ConstanteParametros::CHAVE_OPERACAO]]);
        }

        $this->lockHelper->setLock('movimento_conta_conta_' . $parametros[ConstanteParametros::CHAVE_CONTA]);
        if ($this->lockHelper->getLock()->isAcquired() === false) {
            $this->lockHelper->getLock()->acquire(true);
            $movimentoContaORM = $this->movimentoContaFacade->criar($mensagem, $parametros);
            if ((is_null($movimentoContaORM) === true) || (empty($mensagem) === false)) {
                $this->lockHelper->getLock()->release();
                return ResponseFactory::conflict(["parametros" => $parametros], $mensagem);
            }

            $this->lockHelper->getLock()->release();
            return ResponseFactory::created(["movimentoContaORM" => $movimentoContaORM->getId()], "Registro criado com sucesso!");
        } else {
            $parametros = [
                ConstanteParametros::CHAVE_TIPO_EVENTO => \App\Facade\Log\LogFacade::$LOG_CREATE,
                ConstanteParametros::CHAVE_IP_ORIGEM   => $requestHeader->getClientIp(),
                ConstanteParametros::CHAVE_FRANQUEADA  => $parametros[ConstanteParametros::CHAVE_FRANQUEADA],
                ConstanteParametros::CHAVE_USUARIO     => $parametros[ConstanteParametros::CHAVE_USUARIO],
                ConstanteParametros::CHAVE_INFO_EVENTO => "Ocorreu um erro de Deadlock em:" . $requestHeader->getUri() . " \n Possivelmente 2 ou mais usuarios tentaram executar o update no mesmo registro, ao mesmo tempo.",
            ];
            $erroMsg    = "";
            self::getLogFacade()->criarLog($erroMsg, $parametros);
            return ResponseFactory::conflict(["parametros" => $parametros], "Não foi possível prosseguir com o pagamento, possivelmente o pagamento já sendo executado por outra pessoa. Tente novamente.");
        }//end if
    }

    /**
     *
     * @SWG\Patch(
     *     path="/api/movimento_conta/atualizar/{id}",
     *     summary="Atualiza um MovimentoConta",
     *     description="Atualiza um MovimentoConta no banco",
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
     * @FOSRest\RequestParam(name="observacao", strict=false, nullable=true, description="Observação")
     *
     * @FOSRest\Patch("/movimento_conta/atualizar/{id}")
     *
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function atualizar($id, ParamFetcher $request)
    {
        $parametros = $request->all();
        $mensagem   = "";
        $retorno    = $this->movimentoContaFacade->atualizar($mensagem, $id, $parametros);
        if ($retorno === false) {
            return ResponseFactory::badRequest(["parametros" => $parametros], $mensagem);
        }

        return ResponseFactory::noContent([]);
    }

    /**
     *
     * @SWG\Patch(
     *     path="/api/movimento_conta/conciliar",
     *     summary="Concilia uma série de movimentos",
     *     description="Concilia uma série de movimentos",
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
     * @FOSRest\RequestParam(name="data_contabil", strict=true, nullable=false, description="Data contabil")
     * @FOSRest\RequestParam(name="data_deposito", strict=true, nullable=false, description="Data do deposito")
     * @FOSRest\RequestParam(name="ids",           strict=true, nullable=false, description="IDs dos movimentos a conciliar", map=true)
     *
     * @FOSRest\Patch("/movimento_conta/conciliar")
     *
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function conciliar (ParamFetcher $request)
    {
        $parametros = $request->all();
        $mensagem   = "";
        $retorno    = $this->movimentoContaFacade->conciliar($mensagem, $parametros);
        if ($retorno === false) {
            return ResponseFactory::badRequest(["parametros" => $parametros], $mensagem);
        }

        return ResponseFactory::noContent([]);
    }
    /**
     *
     * @SWG\Patch(
     *     path="/api/movimento_conta/transferir_existente/{id}",
     *     summary="Transfere um movimento de conta existente para outra conta",
     *     description="Transfere um movimento de conta existente para outra conta",
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
     * @FOSRest\RequestParam(name="conta", strict=true, nullable=false, description="Data do deposito")
     *
     * @FOSRest\Patch("/movimento_conta/transferir_existente/{id}")
     *
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function transferirExistente ($id, ParamFetcher $request)
    {
        $parametros = $request->all();
        $mensagem   = "";
        $retorno    = $this->movimentoContaFacade->transferirExistente($mensagem, $id, $parametros);
        if ($retorno === false) {
            return ResponseFactory::badRequest(["parametros" => $parametros], $mensagem);
        }

        return ResponseFactory::noContent([]);
    }

    /**
     *
     * @SWG\Post(
     *     path="/api/movimento_conta/transferir",
     *     summary="Cria uma MovimentoConta",
     *     description="Cria uma MovimentoConta no banco",
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
     * @FOSRest\RequestParam(name="franqueada",       strict=true, nullable=false, description="Franqueada", requirements="\d+")
     * @FOSRest\RequestParam(name="conta_origem",     strict=false, nullable=true, description="Conta", requirements="\d+")
     * @FOSRest\RequestParam(name="conta_destino",    strict=false, nullable=true, description="Conta", requirements="\d+")
     * @FOSRest\RequestParam(name="data_contabil",    strict=false, nullable=true, description="Data contabil")
     * @FOSRest\RequestParam(name="valor_lancamento", strict=true, nullable=false, description="Valor do lancamento", requirements="^-?\d{0,13}+\.?\d{0,2}?$")
     *
     * @FOSRest\Post("/movimento_conta/transferir")
     *
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function transferir (ParamFetcher $request, Request $requestHeader)
    {
        $parametros = $request->all();
        $mensagem   = "";
        $parametros[ConstanteParametros::CHAVE_USUARIO] = $requestHeader->headers->get('Authorization-User-ID');

        $this->lockHelper->setLock('movimento_conta_conta_' . $parametros[ConstanteParametros::CHAVE_CONTA_ORIGEM] . '_' . $parametros[ConstanteParametros::CHAVE_CONTA_DESTINO]);
        if ($this->lockHelper->getLock()->isAcquired() === false) {
            $this->lockHelper->getLock()->acquire(true);
            $transferiu = $this->movimentoContaFacade->transferir($mensagem, $parametros);
            if ($transferiu === false) {
                $this->lockHelper->getLock()->release();
                return ResponseFactory::conflict(["parametros" => $parametros], $mensagem);
            }

            $this->lockHelper->getLock()->release();
            return ResponseFactory::created([], "Registro criado com sucesso!");
        } else {
            $parametros = [
                ConstanteParametros::CHAVE_TIPO_EVENTO => \App\Facade\Log\LogFacade::$LOG_CREATE,
                ConstanteParametros::CHAVE_IP_ORIGEM   => $requestHeader->getClientIp(),
                ConstanteParametros::CHAVE_FRANQUEADA  => $parametros[ConstanteParametros::CHAVE_FRANQUEADA],
                ConstanteParametros::CHAVE_USUARIO     => $parametros[ConstanteParametros::CHAVE_USUARIO],
                ConstanteParametros::CHAVE_INFO_EVENTO => "Ocorreu um erro de Deadlock em:" . $requestHeader->getUri() . " \n Possivelmente 2 ou mais usuarios tentaram executar o update no mesmo registro, ao mesmo tempo.",
            ];
            $erroMsg    = "";
            self::getLogFacade()->criarLog($erroMsg, $parametros);
            return ResponseFactory::conflict(["parametros" => $parametros], "Não foi possível prosseguir com transferência, possivelmente já sendo executado por outra pessoa. Tente novamente.");
        }//end if
    }

    /**
     *
     * @SWG\Post(
     *     path="/api/movimento_conta/estornar",
     *     summary="Estornar um lançamento",
     *     description="Estornar um lançamento",
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
     * @FOSRest\RequestParam(name="franqueada",   strict=true, nullable=false, description="Franqueada", requirements="\d+")
     * @FOSRest\RequestParam(name="lancamento",   strict=false, nullable=true, description="Lançamento", requirements="\d+")
     * @FOSRest\RequestParam(name="data_estorno", strict=false, nullable=true, description="Data estorno")
     * @FOSRest\RequestParam(name="observacao",   strict=true, nullable=false, description="Observação", requirements=".*")
     *
     * @FOSRest\Post("/movimento_conta/estornar")
     *
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function estornar (ParamFetcher $request, Request $requestHeader)
    {
        $parametros = $request->all();
        $mensagem   = "";
        $parametros[ConstanteParametros::CHAVE_USUARIO] = $requestHeader->headers->get('Authorization-User-ID');
        $movimentoContaORM = $this->movimentoContaFacade->buscarPorId($mensagem, $parametros[ConstanteParametros::CHAVE_LANCAMENTO]);
        $contaID           = $movimentoContaORM->getConta()->getId();

        $this->lockHelper->setLock('movimento_conta_conta_' . $contaID);

        if ($this->lockHelper->getLock()->isAcquired() === false) {
            $this->lockHelper->getLock()->acquire(true);
            $estornou = $this->movimentoContaFacade->estornar($mensagem, $parametros, $movimentoContaORM);
            if ($estornou === false) {
                $this->lockHelper->getLock()->release();
                return ResponseFactory::conflict(["parametros" => $parametros], $mensagem);
            }

            $this->lockHelper->getLock()->release();
            return ResponseFactory::created([], "Registro criado com sucesso!");
        } else {
            $parametros = [
                ConstanteParametros::CHAVE_TIPO_EVENTO => \App\Facade\Log\LogFacade::$LOG_CREATE,
                ConstanteParametros::CHAVE_IP_ORIGEM   => $requestHeader->getClientIp(),
                ConstanteParametros::CHAVE_FRANQUEADA  => $parametros[ConstanteParametros::CHAVE_FRANQUEADA],
                ConstanteParametros::CHAVE_USUARIO     => $parametros[ConstanteParametros::CHAVE_USUARIO],
                ConstanteParametros::CHAVE_INFO_EVENTO => "Ocorreu um erro de Deadlock em:" . $requestHeader->getUri() . " \n Possivelmente 2 ou mais usuarios tentaram executar o update no mesmo registro, ao mesmo tempo.",
            ];
            $erroMsg    = "";
            self::getLogFacade()->criarLog($erroMsg, $parametros);
            return ResponseFactory::conflict(["parametros" => $parametros], "Não foi possível prosseguir com o estorno, possivelmente já sendo executado por outra pessoa. Tente novamente.");
        }//end if
    }


}
