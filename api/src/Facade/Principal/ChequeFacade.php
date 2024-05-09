<?php

namespace App\Facade\Principal;

use Doctrine\Common\Persistence\ManagerRegistry;
use App\BO\Principal\ChequeBO;
use App\Helper\ConstanteParametros;
use App\Helper\FunctionHelper;
use App\Helper\SituacoesSistema;

/**
 *
 * @author Luiz A Costa
 */
class ChequeFacade extends GenericFacade
{
    /**
     *
     * @var \App\BO\Principal\ChequeBO
     */
    private $chequeBO;

    /**
     *
     * @var \App\Repository\Principal\ChequeRepository
     */
    private $chequeRepository;

    /**
     * {@inheritDoc}
     *
     * @see \App\Facade\Principal\GenericFacade::__construct()
     */
    function __construct (ManagerRegistry $managerRegistry, $connection="base_principal")
    {
        parent::__construct($managerRegistry);
        $this->chequeBO         = new ChequeBO(self::getEntityManager());
        $this->chequeRepository = self::getEntityManager()->getRepository(\App\Entity\Principal\Cheque::class);
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
        $retornoRepositorio = $this->chequeRepository->filtrarChequePorPagina($parametros, $parametros[ConstanteParametros::CHAVE_PAGINA]);
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
     *
     * @return array|\App\Entity\Principal\Cheque
     */
    public function buscarPorId(&$mensagemErro, $id)
    {
        $objetoORM = $this->chequeRepository->buscarRegistroPorId($id);
        if (empty($objetoORM) === true) {
            $mensagemErro = "Cheque não encontrado na base de dados.";
        }

        return $objetoORM;
    }

    /**
     * Cria um objeto no banco de dados
     *
     * @param string $mensagemErro Mensagem que ira retornar para front-end
     * @param array $parametros Parametros a serem inclusos no objeto
     * @param bool $persistFlush Flag para falar se deve persistir e executar o flush
     *
     * @return mixed|null|\App\Entity\Principal\Cheque
     */
    public function criar(&$mensagemErro, &$parametros=[], $persistFlush=true)
    {
        $objetoORM = null;
        if ($this->chequeBO->podeSalvar($parametros, $mensagemErro) === true) {
            $dataBomPara = $parametros[ConstanteParametros::CHAVE_DATA_BOM_PARA];
            unset($parametros[ConstanteParametros::CHAVE_DATA_BOM_PARA]);

            if (isset($parametros[ConstanteParametros::CHAVE_DATA_ENTRADA]) === true) {
                unset($parametros[ConstanteParametros::CHAVE_DATA_ENTRADA]);
            }

            if (isset($parametros[ConstanteParametros::CHAVE_DATA_BAIXA]) === true) {
                unset($parametros[ConstanteParametros::CHAVE_DATA_BAIXA]);
            }

            if (isset($parametros[ConstanteParametros::CHAVE_DATA_DEVOLUCAO]) === true) {
                unset($parametros[ConstanteParametros::CHAVE_DATA_DEVOLUCAO]);
            }

            if (isset($parametros[ConstanteParametros::CHAVE_SITUACAO]) === true && $parametros[ConstanteParametros::CHAVE_SITUACAO] === 'PEN') {
                $parametros[ConstanteParametros::CHAVE_SITUACAO] = SituacoesSistema::SITUACAO_CHEQUE_PENDENTE;
            }

            $objetoORM = \App\Factory\GeneralORMFactory::criar(\App\Entity\Principal\Cheque::class, true, $parametros);
            $parametros[ConstanteParametros::CHAVE_DATA_BOM_PARA] = $dataBomPara;

            $this->chequeBO->configuraParametros($parametros, $objetoORM, $mensagemErro);
            if ($persistFlush === true) {
                self::criarRegistro($objetoORM, $mensagemErro);
            } else {
                self::persistSeguro($objetoORM, $mensagemErro);
            }
        }//end if

        return $objetoORM;
    }

    /**
     * Cria os cheques partir do tituloPagar
     *
     * @param string $mensagem
     * @param \App\Entity\Principal\TituloPagar[] $titulosPagarORM
     * @param array $parametros
     *
     * @return array|NULL
     */
    public function criarComTituloPagar(&$mensagem, &$titulosPagarORM=[], $parametros=[])
    {
        $objetoORM = [];
        foreach ($parametros[ConstanteParametros::CHAVE_PARCELA] as $informacaoParcela) {
            $bErro = false;
            foreach ($titulosPagarORM as &$tituloPagarORM) {
                if (($tituloPagarORM->getFormaCobranca()->getFormaCheque() === true) && (isset($informacaoParcela[ConstanteParametros::CHAVE_CHEQUE]) === true) && (gettype($informacaoParcela[ConstanteParametros::CHAVE_CHEQUE]) === 'array')) {
                    if ((intval($informacaoParcela[ConstanteParametros::CHAVE_TIT_NUMERO_PARCELA_DOCUMENTO]) === $tituloPagarORM->getNumeroParcelaDocumento())) {
                        $informacaoParcela[ConstanteParametros::CHAVE_CHEQUE][ConstanteParametros::CHAVE_TITULO_PAGAR]      = $tituloPagarORM;
                        $informacaoParcela[ConstanteParametros::CHAVE_CHEQUE][ConstanteParametros::CHAVE_TITULO_RECEBER]    = null;
                        $informacaoParcela[ConstanteParametros::CHAVE_CHEQUE][ConstanteParametros::CHAVE_FRANQUEADA]        = $tituloPagarORM->getFranqueada();
                        $informacaoParcela[ConstanteParametros::CHAVE_CHEQUE][ConstanteParametros::CHAVE_ATENDENTE_USUARIO] = $tituloPagarORM->getContaPagar()->getUsuario();
                        $informacaoParcela[ConstanteParametros::CHAVE_CHEQUE][ConstanteParametros::CHAVE_PESSOA]            = $tituloPagarORM->getFavorecidoPessoa();
                        $informacaoParcela[ConstanteParametros::CHAVE_CHEQUE][ConstanteParametros::CHAVE_TIPO] = 'P';

                        $retornoORM = $this->criar($mensagem, $informacaoParcela[ConstanteParametros::CHAVE_CHEQUE], false);
                        if (is_null($retornoORM) === true) {
                            $bErro     = true;
                            $mensagem .= "Ocorreu um erro na criação do cheque da parcela de numero:" . $tituloPagarORM->getNumeroParcelaDocumento();
                        } else {
                            $tituloPagarORM->setCheque($retornoORM);
                            $objetoORM[] = $retornoORM;
                            self::persistSeguro($retornoORM, $mensagem);
                        }

                        break;
                    }//end if
                }//end if
            }//end foreach

            if ($bErro === true) {
                break;
            }
        }//end foreach

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
        $objetoORM = $this->chequeRepository->find($id);
        if (is_null($objetoORM) === true) {
            $mensagemErro = "Cheque não encontrado na base de dados.";
        } else {
            $parametros[ConstanteParametros::CHAVE_USUARIO] = $objetoORM->getAtendenteUsuario();
            if ($this->chequeBO->podeSalvar($parametros, $mensagemErro) === true) {
                $this->chequeBO->configuraParametros($parametros, $objetoORM, $mensagemErro);
                self::flushSeguro($mensagemErro);
            }
        }

        return empty($mensagemErro);
    }

    /**
     * Atualiza a data bom para de um cheque
     *
     * @param string $mensagemErro
     * @param int $id
     * @param array $parametros Campos e valores que iram ser atualizados
     *
     * @return boolean
     */
    public function atualizarInformacoes (&$mensagemErro, $id, $parametros=[])
    {
        $objetoORM = $this->chequeRepository->find($id);
        if (is_null($objetoORM) === true) {
            $mensagemErro = "Cheque não encontrado na base de dados.";
        } else {
            $this->chequeBO->configuraParametros($parametros, $objetoORM, $mensagemErro);
            self::persistSeguro($objetoORM, $mensagemErro);
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
        $objetoORM = $this->chequeRepository->find($id);
        if (is_null($objetoORM) === true) {
            $mensagemErro = "Cheque não encontrado na base de dados.";
        } else {
            if (($objetoORM->getSituacao() === 'P') || ($objetoORM->getSituacao() === 'D')) {
                $objetoORM->setExcluido(true);

                $tituloPagarORM   = $objetoORM->getTituloPagar();
                $tituloReceberORM = $objetoORM->getTituloReceber();

                if (is_null($tituloPagarORM) === false) {
                    $chequePagarORM = $tituloPagarORM->getCheque();
                    if ((is_null($chequePagarORM) === false) && ($chequePagarORM->getId() === $objetoORM->getId())) {
                        $tituloPagarORM->setCheque(null);
                    }
                }

                self::flushSeguro($mensagemErro);
            } else {
                $mensagemErro = "Apenas cheques marcados como 'pendente' ou 'devolvido' podem ser excluidos.";
            }//end if
        }//end if

        return empty($mensagemErro);
    }

    /**
     * Realiza a baixa de um cheque um registro no banco de dados
     *
     * @param string $mensagemErro Mensagem que ira retornar para front-end
     * @param array $cheques Array de cheques a retornar ao controller para movimentação de conta
     * @param array $chequesORM Array de objetos ORM de cheques a retornar ao controller para movimentação de conta
     * @param boolean $deveExecutarFlush Se deve executar o flush de alterações
     *
     * @return boolean
     */
    public function baixarCheque(&$mensagemErro, $cheques, &$chequesORM, $deveExecutarFlush=true)
    {
        foreach ($cheques as $cheque) {
            $objetoORM = $this->chequeRepository->find($cheque[ConstanteParametros::CHAVE_ID]);
            if (is_null($objetoORM) === true) {
                $mensagemErro = "Cheque não encontrado na base de dados.";
            } else {
                if ((is_null($objetoORM->getDataSegundaDevolucao()) === true) && (($objetoORM->getSituacao() === SituacoesSistema::SITUACAO_CHEQUE_PENDENTE) || ($objetoORM->getSituacao() === SituacoesSistema::SITUACAO_CHEQUE_DEVOLVIDO))) {
                    $objetoORM->setDataBaixa(FunctionHelper::formataCampoDateTimeJS($cheque[ConstanteParametros::CHAVE_DATA_BAIXA]));
                    $objetoORM->setSituacao(SituacoesSistema::SITUACAO_CHEQUE_BAIXADO);
                    $chequesORM[] = $objetoORM;
                } else {
                    $mensagemErro = "Para poder dar baixa no cheque é necessario que ele esteja marcado como \"pendente\" ou \"devolvido\" e somente devolvido uma vez.";
                }
            }
        }

        if ((empty($mensagemErro) === true) && ($deveExecutarFlush === true)) {
            self::flushSeguro($mensagemErro);
        }

        return empty($mensagemErro);
    }

    /**
     * Realiza a devolucao de um cheque
     *
     * @param string $mensagemErro Mensagem que ira retornar para front-end
     * @param array $parametros
     * @param array $parametrosMovimentosConta Parametros para movimentação de conta
     * @param boolean $deveExecutarFlush Se deverá efetuar o flush
     *
     * @return boolean
     */
    public function devolverCheque(&$mensagemErro, $parametros, &$parametrosMovimentosConta=[], $deveExecutarFlush=true)
    {
        $motivoDevolucaoId = $parametros[ConstanteParametros::CHAVE_MOTIVO_DEVOLUCAO_CHEQUE];
        $ids           = $parametros[ConstanteParametros::CHAVE_IDS];
        $dataDevolucao = $parametros[ConstanteParametros::CHAVE_DATA_DEVOLUCAO];
        foreach ($ids as $chave => $id) {
            $objetoORM = $this->chequeRepository->find($id);
            if (is_null($objetoORM) === true) {
                $mensagemErro = "Cheque não encontrado na base de dados.";
            } else {
                if ((($objetoORM->getSituacao() === SituacoesSistema::SITUACAO_CHEQUE_BAIXADO) || ($objetoORM->getSituacao() === SituacoesSistema::SITUACAO_CHEQUE_PENDENTE)) && (is_null($objetoORM->getDataSegundaDevolucao()) === true)) {
                    $motivoDevolucaoORM = $this->chequeBO->getMotivoDevolucaoChequeRepository()->find($motivoDevolucaoId);
                    if (is_null($motivoDevolucaoORM) === false) {
                        \App\Helper\FunctionHelper::formataCampoDateTimeJS($dataDevolucao, $dataDevolucao);
                        $objetoORM->setMotivoDevolucaoCheque($motivoDevolucaoORM);

                        if (is_null($objetoORM->getDataDevolucao()) === true) {
                            $objetoORM->setDataDevolucao($dataDevolucao);
                        } else {
                            $objetoORM->setDataSegundaDevolucao($dataDevolucao);
                        }

                        if ($objetoORM->getSituacao() === SituacoesSistema::SITUACAO_CHEQUE_BAIXADO) {
                            $tituloPagarORM   = $objetoORM->getTituloPagar();
                            $tituloReceberORM = $objetoORM->getTituloReceber();

                            $parameters = [
                                'estornado'         => 0,
                                'movimento_estorno' => 0,
                                'numero_documento'  => $objetoORM->getNumero(),
                            ];

                            $movimentoContaORM = null;
                            if (is_null($tituloPagarORM) === false) {
                                $parameters['titulo_pagar'] = $tituloPagarORM->getId();
                            }

                            if (is_null($tituloReceberORM) === false) {
                                $parameters['titulo_receber'] = $tituloReceberORM->getId();
                            }

                            $parametrosMovimentosConta[] = $parameters;
                        }//end if

                        $objetoORM->setSituacao(SituacoesSistema::SITUACAO_CHEQUE_DEVOLVIDO);
                    } else {
                        $mensagemErro = "Não foi possivel localizar o motivo de devolução do cheque selecionado. Id:" . $motivoDevolucaoId;
                    }//end if
                } else {
                    $mensagemErro = "Não é possível devolver cheques já devolvidos duas vezes";
                }//end if
            }//end if
        }//end foreach

        if ((empty($mensagemErro) === true) && ($deveExecutarFlush === true)) {
            self::flushSeguro($mensagemErro);
        }

        return empty($mensagemErro);
    }

    /**
     * Remove um registro do banco de dados
     *
     * @param string $mensagemErro Mensagem que ira retornar para o front-end
     * @param array $ids Array com chave primaria do registro
     *
     * @return boolean
     */
    public function removerMultiplos(&$mensagemErro, $ids)
    {
        foreach ($ids as $chave => $id) {
            $objetoORM = $this->chequeRepository->find($id);
            if (is_null($objetoORM) === true) {
                $mensagemErro = "Cheque de id:" . $id . " não encontrado na base de dados.";
            } else {
                if (($objetoORM->getSituacao() === 'P') || ($objetoORM->getSituacao() === 'D')) {
                    $objetoORM->setExcluido(true);
                    $tituloPagarORM   = $objetoORM->getTituloPagar();
                    $tituloReceberORM = $objetoORM->getTituloReceber();

                    if (is_null($tituloPagarORM) === false) {
                        $chequePagarORM = $tituloPagarORM->getCheque();
                        if ((is_null($chequePagarORM) === false) && ($chequePagarORM->getId() === $objetoORM->getId())) {
                            $tituloPagarORM->setCheque(null);
                        }
                    }
                } else {
                    $mensagemErro = "Apenas cheques marcados como 'pendente' ou 'devolvido' podem ser excluidos.";
                }//end if
            }//end if
        }//end foreach

        if (empty($mensagemErro) === true) {
            self::flushSeguro($mensagemErro);
        }

        return empty($mensagemErro);
    }


    /**
     * Valida se o array passado contém todas as informações necessárias para criação de cheque
     *
     * @param array $chequeMetadata
     *
     * @return boolean
     */
    public function possuiInformacoesCheque ($chequeMetadata)
    {
        $dadosValidos = true;
        if ((isset($chequeMetadata[ConstanteParametros::CHAVE_BANCO]) === false) || (empty($chequeMetadata[ConstanteParametros::CHAVE_BANCO]) === true)) {
            $dadosValidos = false;
        }

        if ((isset($chequeMetadata[ConstanteParametros::CHAVE_NUMERO]) === false) || (empty($chequeMetadata[ConstanteParametros::CHAVE_NUMERO]) === true)) {
            $dadosValidos = false;
        }

        if ((isset($chequeMetadata[ConstanteParametros::CHAVE_TITULAR]) === false) || (empty($chequeMetadata[ConstanteParametros::CHAVE_TITULAR]) === true)) {
            $dadosValidos = false;
        }

        if ((isset($chequeMetadata[ConstanteParametros::CHAVE_AGENCIA]) === false) || (empty($chequeMetadata[ConstanteParametros::CHAVE_AGENCIA]) === true)) {
            $dadosValidos = false;
        }

        return $dadosValidos;
    }

    /**
     * Possibilita o flush de operações de fora da Façade
     *
     * @param string $mensagemErro
     */
    public function flush (&$mensagemErro)
    {
        self::flushSeguro($mensagemErro);
    }

    /**
     * Gera as informações para a seleção de registros do relatório.
     *
     * @param string $mensagemErro Mensagem que ira retornar para o front-end
     * @param array  $parametros
     *
     * @return string
     */
    public function gerarDadosRelatorio ($filtros)
    {
        return $this->chequeRepository->prepararDadosRelatorio($filtros);
    }


    /**
     * Cancela o cheque passado
     *
     * @param string $mensagemErro
     * @param integer $id
     *
     * @return boolean
     */
    public function cancelar (&$mensagemErro, $id)
    {
        $cheque = $this->chequeRepository->find($id);

        if (is_null($cheque) === true) {
            $mensagemErro = "Cheque não encontrado.";
            return false;
        }

        if ($cheque->getSituacao() === SituacoesSistema::SITUACAO_CHEQUE_PENDENTE) {
            $cheque->setSituacao(SituacoesSistema::SITUACAO_CHEQUE_CANCELADO);
        }

        return empty($mensagemErro);
    }

    /**
     * @param $data
     * @return array []
     */
    public function agrupaDadosPorBanco($data): array
    {
        $return = [];
        foreach ($data as $item) {
            $return[$item['banco']][] = $item;
        }

        return $return;
    }


}
