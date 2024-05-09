<?php

namespace App\Repository\Principal;

use App\Entity\Principal\ParcelaParcelamento;
use App\Entity\Principal\TransacaoCartao;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Symfony\Bridge\Doctrine\RegistryInterface;
use App\Helper\ConstanteParametros;
use App\Helper\VariaveisCompartilhadas;

/**
 * @method TransacaoCartao|null find($id, $lockMode = null, $lockVersion = null)
 * @method TransacaoCartao|null findOneBy(array $criteria, array $orderBy = null)
 * @method TransacaoCartao[]    findAll()
 * @method TransacaoCartao[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class TransacaoCartaoRepository extends ServiceEntityRepository
{


    public function __construct(RegistryInterface $registry)
    {
        parent::__construct($registry, TransacaoCartao::class);
    }

    /**
     * Monta a query base
     *
     * @return \Doctrine\ORM\QueryBuilder
     */
    protected function montaQueryBase()
    {
        $queryBuilder = $this->createQueryBuilder("trc");
        $queryBuilder->addSelect("fran");
        $queryBuilder->addSelect("tr");
        $queryBuilder->addSelect("sp");
        $queryBuilder->addSelect("fc");
        $queryBuilder->addSelect("ct");
        $queryBuilder->addSelect("op");
        $queryBuilder->addSelect("a");
        $queryBuilder->addSelect("ap");
        $queryBuilder->join("trc.franqueada", "fran");
        $queryBuilder->join("trc.titulo_receber", "tr");
        $queryBuilder->join(ParcelaParcelamento::class, "pp");
        $queryBuilder->where('pp.numero_parcela = 1');
        $queryBuilder->andWhere('pp.parcelamento_operadora_cartao = trc.parcelamento_operadora_cartao');
        $queryBuilder->leftJoin("trc.operadora_cartao", "op");
        $queryBuilder->leftJoin("tr.sacado_pessoa", "sp");
        $queryBuilder->leftJoin("tr.aluno", "a");
        $queryBuilder->leftJoin("a.pessoa", "ap");
        $queryBuilder->leftJoin("tr.forma_cobranca", "fc");
        $queryBuilder->leftJoin("tr.conta", "ct");

        return $queryBuilder;
    }

    /**
     * Query para realizar fitlro de franqueada
     *
     * @param \Doctrine\ORM\QueryBuilder $queryBuilder
     */
    private function filtrarFranqueada(&$queryBuilder)
    {
        $queryBuilder->andWhere('fran = :franqueada');
        $queryBuilder->setParameter('franqueada', VariaveisCompartilhadas::$franqueadaID);
    }

    /**
     * Monta os filtros De/Ate
     *
     * @param \Doctrine\ORM\QueryBuilder $queryBuilder
     * @param array $parametros
     */
    protected function montaFiltrosDeAte(&$queryBuilder, $parametros)
    {
        if ((isset($parametros[ConstanteParametros::CHAVE_VALOR_LIQUIDO_INICIO]) === true)&&(empty($parametros[ConstanteParametros::CHAVE_VALOR_LIQUIDO_INICIO]) === false)) {
            $queryBuilder->andWhere("trc.valor_liquido >= :valorLiquidoInicio");
            $queryBuilder->setParameter("valorLiquidoInicio", $parametros[ConstanteParametros::CHAVE_VALOR_LIQUIDO_INICIO]);
        }

        if ((isset($parametros[ConstanteParametros::CHAVE_VALOR_LIQUIDO_FIM]) === true)&&(empty($parametros[ConstanteParametros::CHAVE_VALOR_LIQUIDO_FIM]) === false)) {
            $queryBuilder->andWhere("trc.valor_liquido <= :valorLiquidoFim");
            $queryBuilder->setParameter("valorLiquidoFim", $parametros[ConstanteParametros::CHAVE_VALOR_LIQUIDO_FIM]);
        }

        if ((isset($parametros[ConstanteParametros::CHAVE_PREVISAO_REPASSE_INICIO]) === true)&&(empty($parametros[ConstanteParametros::CHAVE_PREVISAO_REPASSE_INICIO]) === false)) {
            $dataObj = null;
            \App\Helper\FunctionHelper::formataCampoDateTimeJS($parametros[ConstanteParametros::CHAVE_PREVISAO_REPASSE_INICIO], $dataObj);
            $queryBuilder->andWhere("trc.previsao_repasse >= :previsaoRepasseInicio");
            $queryBuilder->setParameter("previsaoRepasseInicio", $dataObj);
        }

        if ((isset($parametros[ConstanteParametros::CHAVE_PREVISAO_REPASSE_FIM]) === true)&&(empty($parametros[ConstanteParametros::CHAVE_PREVISAO_REPASSE_FIM]) === false)) {
            $dataObj = null;
            \App\Helper\FunctionHelper::formataCampoDateTimeJS($parametros[ConstanteParametros::CHAVE_PREVISAO_REPASSE_FIM], $dataObj);
            $queryBuilder->andWhere("trc.previsao_repasse <= :previsaoRepasseFim");
            $queryBuilder->setParameter("previsaoRepasseFim", $dataObj);
        }

        if ((isset($parametros[ConstanteParametros::CHAVE_DATA_ESTORNO_INICIO]) === true)&&(empty($parametros[ConstanteParametros::CHAVE_DATA_ESTORNO_INICIO]) === false)) {
            $dataObj = null;
            \App\Helper\FunctionHelper::formataCampoDateTimeJS($parametros[ConstanteParametros::CHAVE_DATA_ESTORNO_INICIO], $dataObj);
            $queryBuilder->andWhere("trc.data_estorno >= :dataEstornoInicio");
            $queryBuilder->setParameter("dataEstornoInicio", $dataObj);
        }

        if ((isset($parametros[ConstanteParametros::CHAVE_DATA_ESTORNO_FIM]) === true)&&(empty($parametros[ConstanteParametros::CHAVE_DATA_ESTORNO_FIM]) === false)) {
            $dataObj = null;
            \App\Helper\FunctionHelper::formataCampoDateTimeJS($parametros[ConstanteParametros::CHAVE_DATA_ESTORNO_FIM], $dataObj);
            $queryBuilder->andWhere("trc.data_estorno <= :dataEstornoFim");
            $queryBuilder->setParameter("dataEstornoFim", $dataObj);
        }

    }

    /**
     * Monta os filtros para realizacao da busca
     *
     * @param \Doctrine\ORM\QueryBuilder $queryBuilder
     * @param array $parametros
     */
    protected function montaFiltros(&$queryBuilder, $parametros)
    {
        $queryBuilder->andWhere("trc.situacao <> 'EXC'");

        if ((isset($parametros[ConstanteParametros::CHAVE_OPERADORA_CARTAO]) === true) && (empty($parametros[ConstanteParametros::CHAVE_OPERADORA_CARTAO]) === false)) {
            $queryBuilder->andWhere("op.id = :operadoraCartao");
            $queryBuilder->setParameter("operadoraCartao", $parametros[ConstanteParametros::CHAVE_OPERADORA_CARTAO]);
        }

        if ((isset($parametros[ConstanteParametros::CHAVE_NUMERO_LANCAMENTO]) === true)&&(empty($parametros[ConstanteParametros::CHAVE_NUMERO_LANCAMENTO]) === false)) {
            $queryBuilder->andWhere("trc.numero_lancamento LIKE :numLancamento");
            $queryBuilder->setParameter("numLancamento", "%" . $parametros[ConstanteParametros::CHAVE_NUMERO_LANCAMENTO] . "%");
        }

        if ((isset($parametros[ConstanteParametros::CHAVE_SACADO_PESSOA]) === true)&&(empty($parametros[ConstanteParametros::CHAVE_SACADO_PESSOA]) === false)) {
            $queryBuilder->andWhere("sp.id = :pessoa OR ap.id = :pessoa");
            $queryBuilder->setParameter("pessoa", $parametros[ConstanteParametros::CHAVE_SACADO_PESSOA]);
        }

        if ((isset($parametros[ConstanteParametros::CHAVE_ALUNO_PESSOA]) === true)&&(empty($parametros[ConstanteParametros::CHAVE_ALUNO_PESSOA]) === false)) {
            $queryBuilder->andWhere("sp.id = :pessoa OR ap.id = :pessoa");
            $queryBuilder->setParameter("pessoa", $parametros[ConstanteParametros::CHAVE_ALUNO_PESSOA]);
        }

        if ((isset($parametros[ConstanteParametros::CHAVE_IDENTIFICADOR]) === true)&&(empty($parametros[ConstanteParametros::CHAVE_IDENTIFICADOR]) === false)) {
            $queryBuilder->andWhere("trc.identificador LIKE :identificadorString");
            $queryBuilder->setParameter("identificadorString", "%" . $parametros[ConstanteParametros::CHAVE_IDENTIFICADOR] . "%");
        }

        if ((isset($parametros[ConstanteParametros::CHAVE_TIPO_TRANSACAO]) === true)&&(empty($parametros[ConstanteParametros::CHAVE_TIPO_TRANSACAO]) === false)) {
            $queryBuilder->andWhere("UPPER(trc.tipo_transacao) = :tipoTransacao");
            $queryBuilder->setParameter("tipoTransacao", strtoupper($parametros[ConstanteParametros::CHAVE_TIPO_TRANSACAO]));
        }

        if ((isset($parametros[ConstanteParametros::CHAVE_SITUACAO]) === true)&&(empty($parametros[ConstanteParametros::CHAVE_SITUACAO]) === false)) {
            $queryBuilder->andWhere("trc.situacao IN (:situacaoTransacao)");
            $queryBuilder->setParameter("situacaoTransacao", $parametros[ConstanteParametros::CHAVE_SITUACAO]);
        }

        $this->montaFiltrosDeAte($queryBuilder, $parametros);
    }

    /**
     * Filtra as transacoes por pagina
     *
     * @param array $parametros
     * @param number $numeroItensPorPagina
     *
     * @return \Knp\Component\Pager\Pagination\SlidingPagination
     */
    public function filtrarTransacaoCartaoPorPagina($parametros, $numeroItensPorPagina=50)
    {
        $queryBuilder = $this->montaQueryBase();
        // $queryBuilder->addSelect("pp.taxa as taxa");
        $this->filtrarFranqueada($queryBuilder);
        $this->montaFiltros($queryBuilder, $parametros);
        $opcoes = [];

        if ((isset($parametros[ConstanteParametros::CHAVE_ORDENACAO_COLUNA]) === true)&&(is_null($parametros[ConstanteParametros::CHAVE_ORDENACAO_COLUNA]) === false)) {
            $queryBuilder->orderBy($parametros[ConstanteParametros::CHAVE_ORDENACAO_COLUNA], $parametros[ConstanteParametros::CHAVE_ORDENACAO_SORT]);
            $opcoes[\Knp\Component\Pager\Paginator::SORT_FIELD_PARAMETER_NAME]     = "~";
            $opcoes[\Knp\Component\Pager\Paginator::SORT_DIRECTION_PARAMETER_NAME] = "~";
        }

        return \App\Helper\FunctionHelper::montaPaginatorPaginacao($queryBuilder, $parametros[ConstanteParametros::CHAVE_PAGINA], $numeroItensPorPagina, $opcoes);
    }

    /**
     * Filtra os registros por id
     *
     * @param int $id
     *
     * @return array|NULL
     */
    public function buscarRegistroPorId($id)
    {
        $queryBuilder = $this->montaQueryBase();
        $this->filtrarFranqueada($queryBuilder);
        $queryBuilder->andWhere("trc.id = :id");
        $queryBuilder->setParameter("id", $id);
        return \App\Helper\FunctionHelper::retornaArrayNull($queryBuilder, true);
    }


}
