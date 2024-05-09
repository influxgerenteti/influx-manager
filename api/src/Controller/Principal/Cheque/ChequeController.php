<?php

namespace App\Controller\Principal\Cheque;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use FOS\RestBundle\Controller\Annotations as FOSRest;
use Swagger\Annotations as SWG;
use App\Factory\ResponseFactory;
use App\Controller\Principal\Base\GenericController;
use FOS\RestBundle\Request\ParamFetcher;
use Symfony\Component\HttpFoundation\Request;
use App\Helper\ConstanteParametros;
use App\Facade\Principal\ChequeFacade;
use App\Facade\Principal\FormaPagamentoFacade;
use App\Facade\Principal\TipoMovimentoContaFacade;
use App\Facade\Principal\MovimentoContaFacade;
use App\Helper\SituacoesSistema;
use App\Helper\LockHelper;

/**
 *
 * @author        Luiz A Costa
 * @Route("/api")
 */
class ChequeController extends GenericController
{


    /**
     *
     * @var \App\Facade\Principal\ChequeFacade
     */
    private $chequeFacade;

    /**
     *
     * @var \App\Facade\Principal\TipoMovimentoContaFacade
     */
    private $tipoMovimentoContaFacade;

    /**
     *
     * @var \App\Facade\Principal\FormaPagamentoFacade
     */
    private $formaPagamentoFacade;

    /**
     *
     * @var \App\Facade\Principal\MovimentoContaFacade
     */
    private $movimentoContaFacade;

    /**
     *
     * @var \App\Repository\Principal\ContaRepository
     */
    private $contaRepository;

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
        $this->chequeFacade         = new ChequeFacade(self::getManagerRegistry());
        $this->formaPagamentoFacade = new FormaPagamentoFacade(self::getManagerRegistry());
        $this->tipoMovimentoContaFacade = new TipoMovimentoContaFacade(self::getManagerRegistry());
        $this->movimentoContaFacade     = new MovimentoContaFacade(self::getManagerRegistry());
        $this->contaRepository          = self::getEntityManager()->getRepository(\App\Entity\Principal\Conta::class);
        $this->lockHelper = new LockHelper();
    }

    /**
     *
     * @SWG\Get(
     *     path="/api/cheque/listar",
     *     summary="Listar cheque",
     *     description="Lista as cheque do banco",
     *     produces={"application/json"},
     * @SWG\Response(
     *         response="200",
     *         description="Retorna os cheque"
     *     ),
     * @SWG\Response(
     *         response="400",
     *         description="Ocorreu algum erro no servidor",
     *     )
     * )
     *
     * @FOSRest\QueryParam(name="pagina",                 strict=false, nullable=true, default="1", description="Página para realizar o scroll", requirements="\d{0,2}")
     * @FOSRest\QueryParam(name="franqueada",             strict=true, nullable=false, description="ID da franqueada", requirements="\d+")
     * @FOSRest\QueryParam(name="tipo",                   strict=false, nullable=true, description="Tipo do cheque", requirements="\d+")
     * @FOSRest\QueryParam(name="numero_cheque",          strict=false, nullable=true, description="Numero do cheque", requirements="\d+")
     * @FOSRest\QueryParam(name="conta",                  strict=false, nullable=true, description="Numero da conta", requirements="\d+")
     * @FOSRest\QueryParam(name="banco",                  strict=false, nullable=true, description="Banco")
     * @FOSRest\QueryParam(name="mes_entrada",            strict=false, nullable=true, description="Mes de entrada", requirements="\d+")
     * @FOSRest\QueryParam(name="mes_bom_para",           strict=false, nullable=true, description="Mes bom para", requirements="\d+")
     * @FOSRest\QueryParam(name="valor_inicial",          strict=false, nullable=true, description="Valor Inicial", requirements="^\d{0,7}+\.?\d{0,2}?$")
     * @FOSRest\QueryParam(name="valor_final",            strict=false, nullable=true, description="Valor Final", requirements="^\d{0,7}+\.?\d{0,2}?$")
     * @FOSRest\QueryParam(name="data_entrada_inicial",   strict=false, nullable=true, description="Data Inicial de vencimento")
     * @FOSRest\QueryParam(name="data_entrada_final",     strict=false, nullable=true, description="Data Final de vencimento")
     * @FOSRest\QueryParam(name="data_bom_para_inicial",  strict=false, nullable=true, description="Data Inicial de pagamento")
     * @FOSRest\QueryParam(name="data_devolvido_inicial", strict=false, nullable=true, description="Data Inicial de devolução")
     * @FOSRest\QueryParam(name="data_devolvido_final",   strict=false, nullable=true, description="Data Final de devolução")
     * @FOSRest\QueryParam(name="data_bom_para_final",    strict=false, nullable=true, description="Data Final de pagamento")
     * @FOSRest\QueryParam(name="aluno",                  strict=false, nullable=true, description="Aluno ao qual se refere o titulo que o cheque está pagando")
     * @FOSRest\QueryParam(name="situacao",               strict=false, nullable=true, description="Situação", map=true)
     * @FOSRest\QueryParam(name="order",                  strict=false, nullable=true,  description="Coluna de ordenação")
     * @FOSRest\QueryParam(name="direcao",                strict=false, nullable=true,  description="ASC|DESC")
     * @FOSRest\QueryParam(name="item_entregue",          strict=false, nullable=true, description="Entregue/Nao Entregue", requirements="(0|1)")
     * @FOSRest\QueryParam(name="situacao",               strict=false, nullable=true, description="Situação", map=true)
     *
     * @FOSRest\Get("/cheque/listar")
     *
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function lista(ParamFetcher $request)
    {
        $parametros = $request->all();
        $mensagem   = "";
        $resultados = $this->chequeFacade->listar($parametros);
        if ($resultados === false) {
            return ResponseFactory::badRequest([], $mensagem);
        }

        return ResponseFactory::ok($resultados);
    }

    /**
     *
     * @SWG\Get(
     *     path="/api/cheque/{id}",
     *     summary="Buscar a cheque",
     *     description="Busca a cheque através da ID",
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
     * @FOSRest\Get("/cheque/{id}")
     *
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function buscar($id)
    {
        $mensagemErro = "";
        $objetoORM    = $this->chequeFacade->buscarPorId($mensagemErro, $id);
        if (is_null($objetoORM) === true) {
            return ResponseFactory::notFound([], $mensagemErro);
        }

        return ResponseFactory::ok($objetoORM);
    }

    /**
     *
     * @SWG\Post(
     *     path="/api/cheque/criar",
     *     summary="Cria uma cheque",
     *     description="Cria uma cheque no banco",
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
     * @FOSRest\RequestParam(name="franqueada",    strict=true, nullable=false, description="ID da franqueada", requirements="\d+")
     * @FOSRest\RequestParam(name="numero",        strict=true, nullable=false, allowBlank=false, description="Numero do cheque", requirements="\d+")
     * @FOSRest\RequestParam(name="banco",         strict=true, nullable=false, allowBlank=false, description="Banco")
     * @FOSRest\RequestParam(name="agencia",       strict=true, nullable=false, allowBlank=false, description="Numero da Agencia")
     * @FOSRest\RequestParam(name="conta",         strict=true, nullable=false, allowBlank=false, description="Numero da Conta")
     * @FOSRest\RequestParam(name="valor",         strict=true, nullable=false, allowBlank=false, description="valor do cheque", requirements="^\d{0,7}+\.?\d{0,2}?$")
     * @FOSRest\RequestParam(name="data_bom_para", strict=true, nullable=true, allowBlank=true, description="Data Bom Para")
     * @FOSRest\RequestParam(name="titular",       strict=true, nullable=false, allowBlank=false, description="Titular do cheque")
     * @FOSRest\RequestParam(name="complemento",   strict=true, nullable=true, allowBlank=true, description="Complemento")
     * @FOSRest\RequestParam(name="observacao",    strict=true, nullable=true, allowBlank=true, description="Observacao")
     *
     * @FOSRest\Post("/cheque/criar")
     *
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function criar(ParamFetcher $request, Request $requestHeader)
    {
        $parametros = $request->all();
        $mensagem   = "";
        $parametros[ConstanteParametros::CHAVE_USUARIO] = $requestHeader->headers->get('Authorization-User-ID');
        $objetoORM = $this->chequeFacade->criar($mensagem, $parametros);
        if ((is_null($objetoORM) === true) || (empty($mensagem) === false)) {
            return ResponseFactory::conflict(["parametros" => $parametros], $mensagem);
        }

        return ResponseFactory::created(["objetoORM" => $objetoORM->getId()], "Registro criado com sucesso!");
    }

    /**
     *
     * @SWG\Patch(
     *     path="/api/cheque/atualizar/{id}",
     *     summary="Atualiza um cheque",
     *     description="Atualiza um cheque no banco",
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
     * @FOSRest\RequestParam(name="franqueada",    strict=true, nullable=false, description="ID da franqueada", requirements="\d+")
     * @FOSRest\RequestParam(name="numero",        strict=false, nullable=false, allowBlank=false, description="Numero do cheque", requirements="\d+")
     * @FOSRest\RequestParam(name="banco",         strict=false, nullable=false, allowBlank=false, description="Banco")
     * @FOSRest\RequestParam(name="agencia",       strict=false, nullable=false, allowBlank=false, description="Numero da Agencia")
     * @FOSRest\RequestParam(name="conta",         strict=false, nullable=false, allowBlank=false, description="Numero da Conta")
     * @FOSRest\RequestParam(name="valor",         strict=false, nullable=true, allowBlank=true, description="valor do cheque", requirements="^\d{0,7}+\.?\d{0,2}?$")
     * @FOSRest\RequestParam(name="data_bom_para", strict=false, nullable=false, allowBlank=false, description="Data bom para")
     * @FOSRest\RequestParam(name="titular",       strict=false, nullable=false, allowBlank=true, description="Titular")
     * @FOSRest\RequestParam(name="complemento",   strict=false, nullable=true, allowBlank=true, description="Complemento")
     * @FOSRest\RequestParam(name="observacao",    strict=false, nullable=true, allowBlank=true, description="Observacao")
     *
     * @FOSRest\Patch("/cheque/atualizar/{id}")
     *
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function atualizar($id, ParamFetcher $request)
    {
        $parametros = $request->all();
        $mensagem   = "";
        $retorno    = $this->chequeFacade->atualizar($mensagem, $id, $parametros);
        if ($retorno === false) {
            return ResponseFactory::badRequest(["parametros" => $parametros], $mensagem);
        }

        return ResponseFactory::noContent([]);
    }

    /**
     *
     * @SWG\Delete(
     *     path="/api/cheque/remover/{id}",
     *     summary="Remove uma cheque",
     *     description="Remove uma cheque no banco",
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
     * @FOSRest\Delete("/cheque/remover/{id}")
     *
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function excluir($id)
    {
        $mensagem = "";
        $retorno  = $this->chequeFacade->remover($mensagem, $id);
        if ($retorno === false) {
            return ResponseFactory::badRequest([], $mensagem);
        }

        return ResponseFactory::ok([], "Excluido com sucesso");
    }

    /**
     *
     * @SWG\Patch(
     *     path="/api/cheque/baixar-cheques",
     *     summary="Baixar um cheque",
     *     description="Realiza uma baixa de um cheque no banco",
     *     consumes={"application/x-www-form-urlencoded"},
     *     produces={"application/json"},
     * @SWG\Response(
     *         response="204",
     *         description="Baixa efetuada com sucesso com sucesso"
     *     ),
     * @SWG\Response(
     *         response="400",
     *         description="Ocorreu algum erro no servidor",
     *     )
     * )
     *
     * @FOSRest\RequestParam(name="franqueada", strict=true, nullable=false, description="Franqueada", requirements="\d+")
     * @FOSRest\RequestParam(name="cheques",    strict=true, nullable=false, description="Cheques selecionados", map=true)
     * @FOSRest\RequestParam(name="conta",      strict=true, nullable=false, description="Conta para depósito", requirements="\d+")
     *
     * @FOSRest\Patch("/cheque/baixar-cheques")
     *
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function baixarCheque(ParamFetcher $request, Request $requestHeader)
    {
        $parametros = $request->all();
        $franqueada = $parametros[ConstanteParametros::CHAVE_FRANQUEADA];
        $contaId    = $parametros[ConstanteParametros::CHAVE_CONTA];

        $usuario = $requestHeader->headers->get('Authorization-User-ID');

        $mensagem        = "";
        $arrayChequesORM = [];
        $retorno         = $this->chequeFacade->baixarCheque($mensagem, $parametros[ConstanteParametros::CHAVE_CHEQUES], $arrayChequesORM, false);

        if ($retorno === false) {
            return ResponseFactory::badRequest([], $mensagem);
        }

        $data = new \DateTime();
        $data->setTime(12, 0, 0);
        $data = $data->format('Y-m-d\TH:i:s.uP');

        $formaPagamento            = $this->formaPagamentoFacade->buscarPorPropriedades(['forma_cheque' => true])->getId();
        $tipoMovimentoContaCredito = $this->tipoMovimentoContaFacade->buscarPorPropriedades([ConstanteParametros::CHAVE_MC_TIPO_OPERACAO => SituacoesSistema::OPERACAO_CREDITO])->getId();
        $tipoMovimentoContaDebito  = $this->tipoMovimentoContaFacade->buscarPorPropriedades([ConstanteParametros::CHAVE_MC_TIPO_OPERACAO => SituacoesSistema::OPERACAO_DEBITO])->getId();

        $deveExecutarFlush = true;
        $contaORM          = $this->contaRepository->find($contaId);

        foreach ($arrayChequesORM as $chequeORM) {
            $operacao      = null;
            $tipoMovimento = null;
            $dataDeposito  = $chequeORM->getDataBaixa();

            if ($chequeORM->getTipo() === 'P') {
                $operacao      = SituacoesSistema::OPERACAO_DEBITO;
                $tipoMovimento = $tipoMovimentoContaDebito;
            } else {
                $operacao      = SituacoesSistema::OPERACAO_CREDITO;
                $tipoMovimento = $tipoMovimentoContaCredito;
            }

            $tituloPagarORM   = $chequeORM->getTituloPagar();
            $tituloReceberORM = $chequeORM->getTituloReceber();

            $parametros = [
                ConstanteParametros::CHAVE_USUARIO                  => $usuario,
                ConstanteParametros::CHAVE_FRANQUEADA               => $franqueada,
                ConstanteParametros::CHAVE_FORMA_PAGAMENTO          => $formaPagamento,
                ConstanteParametros::CHAVE_MC_DATA_CONTABIL         => $chequeORM->getDataBaixa(),
                ConstanteParametros::CHAVE_MC_DATA_DEPOSITO         => $chequeORM->getDataBaixa(),
                ConstanteParametros::CHAVE_MC_VALOR_MONTANTE        => $chequeORM->getValor() + $chequeORM->getValorDesconto(),
                ConstanteParametros::CHAVE_MC_VALOR_LANCAMENTO      => $chequeORM->getValor(),
                ConstanteParametros::CHAVE_MC_VALOR_JUROS           => null,
                ConstanteParametros::CHAVE_MC_VALOR_MULTA           => null,
                ConstanteParametros::CHAVE_MC_VALOR_DESCONTO        => $chequeORM->getValorDesconto(),
                ConstanteParametros::CHAVE_MC_VALOR_DIFERENCA_BAIXA => null,
                ConstanteParametros::CHAVE_OPERACAO                 => $operacao,
                ConstanteParametros::CHAVE_NUMERO_DOCUMENTO         => $chequeORM->getNumero(),
                ConstanteParametros::CHAVE_OBSERVACAO               => $chequeORM->getObservacao(),
                ConstanteParametros::CHAVE_TIPO_MOVIMENTO_CONTA     => $tipoMovimento,
                ConstanteParametros::CHAVE_TITULO_PAGAR             => null,
                ConstanteParametros::CHAVE_TITULO_RECEBER           => null,
                ConstanteParametros::CHAVE_CHEQUE                   => $chequeORM->getId(),
                ConstanteParametros::CHAVE_CONTA                    => $contaId,
            ];

            if (is_null($tituloPagarORM) === false) {
                $parametros[ConstanteParametros::CHAVE_TITULO_PAGAR] = $tituloPagarORM->getId();
                $tituloPagarORM->setConta($contaORM);
            }

            if (is_null($tituloReceberORM) === false) {
                $parametros[ConstanteParametros::CHAVE_TITULO_RECEBER] = $tituloReceberORM->getId();
                $tituloReceberORM->setConta($contaORM);
            }

            $this->lockHelper->setLock('movimento_conta_conta_' . $parametros[ConstanteParametros::CHAVE_CONTA]);
            if ($this->lockHelper->getLock()->isAcquired() === false) {
                $this->lockHelper->getLock()->acquire(true);
                $movimentoContaORM = $this->movimentoContaFacade->criar($mensagem, $parametros, false);
                if ((is_null($movimentoContaORM) === true) || (empty($mensagem) === false)) {
                    $this->lockHelper->getLock()->release();
                    $deveExecutarFlush = false;
                    break;
                }

                $chequeORM->setMovimentoConta($movimentoContaORM);
                $chequeORM->setDataBaixa($dataDeposito);
                $this->lockHelper->getLock()->release();
            } else {
                $mensagemEvento = "Ocorreu um erro de Deadlock em:" . $requestHeader->getUri() . " \n Possivelmente 2 ou mais usuarios tentaram executar o update no mesmo registro, ao mesmo tempo.";
                $erroMsg        = "";
                $parametros     = [
                    ConstanteParametros::CHAVE_TIPO_EVENTO => \App\Facade\Log\LogFacade::$LOG_CREATE,
                    ConstanteParametros::CHAVE_IP_ORIGEM   => $requestHeader->getClientIp(),
                    ConstanteParametros::CHAVE_FRANQUEADA  => $parametros[ConstanteParametros::CHAVE_FRANQUEADA],
                    ConstanteParametros::CHAVE_USUARIO     => $parametros[ConstanteParametros::CHAVE_USUARIO],
                    ConstanteParametros::CHAVE_INFO_EVENTO => $mensagemEvento,
                ];
                self::getLogFacade()->criarLog($erroMsg, $parametros);
                $mensagem          = "Não foi possível prosseguir com o pagamento, possivelmente o pagamento já sendo executado por outra pessoa. Tente novamente.";
                $deveExecutarFlush = false;
                break;
            }//end if
        }//end foreach

        if ($deveExecutarFlush === true) {
            $this->chequeFacade->flush($mensagem);
        }

        if (empty($mensagem) === false) {
            return ResponseFactory::badRequest([], $mensagem);
        }

        return ResponseFactory::noContent([]);
    }

    /**
     *
     * @SWG\Patch(
     *     path="/api/cheque/devolver-cheques",
     *     summary="Realiza devolucao de um cheque",
     *     description="Atualiza um cheque no banco",
     *     consumes={"application/x-www-form-urlencoded"},
     *     produces={"application/json"},
     * @SWG\Response(
     *         response="204",
     *         description="Retorna Cheque devolvido com sucesso"
     *     ),
     * @SWG\Response(
     *         response="400",
     *         description="Ocorreu algum erro no servidor",
     *     )
     * )
     *
     * @FOSRest\RequestParam(name="franqueada",              strict=true, nullable=false, description="Franqueada", requirements="\d+")
     * @FOSRest\RequestParam(name="ids",                     strict=true, nullable=false, description="Cheques selecionados", requirements="\d+", map=true)
     * @FOSRest\RequestParam(name="motivo_devolucao_cheque", strict=true, nullable=false, description="ID do Motivo Devolucao Cheque", requirements="\d+")
     * @FOSRest\RequestParam(name="data_devolucao",          strict=false, nullable=false, allowBlank=false, description="Data de devoluçao do cheque")
     *
     * @FOSRest\Patch("/cheque/devolver-cheques")
     *
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function devolverCheque(ParamFetcher $request, Request $requestHeader)
    {
        $mensagem   = "";
        $parametros = $request->all();
        $parametrosMovimentosConta = [];
        $retorno = $this->chequeFacade->devolverCheque($mensagem, $parametros, $parametrosMovimentosConta, false);
        $usuario = $requestHeader->headers->get('Authorization-User-ID');

        foreach ($parametrosMovimentosConta as $parameters) {
            $movimentoContaORM = $this->movimentoContaFacade->buscarPorParametros($parameters);
            if (is_null($movimentoContaORM) === false) {
                $parametrosMov = [
                    ConstanteParametros::CHAVE_NAO_ALTERAR_CHEQUE => true,
                    ConstanteParametros::CHAVE_FRANQUEADA         => $request->get(ConstanteParametros::CHAVE_FRANQUEADA),
                    ConstanteParametros::CHAVE_USUARIO            => $usuario,
                    ConstanteParametros::CHAVE_MC_DATA_ESTORNO    => (new \DateTime())->setTime(12, 0, 0)->format('Y-m-d\TH:i:s.uP'),
                    ConstanteParametros::CHAVE_OBSERVACAO         => "Devolução de cheque (nº {$parameters['numero_documento']})",
                ];

                $this->movimentoContaFacade->estornar($mensagem, $parametrosMov, $movimentoContaORM, false);
            }
        }

        if (empty($mensagem) === true) {
            $this->chequeFacade->flush($mensagem);
        }

        if (empty($mensagem) === false) {
            return ResponseFactory::badRequest([], $mensagem);
        }

        return ResponseFactory::noContent([]);
    }

    /**
     *
     * @SWG\Post(
     *     path="/api/cheque/excluir-multiplos",
     *     summary="Remove multiplos cheque",
     *     description="Remove multiplos cheque no banco",
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
     * @FOSRest\RequestParam(name="ids", strict=true, nullable=false, description="Cheques selecionados", requirements="\d+", map=true)
     *
     * @FOSRest\Post("/cheque/excluir-multiplos")
     *
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function excluirVariosCheques(ParamFetcher $request)
    {
        $mensagem = "";
        $ids      = $request->get(ConstanteParametros::CHAVE_IDS, true);
        $retorno  = $this->chequeFacade->removerMultiplos($mensagem, $ids);
        if ($retorno === false) {
            return ResponseFactory::badRequest([], $mensagem);
        }

        return ResponseFactory::ok([], "Excluido com sucesso");
    }


}
