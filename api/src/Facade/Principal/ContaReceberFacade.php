<?php

namespace App\Facade\Principal;

use Doctrine\Common\Persistence\ManagerRegistry;
use App\Helper\ConstanteParametros;
use App\BO\Principal\ContaReceberBO;
use App\Facade\Principal\ItemContaReceberFacade;
use App\Facade\Principal\MovimentoEstoqueFacade;
use App\Facade\Principal\TituloReceberFacade;
use App\Facade\Principal\ChequeFacade;
use App\Facade\Principal\BoletoFacade;
use App\Facade\Principal\TransferenciaBancariaFacade;
use App\Facade\Principal\TransacaoCartaoFacade;
use App\Helper\SituacoesSistema;
use App\Helper\VariaveisCompartilhadas;

/**
 *
 * @author Luiz A Costa
 */
class ContaReceberFacade extends GenericFacade
{

    /**
     *
     * @var \App\Facade\Principal\ItemContaReceberFacade
     */
    private $itemContaReceberFacade;

    /**
     *
     * @var \App\Facade\Principal\MovimentoContaFacade
     */
    private $movimentoContaFacade;

    /**
     *
     * @var \App\Facade\Principal\MovimentoEstoqueFacade
     */
    private $movimentoEstoqueFacade;

    /**
     *
     * @var \App\Facade\Principal\TituloReceberFacade
     */
    private $tituloReceberFacade;

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
     * @var \App\Facade\Principal\TransferenciaBancariaFacade
     */
    private $transferenciaBancariaFacade;

    /**
     *
     * @var \App\Facade\Principal\TransacaoCartaoFacade
     */
    private $transacaoCartaoFacade;

    /**
     *
     * @var \App\Repository\Principal\ContaReceberRepository
     */
    private $contaReceberRepository;

    /**
     *
     * @var \App\BO\Principal\ContaReceberBO
     */
    private $contaReceberBO;

    /**
     *
     * @var \App\Repository\Principal\FuncionarioRepository
     */
    private $funcionarioRepository;

    /**
     *
     * @var \App\Repository\Principal\AlunoRepository
     */
    private $alunoRepository;

    /**
     *
     * @var \App\Repository\Principal\ItemRepository
     */
    private $itemRepository;

    /**
     *
     * @var \App\Repository\Principal\TipoMovimentoContaRepository
     */
    private $tipoMovimentoContaRepository;

    /**
     * Retorna a id do primeiro funcionario que tiver o usuario informado caso não encontre, retornará null
     *
     * @param string $usuarioId
     *
     * @return number|NULL
     */
    private function retornaFuncionarioIdDoUsuario($usuarioId)
    {
        $funcionarios = $this->funcionarioRepository->findBy([ConstanteParametros::CHAVE_USUARIO => $usuarioId]);
        if (count($funcionarios) > 0) {
            $funcionarioORM = $funcionarios[0];
            return $funcionarioORM->getId();
        }

        return null;
    }

    /**
     * Retorna a pessoa id do responsavel financeiro do aluno
     *
     * @param int $alunoId
     *
     * @return number|NULL
     */
    private function retornaPessoaIdDoResponsavelFinanceiroAluno($alunoId)
    {
        $alunoORM = $this->alunoRepository->find($alunoId);
        if (is_null($alunoORM) === false) {
            if (is_null($alunoORM->getResponsavelFinanceiroPessoa()) === false) {
                return $alunoORM->getResponsavelFinanceiroPessoa()->getId();
            }
        }

        return null;
    }

    /**
     * Cria os parametros para criação da movimentação de conta
     *
     * @param int $usuarioId
     * @param int $valorDesconto
     * @param int $valorFinalConta
     * @param int $contaReceberContaPadraoId Conta id
     * @param array $parametrosTituloReceber
     *
     * @return array
     */
    private function criarParametrosMovimentoConta($usuarioId, $valorDesconto, $valorFinalConta, $contaReceberContaPadraoId, $parametrosTituloReceber)
    {
        $tipoMovimentoContaORM = $this->tipoMovimentoContaRepository->findOneBy([ConstanteParametros::CHAVE_TIPO_OPERACAO => "C"]);
        $contaId = $parametrosTituloReceber[ConstanteParametros::CHAVE_CONTA];
        if (empty($contaId) === true) {
            $contaId = $contaReceberContaPadraoId;
        }

        $descontoSuperAmigos = null;
        if (isset($parametrosTituloReceber[ConstanteParametros::CHAVE_DESCONTO_SUPER_AMIGOS]) === true && empty($parametrosTituloReceber[ConstanteParametros::CHAVE_DESCONTO_SUPER_AMIGOS]) === false) {
            $descontoSuperAmigos = $parametrosTituloReceber[ConstanteParametros::CHAVE_DESCONTO_SUPER_AMIGOS];
            unset($parametrosTituloReceber[ConstanteParametros::CHAVE_DESCONTO_SUPER_AMIGOS]);
        }

        return [
            ConstanteParametros::CHAVE_TITULO_PAGAR            => null,
            ConstanteParametros::CHAVE_TIPO_MOVIMENTO_CONTA    => $tipoMovimentoContaORM,
            ConstanteParametros::CHAVE_USUARIO                 => $usuarioId,
            ConstanteParametros::CHAVE_MC_DATA_CONTABIL        => new \DateTime(),
            ConstanteParametros::CHAVE_MC_DATA_DEPOSITO        => new \DateTime(),
            ConstanteParametros::CHAVE_CONTA                   => $contaId,
            ConstanteParametros::CHAVE_FRANQUEADA              => VariaveisCompartilhadas::$franqueadaID,
            ConstanteParametros::CHAVE_FORMA_PAGAMENTO         => $parametrosTituloReceber[ConstanteParametros::CHAVE_FORMA_COBRANCA],
            ConstanteParametros::CHAVE_OPERACAO                => "C",
            ConstanteParametros::CHAVE_VALOR_LANCAMENTO        => 0.0,
            ConstanteParametros::CHAVE_VALOR_TITULO            => $parametrosTituloReceber[ConstanteParametros::CHAVE_VALOR_SALDO_DEVEDOR],
            ConstanteParametros::CHAVE_VALOR_DESCONTO          => $valorDesconto,
            ConstanteParametros::CHAVE_VALOR_SALDO_FINAL_CONTA => $valorFinalConta,
            ConstanteParametros::CHAVE_CONCILIADO              => "S",
            ConstanteParametros::CHAVE_MC_VALOR_MONTANTE       => $parametrosTituloReceber[ConstanteParametros::CHAVE_VALOR_ORIGINAL],
            ConstanteParametros::CHAVE_DESCONTO_SUPER_AMIGOS   => $descontoSuperAmigos,
        ];
    }

    /**
     * Cálculo fixo do valor de desconto de super-amigos
     *
     * @param int $divisor
     *
     * @return double
     */
    private function calcularDescontoSuperAmigos($divisor)
    {
        $itemOrm        = $this->itemRepository->buscarItemValorCurso();
        $itemValorCurso = $itemOrm->getItemFranqueadas()->get(0);
        $valorItem      = 1;
        if (is_null($itemValorCurso) === false) {
            $valorItem = (float) $itemValorCurso->getValorVenda();
        }

        return 0;
        //return ($valorItem / $divisor) * 0.9;
    }

    /**
     * Aplica o Desconto de super amigo
     *
     * @param boolean $bAplicaDescontoSuperAmigos
     * @param string $mensagemErro
     * @param \App\Entity\Principal\ContaReceber $contaReceberORM
     * @param int $alunoIndicadorId
     * @param array $parametrosTituloReceber
     *
     * @return boolean
     */
    private function aplicaDescontoSuperAmigos($bAplicaDescontoSuperAmigos, &$mensagemErro, $contaReceberORM, $alunoIndicadorId, &$parametrosTituloReceber)
    {
        $bRetorno = true;
        if ((bool) $bAplicaDescontoSuperAmigos === true) {
            $alunoORM   = $this->alunoRepository->buscarAlunoPorIdComContratoVigente($alunoIndicadorId);
            $franqueada = $contaReceberORM->getFranqueada();
            if (is_null($alunoORM) === true) {
                $bRetorno      = false;
                $mensagemErro .= "Aluno que indicou não foi encontrado na base.";
            } else {
                if ($franqueada->getDescontoSuperAmigosAtivo() === true) {
                    $descontoParametrizadoOriginal = $this->calcularDescontoSuperAmigos(12);
                    $descontoParametrizado         = $descontoParametrizadoOriginal;

                    // Registro do histórico de super-amigos - indicado
                    $parametrosSuperAmigos  = [
                        ConstanteParametros::CHAVE_CONTRATO        => $contaReceberORM->getContrato(),
                        ConstanteParametros::CHAVE_ALUNO           => $contaReceberORM->getAluno(),
                        ConstanteParametros::CHAVE_NOME_DESCONTO   => 'Super-Amigos',
                        ConstanteParametros::CHAVE_VALOR_ORIGINAL  => $descontoParametrizadoOriginal,
                        ConstanteParametros::CHAVE_TIT_VALOR_SALDO => $descontoParametrizadoOriginal,
                        ConstanteParametros::CHAVE_INDICADOR       => 0,
                        ConstanteParametros::CHAVE_FINALIZADO      => 0,
                    ];
                    $superAmigosIndicadoORM = \App\Factory\GeneralORMFactory::criar(\App\Entity\Principal\DescontoSuperAmigo::class, true, $parametrosSuperAmigos);
                    self::persistSeguro($superAmigosIndicadoORM, $mensagemErro);

                    foreach ($parametrosTituloReceber as &$tituloReceberMetaData) {
                        $descontoAplicado = $descontoParametrizado;
                        $resultado        = $tituloReceberMetaData[ConstanteParametros::CHAVE_VALOR_SALDO_DEVEDOR] - $descontoParametrizado;

                        $saldoSuperAmigos = $superAmigosIndicadoORM->getValorSaldo() - $tituloReceberMetaData[ConstanteParametros::CHAVE_VALOR_SALDO_DEVEDOR];
                        if ($saldoSuperAmigos < 0) {
                            $saldoSuperAmigos = 0.0;
                        }

                        $superAmigosIndicadoORM->setValorSaldo($saldoSuperAmigos);

                        $tituloReceberMetaData[ConstanteParametros::CHAVE_DESCONTO_SUPER_AMIGOS] = $superAmigosIndicadoORM;
                        $tituloReceberMetaData[ConstanteParametros::CHAVE_VALOR_SALDO_DEVEDOR]   = $resultado;

                        if ($resultado < 0) {
                            $descontoParametrizado = $resultado * (-1);
                            $tituloReceberMetaData[ConstanteParametros::CHAVE_VALOR_SALDO_DEVEDOR] = 0.0;
                        }

                        $parametrosMovimentoConta = $this->criarParametrosMovimentoConta($contaReceberORM->getUsuario()->getId(), $descontoAplicado, $contaReceberORM->getFranqueada()->getContaPadrao()->getValorSaldo(), $contaReceberORM->getFranqueada()->getContaPadrao()->getId(), $tituloReceberMetaData);
                        $tituloReceberMetaData[ConstanteParametros::CHAVE_MOVIMENTO_CONTA_PARAMETROS] = $parametrosMovimentoConta;
                        if ($resultado >= 0) {
                            break;
                        }
                    }//end foreach

                    // Se o valor do saldo do Super-Amigos for zero, está finalizado
                    if ($superAmigosIndicadoORM->getValorSaldo() === 0.0) {
                        $superAmigosIndicadoORM->setFinalizado(true);
                    }

                    $descontoParametrizado     = $descontoParametrizadoOriginal;
                    $filtroPorCampo            = \Doctrine\Common\Collections\Criteria::create();
                    $filtroPorCampo            = $filtroPorCampo->orderBy(["id" => \Doctrine\Common\Collections\Criteria::DESC]);
                    $contasReceberIndicadorORM = $alunoORM->getAlunoContaReceber()->matching($filtroPorCampo);

                    // Registro do histórico de super-amigos - indicador
                    $parametrosSuperAmigosIndicador = [
                        ConstanteParametros::CHAVE_CONTRATO        => $contaReceberORM->getContrato(),
                        ConstanteParametros::CHAVE_ALUNO           => $alunoORM,
                        ConstanteParametros::CHAVE_NOME_DESCONTO   => 'Super-Amigos',
                        ConstanteParametros::CHAVE_VALOR_ORIGINAL  => $descontoParametrizado,
                        ConstanteParametros::CHAVE_TIT_VALOR_SALDO => $descontoParametrizado,
                        ConstanteParametros::CHAVE_INDICADOR       => 1,
                        ConstanteParametros::CHAVE_FINALIZADO      => 0,
                    ];
                    $superAmigosIndicadorORM        = \App\Factory\GeneralORMFactory::criar(\App\Entity\Principal\DescontoSuperAmigo::class, true, $parametrosSuperAmigosIndicador);
                    self::persistSeguro($superAmigosIndicadorORM, $mensagemErro);

                    foreach ($contasReceberIndicadorORM as &$contaReceberIndicadorORM) {
                        $filtroPorCampo    = \Doctrine\Common\Collections\Criteria::create();
                        $filtroPorCampo    = $filtroPorCampo->orderBy(["numero_parcela_documento" => \Doctrine\Common\Collections\Criteria::DESC]);
                        $titulosReceberORM = $contaReceberIndicadorORM->getTituloRecebers()->matching($filtroPorCampo);
                        foreach ($titulosReceberORM as &$tituloReceberORM) {
                            $descontoAplicado = $descontoParametrizado;
                            $resultado        = $tituloReceberORM->getValorSaldoDevedor() - $descontoParametrizado;

                            $saldoSuperAmigos = $superAmigosIndicadorORM->getValorSaldo() - $tituloReceberORM->getValorSaldoDevedor();
                            if ($saldoSuperAmigos < 0) {
                                $saldoSuperAmigos = 0.0;
                            }

                            $superAmigosIndicadorORM->setValorSaldo($saldoSuperAmigos);
                            $tituloReceberORM->setValorSaldoDevedor($resultado);

                            if ($resultado < 0) {
                                $descontoParametrizado = $resultado * (-1);
                                $tituloReceberORM->setValorSaldoDevedor(0.0);
                            }

                            $parametrosTituloReceberIndicador  = [
                                ConstanteParametros::CHAVE_CONTA                 => $tituloReceberORM->getConta()->getId(),
                                ConstanteParametros::CHAVE_FORMA_COBRANCA        => $tituloReceberORM->getFormaCobranca()->getId(),
                                ConstanteParametros::CHAVE_VALOR_SALDO_DEVEDOR   => $resultado,
                                ConstanteParametros::CHAVE_VALOR_ORIGINAL        => $tituloReceberORM->getValorOriginal(),
                                ConstanteParametros::CHAVE_DESCONTO_SUPER_AMIGOS => $superAmigosIndicadorORM,
                            ];
                            $parametrosMovimentoContaIndicador = $this->criarParametrosMovimentoConta($contaReceberIndicadorORM->getUsuario()->getId(), $descontoAplicado, $contaReceberIndicadorORM->getFranqueada()->getContaPadrao()->getValorSaldo(), $contaReceberIndicadorORM->getFranqueada()->getContaPadrao()->getId(), $parametrosTituloReceberIndicador);
                            $movimentoContaIndicador           = $this->movimentoContaFacade->criar($mensagemErro, $parametrosMovimentoContaIndicador, false);
                            if ((is_null($movimentoContaIndicador) === true) || (empty($mensagemErro) === false)) {
                                $bRetorno = false;
                                break;
                            }

                            $tituloReceberORM->addMovimentoConta($movimentoContaIndicador);
                            if ($resultado >= 0) {
                                break;
                            }
                        }//end foreach
                    }//end foreach

                    // Se o valor do saldo do Super-Amigos for zero, está finalizado
                    if ($superAmigosIndicadorORM->getValorSaldo() === 0.0) {
                        $superAmigosIndicadorORM->setFinalizado(true);
                    }
                } else {
                    $bRetorno      = false;
                    $mensagemErro .= "O desconto de super_amigos foi desabilitado pela franqueada.\nFavor entrar em contato com o suporte.";
                }//end if
            }//end if
        }//end if

        return $bRetorno;
    }

    /**
     * Aplica o Desconto de super amigo turbinados
     *
     * @param boolean $bAplicaDescontoSuperAmigosTurbinados
     * @param string $mensagemErro
     * @param \App\Entity\Principal\ContaReceber $contaReceberORM
     * @param int $alunoIndicadorId
     * @param array $parametrosTituloReceber
     *
     * @return boolean
     */
    private function aplicaDescontoSuperAmigosTurbinados($bAplicaDescontoSuperAmigosTurbinados, &$mensagemErro, $contaReceberORM, $alunoIndicadorId, &$parametrosTituloReceber)
    {
        $bRetorno = true;
        if ((bool) $bAplicaDescontoSuperAmigosTurbinados === true) {
            $alunoORM   = $this->alunoRepository->buscarAlunoPorIdComContratoVigente($alunoIndicadorId);
            $franqueada = $contaReceberORM->getFranqueada();
            if (is_null($alunoORM) === true) {
                $bRetorno      = false;
                $mensagemErro .= "Aluno que indicou não foi encontrado na base.";
            } else {
                if ($franqueada->getDescontoSuperAmigosTurbinadoAtivo() === true) {
                    $descontoParametrizadoOriginal = $this->calcularDescontoSuperAmigos(12);
                    $descontoParametrizado         = $descontoParametrizadoOriginal;

                    // Registro do histórico de super-amigos - indicado
                    $parametrosSuperAmigos  = [
                        ConstanteParametros::CHAVE_CONTRATO        => $contaReceberORM->getContrato(),
                        ConstanteParametros::CHAVE_ALUNO           => $contaReceberORM->getAluno(),
                        ConstanteParametros::CHAVE_NOME_DESCONTO   => 'Super-Amigos Turbinado',
                        ConstanteParametros::CHAVE_VALOR_ORIGINAL  => $descontoParametrizadoOriginal,
                        ConstanteParametros::CHAVE_TIT_VALOR_SALDO => $descontoParametrizadoOriginal,
                        ConstanteParametros::CHAVE_INDICADOR       => 0,
                        ConstanteParametros::CHAVE_FINALIZADO      => 0,
                    ];
                    $superAmigosIndicadoORM = \App\Factory\GeneralORMFactory::criar(\App\Entity\Principal\DescontoSuperAmigo::class, true, $parametrosSuperAmigos);
                    self::persistSeguro($superAmigosIndicadoORM, $mensagemErro);

                    foreach ($parametrosTituloReceber as &$tituloReceberMetaData) {
                        $descontoAplicado = $descontoParametrizado;
                        $resultado        = $tituloReceberMetaData[ConstanteParametros::CHAVE_VALOR_SALDO_DEVEDOR] - $descontoParametrizado;

                        $saldoSuperAmigos = $superAmigosIndicadoORM->getValorSaldo() - $tituloReceberMetaData[ConstanteParametros::CHAVE_VALOR_SALDO_DEVEDOR];
                        if ($saldoSuperAmigos < 0) {
                            $saldoSuperAmigos = 0.0;
                        }

                        $superAmigosIndicadoORM->setValorSaldo($saldoSuperAmigos);
                        $tituloReceberMetaData[ConstanteParametros::CHAVE_DESCONTO_SUPER_AMIGOS] = $superAmigosIndicadoORM;
                        $tituloReceberMetaData[ConstanteParametros::CHAVE_VALOR_SALDO_DEVEDOR]   = $resultado;
                        if ($resultado < 0) {
                            $descontoParametrizado = $resultado * (-1);
                            $tituloReceberMetaData[ConstanteParametros::CHAVE_VALOR_SALDO_DEVEDOR] = 0.0;
                        }

                        $parametrosMovimentoConta = $this->criarParametrosMovimentoConta($contaReceberORM->getUsuario()->getId(), $descontoAplicado, $contaReceberORM->getFranqueada()->getContaPadrao()->getValorSaldo(), $contaReceberORM->getFranqueada()->getContaPadrao()->getId(), $tituloReceberMetaData);
                        $tituloReceberMetaData[ConstanteParametros::CHAVE_MOVIMENTO_CONTA_PARAMETROS] = $parametrosMovimentoConta;
                        if ($resultado >= 0) {
                            break;
                        }
                    }//end foreach

                    // Se o valor do saldo do Super-Amigos for zero, está finalizado
                    if ($superAmigosIndicadoORM->getValorSaldo() === 0.0) {
                        $superAmigosIndicadoORM->setFinalizado(true);
                    }

                    $descontoParametrizado     = $this->calcularDescontoSuperAmigos(6);
                    $filtroPorCampo            = \Doctrine\Common\Collections\Criteria::create();
                    $filtroPorCampo            = $filtroPorCampo->orderBy(["id" => \Doctrine\Common\Collections\Criteria::DESC]);
                    $contasReceberIndicadorORM = $alunoORM->getAlunoContaReceber()->matching($filtroPorCampo);

                    // Registro do histórico de super-amigos - indicador
                    $parametrosSuperAmigosIndicador = [
                        ConstanteParametros::CHAVE_CONTRATO        => $contaReceberORM->getContrato(),
                        ConstanteParametros::CHAVE_ALUNO           => $alunoORM,
                        ConstanteParametros::CHAVE_NOME_DESCONTO   => 'Super-Amigos Turbinado',
                        ConstanteParametros::CHAVE_VALOR_ORIGINAL  => $descontoParametrizado,
                        ConstanteParametros::CHAVE_TIT_VALOR_SALDO => $descontoParametrizado,
                        ConstanteParametros::CHAVE_INDICADOR       => 1,
                        ConstanteParametros::CHAVE_FINALIZADO      => 0,
                    ];
                    $superAmigosIndicadorORM        = \App\Factory\GeneralORMFactory::criar(\App\Entity\Principal\DescontoSuperAmigo::class, true, $parametrosSuperAmigosIndicador);
                    self::persistSeguro($superAmigosIndicadorORM, $mensagemErro);

                    foreach ($contasReceberIndicadorORM as &$contaReceberIndicadorORM) {
                        $filtroPorCampo    = \Doctrine\Common\Collections\Criteria::create();
                        $filtroPorCampo    = $filtroPorCampo->orderBy(["numero_parcela_documento" => \Doctrine\Common\Collections\Criteria::DESC]);
                        $titulosReceberORM = $contaReceberIndicadorORM->getTituloRecebers()->matching($filtroPorCampo);
                        foreach ($titulosReceberORM as &$tituloReceberORM) {
                            $descontoAplicado = $descontoParametrizado;
                            $resultado        = $tituloReceberORM->getValorSaldoDevedor() - $descontoParametrizado;

                            $saldoSuperAmigos = $superAmigosIndicadorORM->getValorSaldo() - $tituloReceberORM->getValorSaldoDevedor();
                            if ($saldoSuperAmigos < 0) {
                                $saldoSuperAmigos = 0.0;
                            }

                            $superAmigosIndicadorORM->setValorSaldo($saldoSuperAmigos);

                            $tituloReceberORM->setValorSaldoDevedor($resultado);
                            if ($resultado < 0) {
                                $descontoParametrizado = $resultado * (-1);
                                $tituloReceberORM->setValorSaldoDevedor(0.0);
                            }

                            $parametrosTituloReceberIndicador  = [
                                ConstanteParametros::CHAVE_CONTA                 => $tituloReceberORM->getConta()->getId(),
                                ConstanteParametros::CHAVE_FORMA_COBRANCA        => $tituloReceberORM->getFormaCobranca()->getId(),
                                ConstanteParametros::CHAVE_VALOR_SALDO_DEVEDOR   => $resultado,
                                ConstanteParametros::CHAVE_VALOR_ORIGINAL        => $tituloReceberORM->getValorOriginal(),
                                ConstanteParametros::CHAVE_DESCONTO_SUPER_AMIGOS => $superAmigosIndicadorORM,
                            ];
                            $parametrosMovimentoContaIndicador = $this->criarParametrosMovimentoConta($contaReceberIndicadorORM->getUsuario()->getId(), $descontoAplicado, $contaReceberIndicadorORM->getFranqueada()->getContaPadrao()->getValorSaldo(), $contaReceberIndicadorORM->getFranqueada()->getContaPadrao()->getId(), $parametrosTituloReceberIndicador);
                            $movimentoContaIndicador           = $this->movimentoContaFacade->criar($mensagemErro, $parametrosMovimentoContaIndicador, false);
                            if ((is_null($movimentoContaIndicador) === true) || (empty($mensagemErro) === false)) {
                                $bRetorno = false;
                                break;
                            }

                            $tituloReceberORM->addMovimentoConta($movimentoContaIndicador);
                            if ($resultado >= 0) {
                                break;
                            }
                        }//end foreach
                    }//end foreach

                    // Se o valor do saldo do Super-Amigos for zero, está finalizado
                    if ($superAmigosIndicadorORM->getValorSaldo() === 0.0) {
                        $superAmigosIndicadorORM->setFinalizado(true);
                    }
                } else {
                    $bRetorno      = false;
                    $mensagemErro .= "O desconto de super_amigos_turbinado foi desabilitado pela franqueada.\nFavor entrar em contato com o suporte.";
                }//end if
            }//end if
        }//end if

        return $bRetorno;
    }

    /**
     * {@inheritDoc}
     *
     * @see \App\Facade\Principal\GenericFacade::__construct()
     */
    function __construct (ManagerRegistry $managerRegistry, $connection="base_principal")
    {
        parent::__construct($managerRegistry);
        $this->itemContaReceberFacade = new ItemContaReceberFacade($managerRegistry);
        $this->movimentoContaFacade   = new MovimentoContaFacade($managerRegistry);
        $this->movimentoEstoqueFacade = new MovimentoEstoqueFacade($managerRegistry);
        $this->tituloReceberFacade    = new TituloReceberFacade($managerRegistry);
        $this->chequeFacade           = new ChequeFacade($managerRegistry);
        $this->boletoFacade           = new BoletoFacade($managerRegistry);
        $this->transferenciaBancariaFacade = new TransferenciaBancariaFacade($managerRegistry);
        $this->transacaoCartaoFacade       = new TransacaoCartaoFacade($managerRegistry);
        $this->contaReceberBO         = new ContaReceberBO(self::getEntityManager());
        $this->contaReceberRepository = self::getEntityManager()->getRepository(\App\Entity\Principal\ContaReceber::class);
        $this->funcionarioRepository  = self::getEntityManager()->getRepository(\App\Entity\Principal\Funcionario::class);
        $this->alunoRepository        = self::getEntityManager()->getRepository(\App\Entity\Principal\Aluno::class);
        $this->itemRepository         = self::getEntityManager()->getRepository(\App\Entity\Principal\Item::class);
        $this->tipoMovimentoContaRepository = self::getEntityManager()->getRepository(\App\Entity\Principal\TipoMovimentoConta::class);
    }

/**
     * Lista todos os registros do banco de dados
     *
     * @param array $parametros Parametros da requisicao
     *
     * @return array
     */
    public function consulta($parametros)
    {

        $titulos = [];
        if (isset($parametros[ConstanteParametros::CHAVE_SITUACAO]) && (empty($parametros[ConstanteParametros::CHAVE_SITUACAO]) === false)) {
            $titulos = $this->contaReceberRepository->buscarTitulos($parametros);
        }
   
        $totalRecebido = 0;
        $totalRecebidoNaoConciliado = 0;
        $totalPendente = 0;
        $totalVencido = 0;
        $totalFaturado= 0; 
        $totalCancelado = 0;
      

        
        $titulosRetorno = [];
        
        foreach ($titulos as $titulo) {
            
            $totalPendente += $titulo['valor_receber'];

            $totalRecebido += $titulo['valor_recebido'];


            $totalCancelado += $titulo['valor_cancelado'];
            $totalVencido += $titulo['valor_vencido'];
 

            $titulosRetorno[] =$titulo;
           
        }

        $totalFaturado += $totalRecebido;
        $totalFaturado += $totalPendente; 
        // $totalFaturado += $totalVencido;
        
     
        $retorno = [
            ConstanteParametros::CHAVE_TOTAL          => count($titulosRetorno),
            ConstanteParametros::CHAVE_ITENS          => $titulosRetorno,
            ConstanteParametros::CHAVE_TOTAL_RECEBIDO =>$totalRecebido,
            ConstanteParametros::CHAVE_TOTAL_RECEBIDO_NAO_CONCILIADO =>$totalRecebidoNaoConciliado,
            ConstanteParametros::CHAVE_TOTAL_PENDENTE =>$totalPendente,
            ConstanteParametros::CHAVE_TOTAL_VENCIDO =>$totalVencido,
            ConstanteParametros::CHAVE_TOTAL_FATURADO =>$totalFaturado,
            ConstanteParametros::CHAVE_TOTAL_CANCELADO =>$totalCancelado,
        ];

            return $retorno;
    }

    /**
     * Lista todos os registros do banco de dados
     *
     * @param array $parametros Parametros da requisicao
     *
     * @return array
     */
    public function detalhes($titulo_id)
    {
        
        $titulo = $this->contaReceberRepository->buscarTitulo($titulo_id);
        
        
        return $titulo;
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
        $buscaContasReceber = $this->contaReceberRepository->filtrarContaReceberPorPagina($parametros);
        $retornoRepositorio = $buscaContasReceber[ConstanteParametros::CHAVE_ITENS];
        $totalRecebido      = $buscaContasReceber[ConstanteParametros::CHAVE_TOTAL_RECEBIDO];
       // $totalCancelados    = $buscaContasReceber['Total_Cancelados'];
      //  $totalVencidos      = $buscaContasReceber['Total_Vencidos'];
        $totalPendentes     = $buscaContasReceber['Total_Pendentes'];
        $retorno = [
            ConstanteParametros::CHAVE_TOTAL          => $retornoRepositorio->getTotalItemCount(),
            ConstanteParametros::CHAVE_ITENS          => $retornoRepositorio->getItems(),
            ConstanteParametros::CHAVE_TOTAL_RECEBIDO => $totalRecebido,
 //           'Total_Cancelados' => $totalCancelados,
 //           'Total_Vencidos' => $totalVencidos,
            'Total_Pendentes' => $totalPendentes
        ];

        return $retorno;
    }

    /**
     * Busca o registro pela chave primaria
     *
     * @param string $mensagemErro Mensagem que ira retornar para front-end
     * @param int $id Chave primaria do registro
     *
     * @return array
     */
    public function buscarPorId(&$mensagemErro, $id)
    {
        $objetoORM = $this->contaReceberRepository->buscarPorId($id);
        if (empty($objetoORM) === true) {
            $mensagemErro = "ContaReceber não encontrado na base de dados.";
        }

        return $objetoORM;
    }

    /**
     * Cria um objeto Conta Receber no banco de dados e ItemContaReceber atrelados a ele.<br>Ao mesmo tempo realiza a geracao de TituloReceber
     *
     * @param string $mensagemErro Mensagem que ira retornar para front-end
     * @param array $parametros Parametros a serem inclusos no objeto
     * @param array $boletos A lista de boletos gerados nos títulos, para referência e impressão deles no front-end
     *
     * @return mixed|null|\App\Entity\Principal\ContaReceber
     */
    public function criar(&$mensagemErro, $parametros=[], &$boletos=[])
    {
        $contaReceberORM = null;

        if ((isset($parametros[ConstanteParametros::CHAVE_TITULOS_RECEBER]) === false) || (empty($parametros[ConstanteParametros::CHAVE_TITULOS_RECEBER]) === true) || (count($parametros[ConstanteParametros::CHAVE_TITULOS_RECEBER]) < 1)) {
            $mensagemErro = "Para prosseguir com a criação da conta receber, será necessário ao menos 1 título informado.";
            return $contaReceberORM;
        }

        $itensContaReceberArray = [];
        if (isset($parametros[ConstanteParametros::CHAVE_ITENS]) === true) {
            $itensContaReceberArray = $parametros[ConstanteParametros::CHAVE_ITENS];
        }

        unset($parametros[ConstanteParametros::CHAVE_ITENS]);

        if ($this->contaReceberBO->podeSalvar($parametros, $mensagemErro) !== true) {
            return null;
        }

        $contaReceberORM = \App\Factory\GeneralORMFactory::criar(\App\Entity\Principal\ContaReceber::class, true, $parametros);
        if (isset($parametros[ConstanteParametros::CHAVE_CONTRATO]) === true) {
            $contaReceberORM->setBolsista($parametros[ConstanteParametros::CHAVE_CONTRATO]->getBolsista());
        }

        self::persistSeguro($contaReceberORM, $mensagemErro);

        if (empty($mensagemErro) === false) {
            return $contaReceberORM;
        }

        if ((isset($parametros[ConstanteParametros::CHAVE_APLICAR_DESCONTO_SUPER_AMIGOS]) === true) || (isset($parametros[ConstanteParametros::CHAVE_APLICAR_DESCONTO_SUPER_AMIGOS_TURBINADO]) === true)) {
            if (($parametros[ConstanteParametros::CHAVE_APLICAR_DESCONTO_SUPER_AMIGOS] === '1') || ($parametros[ConstanteParametros::CHAVE_APLICAR_DESCONTO_SUPER_AMIGOS_TURBINADO] === '1')) {
                $parcelasOrdenadasUltimaParaPrimeira = \App\Helper\FunctionHelper::ordenarArrayPorChave($parametros[ConstanteParametros::CHAVE_TITULOS_RECEBER], "numero_parcela_documento", SORT_DESC, true);
                if ($this->aplicaDescontoSuperAmigos((bool) $parametros[ConstanteParametros::CHAVE_APLICAR_DESCONTO_SUPER_AMIGOS], $mensagemErro, $contaReceberORM, $parametros[ConstanteParametros::CHAVE_ALUNO_INDICADOR], $parcelasOrdenadasUltimaParaPrimeira) === false) {
                    return null;
                }

                if ($this->aplicaDescontoSuperAmigosTurbinados((bool) $parametros[ConstanteParametros::CHAVE_APLICAR_DESCONTO_SUPER_AMIGOS_TURBINADO], $mensagemErro, $contaReceberORM, $parametros[ConstanteParametros::CHAVE_ALUNO_INDICADOR], $parcelasOrdenadasUltimaParaPrimeira) === false) {
                    return null;
                }

                $parametros[ConstanteParametros::CHAVE_TITULOS_RECEBER] = \App\Helper\FunctionHelper::ordenarArrayPorChave($parcelasOrdenadasUltimaParaPrimeira, "numero_parcela_documento", SORT_ASC, true);
            }
        }

        // Itens do contrato
        foreach ($itensContaReceberArray as $itemContaReceberData) {
            $itemContaReceberData[ConstanteParametros::CHAVE_CONTA_RECEBER] = $contaReceberORM;
            $erroItemContaReceberMsg = "";

            if (empty($itemContaReceberData[ConstanteParametros::CHAVE_ITEM_ENTREGUE]) === false) {
                $itemContaReceberData[ConstanteParametros::CHAVE_SITUACAO_ENTREGA] = SituacoesSistema::ITEM_ENTREGUE;
            } else {
                $itemContaReceberData[ConstanteParametros::CHAVE_SITUACAO_ENTREGA] = SituacoesSistema::ITEM_NAO_ENTREGUE;
            }

            if ((isset($itemContaReceberData[ConstanteParametros::CHAVE_DATA_VENCIMENTO]) === false || empty($itemContaReceberData[ConstanteParametros::CHAVE_DATA_VENCIMENTO]) === true) && isset($parametros[ConstanteParametros::CHAVE_TITULOS_RECEBER][0]) === true) {
                $itemContaReceberData[ConstanteParametros::CHAVE_DATA_VENCIMENTO] = $parametros[ConstanteParametros::CHAVE_TITULOS_RECEBER][0][ConstanteParametros::CHAVE_DATA_VENCIMENTO];
            }

            $itemContaReceberORM = $this->itemContaReceberFacade->criar($erroItemContaReceberMsg, $itemContaReceberData);
            if ((is_null($itemContaReceberORM) === true) || (empty($erroItemContaReceberMsg) === false)) {
                $mensagemErro = "Ocorreu o seguinte erro ao atribuir o item à Conta a Receber. Mensagem do Erro: " . $erroItemContaReceberMsg;
                return null;
            }

            if ($itemContaReceberORM->getSituacaoEntrega() === SituacoesSistema::ITEM_ENTREGUE) {
                $itemContaReceberORM->setUsuarioEntregue($contaReceberORM->getUsuario());
                $itemContaReceberORM->setDataEntrega(new \DateTime());

                $movimentoEstoqueORM = $this->movimentoEstoqueFacade->criar($mensagemErro, $itemContaReceberORM);
                if ((is_null($movimentoEstoqueORM) === true) || (empty($mensagemErro) === false)) {
                    return null;
                }
            }
        }//end foreach

        $contas = $contaReceberORM->getFranqueada()->getContas();
        $contaSelecionadaORM = $contaReceberORM->getFranqueada()->getContaPadrao();
        foreach ($contas as $contaORM) {
            if ($contaORM->getBancoEmiteBoleto() === true) {
                $contaSelecionadaORM = $contaORM;
                break;
            }
        }

        // Títulos a Receber
        foreach ($parametros[ConstanteParametros::CHAVE_TITULOS_RECEBER] as $tituloReceberMetaData) {
            $tituloReceberMetaData[ConstanteParametros::CHAVE_CONTA_RECEBER] = $contaReceberORM;
            $tituloReceberMetaData[ConstanteParametros::CHAVE_FRANQUEADA]    = $contaReceberORM->getFranqueada();
            $tituloReceberMetaData[ConstanteParametros::CHAVE_CONTA]         = $contaSelecionadaORM;
            $tituloReceberMetaData[ConstanteParametros::CHAVE_SACADO_PESSOA] = $contaReceberORM->getSacadoPessoa()->getId();
            $parametrosMovimentoContaIndicado = null;

            $chequeMetadata          = null;
            $boletoMetadata          = null;
            $transacaoCartaoMetadata = null;
            $transferenciaMetadata   = null;

            if (isset($tituloReceberMetaData[ConstanteParametros::CHAVE_CHEQUE]) === true) {
                $chequeMetadata = $tituloReceberMetaData[ConstanteParametros::CHAVE_CHEQUE];
                unset($tituloReceberMetaData[ConstanteParametros::CHAVE_CHEQUE]);

                if ($this->chequeFacade->possuiInformacoesCheque($chequeMetadata) === false) {
                    $chequeMetadata = null;
                }
            }

            if (isset($tituloReceberMetaData[ConstanteParametros::CHAVE_BOLETO]) === true) {
                $boletoMetadata = $tituloReceberMetaData[ConstanteParametros::CHAVE_BOLETO];
                unset($tituloReceberMetaData[ConstanteParametros::CHAVE_BOLETO]);
            }

            if (isset($tituloReceberMetaData[ConstanteParametros::CHAVE_TRANSFERENCIA_BANCARIA]) === true) {
                $transferenciaMetadata = $tituloReceberMetaData[ConstanteParametros::CHAVE_TRANSFERENCIA_BANCARIA];
                unset($tituloReceberMetaData[ConstanteParametros::CHAVE_TRANSFERENCIA_BANCARIA]);
            }

            if (isset($tituloReceberMetaData[ConstanteParametros::CHAVE_TRANSACAO_CARTAO]) === true) {
                $transacaoCartaoMetadata = $tituloReceberMetaData[ConstanteParametros::CHAVE_TRANSACAO_CARTAO];
                unset($tituloReceberMetaData[ConstanteParametros::CHAVE_TRANSACAO_CARTAO]);

                if ($this->transacaoCartaoFacade->possuiInformacoesCartao($transacaoCartaoMetadata) === false) {
                    $transacaoCartaoMetadata = null;
                }
            }

            if (isset($tituloReceberMetaData[ConstanteParametros::CHAVE_MOVIMENTO_CONTA_PARAMETROS]) === true) {
                $parametrosMovimentoContaIndicado = $tituloReceberMetaData[ConstanteParametros::CHAVE_MOVIMENTO_CONTA_PARAMETROS];
                unset($tituloReceberMetaData[ConstanteParametros::CHAVE_MOVIMENTO_CONTA_PARAMETROS]);
            }

            $tituloReceberORM = $this->tituloReceberFacade->criar($mensagemErro, $tituloReceberMetaData);
            if ((is_null($tituloReceberORM) === true) || (empty($mensagemErro) === false)) {
                return null;
            }

            if (is_null($parametrosMovimentoContaIndicado) === false) {
                $movimentoContaIndicador = $this->movimentoContaFacade->criar($mensagemErro, $parametrosMovimentoContaIndicado, false);
                if ((is_null($movimentoContaIndicador) === true) || (empty($mensagemErro) === false)) {
                    return null;
                }

                $tituloReceberORM->addMovimentoConta($movimentoContaIndicador);
            }

            if ((isset($chequeMetadata) === true) && (empty($chequeMetadata) === false) && (is_array($chequeMetadata) === true)) {
                $chequeMetadata[ConstanteParametros::CHAVE_ATENDENTE_USUARIO] = $parametros[ConstanteParametros::CHAVE_USUARIO];
                $chequeMetadata[ConstanteParametros::CHAVE_TITULO_PAGAR]      = null;
                $chequeMetadata[ConstanteParametros::CHAVE_TITULO_RECEBER]    = $tituloReceberORM;
                $chequeMetadata[ConstanteParametros::CHAVE_FRANQUEADA]        = $tituloReceberORM->getFranqueada();
                $chequeMetadata[ConstanteParametros::CHAVE_DATA_BOM_PARA]     = $tituloReceberORM->getDataProrrogacao();
                $chequeMetadata[ConstanteParametros::CHAVE_PESSOA]            = $tituloReceberORM->getSacadoPessoa();
                $chequeORM = $this->chequeFacade->criar($mensagemErro, $chequeMetadata, false);
                if ((is_null($chequeORM) === true) || (empty($mensagemErro) === false)) {
                    return null;
                }

                $tituloReceberORM->addCheque($chequeORM);
            }

            if (isset($boletoMetadata) === true && empty($boletoMetadata) === false && is_array($boletoMetadata) === true) {
                $boletoMetadata[ConstanteParametros::CHAVE_TITULO_RECEBER]  = $tituloReceberORM;
                $boletoMetadata[ConstanteParametros::CHAVE_FRANQUEADA]      = $tituloReceberORM->getFranqueada();
                $boletoMetadata[ConstanteParametros::CHAVE_CONTA]           = $contaSelecionadaORM;
                $boletoMetadata[ConstanteParametros::CHAVE_VALOR]           = $tituloReceberORM->getValorOriginal();
                $boletoMetadata[ConstanteParametros::CHAVE_DATA_VENCIMENTO] = $tituloReceberORM->getDataVencimento();

                $boletoORM = $this->boletoFacade->criar($mensagemErro, $boletoMetadata, false);
                if ((is_null($boletoORM) === true) || (empty($mensagemErro) === false)) {
                    return null;
                }

                $tituloReceberORM->addBoleto($boletoORM);
                $boletos[] = $boletoORM;
            }

            if (isset($transferenciaMetadata) === true && empty($transferenciaMetadata) === false && is_array($transferenciaMetadata) === true) {
                $transferenciaMetadata[ConstanteParametros::CHAVE_TITULO_RECEBER] = $tituloReceberORM;
                $transferenciaMetadata[ConstanteParametros::CHAVE_FRANQUEADA]     = $tituloReceberORM->getFranqueada();
                $transferenciaMetadata[ConstanteParametros::CHAVE_VALOR]          = $tituloReceberORM->getValorOriginal();

                $transferenciaORM = $this->transferenciaBancariaFacade->criar($mensagemErro, $transferenciaMetadata, false);
                if ((is_null($transferenciaORM) === true) || (empty($mensagemErro) === false)) {
                    return null;
                }

                $tituloReceberORM->addTransferenciaBancaria($transferenciaORM);
            }

            if (isset($transacaoCartaoMetadata) === true && empty($transacaoCartaoMetadata) === false && is_array($transacaoCartaoMetadata) === true) {
                $transacaoCartaoMetadata[ConstanteParametros::CHAVE_TITULO_RECEBER] = $tituloReceberORM;
                $transacaoCartaoMetadata[ConstanteParametros::CHAVE_FRANQUEADA]     = $tituloReceberORM->getFranqueada();

                if ($tituloReceberORM->getFormaCobranca()->getFormaCartaoDebito() === true) {
                    $transacaoCartaoMetadata[ConstanteParametros::CHAVE_TIPO_TRANSACAO] = 'D';
                }
                $dataPagamento = $transacaoCartaoMetadata[ConstanteParametros::CHAVE_DATA_PAGAMENTO];
                \App\Helper\FunctionHelper::formataCampoDateTimeJS($dataPagamento, $dataPagamento);
                if($dataPagamento == false){
                    $dataPagamento = $transacaoCartaoMetadata[ConstanteParametros::CHAVE_DATA_PAGAMENTO];
                    \App\Helper\FunctionHelper::formataCampoDateTime($dataPagamento, $dataPagamento);
                }
                $transacaoCartaoMetadata[ConstanteParametros::CHAVE_PREVISAO_REPASSE] = $this->transacaoCartaoFacade->gerarDataPrevisaoRepasse( $dataPagamento, $transacaoCartaoMetadata[ConstanteParametros::CHAVE_PARCELAMENTO_OPERADORA_CARTAO], $mensagemErro);

                if (empty($mensagemErro) === false) {
                    return null;
                }

                $transacaoCartaoORM = $this->transacaoCartaoFacade->criar($mensagemErro, $transacaoCartaoMetadata, false);
                if ((is_null($transacaoCartaoORM) === true) || (empty($mensagemErro) === false)) {
                    return null;
                }

                $tituloReceberORM->addTransacaoCartao($transacaoCartaoORM);
            }//end if
        }//end foreach

        return $contaReceberORM;
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
    public function atualizarStatus(&$mensagemErro, $id, $parametros=[])
    {
        $objetoORM = $this->contaReceberRepository->find($id);
        if (is_null($objetoORM) === true) {
            $mensagemErro = "ContaReceber não encontrado na base de dados.";
        } else if ($objetoORM->getFranqueada()->getId() !== (int) $parametros[ConstanteParametros::CHAVE_FRANQUEADA]) {
            $mensagemErro = "Você não pode alterar o status desta conta, pois ela não pertence a Franqueada selecionada.";
        } else {
            $objetoORM->setSituacao($parametros[ConstanteParametros::CHAVE_SITUACAO]);
            self::flushSeguro($mensagemErro);
        }

        return empty($mensagemErro);
    }

    /**
     * Cria os parametros para geração de conta a receber e titulo a recber
     *
     * @param string $mensagemErro
     * @param int $franqueadaId
     * @param int $alunoId
     * @param int $usuarioId
     * @param double $valor
     * @param int $formaCobrancaId
     * @param int $itemId Item id
     *
     * @return array
     */
    public function gerarParametrosContaReceberTituloReceber(&$mensagemErro, $franqueadaId, $alunoId, $usuarioId, $valor, $formaCobrancaId, $itemId)
    {
        $dataConclusao          = new \DateTime();
        $dataConclusaoFormatada = $dataConclusao->format("Y-m-d\TH:i:s.uP");
        $funcionarioIdDoUsuario = $this->retornaFuncionarioIdDoUsuario($usuarioId);
        $responsavelFinanceiroPessoaId = $this->retornaPessoaIdDoResponsavelFinanceiroAluno($alunoId);
        $itemORM      = $this->itemRepository->find($itemId);
        $planoContaId = null;
        if (is_null($funcionarioIdDoUsuario) === true) {
            $mensagemErro .= "Não foi encontrado um funcionario para o usuario informado.\n";
        }

        if (is_null($responsavelFinanceiroPessoaId) === true) {
            $mensagemErro .= "Não foi encontrado um responsavel financeiro para o aluno informado.\n";
        }

        if (is_null($itemORM) === true) {
            $mensagemErro .= "Não foi encontrado um registro para o item informado.\n";
        } else {
            $planoContaId = $itemORM->getPlanoConta()->getId();
        }

        return [
            ConstanteParametros::CHAVE_FRANQUEADA           => $franqueadaId,
            ConstanteParametros::CHAVE_ALUNO                => $alunoId,
            ConstanteParametros::CHAVE_SACADO_PESSOA        => $responsavelFinanceiroPessoaId,
            ConstanteParametros::CHAVE_USUARIO              => $usuarioId,
            ConstanteParametros::CHAVE_VENDEDOR_FUNCIONARIO => $funcionarioIdDoUsuario,
            ConstanteParametros::CHAVE_VALOR_TOTAL          => $valor,
            // ConstanteParametros::CHAVE_OBSERVACAO, ???
            ConstanteParametros::CHAVE_SITUACAO             => SituacoesSistema::SITUACAO_PENDENTE,
            ConstanteParametros::CHAVE_BOLSISTA             => false,
            ConstanteParametros::CHAVE_ITENS                => [
                [
                    ConstanteParametros::CHAVE_NUMERO_SEQUENCIA               => 1,
                    ConstanteParametros::CHAVE_ITEM                           => $itemId,
                    ConstanteParametros::CHAVE_PLANO_CONTA                    => $planoContaId,
                    ConstanteParametros::CHAVE_TIT_VALOR_ITEM                 => $valor,
                    ConstanteParametros::CHAVE_TIT_VALOR_DESCONTO_SUPER_AMIGO => 0,
                    ConstanteParametros::CHAVE_DATA_VENCIMENTO                => $dataConclusaoFormatada,
                ],
            ],
            ConstanteParametros::CHAVE_TITULOS_RECEBER      => [
                [
                    ConstanteParametros::CHAVE_ALUNO                    => $alunoId,
                    ConstanteParametros::CHAVE_SACADO_PESSOA            => $responsavelFinanceiroPessoaId,
                    ConstanteParametros::CHAVE_FORMA_COBRANCA           => $formaCobrancaId,
                    ConstanteParametros::CHAVE_FORMA_RECEBIMENTO        => $formaCobrancaId,
                    ConstanteParametros::CHAVE_DATA_VENCIMENTO          => $dataConclusaoFormatada,
                    ConstanteParametros::CHAVE_DATA_PRORROGACAO         => $dataConclusaoFormatada,
                    ConstanteParametros::CHAVE_VALOR_ORIGINAL           => $valor,
                    ConstanteParametros::CHAVE_VALOR_SALDO_DEVEDOR      => $valor,
                    ConstanteParametros::CHAVE_SITUACAO                 => SituacoesSistema::SITUACAO_PENDENTE,
                    ConstanteParametros::CHAVE_NUMERO_PARCELA_DOCUMENTO => 1,
                    ConstanteParametros::CHAVE_OBSERVACAO               => $itemORM->getDescricao(),
                ],
            ],
        ];
    }

    public function buscarDadosRelatorioSaidaEstoque($parametros) {
        return $this->contaReceberRepository->gerarDadosRelatorioSaidaEstoque($parametros);
    }
}
