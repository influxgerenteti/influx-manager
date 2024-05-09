<?php

namespace App\Facade\Principal;

use App\Facade\Principal\GenericFacade;
use Doctrine\Common\Persistence\ManagerRegistry;
use App\Helper\ConstanteParametros;
use App\Helper\SituacoesSistema;
use App\Helper\LockHelper;
use App\Facade\Principal\MovimentoContaFacade;
use App\Facade\Principal\ContaFacade;
use App\Facade\Principal\BoletoFacade;
use App\Facade\Principal\TipoMovimentoContaFacade;
use App\Facade\Principal\FormaPagamentoFacade;
use App\Facade\Log\LogFacade;

/**
 *
 * @author Rodrigo de Souza Fernandes (GATI labs)
 */
class RetornoFacade extends GenericFacade
{

    /**
     *
     * @var \App\Facade\Principal\MovimentoContaFacade
     */
    private $movimentoContaFacade;

    /**
     *
     * @var \App\Facade\Principal\BoletoFacade
     */
    private $boletoFacade;

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
     * @var \App\Facade\Log\LogFacade
     */
    private $logFacade;

    /**
     *
     * @var \App\Helper\LockHelper;
     */
    private $lockHelper;

    /**
     * {@inheritDoc}
     *
     * @see \App\Facade\Principal\GenericFacade::__construct()
     */
    function __construct (ManagerRegistry $managerRegistry, $connection="base_principal")
    {
        parent::__construct($managerRegistry);
        $this->movimentoContaFacade = new MovimentoContaFacade($managerRegistry);
        $this->boletoFacade         = new BoletoFacade($managerRegistry);
        $this->contaFacade          = new ContaFacade($managerRegistry);
        $this->tipoMovimentoContaFacade = new TipoMovimentoContaFacade($managerRegistry);
        $this->formaPagamentoFacade     = new FormaPagamentoFacade($managerRegistry);
        $this->logFacade  = new LogFacade($managerRegistry);
        $this->lockHelper = new LockHelper();
    }


    /**
     * Gera um array com os dados dos boletos não processados a serem mostrados na tela
     *
     * @param \App\Helper\RemessaPHP\Cnab\Retorno\IDetalhe $detalhe
     * @param string $situacao
     *
     * @return array
     */
    private function buscarDadosBoletosNaoProcessados($detalhe, $situacao='')
    {
        $contrato       = '';
        $nomeSacado     = $detalhe->segmento_t->nome_sacado;
        $dataVencimento = $detalhe->segmento_t->data_vencimento;
        $dataVencimento = str_pad($dataVencimento, 8, '0', STR_PAD_LEFT);
        $categoria      = '';
        $valorTitulo    = $detalhe->segmento_t->valor_titulo;
        $valorPago      = $detalhe->segmento_u->valor_pago;
        return [
            "contrato"        => $contrato,
            "nome_sacado"     => $nomeSacado,
            "data_vencimento" => $dataVencimento,
            "categoria"       => $categoria,
            "valor_titulo"    => $valorTitulo,
            "valor_pago"      => $valorPago,
            "situacao"        => $situacao,
        ];
    }

    /**
     * Importa o retorno de retorno
     *
     * @param request $requestHeader
     * @param string $caminhoArquivo
     * @param array $parametros
     * @param string $mensagemErro
     * @param array $boletosNE
     *
     * @return \App\Entity\Principal\Boleto[]|[] $boletos
     */
    public function importarRetorno ($requestHeader, $caminhoArquivo, &$parametros, &$mensagemErro, &$boletosNE)
    {
        $boletos     = [];
        $cnabFactory = new \App\Helper\RemessaPHP\Cnab\Factory();
        $arquivo     = $cnabFactory->createRetorno($caminhoArquivo);
        $detalhes    = $arquivo->listDetalhes();
        $status      = [
            SituacoesSistema::SITUACAO_CANCELADO,
            SituacoesSistema::SITUACAO_DEVOLVIDO,
            SituacoesSistema::SITUACAO_REJEITADO,
        ];

        $codigoBanco = (int) $arquivo->getCodigoBanco();
        $ehSicredi   = $codigoBanco === \App\Helper\RemessaPHP\Cnab\Banco::SICREDI;

        $parametrosConta = [
            ConstanteParametros::CHAVE_BANCO            => $codigoBanco,
            ConstanteParametros::CHAVE_EMPRESA_NO_BANCO => (int) $arquivo->getNumeroConta(),
        ];

        $dadosConta = $this->contaFacade->buscarPorParametros($parametrosConta);
        if ($dadosConta === null) {
            $mensagemErro .= 'O arquivo de importação fornecido não pertence a nenhuma conta cadastrada no sistema.';
            return [];
        }

        if ((int) $parametros[ConstanteParametros::CHAVE_FRANQUEADA] !== $dadosConta["franqueada"]["id"]) {
            $mensagemErro .= 'O arquivo de importação fornecido não pertence a uma conta da franqueada selecionada.';
            return [];
        }

        foreach ($detalhes as $detalhe) {
            $nossoNumeroCompleto = $detalhe->getNossoNumero();

            if ($ehSicredi === true) {
                $nossoNumero = substr($nossoNumeroCompleto, 3, 5);
            } else {
                $nossoNumero = $nossoNumeroCompleto;
            }

            $nossoNumero = (int) $nossoNumero;

            // Só podemos importar boletos que nós mesmos criamos. Se o terceiro dígito não for 2, são boletos criados por outros lugares
            if ($ehSicredi === true) {
                $digitoOrigem = substr($nossoNumeroCompleto, 2, 1);
                if ($digitoOrigem !== '2') {
                    $boletosNE[] = $this->buscarDadosBoletosNaoProcessados($detalhe, 'Boleto não foi emitido pelo sistema da Influx.');
                    continue;
                }
            }

            $parametrosBoleto = [
                ConstanteParametros::CHAVE_FRANQUEADA   => $parametros[ConstanteParametros::CHAVE_FRANQUEADA],
                ConstanteParametros::CHAVE_NOSSO_NUMERO => $nossoNumero,
                ConstanteParametros::CHAVE_CONTA        => $dadosConta["id"],
            ];
            $boletoORM        = $this->boletoFacade->buscarPorPropriedades($parametrosBoleto);
            if (is_null($boletoORM) === true) {
                if ($ehSicredi === true) {
                    $boletosNE[] = $this->buscarDadosBoletosNaoProcessados($detalhe, 'Boleto não encontrado no sistema.');
                } else {
                    $boletosNE[] = $detalhe;
                }

                continue;
            }

            if ($detalhe->getValorRecebido() > 0 && $detalhe->isBaixa() === true && in_array($boletoORM->getSituacaoCobranca(), $status) === false) {
                if ($ehSicredi === true) {
                    $data_contabil = $arquivo->getDataGeracao();
                    $data_deposito = $arquivo->getDataGeracao();
                } else {
                    $data_contabil = $detalhe->getDataCredito();
                    if ($data_contabil === false) {
                        $data_contabil = $detalhe->getDataOcorrencia();
                    }

                    $data_deposito = $detalhe->getDataOcorrencia();
                }

                $tipoMovimentoContaORM = $this->tipoMovimentoContaFacade->buscarPorPropriedades([ConstanteParametros::CHAVE_MC_TIPO_OPERACAO => SituacoesSistema::OPERACAO_CREDITO]);
                $formaPagamentoORM     = $this->formaPagamentoFacade->buscarPorPropriedades([ConstanteParametros::CHAVE_FORMA_BOLETO => true]);
                if ($ehSicredi === true) {
                    $juros = $detalhe->getValorPago() - $detalhe->getValorTitulo();
                    $multa = 0;
                } else {
                    $juros = $detalhe->getValorJuros();
                    $multa = $detalhe->getValorMoraMulta();
                }

                $valorDesconto       = 0;
                $valorOriginalTitulo = $boletoORM->getTituloReceber()->getValorOriginal();
                $valorRecebido       = $detalhe->getValorRecebido();
                if ($valorOriginalTitulo > $valorRecebido) {
                    $valorDesconto = $valorOriginalTitulo - $valorRecebido;
                }

                $parametrosMovimentoConta = [
                    ConstanteParametros::CHAVE_FRANQUEADA               => $parametros[ConstanteParametros::CHAVE_FRANQUEADA],
                    ConstanteParametros::CHAVE_BOLETO                   => $boletoORM->getId(),
                    ConstanteParametros::CHAVE_CONTA                    => $boletoORM->getConta()->getId(),
                    ConstanteParametros::CHAVE_TIPO_MOVIMENTO_CONTA     => $tipoMovimentoContaORM->getId(),
                    ConstanteParametros::CHAVE_TITULO_PAGAR             => null,
                    ConstanteParametros::CHAVE_TITULO_RECEBER           => $boletoORM->getTituloReceber()->getId(),
                    ConstanteParametros::CHAVE_FORMA_PAGAMENTO          => $formaPagamentoORM->getId(),
                    ConstanteParametros::CHAVE_MC_DATA_CONTABIL         => $data_contabil,
                    ConstanteParametros::CHAVE_MC_DATA_DEPOSITO         => $data_deposito,
                    ConstanteParametros::CHAVE_MC_VALOR_MONTANTE        => $valorOriginalTitulo,
                    ConstanteParametros::CHAVE_MC_VALOR_LANCAMENTO      => $valorRecebido,
                    ConstanteParametros::CHAVE_MC_VALOR_JUROS           => $juros,
                    ConstanteParametros::CHAVE_MC_VALOR_MULTA           => $multa,
                    ConstanteParametros::CHAVE_MC_VALOR_DESCONTO        => $valorDesconto,
                    ConstanteParametros::CHAVE_MC_VALOR_DIFERENCA_BAIXA => 0,
                    ConstanteParametros::CHAVE_CONCILIADO               => SituacoesSistema::CONCILIADO_SIM,
                    ConstanteParametros::CHAVE_OPERACAO                 => SituacoesSistema::OPERACAO_CREDITO,
                    ConstanteParametros::CHAVE_NUMERO_DOCUMENTO         => $detalhe->getNumeroDocumento(),
                    ConstanteParametros::CHAVE_USUARIO                  => $requestHeader->headers->get('Authorization-User-ID'),
                ];

                $this->lockHelper->setLock('movimento_conta_conta_' . $parametrosMovimentoConta[ConstanteParametros::CHAVE_CONTA]);
                if ($this->lockHelper->getLock()->isAcquired() === false) {
                    $this->lockHelper->getLock()->acquire(true);
                    $movimentoContaORM = $this->movimentoContaFacade->criar($mensagemErro, $parametrosMovimentoConta, false);
                    if (is_null($movimentoContaORM) === true || empty($mensagemErro) === false) {
                        $this->lockHelper->getLock()->release();
                        $parametros = $parametrosMovimentoConta;
                        return [];
                    }

                    $this->lockHelper->getLock()->release();
                } else {
                    $parametrosLog = [
                        ConstanteParametros::CHAVE_TIPO_EVENTO => \App\Facade\Log\LogFacade::$LOG_CREATE,
                        ConstanteParametros::CHAVE_IP_ORIGEM   => $requestHeader->getClientIp(),
                        ConstanteParametros::CHAVE_FRANQUEADA  => $parametrosMovimentoConta[ConstanteParametros::CHAVE_FRANQUEADA],
                        ConstanteParametros::CHAVE_USUARIO     => $parametrosMovimentoConta[ConstanteParametros::CHAVE_USUARIO],
                        ConstanteParametros::CHAVE_INFO_EVENTO => "Ocorreu um erro de Deadlock em:" . $requestHeader->getUri() . " \n Possivelmente 2 ou mais usuarios tentaram executar o update no mesmo registro, ao mesmo tempo.",
                    ];
                    $this->logFacade->criarLog($mensagemErro, $parametrosLog);
                    $parametros = $parametrosLog;
                    return [];
                }//end if
            } else {
                if ($boletoORM->getSituacaoCobranca() === SituacoesSistema::SITUACAO_ENVIADO || $boletoORM->getSituacaoCobranca() === SituacoesSistema::SITUACAO_PENDENTE) {
                    $boletoORM->setSituacaoCobranca(SituacoesSistema::SITUACAO_CONFIRMADO);
                }
            }//end if

            $boletos[] = $boletoORM->getId();
        }//end foreach

        self::flushSeguro($mensagemErro);

        if (empty($boletos) === true) {
            return [];
        }

        return $this->boletoFacade->buscarBoletosPorIds($boletos);
    }


}
