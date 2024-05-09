<?php

namespace App\Facade\Principal;

use Doctrine\Common\Persistence\ManagerRegistry;
use App\Helper\ConstanteParametros;
use App\Helper\SituacoesSistema;
use App\Entity\Principal\TituloPagar;
use App\Entity\Principal\FormaPagamento;
use App\Entity\Principal\PlanoConta;
use App\BO\Principal\TituloPagarBO;
use App\BO\Principal\FormaPagamentoBO;
use App\BO\Principal\ParametrosFranqueadoraBO;

/**
 *
 * @author Rodrigo de Souza Fernandes (GATI labs)
 */
class TituloPagarFacade extends GenericFacade
{
    /**
     *
     * @var \App\Repository\Principal\TituloPagarRepository
     */
    private $tituloPagarRepository;

    /**
     *
     * @var \App\Repository\Principal\FormaPagamentoRepository
     */
    private $formaPagamentoRepository;

    /**
     *
     * @var \App\BO\Principal\TituloPagarBO
     */
    private $tituloPagarBO;

    /**
     *
     * @var \App\BO\Principal\ParametrosFranqueadoraBO
     */
    private $parametrosFranqueadoraBO;

    /**
     *
     * @var \App\Facade\Principal\ChequeFacade
     */
    private $chequeFacade;

    /**
     * {@inheritDoc}
     *
     * @see \App\Facade\Principal\GenericFacade::__construct()
     */
    function __construct (ManagerRegistry $managerRegistry, $connection="base_principal")
    {
        parent::__construct($managerRegistry);
        $this->tituloPagarRepository    = self::getEntityManager()->getRepository(TituloPagar::class);
        $this->formaPagamentoRepository = self::getEntityManager()->getRepository(FormaPagamento::class);
        $this->planoContaRepository     = self::getEntityManager()->getRepository(PlanoConta::class);
        $this->tituloPagarBO            = new TituloPagarBO(self::getEntityManager());
        $this->parametrosFranqueadoraBO = new ParametrosFranqueadoraBO(self::getEntityManager());
        $this->chequeFacade = new \App\Facade\Principal\ChequeFacade($managerRegistry);
    }


    /**
     * Lista todos os registros do banco de dados
     *
     * @param string $mensagemErro Mensagem que ira retornar para front-end
     * @param array $parametros Parametros da requisicao
     *
     * @return array
     */
    public function listar(&$mensagemErro, $parametros)
    {
        $titulosORM = [];
        if ($this->tituloPagarBO->podeListar($parametros, $mensagemErro) === true) {
            $retornoRepositorio = $this->tituloPagarRepository->filtrarTituloPagar($parametros, $parametros[ConstanteParametros::CHAVE_PAGINA], $parametros[ConstanteParametros::CHAVE_ITENS_POR_PAGINA]);
            $retorno            = [
                ConstanteParametros::CHAVE_TOTAL => $retornoRepositorio->getTotalItemCount(),
                ConstanteParametros::CHAVE_ITENS => $retornoRepositorio->getItems(),
            ];
            return $retorno;
        }

        return $titulosORM;
    }

    /**
     * Busca um registro atraves da ID
     *
     * @param string $mensagemErro Ponteiro de retorno de msg de erro para o front-end
     * @param integer $id Id do registro a ser buscado no banco de dados
     *
     * @return NULL|\App\Entity\Principal\TituloPagar
     */
    public function buscarPorId(&$mensagemErro, $id)
    {
        $tituloPagarORM = null;
        $this->tituloPagarBO->verificaTituloPagarExisteId($this->tituloPagarRepository, $id, $mensagemErro, $tituloPagarORM);
        return $tituloPagarORM;
    }

    /**
     * Cria um objeto no banco de dados
     *
     * @param string $mensagemErro Mensagem que ira retornar para front-end
     * @param \App\Entity\Principal\ContaPagar $objetoContaPagar Objeto da conta a pagar
     * @param array $parametros Parametros a serem inclusos no objeto
     * @param boolean $fazerFlush Se é necessário ou não fazer o flush
     *
     * @return array \App\Entity\Principal\TituloPagar[]|[]
     */
    public function criar(&$mensagemErro, $objetoContaPagar, $parametros=[], $fazerFlush=true)
    {
        $titulosORM         = [];
        $parcelasCalculadas = $this->calcular($mensagemErro, $parametros);
        if ($this->tituloPagarBO->podeCriar($parametros, $mensagemErro, $parcelasCalculadas) === true) {
            $franqueada    = $objetoContaPagar->getFranqueada();
            $fornecedor    = $objetoContaPagar->getFornecedorPessoa();
            $dataMovimento = $objetoContaPagar->getDataMovimento();
            unset($parametros[ConstanteParametros::CHAVE_SITUACAO]);
            foreach ($parametros[ConstanteParametros::CHAVE_PARCELA] as $parcela) {
                if ((isset($parcela[ConstanteParametros::CHAVE_ID]) === true) && (empty($parcela[ConstanteParametros::CHAVE_ID]) === false)) {
                    continue;
                }

                $paramTitPagar   = null;
                $paramTitReceber = $parametros;
                $paramTitPagar[ConstanteParametros::CHAVE_CONTA] = $parametros[ConstanteParametros::CHAVE_CONTA];
                $paramTitPagar[ConstanteParametros::CHAVE_FAVORECIDO_PESSOA]    = $fornecedor;
                $paramTitPagar[ConstanteParametros::CHAVE_FRANQUEADA]           = $franqueada;
                $paramTitPagar[ConstanteParametros::CHAVE_CONTA_PAGAR]          = $objetoContaPagar;
                $paramTitPagar[ConstanteParametros::CHAVE_TIT_DATA_VENCIMENTO]  = $parcela[ConstanteParametros::CHAVE_TIT_DATA_VENCIMENTO];
                $paramTitPagar[ConstanteParametros::CHAVE_TIT_DATA_PRORROGACAO] = $parcela[ConstanteParametros::CHAVE_TIT_DATA_VENCIMENTO];
                $paramTitPagar[ConstanteParametros::CHAVE_TIT_DATA_MOVIMENTO]   = $dataMovimento;

                $paramTitPagar[ConstanteParametros::CHAVE_TIT_NUMERO_PARCELA_DOCUMENTO] = $parcela[ConstanteParametros::CHAVE_TIT_NUMERO_PARCELA_DOCUMENTO];
                $paramTitPagar[ConstanteParametros::CHAVE_TIT_VALOR_DOCUMENTO]          = $parcela[ConstanteParametros::CHAVE_TIT_VALOR_DOCUMENTO];
                $paramTitPagar[ConstanteParametros::CHAVE_TIT_NARRATIVA_PLANO_CONTA]    = $parcela[ConstanteParametros::CHAVE_TIT_NARRATIVA_PLANO_CONTA];
                $paramTitPagar[ConstanteParametros::CHAVE_TIT_VALOR_SALDO] = $paramTitPagar[ConstanteParametros::CHAVE_TIT_VALOR_DOCUMENTO];
                $paramTitPagar[ConstanteParametros::CHAVE_OBSERVACAO]      = $parametros[ConstanteParametros::CHAVE_OBSERVACAO];

                if (FormaPagamentoBO::verificaFormaPagamentoExiste($this->formaPagamentoRepository, $parcela[ConstanteParametros::CHAVE_FORMA_COBRANCA], $mensagemErro, $paramTitPagar[ConstanteParametros::CHAVE_FORMA_COBRANCA]) === false) {
                    return $titulosORM;
                }

                $tituloPagarORM = \App\Factory\GeneralORMFactory::criar(TituloPagar::class, true, $paramTitPagar);
                self::persistSeguro($tituloPagarORM, $mensagemErro);
                $titulosORM[] = $tituloPagarORM;
            }//end foreach
        }//end if

        if ($fazerFlush === true) {
            self::flushSeguro($mensagemErro);
        }

        return $titulosORM;
    }

    /**
     * Cria um objeto no banco de dados
     *
     * @param string $mensagemErro
     * @param array $parcela
     * @param boolean $fazerFlush Se é necessário ou não fazer o flush
     *
     * @return array \App\Entity\Principal\TituloPagar[]|[]
     */
    public function atualizar (&$mensagemErro, $parcela, $fazerFlush=true)
    {
        $titulo = $this->buscarPorId($mensagemErro, $parcela[ConstanteParametros::CHAVE_ID]);

        if (is_null($titulo) === false && count($titulo->getMovimentoConta()) === 0 && $titulo->getValorDocumento() === $titulo->getValorSaldo()) {
            if (FormaPagamentoBO::verificaFormaPagamentoExiste($this->formaPagamentoRepository, $parcela[ConstanteParametros::CHAVE_FORMA_COBRANCA], $mensagemErro, $parcela[ConstanteParametros::CHAVE_FORMA_COBRANCA]) === false) {
                return null;
            }

            $parcela[ConstanteParametros::CHAVE_TIT_VALOR_SALDO]           = $parcela[ConstanteParametros::CHAVE_TIT_VALOR_DOCUMENTO];
            $parcela[ConstanteParametros::CHAVE_TIT_NARRATIVA_PLANO_CONTA] = $parcela[ConstanteParametros::CHAVE_TIT_NARRATIVA_PLANO_CONTA];

            \App\Helper\FunctionHelper::formataCampoDateTimeJS($parcela[ConstanteParametros::CHAVE_TIT_DATA_VENCIMENTO], $parcela[ConstanteParametros::CHAVE_TIT_DATA_VENCIMENTO]);
            \App\Helper\FunctionHelper::formataCampoDateTimeJS($parcela[ConstanteParametros::CHAVE_TIT_DATA_PRORROGACAO], $parcela[ConstanteParametros::CHAVE_TIT_DATA_PRORROGACAO]);

            if ($parcela[ConstanteParametros::CHAVE_FORMA_COBRANCA]->getFormaCheque() === false) {
                unset($parcela[ConstanteParametros::CHAVE_CHEQUE]);
                $titulo->setCheque(null);
            } else {
                $cheque = $titulo->getCheque();
                if (is_null($cheque) === false) {
                    $cheque->setValor($titulo->getValorDocumento());
                } else {
                    $chequeMetadata = $parcela[ConstanteParametros::CHAVE_CHEQUE];
                    $chequeMetadata[ConstanteParametros::CHAVE_TITULO_PAGAR]   = $titulo;
                    $chequeMetadata[ConstanteParametros::CHAVE_TITULO_RECEBER] = null;
                    $chequeMetadata[ConstanteParametros::CHAVE_PESSOA]         = $titulo->getFavorecidoPessoa();
                    $chequeMetadata[ConstanteParametros::CHAVE_FRANQUEADA]     = $parcela[ConstanteParametros::CHAVE_FRANQUEADA];
                    $chequeMetadata[ConstanteParametros::CHAVE_VALOR]          = $parcela[ConstanteParametros::CHAVE_TIT_VALOR_DOCUMENTO];
                    $chequeMetadata[ConstanteParametros::CHAVE_ATENDENTE_USUARIO] = $parcela[ConstanteParametros::CHAVE_ATENDENTE_USUARIO];

                    $cheque = $this->chequeFacade->criar($mensagemErro, $chequeMetadata, false);
                    $titulo->setCheque($cheque);
                }

                self::persistSeguro($cheque, $mensagemErro);
                $parcela[ConstanteParametros::CHAVE_CHEQUE] = $cheque;
            }//end if

            unset($parcela[ConstanteParametros::CHAVE_FRANQUEADA]);
            self::getFctHelper()->setParams($parcela, $titulo);

            if ($fazerFlush === true) {
                self::flushSeguro($mensagemErro);
            }
        }//end if

        return $titulo;
    }

    /**
     * Atualizado saldo do titulo, apos pagamento
     *
     * @param string $mensagemErro Mensagem que ira retornar para front-end
     * @param \App\Entity\Principal\MovimentoConta $movimentoContaORM Objeto de MovimentoConta
     * @param array $parametros Parametros a serem inclusos no objeto
     *
     * @return \App\Entity\Principal\TituloPagar $tituloPagarORM Objeto de TituloPagar
     */
    public function atualizarSaldo(&$mensagemErro, $movimentoContaORM, $parametros=[])
    {
        $tituloPagarORM = $movimentoContaORM->getTituloPagar();
        $parametros[ConstanteParametros::CHAVE_TIT_VALOR_SALDO]       = (float) $tituloPagarORM->getValorSaldo();
        $parametros[ConstanteParametros::CHAVE_VALOR_SALDO_CALCULADO] = 0;

        if ($this->tituloPagarBO->podeAtualizarSaldo($parametros, $mensagemErro) === true) {
            $this->tituloPagarBO->calculaValoresSaldo($parametros, $tituloPagarORM);
        }

        return $tituloPagarORM;
    }

    /**
     * Calcula as parcelas conforme o percentual da parcela
     *
     * @param string $mensagemErro Mensagem que ira retornar para front-end
     * @param array $parametros Parametros a serem inclusos no objeto
     *
     * @return array $parcelas Retorna um array de Parcelas calculadas
     */
    public function calcular(&$mensagemErro, $parametros=[])
    {
        $condicaoPagamentoORM = null;
        $parcelas = [];
        if ($this->tituloPagarBO->podeCalcularParcelas($parametros, $mensagemErro, $condicaoPagamentoORM) === true) {
            $condicaoPagamentoParcelas = $condicaoPagamentoORM->getCondicaoPagamentoParcelas();
            $this->tituloPagarBO->calculaTotalParcelas($condicaoPagamentoParcelas, $parametros, $parcelas);
        }//end if

        return $parcelas;
    }

    /**
     * Remove todos os títulos a pagar de uma conta
     *
     * @param string $mensagemErro Mensagem que ira retornar para front-end
     * @param int $idContaPagar Chave primaria da conta a pagar
     *
     * @return boolean
     */
    public function excluir (&$mensagemErro, $idContaPagar)
    {
        $objetosORM = $this->tituloPagarRepository->findBy(["conta_pagar" => $idContaPagar]);
        $bRetorno   = true;
        if (count($objetosORM) > 0) {
            foreach ($objetosORM as $tituloPagarORM) {
                if ($tituloPagarORM->getValorSaldo() === $tituloPagarORM->getValorDocumento()) {
                    self::removerSeguro($tituloPagarORM, $mensagemErro);
                    if (empty($mensagemErro) === false) {
                        $bRetorno      = false;
                        $mensagemErro .= "Ocorreu um erro ao excluir a nota: " . $mensagemErro;
                        break;
                    }
                } else {
                    $bRetorno      = false;
                    $mensagemErro .= "Favor realizar o estorno do pagamento para poder prosseguir com a exclusão.";
                    break;
                }
            }
        }

        return $bRetorno;
    }

    /**
     * Remove o título a pagar apenas
     *
     * @param string $mensagemErro
     * @param \App\Entity\Principal\TituloPagar $tituloPagar
     */
    public function remover (&$mensagemErro, &$tituloPagar)
    {
        self::removerSeguro($tituloPagar, $mensagemErro);
    }

    /**
     * Remove o título a pagar pela ID
     *
     * @param string $mensagemErro
     * @param integer $id
     *
     * @return boolean
     */
    public function removerPorID (&$mensagemErro, $id)
    {
        $tituloPagar = $this->tituloPagarRepository->find($id);
        $retorno     = true;

        if ($tituloPagar->getValorSaldo() === $tituloPagar->getValorDocumento()) {
            $cheque = $tituloPagar->getCheque();
            if (is_null($cheque) === false) {
                $cheque->setExcluido(1);
            }

            $tituloPagar->setExcluido(1);

            $pagamentosFuncionario = $tituloPagar->getContaPagar()->getPagamentoFuncionarios();
            if (count($pagamentosFuncionario) > 0) {
                foreach ($pagamentosFuncionario as $pagamento) {
                    if (($pagamento->getSituacao() !== SituacoesSistema::SITUACAO_PAGO) && ($pagamento->getSituacao() !== SituacoesSistema::SITUACAO_CANCELADO)) {
                        $pagamento->setSituacao(SituacoesSistema::SITUACAO_CANCELADO);
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

            self::flushSeguro($mensagemErro);

            if (empty($mensagemErro) === false) {
                $retorno = false;
            }
        } else {
            $mensagemErro = 'É necessário fazer o estorno dos pagamentos desta conta para prosseguir com a exclusão.';
            $retorno      = false;
        }//end if

        return $retorno;
    }

    /**
     * Executa o flush das operações de banco
     *
     * @param string $mensagemErro

     * @return void
     */
    public function flush (&$mensagemErro)
    {
        self::flushSeguro($mensagemErro);
    }

    /**
     * Gera as informações para a seleção de registros do relatório.
     *
     * @param array  $parametros
     *
     * @return string
     */
    public function gerarDadosRelatorio($parametros)
    {
        return $this->tituloPagarRepository->prepararDadosRelatorio($parametros);
    }


}
