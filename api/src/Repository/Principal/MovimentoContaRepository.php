<?php

namespace App\Repository\Principal;

use App\Entity\Principal\MovimentoConta;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\Query;
use Symfony\Bridge\Doctrine\RegistryInterface;
use App\Helper\ConstanteParametros;
use App\Helper\VariaveisCompartilhadas;
use Doctrine\Persistence\ManagerRegistry;

/**
 *
 * @method MovimentoConta|null find($id, $lockMode = null, $lockVersion = null)
 * @method MovimentoConta|null findOneBy(array $criteria, array $orderBy = null)
 * @method MovimentoConta[]    findAll()
 * @method MovimentoConta[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class MovimentoContaRepository extends ServiceEntityRepository
{

    /**
     * @var ManagerRegistry
     */
    private ManagerRegistry $managerRegistry;

    public function __construct(RegistryInterface $registry, ManagerRegistry $managerRegistry)
    {
        parent::__construct($registry, MovimentoConta::class);
        $this->managerRegistry = $managerRegistry;
    }

    /**
     * Constrói a query
     *
     * @return \Doctrine\ORM\QueryBuilder
     */
    public function montaQuery ()
    {
        $queryBuilder = $this->createQueryBuilder("movimento");
        $queryBuilder->addSelect('conta');
        $queryBuilder->addSelect('tipoMovimentoConta');
        $queryBuilder->addSelect('formaPagamento');
        $queryBuilder->addSelect('usuario');
        $queryBuilder->addSelect('tituloPagar');
        $queryBuilder->addSelect('tituloPagarCheque');
        $queryBuilder->addSelect('tituloPagarFavorecidoPessoa');
        $queryBuilder->addSelect('tituloReceber');
        $queryBuilder->addSelect('tituloReceberCheques');
        $queryBuilder->addSelect('tituloReceberCartao');
        $queryBuilder->addSelect('tituloReceberSacadoPessoa');
        $queryBuilder->addSelect('aluno');
        $queryBuilder->addSelect('pessoaAluno');

        $queryBuilder->join('movimento.conta', 'conta');
        $queryBuilder->join('movimento.tipo_movimento_conta', 'tipoMovimentoConta');
        $queryBuilder->join('movimento.forma_pagamento', 'formaPagamento');
        $queryBuilder->join('movimento.usuario', 'usuario');

        $queryBuilder->leftJoin('movimento.titulo_pagar', 'tituloPagar');
        $queryBuilder->leftJoin('tituloPagar.cheque', 'tituloPagarCheque');
        $queryBuilder->leftJoin('tituloPagar.favorecido_pessoa', 'tituloPagarFavorecidoPessoa');
        $queryBuilder->leftJoin('movimento.titulo_receber', 'tituloReceber');
        $queryBuilder->leftJoin('tituloReceber.cheques', 'tituloReceberCheques');
        $queryBuilder->leftJoin('tituloReceber.sacado_pessoa', 'tituloReceberSacadoPessoa');
        $queryBuilder->leftJoin('tituloReceber.aluno', 'aluno');
        $queryBuilder->leftJoin('aluno.pessoa', 'pessoaAluno');
        $queryBuilder->leftJoin('tituloReceber.transacoes_cartao', 'tituloReceberCartao');
        $queryBuilder->leftJoin('tituloReceber.transferencias_bancarias', 'tituloReceberTransferencia');

        return $queryBuilder;
    }

    /**
     * Query para realizar fitlro de franqueada
     *
     * @param \Doctrine\ORM\QueryBuilder $queryBuilder
     */
    private function filtrarFranqueada(&$queryBuilder)
    {
        $queryBuilder->where('movimento.franqueada = :franqueada');
        $queryBuilder->setParameter('franqueada', VariaveisCompartilhadas::$franqueadaID);
    }

    /**
     * Filtra as movimentações da conta por pagina e numero de itens por pagina
     *
     * @param array $parametros Parâmetros usados para filtros
     *
     * @return array Resultados em formato de array
     */
    public function filtrarMovimentoContaPorPagina($parametros)
    {
        $pagina = $parametros[ConstanteParametros::CHAVE_PAGINA];
        $numeroItensPorPagina = 10000;

        $queryBuilder = $this->montaQuery();
        $this->filtrarFranqueada($queryBuilder);

        if (isset($parametros[ConstanteParametros::CHAVE_CONTA]) === true && is_null($parametros[ConstanteParametros::CHAVE_CONTA]) === false) {
            $queryBuilder->andWhere('movimento.conta = :conta');
            $queryBuilder->setParameter('conta', $parametros[ConstanteParametros::CHAVE_CONTA]);
        }
        $queryBuilderTemp = clone $queryBuilder;
        $this->filtrarPeriodoParaSaldoInicial($queryBuilderTemp, $parametros);

        $this->filtrarMes($queryBuilder, $parametros);
        $this->filtrarConciliado($queryBuilder, $parametros);
        $this->filtrarTipo($queryBuilder, $parametros);
        $this->filtrarFormaPagamento($queryBuilder, $parametros);
        // $this->filtrarDatasLancamento($queryBuilder, $parametros);
        $this->filtrarValor($queryBuilder, $parametros);
        $this->filtrarUsuario($queryBuilder, $parametros);
        $this->filtrarOrigem($queryBuilder, $parametros);
        $this->filtrarNumeroLancamento($queryBuilder, $parametros);
        $this->filtrarNumeroChequeCartao($queryBuilder, $parametros);
        $this->filtrarTitulo($queryBuilder, $parametros);
        $this->filtrarCategoria($queryBuilder, $parametros);

        $queryBuilderCalc = clone $queryBuilder;

        $queryBuilderCalc = $queryBuilderCalc->select('DISTINCT  movimento.id ,movimento.operacao, movimento.valor_lancamento');
        // $queryBuilderCalc = $queryBuilderCalc->andWhere("movimento.operacao = :operacaoEntrada");
        // $somaEntradas = $somaEntradas->setParameter('operacaoEntrada', 'C');
        
        $queryBuilderCalc = $queryBuilderCalc->getQuery();
        $dataResults = $queryBuilderCalc->getResult();
        
        $totalEntradas = 0;
        $totalSaidas = 0;
        foreach ($dataResults as $item) {
            if ($item['operacao'] == 'C') {
                $totalEntradas +=  $item['valor_lancamento'];
            }
            if ($item['operacao'] == 'D') {
                $totalSaidas +=  $item['valor_lancamento'];
            }
          
        }

        $totalNaoConciliados = 0;
        // $totalNaoConciliados = $this->somarNaoConciliados($queryBuilder);
//        $saldoInicial         = $this->getSaldoInicial($queryBuilderTemp);
        $saldoInicial         = $this->getSaldoInicialTempoReal($parametros);

        if (isset($totalEntradas) === false && is_null($totalEntradas) === true) {
            $totalEntradas = 0;
        }

        if (isset($totalSaidas) === false && is_null($totalSaidas) === true) {
            $totalSaidas = 0;
        }
        if (isset($saldoInicial) === false && is_null($saldoInicial) === true) {
            $saldoInicial = 0;
        }

        if (isset($totalNaoConciliados) === false && is_null($totalNaoConciliados) === true) {
            $totalNaoConciliados = 0;
        }
 
        return [
            ConstanteParametros::CHAVE_ITENS                 => \App\Helper\FunctionHelper::montaPaginatorPaginacao($queryBuilder, $pagina, $numeroItensPorPagina),
            ConstanteParametros::CHAVE_TOTAL_ENTRADAS        => $totalEntradas,
            ConstanteParametros::CHAVE_TOTAL_SAIDAS          => $totalSaidas,
            ConstanteParametros::CHAVE_SALDO_INICIAL         => $saldoInicial,
            ConstanteParametros::CHAVE_TOTAL_NAO_CONCILIADOS => $totalNaoConciliados,
        ];
    }

    /**
     * Filtra as movimentações da conta por pagina e numero de itens por pagina
     *
     * @param string $mensagem Mensagem de erro que retorna pro front
     * @param array $parametros Parâmetros usados para filtros
     *
     * @return array Resultados em formato de array
     */
    public function buscarAlunoFornecedorComMovimento(&$mensagem, $parametros)
    {

        $queryBuilder = $this->createQueryBuilder("movimento");

        $queryBuilder->addSelect(
            "
            (CASE WHEN tituloPagarFavorecidoPessoa.nome_contato IS NOT NULL
                    THEN tituloPagarFavorecidoPessoa.nome_contato
                WHEN pessoaAluno.nome_contato IS NOT NULL
                    THEN pessoaAluno.nome_contato
                WHEN pessoaSacado.nome_contato IS NOT NULL
                    THEN pessoaSacado.nome_contato
                ELSE ''
            END) AS nome_contato"
        );

        $queryBuilder->addSelect(
            "(CASE WHEN tituloPagarFavorecidoPessoa.id IS NOT NULL 
                    THEN tituloPagarFavorecidoPessoa.id
                    WHEN pessoaAluno.id IS NOT NULL 
                    THEN pessoaAluno.id
                    WHEN pessoaSacado.id IS NOT NULL 
                    THEN pessoaSacado.id
                    ELSE 0 END) AS id"
        );

        $queryBuilder->join('movimento.tipo_movimento_conta', 'tipoMovimentoConta');

        $queryBuilder->leftJoin('movimento.titulo_pagar', 'tituloPagar');
        $queryBuilder->leftJoin('tituloPagar.favorecido_pessoa', 'tituloPagarFavorecidoPessoa');
        $queryBuilder->leftJoin('movimento.titulo_receber', 'tituloReceber');
        $queryBuilder->leftJoin('tituloReceber.aluno', 'aluno');
        $queryBuilder->leftJoin('aluno.pessoa', 'pessoaAluno');
        $queryBuilder->leftJoin('tituloReceber.sacado_pessoa', 'pessoaSacado');

        $this->filtrarFranqueada($queryBuilder);
        $resultado = \App\Helper\FunctionHelper::retornaArrayNull($queryBuilder);

        if ($resultado === null || count($resultado) === 0) {
            return [];
        }

        $retorno    = [];
        $pessoasIds = [];

        foreach ($resultado as $res) {
            if (isset($res['nome_contato']) === true && empty($res['nome_contato']) === false) {
                $resultadoPossuiNome = strpos(strtolower($res['nome_contato']), strtolower($parametros[ConstanteParametros::CHAVE_NOME_CONTATO])) !== false;
                if ($resultadoPossuiNome === true && in_array($res["id"], $pessoasIds) === false) {
                    $pessoasIds[] = $res["id"];
                    $retorno[]    = [
                        "nome_contato" => $res["nome_contato"],
                        "id"           => $res["id"],
                    ];
                }
            }
        }

        return $retorno;
    }


   /**
     * Filtra as movimentações da conta por Titulo
     *
     * @param string $mensagem Mensagem de erro que retorna pro front
     * @param int $parametros Parâmetros usados para filtros
     *
     * @return array Resultados em formato de array
     */
    public function buscarMovimentoPorTitulo(&$mensagem, $tituloId)
    {

        $queryBuilder = $this->createQueryBuilder("movimento");

        $queryBuilder->where('movimento.titulo_receber = :tituloId');
        $queryBuilder->setParameter('tituloId', $tituloId);
        
        return $queryBuilder->getQuery()->getResult();
    }

    /**
     * Soma todas as entradas, respeitando os filtros
     *
     * @param \Doctrine\ORM\QueryBuilder $queryBuilder
     *
     * @return string
     */
    private function somarEntradas ($queryBuilder)
    {
        $somaEntradas = clone $queryBuilder;
        $somaEntradas = $somaEntradas->select('DISTINCT  movimento.id , movimento.valor_lancamento');
        $somaEntradas = $somaEntradas->andWhere("movimento.operacao = :operacaoEntrada");
        $somaEntradas = $somaEntradas->setParameter('operacaoEntrada', 'C');
        
        $somaEntradas = $somaEntradas->getQuery();
        $somaEntradas = $somaEntradas->getResult();
        
        $somaTotal = 0;
        foreach ($somaEntradas as $somaEntrada) {
            $somaTotal += $somaEntrada->getValorLancamento();
        }

        return $somaTotal;
    }

    /**
     * Soma todas as saídas, respeitando os filtros
     *
     * @param \Doctrine\ORM\QueryBuilder $queryBuilder
     *
     * @return string
     */
    private function somarSaidas ($queryBuilder)
    {
        $somaSaidas = clone $queryBuilder;
        $somaSaidas = $somaSaidas->select('DISTINCT  movimento.id , movimento.valor_lancamento');
        $somaSaidas = $somaSaidas->andWhere("movimento.operacao = :operacaoSaida");
        $somaSaidas = $somaSaidas->setParameter('operacaoSaida', 'D');
        
        $somaSaidas = $somaSaidas->getQuery();
        $somaSaidas = $somaSaidas->getResult();
        
        $somaTotal = 0;
        foreach ($somaSaidas as $somaSaida) {
            $somaTotal += $somaSaida['valor_lancamento'];
        }

        return $somaTotal;

    }

    /**
     * Soma todas as saídas, respeitando os filtros
     *
     * @param \Doctrine\ORM\QueryBuilder $queryBuilder
     *
     * @return string
     */
    private function getSaldoInicial ($queryBuilder)
    {
        $saldoQb = clone $queryBuilder;
        $saldoQb= $saldoQb->select('movimento.valor_lancamento,movimento.valor_saldo_final_conta');
        // $somaSaidas = $somaSaidas->andWhere("movimento.operacao = :operacaoSaida");
        // $somaSaidas = $somaSaidas->setParameter('operacaoSaida', 'D');
        $saldoQb = $saldoQb->orderBy('movimento.id','desc');
        $saldoQb = $saldoQb->setMaxResults(1);
        $saldoQb = $saldoQb->getQuery();
        $dados = $saldoQb->getOneOrNullResult(Query::HYDRATE_ARRAY);
        if (isset($dados["valor_lancamento"]) && isset($dados["valor_saldo_final_conta"])){
            return $dados["valor_saldo_final_conta"];
        }
        return 0;
    }

        /**
     * Soma todas as saídas, respeitando os filtros
     *
     * @param \Doctrine\ORM\QueryBuilder $queryBuilder
     *
     * @return string
     */
    private function getSaldoInicialTempoReal ($parametros)
    {

        $franqueada = $parametros[ConstanteParametros::CHAVE_FRANQUEADA];
        $conta = $parametros[ConstanteParametros::CHAVE_CONTA];

        $mes = date('m');
        $ano = date('Y');
     
        if(isset($parametros['mes']) && !is_null($parametros['mes'])){
            $mes = (int) $parametros['mes']+1;
            $mes = $mes < 10 ? '0'.$mes : $mes;
        }
        if(isset($parametros['ano']) && !is_null($parametros['ano'])){
            $ano = (int) $parametros['ano'];
        }
        $data = $ano.'-'.$mes.'-01';
 
        $sql = "SELECT DISTINCT  mc.id ,
                    (CASE WHEN mc.operacao = 'C' THEN mc.valor_lancamento ELSE 0 END) AS entrada,
                    (CASE WHEN mc.operacao = 'D' THEN mc.valor_lancamento ELSE 0 END) AS saida
                        -- SUM(CASE WHEN mc.operacao = 'C' THEN mc.valor_lancamento ELSE -mc.valor_lancamento END) AS saldo_inicial
                    FROM
                        movimento_conta mc
                    INNER JOIN conta c1_ ON	mc.conta_id = c1_.id
                    INNER JOIN tipo_movimento_conta t2_ ON mc.tipo_movimento_conta_id = t2_.id
                    INNER JOIN forma_pagamento f3_ ON mc.forma_pagamento_id = f3_.id
                    INNER JOIN usuario u4_ ON mc.usuario_id = u4_.id
                    LEFT JOIN titulo_pagar t5_ ON mc.titulo_pagar_id = t5_.id
                    LEFT JOIN cheque c6_ ON	t5_.cheque_id = c6_.id
                    LEFT JOIN pessoa p7_ ON	t5_.favorecido_pessoa_id = p7_.id
                    LEFT JOIN titulo_receber t8_ ON	mc.titulo_receber_id = t8_.id
                    LEFT JOIN cheque c9_ ON	t8_.id = c9_.titulo_receber_id
                    LEFT JOIN pessoa p10_ ON t8_.sacado_pessoa_id = p10_.id
                    LEFT JOIN aluno a11_ ON	t8_.aluno_id = a11_.id
                    LEFT JOIN pessoa p12_ ON a11_.pessoa_id = p12_.id
                    LEFT JOIN transacao_cartao t13_ ON t8_.id = t13_.titulo_receber_id
                    LEFT JOIN transferencia_bancaria t14_ ON t8_.id = t14_.titulo_receber_id
                    WHERE
                        mc.franqueada_id = {$franqueada} 

                        AND mc.data_deposito > '1900-06-01T00:00:01-03:00'
                        AND mc.data_deposito < '{$data}' 
                        AND mc.operacao in ('D', 'C')";
    
    if($conta != '') {
        $sql = $sql . " AND mc.conta_id = {$conta} ";
    }
       
    $dados = $this->managerRegistry->getConnection()->fetchAllAssociative($sql);
    
    if (empty($dados) === false) {
        $somaTotal = 0;
        $somaTotalEntrada = 0;
        $somaTotalSaida = 0;
        foreach ($dados as $dado) {
            $somaTotalEntrada += $dado['entrada'];
            $somaTotalSaida  += $dado['saida'];
        }

        $somaTotal = $somaTotalEntrada - $somaTotalSaida;

        return $somaTotal;
    }

    return 0;

}

    /**
     * Soma todas os conciliados, respeitando os filtros
     *
     * @param \Doctrine\ORM\QueryBuilder $queryBuilder
     *
     * @return string
     */
    private function somarNaoConciliados ($queryBuilder)
    {
        $somaConciliados = clone $queryBuilder;
        $somaConciliados = $somaConciliados->select('SUM(CASE WHEN movimento.operacao = :naoConciliadoOperacao THEN movimento.valor_lancamento * -1 ELSE movimento.valor_lancamento END)');
        $somaConciliados = $somaConciliados->andWhere("movimento.conciliado = :naoConciliado");
        $somaConciliados = $somaConciliados->setParameter('naoConciliado', 'N');
        $somaConciliados = $somaConciliados->setParameter('naoConciliadoOperacao', 'D');
        $somaConciliados = $somaConciliados->getQuery();
        $somaConciliados = $somaConciliados->getOneOrNullResult();

        return array_values($somaConciliados)[0];
    }

    /**
     * Filtra as movimentações por mês
     *
     * @param \Doctrine\ORM\QueryBuilder $queryBuilder
     * @param array $parametros Parâmetros usados para filtros
     */
    private function filtrarMes (&$queryBuilder, $parametros)
    {

        $anoInicial = (new \DateTime())->format('Y');
        $anoFinal = (new \DateTime())->format('Y');
        $mesInicial = (new \DateTime())->format('m');
        $mesFinal = (new \DateTime())->format('m');
        
        if(isset($parametros[ConstanteParametros::CHAVE_FILTRO_ANO])){
            $ano = $parametros[ConstanteParametros::CHAVE_FILTRO_ANO];
            $anoInicial = $ano;
            $anoFinal = $ano;
            //filtra ano
        }

        if(isset($parametros[ConstanteParametros::CHAVE_FILTRO_MES])){
            $mes = $parametros[ConstanteParametros::CHAVE_FILTRO_MES];
            
            if ($mes != null && $mes!= '') {
                $mesInicial = intval($mes) + 1;
                $mesFinal = intval($mes) + 1;
    
            }
        }

      

            //datas no formato mes ano
            $firstDayOfMonth = (new \DateTime(date("$anoInicial-$mesInicial-01 00:00:00")))->format('c');
            $lastDayOfMonth  = (new \DateTime(date("$anoFinal-$mesFinal-01 23:59:59")))->format('Y-m-t\TH:i:sP');


             //datas no formato dia mes ano
            if(isset($parametros[ConstanteParametros::CHAVE_FILTRO_DATA_LANCAMENTO_INICIO])){                
                \App\Helper\FunctionHelper::formataCampoDateTimeJS($parametros[ConstanteParametros::CHAVE_FILTRO_DATA_LANCAMENTO_INICIO], $dataInicial);
                if (false !== $dataInicial) {
                    $firstDayOfMonth = $dataInicial->setTime(0, 0, 0);                    
                }
            }
            if(isset($parametros[ConstanteParametros::CHAVE_FILTRO_DATA_LANCAMENTO_FIM])){
                \App\Helper\FunctionHelper::formataCampoDateTimeJS($parametros[ConstanteParametros::CHAVE_FILTRO_DATA_LANCAMENTO_FIM], $dataFinal);
                if (false !== $dataFinal) {
                    $lastDayOfMonth = $dataFinal->setTime(23, 59, 59);                    
                }

            }

            
            $queryBuilder->andWhere("movimento.data_contabil >= :data_inicial");
            $queryBuilder->andWhere("movimento.data_contabil <= :data_final");
            $queryBuilder->setParameter("data_inicial", $firstDayOfMonth);
            $queryBuilder->setParameter("data_final", $lastDayOfMonth);

        // $mes = $parametros[ConstanteParametros::CHAVE_FILTRO_MES];

        // if ((is_null($mes) === false) && ((empty($mes) === false) || ($mes === '0'))) {
        //     if ((isset($parametros[ConstanteParametros::CHAVE_FILTRO_ANO]) === true) && (empty($parametros[ConstanteParametros::CHAVE_FILTRO_ANO]) === false)) {
        //         $ano = $parametros[ConstanteParametros::CHAVE_FILTRO_ANO];
        //     } else {
        //         $ano = (new \DateTime())->format('Y');
        //     }

        //     $mes = intval($mes) + 1;
        //     $firstDayOfMonth = (new \DateTime(date("$ano-$mes-01 00:00:01")))->format('c');
        //     $lastDayOfMonth  = (new \DateTime(date("$ano-$mes-01 23:59:59")))->format('Y-m-t\TH:i:sP');


        //     $queryBuilder->andWhere("movimento.data_deposito >= :mesInicial");
        //     $queryBuilder->setParameter("mesInicial", $firstDayOfMonth);

        //     $queryBuilder->andWhere("movimento.data_deposito <= :mesFinal");
        //     $queryBuilder->setParameter("mesFinal", $lastDayOfMonth);
        // } else if (isset($parametros[ConstanteParametros::CHAVE_CONTA]) === false || is_null($parametros[ConstanteParametros::CHAVE_CONTA]) === true) {
        //     // Se for pra pegar de todas as contas, pega apenas o ultimo mês
        //     $data30diasAtras = new \DateTime();
        //     $data30diasAtras->sub(new \DateInterval('P30D'));
        //     $data30diasAtras->setTime(0, 0, 0);
        //     $dataFormatada = $data30diasAtras->format('Y-m-d\TH:i:sP');

        //     $queryBuilder->andWhere("movimento.data_deposito >= :dataInicial");
        //     $queryBuilder->setParameter("dataInicial", $dataFormatada);
        // }//end if
    }

   /**
     * Filtra as movimentações por mês
     *
     * @param \Doctrine\ORM\QueryBuilder $queryBuilder
     * @param array $parametros Parâmetros usados para filtros
     */
    private function filtrarPeriodoParaSaldoInicial (&$queryBuilder, $parametros)
    {
        $mes = $parametros[ConstanteParametros::CHAVE_FILTRO_MES];

        if ((is_null($mes) === false) && ((empty($mes) === false) || ($mes === '0'))) {
            if ((isset($parametros[ConstanteParametros::CHAVE_FILTRO_ANO]) === true) && (empty($parametros[ConstanteParametros::CHAVE_FILTRO_ANO]) === false)) {
                $ano = $parametros[ConstanteParametros::CHAVE_FILTRO_ANO];
            } else {
                $ano = (new \DateTime())->format('Y');
            }

            $mes = intval($mes) ;
            $firstDayOfMonth = (new \DateTime(date("1990-01-01 00:00:01")))->format('c');
            // $firstDayOfMonth = (new \DateTime(date("$ano-$mes-01 00:00:01")))->format('c');
            $lastDayOfMonth  = (new \DateTime(date("$ano-$mes-01 23:59:59")))->format('Y-m-t\TH:i:sP');


            $queryBuilder->andWhere("movimento.data_deposito >= :mesInicial");
            $queryBuilder->setParameter("mesInicial", $firstDayOfMonth);

            $queryBuilder->andWhere("movimento.data_deposito <= :mesFinal");
            $queryBuilder->setParameter("mesFinal", $lastDayOfMonth);
        } else if (isset($parametros[ConstanteParametros::CHAVE_CONTA]) === false || is_null($parametros[ConstanteParametros::CHAVE_CONTA]) === true) {
            // Se for pra pegar de todas as contas, pega apenas o ultimo mês
            $data30diasAtras = new \DateTime();
            $data30diasAtras->sub(new \DateInterval('P30D'));
            $data30diasAtras->setTime(0, 0, 0);
            $dataFormatada = $data30diasAtras->format('Y-m-d\TH:i:sP');

            $queryBuilder->andWhere("movimento.data_deposito >= :dataInicial");
            $queryBuilder->setParameter("dataInicial", $dataFormatada);
        }//end if
    }

    /**
     * Filtra as movimentações por conciliado
     *
     * @param \Doctrine\ORM\QueryBuilder $queryBuilder
     * @param array $parametros Parâmetros usados para filtros
     */
    private function filtrarConciliado (&$queryBuilder, $parametros)
    {
        $conciliado = $parametros[ConstanteParametros::CHAVE_FILTRO_CONCILIADO];

        if ((is_null($conciliado) === false) && (empty($conciliado) === false)) {
            $queryBuilder->andWhere("movimento.conciliado IN (:conciliado)");
            $queryBuilder->setParameter("conciliado", $conciliado);
        }
    }

    /**
     * Filtra as movimentações por tipo
     *
     * @param \Doctrine\ORM\QueryBuilder $queryBuilder
     * @param array $parametros Parâmetros usados para filtros
     */
    private function filtrarTipo (&$queryBuilder, $parametros)
    {
        $operacao = $parametros[ConstanteParametros::CHAVE_FILTRO_TIPO];

        if ((is_null($operacao) === false) && (empty($operacao) === false)) {
            $queryBuilder->andWhere("movimento.operacao IN (:operacao)");
            $queryBuilder->setParameter("operacao", $operacao);
        }
    }

    /**
     * Filtra as movimentações por Forma de Pagamento
     *
     * @param \Doctrine\ORM\QueryBuilder $queryBuilder
     * @param array $parametros Parâmetros usados para filtros
     */
    private function filtrarFormaPagamento (&$queryBuilder, $parametros)
    {
        $formaPagamento = $parametros[ConstanteParametros::CHAVE_FILTRO_FORMA_PAGAMENTO];

        if ((is_null($formaPagamento) === false) && (empty($formaPagamento) === false)) {
            $queryBuilder->andWhere("movimento.forma_pagamento = :formaPagamento");
            $queryBuilder->setParameter("formaPagamento", $formaPagamento);
        }
    }

    /**
     * Filtra as movimentações pela data do lançamento
     *
     * @param \Doctrine\ORM\QueryBuilder $queryBuilder
     * @param array $parametros Parâmetros usados para filtros
     */
    protected function filtrarDatasLancamento (&$queryBuilder, $parametros)
    {
        $dataInicial = null;
        $dataFinal   = null;

        if ((is_null($parametros[ConstanteParametros::CHAVE_FILTRO_DATA_LANCAMENTO_INICIO]) === false) && (empty($parametros[ConstanteParametros::CHAVE_FILTRO_DATA_LANCAMENTO_INICIO]) === false)) {
            \App\Helper\FunctionHelper::formataCampoDateTimeJS($parametros[ConstanteParametros::CHAVE_FILTRO_DATA_LANCAMENTO_INICIO], $dataInicial);
            if (false !== $dataInicial) {
                $dataInicial = $dataInicial->setTime(0, 0);
                $queryBuilder->andWhere("movimento.data_contabil >= :data_inicial");
                $queryBuilder->setParameter("data_inicial", $dataInicial);
            }
        }

        if ((is_null($parametros[ConstanteParametros::CHAVE_FILTRO_DATA_LANCAMENTO_FIM]) === false) && (empty($parametros[ConstanteParametros::CHAVE_FILTRO_DATA_LANCAMENTO_FIM]) === false)) {
            \App\Helper\FunctionHelper::formataCampoDateTimeJS($parametros[ConstanteParametros::CHAVE_FILTRO_DATA_LANCAMENTO_FIM], $dataFinal);
            if (false !== $dataFinal) {
                $dataFinal = $dataFinal->setTime(23, 59, 59);
                $queryBuilder->andWhere("movimento.data_contabil <= :data_final");
                $queryBuilder->setParameter("data_final", $dataFinal->format('Y-m-d\TH:i:sP'));
            }
        }
    }

    /**
     * Filtra as movimentações pelo valor do lançamento
     *
     * @param \Doctrine\ORM\QueryBuilder $queryBuilder
     * @param array $parametros Parâmetros usados para filtros
     */
    protected function filtrarValor (&$queryBuilder, $parametros)
    {
        if ((isset($parametros[ConstanteParametros::CHAVE_FILTRO_VALOR_LANCAMENTO_DE]) === true) && (is_null($parametros[ConstanteParametros::CHAVE_FILTRO_VALOR_LANCAMENTO_DE]) === false) && (empty($parametros[ConstanteParametros::CHAVE_FILTRO_VALOR_LANCAMENTO_DE]) === false) && ($parametros[ConstanteParametros::CHAVE_FILTRO_VALOR_LANCAMENTO_DE] !== '0')) {
            $queryBuilder->andWhere("movimento.valor_lancamento >= :valor_inicial");
            $queryBuilder->setParameter("valor_inicial", $parametros[ConstanteParametros::CHAVE_FILTRO_VALOR_LANCAMENTO_DE]);
        }

        if ((isset($parametros[ConstanteParametros::CHAVE_FILTRO_VALOR_LANCAMENTO_ATE]) === true) && (is_null($parametros[ConstanteParametros::CHAVE_FILTRO_VALOR_LANCAMENTO_ATE]) === false) && (empty($parametros[ConstanteParametros::CHAVE_FILTRO_VALOR_LANCAMENTO_ATE]) === false) && ($parametros[ConstanteParametros::CHAVE_FILTRO_VALOR_LANCAMENTO_ATE] !== '0')) {
            $queryBuilder->andWhere("movimento.valor_lancamento <= :valor_final");
            $queryBuilder->setParameter("valor_final", $parametros[ConstanteParametros::CHAVE_FILTRO_VALOR_LANCAMENTO_ATE]);
        }
    }

    /**
     * Filtra as movimentações por usuário
     *
     * @param \Doctrine\ORM\QueryBuilder $queryBuilder
     * @param array $parametros Parâmetros usados para filtros
     */
    private function filtrarUsuario (&$queryBuilder, $parametros)
    {
        $usuario = $parametros[ConstanteParametros::CHAVE_USUARIO];

        if ((is_null($usuario) === false) && (empty($usuario) === false)) {
            $queryBuilder->andWhere("usuario = :usuario");
            $queryBuilder->setParameter("usuario", $usuario);
        }
    }

    /**
     * Filtra as movimentações por origem
     *
     * @param \Doctrine\ORM\QueryBuilder $queryBuilder
     * @param array $parametros Parâmetros usados para filtros
     */
    private function filtrarOrigem (&$queryBuilder, $parametros)
    {
        $origem = $parametros[ConstanteParametros::CHAVE_FILTRO_ORIGEM];

        if ((is_null($origem) === false) && (empty($origem) === false)) {
            $queryBuilder->andWhere(
                $queryBuilder->expr()->orX(
                    $queryBuilder->expr()->eq("tituloPagar.favorecido_pessoa", $origem),
                    $queryBuilder->expr()->eq("tituloReceber.sacado_pessoa", $origem),
                    $queryBuilder->expr()->eq("pessoaAluno", $origem)
                )
            );
        }
    }

    /**
     * Filtra as movimentações por número do lançamento
     *
     * @param \Doctrine\ORM\QueryBuilder $queryBuilder
     * @param array $parametros Parâmetros usados para filtros
     */
    private function filtrarNumeroLancamento (&$queryBuilder, $parametros)
    {
        $numeroLancamento = $parametros[ConstanteParametros::CHAVE_FILTRO_NUMERO_LANCAMENTO];

        if ((is_null($numeroLancamento) === false) && (empty($numeroLancamento) === false)) {
            $queryBuilder->andWhere('movimento = :movimento');
            $queryBuilder->setParameter('movimento', $numeroLancamento);
        }
    }

    /**
     * Filtra as movimentações por número do cheque ou do cartão
     *
     * @param \Doctrine\ORM\QueryBuilder $queryBuilder
     * @param array $parametros Parâmetros usados para filtros
     */
    private function filtrarNumeroChequeCartao (&$queryBuilder, $parametros)
    {
        $numeroChequeCartao = $parametros[ConstanteParametros::CHAVE_FILTRO_NUMERO_CHEQUE_CARTAO];

        if ((is_null($numeroChequeCartao) === false) && (empty($numeroChequeCartao) === false)) {
            $queryBuilder->andWhere("movimento.numero_documento = :numero_cheque_cartao");
            $queryBuilder->setParameter('numero_cheque_cartao', $numeroChequeCartao);
        }
    }

     /**
     * Filtra as movimentações por número do titulo
     *
     * @param \Doctrine\ORM\QueryBuilder $queryBuilder
     * @param array $parametros Parâmetros usados para filtros
     */
    private function filtrarTitulo (&$queryBuilder, $parametros)
    {
        if (isset($parametros[ConstanteParametros::CHAVE_FILTRO_TITULO])) {
            $numero_titulo = $parametros[ConstanteParametros::CHAVE_FILTRO_TITULO];

            if ((is_null($numero_titulo) === false) && (empty($numero_titulo) === false)) {
                $queryBuilder->andWhere("movimento.titulo_receber = :numero_titulo");
                $queryBuilder->setParameter('numero_titulo', $numero_titulo);
            }    
        }
        
    }

    /**
     * Filtra as movimentações por categoria
     *
     * @param \Doctrine\ORM\QueryBuilder $queryBuilder
     * @param array $parametros Parâmetros usados para filtros
     */
    private function filtrarCategoria (&$queryBuilder, $parametros)
    {
        $categoria = $parametros[ConstanteParametros::CHAVE_FILTRO_CATEGORIA];

        if ((is_null($categoria) === false) && (empty($categoria) === false)) {
            $queryBuilder->andWhere('movimento.observacao LIKE :categoria');
            $queryBuilder->setParameter('categoria', "%$categoria%");
        }
    }

    /**
     * Filtra as movimentações por conta
     *
     * @param \Doctrine\ORM\QueryBuilder $queryBuilder
     * @param array $parametros Parâmetros usados para filtros
     *
     * @return \Doctrine\ORM\QueryBuilder
     */
    public function filtraPorConta(&$queryBuilder, $parametros)
    {

        $conta = $parametros[ConstanteParametros::CHAVE_CONTA];

        if ((is_null($conta) === false) && (empty($conta) === false)) {
            $queryBuilder->andWhere('movimento.conta = :conta');
            $queryBuilder->setParameter('conta', $conta);
        }

        return $queryBuilder;
    }

    /**
     * Filtra as movimentações por data de e ate
     *
     * @param \Doctrine\ORM\QueryBuilder $queryBuilder
     * @param array $parametros Parâmetros usados para filtros
     *
     * @return \Doctrine\ORM\QueryBuilder
     */
    public function filtraPorDataContabil (&$queryBuilder, $parametros)
    {
        $dataInicial = null;
        $dataFinal   = null;

        $dataInicial = $parametros[ConstanteParametros::CHAVE_DATA_INICIAL];
        $dataFinal   = $parametros[ConstanteParametros::CHAVE_DATA_FINAL];

        if ((is_null($dataInicial) === false) && (empty($dataInicial) === false)) {
            $queryBuilder->andWhere("movimento.data_contabil >= :dataInicial");
            $queryBuilder->setParameter("dataInicial", $dataInicial);
        }

        if ((is_null($dataFinal) === false) && (empty($dataFinal) === false)) {
            $queryBuilder->andWhere("movimento.data_contabil <= :dataFinal");
            $queryBuilder->setParameter("dataFinal", $dataFinal);
        }

        return $queryBuilder;
    }

    /**
     * Monta dados do relatorio de balancete financeiro
     *
     * @param string $mensagemErro
     * @param array $parametros
     *
     * @return array|NULL
     */
    public function prepararDadosRelatorioDeBalanceteFinanceiro ($parametros)
    {

        $queryBuilder = $this->createQueryBuilder("movimento")
            ->select([
                'c.descricao',
                "date_format(movimento.data_movimento, '%Y-%m-%d') as data_movimento",
                "date_format(movimento.data_contabil, '%Y-%m-%d') as data_contabil",
                "date_format(movimento.data_deposito, '%Y-%m-%d') as data_deposito",
                'movimento.operacao',
                'movimento.valor_lancamento',
                'movimento.valor_titulo',
                'movimento.valor_multa',
                'movimento.valor_juros',
                'movimento.valor_desconto',
                'movimento.valor_diferenca_baixa',
                'movimento.valor_saldo_final_conta',
                'movimento.observacao',
                'movimento.conciliado',
                'movimento.estornado',
                'movimento.numero_documento'
            ])
            ->leftJoin("movimento.conta", "c")
            ->andWhere('movimento.franqueada = :franqueada')
            ->setParameter('franqueada', $parametros[ConstanteParametros::CHAVE_FRANQUEADA]);

            if(isset($parametros[ConstanteParametros::CHAVE_CONTA])) {
                $queryBuilder->andWhere("c in (:conta)");
                $queryBuilder->setParameter("conta", $parametros[ConstanteParametros::CHAVE_CONTA]);
            }
    
            if(isset($parametros[ConstanteParametros::CHAVE_DATA_FINAL])) {
                $dataInicial = strtotime(str_replace("/", "-",$parametros[ConstanteParametros::CHAVE_DATA_INICIAL] . " 00:00:00"));
                $dataInicial = date('Y-m-d H:i:s', $dataInicial);
                $queryBuilder->andWhere("movimento.data_contabil >= :data_inicial");
                $queryBuilder->setParameter('data_inicial', $dataInicial);
            }
    
            if(isset($parametros[ConstanteParametros::CHAVE_DATA_FINAL])) {
                $dataFinal = strtotime(str_replace("/", "-",$parametros[ConstanteParametros::CHAVE_DATA_FINAL] . " 23:59:59"));
                $dataFinal = date('Y-m-d H:i:s', $dataFinal);
                $queryBuilder->andWhere("movimento.data_contabil <= :data_final");
                $queryBuilder->setParameter('data_final', $dataFinal);
            }

        return $queryBuilder->getQuery()->getResult();
    }

    /**
     * Monta dados do relatorio de balancete financeiro
     *
     * @param array $parametros
     *
     * @return array|NULL
     */
    public function listaMovimentos($conta)
    {
        $parametros = [];

        $parametros[ConstanteParametros::CHAVE_CONTA] = $conta;

        $queryBuilder = $this->createQueryBuilder("movimento");
        $queryBuilder->addSelect("c");
        $queryBuilder->leftJoin("movimento.conta", "c");
        $queryBuilder->orderBy("movimento.id");

        // $this->filtrarFranqueada($queryBuilder);
        $this->filtraPorConta($queryBuilder, $parametros);

        $result = $queryBuilder->getQuery()->getResult(\Doctrine\ORM\Query::HYDRATE_OBJECT);
        return $result;
        // return \App\Helper\FunctionHelper::retornaArrayNull($queryBuilder);

    }


}
