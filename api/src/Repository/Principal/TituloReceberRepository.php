<?php

namespace App\Repository\Principal;

use App\Entity\Principal\TituloReceber;
use App\Helper\ConstanteParametros;
use App\Helper\SituacoesSistema;
use App\Helper\VariaveisCompartilhadas;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Symfony\Bridge\Doctrine\RegistryInterface;

/**
 * @method TituloReceber|null find($id, $lockMode = null, $lockVersion = null)
 * @method TituloReceber|null findOneBy(array $criteria, array $orderBy = null)
 * @method TituloReceber[]    findAll()
 * @method TituloReceber[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class TituloReceberRepository extends ServiceEntityRepository
{


    public function __construct(RegistryInterface $registry)
    {
        parent::__construct($registry, TituloReceber::class);
    }

    /**
     * Monta Query principal
     *
     * @return \Doctrine\ORM\QueryBuilder
     */
    protected function montaQueryBase()
    {
        $queryBuilder = $this->createQueryBuilder("tr");
        $queryBuilder->addSelect("fran");
        $queryBuilder->addSelect("cr");
        $queryBuilder->addSelect("sp");
        $queryBuilder->addSelect("al");
        $queryBuilder->addSelect("p");
        $queryBuilder->addSelect("ct");
        $queryBuilder->addSelect("fc");
        $queryBuilder->addSelect("fr");
        $queryBuilder->addSelect("tc");
        $queryBuilder->addSelect("ch");
        $queryBuilder->addSelect("bl");
        $queryBuilder->leftJoin("tr.franqueada", "fran");
        $queryBuilder->leftJoin("tr.conta_receber", "cr");
        $queryBuilder->leftJoin("tr.sacado_pessoa", "sp");
        $queryBuilder->leftJoin("tr.aluno", "al");
        $queryBuilder->leftJoin("al.pessoa", "p");
        $queryBuilder->leftJoin("tr.conta", "ct");
        $queryBuilder->leftJoin("tr.forma_cobranca", "fc");
        $queryBuilder->leftJoin("tr.forma_recebimento", "fr");
        $queryBuilder->leftJoin("tr.transacoes_cartao", "tc");
        $queryBuilder->leftJoin("tr.cheques", "ch");
        $queryBuilder->leftJoin("tr.boletos", "bl");

        return $queryBuilder;
    }

    /**
     * Busca o registro pela chave primaria
     *
     * @param int $id
     *
     * @return array|NULL
     */
    public function buscarPorId($id)
    {
        $queryBuilder = $this->montaQueryBase();
        $queryBuilder->where("tr.id = :id");
        $queryBuilder->setParameter("id", $id);

        return $queryBuilder->getQuery()->getResult(\Doctrine\ORM\Query::HYDRATE_ARRAY);
    }

    public function buscaMovimentosPorTitulo($tituloId){
        $franqueadaId = VariaveisCompartilhadas::$franqueadaID;

       

        $connection = self::getEntityManager()->getConnection();

        $sqlCheques = <<<SQL
            SELECT 'CHEQUE' as tipo, id, movimento_conta_id as movimento_id, numero as identificador, 
                situacao as situacao,
                'C' as operacao,
                valor as valor,
                data_baixa as data_pagamento,
                numero,
                titular,
                banco,
                agencia,
                conta as conta_corrente,
                data_bom_para,
                data_devolucao,
                data_segunda_devolucao
            FROM influx_crm_prod.cheque
            where excluido = 0 and titulo_receber_id = {$tituloId};
            SQL;

        $sqlTranferencias = <<<SQL
            SELECT 'TR' as tipo,
                tr.id,
                movimento_conta_id as movimento_id,
                '' as identificador, 
                tr.situacao as situacao,
                tipo_transacao as operacao,
                valor as valor,
                '' as data_pagamento,
                agencia,
                data_estorno,
                con.descricao as conta,
                mov.data_contabil
            FROM influx_crm_prod.transferencia_bancaria tr
            LEFT JOIN influx_crm_prod.movimento_conta mov ON tr.movimento_conta_id = mov.id
            LEFT JOIN conta con ON mov.conta_id = con.id
            where tr.titulo_receber_id = {$tituloId};
            SQL;

         $sqlBoletos = <<<SQL
            SELECT 'BOLETO' as tipo, 
                boleto.id, 
                movimento_conta_id as movimento_id, 
                nosso_numero as identificador, 
                situacao_cobranca as situacao,
                'C' as operacao,
                valor as valor,
                data_vencimento as data_contabil,
                nosso_numero,
                valor_desconto,
                conta.descricao as conta
            FROM influx_crm_prod.boleto
            LEFT JOIN influx_crm_prod.conta ON conta.id = boleto.conta_id
            where titulo_receber_id = {$tituloId};
            SQL;

         $sqlCartao="
            SELECT 'CARTAO' as tipo, id, movimento_conta_id as movimento_id, identificador as identificador, 
            situacao as situacao,
            tipo_transacao as operacao,
            valor_liquido as valor,
            data_pagamento as data_pagamento,
            operadora_cartao_id,
            parcelamento_operadora_cartao_id,
            numero_lancamento, 
            taxa,previsao_repasse,
            data_estorno,
            valor_desconto
            FROM influx_crm_prod.transacao_cartao 
            where titulo_receber_id = {$tituloId}
         ";

    //     $sqlPagamentos = " SELECT 'TR' as tipo, id, movimento_conta_id as movimento_id, '' as identificador, 
    //     situacao as situacao,
    //     tipo_transacao as operacao,
    //     valor as valor,
    //     '' as data_pagamento
    //      FROM influx_crm_prod.transferencia_bancaria
    //      where titulo_receber_id = {$tituloId}
    //      UNION ALL
         
    //      SELECT 'BOLETO' as tipo, id, movimento_conta_id as movimento_id, nosso_numero as identificador, 
    //    situacao_cobranca as situacao,
    //     'C' as operacao,
    //     valor as valor,
    //     data_vencimento as data_pagamento
    //      FROM influx_crm_prod.boleto
    //      where titulo_receber_id = {$tituloId}
         
    //      UNION ALL
         
    //      SELECT 'CHEQUE' as tipo, id, movimento_conta_id as movimento_id, numero as identificador, 
    //      situacao as situacao,
    //     'C' as operacao,
    //     valor as valor,
    //     data_baixa as data_pagamento
    //     FROM influx_crm_prod.cheque 
    //     where excluido = 0 and titulo_receber_id = {$tituloId}
        
    //     UNION ALL
    //     SELECT 'CARTAO' as tipo, id, movimento_conta_id as movimento_id, identificador as identificador, 
    //     situacao as situacao,
    //     tipo_transacao as operacao,
    //     valor_liquido as valor,
    //     data_pagamento as data_pagamento
    //     FROM influx_crm_prod.transacao_cartao 
    //     where  titulo_receber_id = {$tituloId}
    //     ";

        

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

       

        $movimentos = $connection->fetchAllAssociative($sqlMovimentos);
        $pagamentos =[];
        $retorno = $connection->fetchAllAssociative($sqlBoletos);
        foreach ($retorno as $r) {
            $pagamentos[] =$r;
        }
        $retorno = $connection->fetchAllAssociative($sqlCheques);
        foreach ($retorno as $r) {
            $pagamentos[] =$r;
        }
        $retorno = $connection->fetchAllAssociative($sqlTranferencias);
        foreach ($retorno as $r) {
            $pagamentos[] =$r;
        }
        $retorno = $connection->fetchAllAssociative($sqlCartao);
        foreach ($retorno as $r) {
            $pagamentos[] =$r;
        }
        

       $retorno = [];
       $retorno['movimentos']  = $movimentos;
       $retorno['pagamentos']  = $pagamentos;

        // $titulo['movimentos'] = $movimentos;

        return $retorno;
    }

    /**
     * Monta a query para ser executada no relatório de alunos inadimplentes
     *
     * @param array $parametros
     *
     * @return string
     */
    public function prepararDadosRelatorioAlunosInadimplentes($parametros)
    {
        $queryBuilder = $this->createQueryBuilder("tr");
        $queryBuilder->select('tr.id');
        $queryBuilder->join('tr.aluno', 'a');
        $queryBuilder->join('a.pessoa', 'p');
        $queryBuilder->leftJoin('p.cidade', 'c');

        $queryBuilder->andWhere("tr.situacao = 'PEN'");

        $queryBuilder->andWhere('tr.franqueada = :franqueada');
        $queryBuilder->setParameter('franqueada', VariaveisCompartilhadas::$franqueadaID);

        if ((isset($parametros[ConstanteParametros::CHAVE_SITUACAO]) === true)&&(empty($parametros[ConstanteParametros::CHAVE_SITUACAO]) === false)) {
            $situacao = explode(',', $parametros[ConstanteParametros::CHAVE_SITUACAO]);
            $queryBuilder->andWhere("a.situacao IN (:situacao)");
            $queryBuilder->setParameter('situacao', implode("', '", $situacao));
        }

        //forma_cobranca
        if ((isset($parametros[ConstanteParametros::CHAVE_FORMA_COBRANCA]) === true)&&(empty($parametros[ConstanteParametros::CHAVE_FORMA_COBRANCA]) === false)) {
            $queryBuilder->andWhere('tr.forma_cobranca = :forma_cobranca');
            $queryBuilder->setParameter('forma_cobranca', $parametros[ConstanteParametros::CHAVE_FORMA_COBRANCA]);
        }

        if ((isset($parametros[ConstanteParametros::CHAVE_CLASSIFICACAO_ALUNO]) === true)&&(empty($parametros[ConstanteParametros::CHAVE_CLASSIFICACAO_ALUNO]) === false)) {
            $queryBuilder->andWhere('a.classificacao_aluno = :classificacao');
            $queryBuilder->setParameter('classificacao', $parametros[ConstanteParametros::CHAVE_CLASSIFICACAO_ALUNO]);
        }

        if ((isset($parametros[ConstanteParametros::CHAVE_TIPO_ALUNO]) === true)&&(empty($parametros[ConstanteParametros::CHAVE_TIPO_ALUNO]) === false)) {
            switch ((int) $parametros[ConstanteParametros::CHAVE_TIPO_ALUNO]) {
                case 0:
                $queryBuilder->andWhere('tr.data_prorrogacao < :hoje or tr.negativado = 1');
                $queryBuilder->setParameter("hoje", (new \DateTime())->format('Y-m-d H:i:s'));
                    break;
                case 1:
                $queryBuilder->andWhere('tr.negativado = 1');
                    break;
                case 2:
                $queryBuilder->andWhere('tr.data_prorrogacao < :hoje and tr.negativado = 0');
                $queryBuilder->setParameter("hoje", (new \DateTime())->format('Y-m-d H:i:s'));
                    break;
            }
        }

        // Filtra e substitui a query para passar ao Jasper
        $sql = $queryBuilder->getQuery()->getSQL();
        $sql = preg_replace('/.*WHERE\s(.*)$/', '$1', $sql);

        // Seleciona somente os wheres
        $sql = preg_replace('/t0_/', 'tr', $sql);
        $sql = preg_replace('/a1_/', 'a', $sql);
        $sql = preg_replace('/p2_/', 'p', $sql);
        $sql = preg_replace('/c3_/', 'c', $sql);

        // Substituição de parâmetros
        $parameters = $queryBuilder->getParameters();
        foreach ($parameters as $parameter) {
            $param = $parameter->getValue();
            $sql   = preg_replace('/\?/', "'$param'", $sql, 1);
        }

        return $sql;
    }


    /**
     * Configura o parametro de situação
     *
     * @param array $parametros
     * @param \Doctrine\ORM\QueryBuilder $queryBuilder
     */
    protected function configuraParametrosSituacao($parametros, &$queryBuilder)
    {
        $situacoes = $parametros[ConstanteParametros::CHAVE_SITUACAO];

        if (is_null($situacoes) === true || empty($situacoes) === true) {
            return false;
        }

        $haSituacaoPendente = in_array(SituacoesSistema::SITUACAO_PENDENTE, $situacoes);
        $haSituacaoQuitado  = in_array(SituacoesSistema::SITUACAO_LIQUIDADO, $situacoes);

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
                    $expr->eq('titrec.id', 't.id'),
                    $expr->orX(
                        $expr->eq('transcart.situacao', ':situacaoPendente'),
                        $expr->andX(
                            $expr->eq('cheq.situacao', ':situacaoChequePendente'),
                            $expr->eq('cheq.excluido', 0)
                        )
                    )
                )
            );
            $queryBuilder->setParameter("situacaoPendente", SituacoesSistema::SITUACAO_PENDENTE);
            $queryBuilder->setParameter("situacaoChequePendente", SituacoesSistema::SITUACAO_CHEQUE_PENDENTE);
            $conditions = [];
            // Quando estiver checando por pendente não precisa checar por vencidos, visto que é um subgrupo de pendentes
            if (($key = array_search(SituacoesSistema::SITUACAO_PENDENTE, $situacoes)) !== false) {
                unset($situacoes[$key]);
                if (count($situacoes) > 0) {
                    $conditions[] = $queryBuilder->expr()->in("t.situacao", $situacoes);
                }

                $conditions[] = $queryBuilder->expr()->andX(
                    $queryBuilder->expr()->eq('t.situacao', ':situacaoPendente'),
                    $queryBuilder->expr()->not(
                        $queryBuilder->expr()->exists($subQuery->getDQL())
                    )
                );
            }

            if (in_array(SituacoesSistema::SITUACAO_LIQUIDADO, $situacoes) === true) {
                $conditions[] = $queryBuilder->expr()->in("t.situacao", $situacoes);
                $conditions[] = $queryBuilder->expr()->exists($subQuery->getDQL());

                if (in_array(SituacoesSistema::SITUACAO_VENCIDAS, $situacoes) === true) {
                    $conditions[] = $queryBuilder->expr()->andX(
                        $queryBuilder->expr()->eq('t.situacao', ':situacaoPendente'),
                        $queryBuilder->expr()->lt('t.data_vencimento', ':hoje')
                    );
                    $queryBuilder->setParameter("hoje", new \DateTime());
                }
            }

            // A funcionalidade abaixo é o equivalente a chamar andWhere orX em todas as condições dentro do array $conditions
            // Ou seja, se qualquer uma das condições do array estiver ok, vai atender ao andWhere
            $conditions = call_user_func_array([$queryBuilder->expr(), 'orX'], $conditions);
            $queryBuilder->andWhere($conditions);
        } else {
            $orQuery = '';
            if (in_array(SituacoesSistema::SITUACAO_VENCIDAS, $situacoes) === true) {
                $orQuery .= " OR (t.situacao = 'PEN' AND t.data_vencimento < :hoje)";
                $queryBuilder->setParameter("hoje", new \DateTime());
            }

            $queryBuilder->andWhere("t.situacao IN (:situacao) $orQuery");
            $queryBuilder->setParameter("situacao", $situacoes);
        }//end if
    }


    /**
     * Busca os dados do relatório de titulos
     *
     * @param string $mensagem
     * @param array $parametros
     *
     * @return array
     */
    public function buscarDadosRelatorioTitulos(&$mensagem, $parametros)
    {
        $queryBuilder = $this->createQueryBuilder("t");
        $queryBuilder->select(
            [
                "t.id",
                "p.nome_contato",
                "a.id aluno_id",
                "p1.nome_contato",
                "t.observacao parcela",
                "date_format(t.data_vencimento, '%Y-%m-%d') as data_vencimento",
                "date_format(mc.data_deposito, '%Y-%m-%d') as data_pagamento",
                "t.valor_parcela_sem_desconto",
                "t.desconto_antecipacao",
                "t.situacao situacao_titulo",
                "fc.descricao forma_cobranca",
                "fp.descricao forma_pagamento",
                "fc.forma_cartao cartao",
                "COALESCE(mc.valor_lancamento ,0) valor_pago",
                "t.valor_saldo_devedor as saldo_devedor",
                "cheques.id as tcheque",
                "transacoesCartao.id as tcartao",
            ]
        );
        $queryBuilder->leftJoin('t.conta_receber', 'cr');
        $queryBuilder->leftJoin('cr.contrato', 'c');
        $queryBuilder->leftJoin('c.aluno', 'a');
        $queryBuilder->leftJoin('a.pessoa', 'p');
        $queryBuilder->leftJoin('t.sacado_pessoa', 'p1');
        $queryBuilder->leftJoin('t.movimento_conta', 'mc', 'WITH', "mc.operacao = 'C' AND mc.estornado = 0");
        $queryBuilder->leftJoin('mc.forma_pagamento', 'fp');
        $queryBuilder->leftJoin('t.forma_cobranca', 'fc');
        $queryBuilder->leftJoin("t.cheques", "cheques");
        $queryBuilder->leftJoin("t.transacoes_cartao", "transacoesCartao");        
        $queryBuilder->where('t.franqueada = :franqueada');
        $queryBuilder->setParameter('franqueada', VariaveisCompartilhadas::$franqueadaID);


        if (isset($parametros[ConstanteParametros::CHAVE_SITUACAO]) === true && empty($parametros[ConstanteParametros::CHAVE_SITUACAO]) === false) {
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
                                                    $expr->eq('titrec.id', 't.id'),
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
                                                    $conditions[] = $queryBuilder->expr()->in("t.situacao", $situacoes);
                                                }
                            
                                                $conditions[] = $queryBuilder->expr()->andX(
                                                    $queryBuilder->expr()->eq('t.situacao', ':situacaoPendente'),
                                                    $queryBuilder->expr()->not(
                                                        $queryBuilder->expr()->exists($subQuery->getDQL())
                                                        )
                                                    );
                                                }
                                                
                                                if (in_array(SituacoesSistema::SITUACAO_LIQUIDADO, $situacoes) === true) {
                                                    $conditions[] = $queryBuilder->expr()->in("t.situacao", $situacoes);
                                                    $conditions[] = $queryBuilder->expr()->exists($subQuery->getDQL());
                                                    
                                                    if (in_array(SituacoesSistema::SITUACAO_VENCIDAS, $situacoes) === true) {
                                                        $conditions[] = $queryBuilder->expr()->andX(
                                                            $queryBuilder->expr()->eq('t.situacao', ':situacaoPendente'),
                                                            $queryBuilder->expr()->lt('t.data_vencimento', ':hoje')
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
                                                $orQuery .= " OR (t.situacao = 'PEN' AND t.data_vencimento < :hoje)";
                                                $queryBuilder->setParameter("hoje", new \DateTime());
                                            }
                            
                                            $queryBuilder->andWhere("t.situacao IN (:situacao) $orQuery");
                                            $queryBuilder->setParameter("situacao", $situacoes);
                                        }//end if
                                    }
 
 
 
 
            // $queryBuilder->andWhere('t.situacao in (:filtro_situacao)')
            // ->setParameter(':filtro_situacao', $parametros[ConstanteParametros::CHAVE_SITUACAO]);
        }
        
        if (isset($parametros[ConstanteParametros::CHAVE_FORMAS_COBRANCA]) === true && empty($parametros[ConstanteParametros::CHAVE_FORMAS_COBRANCA]) === false) {
            // $whereString .= " AND t.forma_cobranca_id in (" . implode(',', $parametros[ConstanteParametros::CHAVE_FORMAS_COBRANCA]) . ") ";
            $queryBuilder->andWhere("t.forma_cobranca in(:formas_cobranca)");
            $queryBuilder->setParameter('formas_cobranca', $parametros[ConstanteParametros::CHAVE_FORMAS_COBRANCA]);
        }
        
        if (isset($parametros[ConstanteParametros::CHAVE_FORMAS_PAGAMENTO]) === true && empty($parametros[ConstanteParametros::CHAVE_FORMAS_PAGAMENTO]) === false) {
            // $whereString .= " AND mc.forma_pagamento_id in (" . implode(',', $parametros[ConstanteParametros::CHAVE_FORMAS_PAGAMENTO]) . ") ";
            $queryBuilder->andWhere("mc.forma_pagamento in(:formas_pagamento)");
            $queryBuilder->setParameter('formas_pagamento', $parametros[ConstanteParametros::CHAVE_FORMAS_PAGAMENTO]);
        }
       
        
        if (isset($parametros[ConstanteParametros::CHAVE_FORMAS_PAGAMENTO]) === false && isset($parametros[ConstanteParametros::CHAVE_FORMAS_COBRANCA]) === false ) {
            if($parametros[ConstanteParametros::CHAVE_FORMA_CARTAO] == '1' && $parametros[ConstanteParametros::CHAVE_FORMA_CHEQUE] == '1'){

                $subQueryBuilder = $queryBuilder->expr()->orX();
    
                $subQueryBuilder->add(
                    $queryBuilder->expr()->andX(
                        't.franqueada = :franqueadaId',
                        'fp.forma_cheque = :formaCheque',
                        'fp.forma_cartao = :formaCartao',
                    )
                );
                $queryBuilder->setParameter(":formaCartao", 1 );
                $queryBuilder->setParameter(":formaCheque", 1 );
                $queryBuilder->setParameter(':franqueadaId', VariaveisCompartilhadas::$franqueadaID);
                $queryBuilder->orWhere($subQueryBuilder);
            }else{  
                if ($parametros[ConstanteParametros::CHAVE_FORMA_CARTAO] == '1') {
                    $queryBuilder->orWhere('fp.forma_cartao = 1');
                    $queryBuilder->andWhere('fp.forma_cheque = 0');
                }
                if ($parametros[ConstanteParametros::CHAVE_FORMA_CHEQUE] == '1') {
                    $queryBuilder->orWhere('fp.forma_cheque = 1');
                    $queryBuilder->andWhere('fp.forma_cartao = 0');
                }
                if($parametros[ConstanteParametros::CHAVE_FORMA_CARTAO] == '0' && $parametros[ConstanteParametros::CHAVE_FORMA_CHEQUE] == '0'){
                    $queryBuilder->andWhere('fp.forma_cheque = 0');
                    $queryBuilder->andWhere('fp.forma_cartao = 0');
                }
            }
        }

 

        if (isset($parametros[ConstanteParametros::CHAVE_PLANO_CONTA]) === true && empty($parametros[ConstanteParametros::CHAVE_PLANO_CONTA]) === false) {
            $subQuery = $this->_em->createQueryBuilder();
            $subQuery->select('icr');
            $subQuery->from(ItemContaReceber::class, 'icr');
            $subQuery->where('icr.conta_receber = cr.id');
            $subQuery->andWhere('icr.plano_conta = :plano_conta');
            $queryBuilder->setParameter('plano_conta', $parametros[ConstanteParametros::CHAVE_PLANO_CONTA]);
            $queryBuilder->andWhere($queryBuilder->expr()->exists($subQuery->getDQL()));
        }


        if ($parametros['data_inicial_vencimento'] !== null || $parametros['data_final_vencimento'] !== null) {
            $dataInicial = $parametros['data_inicial_vencimento'];
            $dataInicial = $dataInicial. ' 00:00:01';
            $dataFinal =  $parametros['data_final_vencimento'];
            $dataFinal = $dataFinal . ' 23:59:59';       
            
            $queryBuilder->andWhere('t.data_vencimento >= :data_inicial_vencimento');
            $queryBuilder->setParameter('data_inicial_vencimento', $dataInicial);
            
            $queryBuilder->andWhere('t.data_vencimento <= :data_final_vencimento');
            $queryBuilder->setParameter('data_final_vencimento', $dataFinal);
        } 

        
        if ($parametros["agrupar_por_parcelas"] === 'S') {
            $queryBuilder->addOrderBy("p.nome_contato", "ASC");
        }
        
        if (isset($parametros[ConstanteParametros::CHAVE_ORDENACAO_COLUNA]) === true && empty($parametros[ConstanteParametros::CHAVE_ORDENACAO_COLUNA]) === false) {
            if ($parametros[ConstanteParametros::CHAVE_ORDENACAO_COLUNA] === 'V') {
                $queryBuilder->addOrderBy("t.data_vencimento", "DESC");
            } else if ($parametros[ConstanteParametros::CHAVE_ORDENACAO_COLUNA] === 'P') {
                $queryBuilder->addOrderBy("mc.data_deposito", "DESC");
                $queryBuilder->addOrderBy("t.data_vencimento", "DESC");
            }
        }

        return $queryBuilder->getQuery()->getResult(\Doctrine\ORM\Query::HYDRATE_ARRAY);
    }

    /**
     * Monta a query para ser executada no relatório
     *
     * @param array $parametros
     *
     * @return string
     */
    public function gerarDadosRelatorioContaReceber($parametros)
    {
        $queryBuilder = $this->createQueryBuilder("tr");
        $queryBuilder->select('tr.id');
        $queryBuilder->join('tr.franqueada', 'f');
        $queryBuilder->join('tr.sacado_pessoa', 'p');
        $queryBuilder->join('tr.forma_recebimento', 'fc');
        $queryBuilder->join('tr.conta_receber', 'cr');
        $queryBuilder->join('tr.conta', 'co');
        $queryBuilder->leftJoin('tr.movimento_conta', 'mc');
        $queryBuilder->leftJoin('mc.forma_pagamento', 'fp');
        $queryBuilder->leftJoin('cr.itemsContaReceber', 'icr');
        $queryBuilder->leftJoin('icr.plano_conta', 'pc');

        $queryBuilder->andWhere('tr.franqueada = :franqueada');
        $queryBuilder->setParameter('franqueada', VariaveisCompartilhadas::$franqueadaID);

        if (is_null($parametros[ConstanteParametros::CHAVE_DATA_VENCIMENTO_INICIAL]) === false) {
            $queryBuilder->andWhere('tr.data_prorrogacao >= :data_vencimento_inicial');
            $queryBuilder->setParameter('data_vencimento_inicial', $parametros[ConstanteParametros::CHAVE_DATA_VENCIMENTO_INICIAL]);
        }

        if (is_null($parametros[ConstanteParametros::CHAVE_DATA_VENCIMENTO_FINAL]) === false) {
            $queryBuilder->andWhere('tr.data_prorrogacao <= :data_vencimento_final');
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
            $queryBuilder->andWhere('tr.valor_original >= :valor_inicial');
            $queryBuilder->setParameter('valor_inicial', $parametros[ConstanteParametros::CHAVE_VALOR_INICIAL]);
        }

        if (is_null($parametros[ConstanteParametros::CHAVE_VALOR_FINAL]) === false) {
            $queryBuilder->andWhere('tr.valor_original <= :valor_final');
            $queryBuilder->setParameter('valor_final', $parametros[ConstanteParametros::CHAVE_VALOR_FINAL]);
        }

        if (is_null($parametros[ConstanteParametros::CHAVE_CONTA]) === false) {
            $queryBuilder->andWhere('co = :conta');
            $queryBuilder->setParameter('conta', $parametros[ConstanteParametros::CHAVE_CONTA]);
        }

        if (is_null($parametros[ConstanteParametros::CHAVE_PLANO_CONTA]) === false) {
            $queryBuilder->andWhere('pc.id = :planoConta');
            $queryBuilder->setParameter('planoConta', $parametros[ConstanteParametros::CHAVE_PLANO_CONTA]);
        }

        if (is_null($parametros[ConstanteParametros::CHAVE_FORMA_COBRANCA]) === false) {
            $queryBuilder->andWhere('fc = :formaCobranca');
            $queryBuilder->setParameter('formaCobranca', $parametros[ConstanteParametros::CHAVE_FORMA_COBRANCA]);
        }

        if (is_null($parametros[ConstanteParametros::CHAVE_FORMA_RECEBIMENTO]) === false) {
            $queryBuilder->andWhere('fp = :formaPagamento');
            $queryBuilder->setParameter('formaPagamento', $parametros[ConstanteParametros::CHAVE_FORMA_RECEBIMENTO]);
        }

        if ((is_null($parametros['somente_recebidas_atraso']) === false) && $parametros['somente_recebidas_atraso'] === 1) {
            if ($parametros['somente_recebidas_atraso'] === 1) {
                $queryBuilder->andWhere('tr.data_prorrogacao < mc.data_contabil');
            }
        }

        if ((is_null($parametros['somente_vencidas']) === false) && $parametros['somente_vencidas'] === 1) {
            $orQuery = '';
            $orQuery = " OR (tr.situacao = 'PEN' AND tr.data_prorrogacao < :hoje)";
            $queryBuilder->andWhere("tr.situacao = 'VEN' $orQuery");

            if (empty($orQuery) === false) {
                $queryBuilder->setParameter("hoje", (new \DateTime())->format('Y-m-d H:i:s'));
            }
        } else if (is_null($parametros[ConstanteParametros::CHAVE_SITUACAO]) === false) {
            $situacao = explode(',', $parametros[ConstanteParametros::CHAVE_SITUACAO]);
            $orQuery  = '';

            if (in_array(SituacoesSistema::SITUACAO_VENCIDAS, $situacao) === true) {
                $orQuery = " OR (tr.situacao = 'PEN' AND tr.data_prorrogacao < :hoje)";
            }

            $queryBuilder->andWhere("tr.situacao IN (:situacao) $orQuery");
            $queryBuilder->setParameter('situacao', implode("', '", $situacao));

            if (empty($orQuery) === false) {
                $queryBuilder->setParameter("hoje", (new \DateTime())->format('Y-m-d H:i:s'));
            }
        }//end if

        // Filtra e substitui a query para passar ao Jasper
        $sql = $queryBuilder->getQuery()->getSQL();
        $sql = preg_replace('/.*WHERE\s(.*)$/', '$1', $sql);

        // Seleciona somente os wheres
        $sql = preg_replace('/t0_/', 'tituloReceber', $sql);
        $sql = preg_replace('/f1_/', 'franqueada', $sql);
        $sql = preg_replace('/p2_/', 'sacadoPessoa', $sql);
        $sql = preg_replace('/f3_/', 'formaRecebimento', $sql);
        $sql = preg_replace('/c4_/', 'contaReceber', $sql);
        $sql = preg_replace('/c5_/', 'conta', $sql);
        $sql = preg_replace('/m6_/', 'movimentoConta', $sql);
        $sql = preg_replace('/f7_/', 'formaPagamento', $sql);
        $sql = preg_replace('/i8_/', 'itemContaReceber', $sql);
        $sql = preg_replace('/p9_/', 'planoConta', $sql);

        // Substituição de parâmetros
        $parameters = $queryBuilder->getParameters();
        foreach ($parameters as $parameter) {
            $param = $parameter->getValue();
            $sql   = preg_replace('/\?/', "'$param'", $sql, 1);
        }

        return $sql;
    }

    /**
     * Busca o registro pela chave primaria
     *
     * @param int $id
     *
     * @return array|NULL
     */
    public function buscarTituloSomente($id)
    {
        $queryBuilder = $this->createQueryBuilder("titulo_receber");
        $queryBuilder->addSelect("sacado_pessoa, conta_receber, contrato, aluno, forma_cobranca");
        $queryBuilder->leftJoin("titulo_receber.aluno", "aluno");
        $queryBuilder->join("titulo_receber.sacado_pessoa", "sacado_pessoa");
        $queryBuilder->join("titulo_receber.conta_receber", "conta_receber");
        $queryBuilder->join("titulo_receber.forma_cobranca", "forma_cobranca");
        $queryBuilder->leftJoin("conta_receber.contrato", "contrato");
        $queryBuilder->where("titulo_receber.id = :id");
        $queryBuilder->setParameter("id", $id);

        return $queryBuilder->getQuery()->getOneOrNullResult(\Doctrine\ORM\Query::HYDRATE_ARRAY);
    }

    /**
     * Busca o pessoas relacionadas a titulos conforme parametros
     *
     * @param array $parametros
     *
     * @return array
     */
    public function buscarPessoas($parametros)
    {
        $query = "SELECT distinct p.* 
                        from    pessoa p, 
                                titulo_receber t 
                    LEFT JOIN aluno a ON a.id = t.aluno_id
                    LEFT JOIN pessoa aluno_pessoa ON a.pessoa_id = aluno_pessoa.id
        WHERE t.franqueada_id = :franqueada
        AND (t.sacado_pessoa_id = p.id OR aluno_pessoa.id = p.id)
        AND LOWER(p.nome_contato) like :nomePessoa";

        $params = [
            "franqueada" => $parametros[ConstanteParametros::CHAVE_FRANQUEADA],
            "nomePessoa" => '%' . strtolower($parametros[ConstanteParametros::CHAVE_NOME_PESSOA]) . '%',
        ];

        $em   = self::getEntityManager();
        $stmt = $em->getConnection()->prepare($query);
        $stmt->execute($params);
        $result = $stmt->fetchAll();
        return $result;
    }

    public function buscarDetalhesPorId($id) {
        $queryBuilder = $this->montaQueryBase();
        $queryBuilder->where("tr.id = :id");
        $queryBuilder->setParameter("id", $id);

        return $queryBuilder->getQuery()->getResult(\Doctrine\ORM\Query::HYDRATE_ARRAY);
    }
}
