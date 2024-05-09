<?php

namespace App\Facade\Principal;

use App\Facade\Principal\GenericFacade;
use Doctrine\Common\Persistence\ManagerRegistry;
use App\Helper\ConstanteParametros;
use App\Helper\SituacoesSistema;
use App\Helper\LockHelper;
use App\BO\Principal\TransacaoCartaoBO;
use App\Facade\Principal\MovimentoContaFacade;
use App\Facade\Principal\TipoMovimentoContaFacade;
use App\Facade\Principal\FormaPagamentoFacade;
use App\Facade\Principal\ParcelamentoOperadoraCartaoFacade;
use App\Helper\FunctionHelper;

/**
 *
 * @author Luiz A Costa
 */
class TransacaoCartaoFacade extends GenericFacade
{

    /**
     *
     * @var \App\Repository\Principal\TransacaoCartaoRepository
     */
    private $transacaoCartaoRepository;

    /**
     *
     * @var \App\Repository\Principal\ContaRepository
     */
    private $contaRepository;

    /**
     *
     * @var \App\BO\Principal\TransacaoCartaoBO
     */
    private $transacaoCartaoBO;

    /**
     *
     * @var \App\Helper\LockHelper
     */
    private $lockHelper;

    /**
     *
     * @var \App\Facade\Principal\MovimentoContaFacade
     */
    private $movimentoContaFacade;

        /**
     *
     * @var \App\Repository\Principal\TituloReceberRepository
     */
    private $tituloReceberRepository;

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
     * @var \App\Facade\Principal\ParcelamentoOperadoraCartaoFacade
     */
    private $parcelamentoOperadoraCartaoFacade;

    /**
     * {@inheritDoc}
     *
     * @see \App\Facade\Principal\GenericFacade::__construct()
     */
    function __construct (ManagerRegistry $managerRegistry, $connection="base_principal")
    {
        parent::__construct($managerRegistry);
        $this->tituloReceberRepository  = self::getEntityManager()->getRepository(\App\Entity\Principal\TituloReceber::class);
        $this->transacaoCartaoRepository = self::getEntityManager()->getRepository(\App\Entity\Principal\TransacaoCartao::class);
        $this->contaRepository           = self::getEntityManager()->getRepository(\App\Entity\Principal\Conta::class);
        $this->transacaoCartaoBO         = new TransacaoCartaoBO(self::getEntityManager());
        $this->movimentoContaFacade      = new MovimentoContaFacade($managerRegistry);
        $this->tipoMovimentoContaFacade  = new TipoMovimentoContaFacade($managerRegistry);
        $this->formaPagamentoFacade      = new FormaPagamentoFacade($managerRegistry);
        $this->parcelamentoOperadoraCartaoFacade = new ParcelamentoOperadoraCartaoFacade(self::getManagerRegistry());
        $this->lockHelper = new LockHelper();
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
        $retornoRepositorio = $this->transacaoCartaoRepository->filtrarTransacaoCartaoPorPagina($parametros);
        $retorno            = [
            ConstanteParametros::CHAVE_TOTAL => $retornoRepositorio->getTotalItemCount(),
            ConstanteParametros::CHAVE_ITENS => $retornoRepositorio->getItems(),
        ];
        return $retorno;
    }

    /**
     * Busca o registro pela chave primaria
     *
     * @param string $mensagemErro Mensagem que ira retornar para front-end
     * @param int $id Chave primaria do registro
     * @param boolean $retornarObjeto
     *
     * @return array|NULL|\App\Entity\Principal\TransacaoCartao
     */
    public function buscarPorId(&$mensagemErro, $id, $retornarObjeto=false)
    {
        $objetoORM = null;
        if ($retornarObjeto === false) {
            $objetoORM = $this->transacaoCartaoRepository->buscarRegistroPorId($id);
        } else {
            $objetoORM = $this->transacaoCartaoRepository->find($id);
        }

        if (empty($objetoORM) === true) {
            $mensagemErro = "Transacao não encontrada na base de dados.";
        }

        return $objetoORM;
    }

    /**
     * Cria um objeto no banco de dados
     *
     * @param string $mensagemErro Mensagem que ira retornar para front-end
     * @param array $parametros Parametros a serem inclusos no objeto
     * @param boolean $persistFlush Flag para realizar o persist e flush
     *
     * @return mixed|null|\App\Entity\Principal\TransacaoCartao
     */
    public function criar(&$mensagemErro, $parametros=[], $persistFlush=true)
    {
        $objetoORM = null;
        if ($this->transacaoCartaoBO->podeSalvar($parametros, $mensagemErro) === true) {
            if (isset($parametros[ConstanteParametros::CHAVE_TAXA]) === true && empty($parametros[ConstanteParametros::CHAVE_TAXA]) === true) {
                unset($parametros[ConstanteParametros::CHAVE_TAXA]);
            }

            if (isset($parametros[ConstanteParametros::CHAVE_TAXA]) === false) {
                $this->transacaoCartaoBO->gerarTaxa($parametros, $mensagemErro);
            }

            $objetoORM = \App\Factory\GeneralORMFactory::criar(\App\Entity\Principal\TransacaoCartao::class, true, $parametros);
            if ($persistFlush === true) {
                self::criarRegistro($objetoORM, $mensagemErro);
            } else {
                self::persistSeguro($objetoORM, $mensagemErro);
            }
        }

        return $objetoORM;
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
        $objetoORM = $this->transacaoCartaoRepository->find($id);
        if (is_null($objetoORM) === true) {
            $mensagemErro = "Transacao não encontrada na base de dados.";
        } else {
            $this->transacaoCartaoBO->configuraParametros($parametros, $objetoORM);
            self::flushSeguro($mensagemErro);
        }

        return empty($mensagemErro);
    }

    /**
     * Remove um registro do banco de dados
     *
     * @param string $mensagemErro Mensagem que ira retornar para o front-end
     * @param int $id Chave primaria do registro
     *
     * @return boolean
     */
    public function remover(&$mensagemErro, $id)
    {
        $objetoORM = $this->transacaoCartaoRepository->find($id);
        if (is_null($objetoORM) === true) {
            $mensagemErro = "Transacao não encontrada na base de dados.";
        } else {
            $objetoORM->setSituacao(SituacoesSistema::SITUACAO_EXCLUIDO);
            self::flushSeguro($mensagemErro);
        }

        return empty($mensagemErro);
    }

    /**
     * Executa a validacao das regras para permitir salvar
     *
     * @param string $dataRecebimento data de recebimento do pagamento
     * @param int $parcelamentoOperadoraCartaoId id do parcelamento operadora cartao escolhido
     * @param string $mensagemErro
     *
     * @return boolean
     */
    public function gerarDataPrevisaoRepasse ($dataRecebimento, $parcelamentoOperadoraCartaoId, &$mensagemErro)
    {
        // FunctionHelper::formataCampoDateTimeJS($dataRecebimento, $dataRecebimento);
        $minimoDias = $this->parcelamentoOperadoraCartaoFacade->buscarMinimoDiasRepasseParcelamento($mensagemErro, $parcelamentoOperadoraCartaoId);
        if (empty($mensagemErro) === false) {
            return false;
        }

        $dataRecebimento->add(new \DateInterval('P' . $minimoDias . 'D'));
        // FunctionHelper::formataCampoJSDateTime($dataRecebimento, $dataRecebimento);

        return $dataRecebimento;
    }

    /**
     * Atualiza a data de repasse de uma transação de cartão
     *
     * @param string $mensagemErro
     * @param \App\Entity\Principal\TransacaoCartao $transacaoCartaoORM
     * @param string $data Nova data de repasse
     *
     * @return boolean
     */
    public function atualizarPrevisaoRepasse (&$mensagemErro, $transacaoCartaoORM, $data)
    {
        // FunctionHelper::formataCampoDateTimeJS($data, $data);

        $transacaoCartaoORM->setPrevisaoRepasse($data);
        self::persistSeguro($transacaoCartaoORM,$mensagemErro);

        return empty($mensagemErro);
    }

    /**
     * Valida se o array passado contém todas as informações necessárias para criação de transação de cartão
     *
     * @param array $transacaoCartaoMetadata
     *
     * @return boolean
     */
    public function possuiInformacoesCartao ($transacaoCartaoMetadata)
    {
        $dadosValidos = true;

        if ((isset($transacaoCartaoMetadata[ConstanteParametros::CHAVE_IDENTIFICADOR]) === false) || (empty($transacaoCartaoMetadata[ConstanteParametros::CHAVE_IDENTIFICADOR]) === true)) {
            $dadosValidos = false;
        }

        if ((isset($transacaoCartaoMetadata[ConstanteParametros::CHAVE_OPERADORA_CARTAO]) === false) || (empty($transacaoCartaoMetadata[ConstanteParametros::CHAVE_OPERADORA_CARTAO]) === true)) {
            $dadosValidos = false;
        }

        if ((isset($transacaoCartaoMetadata[ConstanteParametros::CHAVE_PARCELAMENTO_OPERADORA_CARTAO]) === false) || (empty($transacaoCartaoMetadata[ConstanteParametros::CHAVE_PARCELAMENTO_OPERADORA_CARTAO]) === true)) {
            $dadosValidos = false;
        }

        return $dadosValidos;
    }


    /**
     * Conciliar uma transação de cartão
     *
     * @param string $mensagemErro
     * @param array $parametros
     *
     * @return boolean
     */
    public function conciliar (&$mensagemErro, $parametros)
    {
        $transacaoCartao = $this->transacaoCartaoRepository->find($parametros[ConstanteParametros::CHAVE_ID]);
        $tituloReceber   = $transacaoCartao->getTituloReceber();
        $contaId         = $parametros[ConstanteParametros::CHAVE_CONTA];
        $dataConciliacao = $parametros[ConstanteParametros::CHAVE_DATA_CONCILIACAO];
        $contaORM        = $this->contaRepository->find($contaId);
        $tituloReceber->setConta($contaORM);
        $operacao = SituacoesSistema::OPERACAO_CREDITO;

        $tipoMovimentoConta = $this->tipoMovimentoContaFacade->buscarPorPropriedades([ConstanteParametros::CHAVE_MC_TIPO_OPERACAO => $operacao]);
        
        $dataHoje           = (new \DateTime())->format('Y-m-d\TH:i:s.uP');
        $dataContabil = $dataHoje;

        if ($dataConciliacao) {
            $dataContabil = (new \DateTime($dataConciliacao))->format('Y-m-d\TH:i:s.uP');
        }

        $propriedadesFormaPagamento = [];
        if ($transacaoCartao->getTipoTransacao() === SituacoesSistema::OPERACAO_DEBITO) {
            $propriedadesFormaPagamento['forma_cartao_debito'] = 1;
        } else {
            $propriedadesFormaPagamento['forma_cartao'] = 1;
        }

        $formaPagamento = $this->formaPagamentoFacade->buscarPorPropriedades($propriedadesFormaPagamento);

        $parametros = [
            ConstanteParametros::CHAVE_FRANQUEADA               => $transacaoCartao->getFranqueada()->getId(),
            ConstanteParametros::CHAVE_TITULO_PAGAR             => null,
            ConstanteParametros::CHAVE_TITULO_RECEBER           => $tituloReceber->getId(),
            ConstanteParametros::CHAVE_TRANSACAO_CARTAO         => $transacaoCartao->getId(),
            ConstanteParametros::CHAVE_BOLETO                   => null,
            ConstanteParametros::CHAVE_CHEQUE                   => null,
            ConstanteParametros::CHAVE_CONTA                    => $contaId,
            ConstanteParametros::CHAVE_TIPO_MOVIMENTO_CONTA     => $tipoMovimentoConta->getId(),
            ConstanteParametros::CHAVE_MC_VALOR_MONTANTE        => $transacaoCartao->getValorLiquido() + $transacaoCartao->getValorDesconto(),
            ConstanteParametros::CHAVE_MC_VALOR_LANCAMENTO      => $transacaoCartao->getValorLiquido(),
            ConstanteParametros::CHAVE_TAXA                     => $transacaoCartao->getTaxa(),
            ConstanteParametros::CHAVE_MC_VALOR_JUROS           => null,
            ConstanteParametros::CHAVE_MC_VALOR_MULTA           => null,
            ConstanteParametros::CHAVE_MC_VALOR_DESCONTO        => $transacaoCartao->getValorDesconto(),
            ConstanteParametros::CHAVE_MC_VALOR_DIFERENCA_BAIXA => null,
            ConstanteParametros::CHAVE_USUARIO                  => $parametros[ConstanteParametros::CHAVE_USUARIO],
            ConstanteParametros::CHAVE_OPERACAO                 => $operacao,
            ConstanteParametros::CHAVE_MC_DATA_CONTABIL         => $dataContabil,
            ConstanteParametros::CHAVE_MC_DATA_DEPOSITO         => $dataConciliacao,
            ConstanteParametros::CHAVE_FORMA_PAGAMENTO          => $formaPagamento->getId(),
        ];

        $this->lockHelper->setLock('movimento_conta_conta' . $contaId);
        if ($this->lockHelper->getLock()->isAcquired() === false) {
            $this->lockHelper->getLock()->acquire(true);
            $parametros[ConstanteParametros::CHAVE_MOVIMENTO_CONTA] = $this->movimentoContaFacade->criar($mensagemErro, $parametros, false);

            $transacaoCartao->setMovimentoConta($parametros[ConstanteParametros::CHAVE_MOVIMENTO_CONTA]);

            self::flushSeguro($mensagemErro);

            $this->lockHelper->getLock()->release();

            return empty($mensagemErro) === true;
        } else {
            $parametros[ConstanteParametros::CHAVE_ERRO_DEADLOCK] = true;
            $mensagemErro = "Não foi possível prosseguir com o pagamento, outra pessoa pode estar executando movimentações nesta conta. Tente novamente.";

            return false;
        }
    }

    /**
     * Estorna uma transação de cartão
     *
     * @param string $mensagemErro
     * @param integer $id
     * @param array $parametros
     *
     * @return boolean
     */
    public function estornar (&$mensagemErro, $id, &$parametros)
    {
        $transacaoCartao = $this->transacaoCartaoRepository->find($id);

        $tituloReceberID = $transacaoCartao->getTituloReceber()->getId();

        $tituloReceberORM = $this->tituloReceberRepository->find($tituloReceberID);
        
            
        if (is_null($transacaoCartao) === true) {
            $mensagemErro = "Transação de cartão não encontrada.";
            return false;
        }

        if (($transacaoCartao->getSituacao() !== SituacoesSistema::SITUACAO_CREDITADO) && ($transacaoCartao->getSituacao() !== SituacoesSistema::SITUACAO_PENDENTE)) {
            $mensagemErro = "A transação de cartão deve estar pendente ou creditada para ser estornada.";
            return false;
        }

        $movimentoConta = $transacaoCartao->getMovimentoConta();

        if (is_null($movimentoConta) === true) {
            
            $movimentosContaORM = $this->movimentoContaFacade->buscarMovimentoPorTitulo($mensagemErro, $tituloReceberID);
            if ($movimentosContaORM !== null) {
                foreach ($movimentosContaORM as $movimentoContaCartao) {
                    $this->movimentoContaFacade->estornar($mensagemErro, $parametros, $movimentoContaCartao);
                }
            } else {
                $tituloReceberORM = $this->tituloReceberRepository->find($tituloReceberID);
                    
                $tituloReceberORM = $tituloReceberORM->setSituacao(SituacoesSistema::SITUACAO_PENDENTE);
                $tituloReceberORM = $tituloReceberORM->setValorSaldoDevedor($transacaoCartao->getTituloReceber()->getValorOriginal());
            }
            $transacaoCartao->setSituacao(SituacoesSistema::SITUACAO_ESTORNADO);
            $transacaoCartao->setDataEstorno(new \DateTime());
        
            self::flushSeguro($mensagemErro);

        } else {
           $this->movimentoContaFacade->estornar($mensagemErro, $parametros, $movimentoConta);
        }

        return empty($mensagemErro);
    }


    /**
     * Cancela uma transação de cartão
     *
     * @param string $mensagemErro
     * @param integer $id
     *
     * @return boolean
     */
    public function cancelar (&$mensagemErro, $id)
    {
        $transacaoCartao = $this->transacaoCartaoRepository->find($id);

        if (is_null($transacaoCartao) === true) {
            $mensagemErro = "Transação de cartão não encontrada.";
            return false;
        }

        if ($transacaoCartao->getSituacao() === SituacoesSistema::SITUACAO_PENDENTE) {
            $transacaoCartao->setSituacao(SituacoesSistema::SITUACAO_EXCLUIDO);
        }

        return empty($mensagemErro);
    }


}
