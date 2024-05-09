<?php

namespace App\Facade\Principal;

use App\Facade\Principal\GenericFacade;
use App\Helper\SituacoesSistema;
use App\Helper\ConstanteParametros;
use Doctrine\Common\Persistence\ManagerRegistry;
use App\BO\Principal\TituloReceberBO;
use App\BO\Principal\FormaPagamentoBO;
use App\Facade\Principal\ChequeFacade;
use App\Facade\Principal\TransacaoCartaoFacade;
use App\Facade\Principal\TransferenciaBancariaFacade;
use App\Facade\Principal\BoletoFacade;
use App\Helper\VariaveisCompartilhadas;

/**
 *
 * @author Luiz A Costa
 */
class TituloReceberFacade extends GenericFacade
{

    /**
     *
     * @var \App\Repository\Principal\TituloReceberRepository
     */
    private $tituloReceberRepository;

    /**
     *
     * @var \App\Repository\Principal\MovimentoContaRepository
     */
    private $movimentoContaRepository;

     /**
     *
     * @var \App\Repository\Principal\UsuarioRepository
     */
    private $usuarioRepository;

    /**
     *
     * @var \App\Repository\Principal\FormaPagamentoRepository
     */
    private $formaPagamentoRepository;

    /**
     *
     * @var \App\Repository\Principal\FranqueadaRepository
     */
    private $franqueadaRepository;

    /**
     *
     * @var \App\Repository\Principal\ContaRepository
     */
    private $contaRepository;

    /**
     *
     * @var \App\BO\Principal\TituloReceberBO
     */
    private $tituloReceberBO;

    /**
     *
     * @var \App\Facade\Principal\ChequeFacade
     */
    private $chequeFacade;

    /**
     *
     * @var \App\Facade\Principal\BoletoFacade
     */
    private $boletoFacade;

    /**
     *
     * @var \App\Facade\Principal\TransacaoCartaoFacade
     */
    private $transacaoCartaoFacade;

    /**
     *
     * @var \App\Facade\Principal\TransferenciaBancariaFacade
     */
    private $transferenciaBancariaFacade;


       /**
     *
     * @var \App\Repository\Principal\BoletoRepository
     */
    private $boletoRepository;


    /**
     * {@inheritDoc}
     *
     * @see \App\Facade\Principal\GenericFacade::__construct()
     */
    function __construct (ManagerRegistry $managerRegistry, $connection="base_principal")
    {
        parent::__construct($managerRegistry);
        $this->tituloReceberRepository  = self::getEntityManager()->getRepository(\App\Entity\Principal\TituloReceber::class);
        $this->movimentoContaRepository = self::getEntityManager()->getRepository(\App\Entity\Principal\MovimentoConta::class);
        $this->formaPagamentoRepository = self::getEntityManager()->getRepository(\App\Entity\Principal\FormaPagamento::class);
        $this->franqueadaRepository     = self::getEntityManager()->getRepository(\App\Entity\Principal\Franqueada::class);
        $this->contaRepository          = self::getEntityManager()->getRepository(\App\Entity\Principal\Conta::class);
        $this->usuarioRepository        = self::getEntityManager()->getRepository(\App\Entity\Principal\Usuario::class);

        $this->tituloReceberBO          = new TituloReceberBO(self::getEntityManager());
        $this->chequeFacade          = new ChequeFacade($managerRegistry);
        $this->boletoFacade          = new BoletoFacade($managerRegistry);
        $this->transacaoCartaoFacade = new TransacaoCartaoFacade($managerRegistry);
        $this->transferenciaBancariaFacade = new TransferenciaBancariaFacade($managerRegistry);
        $this->boletoRepository          =  self::getEntityManager()->getRepository(\App\Entity\Principal\Boleto::class);
    }

    /**
     * Busca um objeto no banco de dados
     *
     * @param string $mensagemErro Mensagem que ira retornar para front-end
     * @param integer $id
     *
     * @return mixed|null|\App\Entity\Principal\TituloReceber
     */
    public function buscarPorID(&$mensagemErro, $id)
    {
        $objetoORM = null;
        $this->tituloReceberBO->verificaTituloReceberExisteId($this->tituloReceberRepository, $id, $mensagemErro, $tituloORM);

        return $tituloORM;
    }

    /**
     * Cria um objeto no banco de dados
     *
     * @param string $mensagemErro Mensagem que ira retornar para front-end
     * @param array $parametros Parametros a serem inclusos no objeto
     *
     * @return mixed|null|\App\Entity\Principal\TituloReceber
     */
    public function criar(&$mensagemErro, $parametros=[])
    {
        $objetoORM = null;
        if ($this->tituloReceberBO->podeSalvar($parametros, $mensagemErro) === true) {
            if (isset($parametros[ConstanteParametros::CHAVE_VALOR_DESPESAS]) === true) {
                unset($parametros[ConstanteParametros::CHAVE_VALOR_DESPESAS]);
            }

            $franqueadaORM = $parametros[ConstanteParametros::CHAVE_FRANQUEADA];
            $parametros[ConstanteParametros::CHAVE_TAXA_MULTA]    = $franqueadaORM->getPercentualMulta();
            $parametros[ConstanteParametros::CHAVE_TAXA_JURO_DIA] = $franqueadaORM->getPercentualJuroDia();

            $objetoORM = \App\Factory\GeneralORMFactory::criar(\App\Entity\Principal\TituloReceber::class, true, $parametros);
            self::persistSeguro($objetoORM, $mensagemErro);
        }

        return $objetoORM;
    }

    /**
     * Atualiza um registro no banco de dados
     *
     * @param string $mensagemErro Mensagem que ira retornar para front-end
     * @param string $mensagemSucesso Mensagem que ira retornar para front-end
     * @param int $id Chave primaria do registro
     * @param array $parametros Campos e valores que iram ser atualizados
     * @param \App\Entity\Principal\TituloReceber $tituloReceberORM Objeto do título a receber
     *
     * @return boolean
     */
    public function atualizar(&$mensagemErro, &$mensagemSucesso, $id, &$parametros=[], &$tituloReceberORM=null, &$transacaoCartaoORM=null)
    {
        $tituloReceberORM = $this->tituloReceberRepository->find($id);
        if (is_null($tituloReceberORM) === true) {
            $mensagemErro = "Título a receber não encontrado na base de dados.";
            return false;
        }

        $formaCobrancaORM = null;
        FormaPagamentoBO::verificaFormaPagamentoExiste($this->formaPagamentoRepository, $parametros[ConstanteParametros::CHAVE_FORMA_PAGAMENTO], $mensagemErro, $formaCobrancaORM);

        if (is_null($formaCobrancaORM) === true) {
            $mensagemErro = "Forma de cobrança não encontrada na base de dados.";
            return false;
        }

        $tituloReceberORM->setFormaCobranca($formaCobrancaORM);
        $dataProrrogacao = "";
        \App\Helper\FunctionHelper::formataCampoDateTimeJS($parametros[ConstanteParametros::CHAVE_DATA_RECEBIMENTO], $dataProrrogacao);
        if($dataProrrogacao == false){
            \App\Helper\FunctionHelper::formataCampoDateTime($parametros[ConstanteParametros::CHAVE_DATA_RECEBIMENTO], $dataProrrogacao);
        }
        $tituloReceberORM->setDataProrrogacao($dataProrrogacao);

        $contaORM = $this->contaRepository->find($parametros[ConstanteParametros::CHAVE_CONTA]);
        if (is_null($contaORM) === true) {
            $mensagemErro = "Conta não encontrada na base de dados.";
            return false;
        }

        $tituloReceberORM->setConta($contaORM);

        $this->cancelaPagamentoPendente($mensagemErro,$tituloReceberORM ,$formaCobrancaORM,$parametros);

        if ($formaCobrancaORM->getFormaCheque() === true) {
            $chequeMetadata = $parametros[ConstanteParametros::CHAVE_CHEQUE];

            if ((isset($chequeMetadata[ConstanteParametros::CHAVE_ID]) === false) || (empty($chequeMetadata[ConstanteParametros::CHAVE_ID]) === true)) {
                if ($this->chequeFacade->possuiInformacoesCheque($chequeMetadata) === true) {
                    $chequeMetadata[ConstanteParametros::CHAVE_TITULO_PAGAR]   = null;
                    $chequeMetadata[ConstanteParametros::CHAVE_TITULO_RECEBER] = $tituloReceberORM;
                    $chequeMetadata[ConstanteParametros::CHAVE_PESSOA]         = $tituloReceberORM->getSacadoPessoa();
                    $chequeMetadata[ConstanteParametros::CHAVE_FRANQUEADA]     = $parametros[ConstanteParametros::CHAVE_FRANQUEADA];
                    $chequeMetadata[ConstanteParametros::CHAVE_VALOR]          = $parametros[ConstanteParametros::CHAVE_VALOR_LANCAMENTO];
                    $chequeMetadata[ConstanteParametros::CHAVE_VALOR_DESCONTO] = $parametros[ConstanteParametros::CHAVE_VALOR_DESCONTO];
                    $chequeMetadata[ConstanteParametros::CHAVE_DATA_BOM_PARA]  = $parametros[ConstanteParametros::CHAVE_DATA_RECEBIMENTO];
                    $chequeMetadata[ConstanteParametros::CHAVE_ATENDENTE_USUARIO] = $parametros[ConstanteParametros::CHAVE_ATENDENTE_USUARIO];

                    $chequeORM = $this->chequeFacade->criar($mensagemErro, $chequeMetadata, false);

                    if (empty($mensagemErro) === false) {
                        return false;
                    }

                    $tituloReceberORM->addCheque($chequeORM);
                }//end if
            } else {
                $id = $chequeMetadata[ConstanteParametros::CHAVE_ID];
                $chequeMetadata = [
                    ConstanteParametros::CHAVE_DATA_BOM_PARA  => $parametros[ConstanteParametros::CHAVE_DATA_RECEBIMENTO],
                    ConstanteParametros::CHAVE_VALOR_DESCONTO => $parametros[ConstanteParametros::CHAVE_VALOR_DESCONTO],
                    ConstanteParametros::CHAVE_VALOR          => $parametros[ConstanteParametros::CHAVE_VALOR_LANCAMENTO],
                ];

                $atualizou = $this->chequeFacade->atualizarInformacoes($mensagemErro, $id, $chequeMetadata);

                if ($atualizou === false) {
                    return false;
                }
            }//end if
        } else if ($formaCobrancaORM->getFormaCartao() === true || $formaCobrancaORM->getFormaCartaoDebito() === true) {
            $transacaoCartaoMetadata = $parametros[ConstanteParametros::CHAVE_TRANSACAO_CARTAO];
            if($transacaoCartaoMetadata[ConstanteParametros::CHAVE_ID] === "undefined"){
                unset($transacaoCartaoMetadata[ConstanteParametros::CHAVE_ID]); 
            }
            if ((isset($transacaoCartaoMetadata[ConstanteParametros::CHAVE_ID]) === false) || (empty($transacaoCartaoMetadata[ConstanteParametros::CHAVE_ID]) === true)) {
                if ($this->transacaoCartaoFacade->possuiInformacoesCartao($transacaoCartaoMetadata) === true) {
                    $transacaoCartaoMetadata[ConstanteParametros::CHAVE_TITULO_RECEBER]   = $tituloReceberORM;
                    $transacaoCartaoMetadata[ConstanteParametros::CHAVE_FRANQUEADA]       = $parametros[ConstanteParametros::CHAVE_FRANQUEADA];
                    $transacaoCartaoMetadata[ConstanteParametros::CHAVE_VALOR_LIQUIDO]    = $parametros[ConstanteParametros::CHAVE_VALOR_LANCAMENTO];
                    $transacaoCartaoMetadata[ConstanteParametros::CHAVE_DATA_PAGAMENTO]    = $parametros[ConstanteParametros::CHAVE_DATA_RECEBIMENTO];

                    $dataPagamento = $transacaoCartaoMetadata[ConstanteParametros::CHAVE_DATA_PAGAMENTO];
                    \App\Helper\FunctionHelper::formataCampoDateTimeJS($dataPagamento, $dataPagamento);
                    if($dataPagamento == false){
                        $dataPagamento = $transacaoCartaoMetadata[ConstanteParametros::CHAVE_DATA_PAGAMENTO];
                        \App\Helper\FunctionHelper::formataCampoDateTime($dataPagamento, $dataPagamento);
                    }
                    $p = $transacaoCartaoMetadata[ConstanteParametros::CHAVE_PARCELAMENTO_OPERADORA_CARTAO];
                    $transacaoCartaoMetadata[ConstanteParametros::CHAVE_PREVISAO_REPASSE] = $this->transacaoCartaoFacade->gerarDataPrevisaoRepasse($dataPagamento, $transacaoCartaoMetadata[ConstanteParametros::CHAVE_PARCELAMENTO_OPERADORA_CARTAO], $mensagemErro);
                    $transacaoCartaoMetadata[ConstanteParametros::CHAVE_VALOR_DESCONTO]   = $parametros[ConstanteParametros::CHAVE_VALOR_DESCONTO];

                    if (empty($mensagemErro) === false) {
                        return false;
                    }

                    if ($formaCobrancaORM->getFormaCartaoDebito() === true) {
                        $transacaoCartaoMetadata[ConstanteParametros::CHAVE_TIPO_TRANSACAO] = 'D';
                    }

                    $transacaoCartaoORM = $this->transacaoCartaoFacade->criar($mensagemErro, $transacaoCartaoMetadata, false);

                    if (empty($mensagemErro) === false) {
                        return false;
                    }

                    $tituloReceberORM->addTransacaoCartao($transacaoCartaoORM);
                }//end if
            } else {
                $id = $transacaoCartaoMetadata[ConstanteParametros::CHAVE_ID];
                $transacaoCartaoORM = $this->transacaoCartaoFacade->buscarPorId($mensagemErro, $id, true);

                if (is_null($transacaoCartaoORM) === true) {
                    return false;
                }

                $parcelamentoOperadoraCartaoId = $transacaoCartaoORM->getParcelamentoOperadoraCartao()->getId();
                $transacaoCartaoORM->setValorDesconto($parametros[ConstanteParametros::CHAVE_VALOR_DESCONTO]);
                $transacaoCartaoORM->setValorLiquido($transacaoCartaoMetadata[ConstanteParametros::CHAVE_VALOR_LIQUIDO]);
               
                $dataPagamento = $transacaoCartaoMetadata[ConstanteParametros::CHAVE_DATA_PAGAMENTO];
                \App\Helper\FunctionHelper::formataCampoDateTimeJS($dataPagamento, $dataPagamento);
                if($dataPagamento == false){
                    $dataPagamento = $transacaoCartaoMetadata[ConstanteParametros::CHAVE_DATA_PAGAMENTO];
                    \App\Helper\FunctionHelper::formataCampoDateTime($dataPagamento, $dataPagamento);
                }
                $transacaoCartaoORM->setDataPagamento($dataPagamento);

                $previsaoRepasse = $this->transacaoCartaoFacade->gerarDataPrevisaoRepasse($dataPagamento, $parcelamentoOperadoraCartaoId, $mensagemErro);

                if (empty($mensagemErro) === false) {
                    return false;
                }

                if ($transacaoCartaoORM->getSituacao() === 'PEN') {
                    $atualizou = $this->transacaoCartaoFacade->atualizarPrevisaoRepasse($mensagemErro, $transacaoCartaoORM, $previsaoRepasse);

                    if ($atualizou === false) {
                        return false;
                    }
                }
            }//end if
        } else if ($formaCobrancaORM->getFormaBoleto() === true) {
            $boletoMetadata = $parametros[ConstanteParametros::CHAVE_BOLETO];
            $boletoORM =   null;
            if (isset($boletoMetadata[ConstanteParametros::CHAVE_ID])) {
                $boletoORM = $this->boletoRepository->find($boletoMetadata[ConstanteParametros::CHAVE_ID]);
            }
            if ($boletoORM === null) {
                $boletoMetadata[ConstanteParametros::CHAVE_TITULO_RECEBER] = $tituloReceberORM;
                $boletoMetadata[ConstanteParametros::CHAVE_FRANQUEADA]     = $parametros[ConstanteParametros::CHAVE_FRANQUEADA];
                $boletoMetadata[ConstanteParametros::CHAVE_CONTA]          = $tituloReceberORM->getConta();
                $boletoMetadata[ConstanteParametros::CHAVE_SITUACAO_COBRANCA] = SituacoesSistema::SITUACAO_PENDENTE;
                $boletoMetadata[ConstanteParametros::CHAVE_VALOR]           = $parametros[ConstanteParametros::CHAVE_VALOR_LANCAMENTO];
                $boletoMetadata[ConstanteParametros::CHAVE_DATA_VENCIMENTO] = $tituloReceberORM->getDataVencimento();

                $boletoORM = $this->boletoFacade->criar($mensagemErro, $boletoMetadata, false);

                if (empty($mensagemErro) === false) {
                    return false;
                }
                $parametros["boletoORM"] = $boletoORM;
    
                $tituloReceberORM->addBoleto($boletoORM);
    

            }
            
            $boletoORM->setSituacaoCobranca(SituacoesSistema::SITUACAO_PENDENTE);
            $boletoORM->setConta($tituloReceberORM->getConta()); 
            $boletoORM->setValor($parametros[ConstanteParametros::CHAVE_VALOR_LANCAMENTO]); 
            $boletoORM->setDataVencimento($tituloReceberORM->getDataVencimento()); 
            $parametros["boletoORM"] = $boletoORM;

            $tituloReceberORM->addBoleto($boletoORM);

           
        }//end if

        // self::flushSeguro($mensagemErro);
        return empty($mensagemErro);
    }

    public function cancelaPagamentoPendente(&$mensagemErro, $titulo,$formaPagamento, $parametros) {
        /** @var \App\Entity\Principal\TituloReceber $titulo */
        
        if ($formaPagamento->getFormaBoleto() === false) {
            $boletos = $titulo->getBoletos();
            /** @var \App\Entity\Principal\Boleto $boleto */
            foreach ($boletos as $boleto) {
                if ($boleto->getSituacaoCobranca() === SituacoesSistema::SITUACAO_PENDENTE || $boleto->getSituacaoCobranca() === SituacoesSistema::SITUACAO_ENVIADO) {
                    $boleto->setSituacaoCobranca(SituacoesSistema::SITUACAO_CANCELADO);
                }        
            }
        }
        if ($formaPagamento->getFormaCheque() === false) {
            $cheques = $titulo->getCheques();
            /** @var \App\Entity\Principal\Cheque $cheque */
            foreach ($cheques as $cheque) {
                if ($cheque->getSituacao() === 'P') {
                    $cheque->setSituacao('C');
                    $cheque->setComplemento('cancelado por substituição');
                }        
            }
        }
        if ($formaPagamento->getFormaCartao() === false && $formaPagamento->getFormaCartaoDebito() === false) {
            $transacoes = $titulo->getTransacoesCartao();
            /** @var \App\Entity\Principal\TransacaoCartao $transacao */
            foreach ($transacoes as $transacao) {
                if ($transacao->getSituacao() === 'PEN') {
                    $transacao->setSituacao('EXC');
                }        
            }
        }
        if ($formaPagamento->getFormaTransferencia() === false ) {
            $transacoes = $titulo->getTransferenciasBancaria();
            /** @var \App\Entity\Principal\TransferenciaBancaria $transacao */
            foreach ($transacoes as $transacao) {
                if ($transacao->getSituacao() === 'PEN') {
                    $transacao->setSituacao('CAN');
                }        
            }
        }

    }

    /**
     * Atualizado saldo do titulo, apos recebimento
     *
     * @param string $mensagemErro Mensagem que ira retornar para front-end
     * @param \App\Entity\Principal\MovimentoConta $movimentoContaORM Objeto de MovimentoConta
     * @param array $parametros Parametros a serem inclusos no objeto
     *
     * @return \App\Entity\Principal\TituloReceber $tituloORM Objeto de Titulo
     */
    public function atualizarSaldo(&$mensagemErro, $movimentoContaORM, $parametros=[])
    {
        $tituloORM = $movimentoContaORM->getTituloReceber();
        $parametros[ConstanteParametros::CHAVE_TIT_VALOR_SALDO]       = (float) $tituloORM->getValorSaldoDevedor();
        $parametros[ConstanteParametros::CHAVE_VALOR_SALDO_CALCULADO] = $movimentoContaORM->getValorLancamento();

        if ($this->tituloReceberBO->podeAtualizarSaldo($parametros, $mensagemErro) === true) {
            $this->tituloReceberBO->calculaValoresSaldo($parametros, $tituloORM);
        }

        return $tituloORM;
    }

    /**
     *
     * @param string $mensagemErro Mensagem que ira retornar para front-end
     * @param array $parametros Parametros da busca
     *
     * @return array
     */
    public function buscarPessoas(&$mensagemErro, $parametros=[])
    {

        return $this->tituloReceberRepository->buscarPessoas($parametros);
    }

    /**
     * Executa o flush das alterações no bando de dados
     *
     * @param string $mensagemErro
     */
    public function flush(&$mensagemErro)
    {
        self::flushSeguro($mensagemErro);
    }

    /**
     * Gera as informações para a seleção de registros do relatório de alunos inadimplentes
     *
     * @param array  $parametros
     *
     * @return string
     */
    public function prepararDadosRelatorioAlunosInadimplentes($parametros)
    {
        return $this->tituloReceberRepository->prepararDadosRelatorioAlunosInadimplentes($parametros);
    }

    /**
     * Gera as informações para a seleção de registros do relatório de contas a receber
     *
     * @param array  $parametros
     *
     * @return string
     */
    public function gerarDadosRelatorioContaReceber($parametros)
    {
        return $this->tituloReceberRepository->gerarDadosRelatorioContaReceber($parametros);
    }


    /**
     * Consulta informações dos títulos para renegociação
     *
     * @param array $parametros
     *
     * @return array|null
     */
    public function consultaTitulosParaRenegociacao($parametros, &$mensagemErro)
    {
        $franqueadaORM = $this->franqueadaRepository->find(\App\Helper\VariaveisCompartilhadas::$franqueadaID);
        $taxaMulta     = $franqueadaORM->getPercentualMulta();
        $taxaJuros     = $franqueadaORM->getPercentualJuroDia();
        $resultado     = [
            ConstanteParametros::CHAVE_TITULOS       => [],
            ConstanteParametros::CHAVE_SACADO_PESSOA => null,
            ConstanteParametros::CHAVE_ALUNO => null,
            ConstanteParametros::CHAVE_TOTAL_MULTA   => 0,
            ConstanteParametros::CHAVE_TOTAL_JUROS   => 0,
            ConstanteParametros::CHAVE_VALOR_TOTAL   => 0,
        ];

        $dataHoje = new \DateTime();

        $resultado[ConstanteParametros::CHAVE_SACADO_PESSOA] = null;
        $resultado[ConstanteParametros::CHAVE_ALUNO] = null;

        foreach ($parametros[ConstanteParametros::CHAVE_TITULO_RECEBER] as $tituloId) {
            $titulo = $this->tituloReceberRepository->buscarTituloSomente($tituloId);

            if (is_null($titulo) === true) {
                $mensagemErro = "Título $tituloId não encontrado.";
                return;
            }

            if ((is_null($resultado[ConstanteParametros::CHAVE_SACADO_PESSOA]) === false) && ($titulo[ConstanteParametros::CHAVE_SACADO_PESSOA][ConstanteParametros::CHAVE_ID] !== $resultado[ConstanteParametros::CHAVE_SACADO_PESSOA][ConstanteParametros::CHAVE_ID])) {
                $mensagemErro = "Os títulos a renegociar devem pertencer a mesma pessoa.";
                return;
            }

            $resultado[ConstanteParametros::CHAVE_SACADO_PESSOA] = $titulo[ConstanteParametros::CHAVE_SACADO_PESSOA];
            $resultado[ConstanteParametros::CHAVE_ALUNO] = $titulo[ConstanteParametros::CHAVE_ALUNO];

            $valorSaldo       = $titulo[ConstanteParametros::CHAVE_VALOR_SALDO_DEVEDOR];
            $situacaoPendente = ($titulo[ConstanteParametros::CHAVE_SITUACAO] !== SituacoesSistema::SITUACAO_PENDENTE) && ($titulo[ConstanteParametros::CHAVE_SITUACAO] !== SituacoesSistema::SITUACAO_VENCIDAS);
            if (($valorSaldo === 0) || ($situacaoPendente === true)) {
                continue;
            }

            $valorMulta = 0;
            $valorJuros = 0;
            $titulo[ConstanteParametros::CHAVE_MC_VALOR_MULTA] = 0;
            $titulo[ConstanteParametros::CHAVE_MC_VALOR_JUROS] = 0;

            $diferencaDatas   = $titulo[ConstanteParametros::CHAVE_DATA_PRORROGACAO]->diff($dataHoje);
            $diferencaDias    = $diferencaDatas->days;
            $cobrarMultaJuros = $diferencaDatas->invert === 0 && $diferencaDias > 0;

            if (($taxaMulta > 0) && ($cobrarMultaJuros === true)) {
                $valorMulta = $valorSaldo / 100 * $taxaMulta;
                $titulo[ConstanteParametros::CHAVE_MC_VALOR_MULTA] = $valorMulta;
                $valorSaldo += $valorMulta;
            }

            if (($taxaJuros > 0) && ($cobrarMultaJuros === true)) {
                $saldoComJuros = $this->calcularJurosDia($valorSaldo, $taxaJuros, $diferencaDias);
                $valorJuros    = $saldoComJuros - $valorSaldo;
                $titulo[ConstanteParametros::CHAVE_MC_VALOR_JUROS] = $valorJuros;
                $valorSaldo += $valorJuros;
            }

            $resultado[ConstanteParametros::CHAVE_TOTAL_MULTA] += $valorMulta;
            $resultado[ConstanteParametros::CHAVE_TOTAL_JUROS] += $valorJuros;
            $resultado[ConstanteParametros::CHAVE_VALOR_TOTAL] += $valorSaldo;
            $resultado[ConstanteParametros::CHAVE_TITULOS][]    = $titulo;
        }//end foreach

        return $resultado;
    }

    /**
     * Calcula os juros sobre juros sobre um valor
     *
     * @param float $valor
     * @param float $taxaJuros
     * @param integer $dias
     *
     * @return float
     */
    private function calcularJurosDia($valor, $taxaJuros, $dias)
    {
        if ($dias <= 0) {
            return $valor;
        }

        $valorJuros = $valor / 100 * $taxaJuros;
        $valor      = $valor + $valorJuros;

        return $this->calcularJurosDia($valor, $taxaJuros, --$dias);
    }

    /**
     * Marca os títulos passados como renegociados
     *
     * @param array $titulos
     * @param string $mensagem
     *
     * @return boolean
     */
    public function marcarRenegociados($titulos, $usuarioID, &$mensagem)
    {
        foreach ($titulos as $titulo) {
            $tituloORM = $this->buscarPorID($mensagem, $titulo);
            if (is_null($tituloORM) === true) {
                return false;
            }

            if (($tituloORM->getSituacao() === SituacoesSistema::SITUACAO_PENDENTE) && ($tituloORM->getRenegociado() === false)) {
                $tituloORM->setSituacao(SituacoesSistema::SITUACAO_SUBSTITUIDO);
                $tituloORM->setRenegociado(true);

                $obs = $tituloORM->getObservacao();
                $usuarioORM = $this->usuarioRepository->find(VariaveisCompartilhadas::$usuarioID);
    
                $nomeUsuario = $usuarioORM->getNome();
    
                $dataHoje   = new \DateTime();
    
                $obsRenegociado = 'Usuario: '.$nomeUsuario.' </br> Data: '.$dataHoje->format("d/m/Y"). ' </br> '.$obs;
                $tituloORM->setObservacao($obsRenegociado);

                $cheques = $tituloORM->getCheques();
                foreach ($cheques as $chequeORM) {
                    $chequeORM->setSituacao(SituacoesSistema::SITUACAO_CHEQUE_CANCELADO);
                }

                $boletos = $tituloORM->getBoletos();
                foreach ($boletos as $boletoORM) {
                    $boletoORM->setSituacaoCobranca(SituacoesSistema::SITUACAO_CANCELADO);
                }

                $transacoesCartao = $tituloORM->getTransacoesCartao();
                foreach ($transacoesCartao as $transacaoCartaoORM) {
                    $transacaoCartaoORM->setSituacao(SituacoesSistema::SITUACAO_EXCLUIDO);
                }
            } else {
                $observacao = $tituloORM->getObservacao();
                $mensagem   = "O título \"$observacao\" não pode ser renegociado porque não está pendente.";
                return false;
            }//end if
        }//end foreach

    }

    /**
     * Marca os títulos passados como renegociados
     *
     * @param string $mensagem
     * @param int[] $titulos
     *
     * @return boolean
     */
    public function cancelarTitulosReceber(&$mensagem, $titulos, $usuarioID)
    {
        foreach ($titulos as $titulo) {
            $tituloORM = $this->buscarPorID($mensagem, $titulo);
            if (is_null($tituloORM) === true) {
                return false;
            }

            if ($tituloORM->getSituacao() !== SituacoesSistema::SITUACAO_PENDENTE) {
                $mensagem = "O titulo $titulo está com a situação " . $tituloORM->getSituacao() . ".";
                return false;
            }

            $obs = $tituloORM->getObservacao();
            $usuarioORM = $this->usuarioRepository->find(VariaveisCompartilhadas::$usuarioID);

            $nomeUsuario = $usuarioORM->getNome();

            $dataHoje   = new \DateTime();

            $obsCancelamento = 'Usuario: '.$nomeUsuario.' </br> Data Canc: '.$dataHoje->format("d/m/Y"). ' </br> '.$obs;
            $tituloORM->setSituacao(SituacoesSistema::SITUACAO_CANCELADO);
            $tituloORM->setObservacao($obsCancelamento);


            foreach ($tituloORM->getTransacoesCartao() as $transacaoCartaoORM) {
                if ($this->transacaoCartaoFacade->cancelar($mensagem, $transacaoCartaoORM->getId()) === false) {
                    return false;
                }
            }

            foreach ($tituloORM->getBoletos() as $boletoORM) {
                if ($this->boletoFacade->cancelar($mensagem, $boletoORM->getId()) === false) {
                    return false;
                }
            }

            foreach ($tituloORM->getTransferenciasBancaria() as $transferenciaORM) {
                if ($this->transferenciaBancariaFacade->cancelar($mensagem, $transferenciaORM->getId()) === false) {
                    return false;
                }
            }

            foreach ($tituloORM->getCheques() as $chequeORM) {
                if ($this->chequeFacade->cancelar($mensagem, $chequeORM->getId()) === false) {
                    return false;
                }
            }
        }//end foreach

        return empty($mensagem);
    }

    /**
     * Gera os dados do relatório de titulos sintético
     *
     * @param string $mensagem
     * @param array $parametros
     *
     * @return array
     */
    public function gerarDadosRelatorioTitulosSintetico(&$mensagem, $parametros)
    {
        $dadosRelatorio = $this->tituloReceberRepository->buscarDadosRelatorioTitulos($mensagem, $parametros);
        $totalTitulosPendente    = 0;
        $totalTitulosQuitado     = 0;
        $totalTitulosCancelado   = 0;
        $totalTitulosVencido     = 0;
        $totalTitulosRenegociado = 0;
        

        $dataHoje = new \DateTime();

        foreach ($dadosRelatorio as $k => $dado) {
            $dataVenc = new \DateTime($dado["data_vencimento"]);
            if ($dado["situacao_titulo"] === 'LIQ') {
                $totalTitulosQuitado += $dado["valor_pago"]; 
            }

            if ($dado["situacao_titulo"] === 'PEN' && $dataVenc >= $dataHoje) {
                if ((is_null($dado["tcartao"]) === true) && (is_null($dado["tcheque"]) === true)) {
                    $totalTitulosPendente += $dado["saldo_devedor"]; 
                } else {
                    $dado["situacao_titulo"] = 'LIQ'; 
                }  
            }
            if ($dado["situacao_titulo"] === 'PEN' && $dataVenc < $dataHoje) {
                if ((is_null($dado["tcartao"]) === true) && (is_null($dado["tcheque"]) === true)) {
                    $totalTitulosVencido += $dado["saldo_devedor"]; 
                } else {
                    $dado["situacao_titulo"] = 'LIQ'; 
                }               
            }

            if ($dado["situacao_titulo"] === 'SUB') {
                $totalTitulosRenegociado += $dado["saldo_devedor"]; 
            }

            if ($dado["situacao_titulo"] === 'CAN') {
                $totalTitulosCancelado += $dado["saldo_devedor"]; 
            }

        }        
        $retorno = [
            'data' => $dadosRelatorio,
            'LIQ' => $totalTitulosQuitado,
            'PEN' => $totalTitulosPendente,
            'VEN' => $totalTitulosVencido,
            'SUB' => $totalTitulosRenegociado,
            'CAN' => $totalTitulosCancelado            
        ];

        return $retorno;
        
        $linhas_excel = [
            [
                "",
                "",
                "",
                "Contas a receber - Sintético",
                "",
                "",
                "",
                "",
                "",
            ],
        ];

        $linhas_excel = [
            [
                "Parcela",
                "Valor",
                "Sacado",
                "Vencimento",
                "Forma cobrança",
                "Forma pagamento",
                "Situação",
                "Valor pago",
                "Data pagamento",
            ],
        ];

        $valorTotalGeral         = 0;
        $valorTotalGeralRecebido = 0;
        $totalTitulosPendente    = 0;
        $totalTitulosQuitado     = 0;
        $totalTitulosCancelado   = 0;
        $totalTitulosVencido     = 0;
        $totalTitulosRenegociado = 0;

        foreach ($dadosRelatorio as $k => $dado) {
            $valor            = 0;
            if ($dado["situacao_titulo"] === 'LIQ') {
                $totalTitulosQuitado += $dado["valor_pago"]; 
            }

            $pagamentosTitulo = $this->movimentoContaRepository->findBy(["titulo_receber" => $dado["id"]]);
            foreach ($pagamentosTitulo as $pagamento) {
                if ($pagamento->getOperacao() === 'C') {
                    $valor += $pagamento->getValorLancamento();
                } else {
                    $valor -= $pagamento->getValorLancamento();
                }
            }

            $dadosRelatorio[$k]["valor_pago"] = $valor;

            $linhas_excel[]           = [
                $dado["parcela"],
                $dado["valor_parcela_sem_desconto"] - $dado["desconto_antecipacao"],
                $dado["nome_contato"],
                $dado["data_vencimento"],
                $dado["forma_cobranca"],
                $dado["forma_pagamento"],
                $dado["situacao_titulo"],
                $dado["valor_pago"],
                $dado["data_pagamento"],
            ];
            $valorTotalGeral         += $dado["valor_parcela_sem_desconto"] - $dado["desconto_antecipacao"];
            $valorTotalGeralRecebido += $dado["valor_pago"];
        }//end foreach


        $linhas_excel[] = [
            "",
            "",
            "",
            "",
            "",
            "",
            "",
            "",
            "Totalizadores:",
        ];
        $linhas_excel[] = [
            "",
            "",
            "",
            "",
            "",
            "",
            "",
            "",
            "Total de Registros: " . count($dadosRelatorio),
        ];
        $linhas_excel[] = [
            "",
            "",
            "",
            "",
            "",
            "",
            "",
            "",
            "Valor total: R$" . number_format($valorTotalGeral, 2, ',', '.'),
        ];
        $linhas_excel[] = [
            "",
            "",
            "",
            "",
            "",
            "",
            "",
            "",
            "Valor recebido: R$" . number_format($valorTotalGeralRecebido, 2, ',', '.'),
        ];

        return $linhas_excel;
    }

    /**
     * Gera os dados do relatório de titulos analítico
     *
     * @param string $mensagem
     * @param array $parametros
     *
     * @return array
     */
    public function gerarDadosRelatorioTitulosAnalitico(&$mensagem, $parametros)
    {
        $dadosRelatorio = $this->tituloReceberRepository->buscarDadosRelatorioTitulos($mensagem, $parametros);

        $response = [];
        $alunoId         = null;
        $valorTotalAluno = 0;
        $valorTotalAlunoPago     = 0;
        $valorTotalGeral         = 0;
        $valorTotalGeralRecebido = 0;

        $totalTitulosPendente    = 0;
        $totalTitulosQuitado     = 0;
        $totalTitulosCancelado   = 0;
        $totalTitulosVencido     = 0;
        $totalTitulosRenegociado = 0;
        
        $dataHoje = new \DateTime();

        
        foreach ($dadosRelatorio as $k => $dado) {
            $dataVenc = new \DateTime($dado["data_vencimento"]);
            
            if ($dado["situacao_titulo"] === 'LIQ') {
                $totalTitulosQuitado += $dado["valor_pago"]; 
            }
            if ($dado["situacao_titulo"] === 'PEN' && $dataVenc >= $dataHoje) {
                if ((is_null($dado["tcartao"]) === true) && (is_null($dado["tcheque"]) === true)) {
                    $totalTitulosPendente += $dado["saldo_devedor"]; 
                } else {
                    $dado["situacao_titulo"] = 'LIQ'; 
                }             
            }
            if ($dado["situacao_titulo"] === 'PEN' && $dataVenc < $dataHoje) {
                if ((is_null($dado["tcartao"]) === true) && (is_null($dado["tcheque"]) === true)) {
                    $totalTitulosVencido += $dado["saldo_devedor"]; 
                } else {
                    $dado["situacao_titulo"] = 'LIQ'; 
                } 
            }

            if ($dado["situacao_titulo"] === 'SUB') {
                $totalTitulosRenegociado += $dado["saldo_devedor"]; 
            }

            if ($dado["situacao_titulo"] === 'CAN') {
                $totalTitulosCancelado += $dado["saldo_devedor"]; 
            }



            if ($dado["aluno_id"] !== $alunoId) {
                $response[$dado['aluno_id']]['valor_total'] = number_format($valorTotalAluno, 2, ',', '.');
                $response[$dado['aluno_id']]['valor_pago'] = number_format($valorTotalAlunoPago, 2, ',', '.');

                $valorTotalAluno     = 0;
                $valorTotalAlunoPago = 0;
                $alunoId = $dado["aluno_id"];
            }

            if(isset($dado['aluno_id']) && !key_exists('nome_contato', $response[$dado['aluno_id']])) {
                $response[$dado['aluno_id']]['nome_contato'] = $dado['nome_contato'];
            }

            $valor            = 0;
            $pagamentosTitulo = $this->movimentoContaRepository->findBy(["titulo_receber" => $dado["id"]]);
            foreach ($pagamentosTitulo as $pagamento) {
                if ($pagamento->getOperacao() === 'C') {
                    $valor += $pagamento->getValorLancamento();
                } else {
                    $valor -= $pagamento->getValorLancamento();
                }
            }

            $dadosRelatorio[$k]["valor_pago"] = $valor;

            $response[$dado['aluno_id']]['data'][] = [
                'parcela' => $dado["parcela"],
                'valor_parcela_sem_desconto' => $dado["valor_parcela_sem_desconto"],
                'valor_desconto' => $dado["desconto_antecipacao"],
                'data_vencimento' => $dado["data_vencimento"],
                'forma_cobranca' => $dado["forma_cobranca"],
                'forma_pagamento' => $dado["forma_pagamento"],
                'situacao_titulo' => $dado["situacao_titulo"],
                'data_pagamento' => $dado["data_pagamento"],
                'valor_pago' => $dado["valor_pago"],
            ];
            $valorTotalAluno         += $dado["valor_parcela_sem_desconto"] - $dado["desconto_antecipacao"];
            $valorTotalGeral         += $dado["valor_parcela_sem_desconto"] - $dado["desconto_antecipacao"];
            $valorTotalAlunoPago     += $dado["valor_pago"];
            $valorTotalGeralRecebido += $dado["valor_pago"];
        }

        $retorno = [
            'data' => $response,
            'LIQ' => $totalTitulosQuitado,
            'PEN' => $totalTitulosPendente,
            'VEN' => $totalTitulosVencido,
            'SUB' => $totalTitulosRenegociado,
            'CAN' => $totalTitulosCancelado            
        ];

        return $retorno;

        // var_dump($totalTitulosQuitado);
        // die;

       // return $response;

        // Quando acaba as linhas dos alunos, printa o valor final do ultimo aluno
        if (count($dadosRelatorio) > 0) {
            $linhas_excel[] = [
                "",
                number_format($valorTotalAluno, 2, ',', '.'),
                "",
                "",
                "",
                "",
                "",
                number_format($valorTotalAlunoPago, 2, ',', '.'),
            ];
        }

        $linhas_excel[] = [
            "",
            "",
            "",
            "",
            "",
            "",
            "",
            "Totalizadores:",
        ];
        $linhas_excel[] = [
            "",
            "",
            "",
            "",
            "",
            "",
            "",
            "Total de Registros: " . count($dadosRelatorio),
        ];
        $linhas_excel[] = [
            "",
            "",
            "",
            "",
            "",
            "",
            "",
            "Valor total: R$" . number_format($valorTotalGeral, 2, ',', '.'),
        ];
        $linhas_excel[] = [
            "",
            "",
            "",
            "",
            "",
            "",
            "",
            "Valor recebido: R$" . number_format($valorTotalGeralRecebido, 2, ',', '.'),
        ];

        
    }

    public function buscarDetalhesPorId($id) {
        $retorno =  $this->tituloReceberRepository->buscarPorId($id);
        // $retorno['movimentos'] = $this->tituloReceberRepository->buscaMovimentosPorTitulo($id);
        return $retorno;
    }

    public function buscarMovimentosTitulo($id) {
        $retorno =   $this->tituloReceberRepository->buscaMovimentosPorTitulo($id);
        return $retorno;
    }

    /**
     * Marca os títulos passados como renegociados
     *
     * @param string $mensagem
     * @param int[] $titulos
     *
     * @return boolean
     */
    public function aplicaDescontoManual(&$mensagem, $titulo, $desconto, $motivo)
    {
            
            if ($titulo->getSituacao() !== SituacoesSistema::SITUACAO_PENDENTE) {
                $mensagem = "O titulo $titulo está com a situação " . $titulo->getSituacao() . ".";
                return false;
            }
            $titulo->setValor_desconto_manual($desconto);
            $titulo->setMotivo($motivo);
            
            //recalcula titulo

        return empty($mensagem);
    }

}
