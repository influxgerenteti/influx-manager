<?php

namespace App\Facade\Principal;

use App\BO\Principal\ContaBO;
use App\Helper\ConstanteParametros;
use App\Helper\Logger;
use App\Helper\SituacoesSistema;

use App\BO\Principal\MovimentoContaBO;
use App\Facade\Principal\GenericFacade;
use App\Facade\Principal\TituloPagarFacade;
use App\Facade\Principal\TituloReceberFacade;
use App\Facade\Principal\TransferenciaBancariaFacade;
use App\Entity\Principal\MovimentoConta;
use App\Entity\Principal\TransacaoCartao;
use App\Entity\Principal\Cheque;
use App\Entity\Principal\Boleto;
use App\Entity\Principal\TransferenciaBancaria;
use App\Repository\Principal\ContaRepository;
use Doctrine\Common\Persistence\ManagerRegistry;

/**
 *
 * @author Rodrigo de Souza Fernandes (GATI labs)
 */
class MovimentoContaFacade extends GenericFacade
{

    /**
     *
     * @var \App\Repository\Principal\MovimentoContaRepository
     */
    private $movimentoContaRepository;

    /**
     *
     * @var \App\Repository\Principal\TransacaoCartaoRepository
     */
    private $transacaoCartaoRepository;

    /**
     *
     * @var \App\Repository\Principal\ChequeRepository
     */
    private $chequeRepository;

    /**
     *
     * @var \App\Repository\Principal\BoletoRepository
     */
    private $boletoRepository;

    /**
     *
     * @var \App\BO\Principal\MovimentoContaBO
     */
    private $movimentoContaBO;

    /**
     *
     * @var \App\Facade\Principal\TituloPagarFacade
     */
    private $tituloPagarFacade;

    /**
     *
     * @var \App\Facade\Principal\TituloReceberFacade
     */
    private $tituloReceberFacade;

    /**
     *
     * @var \App\Facade\Principal\TransferenciaBancariaFacade
     */
    private $transferenciaBancariaFacade;

    /**
     * {@inheritDoc}
     *
     * @see \App\Facade\Principal\GenericFacade::__construct()
     */
    function __construct (ManagerRegistry $managerRegistry, $connection="base_principal")
    {
        parent::__construct($managerRegistry);
        $this->movimentoContaRepository  = self::getEntityManager()->getRepository(MovimentoConta::class);
        $this->transacaoCartaoRepository = self::getEntityManager()->getRepository(TransacaoCartao::class);
        $this->chequeRepository          = self::getEntityManager()->getRepository(Cheque::class);
        $this->boletoRepository          = self::getEntityManager()->getRepository(Boleto::class);
        $this->transferenciaBancariaRepository = self::getEntityManager()->getRepository(TransferenciaBancaria::class);
        $this->movimentoContaBO            = new MovimentoContaBO(self::getEntityManager(),new ContaBO( self::getEntityManager()));
        $this->tituloPagarFacade           = new TituloPagarFacade($managerRegistry);
        $this->transferenciaBancariaFacade = new TransferenciaBancariaFacade($managerRegistry);
    }


    /**
     * Lista todos os registros do banco de dados
     *
     * @param array $parametros Parametros da requisicao
     *
     * @return array
     */
    public function listar($parametros)
    {
        $retornoRepositorio = $this->movimentoContaRepository->filtrarMovimentoContaPorPagina($parametros);
        $itens   = $retornoRepositorio['itens'];
        $retorno = [
            ConstanteParametros::CHAVE_TOTAL                 => $itens->getTotalItemCount(),
            ConstanteParametros::CHAVE_ITENS                 => $itens->getItems(),
            ConstanteParametros::CHAVE_TOTAL_ENTRADAS        => $retornoRepositorio[ConstanteParametros::CHAVE_TOTAL_ENTRADAS],
            ConstanteParametros::CHAVE_TOTAL_SAIDAS          => $retornoRepositorio[ConstanteParametros::CHAVE_TOTAL_SAIDAS],
            ConstanteParametros::CHAVE_SALDO_INICIAL          => $retornoRepositorio[ConstanteParametros::CHAVE_SALDO_INICIAL],
            ConstanteParametros::CHAVE_TOTAL_NAO_CONCILIADOS => $retornoRepositorio[ConstanteParametros::CHAVE_TOTAL_NAO_CONCILIADOS],
        ];
        return $retorno;
    }

    /**
     * Busca um registro atraves da ID
     *
     * @param string $mensagemErro Ponteiro de retorno de msg de erro para o front-end
     * @param integer $id Id do registro a ser buscado no banco de dados
     *
     * @return NULL|\App\Entity\Principal\MovimentoConta
     */
    public function buscarPorId(&$mensagemErro, $id)
    {
        $movimentoContaORM = null;
        $this->movimentoContaBO->verificaMovimentoContaExisteId($this->movimentoContaRepository, $id, $mensagemErro, $movimentoContaORM);
        return $movimentoContaORM;
    }

    /**
     * Busca um registro por parametros passados
     *
     * @param array $parametros
     *
     * @return NULL|\App\Entity\Principal\MovimentoConta
     */
    public function buscarPorParametros ($parametros)
    {
        return $this->movimentoContaRepository->findOneBy($parametros);
    }

    /**
     * Busca alunos e fornecedores que possuam algum movimento
     *
     * @param string $mensagem
     * @param array $parametros
     *
     * @return array
     */
    public function buscarAlunoFornecedorComMovimento (&$mensagem, $parametros)
    {
        return $this->movimentoContaRepository->buscarAlunoFornecedorComMovimento($mensagem, $parametros);
    }

        /**
     * Busca alunos e fornecedores que possuam algum movimento
     *
     * @param string $mensagem
     * @param int $tituloId
     *
     * @return array
     */
    public function buscarMovimentoPorTitulo (&$mensagem, $tituloId)
    {
        return $this->movimentoContaRepository->buscarMovimentoPorTitulo($mensagem, $tituloId);
    }

    /**
     * Cria um objeto no banco de dados
     *
     * @param string $mensagemErro Mensagem que ira retornar para front-end
     * @param array $parametros Parametros a serem inclusos no objeto
     * @param boolean $deveGravar Se deve executar o flush das alterações
     * @param \App\Entity\Principal\TransacaoCartao|null $transacaoCartaoORM Transação do movimento, passado caso não tenha o ID ainda
     *
     * @return \App\Entity\Principal\MovimentoConta|null $movimentoContaORM Objeto de Movimento Conta
     */
    public function criar(&$mensagemErro, $parametros=[], $deveGravar=true, $transacaoCartaoORM=null)
    {

        Logger::log('criar movimento em  conta','SALDOS');
      
        $movimentoContaORM = null;

        if (isset($parametros[ConstanteParametros::CHAVE_MC_DATA_DEPOSITO]) === true && isset($parametros[ConstanteParametros::CHAVE_MC_DATA_CONTABIL]) === false) {
            $parametros[ConstanteParametros::CHAVE_MC_DATA_CONTABIL] = $parametros[ConstanteParametros::CHAVE_MC_DATA_DEPOSITO];
        }

        if ($this->movimentoContaBO->podeCriar($parametros, $mensagemErro) === true) {
            $tituloPagarORM = $parametros[ConstanteParametros::CHAVE_TITULO_PAGAR];
            if (is_null($tituloPagarORM) === false) {
                // Quando o título for a pagar, deve-se atualizar o saldo do título para sempre estar liquidado.
                // Desta forma, o saldo do título será sempre o valor_montante.
                $valorMontante = (float) $parametros[ConstanteParametros::CHAVE_MC_VALOR_MONTANTE];
                $tituloPagarORM->setValorSaldo($valorMontante);
                $parametros[ConstanteParametros::CHAVE_OPERACAO] = SituacoesSistema::OPERACAO_DEBITO;

                self::persistSeguro($tituloPagarORM, $mensagemErro);
            }

            if (isset($parametros[ConstanteParametros::CHAVE_TRANSACAO_CARTAO]) === true) {
                $transacaoCartaoID = $parametros[ConstanteParametros::CHAVE_TRANSACAO_CARTAO];
                unset($parametros[ConstanteParametros::CHAVE_TRANSACAO_CARTAO]);
            }

            if (isset($parametros[ConstanteParametros::CHAVE_CHEQUE]) === true) {
                $chequeID = $parametros[ConstanteParametros::CHAVE_CHEQUE];
                unset($parametros[ConstanteParametros::CHAVE_CHEQUE]);
            }

            if (isset($parametros["boletoORM"]) === true) {
                $boleto = $parametros["boletoORM"];
                if($boleto != null){
                    $parametros[ConstanteParametros::CHAVE_BOLETO] = $boleto; 
                }
            } else if (isset($parametros[ConstanteParametros::CHAVE_BOLETO]) === true) {
                $boletoID = $parametros[ConstanteParametros::CHAVE_BOLETO];
                if (isset($boletoID[ConstanteParametros::CHAVE_ID]) === true) {
                    $boletoID = $boletoID[ConstanteParametros::CHAVE_ID];
                }

                $boleto = $this->boletoRepository->find($boletoID);

                unset($parametros[ConstanteParametros::CHAVE_BOLETO]);
            }

            // Cria movimento
            if (isset($parametros[ConstanteParametros::CHAVE_TRANSFERENCIA_BANCARIA]) === true) {
                if (isset($parametros[ConstanteParametros::CHAVE_TRANSFERENCIA_BANCARIA]) === true) {
                    $parametrosTransferencia = $parametros[ConstanteParametros::CHAVE_TRANSFERENCIA_BANCARIA];
                    unset($parametros[ConstanteParametros::CHAVE_TRANSFERENCIA_BANCARIA]);
                }
            }

           

            

            $movimentoContaORM = \App\Factory\GeneralORMFactory::criar(\App\Entity\Principal\MovimentoConta::class, true, $parametros);
            
            $movimentoContaORM->setValorTitulo($parametros[ConstanteParametros::CHAVE_VALOR_LANCAMENTO]);
            $movimentoContaORM->setValorLancamento($parametros[ConstanteParametros::CHAVE_MC_VALOR_MONTANTE]);
            // Calcula saldo final da conta
            $this->movimentoContaBO->calculaValorSaldoFinalConta($parametros, $movimentoContaORM);

            if (isset($parametros[ConstanteParametros::CHAVE_TAXA]) === true && is_null($parametros[ConstanteParametros::CHAVE_TAXA]) === false) {
                $valorLancamento = $movimentoContaORM->getValorLancamento() - $parametros[ConstanteParametros::CHAVE_TAXA];
                $movimentoContaORM->setValorLancamento($valorLancamento);
                unset($parametros[ConstanteParametros::CHAVE_TAXA]);
            }

            self::persistSeguro($movimentoContaORM, $mensagemErro);

            $existeTituloPagarOuReceber = false;

            if ((isset($parametros[ConstanteParametros::CHAVE_TITULO_PAGAR]) === true) && (is_null($parametros[ConstanteParametros::CHAVE_TITULO_PAGAR]) === false)) {
                $tituloPagarORM = $this->tituloPagarFacade->atualizarSaldo($mensagemErro, $movimentoContaORM, $parametros);
                $movimentoContaORM->setObservacao($tituloPagarORM->getNarrativaPlanoConta());

                if ((is_null($tituloPagarORM) === false) && (empty($mensagemErro) === true)) {
                    $tituloPagarORM->setFormaCobranca($parametros[ConstanteParametros::CHAVE_FORMA_PAGAMENTO]);

                    $situacaoTitulo = $tituloPagarORM->getSituacao();
                    if (($situacaoTitulo === SituacoesSistema::SITUACAO_LIQUIDADO) || ($situacaoTitulo === SituacoesSistema::SITUACAO_BAIXADO)) {
                        $cheque = $tituloPagarORM->getCheque();
                        if (($tituloPagarORM->getFormaCobranca()->getFormaCheque() === true) && (is_null($cheque) === false)) {
                            $cheque->setSituacao('B');
                            $cheque->setDataBaixa(new \DateTime());
                            $movimentoContaORM->setNumeroDocumento($cheque->getNumero());
                        }
                    }

                    $pagamentosFuncionario = $tituloPagarORM->getContaPagar()->getPagamentoFuncionarios();
                    if (count($pagamentosFuncionario) > 0) {
                        foreach ($pagamentosFuncionario as $pagamento) {
                            if (($pagamento->getSituacao() !== SituacoesSistema::SITUACAO_CANCELADO) && ($pagamento->getSituacao() !== SituacoesSistema::SITUACAO_PAGO)) {
                                $pagamento->setSituacao(SituacoesSistema::SITUACAO_PAGO);
                                $cabecalhos = $pagamento->getCabecalhoPagamentos();
                                foreach ($cabecalhos as $cabecalho) {
                                    $cabecalho->setSituacao($pagamento->getSituacao());
                                    $turmaAulas           = $cabecalho->getPagamentoTurmaAulas()->toArray();
                                    $alunoDiariosPersonal = $cabecalho->getPagamentoAlunoDiarioPersonals()->toArray();
                                    $atividadesExtra      = $cabecalho->getPagamentoAtividadeExtras()->toArray();
                                    $reposicoesAula       = $cabecalho->getPagamentoReposicaoAulas()->toArray();
                                    $bonusClasses         = $cabecalho->getPagamentoBonusClasses()->toArray();

                                    $aulas = array_merge($turmaAulas, $alunoDiariosPersonal, $atividadesExtra, $reposicoesAula, $bonusClasses);
                                    foreach ($aulas as $aula) {
                                        $aula->setSituacao($pagamento->getSituacao());
                                    }
                                }
                            }//end if
                        }//end foreach
                    }//end if

                    $existeTituloPagarOuReceber = true;
                    if ($deveGravar === true) {
                        self::flushSeguro($mensagemErro);
                    }
                }//end if
            }//end if

            if ((isset($parametros[ConstanteParametros::CHAVE_TITULO_RECEBER]) === true) && (is_null($parametros[ConstanteParametros::CHAVE_TITULO_RECEBER]) === false)) {
                
                $tituloReceberORM = $movimentoContaORM->getTituloReceber();
                
                //$this->tituloReceberFacade = new TituloReceberFacade(self::getManagerRegistry());

                // Atualiza saldo do titulo receber
                $saldoAtual = (float) $tituloReceberORM->getValorSaldoDevedor();
                $valorLancamento = $movimentoContaORM->getValorLancamento();
                $valorDescontos = $movimentoContaORM->getValorDesconto();
                $saldoFinal = $saldoAtual - $valorLancamento - $valorDescontos;
                if($saldoFinal <= 0){
                    $saldoFinal = 0;
                    $tituloReceberORM->setSituacao('LIQ');
                }
                $tituloReceberORM->setValorSaldoDevedor( $saldoFinal);
                

                // $tituloReceberORM = $this->tituloReceberFacade->atualizarSaldo($mensagemErro, $movimentoContaORM, $parametros);
                // Cria observação no movimento igual à do titulo a receber
                //$movimentoContaORM->setObservacao($tituloReceberORM->getObservacao());

                if ((is_null($tituloReceberORM) === false) && (empty($mensagemErro) === true)) {
                    $formaPagamento = $parametros[ConstanteParametros::CHAVE_FORMA_PAGAMENTO];

                    if ($formaPagamento->getFormaCheque() === true) {
                        $cheque = $this->chequeRepository->find($chequeID['id']);
                        $cheque->setSituacao('B');
                        $cheque->setDataBaixa(new \DateTime());
                        $movimentoContaORM->setNumeroDocumento($cheque->getNumero());
                    }

                    if (($formaPagamento->getFormaCartao() === true) || ($formaPagamento->getFormaCartaoDebito() === true)) {
                        if ($transacaoCartaoORM === null) {
                            $transacaoCartaoORM = $this->transacaoCartaoRepository->find($transacaoCartaoID);
                        }

                        $transacaoCartaoORM->setSituacao('CRE');
                        $movimentoContaORM->setNumeroDocumento($transacaoCartaoORM->getNumeroLancamento());
                    }

                    if ($formaPagamento->getFormaBoleto() === true) {
                        // Busca o boleto do pagamento, seta pra RECebido, e pega o nosso numero do boleto pra colocar no movimentoConta
                        $boleto->setSituacaoCobranca(SituacoesSistema::SITUACAO_RECEBIDO);
                        $boleto->setMovimentoConta($movimentoContaORM);
                        $movimentoContaORM->setNumeroDocumento($boleto->getNossoNumero());
                    }

                    if ($formaPagamento->getFormaTransferencia() === true && isset($parametrosTransferencia) === true) {
                        $transferenciaBancariaORM = $this->transferenciaBancariaRepository->find($parametrosTransferencia[ConstanteParametros::CHAVE_ID]);
                        if (is_null($transferenciaBancariaORM) === true) {
                            $parametrosTransferencia[ConstanteParametros::CHAVE_MOVIMENTO_CONTA] = $movimentoContaORM;
                            $parametrosTransferencia[ConstanteParametros::CHAVE_FRANQUEADA]      = $parametros[ConstanteParametros::CHAVE_FRANQUEADA];
                            $parametrosTransferencia[ConstanteParametros::CHAVE_TITULO_RECEBER]  = $tituloReceberORM;
                            $parametrosTransferencia[ConstanteParametros::CHAVE_VALOR]           = $movimentoContaORM->getValorLancamento();
                            $transferenciaBancariaORM = $this->transferenciaBancariaFacade->criar($mensagemErro, $parametrosTransferencia, false);
                            if ((is_null($transferenciaBancariaORM) === true) || (empty($mensagemErro) === false)) {
                                return false;
                            }
                        } else {
                            $transferenciaBancariaORM->setAgencia($parametrosTransferencia[ConstanteParametros::CHAVE_AGENCIA]);
                            $transferenciaBancariaORM->setConta($parametrosTransferencia[ConstanteParametros::CHAVE_CONTA]);
                        }
                    }

                    $contaReceber        = $tituloReceberORM->getContaReceber();
                    $titulosContaReceber = $contaReceber->getTituloRecebers();
                    $situacaoTitulos     = SituacoesSistema::SITUACAO_PENDENTE;

                    // Se existir qualquer titulo ainda pendente na conta, seta como pendente, caso contrário, seta como liquidado
                    foreach ($titulosContaReceber as $tituloContaReceber) {
                        if ($tituloContaReceber->getSituacao() === SituacoesSistema::SITUACAO_PENDENTE) {
                            $situacaoTitulos = SituacoesSistema::SITUACAO_PENDENTE;
                            break;
                        }

                        if ($tituloContaReceber->getSituacao() === SituacoesSistema::SITUACAO_LIQUIDADO) {
                            $situacaoTitulos = SituacoesSistema::SITUACAO_LIQUIDADO;
                        }
                    }

                    $contaReceber->setSituacao($situacaoTitulos);

                    $existeTituloPagarOuReceber = true;
                    if ($deveGravar === true) {
                        self::flushSeguro($mensagemErro);
                    }
                }//end if
            }//end if

            if (($existeTituloPagarOuReceber === false) && ($deveGravar === true)) {
                self::flushSeguro($mensagemErro);
            }
        }//end if

        return $movimentoContaORM;
    }

    /**
     * Atualiza um registro no banco de dados
     *
     * @param string $mensagemErro Mensagem que ira retornar para front-end
     * @param int $id Chave primaria do registro
     * @param array $parametros Campos e valores que iram ser atualizados
     *
     * @return boolean
     */
    public function atualizar(&$mensagemErro, $id, $parametros=[])
    {
        Logger::log('criar atualizacao em  conta','SALDOS');
        $movimentoContaORM = null;
        if ($this->movimentoContaBO->verificaMovimentoContaExisteId($this->movimentoContaRepository, $id, $mensagemErro, $movimentoContaORM) === true) {
            // Busca o valor do lançamento para então atualizar o saldo da conta
            $parametros[ConstanteParametros::CHAVE_MC_VALOR_LANCAMENTO] = $movimentoContaORM->getValorLancamento();

            if ($this->movimentoContaBO->podeAtualizar($parametros, $mensagemErro) === true) {
                self::getFctHelper()->setParams($parametros, $movimentoContaORM);

                // $this->movimentoContaBO->calculaValorSaldoFinalConta($parametros, $movimentoContaORM);
                self::flushSeguro($mensagemErro);
            }
        }

        return empty($mensagemErro) === true;
    }

    /**
     * Cria uma transferência entre contas
     *
     * @param string $mensagemErro
     * @param array $parametros
     *
     * @return boolean
     */
    public function transferir (&$mensagemErro, $parametros=[])
    {
        Logger::log('criar transferencia entre contas','SALDOS');
        $parametros[ConstanteParametros::CHAVE_MC_DATA_DEPOSITO]         = $parametros[ConstanteParametros::CHAVE_MC_DATA_CONTABIL];
        $parametros[ConstanteParametros::CHAVE_MC_VALOR_MONTANTE]        = $parametros[ConstanteParametros::CHAVE_MC_VALOR_LANCAMENTO];
        $parametros[ConstanteParametros::CHAVE_MC_VALOR_JUROS]           = 0;
        $parametros[ConstanteParametros::CHAVE_MC_VALOR_MULTA]           = 0;
        $parametros[ConstanteParametros::CHAVE_MC_VALOR_DESCONTO]        = 0;
        $parametros[ConstanteParametros::CHAVE_MC_VALOR_DIFERENCA_BAIXA] = 0;
        $parametros[ConstanteParametros::CHAVE_CONCILIADO] = SituacoesSistema::CONCILIADO_SIM;
        $parametros[ConstanteParametros::CHAVE_OPERACAO]   = SituacoesSistema::OPERACAO_DEBITO;
        $parametros[ConstanteParametros::CHAVE_CONTA]      = $parametros[ConstanteParametros::CHAVE_CONTA_ORIGEM];

        $tipoMovimentoContaRepository = $this->movimentoContaBO->getTipoMovimentoContaRepository();
        $tipoMovimento = $tipoMovimentoContaRepository->findOneBy(['tipo_operacao' => SituacoesSistema::OPERACAO_TRANSFERENCIA])->getId();
        $parametros[ConstanteParametros::CHAVE_TIPO_MOVIMENTO_CONTA] = $tipoMovimento;

        $contaRepository = $this->movimentoContaBO->getContaRepository();
        $contaOrigem     = $contaRepository->find($parametros[ConstanteParametros::CHAVE_CONTA_ORIGEM]);
        $contaDestino    = $contaRepository->find($parametros[ConstanteParametros::CHAVE_CONTA_DESTINO]);

        $nomeContaOrigem  = $contaOrigem->getDescricao();
        $nomeContaDestino = $contaDestino->getDescricao();
        $parametros[ConstanteParametros::CHAVE_OBSERVACAO] = "Transferência de $nomeContaOrigem para $nomeContaDestino";

        $formaPagamentoRepository = $this->movimentoContaBO->getFormaPagamentoRepository();
        $parametros[ConstanteParametros::CHAVE_FORMA_PAGAMENTO] = $formaPagamentoRepository->findOneBy(['forma_transferencia' => 1])->getId();

        $copiaParametros = [];
        foreach ($parametros as $k => $v) {
            $copiaParametros[$k] = $v;
        }

        if ($this->movimentoContaBO->podeCriar($copiaParametros, $mensagemErro, false) === true) {
            $movimentoContaDebitoORM = \App\Factory\GeneralORMFactory::criar(\App\Entity\Principal\MovimentoConta::class, true, $copiaParametros);
            $this->movimentoContaBO->calculaValorSaldoFinalConta($copiaParametros, $movimentoContaDebitoORM);
            self::persistSeguro($movimentoContaDebitoORM, $mensagemErro);
        }

        $copiaParametros = [];
        foreach ($parametros as $k => $v) {
            $copiaParametros[$k] = $v;
        }

        $copiaParametros[ConstanteParametros::CHAVE_MC_REFERENCIA_MC] = $movimentoContaDebitoORM;
        $copiaParametros[ConstanteParametros::CHAVE_OPERACAO]         = SituacoesSistema::OPERACAO_CREDITO;
        $copiaParametros[ConstanteParametros::CHAVE_CONTA]            = $parametros[ConstanteParametros::CHAVE_CONTA_DESTINO];

        if ($this->movimentoContaBO->podeCriar($copiaParametros, $mensagemErro, false) === true) {
            $movimentoContaCreditoORM = \App\Factory\GeneralORMFactory::criar(\App\Entity\Principal\MovimentoConta::class, true, $copiaParametros);
            $this->movimentoContaBO->calculaValorSaldoFinalConta($copiaParametros, $movimentoContaCreditoORM);
            self::persistSeguro($movimentoContaCreditoORM, $mensagemErro);
            $movimentoContaDebitoORM->setReferenciaMovimentoConta($movimentoContaCreditoORM);
        }

        self::flushSeguro($mensagemErro);
        return empty($mensagemErro) === true;
    }


    /**
     * Estorna um lançamento
     *
     * @param string $mensagemErro
     * @param array $parametros
     * @param \App\Entity\Principal\MovimentoConta $movimentoContaORM
     * @param boolean $deveGravar
     *
     * @return boolean
     */
    public function estornar (&$mensagemErro, $parametros=[], &$movimentoContaORM=null, $deveGravar=true)
    {
        if ($movimentoContaORM->getMovimentoEstorno() === true) {
            $mensagemErro = 'Este lançamento é um estorno, não é possível estorná-lo.';
            return false;
        }

        if ($movimentoContaORM->getEstornado() === true) {
            $mensagemErro = 'Este lançamento já foi estornado.';
            return false;
        }

        if ($movimentoContaORM->getTipoMovimentoConta()->getTipoOperacao() === SituacoesSistema::OPERACAO_TRANSFERENCIA) {
            return $this->estornarTransferencia($mensagemErro, $parametros, $movimentoContaORM, $deveGravar);
        } else {
            return $this->estornarLancamento($mensagemErro, $parametros, $movimentoContaORM, $deveGravar);
        }
    }


    /**
     * Estorna um lançamento comum
     *
     * @param string $mensagemErro
     * @param array $parametros
     * @param \App\Entity\Principal\MovimentoConta $movimentoContaORM
     * @param boolean $deveGravar
     *
     * @return boolean
     */
    public function estornarLancamento (&$mensagemErro, $parametros=[], $movimentoContaORM=null, $deveGravar=true)
    {
        Logger::log('estornar lancamento em  conta','SALDOS');
        if (isset($parametros[ConstanteParametros::CHAVE_MC_DATA_ESTORNO]) === false) {
            $parametros[ConstanteParametros::CHAVE_MC_DATA_ESTORNO] = new \DateTime();
        }

        if (($parametros[ConstanteParametros::CHAVE_MC_DATA_ESTORNO] instanceof \DateTime) === false) {
            $parametros[ConstanteParametros::CHAVE_MC_DATA_ESTORNO] = \App\Helper\FunctionHelper::formataCampoDateTimeJS($parametros[ConstanteParametros::CHAVE_MC_DATA_ESTORNO]);
        }

        $parametros[ConstanteParametros::CHAVE_MC_DATA_CONTABIL]         = (new \DateTime())->format('Y-m-d\TH:i:s.uP');
        $parametros[ConstanteParametros::CHAVE_MC_DATA_DEPOSITO]         = (new \DateTime())->format('Y-m-d\TH:i:s.uP');
        $parametros[ConstanteParametros::CHAVE_MC_VALOR_LANCAMENTO]      = $movimentoContaORM->getValorLancamento();
        $parametros[ConstanteParametros::CHAVE_MC_VALOR_TITULO]          = $movimentoContaORM->getValorTitulo();
        $parametros[ConstanteParametros::CHAVE_MC_VALOR_MONTANTE]        = $movimentoContaORM->getValorLancamento();
        $parametros[ConstanteParametros::CHAVE_MC_VALOR_JUROS]           = $movimentoContaORM->getValorJuros();
        $parametros[ConstanteParametros::CHAVE_MC_VALOR_MULTA]           = $movimentoContaORM->getValorMulta();
        $parametros[ConstanteParametros::CHAVE_MC_VALOR_DESCONTO]        = $movimentoContaORM->getValorDesconto();
        $parametros[ConstanteParametros::CHAVE_MC_VALOR_DIFERENCA_BAIXA] = $movimentoContaORM->getValorDiferencaBaixa();
        $parametros[ConstanteParametros::CHAVE_MC_REFERENCIA_MC]         = $movimentoContaORM;
        $parametros[ConstanteParametros::CHAVE_NUMERO_DOCUMENTO]         = $movimentoContaORM->getNumeroDocumento();
        $parametros[ConstanteParametros::CHAVE_CONCILIADO]      = SituacoesSistema::CONCILIADO_SIM;
        $parametros[ConstanteParametros::CHAVE_CONTA]           = $movimentoContaORM->getConta()->getId();
        $parametros[ConstanteParametros::CHAVE_FORMA_PAGAMENTO] = $movimentoContaORM->getFormaPagamento()->getId();

        $operacao = $movimentoContaORM->getOperacao();

        $tipoMovimentoContaRepository = $this->movimentoContaBO->getTipoMovimentoContaRepository();
        if ($operacao === SituacoesSistema::OPERACAO_CREDITO) {
            $parametros[ConstanteParametros::CHAVE_OPERACAO] = SituacoesSistema::OPERACAO_DEBITO;
            $parametros[ConstanteParametros::CHAVE_TIPO_MOVIMENTO_CONTA] = $tipoMovimentoContaRepository->findOneBy(['tipo_operacao' => SituacoesSistema::OPERACAO_DEBITO])->getId();
        } else {
            $parametros[ConstanteParametros::CHAVE_OPERACAO] = SituacoesSistema::OPERACAO_CREDITO;
            $parametros[ConstanteParametros::CHAVE_TIPO_MOVIMENTO_CONTA] = $tipoMovimentoContaRepository->findOneBy(['tipo_operacao' => SituacoesSistema::OPERACAO_CREDITO])->getId();
        }

        $movimentoContaORM->setEstornado(true);
        $tituloPagarORM   = $movimentoContaORM->getTituloPagar();
        $tituloReceberORM = $movimentoContaORM->getTituloReceber();

        if (is_null($tituloPagarORM) === false) {
            $parametros[ConstanteParametros::CHAVE_TITULO_PAGAR] = $tituloPagarORM->getId();
            $tituloPagarORM->setValorSaldo($tituloPagarORM->getValorDocumento());

            if ($tituloPagarORM->getValorSaldo() === (float) 0) {
                $tituloPagarORM->setSituacao(SituacoesSistema::SITUACAO_LIQUIDADO);
            } else {
                $tituloPagarORM->setSituacao(SituacoesSistema::SITUACAO_PENDENTE);
            }

            if (isset($parametros[ConstanteParametros::CHAVE_NAO_ALTERAR_CHEQUE]) === false) {
                $cheque = $movimentoContaORM->getCheque();
                if (is_null($cheque) === false) {
                    if ($parametros[ConstanteParametros::CHAVE_OPERACAO] === SituacoesSistema::OPERACAO_CREDITO) {
                        $cheque->setSituacao(SituacoesSistema::SITUACAO_CHEQUE_PENDENTE);
                        $cheque->setDataBaixa(null);
                    } else {
                        $cheque->setSituacao(SituacoesSistema::SITUACAO_CHEQUE_BAIXADO);
                        $cheque->setDataBaixa($parametros[ConstanteParametros::CHAVE_MC_DATA_ESTORNO]);
                    }
                }
            }
        }//end if

        if (is_null($tituloReceberORM) === false) {
            $parametros[ConstanteParametros::CHAVE_TITULO_RECEBER] = $tituloReceberORM->getId();
            $saldoAtual = $tituloReceberORM->getValorSaldoDevedor();
            $novoSaldo  = 0;
            if ($parametros[ConstanteParametros::CHAVE_OPERACAO] === SituacoesSistema::OPERACAO_DEBITO) {
                $novoSaldo = $saldoAtual + $movimentoContaORM->getValorTitulo();
            } else {
                $novoSaldo = $saldoAtual - $movimentoContaORM->getValorTitulo();
            }

            $tituloReceberORM->setValorSaldoDevedor($novoSaldo);

            if ((float) $novoSaldo === (float) 0) {
                $tituloReceberORM->setSituacao(SituacoesSistema::SITUACAO_LIQUIDADO);
            } else {
                $tituloReceberORM->setSituacao(SituacoesSistema::SITUACAO_PENDENTE);
            }

            $cheque          = $movimentoContaORM->getCheque();
            $boleto          = $movimentoContaORM->getBoleto();
            $transacaoCartao = $movimentoContaORM->getTransacaoCartao();
            $transferencia   = $movimentoContaORM->getTransferenciaBancaria();

            if ((isset($parametros[ConstanteParametros::CHAVE_NAO_ALTERAR_CHEQUE]) === false) && (is_null($cheque) === false)) {
                if ($parametros[ConstanteParametros::CHAVE_OPERACAO] === SituacoesSistema::OPERACAO_DEBITO) {
                    $cheque->setSituacao(SituacoesSistema::SITUACAO_CHEQUE_PENDENTE);
                    $cheque->setDataBaixa(null);
                } else {
                    $cheque->setSituacao(SituacoesSistema::SITUACAO_CHEQUE_BAIXADO);
                    $cheque->setDataBaixa($parametros[ConstanteParametros::CHAVE_MC_DATA_ESTORNO]);
                }
            }

            if (is_null($boleto) === false) {
                if ($parametros[ConstanteParametros::CHAVE_OPERACAO] === SituacoesSistema::OPERACAO_DEBITO) {
                    $boleto->setSituacaoCobranca(SituacoesSistema::SITUACAO_PENDENTE);
                    $boleto->setMovimentoConta(null);
                } else {
                    $boleto->setSituacaoCobranca(SituacoesSistema::SITUACAO_RECEBIDO);
                }
            }

            if (is_null($transacaoCartao) === false) {
                if ($parametros[ConstanteParametros::CHAVE_OPERACAO] === SituacoesSistema::OPERACAO_DEBITO) {
                    $transacaoCartao->setSituacao(SituacoesSistema::SITUACAO_ESTORNADO);
                    $transacaoCartao->setDataEstorno($parametros[ConstanteParametros::CHAVE_MC_DATA_ESTORNO]);
                } else {
                    $transacaoCartao->setSituacao(SituacoesSistema::SITUACAO_CREDITADO);
                }
            }

            if (is_null($transferencia) === false) {
                if ($parametros[ConstanteParametros::CHAVE_OPERACAO] === SituacoesSistema::OPERACAO_DEBITO) {
                    $transferencia->setSituacao(SituacoesSistema::SITUACAO_ESTORNADO);
                    $transferencia->setDataEstorno($parametros[ConstanteParametros::CHAVE_MC_DATA_ESTORNO]);
                } else {
                    $transferencia->setSituacao(SituacoesSistema::SITUACAO_CREDITADO);
                }
            }
        }//end if

        $novoMovimentoConta = null;
        if ($this->movimentoContaBO->podeCriar($parametros, $mensagemErro) === true) {
            $parametros[ConstanteParametros::CHAVE_OBSERVACAO] = "Estorno de " . $movimentoContaORM->getObservacao();
            $novoMovimentoConta = \App\Factory\GeneralORMFactory::criar(\App\Entity\Principal\MovimentoConta::class, true, $parametros);
            $this->movimentoContaBO->calculaValorSaldoFinalConta($parametros, $novoMovimentoConta);
            $novoMovimentoConta->setValorTitulo($movimentoContaORM->getValorTitulo());
            $novoMovimentoConta->setMovimentoEstorno(true);

            self::persistSeguro($novoMovimentoConta, $mensagemErro);
        }

        if (empty($mensagemErro) === true) {
            if ($deveGravar === true) {
                self::flushSeguro($mensagemErro);
                $parametros[ConstanteParametros::CHAVE_MOVIMENTO_CONTA] = $novoMovimentoConta;
            }

            return empty($mensagemErro) === true;
        }

        return false;
    }


    /**
     * Estorna um lançamento de transferência
     *
     * @param string $mensagemErro
     * @param array $parametros
     * @param \App\Entity\Principal\MovimentoConta $movimentoContaORM
     * @param boolean $deveGravar
     *
     * @return boolean
     */
    public function estornarTransferencia (&$mensagemErro, $parametros=[], $movimentoContaORM=null, $deveGravar=true)
    {
        Logger::log('estornar transferencia em conta','SALDOS');
        $parametros[ConstanteParametros::CHAVE_MC_DATA_CONTABIL]         = $parametros[ConstanteParametros::CHAVE_MC_DATA_ESTORNO];
        $parametros[ConstanteParametros::CHAVE_MC_DATA_DEPOSITO]         = $parametros[ConstanteParametros::CHAVE_MC_DATA_ESTORNO];
        $parametros[ConstanteParametros::CHAVE_MC_VALOR_LANCAMENTO]      = $movimentoContaORM->getValorLancamento();
        $parametros[ConstanteParametros::CHAVE_MC_VALOR_TITULO]          = $movimentoContaORM->getValorTitulo();
        $parametros[ConstanteParametros::CHAVE_MC_VALOR_MONTANTE]        = $movimentoContaORM->getValorLancamento();
        $parametros[ConstanteParametros::CHAVE_MC_VALOR_JUROS]           = $movimentoContaORM->getValorJuros();
        $parametros[ConstanteParametros::CHAVE_MC_VALOR_MULTA]           = $movimentoContaORM->getValorMulta();
        $parametros[ConstanteParametros::CHAVE_MC_VALOR_DESCONTO]        = $movimentoContaORM->getValorDesconto();
        $parametros[ConstanteParametros::CHAVE_MC_VALOR_DIFERENCA_BAIXA] = $movimentoContaORM->getValorDiferencaBaixa();
        $parametros[ConstanteParametros::CHAVE_MC_REFERENCIA_MC]         = $movimentoContaORM;
        $parametros[ConstanteParametros::CHAVE_NUMERO_DOCUMENTO]         = $movimentoContaORM->getNumeroDocumento();
        $parametros[ConstanteParametros::CHAVE_CONCILIADO]      = SituacoesSistema::CONCILIADO_SIM;
        $parametros[ConstanteParametros::CHAVE_CONTA]           = $movimentoContaORM->getConta()->getId();
        $parametros[ConstanteParametros::CHAVE_FORMA_PAGAMENTO] = $movimentoContaORM->getFormaPagamento()->getId();

        $operacao = $movimentoContaORM->getOperacao();
        $parametros[ConstanteParametros::CHAVE_TIPO_MOVIMENTO_CONTA] = $movimentoContaORM->getTipoMovimentoConta()->getId();

        if ($operacao === SituacoesSistema::OPERACAO_CREDITO) {
            $parametros[ConstanteParametros::CHAVE_OPERACAO] = SituacoesSistema::OPERACAO_DEBITO;
        } else {
            $parametros[ConstanteParametros::CHAVE_OPERACAO] = SituacoesSistema::OPERACAO_CREDITO;
        }

        $movimentoContaORM->setEstornado(true);

        if ($this->movimentoContaBO->podeCriar($parametros, $mensagemErro) === true) {
            $novoMovimentoConta = \App\Factory\GeneralORMFactory::criar(\App\Entity\Principal\MovimentoConta::class, true, $parametros);
            $this->movimentoContaBO->calculaValorSaldoFinalConta($parametros, $novoMovimentoConta);
            $novoMovimentoConta->setMovimentoEstorno(true);

            self::persistSeguro($novoMovimentoConta, $mensagemErro);
        }

        $referenciaMovimentoConta = $movimentoContaORM->getReferenciaMovimentoConta();
        if ((is_null($referenciaMovimentoConta) === false) && ($referenciaMovimentoConta->getEstornado() === false)) {
            $this->estornarTransferencia($mensagemErro, $parametros, $referenciaMovimentoConta, $deveGravar);
        }

        if (empty($mensagemErro) === true) {
            self::flushSeguro($mensagemErro);
            return empty($mensagemErro) === true;
        }

        return false;
    }

    /**
     * Concilia lançamentos
     *
     * @param string $mensagemErro
     * @param array $parametros
     *
     * @return boolean
     */
    public function conciliar (&$mensagemErro, $parametros=[])
    {
        Logger::log('conciliar movimentos em  conta','SALDOS');

        foreach ($parametros[ConstanteParametros::CHAVE_IDS] as $id) {
            $movimentoContaORM = null;
            if ($this->movimentoContaBO->verificaMovimentoContaExisteId($this->movimentoContaRepository, $id, $mensagemErro, $movimentoContaORM) === false) {
                return false;
            }

            $movimentoContaORM->setConciliado(SituacoesSistema::CONCILIADO_SIM);
            $parametros[ConstanteParametros::CHAVE_MC_VALOR_LANCAMENTO] = $movimentoContaORM->getValorLancamento();
            if ($this->movimentoContaBO->podeAtualizar($parametros, $mensagemErro) === true) {
                $this->movimentoContaBO->calculaValorSaldoFinalConta($parametros, $movimentoContaORM);
            }
        }

        self::flushSeguro($mensagemErro);
        return empty($mensagemErro);
    }

    /**
     * Transfere um lançamento para outra conta
     *
     * @param string $mensagemErro
     * @param int $id
     * @param array $parametros
     *
     * @return boolean
     */
    public function transferirExistente (&$mensagemErro, $id, $parametros=[])
    {
        Logger::log('transferir lancamento para outra conta','SALDOS');
        if ($this->movimentoContaBO->verificaMovimentoContaExisteId($this->movimentoContaRepository, $id, $mensagemErro, $movimentoContaORM) === true) {
            if ($this->movimentoContaBO->verificaContaExisteBO($parametros, $mensagemErro, $parametros[ConstanteParametros::CHAVE_CONTA]) === true) {
                if ($movimentoContaORM->getTipoMovimentoConta()->getTipoOperacao() !== SituacoesSistema::OPERACAO_TRANSFERENCIA) {
                    if ($movimentoContaORM->getConciliado() === SituacoesSistema::CONCILIADO_NAO) {
                        $movimentoContaORM->setConta($parametros[ConstanteParametros::CHAVE_CONTA]);
                    } else {
                        $operacaoOriginal = $movimentoContaORM->getOperacao();
                        $parametros[ConstanteParametros::CHAVE_MC_VALOR_LANCAMENTO] = $movimentoContaORM->getValorLancamento();

                        // Inverte a operação
                        if ($operacaoOriginal === SituacoesSistema::OPERACAO_DEBITO) {
                            $movimentoContaORM->setOperacao(SituacoesSistema::OPERACAO_CREDITO);
                        } else {
                            $movimentoContaORM->setOperacao(SituacoesSistema::OPERACAO_DEBITO);
                        }

                        // Desfaz o saldo da conta origem
                        $this->movimentoContaBO->calculaValorSaldoFinalConta($parametros, $movimentoContaORM);

                        // Altera a conta para a de destino e volta a operação original
                        $movimentoContaORM->setConta($parametros[ConstanteParametros::CHAVE_CONTA]);
                        $movimentoContaORM->setOperacao($operacaoOriginal);

                        // Observação do lançamento errado
                        $observacao = $movimentoContaORM->getObservacao();
                        $observacao = "$observacao - Acerto de lançamento errado.";
                        $movimentoContaORM->setObservacao($observacao);

                        // Faz o saldo na conta destino
                        $this->movimentoContaBO->calculaValorSaldoFinalConta($parametros, $movimentoContaORM);
                    }//end if

                    self::flushSeguro($mensagemErro);
                    return empty($mensagemErro);
                } else {
                    $mensagemErro = "Não é possível transferir para outra conta uma movimentação de conta do tipo transferência.";
                }//end if
            }//end if
        }//end if

        return false;
    }

    /**
     * Gera as informações para a seleção de registros do relatório.
     *
     * @param string $mensagemErro Mensagem que ira retornar para o front-end
     * @param array  $parametros
     *
     * @return string
     */
    public function gerarDadosRelatoriosDeBalanceteFinanceiro($parametros)
    {
        return $this->movimentoContaRepository->prepararDadosRelatorioDeBalanceteFinanceiro($parametros);
    }

}