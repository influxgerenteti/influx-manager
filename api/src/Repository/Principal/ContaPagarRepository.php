<?php

namespace App\Repository\Principal;

use App\Entity\Principal\ContaPagar;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Symfony\Bridge\Doctrine\RegistryInterface;
use App\Helper\ConstanteParametros;
use App\Helper\VariaveisCompartilhadas;

/**
 *
 * @method ContaPagar|null find($id, $lockMode = null, $lockVersion = null)
 * @method ContaPagar|null findOneBy(array $criteria, array $orderBy = null)
 * @method ContaPagar[]    findAll()
 * @method ContaPagar[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class ContaPagarRepository extends ServiceEntityRepository
{


    public function __construct(RegistryInterface $registry)
    {
        parent::__construct($registry, ContaPagar::class);
    }

    /**
     * Monta a query para buscar as contas a pagar
     *
     * @return \Doctrine\ORM\QueryBuilder
     */
    private function buscaContasPagar()
    {
        $queryBuilder = $this->createQueryBuilder("cp");
        $queryBuilder->addSelect("pessoaFornecedor, planoContasContaPagar, planoConta, tituloPagar, movimentoConta, conta, favorecido, formaCobranca, formaPagamento, cheque");
        $queryBuilder->join("cp.fornecedor_pessoa", "pessoaFornecedor");
        $queryBuilder->join("cp.titulo_pagar", "tituloPagar");
        $queryBuilder->leftJoin("cp.plano_contas_conta_pagar", "planoContasContaPagar");
        $queryBuilder->leftJoin("planoContasContaPagar.plano_conta", "planoConta");
        $queryBuilder->leftJoin("tituloPagar.movimento_conta", "movimentoConta");
        $queryBuilder->leftJoin("tituloPagar.conta", "conta");
        $queryBuilder->leftJoin("tituloPagar.favorecido_pessoa", "favorecido");
        $queryBuilder->leftJoin("tituloPagar.forma_cobranca", "formaCobranca");
        $queryBuilder->leftJoin("movimentoConta.forma_pagamento", "formaPagamento");
        $queryBuilder->leftJoin("tituloPagar.cheque", "cheque");

        return $queryBuilder;
    }

    /**
     * Query para realizar fitlro de franqueada
     *
     * @param \Doctrine\ORM\QueryBuilder $queryBuilder
     */
    private function filtrarFranqueada(&$queryBuilder)
    {
        $queryBuilder->where("cp.franqueada = :franqueada");
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
                $queryBuilder->andWhere("tituloPagar.data_prorrogacao >= :data_inicial_vencimento");
                $queryBuilder->setParameter("data_inicial_vencimento", $dataInicial);
            }
        }

        if (is_null($parametros[ConstanteParametros::CHAVE_FILTRO_DATA_FINAL_VENCIMENTO]) === false) {
            \App\Helper\FunctionHelper::formataCampoDateTimeJS($parametros[ConstanteParametros::CHAVE_FILTRO_DATA_FINAL_VENCIMENTO], $dataFinal);
            if (false !== $dataFinal) {
                $queryBuilder->andWhere("tituloPagar.data_prorrogacao <= :data_final_vencimento");
                $queryBuilder->setParameter("data_final_vencimento", $dataFinal);
            }
        }

        return $queryBuilder;
    }

    /**
     * Configura os parametros de Data Inicial e Data Final de pagamento
     *
     * @param array $parametros Parametros Requisicao
     * @param \Doctrine\ORM\QueryBuilder $queryBuilder Query montada
     *
     * @return \Doctrine\ORM\QueryBuilder
     */
    protected function configuraParametrosDataPagamentoInicialFinal(array $parametros, \Doctrine\ORM\QueryBuilder $queryBuilder)
    {
        $dataInicial = null;
        $dataFinal   = null;
        if (is_null($parametros[ConstanteParametros::CHAVE_FILTRO_DATA_INICIAL_PAGAMENTO]) === false) {
            \App\Helper\FunctionHelper::formataCampoDateTimeJS($parametros[ConstanteParametros::CHAVE_FILTRO_DATA_INICIAL_PAGAMENTO], $dataInicial);
            if (false !== $dataInicial) {
                $queryBuilder->andWhere("movimentoConta.data_movimento >= :data_inicial_pagamento");
                $queryBuilder->setParameter("data_inicial_pagamento", $dataInicial);
            }
        }

        if (is_null($parametros[ConstanteParametros::CHAVE_FILTRO_DATA_FINAL_PAGAMENTO]) === false) {
            \App\Helper\FunctionHelper::formataCampoDateTimeJS($parametros[ConstanteParametros::CHAVE_FILTRO_DATA_FINAL_PAGAMENTO], $dataFinal);
            if (false !== $dataFinal) {
                $queryBuilder->andWhere("movimentoConta.data_movimento <= :data_final_pagamento");
                $queryBuilder->setParameter("data_final_pagamento", $dataFinal);
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

        if (is_null($situacoes) === false && empty($situacoes) === false) {
            $orQuery = '';

            if (in_array('VEN', $situacoes) === true) {
                $orQuery = " OR (tituloPagar.situacao = 'PEN' AND tituloPagar.data_prorrogacao < :hoje)";
            }

            $queryBuilder->andWhere("tituloPagar.situacao IN (:situacao) $orQuery")
                ->setParameter("situacao", $situacoes);

            if (empty($orQuery) === false) {
                $queryBuilder->setParameter("hoje", (new \DateTime())->setTime(12, 0, 0));
            }
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
        $ano = $parametros[ConstanteParametros::CHAVE_FILTRO_ANO];

        if (is_null($ano) === true) {
            $ano = (new \DateTime())->format("Y");
        }

        if ((is_null($mes) === false) && ((empty($mes) === false) || ($mes === '0'))) {
            $mes = intval($mes) + 1;
            $firstDayOfMonth = (new \DateTime(date("{$ano}-{$mes}-01 00:00:01")))->format('c');
            $lastDayOfMonth  = (new \DateTime(date("{$ano}-{$mes}-01 23:59:59")))->format('Y-m-t\TH:i:sP');

            $queryBuilder->andWhere("tituloPagar.data_prorrogacao >= :mesInicial");
            $queryBuilder->setParameter("mesInicial", $firstDayOfMonth);

            $queryBuilder->andWhere("tituloPagar.data_prorrogacao <= :mesFinal");
            $queryBuilder->setParameter("mesFinal", $lastDayOfMonth);
        }

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
        if (is_null($parametros[ConstanteParametros::CHAVE_FILTRO_VALOR_INICIAL]) === false) {
            $queryBuilder->andWhere("tituloPagar.valor_saldo >= :valor_inicial");
            $queryBuilder->setParameter("valor_inicial", $parametros[ConstanteParametros::CHAVE_FILTRO_VALOR_INICIAL]);
        }

        if (is_null($parametros[ConstanteParametros::CHAVE_FILTRO_VALOR_FINAL]) === false) {
            $queryBuilder->andWhere("tituloPagar.valor_saldo <= :valor_final");
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
        if (is_null($parametros[ConstanteParametros::CHAVE_FAVORECIDO_PESSOA]) === false) {
            $queryBuilder->andWhere("tituloPagar.favorecido_pessoa = :destino");
            $queryBuilder->setParameter("destino", $parametros[ConstanteParametros::CHAVE_FAVORECIDO_PESSOA]);
        }

        return $queryBuilder;
    }

    /**
     * Configura os parametros de forma de pagamento para realizar os filtros
     *
     * @param array $parametros
     * @param \Doctrine\ORM\QueryBuilder $queryBuilder
     *
     * @return \Doctrine\ORM\QueryBuilder
     */
    protected function configuraParametrosFormaPagamento(array $parametros, \Doctrine\ORM\QueryBuilder $queryBuilder)
    {
        if (is_null($parametros[ConstanteParametros::CHAVE_FORMA_PAGAMENTO]) === false) {
            $queryBuilder->andWhere("formaCobranca.id = :formaPagamento");
            $queryBuilder->setParameter("formaPagamento", $parametros[ConstanteParametros::CHAVE_FORMA_PAGAMENTO]);
        }

        return $queryBuilder;
    }

    /**
     * Configura os parametros de Planos de Conta para realizar os filtros
     *
     * @param array $parametros
     * @param \Doctrine\ORM\QueryBuilder $queryBuilder
     *
     * @return \Doctrine\ORM\QueryBuilder
     */
    protected function configuraParametrosPlanoDeConta(array $parametros, \Doctrine\ORM\QueryBuilder $queryBuilder)
    {
        if ((isset($parametros[ConstanteParametros::CHAVE_PLANO_CONTA]) === true)&&(empty($parametros[ConstanteParametros::CHAVE_PLANO_CONTA]) === false)) {
            $subQuery = $this->_em->createQueryBuilder();
            $subQuery->select("pcc.id");
            $subQuery->from(\App\Entity\Principal\PlanoConta::class, "pcc");
            $subQuery->where(
                $subQuery->expr()->orX(
                    $subQuery->expr()->eq("pcc.id", $parametros[ConstanteParametros::CHAVE_PLANO_CONTA]),
                    $subQuery->expr()->eq("pcc.pai", $parametros[ConstanteParametros::CHAVE_PLANO_CONTA])
                )
            );
            $queryBuilder->andWhere(
                $queryBuilder->expr()->orX(
                    $queryBuilder->expr()->eq("planoConta.id", $parametros[ConstanteParametros::CHAVE_PLANO_CONTA]),
                    $queryBuilder->expr()->in("planoConta.pai", $subQuery->getDQL())
                )
            );
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
        $queryBuilder->andWhere("tituloPagar.excluido = 0");
        $queryBuilder = $this->configuraParametrosDataVencimentoInicialFinal($parametros, $queryBuilder);
        $queryBuilder = $this->configuraParametrosDataPagamentoInicialFinal($parametros, $queryBuilder);
        $queryBuilder = $this->configuraParametrosValorInicialFinal($parametros, $queryBuilder);
        $queryBuilder = $this->configuraParametrosDestino($parametros, $queryBuilder);
        $queryBuilder = $this->configuraParametrosSituacao($parametros, $queryBuilder);
        $queryBuilder = $this->configuraParametrosMesAno($parametros, $queryBuilder);
        $queryBuilder = $this->configuraParametrosFormaPagamento($parametros, $queryBuilder);
        $queryBuilder = $this->configuraParametrosPlanoDeConta($parametros, $queryBuilder);
        return $queryBuilder;
    }

    /**
     * Filtra as contas a pagar por pagina e numero de itens por pagina
     *
     * @param array $parametros
     * @param integer $pagina
     * @param integer $numeroItensPorPagina
     *
     * @return \Knp\Component\Pager\Pagination\SlidingPagination
     */
    public function filtrarContaPagarPorPagina($parametros, $pagina=1, $numeroItensPorPagina=1500)
    {
        $opcoes       = [];
        $queryBuilder = $this->buscaContasPagar();
        $this->filtrarFranqueada($queryBuilder);
        $queryBuilder = $this->configuraParametrosBusca($parametros, $queryBuilder);

        if ((isset($parametros[ConstanteParametros::CHAVE_ORDENACAO_COLUNA]) === true)&&(is_null($parametros[ConstanteParametros::CHAVE_ORDENACAO_COLUNA]) === false)) {
            $queryBuilder->orderBy($parametros[ConstanteParametros::CHAVE_ORDENACAO_COLUNA], $parametros[ConstanteParametros::CHAVE_ORDENACAO_SORT]);
            $opcoes[\Knp\Component\Pager\Paginator::SORT_FIELD_PARAMETER_NAME]     = "~";
            $opcoes[\Knp\Component\Pager\Paginator::SORT_DIRECTION_PARAMETER_NAME] = "~";
            $opcoes[\Knp\Component\Pager\Paginator::DISTINCT] = false;
        }

        return \App\Helper\FunctionHelper::montaPaginatorPaginacao($queryBuilder, $pagina, $numeroItensPorPagina, $opcoes);
    }

    /**
     * Busca uma conta a pagar por ID
     *
     * @param integer $id
     * @param integer $hydration
     *
     * @return \App\Entity\Principal\ContaPagar
     */
    public function buscarContaPagar ($id, $hydration=null)
    {
        $queryBuilder = $this->buscaContasPagar();
        $this->filtrarFranqueada($queryBuilder);
        $queryBuilder->andWhere('cp.id = :id');
        $queryBuilder->andWhere("tituloPagar.excluido = 0");
        $queryBuilder->setParameter('id', $id);
        return $queryBuilder->getQuery()->getOneOrNullResult($hydration);
    }


}
