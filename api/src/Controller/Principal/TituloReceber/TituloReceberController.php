<?php

namespace App\Controller\Principal\TituloReceber;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use FOS\RestBundle\Controller\Annotations as FOSRest;
use Swagger\Annotations as SWG;
use App\Factory\ResponseFactory;
use App\Controller\Principal\Base\GenericController;
use Symfony\Component\HttpFoundation\Request;
use FOS\RestBundle\Request\ParamFetcher;
use App\Helper\ConstanteParametros;
use App\Helper\SituacoesSistema;
use App\Helper\LockHelper;
use App\Facade\Principal\TituloReceberFacade;
use App\Facade\Principal\FranqueadaFacade;
use App\Facade\Principal\MovimentoContaFacade;
use App\Facade\Principal\TipoMovimentoContaFacade;
use App\Facade\Principal\FormaPagamentoFacade;
use PhpParser\Builder\Param;

/**
 *
 * @author        Luiz A Costa
 * @Route("/api")
 */
class TituloReceberController extends GenericController
{


    /**
     *
     * @var \App\Facade\Principal\TituloReceberFacade
     */
    private $tituloReceberFacade;

    /**
     *
     * @var \App\Facade\Principal\FranqueadaFacade
     */
    private $franqueadaFacade;

    /**
     *
     * @var \App\Facade\Principal\MovimentoContaFacade
     */
    private $movimentoContaFacade;

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
        $this->tituloReceberFacade      = new TituloReceberFacade(self::getManagerRegistry());
        $this->franqueadaFacade         = new FranqueadaFacade(self::getManagerRegistry());
        $this->movimentoContaFacade     = new MovimentoContaFacade(self::getManagerRegistry());
        $this->tipoMovimentoContaFacade = new TipoMovimentoContaFacade(self::getManagerRegistry());
        $this->formaPagamentoFacade     = new FormaPagamentoFacade(self::getManagerRegistry());
        $this->lockHelper = new LockHelper();
    }

    /**
     *
     * @SWG\Patch(
     *     path="/api/titulo_receber/atualizar/{id}",
     *     summary="Atualiza um titulo_receber",
     *     description="Atualiza um titulo_receber no banco",
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
     * @FOSRest\RequestParam(name="franqueada",             strict=true, nullable=false, allowBlank=false, requirements="\d+", description="Franqueada")
     * @FOSRest\RequestParam(name="conta",                  strict=false, nullable=true, description="Conta", requirements="\d+")
     * @FOSRest\RequestParam(name="forma_pagamento",        strict=true, nullable=false, allowBlank=false, requirements="\d+", description="Forma de cobrança")
     * @FOSRest\RequestParam(name="data_recebimento",       strict=false, nullable=true, description="Data de recebimento (bom para)")
     * @FOSRest\RequestParam(name="valor_lancamento",       strict=true, nullable=false, allowBlank=false, description="Valor do cheque")
     * @FOSRest\RequestParam(name="valor_montante",         strict=true, nullable=false, description="Valor do montante", requirements="^-?\d{0,13}+\.?\d{0,2}?$")
     * @FOSRest\RequestParam(name="valor_juros",            strict=false, nullable=true, description="Valor de juros", requirements="^-?\d{0,7}+\.?\d{0,2}?$")
     * @FOSRest\RequestParam(name="valor_multa",            strict=false, nullable=true, description="Valor de multa", requirements="^-?\d{0,13}+\.?\d{0,2}?$")
     * @FOSRest\RequestParam(name="valor_desconto",         strict=false, nullable=true, description="Valor de desconto", requirements="^-?\d{0,7}+\.?\d{0,2}?$")
     * @FOSRest\RequestParam(name="valor_diferenca_baixa",  strict=false, nullable=true, description="Valor baixado", requirements="^-?\d{0,13}+\.?\d{0,2}?$")
     * @FOSRest\RequestParam(name="observacao",             strict=false, allowBlank=true, description="Observação", requirements=".*")
     * @FOSRest\RequestParam(name="cheque",                 strict=true, nullable=true, allowBlank=true, description="Dados do cheque", map=true)
     * @FOSRest\RequestParam(name="boleto",                 strict=true, nullable=true, allowBlank=true, description="Dados do boleto", map=true)
     * @FOSRest\RequestParam(name="transacao_cartao",       strict=true, nullable=true, allowBlank=true, description="Dados da transação cartão", map=true)
     * @FOSRest\RequestParam(name="transferencia_bancaria", strict=true, nullable=true, allowBlank=true, description="Dados da transação cartão", map=true)
     *
     * @FOSRest\Patch("/titulo_receber/atualizar/{id}")
     *
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function atualizar($id, ParamFetcher $request, Request $requestHeader)
    {
        $parametros      = $request->all();
        $mensagem        = "";
        $mensagemSucesso = "";

        $formaPagamento = $this->formaPagamentoFacade->buscarPorId($mensagem, $parametros[ConstanteParametros::CHAVE_FORMA_PAGAMENTO], true);
        
        if ($formaPagamento->getFormaCartao()) {
            if (empty($parametros['transacao_cartao']['identificador']) ||
                    empty($parametros['transacao_cartao']['operadora_cartao']) || empty($parametros['transacao_cartao']['parcelamento_operadora_cartao'])) {
                
               return ResponseFactory::conflict(["parametros" => $parametros], "ERRO! Você precisa preencher todos dados do Cartão!");
                die;
            }
        }

        if ($formaPagamento->getFormaCheque()) {
            if (empty($parametros['cheque']['banco']) || empty($parametros['cheque']['numero']) || empty($parametros['cheque']['conta']) ||
                    empty($parametros['cheque']['titular']) || empty($parametros['cheque']['agencia'])) {
                
               return ResponseFactory::conflict(["parametros" => $parametros], "ERRO! Você precisa preencher todos dados do Cheque!");
                die;
            }
        }
        
        $usuarioID = $requestHeader->headers->get('Authorization-User-ID');
        $parametros[ConstanteParametros::CHAVE_ATENDENTE_USUARIO] = $usuarioID;

        if (is_null($parametros[ConstanteParametros::CHAVE_DATA_RECEBIMENTO]) === true) {
            $parametros[ConstanteParametros::CHAVE_DATA_RECEBIMENTO] = (new \DateTime())->format("Y-m-d\TH:i:s.uP");
        }

        $tituloAtualizadoComSucesso = $this->tituloReceberFacade->atualizar($mensagem, $mensagemSucesso, $id, $parametros, $tituloReceberORM, $transacaoCartaoORM);

        $formaPagamento = $this->formaPagamentoFacade->buscarPorId($mensagem, $parametros[ConstanteParametros::CHAVE_FORMA_PAGAMENTO], true);

        if (($tituloAtualizadoComSucesso === true) && ((float) $tituloReceberORM->getValorSaldoDevedor() === 0.0)) {
            $tituloReceberORM->setSituacao(SituacoesSistema::SITUACAO_LIQUIDADO);
            $this->tituloReceberFacade->flush($mensagem);
        } else if (($tituloAtualizadoComSucesso === true) && ($formaPagamento->getLiquidacaoImediata() === true)) {
            $parametros[ConstanteParametros::CHAVE_USUARIO] = $usuarioID;

            if (empty($parametros[ConstanteParametros::CHAVE_CONTA]) === true) {
                $franqueadaORM = $this->franqueadaFacade->buscarFranqueadaEContaPadrao($parametros[ConstanteParametros::CHAVE_FRANQUEADA]);
                $parametros[ConstanteParametros::CHAVE_CONTA] = $franqueadaORM->getContaPadrao()->getId();
            }

            $this->lockHelper->setLock('movimento_conta_conta_' . $parametros[ConstanteParametros::CHAVE_CONTA]);
            if ($this->lockHelper->getLock()->isAcquired() === false) {
                $this->lockHelper->getLock()->acquire(true);

                $parametros[ConstanteParametros::CHAVE_TITULO_PAGAR]   = null;
                $parametros[ConstanteParametros::CHAVE_TITULO_RECEBER] = $tituloReceberORM->getId();

                $parametros[ConstanteParametros::CHAVE_TIPO_MOVIMENTO_CONTA] = $this->tipoMovimentoContaFacade->buscarPorPropriedades([ConstanteParametros::CHAVE_MC_TIPO_OPERACAO => SituacoesSistema::OPERACAO_CREDITO]);
                $parametros[ConstanteParametros::CHAVE_MC_DATA_CONTABIL]     = $parametros[ConstanteParametros::CHAVE_DATA_RECEBIMENTO];
                $parametros[ConstanteParametros::CHAVE_MC_DATA_DEPOSITO]     = $parametros[ConstanteParametros::CHAVE_DATA_RECEBIMENTO];

                $deveGravar        = false;
                $movimentoContaORM = $this->movimentoContaFacade->criar($mensagem, $parametros, $deveGravar, $transacaoCartaoORM);

                if ($formaPagamento->getFormaTransferencia() === true) {
                    $transferencias = $tituloReceberORM->getTransferenciasBancaria()->filter(
                        function ($transferencia) {
                            return $transferencia->getSituacao() === SituacoesSistema::SITUACAO_PENDENTE;
                        }
                    );
                    foreach ($transferencias as $transferenciaORM) {
                        $transferenciaORM->setMovimentoConta($movimentoContaORM);
                        $transferenciaORM->setSituacao(SituacoesSistema::SITUACAO_CREDITADO);
                    }
                }

                $this->tituloReceberFacade->flush($mensagem);

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
        } else {
            $this->tituloReceberFacade->flush($mensagem);
        }//end if

        if (($tituloAtualizadoComSucesso === false) || (empty($mensagem) === false)) {
            return ResponseFactory::badRequest(["parametros" => $parametros], $mensagem);
        }

        return ResponseFactory::ok([], $mensagemSucesso);
    }


    private function converteParametros( &$parametros, &$mensagem): bool{

        if (is_null($parametros[ConstanteParametros::CHAVE_DATA_RECEBIMENTO]) === true) {            
            
            $parametros[ConstanteParametros::CHAVE_DATA_RECEBIMENTO] = (new \DateTime())->format("Y-m-d\TH:i:s.uP");

        }
        $parametros[ConstanteParametros::CHAVE_FORMA_PAGAMENTO] = $parametros['forma_pagamento_id'];

        $parametros[ConstanteParametros::CHAVE_CONTA] = $parametros['conta_id'];

        if (is_null($parametros[ConstanteParametros::CHAVE_MC_VALOR_DESCONTO]) === true) {                        
            $parametros[ConstanteParametros::CHAVE_MC_VALOR_DESCONTO] = 0;
        }
        if (is_null($parametros[ConstanteParametros::CHAVE_MC_VALOR_JUROS]) === true) {                        
            $parametros[ConstanteParametros::CHAVE_MC_VALOR_JUROS] = 0;
        }
        if (is_null($parametros[ConstanteParametros::CHAVE_MC_VALOR_MULTA]) === true) {                        
            $parametros[ConstanteParametros::CHAVE_MC_VALOR_MULTA] = 0;
        }
       


        return true;
    
    }

    private function validarParametros( $parametros, &$mensagem): bool{

        $formaPagamento = $this->formaPagamentoFacade->buscarPorId($mensagem, $parametros['forma_pagamento_id'], true);


        if ($formaPagamento->getFormaCartao()) {
            if (empty($parametros['transacao_cartao']['identificador']) ||
                    empty($parametros['transacao_cartao']['operadora_cartao']) || empty($parametros['transacao_cartao']['parcelamento_operadora_cartao'])) {
                        $mensagem = "ERRO! Você precisa preencher todos dados do Cartão!";
                        return false;                
            }
        }

        if ($formaPagamento->getFormaCheque()) {
            if (empty($parametros['cheque']['banco']) || empty($parametros['cheque']['numero']) || empty($parametros['cheque']['conta']) ||
                    empty($parametros['cheque']['titular']) || empty($parametros['cheque']['agencia'])) {
                        $mensagem =  "ERRO! Você precisa preencher todos dados do Cheque!";
                        return false;
            }
        }
        return true;
    }

    private function alimentarPagamentos( &$parametros, &$mensagem): bool{

        if (is_null($parametros[ConstanteParametros::CHAVE_DATA_RECEBIMENTO]) === true) {            
            
            $parametros[ConstanteParametros::CHAVE_DATA_RECEBIMENTO] = (new \DateTime())->format("Y-m-d\TH:i:s.uP");

        }
        $parametros[ConstanteParametros::CHAVE_FORMA_PAGAMENTO] = $parametros['forma_pagamento_id'];

        $parametros[ConstanteParametros::CHAVE_CONTA] = $parametros['conta_id'];

        if (is_null($parametros[ConstanteParametros::CHAVE_MC_VALOR_DESCONTO]) === true) {                        
            $parametros[ConstanteParametros::CHAVE_MC_VALOR_DESCONTO] = 0;
        }
        if (is_null($parametros[ConstanteParametros::CHAVE_MC_VALOR_JUROS]) === true) {                        
            $parametros[ConstanteParametros::CHAVE_MC_VALOR_JUROS] = 0;
        }
        if (is_null($parametros[ConstanteParametros::CHAVE_MC_VALOR_MULTA]) === true) {                        
            $parametros[ConstanteParametros::CHAVE_MC_VALOR_MULTA] = 0;
        }
       


        return true;
    
    }



     /**
     *     
     *
     * @FOSRest\RequestParam(name="franqueada",             strict=true, nullable=false, allowBlank=false, requirements="\d+", description="Franqueada")
     * @FOSRest\RequestParam(name="conta_id",                  strict=false, nullable=true, description="Conta", requirements="\d+")
     * @FOSRest\RequestParam(name="forma_pagamento_id",        strict=true, nullable=false, allowBlank=false, requirements="\d+", description="Forma de cobrança")
     * @FOSRest\RequestParam(name="data_recebimento",       strict=false, nullable=true, description="Data de recebimento (bom para)")
     * @FOSRest\RequestParam(name="valor_lancamento",       strict=true, nullable=false, allowBlank=false, description="Valor do cheque")
     * @FOSRest\RequestParam(name="valor_multa",            strict=false, nullable=true, description="Valor de multa", requirements="^-?\d{0,13}+\.?\d{0,2}?$")
     * @FOSRest\RequestParam(name="valor_juros",            strict=false, nullable=true, description="Valor de juros", requirements="^-?\d{0,13}+\.?\d{0,2}?$")
     * @FOSRest\RequestParam(name="valor_desconto",         strict=false, nullable=true, description="Valor de desconto", requirements="^-?\d{0,7}+\.?\d{0,2}?$")
     * @FOSRest\RequestParam(name="valor_desconto_manual",         strict=false, nullable=true, description="Valor de desconto manual", requirements="^-?\d{0,7}+\.?\d{0,2}?$")
     * @FOSRest\RequestParam(name="motivo_desconto_manual",         strict=false, nullable=true, description="Motivo de desconto manual", requirements=".*")
     * @FOSRest\RequestParam(name="observacao",             strict=false, allowBlank=true, description="Observação", requirements=".*")
     * @FOSRest\RequestParam(name="cheque",                 strict=true, nullable=true, allowBlank=true, description="Dados do cheque", map=true)
     * @FOSRest\RequestParam(name="boleto",                 strict=true, nullable=true, allowBlank=true, description="Dados do boleto", map=true)
     * @FOSRest\RequestParam(name="transacao_cartao",       strict=true, nullable=true, allowBlank=true, description="Dados da transação cartão", map=true)
     * @FOSRest\RequestParam(name="transferencia_bancaria", strict=true, nullable=true, allowBlank=true, description="Dados da transação cartão", map=true)
     *
     * @FOSRest\Post("/titulo_receber/receber/{id}")
     *
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function receber($id, ParamFetcher $request, Request $requestHeader)
    {
        $parametros      = $request->all();
        $mensagem        = "";
        $mensagemSucesso = "Operação de recebimento concluída com sucesso!";

        try {
            self::getManagerRegistry()->getConnection()->beginTransaction();

            $usuarioID = $requestHeader->headers->get('Authorization-User-ID');
            $parametros[ConstanteParametros::CHAVE_ATENDENTE_USUARIO] = $usuarioID;

            if($this->validarParametros( $parametros, $mensagem) === false){
                return ResponseFactory::conflict(["parametros" => $parametros], $mensagem);                
            }
            if($this->converteParametros( $parametros, $mensagem) === false){
                return ResponseFactory::conflict(["parametros" => $parametros], $mensagem);
            };

          
            

            $formaPagamento = $this->formaPagamentoFacade->buscarPorId($mensagem, $parametros['forma_pagamento_id'], true);
            $tituloReceberORM = $this->tituloReceberFacade->buscarPorID($mensagem, $id);

            //CALCULA E VALIDA VALORES
            $saldo_devedor_atual = $tituloReceberORM->getValorSaldoDevedor();
            $valor = $parametros[ConstanteParametros::CHAVE_MC_VALOR_LANCAMENTO];
            $saldo_devedor_final = $saldo_devedor_atual - $valor;

            $saldo_desconto = $parametros[ConstanteParametros::CHAVE_MC_VALOR_DESCONTO];
            $saldo_juros = $parametros[ConstanteParametros::CHAVE_MC_VALOR_JUROS];
            $saldo_multa = $parametros[ConstanteParametros::CHAVE_MC_VALOR_MULTA];

            if (isset( $parametros[ConstanteParametros::CHAVE_TIT_VALOR_DESCONTO_MANUAL])) {
                $tituloReceberORM->setValor_desconto_manual($parametros[ConstanteParametros::CHAVE_TIT_VALOR_DESCONTO_MANUAL]);
            }
            if (isset( $parametros[ConstanteParametros::CHAVE_TIT_MOTIVO_DESCONTO_MANUAL])) {
                $tituloReceberORM->setMotivo_desconto_manual($parametros[ConstanteParametros::CHAVE_TIT_MOTIVO_DESCONTO_MANUAL]);
            }

            if($saldo_devedor_final < 0){
                $saldo_devedor_final = 0;
            }

            if($this->alimentarPagamentos( $parametros, $mensagem) === false){
                return ResponseFactory::conflict(["parametros" => $parametros], $mensagem);
            };
            
               //gravar informações no titulo.
            $tituloAtualizadoComSucesso = $this->tituloReceberFacade->atualizar($mensagem, $mensagemSucesso, $id, $parametros, $tituloReceberORM, $transacaoCartaoORM);

            if ($tituloAtualizadoComSucesso === true)
            {
                if($tituloReceberORM->getValorSaldoDevedor() <= 0){
                    $tituloReceberORM->setValorSaldoDevedor($saldo_devedor_final);
                }
            }

            //gera movimento de conta.
            $parametros[ConstanteParametros::CHAVE_TITULO_PAGAR]   = null;
            $parametros[ConstanteParametros::CHAVE_TITULO_RECEBER] = $tituloReceberORM->getId();
            // $totalLancamento = $parametros[ConstanteParametros::CHAVE_MC_VALOR_LANCAMENTO] + $saldo_juros + $saldo_multa - $saldo_desconto;
            $totalLancamento = $parametros[ConstanteParametros::CHAVE_MC_VALOR_LANCAMENTO] ;
            $parametros[ConstanteParametros::CHAVE_MC_VALOR_MONTANTE] = $totalLancamento;
            $parametros[ConstanteParametros::CHAVE_TIPO_MOVIMENTO_CONTA] = $this->tipoMovimentoContaFacade->buscarPorPropriedades([ConstanteParametros::CHAVE_MC_TIPO_OPERACAO => SituacoesSistema::OPERACAO_CREDITO]);
            $parametros[ConstanteParametros::CHAVE_MC_DATA_CONTABIL]     = $parametros[ConstanteParametros::CHAVE_DATA_RECEBIMENTO];
            $parametros[ConstanteParametros::CHAVE_MC_DATA_DEPOSITO]     = $parametros[ConstanteParametros::CHAVE_DATA_RECEBIMENTO];

            
            $deveGravar        = false;
            $movimentoContaORM = $this->movimentoContaFacade->criar($mensagem, $parametros, $deveGravar, $transacaoCartaoORM);

            if($movimentoContaORM === null){
                return ResponseFactory::badRequest([], $mensagem);
                
            }


             //verifica conciliaçao automatica
            if($formaPagamento->getLiquidacaoImediata()){
                if(((float) $tituloReceberORM->getValorSaldoDevedor() <= 0.0)){
                    $tituloReceberORM->setSituacao(SituacoesSistema::SITUACAO_LIQUIDADO);
                }
            }
            


       
        
            $this->tituloReceberFacade->flush($mensagem);
            self::getManagerRegistry()->getConnection()->commit();
            // self::getManagerRegistry()->getConnection()->rollback();

        } catch (\Exception $e) {

            self::getManagerRegistry()->getConnection()->rollback();
            $mensagem = "ERRO! Não foi possível realizar a operação. ".$mensagem ." - ". $e->getMessage();
            return ResponseFactory::conflict(["parametros" => $parametros], $mensagem);                
        }


        
        

        

        

     
       

        // $parametros[ConstanteParametros::CHAVE_TITULO_PAGAR]   = null;
        //         $parametros[ConstanteParametros::CHAVE_TITULO_RECEBER] = $tituloReceberORM->getId();

        //         $parametros[ConstanteParametros::CHAVE_TIPO_MOVIMENTO_CONTA] = $this->tipoMovimentoContaFacade->buscarPorPropriedades([ConstanteParametros::CHAVE_MC_TIPO_OPERACAO => SituacoesSistema::OPERACAO_CREDITO]);
        //         $parametros[ConstanteParametros::CHAVE_MC_DATA_CONTABIL]     = $parametros[ConstanteParametros::CHAVE_DATA_RECEBIMENTO];
        //         $parametros[ConstanteParametros::CHAVE_MC_DATA_DEPOSITO]     = $parametros[ConstanteParametros::CHAVE_DATA_RECEBIMENTO];

        //         $deveGravar        = false;
        //         $movimentoContaORM = $this->movimentoContaFacade->criar($mensagem, $parametros, $deveGravar, $transacaoCartaoORM);

        //         if ($formaPagamento->getFormaTransferencia() === true) {
        //             $transferencias = $tituloReceberORM->getTransferenciasBancaria()->filter(
        //                 function ($transferencia) {
        //                     return $transferencia->getSituacao() === SituacoesSistema::SITUACAO_PENDENTE;
        //                 }
        //             );
        //             foreach ($transferencias as $transferenciaORM) {
        //                 $transferenciaORM->setMovimentoConta($movimentoContaORM);
        //                 $transferenciaORM->setSituacao(SituacoesSistema::SITUACAO_CREDITADO);
        //             }
        //         }

        //         $this->tituloReceberFacade->flush($mensagem);

        //         if ((is_null($movimentoContaORM) === true) || (empty($mensagem) === false)) {
        //             $this->lockHelper->getLock()->release();
        //             return ResponseFactory::conflict(["parametros" => $parametros], $mensagem);
        //         }

        //         $this->lockHelper->getLock()->release();
        //         return ResponseFactory::created(["movimentoContaORM" => $movimentoContaORM->getId()], "Registro criado com sucesso!");

                

        return ResponseFactory::ok([], $mensagemSucesso);
    }

    /**
     *
     * @SWG\Get(
     *     path="/api/titulo_receber/consulta_renegociacao",
     *     summary="Consulta títulos para renegociar",
     *     description="Consulta títulos para renegociar no banco",
     *     consumes={"application/x-www-form-urlencoded"},
     *     produces={"application/json"},
     * @SWG\Response(
     *         response="200",
     *         description="Retorna resultados"
     *     ),
     * @SWG\Response(
     *         response="400",
     *         description="Ocorreu algum erro no servidor",
     *     )
     * )
     *
     * @FOSRest\QueryParam(name="titulo_receber", strict=true, nullable=false, allowBlank=false, description="Títulos a serem renegociados", map=true)
     *
     * @FOSRest\Get("/titulo_receber/consulta_renegociacao")
     *
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function consultaRenegociacao(ParamFetcher $request)
    {
        $parametros = $request->all();
        $mensagem   = "";

        $titulosRenegociacao = $this->tituloReceberFacade->consultaTitulosParaRenegociacao($parametros, $mensagem);
        if (empty($mensagem) === false) {
            return ResponseFactory::badRequest($parametros, $mensagem);
        }

        return ResponseFactory::ok($titulosRenegociacao, $mensagem);
    }

    /**
     *
     * @SWG\Get(
     *     path="/api/titulo_receber/buscar_pessoas_aluno_ou_sacado/{nome_pessoa}",
     *     summary="Busca aluno ou sacado",
     *     description="Busca aluno ou sacado (caso não haja aluno)",
     *     consumes={"application/x-www-form-urlencoded"},
     *     produces={"application/json"},
     * @SWG\Response(
     *         response="200",
     *         description="Retorna resultados"
     *     ),
     * @SWG\Response(
     *         response="400",
     *         description="Ocorreu algum erro no servidor",
     *     )
     * )
     *
     * @FOSRest\QueryParam(name="franqueada", strict=true, allowBlank=false, nullable=false, description="Id Franqueada",requirements="\d+")
     *
     * @FOSRest\Get("/titulo_receber/buscar_pessoas_aluno_ou_sacado/{nome_pessoa}")
     *
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function buscarPessoasAlunoOuSacado($nome_pessoa, ParamFetcher $request)
    {
        $parametros = $request->all();
        $mensagem   = "";
        $parametros[ConstanteParametros::CHAVE_NOME_PESSOA] = $nome_pessoa;

        $titulosRenegociacao = $this->tituloReceberFacade->buscarPessoas($mensagem, $parametros);
        if (empty($mensagem) === false) {
            return ResponseFactory::badRequest($parametros, $mensagem);
        }

        return ResponseFactory::ok($titulosRenegociacao, $mensagem);
    }

    /**
     *
     * @SWG\Post(
     *     path="/api/titulo_receber/cancelar",
     *     summary="Cancela os titulos enviados",
     *     description="Cancela os titulos enviados (os que estiverem pendentes)",
     *     consumes={"application/x-www-form-urlencoded"},
     *     produces={"application/json"},
     * @SWG\Response(
     *         response="200",
     *         description="Retorna resultados"
     *     ),
     * @SWG\Response(
     *         response="400",
     *         description="Ocorreu algum erro no servidor",
     *     )
     * )
     *
     * @FOSRest\RequestParam(name="franqueada",         strict=true, allowBlank=false, nullable=false, description="Id Franqueada",requirements="\d+")
     *
     * @FOSRest\RequestParam(name="usuario",            strict=true, nullable=false, allowBlank=false, description="ID do usuario", requirements="\d+")
     *
     * @FOSRest\RequestParam(name="titulos_receber",    strict=true, nullable=false, allowBlank=false, description="Array contendo os ids dos titulos", map=true)
     *
     * @FOSRest\Post("/titulo_receber/cancelar")
     *
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function cancelarTitulosReceber(ParamFetcher $request)
    {
        $parametros = $request->all();
        $mensagem   = "";

        $titulosRenegociacao = $this->tituloReceberFacade->cancelarTitulosReceber($mensagem, $parametros[ConstanteParametros::CHAVE_TITULOS_RECEBER], $parametros[ConstanteParametros::CHAVE_USUARIO]);
 
        if (empty($mensagem) === false) {
            return ResponseFactory::badRequest($parametros, $mensagem);
        }

        $this->tituloReceberFacade->flush($mensagem);

        if ((empty($mensagem) === false)) {
            return ResponseFactory::badRequest(["parametros" => $parametros], $mensagem);
        }

        return ResponseFactory::ok($titulosRenegociacao, $mensagem);
    }

    /**
     *
     * @SWG\Get(
     *     path="/api/titulo_receber/{id}",
     *     summary="Busca os detalhes de um titulo",
     *     description="Busca do titulo através da ID",
     *     produces={"application/json"},
     * @SWG\Response(
     *         response="200",
     *         description="Retorna detalhes de um titulo"
     *     ),
     * @SWG\Response(
     *         response="404",
     *         description="Ocorreu algum erro no servidor",
     *     )
     * )
     *
     * @FOSRest\Get("/titulo_receber/{id}")
     *
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function buscar($id)
    {
        $mensagemErro = "";
        $objetoORM    = $this->tituloReceberFacade->buscarDetalhesPorId($id);
        if (is_null($objetoORM) === true) {
            return ResponseFactory::notFound([], $mensagemErro);
        }

        return ResponseFactory::ok($objetoORM);
    }

    /**
     *
     * @SWG\Get(
     *     path="/api/titulo_receber_movimentos/{id}",
     *     summary="Busca os detalhes de um titulo",
     *     description="Busca movimentos através da ID",
     *     produces={"application/json"},
     * @SWG\Response(
     *         response="200",
     *         description="Retorna detalhes de um titulo"
     *     ),
     * @SWG\Response(
     *         response="404",
     *         description="Ocorreu algum erro no servidor",
     *     )
     * )
     *
     * @FOSRest\Get("/titulo_receber_movimentos/{id}")
     *
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function buscar_movimentos($id)
    {
        $mensagemErro = "";
        $dados    = $this->tituloReceberFacade->buscarMovimentosTitulo($id);
        if (is_null($dados) === true) {
            return ResponseFactory::notFound([], $mensagemErro);
        }

        return ResponseFactory::ok($dados);
    }

    /**
     *
     * @SWG\Post(
     *     path="/api/titulo_receber/cancelar",
     *     summary="Cancela os titulos enviados",
     *     description="Cancela os titulos enviados (os que estiverem pendentes)",
     *     consumes={"application/x-www-form-urlencoded"},
     *     produces={"application/json"},
     * @SWG\Response(
     *         response="200",
     *         description="Retorna resultados"
     *     ),
     * @SWG\Response(
     *         response="400",
     *         description="Ocorreu algum erro no servidor",
     *     )
     * )
     *
     * @FOSRest\RequestParam(name="valor_desconto",   strict=false, nullable=false, description="Valor de desconto", requirements="^-?\d{0,7}+\.?\d{0,2}?$")
     * @FOSRest\RequestParam(name="motivo",           strict=false, allowBlank=false, description="Motivo", requirements=".*")
     
     *
     * @FOSRest\Post("/titulo_receber/aplica_desconto_manual/{id}")
     *
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function aplicaDescontoManual($id,ParamFetcher $request)
    {
        $parametros = $request->all();
        $mensagem   = "";

        $titulo = $this->tituloReceberFacade->buscarPorID($mensagem, $id);
        
        //verifica se o usuário tem permissão para alterar o titulo

        $this->tituloReceberFacade->aplicaDescontoManual($mensagem,$titulo, $parametros[ConstanteParametros::CHAVE_TIT_VALOR_DESCONTO_MANUAL],$parametros[ConstanteParametros::CHAVE_TIT_MOTIVO_DESCONTO_MANUAL]);
        
        
 
        if (empty($mensagem) === false) {
            return ResponseFactory::badRequest($parametros, $mensagem);
        }

        $this->tituloReceberFacade->flush($mensagem);

        if ((empty($mensagem) === false)) {
            return ResponseFactory::badRequest(["parametros" => $parametros], $mensagem);
        }

        return ResponseFactory::ok($titulo, $mensagem);
    }
}
