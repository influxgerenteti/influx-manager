<?php

namespace App\Repository\Principal;

use App\Entity\Principal\ContaReceber;
use App\Entity\Principal\TituloReceber;
use App\Facade\Principal\GenericFacade;
use DateTime;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Symfony\Bridge\Doctrine\RegistryInterface;
use App\Helper\ConstanteParametros;
use App\Helper\SituacoesSistema;
use App\Helper\VariaveisCompartilhadas;

/**
 * @method ContaReceber|null find($id, $lockMode = null, $lockVersion = null)
 * @method ContaReceber|null findOneBy(array $criteria, array $orderBy = null)
 * @method ContaReceber[]    findAll()
 * @method ContaReceber[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class ContaReceberRepository extends ServiceEntityRepository
{


    public function __construct(RegistryInterface $registry)
    {
        parent::__construct($registry, ContaReceber::class);
    }

    /**
     * Monta a query principal
     *
     * @return \Doctrine\ORM\QueryBuilder
     */
    protected function montaQueryBase()
    {
        $queryBuilder = $this->createQueryBuilder("cr");
        $queryBuilder->addSelect("fran");
        $queryBuilder->addSelect("al");
        $queryBuilder->addSelect("alunoPessoa");
        $queryBuilder->addSelect("sp");
        $queryBuilder->addSelect("ct");
        $queryBuilder->addSelect("usu");
        $queryBuilder->addSelect("vf");
        $queryBuilder->addSelect("icr");
        $queryBuilder->addSelect("titulo");
        $queryBuilder->addSelect("titFormaCobranca");
        $queryBuilder->addSelect("titFormaRecebimento");
        $queryBuilder->addSelect("titConta");
        $queryBuilder->addSelect("titMovimentos");
        $queryBuilder->addSelect("descontoSuperAmigos");
        $queryBuilder->addSelect("contaMovimentoConta");
        $queryBuilder->addSelect("transacaoCartaoMovimentoConta");
        $queryBuilder->addSelect("operadoraTransacaoCartaoMovimentoConta");
        $queryBuilder->addSelect("parcelamentoOperadoraTransacaoCartaoMovimentoConta");
        $queryBuilder->addSelect("boletoMovimentoConta");
        $queryBuilder->addSelect("transferenciaBancariaMovimentoConta");
        $queryBuilder->addSelect("chequeMovimentoConta");
        $queryBuilder->addSelect("movtoFormaPagamento");
        $queryBuilder->addSelect("boletos");
        $queryBuilder->addSelect("cheques");
        $queryBuilder->addSelect("transacoesCartao");
        $queryBuilder->addSelect("transferenciasBancarias");
        $queryBuilder->addSelect("operadoraCartao");
        $queryBuilder->addSelect("parcelamentoOperadoraCartao");

        $queryBuilder->leftJoin("cr.franqueada", "fran");
        $queryBuilder->leftJoin("cr.aluno", "al");
        $queryBuilder->leftJoin("al.pessoa", "alunoPessoa");
        $queryBuilder->leftJoin("cr.sacado_pessoa", "sp");
        $queryBuilder->leftJoin("cr.contrato", "ct");
        $queryBuilder->leftJoin("cr.usuario", "usu");
        $queryBuilder->leftJoin("cr.vendedor_funcionario", "vf");
        $queryBuilder->leftJoin("cr.itemsContaReceber", "icr");
        $queryBuilder->leftJoin("cr.tituloRecebers", "titulo");
        $queryBuilder->leftJoin("titulo.forma_cobranca", "titFormaCobranca");
        $queryBuilder->leftJoin("titulo.forma_recebimento", "titFormaRecebimento");
        $queryBuilder->leftJoin("titulo.conta", "titConta");
        $queryBuilder->leftJoin("titulo.movimento_conta", "titMovimentos");
        $queryBuilder->leftJoin("cr.renegociacoes", "renegociacao");
        $queryBuilder->leftJoin("renegociacao.titulos_receber", "tituloRenegociado");
        $queryBuilder->leftJoin("tituloRenegociado.aluno", "tituloRenegociadoAluno");
        $queryBuilder->leftJoin("tituloRenegociadoAluno.pessoa", "tituloRenegociadoAlunoPessoa");
        $queryBuilder->leftJoin("titMovimentos.forma_pagamento", "movtoFormaPagamento");
        $queryBuilder->leftJoin("titMovimentos.desconto_super_amigos", "descontoSuperAmigos");
        $queryBuilder->leftJoin("titMovimentos.conta", "contaMovimentoConta");
        $queryBuilder->leftJoin("titMovimentos.transacao_cartao", "transacaoCartaoMovimentoConta");
        $queryBuilder->leftJoin("transacaoCartaoMovimentoConta.operadora_cartao", "operadoraTransacaoCartaoMovimentoConta");
        $queryBuilder->leftJoin("transacaoCartaoMovimentoConta.parcelamento_operadora_cartao", "parcelamentoOperadoraTransacaoCartaoMovimentoConta");
        $queryBuilder->leftJoin("titMovimentos.boleto", "boletoMovimentoConta");
        $queryBuilder->leftJoin("titMovimentos.transferencia_bancaria", "transferenciaBancariaMovimentoConta");
        $queryBuilder->leftJoin("titMovimentos.cheque", "chequeMovimentoConta");
        $queryBuilder->leftJoin("titulo.boletos", "boletos");
        $queryBuilder->leftJoin("titulo.cheques", "cheques");
        $queryBuilder->leftJoin("titulo.transacoes_cartao", "transacoesCartao");
        $queryBuilder->leftJoin('titulo.transferencias_bancarias', 'transferenciasBancarias');
        $queryBuilder->leftJoin("transacoesCartao.operadora_cartao", "operadoraCartao");
        $queryBuilder->leftJoin("transacoesCartao.parcelamento_operadora_cartao", "parcelamentoOperadoraCartao");

        return $queryBuilder;
    }

    /**
     * Query para realizar filtro de franqueada
     *
     * @param \Doctrine\ORM\QueryBuilder $queryBuilder
     */
    private function filtrarFranqueada(&$queryBuilder)
    {
        $queryBuilder->where("fran = :franqueada");
        $queryBuilder->setParameter("franqueada", VariaveisCompartilhadas::$franqueadaID);
    }

    /**
     * Configura os parâmetros de Data Inicial e Data Final de vencimento (prorrogação)
     *
     * @param array $parametros Parametros Requisicao
     * @param \Doctrine\ORM\QueryBuilder $queryBuilder Query montada
     *
     * @return \Doctrine\ORM\QueryBuilder
     */
    protected function configuraParametrosDataVencimentoInicialFinal(array $parametros, \Doctrine\ORM\QueryBuilder $queryBuilder)
    {
        $dataInicial = null;
        $dataFinal   = null;
        if (is_null($parametros[ConstanteParametros::CHAVE_FILTRO_DATA_INICIAL_VENCIMENTO]) === false) {
            \App\Helper\FunctionHelper::formataCampoDateTimeJS($parametros[ConstanteParametros::CHAVE_FILTRO_DATA_INICIAL_VENCIMENTO], $dataInicial);
            if (false !== $dataInicial) {
                $dataInicial->setTime(0, 0, 0);
                $queryBuilder->andWhere("titulo.data_vencimento >= :data_inicial_vencimento");
                $queryBuilder->setParameter("data_inicial_vencimento", $dataInicial);
            }
        }

        if (is_null($parametros[ConstanteParametros::CHAVE_FILTRO_DATA_FINAL_VENCIMENTO]) === false) {
            \App\Helper\FunctionHelper::formataCampoDateTimeJS($parametros[ConstanteParametros::CHAVE_FILTRO_DATA_FINAL_VENCIMENTO], $dataFinal);
            if (false !== $dataFinal) {
                $dataFinal->setTime(23, 59, 59);
                $queryBuilder->andWhere("titulo.data_vencimento <= :data_final_vencimento");
                $queryBuilder->setParameter("data_final_vencimento", $dataFinal);
            }
        }

        return $queryBuilder;
    }

    /**
     * Configura o parametro de situação
     *
     * @param array $parametros
     * @param \Doctrine\ORM\QueryBuilder $queryBuilder
     *
     * @return \Doctrine\ORM\QueryBuilder
     */
    protected function configuraParametrosSituacao(array $parametros, \Doctrine\ORM\QueryBuilder $queryBuilder)
    {
        $situacoes = $parametros[ConstanteParametros::CHAVE_SITUACAO];

        if ((is_null($situacoes) === false) && (empty($situacoes) === false)) {
            $haSituacaoPendente = in_array(SituacoesSistema::SITUACAO_PENDENTE, $situacoes);
            $haSituacaoQuitado  = in_array(SituacoesSistema::SITUACAO_LIQUIDADO, $situacoes);
            $haSituacaoCancelados = in_array(SituacoesSistema::SITUACAO_CANCELADO, $situacoes);

            // Quando pelo menos uma dessas situações estiver no array, mas não ambas
            if (($haSituacaoPendente === true && $haSituacaoQuitado === false) || ($haSituacaoPendente === false && $haSituacaoQuitado === true)) {
                $subQuery = $this->_em->createQueryBuilder();
                $subQuery->select('titrec');
                $subQuery->from(TituloReceber::class, 'titrec');
                $subQuery->leftJoin('titrec.cheques', 'cheq');
                $subQuery->leftJoin('titrec.transacoes_cartao', 'transcart');
                $expr = $subQuery->expr();
                $subQuery->where(
                    $expr->andX(
                        $expr->eq('titrec.id', 'titulo.id'),
                        $expr->orX(
                            $expr->eq('transcart.situacao', ':situacaoPendente'),
                            $expr->andX(
                                $expr->eq('cheq.situacao', ':situacaoChequePendente'),
                                $expr->eq('cheq.excluido', 0)
                            )
                        )
                    )
                );

                if ($haSituacaoCancelados === false) {
                    $subQuery->andWhere("titrec.situacao not in ('CAN')");
                }

                $queryBuilder->setParameter("situacaoPendente", SituacoesSistema::SITUACAO_PENDENTE);
                $queryBuilder->setParameter("situacaoChequePendente", SituacoesSistema::SITUACAO_CHEQUE_PENDENTE);
                $conditions = [];
                // Quando estiver checando por pendente não precisa checar por vencidos, visto que é um subgrupo de pendentes
                if (($key = array_search(SituacoesSistema::SITUACAO_PENDENTE, $situacoes)) !== false) {
                    unset($situacoes[$key]);
                    if (count($situacoes) > 0) {
                        $conditions[] = $queryBuilder->expr()->in("titulo.situacao", $situacoes);
                    }

                    $conditions[] = $queryBuilder->expr()->andX(
                        $queryBuilder->expr()->eq('titulo.situacao', ':situacaoPendente'),
                        $queryBuilder->expr()->not(
                            $queryBuilder->expr()->exists($subQuery->getDQL())
                            )
                        );
                    }
                    
                    if (in_array(SituacoesSistema::SITUACAO_LIQUIDADO, $situacoes) === true) {
                        $conditions[] = $queryBuilder->expr()->in("titulo.situacao", $situacoes);
                        $conditions[] = $queryBuilder->expr()->exists($subQuery->getDQL());
                        
                        if (in_array(SituacoesSistema::SITUACAO_VENCIDAS, $situacoes) === true) {
                            $conditions[] = $queryBuilder->expr()->andX(
                                $queryBuilder->expr()->eq('titulo.situacao', ':situacaoPendente'),
                                $queryBuilder->expr()->lt('titulo.data_vencimento', ':hoje')
                            );
                            $queryBuilder->setParameter("hoje", new \DateTime());
                        }
                    }
                    
                    if ($haSituacaoCancelados === false) {
                        $queryBuilder->andWhere("cr.situacao not in ('CAN')");
                    }
                    // A funcionalidade abaixo é o equivalente a chamar andWhere orX em todas as condições dentro do array $conditions
                    // Ou seja, se qualquer uma das condições do array estiver ok, vai atender ao andWhere
                    $conditions = call_user_func_array([$queryBuilder->expr(), 'orX'], $conditions);
                    $queryBuilder->andWhere($conditions);
            } else {
                $orQuery = '';
                if (in_array(SituacoesSistema::SITUACAO_VENCIDAS, $situacoes) === true) {
                    $orQuery .= " OR (titulo.situacao = 'PEN' AND titulo.data_vencimento < :hoje)";
                    $queryBuilder->setParameter("hoje", new \DateTime());
                }

                $queryBuilder->andWhere("titulo.situacao IN (:situacao) $orQuery");
                $queryBuilder->setParameter("situacao", $situacoes);
            }//end if
        }//end if

        return $queryBuilder;
    }

    /**
     * Configura o filtro de item
     *
     * @param array $parametros Parametros Requisicao
     * @param \Doctrine\ORM\QueryBuilder $queryBuilder Query montada
     *
     * @return \Doctrine\ORM\QueryBuilder
     */
    protected function configuraParametrosItem(array $parametros, \Doctrine\ORM\QueryBuilder $queryBuilder)
    {
        if ((isset($parametros[ConstanteParametros::CHAVE_FILTRO_ITEM]) === true) && (is_null($parametros[ConstanteParametros::CHAVE_FILTRO_ITEM]) === false)) {
            $queryBuilder->andWhere("icr.item = :itemId");
            $queryBuilder->setParameter("itemId", $parametros[ConstanteParametros::CHAVE_FILTRO_ITEM]);
        }

        return $queryBuilder;
    }

    /**
     * Configura o parametro de mês
     *
     * @param array $parametros
     * @param \Doctrine\ORM\QueryBuilder $queryBuilder
     *
     * @return \Doctrine\ORM\QueryBuilder
     */
    protected function configuraParametrosMesAno(array $parametros, \Doctrine\ORM\QueryBuilder $queryBuilder)
    {
        $mes = $parametros[ConstanteParametros::CHAVE_FILTRO_MES];

        if ((is_null($mes) === false) && ((empty($mes) === false) || ($mes === '0'))) {
            if (isset($parametros[ConstanteParametros::CHAVE_FILTRO_ANO]) === true) {
                $ano = $parametros[ConstanteParametros::CHAVE_FILTRO_ANO];
            } else {
                $ano = (new \DateTime())->format('Y');
            }

            $mes = intval($mes) + 1;
            $firstDayOfMonth = (new \DateTime(date("$ano-$mes-01 00:00:01")))->format('c');
            $lastDayOfMonth  = (new \DateTime(date("$ano-$mes-01 23:59:59")))->format('Y-m-t\TH:i:sP');

            $queryBuilder->andWhere("titulo.data_vencimento >= :mesInicial");
            $queryBuilder->setParameter("mesInicial", $firstDayOfMonth);

            $queryBuilder->andWhere("titulo.data_vencimento <= :mesFinal");
            $queryBuilder->setParameter("mesFinal", $lastDayOfMonth);
        } else {
            if ((isset($parametros[ConstanteParametros::CHAVE_FILTRO_ANO]) === true) && (empty($parametros[ConstanteParametros::CHAVE_FILTRO_ANO]) === false)) {
                $queryBuilder->andWhere("YEAR(titulo.data_vencimento) = :anoParaFiltro");
                $queryBuilder->setParameter("anoParaFiltro", $parametros[ConstanteParametros::CHAVE_FILTRO_ANO]);
            }
        }//end if

        return $queryBuilder;
    }

    /**
     * Configura os filtros de valor inicial e valor final para realizar a busca na base
     *
     * @param array $parametros Parametros Requisicao
     * @param \Doctrine\ORM\QueryBuilder $queryBuilder Query montada
     *
     * @return \Doctrine\ORM\QueryBuilder
     */
    protected function configuraParametrosValorInicialFinal(array $parametros, \Doctrine\ORM\QueryBuilder $queryBuilder)
    {
        if ((isset($parametros[ConstanteParametros::CHAVE_FILTRO_VALOR_INICIAL]) === true) && (is_null($parametros[ConstanteParametros::CHAVE_FILTRO_VALOR_INICIAL]) === false)) {
            $queryBuilder->andWhere("titulo.valor_saldo_devedor >= :valor_inicial");
            $queryBuilder->setParameter("valor_inicial", $parametros[ConstanteParametros::CHAVE_FILTRO_VALOR_INICIAL]);
        }

        if ((isset($parametros[ConstanteParametros::CHAVE_FILTRO_VALOR_FINAL]) === true) && (is_null($parametros[ConstanteParametros::CHAVE_FILTRO_VALOR_FINAL]) === false)) {
            $queryBuilder->andWhere("titulo.valor_saldo_devedor <= :valor_final");
            $queryBuilder->setParameter("valor_final", $parametros[ConstanteParametros::CHAVE_FILTRO_VALOR_FINAL]);
        }

        return $queryBuilder;
    }

    /**
     * Configura os parametros de destino para realizar os filtros
     *
     * @param array $parametros
     * @param \Doctrine\ORM\QueryBuilder $queryBuilder
     *
     * @return \Doctrine\ORM\QueryBuilder
     */
    protected function configuraParametrosDestino(array $parametros, \Doctrine\ORM\QueryBuilder $queryBuilder)
    {
        if ((isset($parametros[ConstanteParametros::CHAVE_SACADO_PESSOA]) === true) && (is_null($parametros[ConstanteParametros::CHAVE_SACADO_PESSOA]) === false)) {
            $queryBuilder->andWhere("titulo.sacado_pessoa = :destino OR alunoPessoa = :destino OR tituloRenegociadoAlunoPessoa = :destino");
            $queryBuilder->setParameter("destino", $parametros[ConstanteParametros::CHAVE_SACADO_PESSOA]);
        }

        return $queryBuilder;
    }

    /**
     * Configura os parametros de forma de cobrança para filtros
     *
     * @param array $parametros
     * @param \Doctrine\ORM\QueryBuilder $queryBuilder
     *
     * @return \Doctrine\ORM\QueryBuilder
     */
    protected function configuraParametrosFormaCobranca (array $parametros, \Doctrine\ORM\QueryBuilder $queryBuilder)
    {
        if ((isset($parametros[ConstanteParametros::CHAVE_FORMA_COBRANCA]) === true) && (is_null($parametros[ConstanteParametros::CHAVE_FORMA_COBRANCA]) === false)) {
            $queryBuilder->andWhere("titFormaCobranca = :formCobranca");
            $queryBuilder->setParameter("formCobranca", $parametros[ConstanteParametros::CHAVE_FORMA_COBRANCA]);
        }

        return $queryBuilder;
    }

    /**
     * Configura os parametros de boletos para filtros
     *
     * @param array $parametros
     * @param \Doctrine\ORM\QueryBuilder $queryBuilder
     *
     * @return \Doctrine\ORM\QueryBuilder
     */
    protected function configuraParametrosBoleto (array $parametros, \Doctrine\ORM\QueryBuilder $queryBuilder)
    {
        if ((isset($parametros[ConstanteParametros::CHAVE_NOSSO_NUMERO]) === true) && (is_null($parametros[ConstanteParametros::CHAVE_NOSSO_NUMERO]) === false)) {
            $queryBuilder->andWhere("boleto.nosso_numero = :nossoNumero");
            $queryBuilder->setParameter("nossoNumero", $parametros[ConstanteParametros::CHAVE_NOSSO_NUMERO]);
        }

        return $queryBuilder;
    }

    /**
     * Configura os parametros de contratos para filtros
     *
     * @param array $parametros
     * @param \Doctrine\ORM\QueryBuilder $queryBuilder
     *
     * @return \Doctrine\ORM\QueryBuilder
     */
    protected function configuraParametrosContrato (array $parametros, \Doctrine\ORM\QueryBuilder $queryBuilder)
    {
        if ((isset($parametros[ConstanteParametros::CHAVE_CONTRATO]) === true)&&(empty($parametros[ConstanteParametros::CHAVE_CONTRATO]) === false)) {
            $contrato = explode("/", $parametros[ConstanteParametros::CHAVE_CONTRATO]);
            if (count($contrato) > 1) {
                $aluno     = $contrato[0];
                $sequencia = $contrato[1];
            } else {
                $aluno     = '';
                $sequencia = '';
            }

            $queryBuilder->andWhere("al = :aluno");
            $queryBuilder->andWhere("ct.sequencia_contrato = :sequencia");

            $queryBuilder->setParameter("aluno", $aluno);
            $queryBuilder->setParameter("sequencia", $sequencia);
        }

        return $queryBuilder;
    }

    /**
     * Configura os parametros de transferência bancária para filtros
     *
     * @param array $parametros
     * @param \Doctrine\ORM\QueryBuilder $queryBuilder
     *
     * @return \Doctrine\ORM\QueryBuilder
     */
    protected function configuraParametrosTransferenciaBancaria (array $parametros, \Doctrine\ORM\QueryBuilder $queryBuilder)
    {
        if ((isset($parametros[ConstanteParametros::CHAVE_CONTA]) === true) && (empty($parametros[ConstanteParametros::CHAVE_CONTA]) === false)) {
            $conta = $parametros[ConstanteParametros::CHAVE_CONTA];
            $queryBuilder->andWhere("transferenciasBancarias.conta = :conta");
            $queryBuilder->setParameter("conta", $conta);
        }

        if ((isset($parametros[ConstanteParametros::CHAVE_AGENCIA]) === true) && (empty($parametros[ConstanteParametros::CHAVE_AGENCIA]) === false)) {
            $agencia = $parametros[ConstanteParametros::CHAVE_AGENCIA];
            $queryBuilder->andWhere("transferenciasBancarias.agencia = :agencia");
            $queryBuilder->setParameter("agencia", $agencia);
        }

        return $queryBuilder;
    }

    /**
     * Configura os parametros para realizar a busca
     *
     * @param array $parametros Parametros Requisicao
     * @param \Doctrine\ORM\QueryBuilder $queryBuilder Query montada
     *
     * @return \Doctrine\ORM\QueryBuilder
     */
    protected function configuraParametrosBusca(array $parametros, \Doctrine\ORM\QueryBuilder $queryBuilder)
    {
        if ( isset($parametros[ConstanteParametros::CHAVE_FILTRO_DATA_INICIAL_VENCIMENTO])) {
            if ( isset($parametros[ConstanteParametros::CHAVE_FILTRO_DATA_FINAL_VENCIMENTO])) {
                $queryBuilder = $this->configuraParametrosDataVencimentoInicialFinal($parametros, $queryBuilder);        
            }
        }
        if ( isset($parametros[ConstanteParametros::CHAVE_FILTRO_VALOR_INICIAL])) {
            if ( isset($parametros[ConstanteParametros::CHAVE_FILTRO_VALOR_FINAL])) {
                $queryBuilder = $this->configuraParametrosValorInicialFinal($parametros, $queryBuilder);        
            }        
        }        
        // $queryBuilder = $this->configuraParametrosItem($parametros, $queryBuilder);
        // $queryBuilder = $this->configuraParametrosDestino($parametros, $queryBuilder);
        $queryBuilder = $this->configuraParametrosSituacao($parametros, $queryBuilder);
        $queryBuilder = $this->configuraParametrosMesAno($parametros, $queryBuilder);
        // $queryBuilder = $this->configuraParametrosFormaCobranca($parametros, $queryBuilder);
        // $queryBuilder = $this->configuraParametrosBoleto($parametros, $queryBuilder);
        // $queryBuilder = $this->configuraParametrosContrato($parametros, $queryBuilder);
        // $queryBuilder = $this->configuraParametrosTransferenciaBancaria($parametros, $queryBuilder);

        return $queryBuilder;
    }

    /**
     * Filtra as contas a receber por pagina
     *
     * @param array $parametros
     * @param number $numeroItensPorPagina
     *
     * @return \Knp\Component\Pager\Pagination\SlidingPagination
     */
    public function filtrarContaReceberPorPagina($parametros, $numeroItensPorPagina=99999)
    {
        $opcoes       = [];
        $queryBuilder = $this->montaQueryBase();
        $this->filtrarFranqueada($queryBuilder);
        $queryBuilder = $this->configuraParametrosBusca($parametros, $queryBuilder);

   //     $somaTotalRecebidos = $this->calculaTotalRecebido($queryBuilder);
       
        $totalCancelados = null;
        $totalRecebidos = null;
        $totalVencidos = null;
        $totalPendentes = null;
                                                               
        if ($this->calculaTotais($queryBuilder, $totalRecebidos, $totalCancelados, $totalVencidos, $totalPendentes) === false) {
            $totalRecebidos = 0;
    //        $totalCancelados = 0;
        //    $totalVencidos = 0;
            $totalPendentes = 0;
        }

        if ((isset($parametros[ConstanteParametros::CHAVE_ORDENACAO_COLUNA]) === true)&&(is_null($parametros[ConstanteParametros::CHAVE_ORDENACAO_COLUNA]) === false)) {
            $queryBuilder->orderBy($parametros[ConstanteParametros::CHAVE_ORDENACAO_COLUNA], $parametros[ConstanteParametros::CHAVE_ORDENACAO_SORT]);
            $opcoes[\Knp\Component\Pager\Paginator::SORT_FIELD_PARAMETER_NAME]     = "~";
            $opcoes[\Knp\Component\Pager\Paginator::SORT_DIRECTION_PARAMETER_NAME] = "~";
        }

        $resultatosPaginados = \App\Helper\FunctionHelper::montaPaginatorPaginacao($queryBuilder, $parametros[ConstanteParametros::CHAVE_PAGINA], $numeroItensPorPagina, $opcoes);

        return [
            ConstanteParametros::CHAVE_ITENS          => $resultatosPaginados,
            ConstanteParametros::CHAVE_TOTAL_RECEBIDO => $totalRecebidos,
   //         'Total_Cancelados' => $totalCancelados,
  //          'Total_Vencidos' => $totalVencidos,
            'Total_Pendentes' => $totalPendentes
        ];
    }

    /**
     * Cálcula o valor total recebido
     *
     * @param \Doctrine\ORM\QueryBuilder $queryBuilder
     *
     * @return float
     */
    private function calculaTotalRecebido($queryBuilder)
    {
        $soma = clone $queryBuilder;
        $soma->select('titulo.id as idTilulo');
        // $soma->select('titMovimentos.id as idMovimentos');
        $soma->addselect('titulo.situacao as situacao');
        $soma->addselect('(titMovimentos.valor_lancamento - titulo.valor_saldo_devedor) as soma');
        $soma->distinct();
        $resultado  = \App\Helper\FunctionHelper::retornaArrayNull($soma);
        $valorTotal = 0;
     
        if (is_null($resultado) === false ) {
            foreach ($resultado as $registro) {
                if ($registro["situacao"] === 'LIQ') {
                    $valorTotal += $registro["soma"];
                }
            }
        }

         return $valorTotal;
    }

      /**
     * Cálcula o valor total recebido
     *
     * @param \Doctrine\ORM\QueryBuilder $queryBuilder
     *
     * @return boolean
     */
    private function calculaTotais($queryBuilder, &$totalRecebidos, &$totalCancelados, &$totalVencidos, &$totalPendentes)
    {
        $soma = clone $queryBuilder;
        $soma->select('titulo.id as idTilulo');
        // $soma->select('titMovimentos.id as idMovimentos');
        $soma->addselect('titulo.situacao as situacao');
        $soma->addselect('(titMovimentos.valor_lancamento - titulo.valor_saldo_devedor) as soma');
        $soma->addselect('titulo.valor_saldo_devedor as saldo_Devedor');
        $soma->addselect('titulo.data_vencimento as data_venc');
        $soma->addselect('cheques.id as cheque');
        $soma->addselect('transacoesCartao.id as cartao');
        $soma->distinct();
        $resultado  = \App\Helper\FunctionHelper::retornaArrayNull($soma);
        $totalRecebidos = 0;
        $totalCancelados = 0;
        $totalVencidos = 0;
        $totalPendentes = 0;
     
        $dataHoje = new \DateTime();

        if (is_null($resultado) === false ) {
            foreach ($resultado as $registro) {

                $dataVenc = $registro["data_venc"];
                if ($registro["situacao"] === 'LIQ') {
                    if ($registro["saldo_Devedor"] > 0) {
                        if ($dataVenc  <  $dataHoje) {
                            $totalVencidos += $registro["saldo_Devedor"];
                        } else {
                            $totalPendentes += $registro["saldo_Devedor"];
                        }
                    } else {
                        $totalRecebidos += $registro["soma"];
                    }
                }
                if ($registro["situacao"] === 'CAN') {
                    $totalCancelados += $registro["saldo_Devedor"];
                }
                if (($registro["situacao"] === 'PEN') || $registro["situacao"] === 'SUB') {
                    if (is_null($registro["cheque"]) && is_null($registro["cartao"])) {
                        
                       // if ($dataVenc  <  $dataHoje) {
                      //      $totalVencidos += $registro["saldo_Devedor"];
                      //  } else {
                      $totalPendentes += $registro["saldo_Devedor"];
                      //  }   
                    } else {
                        $totalRecebidos += $registro["soma"];
                    }                 
                }
            }
        }


        return true;
    }

    /**
     * Busca a ContaReceber atraves da chave primaria
     *
     * @param int $id
     *
     * @return array|NULL
     */
    public function buscarPorId($id)
    {
        $queryBuilder = $this->montaQueryBase();
        $this->filtrarFranqueada($queryBuilder);
        $queryBuilder->andWhere("cr.id = :id");
        $queryBuilder->setParameter("id", $id);
        return \App\Helper\FunctionHelper::retornaArrayNull($queryBuilder);
    }

    public function gerarDadosRelatorioSaidaEstoque($parametros)
    {
        $queryBuilder = $this->createQueryBuilder('cr')
            ->select([
                'sp.nome_contato as sacado',
                'it.descricao as item',
                'icr.quantidade',
                'icr.valor',
                'ue.nome as usuario_entregue',
                "date_format(cr.data_emissao, '%Y-%m-%d') as data_emissao",
                "date_format(icr.data_entrega, '%Y-%m-%d') as data_entrega",
                "icr.situacao_entrega"
                ])
            ->join('cr.itemsContaReceber', 'icr')
            ->leftJoin('icr.item', 'it')
            ->leftJoin('icr.usuario_entregue', 'ue')
            ->leftJoin('cr.sacado_pessoa', 'sp')
            ->where('cr.franqueada = :franqueada')
            ->setParameter('franqueada', $parametros[ConstanteParametros::CHAVE_FRANQUEADA]);
            
        if(isset($parametros[ConstanteParametros::CHAVE_ALUNO])) {
            $queryBuilder->andWhere('cr.aluno = :aluno_id')
                ->setParameter('aluno_id', $parametros[ConstanteParametros::CHAVE_ALUNO]);
        }
        
        if(isset($parametros[ConstanteParametros::CHAVE_ITEM])) {
            $queryBuilder->andWhere('it = :item_id')
                ->setParameter('item_id', $parametros[ConstanteParametros::CHAVE_ITEM]);
        }
        
        if(isset($parametros[ConstanteParametros::CHAVE_SITUACAO_ENTREGA])) {
            $queryBuilder->andWhere("icr.situacao_entrega IN (:situacao_opcoes)")
                ->setParameter('situacao_opcoes', $parametros[ConstanteParametros::CHAVE_SITUACAO_ENTREGA]);
        }

        if(isset($parametros[ConstanteParametros::CHAVE_SITUACAO_ENTREGA])) {
            $queryBuilder->andWhere("icr.situacao_entrega IN (:situacao_opcoes)")
                ->setParameter('situacao_opcoes', $parametros[ConstanteParametros::CHAVE_SITUACAO_ENTREGA]);
        }

        if(isset($parametros[ConstanteParametros::CHAVE_USUARIO])) {
            $queryBuilder->andWhere('icr.usuario_entregue = :usuario_entregue')
                ->setParameter('usuario_entregue', $parametros[ConstanteParametros::CHAVE_USUARIO]);
        }
        
        if(isset($parametros[ConstanteParametros::CHAVE_APELIDO])) {
            $queryBuilder->andWhere('cr.vendedor_funcionario = :funcionario')
                ->setParameter('funcionario', $parametros[ConstanteParametros::CHAVE_APELIDO]);
        }

        if(isset($parametros[ConstanteParametros::CHAVE_FILTRO_EMPRESA])) {
            $queryBuilder->andWhere('cr.sacado_pessoa = :empresa')
                ->setParameter('empresa', $parametros[ConstanteParametros::CHAVE_FILTRO_EMPRESA]);
        }
        
        if(isset($parametros[ConstanteParametros::CHAVE_DATA_SAIDA_DE])) {
            $dataParametro = strtotime(str_replace("/", "-",$parametros[ConstanteParametros::CHAVE_DATA_SAIDA_DE] . " 00:00:00"));
            $dataParametro = date('Y-m-d H:i:s', $dataParametro);
            $queryBuilder->andWhere('cr.data_emissao >= :data_saida_de')
                ->setParameter('data_saida_de', $dataParametro);
        }
        
        if(isset($parametros[ConstanteParametros::CHAVE_DATA_SAIDA_ATE])) {
            $dataParametro = strtotime(str_replace("/", "-",$parametros[ConstanteParametros::CHAVE_DATA_SAIDA_ATE] . " 23:59:59"));
            $dataParametro = date('Y-m-d H:i:s', $dataParametro);
            $queryBuilder->andWhere('cr.data_emissao <= :data_saida_ate')
                ->setParameter('data_saida_ate', $dataParametro);
        }
        
        if(isset($parametros[ConstanteParametros::CHAVE_DATA_ENTREGA_DE])) {
            $dataParametro = strtotime(str_replace("/", "-",$parametros[ConstanteParametros::CHAVE_DATA_ENTREGA_DE] . " 00:00:00"));
            $dataParametro = date('Y-m-d H:i:s', $dataParametro);
            $queryBuilder->andWhere('icr.data_entrega >= :data_entrega_de')
                ->setParameter('data_entrega_de', $dataParametro);
        }
        
        if(isset($parametros[ConstanteParametros::CHAVE_DATA_ENTREGA_ATE])) {
            $dataParametro = strtotime(str_replace("/", "-",$parametros[ConstanteParametros::CHAVE_DATA_ENTREGA_ATE] . " 23:59:59"));
            $dataParametro = date('Y-m-d H:i:s', $dataParametro);
            $queryBuilder->andWhere('icr.data_entrega <= :data_entrega_ate')
                ->setParameter('data_entrega_ate', $dataParametro);
        }

        return $queryBuilder->getQuery()->getResult();
    }


    public function buscarTitulo($tituloId){

        $franqueadaId = VariaveisCompartilhadas::$franqueadaID;

        $sqlTitulo = " SELECT 
            tr.*

            FROM influx_crm_prod.titulo_receber as tr 
            
            where mc.franqueada_id = {$franqueadaId} and tr.id  = {$tituloId}   

        ";

        $sqlMovimentos = " SELECT 
            mc.numero_documento as numero,
            mc.estornado, 
            mc.conciliado, 
            mc.observacao, 
            mc.valor_desconto, 
            mc.valor_juros, 
            mc.valor_multa, 
            mc.valor_titulo,
            mc.valor_lancamento, 
            mc.operacao, 
            mc.data_contabil, 
            mc.data_movimento,
            c.descricao as conta,
            u.nome as nome_usuario,
            fp.descricao_abreviada as forma_pagamento

            FROM influx_crm_prod.movimento_conta as mc 
            LEFT JOIN influx_crm_prod.conta as c on mc.conta_id = c.id
            LEFT JOIN influx_crm_prod.usuario as u on mc.usuario_id = u.id
            LEFT JOIN influx_crm_prod.forma_pagamento as fp on mc.forma_pagamento_id = fp.id
            where mc.franqueada_id = {$franqueadaId} and mc.titulo_receber_id  = {$tituloId}   

        ";

        $connection = self::getEntityManager()->getConnection();

        $movimentos = $connection->fetchAllAssociative($sqlMovimentos);
        $titulo = $connection->fetchAllAssociative($sqlTitulo);
        if( count($titulo) > 0 ){
            $titulo =  $titulo[0];
        }

       

        $$titulo['movimentos'] = $movimentos;

        return $titulo;
        
        }


    public function buscarTitulos($params){
                $franqueadaId = VariaveisCompartilhadas::$franqueadaID;
                $useMovimento = $params['tipo_data'] == 'MOVIMENTO';

//TODO determinar se um titulo esta ou nao pago além da situacao LIQ
                $sqlMovimentos = "";
                $sqlMovimentosFields = "";

               if($useMovimento){
                $sqlMovimentosFields = " 
                mc.data_contabil,
                mc.data_deposito,
                mc.valor_lancamento,
               
                IFNULL((
                    SELECT SUM(IFNULL(mc.valor_lancamento, 0))
                    FROM movimento_conta mc 
                    WHERE mc.titulo_receber_id = tr.id 
                      AND mc.estornado = 0 
                      AND mc.conciliado = 'S'                      
                ), 0) AS mc_total_lancamentos,
                ";
                                

                $sqlMovimentos = " INNER JOIN movimento_conta mc on mc.titulo_receber_id = tr.id ";
            } 

            
                $sql = "SELECT  tr.id as id, 
                tr.conta_receber_id,
                tr.conta_id,
                tr.forma_cobranca_id,
                tr.forma_recebimento_id,
                tr.data_vencimento,
                tr.data_prorrogacao,
                tr.data_emissao,
                tr.valor_original,
                tr.valor_saldo_devedor,
                tr.valor_despesas,
                tr.taxa_multa,
                tr.taxa_juro_dia,
                tr.observacao,
                tr.situacao,
                tr.numero_parcela_documento,
                tr.negativado,
                tr.lembrete_envio,
                tr.renegociado,
                tr.valor_item,
                tr.valor_parcela_sem_desconto,
                tr.desconto_antecipacao,
                {$sqlMovimentosFields}
                al.situacao as situacao_aluno,
                cr.contrato_id,        
                cr.situacao as contrato_situacao,
                cr.valor_total as contrato_total,
                p2.cnpj_cpf as responsavel_financeiro_cpf,
	            p2.nome_contato as responsavel_financeiro_nome,
                p2.telefone_preferencial as responsavel_financeiro_telefone_preferencial,
                ra.descricao as parentesco_aluno,
                al.id as aluno_id,
                al.situacao as situacao_aluno,
                al_pessoa.nome_contato as aluno_nome,
                p.cnpj_cpf as cliente_cpf,
                p.numero_identidade as cliente_identidade,
                p.nome_contato as cliente_nome,
                c.nome as cliente_cidade,
                p.endereco as cliente_endereco,
                p.numero_endereco as cliente_numero,
                p.bairro_endereco as cliente_bairro,
                p.complemento_endereco as cliente_complemento,
                p.cep_endereco as cliente_cep,
                p.email_preferencial as cliente_email,
                p.telefone_preferencial as cliente_telefone,
                p.negativado as cliente_negativado,
                p.inadimplente as cliente_inadimplente,
                YEAR(FROM_DAYS(TO_DAYS(NOW())-TO_DAYS(p.data_nascimento))) as cliente_idade,
                fp.id as forma_pagamento_id,
                fp.descricao as forma_pagamento,
                fp.descricao_abreviada as fpgto,
                fp.forma_boleto as fpgtoIsBoleto,
                fp.forma_cartao as fpgtoIsCredito,
                fp.forma_cartao_debito as fpgtoIsDebito,
                fp.forma_cheque as fpgtoIsCheque,
                fp.forma_transferencia as fpgtoIsTr,
                fc.descricao_abreviada as fcob,
                fc.forma_boleto as fcobIsBoleto,
                fc.forma_cartao as fcobIsCredito,
                fc.forma_cartao_debito as fcobIsDebito,
                fc.forma_cheque as fcobIsCheque,
                cartao.situacao as cartao_situacao,
                cartao.identificador as cartao_identificador,
                cartao.previsao_repasse as cartao_previsao,
                cartao.data_pagamento as cartao_pagamento,
                cartao.taxa as cartao_taxa,
                boleto.situacao_cobranca as boleto_situacao,
                boleto.nosso_numero as boleto_identificador,
                boleto.data_vencimento as boleto_previsao,
                cheque.situacao as cheque_situacao,
                cheque.numero as cheque_identificador,
                cheque.data_entrada as cheque_pagamento,
                cheque.data_bom_para as cheque_previsao,
                cheque.data_baixa as cheque_baixa,
                
                (curdate() > tr.data_vencimento and fp.forma_boleto = 1 ) AS titulo_vencido,

                (SELECT IFNULL(sum(IFNULL(xtc.valor_liquido,0) ) ,0)  as t from transacao_cartao as xtc where  xtc.titulo_receber_id = tr.id and xtc.situacao = 'CRE') as cartao_total_conciliado,
                (SELECT IFNULL(sum(IFNULL(xtc.valor_liquido,0) + IFNULL(xtc.valor_desconto,0)) ,0)  as t from transacao_cartao as xtc where  xtc.titulo_receber_id = tr.id and (xtc.situacao = 'PEN' or xtc.situacao = 'EST')) as cartao_total_pendente,
                (SELECT IFNULL(sum(IFNULL(xtc.valor_liquido,0) + IFNULL(xtc.valor_desconto,0)) ,0)  as t from transacao_cartao as xtc where   xtc.titulo_receber_id = tr.id and (xtc.situacao = 'EXC' OR xtc.situacao = 'CAN')) as cartao_total_cancelado,
                
                (SELECT IFNULL(sum(IFNULL(xb.valor,0) + IFNULL(xb.valor_desconto,0)) ,0)  as t from boleto as xb where  xb.titulo_receber_id = tr.id and xb.situacao_cobranca = 'REC') as boleto_total_conciliado,
                (SELECT IFNULL(sum(IFNULL(xb.valor,0) + IFNULL(xb.valor_desconto,0)) ,0)  as t from boleto as xb where  xb.titulo_receber_id = tr.id and xb.situacao_cobranca = 'PEN') as boleto_total_pendente,
                (SELECT IFNULL(sum(IFNULL(xb.valor,0) + IFNULL(xb.valor_desconto,0)) ,0)  as t from boleto as xb where   xb.titulo_receber_id = tr.id and xb.situacao_cobranca = 'CAN') as boleto_total_cancelado,
                
                (SELECT IFNULL(sum(IFNULL(xc.valor,0) + IFNULL(xc.valor_desconto,0)) ,0)  as t from cheque as xc where   xc.titulo_receber_id = tr.id and xc.situacao = 'B') as cheque_total_conciliado,
                (SELECT IFNULL(sum(IFNULL(xc.valor,0) + IFNULL(xc.valor_desconto,0)) ,0)  as t from cheque as xc where   xc.titulo_receber_id = tr.id and xc.situacao = 'P') as cheque_total_pendente,
                (SELECT IFNULL(sum(IFNULL(xc.valor,0) + IFNULL(xc.valor_desconto,0)) ,0) as t from cheque as xc where   xc.titulo_receber_id = tr.id and (xc.situacao = 'D' OR xc.situacao = 'C')) as cheque_total_cancelado,
                
                (SELECT IFNULL(sum(IFNULL(xt.valor,0)) ,0)as t from transferencia_bancaria as xt where  xt.titulo_receber_id = tr.id and xt.situacao = 'CRE') as tr_total_conciliado,
                (SELECT IFNULL(sum(IFNULL(xt.valor,0)) ,0)as t from transferencia_bancaria as xt where  xt.titulo_receber_id = tr.id and xt.situacao = 'PEN') as tr_total_pendente,
                (SELECT IFNULL(sum(IFNULL(xt.valor,0)),0) as t from transferencia_bancaria as xt where  xt.titulo_receber_id = tr.id and (xt.situacao = 'EST' OR xt.situacao = 'CAN')) as tr_total_cancelado
                
                
                 
                FROM titulo_receber tr 
                {$sqlMovimentos}
                LEFT JOIN conta_receber cr on tr.conta_receber_id = cr.id
                LEFT JOIN pessoa p on tr.sacado_pessoa_id = p.id 
                LEFT JOIN aluno al on tr.aluno_id = al.id 
                LEFT JOIN pessoa al_pessoa on al.pessoa_id = al_pessoa.id 
                LEFT JOIN pessoa p2 on al.responsavel_financeiro_pessoa_id = p2.id 
                LEFT JOIN relacionamento_aluno ra  on al.responsavel_financeiro_relacionamento_aluno_id  = ra.id 
                INNER JOIN franqueada f on tr.franqueada_id = f.id 
                LEFT JOIN forma_pagamento fp on tr.forma_recebimento_id = fp.id 
                LEFT JOIN forma_pagamento fc on tr.forma_cobranca_id = fc.id 
                LEFT JOIN cidade c on f.cidade_id = c.id 
                LEFT JOIN estado e on f.estado_id = e.id
                LEFT JOIN boleto as boleto on tr.id = boleto.titulo_receber_id
                LEFT JOIN cheque as cheque on tr.id = cheque.titulo_receber_id
                LEFT JOIN transacao_cartao as cartao on tr.id = cartao.titulo_receber_id
                where tr.franqueada_id = {$franqueadaId}";



                $filtros = "";
                $anoInicial = '2000';
                $anoFinal = '2200';
                $mesInicial = '01';
                $mesFinal = '12';
                $situacoes = [];
                if(isset($params[ConstanteParametros::CHAVE_FILTRO_ANO])){
                    $ano = $params[ConstanteParametros::CHAVE_FILTRO_ANO];
                    $anoInicial = $ano;
                    $anoFinal = $ano;
                    //filtra ano
                }

                if(isset($params[ConstanteParametros::CHAVE_FILTRO_MES])){
                    $mes = $params[ConstanteParametros::CHAVE_FILTRO_MES];
                    $filtros = "";
                    if ($mes != null && $mes!= '') {
                        $mesInicial = intval($mes) + 1;
                        $mesFinal = intval($mes) + 1;
            
                    }
                }

              

                    //datas no formato mes ano
                    $dataInicio = (new \DateTime(date("$anoInicial-$mesInicial-01 00:00:00")))->format('c');
                    $dataFim  = (new \DateTime(date("$anoFinal-$mesFinal-01 23:59:59")))->format('Y-m-t\TH:i:sP');
                    // $params[ConstanteParametros::CHAVE_DATA_INICIO] = "11/03/2024";
                    // $params[ConstanteParametros::CHAVE_DATA_FIM] = "11/03/2024";

                     //datas no formato dia mes ano
                    if(isset($params[ConstanteParametros::CHAVE_DATA_INICIO]) && $params[ConstanteParametros::CHAVE_DATA_INICIO] != "" ){
                        $dateArray = explode("/", $params[ConstanteParametros::CHAVE_DATA_INICIO]);
                        $dataInicio =   $dateArray[2].'-' .$dateArray[1].'-' .$dateArray[0]." 00:00:00"; 
                        // $firstDayOfMonth = (new \DateTime(date($dataInicio )))->format('c');
                    }
                    if(isset($params[ConstanteParametros::CHAVE_DATA_FIM]) && $params[ConstanteParametros::CHAVE_DATA_FIM] != "" ){
                        $dateArray = explode("/", $params[ConstanteParametros::CHAVE_DATA_FIM]);
                        $dataFim =   $dateArray[2].'-' .$dateArray[1].'-' .$dateArray[0]." 23:59:59"; 
                        // $lastDayOfMonth  = (new \DateTime(date($dataFim)))->format('Y-m-t\TH:i:sP');
                    }

                    // if($useMovimento){
                    //     $filtros.= " and mc.data_contabil between '$firstDayOfMonth' and '$lastDayOfMonth' ";
                    // }
                    // else{
                    //     $filtros.= " and tr.data_vencimento between '$firstDayOfMonth' and '$lastDayOfMonth' ";
                    // }
                    
                    if($useMovimento){
                        $filtros.= " and mc.data_contabil between '$dataInicio' and '$dataFim' ";
                    }
                    else{
                        $filtros.= " and tr.data_vencimento between '$dataInicio' and '$dataFim' ";
                    }
                    
                
                 $sitSAlunos =[];

                

                if(isset($params['situacao_aluno'])) {

                    $sitSAlunos = $params['situacao_aluno']; 
                       if(count($sitSAlunos) > 0 ){
                        
                        $sitAluno = "";
                        $sitAluno.= "('";
                        foreach ($sitSAlunos as $s) {
                            $sitAluno.=$s."','";
                        }
                        //$sitAluno.= ")";
                        $sitAluno = rtrim($sitAluno, "','");  // Remove a última vírgula e as aspas adicionais desnecessárias
                        $sitAluno .= "')";
                    

                        $sitAluno = substr($sitAluno, 0, -1);
                        $sitAluno.= ")";
                        $filtros.= " and ( UPPER(al.situacao) in $sitAluno or al.situacao is null)";
                    }
               }
              

           
                
                // $filtroFormaPagamentoDinamica = " and ( 1=1 ";

                   

               


                if(isset($params['busca'])){
                    $busca = strtoupper($params['busca']);
                    $filtros.= " and ( UPPER(p.nome_contato) like '%$busca%' OR  UPPER(al_pessoa.nome_contato) like '%$busca%' OR  cr.id = '$busca' OR  tr.id = '$busca') ";
                    
                }


                

                $sql.= $filtros;
                
                // if( $filtrarVencidos){
                //     $sql.= " having titulo_vencido = true ";
                //     //verificar tbm se esta pago
                // }
                $sql.= " ORDER BY  tr.data_vencimento ASC, cliente_nome ASC, tr.id DESC";

        //    echo $sql;
        //    die;
        // $connection = self::getManagerRegistry()->getConnection();
        $connection = self::getEntityManager()->getConnection();

        $dadosNaoTratados = $connection->fetchAllAssociative($sql);
      

        // Array para armazenar os valores únicos
        $dadosUnicos = [];

        foreach ($dadosNaoTratados as $item) {
            $dadosUnicos[$item['id']] = $item;
        }

        // Convertendo de volta para índices numéricos
        $dadosUnicos = array_values($dadosUnicos);
        
        $formaPagamentoFiltro = null;
        //INICIO - Trecho vindo da Consulta Inadimplentes  forma_recebimento
        if(isset($params['forma_cobranca'])) {
            $formaPagamento = $params['forma_cobranca'];
            $sqlFpgto = "SELECT f.forma_boleto, f.forma_cartao, f.forma_cartao_debito, f.forma_cheque, f.forma_transferencia FROM influx_crm_prod.forma_pagamento as f where f.id = $formaPagamento  limit 1" ;
            $connection = self::getEntityManager()->getConnection();

            $formaPagamentoFiltro = $connection->fetchAllAssociative($sqlFpgto)[0];
        }

        $consideraVencidoPendente = false;

        $tituloserrados ='';
        $retorno = [];
        $today = new \DateTime();
        $considera_nao_conciliados = true;
            foreach ($dadosUnicos as $dados) {
                $dados['sitVencido'] = false;
                $dados['sitAberto'] = false;
                $dados['sitRecebido'] = false;
                $dados['sitLiquidado'] = false;
                $dados['sitConciliado'] = false;
               
               if ($dados['fpgtoIsBoleto'] == 0 && $dados['fpgtoIsCheque'] == 0 && $dados['fpgtoIsCredito'] == 0 && $dados['fpgtoIsDebito'] == 0) {
                $dados['fpgtoIsBoleto'] = $dados['fcobIsBoleto'];
                $dados['fpgtoIsCheque'] = $dados['fcobIsCheque'];
                $dados['fpgtoIsCredito'] = $dados['fcobIsCredito'];
                $dados['fpgtoIsDebito'] = $dados['fcobIsDebito'];   
               
                }
               
               
               
                
                $dados['data_pagamento'] = $dados['data_vencimento'];
                $dados['data_baixa'] = '';
                $dados['valor_recebido'] = 0;
                $dados['valor_receber'] = 0;
                $dados['valor_cancelado'] = 0;
                $dados['valor_vencido'] = 0;
                $dados['valor_conciliado'] = 0;
                $dados['valor_aberto'] = 0;

                //CORRIGIGE FORMA DE PAGAMENTO
                if($dados['boleto_total_conciliado'] > 0){
                    $dados['fpgtoIsBoleto'] = 1;
                    $dados['fpgtoIsCheque'] = 0;
                    $dados['fpgtoIsCredito'] = 0;
                    $dados['fpgtoIsDebito'] = 0;
                    $dados['fpgto'] == 'Boleto';
                    $dados['fpgtoIsTr'] = 0;
                }
                if($dados['cheque_total_conciliado'] > 0){
                    $dados['fpgtoIsBoleto'] = 0;
                    $dados['fpgtoIsCheque'] = 1;
                    $dados['fpgtoIsCredito'] = 0;
                    $dados['fpgtoIsDebito'] = 0;
                    $dados['fpgto'] == 'Boleto';
                    $dados['fpgtoIsTr'] = 0;
                }
                if($dados['cartao_total_conciliado'] > 0){
                    $dados['fpgtoIsBoleto'] = 0;
                    $dados['fpgtoIsCheque'] = 0;
                    $dados['fpgtoIsCredito'] = 1;
                    $dados['fpgtoIsDebito'] = 0;
                    $dados['fpgto'] == 'Boleto';
                    $dados['fpgtoIsTr'] = 0;
                }
                if($dados['tr_total_conciliado'] > 0){
                    $dados['fpgtoIsBoleto'] = 0;
                    $dados['fpgtoIsCheque'] = 0;
                    $dados['fpgtoIsCredito'] = 0;
                    $dados['fpgtoIsDebito'] = 0;
                    $dados['fpgto'] == 'Boleto';
                    $dados['fpgtoIsTr'] = 1;
                }

                //valores ficam pendentes até a conciliação
                $dados['valor_saldo_devedor_calculado'] = 0;
                $dados['valor_saldo_devedor_calculado'] += $dados['cartao_total_pendente'];  
                $dados['valor_saldo_devedor_calculado'] += $dados['cheque_total_pendente'];  
                $dados['valor_saldo_devedor_calculado'] += $dados['tr_total_pendente'];  
                $dados['valor_saldo_devedor_calculado'] += $dados['boleto_total_pendente'];  

                
                if (! isset($dados['identificador']) ) {
                    $dados['identificador'] ='';
                }

                $dados['valor_conciliado'] =  $dados['boleto_total_conciliado'] + $dados['tr_total_conciliado'] + $dados['cartao_total_conciliado'] + $dados['cheque_total_conciliado'];
                // $dados['valor_conciliado'] +=  $dados['boleto_total_pendente'] + $dados['tr_total_pendente'] + $dados['cartao_total_pendente'] + $dados['cheque_total_pendente'];
                

                $dados['data_prevista'] = $dados['data_vencimento'];



                if ($dados['fpgtoIsBoleto'] == 1) {
                    $dados['metodo'] ='BOLETO';
                    $dados['data_pagamento'] = $dados['boleto_previsao'];
                    $dados['data_prevista'] = $this->proximoDiaUtil($dados['data_vencimento']);
                
                }
                if ($dados['fpgtoIsCredito'] == 1 || $dados['fpgtoIsDebito'] == 1) {
                    if ($dados['fcobIsDebito'] == 1 ) {
                            $dados['metodo'] ='DEBITO';
                    }
                    else{
                        $dados['metodo'] ='CRÉDITO';
                    }
                }
                if ($dados['fpgtoIsCheque'] == 1) {
                    $dados['metodo'] ='CHEQUE';
                }
                if ($dados['fpgtoIsTr'] == 1 ) {
                    $dados['metodo'] ='TRANSFERÊNCIA';
                }
                if ($dados['fpgto'] == 'Dinheiro' ) {
                    $dados['metodo'] ='DINHEIRO';
                }
                
                
                if($dados['situacao'] === 'SUB' || $dados['situacao'] === 'CAN'){
                    $dados['situacao'] = 'CAN';
                    $dados['valor_cancelado'] =  $dados['valor_original']; 
                }
                else
                {
                    if($dados['situacao']   === 'LIQ' ){
                        $dados['valor_recebido']  =  $dados['valor_conciliado'];
                        if ($useMovimento) {
                            $dados['valor_recebido']  =  $dados['valor_lancamento'];
                        }
                
                    }
                    else
                    {
                        $dados['valor_recebido']  =  $dados['valor_conciliado'];
                        if ($useMovimento) {
                            $dados['valor_recebido']  =  $dados['valor_lancamento'];
                        }
                        if($dados['valor_saldo_devedor']  > 0 ){
                            $dados['situacao']  = 'REC';
                            $dados['valor_receber']  = $dados['valor_saldo_devedor'];
                            if ($dados['fpgtoIsBoleto'] == 1) {
                                $dados['situacao'] = 'ABE';
                                if($dados['titulo_vencido'] && $this->today() > strtotime($dados['data_prevista'])){
                                    $dados['situacao'] = 'VEN';
                                    $dados['valor_vencido'] =  $dados['valor_vencido'] + $dados['valor_saldo_devedor'];
                                }
                                $dados['data_baixa'] = $dados['boleto_previsao'];
                            }
                            $dados['valor_receber']  = $dados['valor_receber'] - $dados['valor_vencido'];
                            $tituloserrados =  $tituloserrados.','.$dados['id'];
                        }
                        else{
                            $dados['situacao']   = 'LIQ'; 
                        }
                    }
                    
                }

                // if ( $dados['situacao'] = 'PEN') {
                //     $dados['situacao'] = 'ABE';
                // }
               
                
                unset($dados['boleto_previsao']);
                unset($dados['boleto_situacao']);
                unset($dados['boleto_identificador']);
                unset($dados['cartao_previsao']);
                unset($dados['cartao_pagamento']);
                unset($dados['cartao_identificador']);
                unset($dados['cartao_situacao']);
                unset($dados['cheque_baixa']);
                unset($dados['cheque_previsao']);
                unset($dados['cheque_pagamento']);
                unset($dados['cheque_identificador']);
                unset($dados['cheque_situacao']);

                if($dados['valor_saldo_devedor'] <= 0){
                    $dados['valor_saldo_devedor'] = $dados['valor_saldo_devedor_calculado'];
                }
                
                


                $liberado = $dados;

                if (isset($params[ConstanteParametros::CHAVE_SITUACAO]) && (empty($params[ConstanteParametros::CHAVE_SITUACAO]) === false)) {
                    $situacoes = $params[ConstanteParametros::CHAVE_SITUACAO];                    
                }    

                $sit = $dados['situacao'];
                if ( count($situacoes) >0 ){
                    $achou = false;
                    foreach ( $situacoes as $s) {
                        if("$sit" === $s){
                            $achou = true;
                        }
                    }
                    if(!$achou){
                        $liberado = null;
                    }
    
                }

                if($formaPagamentoFiltro != null && $liberado != null){
                    $achou = false;
                    if($formaPagamentoFiltro["forma_transferencia"] == 1 &&  $dados['metodo'] === 'TRANSFERÊNCIA'){
                        $achou = true;
                    }
                    if($formaPagamentoFiltro["forma_cartao_debito"] == 1 ){
                        if( $dados['metodo'] === 'DEBITO'){
                            $achou = true;
                        }
                      
                    }
                    else
                    if($formaPagamentoFiltro["forma_cartao"] == 1 &&  $dados['metodo'] === 'CRÉDITO'){
                        $achou = true;
                    }
                    if($formaPagamentoFiltro["forma_cheque"] == 1 &&  $dados['metodo'] === 'CHEQUE'){
                        $achou = true;
                    }
                    if($formaPagamentoFiltro["forma_boleto"] == 1 &&  $dados['metodo'] === 'BOLETO'){
                        $achou = true;
                    }
                    if(!$achou){
                        $liberado = null;
                    }
                }

               
                if($liberado != null){
                        $retorno[] = $dados;
                }
               
                    
               

                // $retorno[] = $dados;
               
            }
                
                
           
        return $retorno;

        }

        function proximoDiaUtil($data, $tempoBaixa = 1) {
            // Convertendo a string de data em timestamp
            $timestamp = strtotime($data);
        
            // Adicionando um dia até encontrar um dia útil
            do {
                $timestamp = strtotime("+1 day", $timestamp);
                $diaDaSemana = date("w", $timestamp);
            } while ($diaDaSemana == "6" || $diaDaSemana == "0"); // 0 é domingo 6 é sabado
        
            //tempo para baixa em dias do titulo
            $timestamp = strtotime("+$tempoBaixa day", $timestamp);
            // Retorna a data do próximo dia útil
            return date("Y-m-d H:i:s", $timestamp);
        }

        function today() {
            // Convertendo a string de data em timestamp
           
            $dataAtual = date("Y-m-d");
            $dataComHora = $dataAtual . " 12:00:00";
            $timestamp = strtotime($dataComHora);
            return $timestamp;

        }
        

       
}
