<?php

namespace App\Repository\Principal;

use App\Entity\Principal\ItemContaReceber;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Symfony\Bridge\Doctrine\RegistryInterface;
use App\Helper\ConstanteParametros;

/**
 * @method ItemContaReceber|null find($id, $lockMode = null, $lockVersion = null)
 * @method ItemContaReceber|null findOneBy(array $criteria, array $orderBy = null)
 * @method ItemContaReceber[]    findAll()
 * @method ItemContaReceber[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class ItemContaReceberRepository extends ServiceEntityRepository
{


    public function __construct(RegistryInterface $registry)
    {
        parent::__construct($registry, ItemContaReceber::class);
    }

    /**
     * Monta queryBase de ligacoes
     *
     * @return \Doctrine\ORM\QueryBuilder
     */
    protected function montaQueryBase()
    {
        $queryBuilder = $this->createQueryBuilder("icr");
        $queryBuilder->addSelect("cr");
        $queryBuilder->addSelect("sacado");
        $queryBuilder->addSelect("al");
        $queryBuilder->addSelect("ctt");
        $queryBuilder->addSelect("mdlt");
        $queryBuilder->addSelect("tur");
        $queryBuilder->addSelect("tma");
        $queryBuilder->addSelect("agp");
        $queryBuilder->addSelect("p");
        $queryBuilder->addSelect("it");
        $queryBuilder->addSelect("tpi");
        $queryBuilder->addSelect("plc");
        $queryBuilder->addSelect("ue");
        $queryBuilder->leftJoin("icr.conta_receber", "cr");
        $queryBuilder->leftJoin("cr.aluno", "al");
        $queryBuilder->leftJoin("cr.contrato", "ctt");
        $queryBuilder->leftJoin("cr.sacado_pessoa", "sacado");
        $queryBuilder->leftJoin("ctt.turma", "tur");
        $queryBuilder->leftJoin("ctt.modalidade_turma", "mdlt");
        $queryBuilder->leftJoin("tur.turmaAulas", "tma");
        $queryBuilder->leftJoin("ctt.agendamentoPersonals", "agp");
        $queryBuilder->leftJoin("al.pessoa", "p");
        $queryBuilder->leftJoin("icr.item", "it");
        $queryBuilder->leftJoin("it.tipo_item", "tpi");
        $queryBuilder->leftJoin("icr.plano_conta", "plc");
        $queryBuilder->leftJoin("icr.usuario_entregue", "ue");

        return $queryBuilder;
    }

    /**
     * Adiciona os filtros De/Ate na query
     *
     * @param array $parametros
     * @param \Doctrine\ORM\QueryBuilder $queryBuilder
     */
    protected function filtrosDeAte($parametros, &$queryBuilder)
    {
        $bFiltrarPersonal = false;

        if ((isset($parametros[ConstanteParametros::CHAVE_MODALIDADE_TURMA]) === true) && (empty($parametros[ConstanteParametros::CHAVE_MODALIDADE_TURMA]) === false)) {
            $modalidadeTurmaRepository = $this->_em->getRepository(\App\Entity\Principal\ModalidadeTurma::class);
            $modalidadeTurmaORM        = $modalidadeTurmaRepository->find($parametros[ConstanteParametros::CHAVE_MODALIDADE_TURMA]);
            if ($modalidadeTurmaORM->getTipo() === 'PER') {
                $bFiltrarPersonal = true;
                $queryBuilder->andWhere("tur.id IS NULL");
            }

            $queryBuilder->andWhere("mdlt.id = :modalideTurma");
            $queryBuilder->setParameter("modalideTurma", $parametros[ConstanteParametros::CHAVE_MODALIDADE_TURMA]);
        }

        if ((isset($parametros[ConstanteParametros::CHAVE_DATA_SAIDA_INICIO]) === true)&&(empty($parametros[ConstanteParametros::CHAVE_DATA_SAIDA_INICIO]) === false)) {
            // if personal == false
            $queryBuilder->andWhere("cr.data_emissao >= :dataSaidaInicio");
            $queryBuilder->setParameter("dataSaidaInicio", $parametros[ConstanteParametros::CHAVE_DATA_SAIDA_INICIO]);
        }

        if ((isset($parametros[ConstanteParametros::CHAVE_DATA_SAIDA_FIM]) === true)&&(empty($parametros[ConstanteParametros::CHAVE_DATA_SAIDA_FIM]) === false)) {
            // if personal == false
            $queryBuilder->andWhere("cr.data_emissao <= :dataSaidaFim");
            $queryBuilder->setParameter("dataSaidaFim", $parametros[ConstanteParametros::CHAVE_DATA_SAIDA_FIM]);
        }

        if ((isset($parametros[ConstanteParametros::CHAVE_DATA_SAIDA_INICIO]) === true)&&(empty($parametros[ConstanteParametros::CHAVE_DATA_SAIDA_INICIO]) === false)) {
            $queryBuilder->andWhere("cr.data_emissao >= :dataSaidaInicio");
            $queryBuilder->setParameter("dataSaidaInicio", $parametros[ConstanteParametros::CHAVE_DATA_SAIDA_INICIO]);
        }

        if ((isset($parametros[ConstanteParametros::CHAVE_DATA_SAIDA_FIM]) === true)&&(empty($parametros[ConstanteParametros::CHAVE_DATA_SAIDA_FIM]) === false)) {
            $queryBuilder->andWhere("cr.data_emissao <= :dataSaidaFim");
            $queryBuilder->setParameter("dataSaidaFim", $parametros[ConstanteParametros::CHAVE_DATA_SAIDA_FIM]);
        }

        if ((isset($parametros[ConstanteParametros::CHAVE_DATA_ENTREGA_INICIO]) === true)&&(empty($parametros[ConstanteParametros::CHAVE_DATA_ENTREGA_INICIO]) === false)) {
            $queryBuilder->andWhere("icr.data_entrega >= :dataEntregaInicio");
            $queryBuilder->setParameter("dataEntregaInicio", $parametros[ConstanteParametros::CHAVE_DATA_ENTREGA_INICIO]);
        }

        if ((isset($parametros[ConstanteParametros::CHAVE_DATA_ENTREGA_FIM]) === true)&&(empty($parametros[ConstanteParametros::CHAVE_DATA_ENTREGA_FIM]) === false)) {
            $queryBuilder->andWhere("icr.data_entrega <= :dataEntregaFim");
            $queryBuilder->setParameter("dataEntregaFim", $parametros[ConstanteParametros::CHAVE_DATA_ENTREGA_FIM]);
        }

        if ((isset($parametros[ConstanteParametros::CHAVE_VALOR_INICIAL]) === true)&&(empty($parametros[ConstanteParametros::CHAVE_VALOR_INICIAL]) === false)) {
            $queryBuilder->andWhere("icr.valor >= :valorInicio");
            $queryBuilder->setParameter("valorInicio", $parametros[ConstanteParametros::CHAVE_VALOR_INICIAL]);
        }

        if ((isset($parametros[ConstanteParametros::CHAVE_VALOR_FIM]) === true)&&(empty($parametros[ConstanteParametros::CHAVE_VALOR_FIM]) === false)) {
            $queryBuilder->andWhere("icr.valor <= :valorFim");
            $queryBuilder->setParameter("valorFim", $parametros[ConstanteParametros::CHAVE_VALOR_FIM]);
        }

        if ($bFiltrarPersonal === true) {
            if ((isset($parametros[ConstanteParametros::CHAVE_DATA_AULA_INICIO]) === true)&&(empty($parametros[ConstanteParametros::CHAVE_DATA_AULA_INICIO]) === false)) {
                $queryBuilder->andWhere("agp.inicio >= :dataAulaInicio");
                $queryBuilder->setParameter("dataAulaInicio", $parametros[ConstanteParametros::CHAVE_DATA_AULA_INICIO]);
            }

            if ((isset($parametros[ConstanteParametros::CHAVE_DATA_AULA_FIM]) === true)&&(empty($parametros[ConstanteParametros::CHAVE_DATA_AULA_FIM]) === false)) {
                $queryBuilder->andWhere("agp.inicio <= :dataAulaFim");
                $queryBuilder->setParameter("dataAulaFim", $parametros[ConstanteParametros::CHAVE_DATA_AULA_FIM]);
            }
        } else {
            if ((isset($parametros[ConstanteParametros::CHAVE_DATA_AULA_INICIO]) === true)&&(empty($parametros[ConstanteParametros::CHAVE_DATA_AULA_INICIO]) === false)) {
                $queryBuilder->andWhere("tma.data_aula >= :dataAulaInicio");
                $queryBuilder->setParameter("dataAulaInicio", $parametros[ConstanteParametros::CHAVE_DATA_AULA_INICIO]);
            }

            if ((isset($parametros[ConstanteParametros::CHAVE_DATA_AULA_FIM]) === true)&&(empty($parametros[ConstanteParametros::CHAVE_DATA_AULA_FIM]) === false)) {
                $queryBuilder->andWhere("tma.data_aula <= :dataAulaFim");
                $queryBuilder->setParameter("dataAulaFim", $parametros[ConstanteParametros::CHAVE_DATA_AULA_FIM]);
            }
        }//end if
    }

    /**
     * Adiciona os filtros na query
     *
     * @param array $parametros
     * @param \Doctrine\ORM\QueryBuilder $queryBuilder
     */
    protected function montaFiltros($parametros, &$queryBuilder)
    {
        $queryBuilder->where("cr.franqueada = :idFranqueada");
        $queryBuilder->setParameter("idFranqueada", $parametros[ConstanteParametros::CHAVE_FRANQUEADA]);
        $queryBuilder->andWhere("tpi.tipo = :tipoItem");
        $queryBuilder->setParameter("tipoItem", 'P');

        if ((isset($parametros[ConstanteParametros::CHAVE_TURMA]) === true)&&(empty($parametros[ConstanteParametros::CHAVE_TURMA]) === false)) {
            $queryBuilder->andWhere("tur.id = :idTurma");
            $queryBuilder->setParameter("idTurma", $parametros[ConstanteParametros::CHAVE_TURMA]);
        }

        if ((isset($parametros[ConstanteParametros::CHAVE_ALUNO]) === true)&&(empty($parametros[ConstanteParametros::CHAVE_ALUNO]) === false)) {
            $queryBuilder->andWhere("al.id = :idAluno");
            $queryBuilder->setParameter("idAluno", $parametros[ConstanteParametros::CHAVE_ALUNO]);
        }

        if ((isset($parametros[ConstanteParametros::CHAVE_ITEM]) === true)&&(empty($parametros[ConstanteParametros::CHAVE_ITEM]) === false)) {
            $queryBuilder->andWhere("it.id = :idItem");
            $queryBuilder->setParameter("idItem", $parametros[ConstanteParametros::CHAVE_ITEM]);
        }

        if ((isset($parametros[ConstanteParametros::CHAVE_USUARIO]) === true)&&(empty($parametros[ConstanteParametros::CHAVE_USUARIO]) === false)) {
            $queryBuilder->andWhere("ue.id = :idUsuarioEntregue");
            $queryBuilder->setParameter("idUsuarioEntregue", $parametros[ConstanteParametros::CHAVE_USUARIO]);
        }

        if (isset($parametros[ConstanteParametros::CHAVE_ITEM_ENTREGUE]) === true) {
            $queryBuilder->andWhere("icr.situacao_entrega IN(:situacoesEntrega)");
            $queryBuilder->setParameter("situacoesEntrega", $parametros[ConstanteParametros::CHAVE_ITEM_ENTREGUE]);
        }

        $this->filtrosDeAte($parametros, $queryBuilder);
    }


    /**
     * Monta a query de paginacao
     *
     * @param array $parametros
     * @param number $pagina
     * @param number $numeroItensPorPagina
     *
     * @return \Knp\Component\Pager\Pagination\SlidingPagination
     */
    public function filtrarItemContaReceberPorPagina($parametros, $pagina=1, $numeroItensPorPagina=50)
    {
        $queryBuilder = $this->montaQueryBase();
        $this->montaFiltros($parametros, $queryBuilder);
        return \App\Helper\FunctionHelper::montaPaginatorPaginacao($queryBuilder, $pagina, $numeroItensPorPagina);
    }

    public function buscarRelatorioControleMaterialDidatico($parametros)
    {
        $query = <<< SQL
            SELECT result.id,
                result.item,
                result.saldo_estoque,
                result.estoque_minimo,
                result.tipo_item,
                Sum(qnt_total)                       AS qnt_total,
                Sum(qnt_entregue)                    AS qnt_entregue,
                Sum(qnt_entregar)                    AS qnt_entregar,
                Sum(qnt_cancelado)                   AS qnt_cancelado,
                Sum(qnt_total_semestre_anterior)     AS qnt_total_semestre_anterior,
                Sum(qnt_entregue_semestre_anterior)  AS qnt_entregue_semestre_anterior,
                Sum(qnt_entregar_semestre_anterior)  AS qnt_entregar_semestre_anterior,
                Sum(qnt_cancelado_semestre_anterior) AS qnt_cancelado_semestre_anterior
            FROM   (SELECT item.id,
                        item.descricao AS item,
                        item_franqueada.saldo_estoque,
                        item_franqueada.estoque_minimo,
                        'p' as tipo_item,
                        Sum(CASE
                                WHEN ( item_conta_receber.situacao_entrega IS NOT NULL
                                        AND semestre.id IS NOT NULL ) THEN
                                item_conta_receber.quantidade
                                ELSE 0
                            END)       AS qnt_total,
                        Sum(CASE
                                WHEN item_conta_receber.situacao_entrega = "E" THEN
                                item_conta_receber.quantidade
                                AND
                                semestre.id IS NOT NULL
                                ELSE 0
                            END)       AS qnt_entregue,
                        Sum(CASE
                                WHEN item_conta_receber.situacao_entrega = "N" THEN
                                item_conta_receber.quantidade
                                AND
                                semestre.id IS NOT NULL
                                ELSE 0
                            END)       AS qnt_entregar,
                        Sum(CASE
                                WHEN item_conta_receber.situacao_entrega = "C" THEN
                                item_conta_receber.quantidade
                                AND
                                semestre.id IS NOT NULL
                                ELSE 0
                            END)       AS qnt_cancelado,
                        Sum(CASE
                                WHEN ( item_conta_receber.situacao_entrega IS NOT NULL
                                        AND semestre_anterior.id IS NOT NULL ) THEN
                                item_conta_receber.quantidade
                                ELSE 0
                            END)       AS qnt_total_semestre_anterior,
                        Sum(CASE
                                WHEN item_conta_receber.situacao_entrega = "E"
                                    AND semestre_anterior.id THEN
                                item_conta_receber.quantidade
                                ELSE 0
                            END)       AS qnt_entregue_semestre_anterior,
                        Sum(CASE
                                WHEN ( item_conta_receber.situacao_entrega = "N"
                                        AND semestre_anterior.id ) THEN
                                item_conta_receber.quantidade
                                ELSE 0
                            END)       AS qnt_entregar_semestre_anterior,
                        Sum(CASE
                                WHEN ( item_conta_receber.situacao_entrega = "C"
                                        AND semestre_anterior.id ) THEN
                                item_conta_receber.quantidade
                                ELSE 0
                            END)       AS qnt_cancelado_semestre_anterior
                    FROM   influx_crm_prod.conta_receber
                        JOIN item_conta_receber
                                ON item_conta_receber.conta_receber_id = conta_receber.id
                        JOIN item
                            ON item.id = item_conta_receber.item_id
                        JOIN item_franqueada
                                ON item_franqueada.item_id = item.id
                        LEFT JOIN semestre
                                ON semestre.id = :semestre
                                    AND conta_receber.data_emissao >= semestre.data_inicio
                                    AND conta_receber.data_emissao <= semestre.data_termino
                        LEFT JOIN semestre AS semestre_anterior
                                ON semestre_anterior.id = :semestre_anterior
                                    AND conta_receber.data_emissao >=
                                        semestre_anterior.data_inicio
                                    AND conta_receber.data_emissao <=
                                        semestre_anterior.data_termino
                    WHERE  conta_receber.contrato_id IS NULL
                        AND item_franqueada.franqueada_id = :franqueada
                        AND item.tipo_item_id = 1
                    GROUP  BY item.id
                    UNION ALL
                    SELECT item.id,
                        item.descricao AS item,
                        item_franqueada.saldo_estoque,
                        item_franqueada.estoque_minimo,
                        'm' as tipo_item,
                        Sum(CASE
                                WHEN ( item_conta_receber.situacao_entrega IS NOT NULL
                                        AND contrato.id IS NOT NULL ) THEN
                                item_conta_receber.quantidade
                                ELSE 0
                            END)       AS qnt_total,
                        Sum(CASE
                                WHEN item_conta_receber.situacao_entrega = "E"
                                    AND contrato.id THEN item_conta_receber.quantidade
                                ELSE 0
                            END)       AS qnt_entregue,
                        Sum(CASE
                                WHEN ( item_conta_receber.situacao_entrega = "N"
                                        AND contrato.id ) THEN item_conta_receber.quantidade
                                ELSE 0
                            END)       AS qnt_entregar,
                        Sum(CASE
                                WHEN ( item_conta_receber.situacao_entrega = "C"
                                        AND contrato.id ) THEN item_conta_receber.quantidade
                                ELSE 0
                            END)       AS qnt_cancelado,
                        Sum(CASE
                                WHEN ( item_conta_receber.situacao_entrega IS NOT NULL
                                        AND contrato_semestre_anterior.id IS NOT NULL ) THEN
                                item_conta_receber.quantidade
                                ELSE 0
                            END)       AS qnt_total_semestre_anterior,
                        Sum(CASE
                                WHEN item_conta_receber.situacao_entrega = "E"
                                    AND contrato_semestre_anterior.id THEN
                                item_conta_receber.quantidade
                                ELSE 0
                            END)       AS qnt_entregue_semestre_anterior,
                        Sum(CASE
                                WHEN ( item_conta_receber.situacao_entrega = "N"
                                        AND contrato_semestre_anterior.id ) THEN
                                item_conta_receber.quantidade
                                ELSE 0
                            END)       AS qnt_entregar_semestre_anterior,
                        Sum(CASE
                                WHEN ( item_conta_receber.situacao_entrega = "C"
                                        AND contrato_semestre_anterior.id ) THEN
                                item_conta_receber.quantidade
                                ELSE 0
                            END)       AS qnt_cancelado_semestre_anterior
                    FROM   item_conta_receber
                        JOIN conta_receber
                                ON conta_receber.id = item_conta_receber.conta_receber_id
                        JOIN item
                                ON item.id = item_conta_receber.item_id
                        JOIN item_franqueada
                                ON item_franqueada.item_id = item.id
                        LEFT JOIN contrato
                                ON contrato.id = conta_receber.contrato_id
                                    AND contrato.semestre_id = :semestre
                        LEFT JOIN contrato AS contrato_semestre_anterior
                                ON contrato_semestre_anterior.id =
                                    conta_receber.contrato_id
                                    AND contrato_semestre_anterior.semestre_id = :semestre_anterior
                    WHERE  item_franqueada.franqueada_id = :franqueada
                        AND conta_receber.franqueada_id = :franqueada
                    GROUP  BY item.id) AS result
        SQL;

        $franqueada = $parametros[ConstanteParametros::CHAVE_FRANQUEADA];
        $semestre = $parametros[ConstanteParametros::CHAVE_SEMESTRE];
        $semestre_anterior = (int)$semestre - 1;
        $saldo_de = 'null';
        $saldo_ate = 'null';

        $where = '';
        if(isset($parametros[ConstanteParametros::CHAVE_SALDO_DE])) {
            $where = ' WHERE result.saldo_estoque >= :saldo_de ';
            $saldo_de = $parametros[ConstanteParametros::CHAVE_SALDO_DE];
        }
        if(isset($parametros[ConstanteParametros::CHAVE_SALDO_ATE])) {
            if(empty($where)) {
                $where = ' WHERE result.saldo_estoque <= :saldo_ate ';
            } else {
                $where .= ' AND result.saldo_estoque <= :saldo_ate ';
            }
            $saldo_ate = $parametros[ConstanteParametros::CHAVE_SALDO_ATE];
        }
        $query .= $where . ' GROUP BY result.id, result.tipo_item;';

        $params = [
            'franqueada' => $franqueada,
            'semestre' => $semestre,
            'semestre_anterior' => $semestre_anterior,
            'saldo_de' => $saldo_de,
            'saldo_ate' => $saldo_ate
        ];

        return $this->getEntityManager()
            ->getConnection()
            ->prepare($query)
            ->executeQuery($params)
            ->fetchAllAssociative();
    }
    
    public function obterRelatorioPedidoMaterialDidatico($parametros)
    {
        $queryBuilder = $this->createQueryBuilder("icr")
            ->select([
                'icr.id',
                'sac.nome_contato as sacado',
                'p.nome_contato as aluno',
                'tur.descricao as turma',
                'it.descricao as item',
                "CONCAT(al.id, '/', ctt.sequencia_contrato) as contrato",
                "liv.descricao as estagio",
                "date_format(ctt.data_matricula, '%Y-%m-%d') as data_matricula",
                "date_format(tur.data_inicio, '%Y-%m-%d') as data_inicio"
            ])
            ->leftJoin("icr.conta_receber", "cr")
            ->leftJoin("cr.aluno", "al")
            ->leftJoin("cr.contrato", "ctt")
            ->leftJoin("cr.sacado_pessoa", "sac")
            ->leftJoin("ctt.curso", "cur")
            ->leftJoin("ctt.turma", "tur")
            ->leftJoin("tur.livro", "liv")
            ->leftJoin("al.pessoa", "p")
            ->leftJoin("icr.item", "it")
            ->groupBy("icr.id")
            ->andWhere("icr.situacao_entrega = :situacao")
            ->setParameter("situacao", "N")
            ->andWhere("cr.franqueada = :idFranqueada")
            ->setParameter("idFranqueada", $parametros[ConstanteParametros::CHAVE_FRANQUEADA])
            ->andWhere("it.tipo_item = :idTipoItem")
            ->setParameter("idTipoItem", "1");
        
        if(key_exists(ConstanteParametros::CHAVE_DATA_INICIAL, $parametros) && !is_null($parametros[ConstanteParametros::CHAVE_DATA_INICIAL])) {
            $dataInicial = strtotime(str_replace("/", "-",$parametros[ConstanteParametros::CHAVE_DATA_INICIAL] . " 00:00:00"));
            $dataInicial = date('Y-m-d H:i:s', $dataInicial);
            $queryBuilder->andWhere("ctt.data_matricula >= :data_inicial");
            $queryBuilder->setParameter('data_inicial', $dataInicial);
        }
        if(key_exists(ConstanteParametros::CHAVE_DATA_FINAL, $parametros) && !is_null($parametros[ConstanteParametros::CHAVE_DATA_FINAL])) {
            $dataFinal = strtotime(str_replace("/", "-",$parametros[ConstanteParametros::CHAVE_DATA_FINAL] . " 23:59:59"));
            $dataFinal = date( 'Y-m-d H:i:s', $dataFinal);
            $queryBuilder->andWhere("ctt.data_matricula <= :data_final");
            $queryBuilder->setParameter('data_final', $dataFinal);
        }
        if(key_exists(ConstanteParametros::CHAVE_LIVRO, $parametros) && !is_null($parametros[ConstanteParametros::CHAVE_LIVRO])) {
            $livro = $parametros[ConstanteParametros::CHAVE_LIVRO];
            $queryBuilder->andWhere("liv.id = :livro");
            $queryBuilder->setParameter('livro', $livro);
        }
        if(key_exists(ConstanteParametros::CHAVE_TURMA, $parametros) && !is_null($parametros[ConstanteParametros::CHAVE_TURMA])) {
            $turma = $parametros[ConstanteParametros::CHAVE_TURMA];
            $queryBuilder->andWhere("tur.id = :turma");
            $queryBuilder->setParameter('turma', $turma);
        }
        if(key_exists(ConstanteParametros::CHAVE_ALUNO, $parametros) && !is_null($parametros[ConstanteParametros::CHAVE_ALUNO])) {
            $aluno = $parametros[ConstanteParametros::CHAVE_ALUNO];
            $queryBuilder->andWhere("al.id = :aluno");
            $queryBuilder->setParameter('aluno', $aluno);
        }


        return $queryBuilder->getQuery()->getResult();
    }
}
