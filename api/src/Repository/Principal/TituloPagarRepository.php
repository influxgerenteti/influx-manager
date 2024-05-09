<?php

namespace App\Repository\Principal;

use App\Entity\Principal\TituloPagar;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Symfony\Bridge\Doctrine\RegistryInterface;
use App\Helper\ConstanteParametros;
use App\Helper\VariaveisCompartilhadas;
use App\Helper\SituacoesSistema;

/**
 *
 * @method TituloPagar|null find($id, $lockMode = null, $lockVersion = null)
 * @method TituloPagar|null findOneBy(array $criteria, array $orderBy = null)
 * @method TituloPagar[]    findAll()
 * @method TituloPagar[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class TituloPagarRepository extends ServiceEntityRepository
{


    public function __construct(RegistryInterface $registry)
    {
        parent::__construct($registry, TituloPagar::class);
    }

    /**
     * Monta a query padrao
     *
     * @return \Doctrine\ORM\QueryBuilder
     */
    protected function montaQueryBase()
    {
        $queryBuilder = $this->createQueryBuilder('tpp');
        $queryBuilder->select('tpp', 'fp', 'ne', 'co');
        $queryBuilder->join("tpp.favorecido_pessoa", "fp");
        $queryBuilder->join("tpp.conta_pagar", "ne");
        $queryBuilder->join("tpp.conta", "co");
        return $queryBuilder;
    }

    /**
     * Filtra o TituloPagar por pagina
     *
     * @param array $parametros             Parametros a serem inclusos no objeto
     * @param integer $pagina               Numero da pagina
     * @param integer $numeroItensPorPagina Numero de itens a serem trazidos na consulta
     *
     * @return NULL|\App\Entity\Principal\TituloPagar[]
     */
    public function filtrarTituloPagar($parametros, $pagina=1, $numeroItensPorPagina=50)
    {
        $setParameters = [
            'franqueada' => $parametros[ConstanteParametros::CHAVE_FRANQUEADA],
        ];

        $queryBuilder = $this->montaQueryBase();
        $queryBuilder->where('tpp.franqueada = :franqueada');

        if (is_null($parametros[ConstanteParametros::CHAVE_FAVORECIDO_PESSOA]) === false) {
            $queryBuilder->andWhere('tpp.favorecido_pessoa = :favorecido_pessoa');
            $setParameters['favorecido_pessoa'] = $parametros[ConstanteParametros::CHAVE_FAVORECIDO_PESSOA];
        }

        if (is_null($parametros[ConstanteParametros::CHAVE_TIT_DATA_INICIAL]) === false) {
            $queryBuilder->andWhere('tpp.data_prorrogacao >= :data_vencimento_inicial');
            $setParameters['data_vencimento_inicial'] = $parametros[ConstanteParametros::CHAVE_TIT_DATA_INICIAL];
        }

        if (is_null($parametros[ConstanteParametros::CHAVE_TIT_DATA_FINAL]) === false) {
            $queryBuilder->andWhere('tpp.data_prorrogacao <= :data_vencimento_final');
            $setParameters['data_vencimento_final'] = $parametros[ConstanteParametros::CHAVE_TIT_DATA_FINAL];
        }

        if (is_null($parametros[ConstanteParametros::CHAVE_TIT_VALOR_INICIAL]) === false) {
            $queryBuilder->andWhere('tpp.valor_saldo >= :valor_saldo_inicial');
            $setParameters['valor_saldo_inicial'] = $parametros[ConstanteParametros::CHAVE_TIT_VALOR_INICIAL];
        }

        if (is_null($parametros[ConstanteParametros::CHAVE_TIT_VALOR_FINAL]) === false) {
            $queryBuilder->andWhere('tpp.valor_saldo <= :valor_saldo_final');
            $setParameters['valor_saldo_final'] = $parametros[ConstanteParametros::CHAVE_TIT_VALOR_FINAL];
        }

        if (is_null($parametros[ConstanteParametros::CHAVE_SITUACAO]) === false) {
            $queryBuilder->andWhere('tpp.situacao = :situacao');
            $setParameters['situacao'] = $parametros[ConstanteParametros::CHAVE_SITUACAO];
        }

        $queryBuilder->orderBy('tpp.data_prorrogacao', 'ASC')->setParameters($setParameters);
        return \App\Helper\FunctionHelper::montaPaginatorPaginacao($queryBuilder, $pagina, $numeroItensPorPagina);
    }

    /**
     * Monta a query para ser executada no relatório
     *
     * @param array $parametros
     *
     * @return string
     */
    public function prepararDadosRelatorio($parametros)
    {
        $queryBuilder = $this->createQueryBuilder("tp");
        $queryBuilder->select('tp.id');
        $queryBuilder->join('tp.franqueada', 'f');
        $queryBuilder->join('tp.favorecido_pessoa', 'p');
        $queryBuilder->join('tp.forma_cobranca', 'fc');
        $queryBuilder->join('tp.conta_pagar', 'cp');
        $queryBuilder->join('tp.conta', 'co');
        $queryBuilder->leftJoin('tp.movimento_conta', 'mc');
        $queryBuilder->leftJoin('mc.forma_pagamento', 'fp');
        $queryBuilder->leftJoin('cp.plano_contas_conta_pagar', 'pccp');
        $queryBuilder->leftJoin('pccp.plano_conta', 'pc');

        $queryBuilder->andWhere('tp.excluido = 0');

        $queryBuilder->andWhere('tp.franqueada = :franqueada');
        $queryBuilder->setParameter('franqueada', VariaveisCompartilhadas::$franqueadaID);
        $queryBuilder->distinct();
        // $queryBuilder->groupBy('tp.id');

        if (is_null($parametros[ConstanteParametros::CHAVE_SITUACAO]) === false) {
            $situacao = explode(',', $parametros[ConstanteParametros::CHAVE_SITUACAO]);
            $orQuery  = '';

            if (in_array(SituacoesSistema::SITUACAO_VENCIDAS, $situacao) === true) {
                $orQuery = " OR (tp.situacao = 'PEN' AND tp.data_prorrogacao < :hoje)";
            }

            $queryBuilder->andWhere("tp.situacao IN (:situacao) $orQuery");
            $queryBuilder->setParameter('situacao', implode("', '", $situacao));

            if (empty($orQuery) === false) {
                $queryBuilder->setParameter("hoje", (new \DateTime())->format('Y-m-d 00:00:01'));
            }
        }

        if (is_null($parametros[ConstanteParametros::CHAVE_CONTA]) === false) {
            $queryBuilder->andWhere('co = :conta');
            $queryBuilder->setParameter('conta', $parametros[ConstanteParametros::CHAVE_CONTA]);
        }

        if (is_null($parametros[ConstanteParametros::CHAVE_TIT_NUMERO_PARCELA_DOCUMENTO]) === false) {
            $queryBuilder->andWhere('tp.numero_parcela_documento = :numero');
            $queryBuilder->setParameter('numero', $parametros[ConstanteParametros::CHAVE_TIT_NUMERO_PARCELA_DOCUMENTO]);
        }

        if (!is_null($parametros[ConstanteParametros::CHAVE_PLANO_CONTA])) {
            $planoContaId = $parametros[ConstanteParametros::CHAVE_PLANO_CONTA];
            
            $subQueryBuilder = $queryBuilder->getEntityManager()->createQueryBuilder();
            $subQueryBuilder->select('sub_pc.id')
            ->from('App\Entity\Principal\PlanoConta', 'sub_pc')
            ->where('sub_pc.pai = :planoConta');
            
            $queryBuilder->andWhere(
                $queryBuilder->expr()->orX(
                    $queryBuilder->expr()->eq('pc.pai', ':planoConta'),
                    $queryBuilder->expr()->in('pc.pai', $subQueryBuilder->getDQL())
                )
            );

        
            $queryBuilder->setParameter('planoConta', $planoContaId);
        }

        if (is_null($parametros[ConstanteParametros::CHAVE_FAVORECIDO_PESSOA]) === false) {
            $queryBuilder->andWhere('p = :favorecido');
            $queryBuilder->setParameter('favorecido', $parametros[ConstanteParametros::CHAVE_FAVORECIDO_PESSOA]);
        }

        if (is_null($parametros[ConstanteParametros::CHAVE_FORMA_COBRANCA]) === false) {
            $queryBuilder->andWhere('fc = :formaCobranca');
            $queryBuilder->setParameter('formaCobranca', $parametros[ConstanteParametros::CHAVE_FORMA_COBRANCA]);
        }

        if (is_null($parametros[ConstanteParametros::CHAVE_FORMA_PAGAMENTO]) === false) {
            $queryBuilder->andWhere('fp = :formaPagamento');
            $queryBuilder->setParameter('formaPagamento', $parametros[ConstanteParametros::CHAVE_FORMA_PAGAMENTO]);
        }

        if (is_null($parametros[ConstanteParametros::CHAVE_DATA_VENCIMENTO_INICIAL]) === false) {
            $queryBuilder->andWhere('tp.data_prorrogacao >= :data_vencimento_inicial');
            $queryBuilder->setParameter('data_vencimento_inicial', $parametros[ConstanteParametros::CHAVE_DATA_VENCIMENTO_INICIAL]);
        }

        if (is_null($parametros[ConstanteParametros::CHAVE_DATA_VENCIMENTO_FINAL]) === false) {
            $queryBuilder->andWhere('tp.data_prorrogacao <= :data_vencimento_final');
            $queryBuilder->setParameter('data_vencimento_final', $parametros[ConstanteParametros::CHAVE_DATA_VENCIMENTO_FINAL]);
        }

        if (is_null($parametros[ConstanteParametros::CHAVE_DATA_PAGAMENTO_INICIAL]) === false) {
            $queryBuilder->andWhere('mc.data_contabil >= :data_pagamento_inicial');
            $queryBuilder->setParameter('data_pagamento_inicial', $parametros[ConstanteParametros::CHAVE_DATA_PAGAMENTO_INICIAL]);
        }

        if (is_null($parametros[ConstanteParametros::CHAVE_DATA_PAGAMENTO_FINAL]) === false) {
            $queryBuilder->andWhere('mc.data_contabil <= :data_pagamento_final');
            $queryBuilder->setParameter('data_pagamento_final', $parametros[ConstanteParametros::CHAVE_DATA_PAGAMENTO_FINAL]);
        }

        if (is_null($parametros[ConstanteParametros::CHAVE_VALOR_INICIAL]) === false) {
            $queryBuilder->andWhere('tp.valor_documento >= :valor_inicial');
            $queryBuilder->setParameter('valor_inicial', $parametros[ConstanteParametros::CHAVE_VALOR_INICIAL]);
        }

        if (is_null($parametros[ConstanteParametros::CHAVE_VALOR_FINAL]) === false) {
            $queryBuilder->andWhere('tp.valor_documento <= :valor_final');
            $queryBuilder->setParameter('valor_final', $parametros[ConstanteParametros::CHAVE_VALOR_FINAL]);
        }


       
        // Filtra e substitui a query para passar ao Jasper
        $sql = $queryBuilder->getQuery()->getSQL();
     
     //   $sql = preg_replace('/.*WHERE\s(.*)$/', '$1', $sql);
        $sql = preg_replace('/^.*?WHERE\s/', ' ', $sql);
  
        // Seleciona somente os wheres
        $sql = preg_replace('/t0_/', 'tituloPagar', $sql);
        $sql = preg_replace('/f1_/', 'franqueada', $sql);
        $sql = preg_replace('/p2_/', 'favorecidoPessoa', $sql);
        $sql = preg_replace('/f3_/', 'formaCobranca', $sql);
        $sql = preg_replace('/c4_/', 'contaPagar', $sql);
        $sql = preg_replace('/c5_/', 'conta', $sql);
        $sql = preg_replace('/m6_/', 'movimentoConta', $sql);
        $sql = preg_replace('/f7_/', 'formaPagamento', $sql);
        $sql = preg_replace('/p8_/', 'planoContaContaPagar', $sql);
        $sql = preg_replace('/p9_/', 'planoConta', $sql);
        $sql = preg_replace('/p10_/', 'planoConta', $sql);

        // Substituição de parâmetros
        $parameters = $queryBuilder->getParameters();
  

        foreach ($parameters as $parameter) {
            $param = $parameter->getValue();
            $sql   = preg_replace('/\?/', "'$param'", $sql, 1);
            if($parameter->getName() == 'planoConta') {
                $sql   = preg_replace('/\?/', "'$param'", $sql, 1);
            }
        }

        return $sql;
    }


}
