<?php

namespace App\Repository\Principal;

use App\Entity\Principal\Item;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Symfony\Bridge\Doctrine\RegistryInterface;
use App\Helper\ConstanteParametros;
use App\Helper\VariaveisCompartilhadas;
use Symfony\Component\Validator\Constraints\IsNull;

/**
 *
 * @method Item|null find($id, $lockMode = null, $lockVersion = null)
 * @method Item|null findOneBy(array $criteria, array $orderBy = null)
 * @method Item[]    findAll()
 * @method Item[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class ItemRepository extends ServiceEntityRepository
{


    public function __construct(RegistryInterface $registry)
    {
        parent::__construct($registry, Item::class);
    }

    /**
     * Busca os registros da tabela utilizando left join
     *
     * @return \Doctrine\ORM\QueryBuilder
     */
    protected function montaQueryBase($parametros=[])
    {
        $queryBuilder = $this->createQueryBuilder("it");
        $queryBuilder->addSelect("fran, plan, tpi, itemFranqueadas, franqueada");
        $queryBuilder->innerJoin("it.franqueada", "fran");
        $queryBuilder->innerJoin("it.tipo_item", "tpi");
        $queryBuilder->leftJoin("it.plano_conta", "plan");
        $queryBuilder->leftJoin("it.movimentoEstoques", "mve");
        $queryBuilder->leftJoin("it.itemFranqueadas", "itemFranqueadas");
       $queryBuilder->leftJoin("itemFranqueadas.franqueada", "franqueada", "WITH", "franqueada = :filtroFranqueada OR fran.franqueadora = 1");
        // $queryBuilder->leftJoin("itemFranqueadas.franqueada", "franqueada", "WITH", "franqueada = :filtroFranqueada OR franqueada = 1");

        if ((isset($parametros[ConstanteParametros::CHAVE_FILTRO_FRANQUEADA]) === true) && (empty($parametros[ConstanteParametros::CHAVE_FILTRO_FRANQUEADA]) === false)) {
            $queryBuilder->setParameter("filtroFranqueada", $parametros[ConstanteParametros::CHAVE_FILTRO_FRANQUEADA]);
        } else {
            $queryBuilder->setParameter("filtroFranqueada", VariaveisCompartilhadas::$franqueadaID);
        }
        $queryBuilder->distinct();

        return $queryBuilder;
    }
        /**
     * Busca os registros da tabela utilizando left join
     *
     * @return \Doctrine\ORM\QueryBuilder
     */
    protected function montaQueryBaseReduzido($parametros=[])
    {
        $queryBuilder = $this->createQueryBuilder("it");
        $queryBuilder->addSelect("fran, plan, tpi,  franqueada");
        $queryBuilder->innerJoin("it.franqueada", "fran");
        $queryBuilder->innerJoin("it.tipo_item", "tpi");
        $queryBuilder->leftJoin("it.plano_conta", "plan");
        $queryBuilder->leftJoin("it.movimentoEstoques", "mve");
        $queryBuilder->leftJoin("it.franqueada", "franqueada", "WITH", "franqueada = :filtroFranqueada OR fran.franqueadora = 1");

        if ((isset($parametros[ConstanteParametros::CHAVE_FILTRO_FRANQUEADA]) === true) && (empty($parametros[ConstanteParametros::CHAVE_FILTRO_FRANQUEADA]) === false)) {
            $queryBuilder->setParameter("filtroFranqueada", $parametros[ConstanteParametros::CHAVE_FILTRO_FRANQUEADA]);
        } else {
            $queryBuilder->setParameter("filtroFranqueada", VariaveisCompartilhadas::$franqueadaID);
        }
        return $queryBuilder;
    }

    /**
     * Query para realizar fitlro de franqueada
     *
     * @param \Doctrine\ORM\QueryBuilder $queryBuilder
     */
    private function filtrarFranqueada(&$queryBuilder)
    {
        $queryBuilder->andWhere(
            $queryBuilder->expr()->orX(
                $queryBuilder->expr()->eq('fran.franqueadora', true),
                $queryBuilder->expr()->eq('fran.id', ':franqueada')
            )
        );

        $queryBuilder->setParameter('franqueada', VariaveisCompartilhadas::$franqueadaID);
    }

    /**
     * Monta os filtros com os campos de valores decimais
     *
     * @param \Doctrine\ORM\QueryBuilder $queryBuilder
     * @param array $parametros
     */
    protected function montaFiltrosValores(&$queryBuilder, $parametros)
    {
        if (isset($parametros[ConstanteParametros::CHAVE_VALOR_COMPRA_INICIAL]) === true) {
            $numero = $parametros[ConstanteParametros::CHAVE_VALOR_COMPRA_INICIAL];
            if ($numero > 0) {
                $queryBuilder->andWhere("itemFranqueadas.valor_compra >= :vlCompraInicial");
                $queryBuilder->setParameter("vlCompraInicial", $numero);
            }
        }

        if (isset($parametros[ConstanteParametros::CHAVE_VALOR_COMPRA_FINAL]) === true) {
            $numero = $parametros[ConstanteParametros::CHAVE_VALOR_COMPRA_FINAL];
            if ($numero > 0) {
                $queryBuilder->andWhere("itemFranqueadas.valor_compra <= :vlCompraFinal");
                $queryBuilder->setParameter("vlCompraFinal", $numero);
            }
        }

        if (isset($parametros[ConstanteParametros::CHAVE_VALOR_VENDA_INICIAL]) === true) {
            $numero = $parametros[ConstanteParametros::CHAVE_VALOR_VENDA_INICIAL];
            if ($numero > 0) {
                $queryBuilder->andWhere("itemFranqueadas.valor_venda >= :vlVendaInicial");
                $queryBuilder->setParameter("vlVendaInicial", $numero);
            }
        }

        if (isset($parametros[ConstanteParametros::CHAVE_VALOR_VENDA_FINAL]) === true) {
            $numero = $parametros[ConstanteParametros::CHAVE_VALOR_VENDA_FINAL];
            if ($numero > 0) {
                $queryBuilder->andWhere("itemFranqueadas.valor_venda <= :vlVendaFinal");
                $queryBuilder->setParameter("vlVendaFinal", $numero);
            }
        }

        if (isset($parametros[ConstanteParametros::CHAVE_SALDO_ESTOQUE_INICIAL]) === true) {
            $numero = $parametros[ConstanteParametros::CHAVE_SALDO_ESTOQUE_INICIAL];
            if ($numero > 0) {
                $queryBuilder->andWhere("itemFranqueadas.saldo_estoque >= :seInicial");
                $queryBuilder->setParameter("seInicial", $numero);
            }
        }

        if (isset($parametros[ConstanteParametros::CHAVE_SALDO_ESTOQUE_FINAL]) === true) {
            $numero = $parametros[ConstanteParametros::CHAVE_SALDO_ESTOQUE_FINAL];
            if ($numero > 0) {
                $queryBuilder->andWhere("itemFranqueadas.saldo_estoque <= :seFinal");
                $queryBuilder->setParameter("seFinal", $numero);
            }
        }

        if (isset($parametros[ConstanteParametros::CHAVE_ESTOQUE_MINIMO_INICIAL]) === true) {
            $numero = $parametros[ConstanteParametros::CHAVE_ESTOQUE_MINIMO_INICIAL];
            if ($numero > 0) {
                $queryBuilder->andWhere("itemFranqueadas.estoque_minimo >= :emInicial");
                $queryBuilder->setParameter("emInicial", $numero);
            }
        }

        if (isset($parametros[ConstanteParametros::CHAVE_ESTOQUE_MINIMO_FINAL]) === true) {
            $numero = $parametros[ConstanteParametros::CHAVE_ESTOQUE_MINIMO_FINAL];
            if ($numero > 0) {
                $queryBuilder->andWhere("itemFranqueadas.estoque_minimo <= :emFinal");
                $queryBuilder->setParameter("emFinal", $numero);
            }
        }
    }

    /**
     * Monta a query com os filtros passados
     *
     * @param \Doctrine\ORM\QueryBuilder $queryBuilder
     * @param array $parametros
     */
    protected function montaFiltros(&$queryBuilder, $parametros)
    {
        if ((isset($parametros[ConstanteParametros::CHAVE_SITUACAO]) === true) && (empty($parametros[ConstanteParametros::CHAVE_SITUACAO]) === false)) {
            $queryBuilder->andWhere("it.situacao IN (:situacaoItem)");
            $queryBuilder->setParameter("situacaoItem", $parametros[ConstanteParametros::CHAVE_SITUACAO]);
        }

        if ((isset($parametros[ConstanteParametros::CHAVE_TIPO_ITEM]) === true) && (empty($parametros[ConstanteParametros::CHAVE_TIPO_ITEM]) === false)) {
            $queryBuilder->andWhere("tpi.tipo IN (:tipoItem)");
            $queryBuilder->setParameter("tipoItem", $parametros[ConstanteParametros::CHAVE_TIPO_ITEM]);
        }

        if ((isset($parametros[ConstanteParametros::CHAVE_DESCRICAO]) === true) && (empty($parametros[ConstanteParametros::CHAVE_DESCRICAO]) === false)) {
            $queryBuilder->andWhere("UPPER(it.descricao) LIKE :descricao");
            $queryBuilder->setParameter("descricao", "%" . strtoupper($parametros[ConstanteParametros::CHAVE_DESCRICAO]) . "%");
        }

        if ((isset($parametros[ConstanteParametros::CHAVE_UNIDADE_MEDIDA]) === true) && (empty($parametros[ConstanteParametros::CHAVE_UNIDADE_MEDIDA]) === false)) {
            $queryBuilder->andWhere("UPPER(it.unidade_medida) LIKE :unidadeMedida");
            $queryBuilder->setParameter("unidadeMedida", "%" . strtoupper($parametros[ConstanteParametros::CHAVE_UNIDADE_MEDIDA]) . "%");
        }


        $this->montaFiltrosValores($queryBuilder, $parametros);
    }

    /**
     * Busca todos os items cadastrados no sistema
     *
     * @param array $parametros Parametros da requisicao
     *
     * @return NULL|\App\Entity\Principal\Item[]
     */
    public function filtrarItemsPorPagina($parametros)
    {
        $numeroItensPorPagina = 999999;
        $opcoes       = [];
        $queryBuilder = $this->montaQueryBase($parametros);
        $this->filtrarFranqueada($queryBuilder);
        $this->montaFiltros($queryBuilder, $parametros);

        if ((isset($parametros[ConstanteParametros::CHAVE_ORDENACAO_COLUNA]) === true)&&(is_null($parametros[ConstanteParametros::CHAVE_ORDENACAO_COLUNA]) === false)) {
            $queryBuilder->orderBy($parametros[ConstanteParametros::CHAVE_ORDENACAO_COLUNA], $parametros[ConstanteParametros::CHAVE_ORDENACAO_SORT]);
            $opcoes[\Knp\Component\Pager\Paginator::SORT_FIELD_PARAMETER_NAME]     = "~";
            $opcoes[\Knp\Component\Pager\Paginator::SORT_DIRECTION_PARAMETER_NAME] = "~";
        }

 
        return \App\Helper\FunctionHelper::montaPaginatorPaginacao($queryBuilder, $parametros[ConstanteParametros::CHAVE_PAGINA], $numeroItensPorPagina, $opcoes);
    }

       /**
     * Busca todos os items cadastrados no sistema
     *
     * @param array $parametros Parametros da requisicao
     *
     * @return NULL|\App\Entity\Principal\Item[]
     */
    public function filtrarItemsContratoPorPagina($parametros)
    {
        $numeroItensPorPagina = 999999;
        $opcoes       = [];
        $queryBuilder = $this->montaQueryBaseReduzido($parametros);
        $this->filtrarFranqueada($queryBuilder);
        $this->montaFiltros($queryBuilder, $parametros);

        if ((isset($parametros[ConstanteParametros::CHAVE_ORDENACAO_COLUNA]) === true)&&(is_null($parametros[ConstanteParametros::CHAVE_ORDENACAO_COLUNA]) === false)) {
            $queryBuilder->orderBy($parametros[ConstanteParametros::CHAVE_ORDENACAO_COLUNA], $parametros[ConstanteParametros::CHAVE_ORDENACAO_SORT]);
            $opcoes[\Knp\Component\Pager\Paginator::SORT_FIELD_PARAMETER_NAME]     = "~";
            $opcoes[\Knp\Component\Pager\Paginator::SORT_DIRECTION_PARAMETER_NAME] = "~";
        }

        // echo $queryBuilder->getQuery()->getSQL();
        // die;
        return \App\Helper\FunctionHelper::montaPaginatorPaginacao($queryBuilder, $parametros[ConstanteParametros::CHAVE_PAGINA], $numeroItensPorPagina, $opcoes);
    }


    /**
     * Busca itens para tela de contrato
     *
     
     *
     * @return NULL|array
     */
        public function listarSimplesContrato($franqueadaId){
        $sql = "
        select  it.id, it.unidade_medida, it.descricao,plan.id as plano, plan.descricao as plan_descricao, plan.tipo_movimento_nota,tpi.tipo ,itf.id as item_franqueada, itf.saldo_estoque, itf.estoque_minimo, itf.valor_venda
            from item_franqueada as itf 
            LEFT JOIN item as it ON itf.item_id = it.id
            LEFT JOIN tipo_item as tpi on it.tipo_item_id = tpi.id
            LEFT JOIN plano_conta as plan ON it.plano_conta_id = plan.id
            
            where itf.franqueada_id = {$franqueadaId}
            and it.situacao = 'A'  
            and tpi.tipo in ('M','P','V','SN','VP32','VP48','VP64','S','VPA')
        ";

        $em = $this->getEntityManager();
        $result = $em->getConnection()->fetchAllAssociative($sql);
        return $result;

        
    }

    /**
     * Busca item pela ID
     *
     * @param integer $id
     *
     * @return NULL|\App\Entity\Principal\Item
     */
    public function buscarPorId ($id)
    {
        $queryBuilder = $this->montaQueryBase();

        $this->filtrarFranqueada($queryBuilder);
        $queryBuilder->andWhere('it.id = :id');
        $queryBuilder->setParameter('id', $id);

        return \App\Helper\FunctionHelper::retornaArrayNull($queryBuilder, true);
    }

    /**
     * Busca todos os itens que contenham a descrição
     *
     * @param string $descricao Descrição do item a ser buscado
     * @param integer $franqueada
     * @param array $tipoItem
     *
     * @return \App\Entity\Principal\Item[]
     */
    public function buscarPorDescricao ($descricao, $franqueada, $tipoItem)
    {
        $queryBuilder = $this->createQueryBuilder('item');
        $queryBuilder->select('item');
        $queryBuilder->addSelect('planoConta, itemFranqueadas');
        $queryBuilder->join('item.franqueada', 'fran');
        $queryBuilder->leftJoin('item.plano_conta', 'planoConta');
        $queryBuilder->join('item.tipo_item', 'tipoItem');
        $queryBuilder->leftJoin("item.itemFranqueadas", "itemFranqueadas", "WITH", "itemFranqueadas.franqueada = :filtroFranqueada");

        $queryBuilder->where('item.descricao LIKE :descricao');
        $queryBuilder->andWhere(
            $queryBuilder->expr()->orX(
                $queryBuilder->expr()->eq('fran.franqueadora', 1),
                $queryBuilder->expr()->eq('item.franqueada', ':franqueada')
            )
        );

        // Removido o tipo itempara no Relatório puxar os itens que estão relacionados com o Responsável Financeiro
        // if (is_null($tipoItem) === false) {
        //     $queryBuilder->andWhere('tipoItem.tipo IN (:tipoItem)');
        //     $queryBuilder->setParameter('tipoItem', $tipoItem);
        // }

        $queryBuilder->setParameter('descricao', "%$descricao%");
        $queryBuilder->setParameter('franqueada', $franqueada);
        $queryBuilder->setParameter("filtroFranqueada", VariaveisCompartilhadas::$franqueadaID);

        return $queryBuilder->getQuery()->getResult(\Doctrine\ORM\Query::HYDRATE_ARRAY);
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
        $queryBuilder = $this->createQueryBuilder("item");
        $queryBuilder->select('item.id');
        $queryBuilder->innerJoin('item.tipo_item', 'tipo_item');

        $queryBuilder->where('item.franqueada = :franqueada');
        $queryBuilder->setParameter('franqueada', VariaveisCompartilhadas::$franqueadaID);

        if ((isset($parametros[ConstanteParametros::CHAVE_DESCRICAO]) === true)&&(is_null($parametros[ConstanteParametros::CHAVE_DESCRICAO]) === false)) {
            $queryBuilder->andWhere('item.descricao like :descricao');
            $queryBuilder->setParameter('descricao', '%' . $parametros[ConstanteParametros::CHAVE_DESCRICAO] . '%');
        }

        if ((isset($parametros[ConstanteParametros::CHAVE_VALOR_COMPRA_INICIAL]) === true)&&(is_null($parametros[ConstanteParametros::CHAVE_VALOR_COMPRA_INICIAL]) === false)) {
            $queryBuilder->andWhere('item.valor_compra >= :valor_compra_inicial');
            $queryBuilder->setParameter('valor_compra_inicial', $parametros[ConstanteParametros::CHAVE_VALOR_COMPRA_INICIAL]);
        }

        if ((isset($parametros[ConstanteParametros::CHAVE_VALOR_COMPRA_FINAL]) === true)&&(is_null($parametros[ConstanteParametros::CHAVE_VALOR_COMPRA_FINAL]) === false)) {
            $queryBuilder->andWhere('item.valor_compra <= :valor_compra_final');
            $queryBuilder->setParameter('valor_compra_final', $parametros[ConstanteParametros::CHAVE_VALOR_COMPRA_FINAL]);
        }

        if ((isset($parametros[ConstanteParametros::CHAVE_VALOR_VENDA_INICIAL]) === true)&&(is_null($parametros[ConstanteParametros::CHAVE_VALOR_VENDA_INICIAL]) === false)) {
            $queryBuilder->andWhere('item.valor_venda >= :valor_venda_inicial');
            $queryBuilder->setParameter('valor_venda_inicial', $parametros[ConstanteParametros::CHAVE_VALOR_VENDA_INICIAL]);
        }

        if ((isset($parametros[ConstanteParametros::CHAVE_VALOR_VENDA_FINAL]) === true)&&(is_null($parametros[ConstanteParametros::CHAVE_VALOR_VENDA_FINAL]) === false)) {
            $queryBuilder->andWhere('item.valor_venda <= :valor_venda_final');
            $queryBuilder->setParameter('valor_venda_final', $parametros[ConstanteParametros::CHAVE_VALOR_VENDA_FINAL]);
        }

        if ((isset($parametros[ConstanteParametros::CHAVE_SITUACAO]) === true)&&(is_null($parametros[ConstanteParametros::CHAVE_SITUACAO]) === false)
            && ($parametros[ConstanteParametros::CHAVE_SITUACAO] === 1)
        ) {
            $queryBuilder->andWhere("item.saldo_estoque < item.estoque_minimo");
        }

        // Filtra e substitui a query para passar ao Jasper
        $sql = $queryBuilder->getQuery()->getSQL();
        $sql = preg_replace('/.*WHERE\s(.*)$/', '$1', $sql);

        // Seleciona somente os wheres
        $sql = preg_replace('/i0_/', 'item', $sql);
        $sql = preg_replace('/t1_/', 'tipo_item', $sql);

        // Substituição de parâmetros
        $parameters = $queryBuilder->getParameters();
        foreach ($parameters as $parameter) {
            $param = $parameter->getValue();
            $sql   = preg_replace('/\?/', "'$param'", $sql, 1);
        }

        return $sql;
    }

    public function buscarItemValorCurso()
    {
        $queryBuilder = $this->createQueryBuilder('item');
        $queryBuilder->addSelect('item');
        $queryBuilder->addSelect('itFranqueada');

        $queryBuilder->join('item.franqueada', 'fran');
        $queryBuilder->leftJoin("item.itemFranqueadas", "itFranqueada");
        $this->filtrarFranqueada($queryBuilder);

        $queryBuilder->join('item.tipo_item', 'tipo_item');
        $queryBuilder->andWhere('tipo_item.tipo = :tipo');
        $queryBuilder->andWhere('itFranqueada.franqueada = :franqueada');
        $queryBuilder->setParameter('tipo', \App\Helper\SituacoesSistema::TIPO_ITEM_VALOR_CURSO);

        return \App\Helper\FunctionHelper::retornaPrimeiroResultado($queryBuilder, false);
    }

    public function gerarDadosRelatorioItensDeEstoque($parametros)
    {
        $queryBuilder = $this->createQueryBuilder('i')
            ->select([
                'i.descricao as item',
                'item_fran.valor_venda as valor_venda',
                'item_fran.valor_compra as valor_custo',
                'item_fran.saldo_estoque as saldo',
                'item_fran.estoque_minimo as estoque_minimo'
            ])
            ->innerjoin('i.tipo_item', 'tipo_item')
            ->innerjoin('i.itemFranqueadas', 'item_fran')
            ->andWhere('item_fran.franqueada = :franqueada')
            ->setParameter('franqueada', VariaveisCompartilhadas::$franqueadaID);
        
        if(key_exists(ConstanteParametros::CHAVE_VALOR_VENDA_DE, $parametros) && !empty($parametros[ConstanteParametros::CHAVE_VALOR_VENDA_DE]))
        {
            $queryBuilder->andWhere('item_fran.valor_venda >= :valor_venda_de')
                ->setParameter(':valor_venda_de', $parametros[ConstanteParametros::CHAVE_VALOR_VENDA_DE]);
        }
        if(key_exists(ConstanteParametros::CHAVE_VALOR_VENDA_ATE, $parametros) && !empty($parametros[ConstanteParametros::CHAVE_VALOR_VENDA_ATE]))
        {
            $queryBuilder->andWhere('item_fran.valor_venda <= :valor_venda_ate')
                ->setParameter(':valor_venda_ate', $parametros[ConstanteParametros::CHAVE_VALOR_VENDA_ATE]);
        }
        if(key_exists(ConstanteParametros::CHAVE_VALOR_CUSTO_DE, $parametros) && !empty($parametros[ConstanteParametros::CHAVE_VALOR_CUSTO_DE]))
        {
            $queryBuilder->andWhere('item_fran.valor_compra >= :valor_compra_de')
                ->setParameter(':valor_compra_de', $parametros[ConstanteParametros::CHAVE_VALOR_CUSTO_DE]);
        }
        if(key_exists(ConstanteParametros::CHAVE_VALOR_CUSTO_ATE, $parametros) && !empty($parametros[ConstanteParametros::CHAVE_VALOR_CUSTO_ATE]))
        {
            $queryBuilder->andWhere('item_fran.valor_compra <= :valor_compra_ate')
                ->setParameter(':valor_compra_ate', $parametros[ConstanteParametros::CHAVE_VALOR_CUSTO_ATE]);
        }
        if(isset($parametros[ConstanteParametros::CHAVE_ESTOQUE_NEGATIVO]))
        {
            if($parametros[ConstanteParametros::CHAVE_ESTOQUE_NEGATIVO] === "false") {
                $queryBuilder->andWhere('item_fran.saldo_estoque >= 0');
            } 
            if($parametros[ConstanteParametros::CHAVE_ESTOQUE_NEGATIVO] === "true"){
                $queryBuilder->andWhere('item_fran.saldo_estoque < 0');
            }
        }
        if(key_exists(ConstanteParametros::CHAVE_ITEM, $parametros) && !empty($parametros[ConstanteParametros::CHAVE_ITEM]))
        {
            $queryBuilder->andWhere('i = :item_id')
                ->setParameter(':item_id', $parametros[ConstanteParametros::CHAVE_ITEM]);
        }
        if(key_exists(ConstanteParametros::CHAVE_SITUACAO, $parametros) && !empty($parametros[ConstanteParametros::CHAVE_SITUACAO]))
        {
            $queryBuilder->andWhere('i.situacao = :situacao')
                ->setParameter(':situacao', $parametros[ConstanteParametros::CHAVE_SITUACAO]);
        }

        return $queryBuilder->getQuery()->getResult();
    }

    public function fetchPairsItem($parametros, $query = null)
    {
        $queryBuilder = $this->createQueryBuilder('i')
            ->select([
                'i.id',
                'i.descricao'
            ])
            ->andWhere('i.franqueada = :franqueada')
            ->setParameter('franqueada', $parametros[ConstanteParametros::CHAVE_FRANQUEADA])
            ->orderBy('i.descricao','ASC');
        
        if(!empty($query))
        {
            $queryBuilder->andWhere('i.descricao LIKE :descricao_item')
                ->setParameter('descricao_item', "%$query%");
        }
        
        return $queryBuilder->getQuery()->getResult();
    }

    public function buscarDadosRelatorioEstoque($parametros)
    {
        $queryBuilder = $this->createQueryBuilder('it')
            ->select([
                'it.descricao as item',
                'it_fran.saldo_estoque',
                'it_fran.estoque_minimo',
                'it_fran.valor_compra',
                'it_fran.valor_venda',
                "date_format(mov.data_movimento, '%Y-%m-%d') as data_movimento",
                "mov.quantidade_item",
                "mov.valor_movimento",
                "tipo_mov.tipo_movimento as movimento",
                "CASE tipo_mov.tipo_movimento WHEN 'AE' THEN 1 WHEN 'AS' THEN 1 ELSE 0 END as manual",
                'icr.quantidade as pedido_quantidade',
                'icr.valor_item as pedido_valor_item',
                'icr.valor_desconto as pedido_valor_desconto',
                "date_format(icr.data_entrega, '%Y-%m-%d') as pedido_data_entrega",
                "CASE icr.situacao_entrega WHEN 'E' THEN 1 ELSE 0 END as pedido_entregue",
                "cr.id as pedido_id"
            ])
            ->join('it.itemFranqueadas', 'it_fran')
            ->join('it.movimentoEstoques', 'mov')
            ->leftJoin('mov.tipo_movimento_estoque', 'tipo_mov')
            ->leftJoin('mov.item_conta_receber', 'icr')
            ->leftJoin('icr.conta_receber', 'cr')
            ->andWhere('it_fran.franqueada = :franqueada')
            ->andWhere('mov.franqueada = :franqueada')
            ->setParameter(':franqueada', $parametros[ConstanteParametros::CHAVE_FRANQUEADA]);

            if(key_exists(ConstanteParametros::CHAVE_ITEM, $parametros) && !empty($parametros[ConstanteParametros::CHAVE_ITEM]))
            {
                $queryBuilder->andWhere("it = :item_id")
                    ->setParameter(":item_id", $parametros[ConstanteParametros::CHAVE_ITEM]);
            }
            if(key_exists(ConstanteParametros::CHAVE_DATA_INICIAL, $parametros) && is_null($parametros[ConstanteParametros::CHAVE_DATA_INICIAL]) === false)
            {
                $dataInicial = strtotime(str_replace("/", "-",$parametros[ConstanteParametros::CHAVE_DATA_INICIAL] . " 00:00:00"));
                $dataInicial = date('Y-m-d H:i:s', $dataInicial);
                $queryBuilder->andWhere("mov.data_movimento >= :data_inicial");
                $queryBuilder->setParameter('data_inicial', $dataInicial);
            }
            if(key_exists(ConstanteParametros::CHAVE_DATA_FINAL, $parametros) && is_null($parametros[ConstanteParametros::CHAVE_DATA_FINAL]) === false)
            {
                $dataFinal = strtotime(str_replace("/", "-",$parametros[ConstanteParametros::CHAVE_DATA_FINAL] . " 23:59:59"));
                $dataFinal = date( 'Y-m-d H:i:s', $dataFinal);
                $queryBuilder->andWhere("mov.data_movimento <= :data_final");
                $queryBuilder->setParameter('data_final', $dataFinal);
            }
        
        return $queryBuilder->getQuery()->getResult();
    }

    public function gerarDadosRelatorioServicosSolicitados($parametros) 
    {
        $queryBuilder = $this->createQueryBuilder('it')
            ->select([
                'p.nome_contato as aluno',
                'it.descricao',
                'ti.descricao as tipo_servico',
                'icr.valor',
                'cr.situacao',
                "date_format(icr.data_entrega, '%Y-%m-%d') as data_solicitacao",
                "date_format(icr.data_vencimento, '%Y-%m-%d') as data_vencimento"
            ])
            ->leftJoin('it.tipo_item', 'ti')
            ->leftJoin('it.itemFranqueadas', 'it_fran')
            ->leftJoin('it_fran.franqueada', 'fran')
            ->leftJoin('it.plano_conta', 'plano')
            ->leftJoin('it.movimentoEstoques', 'mov')
            ->leftJoin('mov.item_conta_receber', 'icr')
            ->leftJoin('icr.conta_receber', 'cr')
            ->leftJoin('cr.aluno', 'a')
            ->leftJoin('a.pessoa', 'p')
            ->andWhere('fran.id = :franqueada')
            ->andWhere('mov.franqueada = :franqueada')
            ->andWhere('cr.franqueada = :franqueada')
            // a baixo coloquei os id's do tipo_item que são considerados serviços.
            ->andWhere('ti.id in (2, 3, 7, 9, 10, 11, 12, 15, 16, 17, 18, 22)')
            ->setParameter('franqueada', $parametros[ConstanteParametros::CHAVE_FRANQUEADA]);


        if ((isset($parametros[ConstanteParametros::CHAVE_ALUNO]) === true) && (is_null($parametros[ConstanteParametros::CHAVE_ALUNO]) === false)) {
            $queryBuilder->andWhere("a = (:aluno)");
            $queryBuilder->setParameter("aluno", $parametros[ConstanteParametros::CHAVE_ALUNO]);
        }

        if(isset($parametros[ConstanteParametros::CHAVE_TIPO_SERVICO])) {
            $queryBuilder->andWhere("ti in (:tipo_servico)");
            $queryBuilder->setParameter("tipo_servico", $parametros[ConstanteParametros::CHAVE_TIPO_SERVICO]);
        }

        if(isset($parametros[ConstanteParametros::CHAVE_DATA_FINAL])) {
            $dataInicial = strtotime(str_replace("/", "-",$parametros[ConstanteParametros::CHAVE_DATA_INICIAL] . " 00:00:00"));
            $dataInicial = date('Y-m-d H:i:s', $dataInicial);
            $queryBuilder->andWhere("icr.data_entrega >= :data_inicial");
            $queryBuilder->setParameter('data_inicial', $dataInicial);
        }

        if(isset($parametros[ConstanteParametros::CHAVE_DATA_FINAL])) {
            $dataFinal = strtotime(str_replace("/", "-",$parametros[ConstanteParametros::CHAVE_DATA_FINAL] . " 23:59:59"));
            $dataFinal = date('Y-m-d H:i:s', $dataFinal);
            $queryBuilder->andWhere("icr.data_entrega <= :data_final");
            $queryBuilder->setParameter('data_final', $dataFinal);
        }

        return $queryBuilder->getQuery()->getResult();
    }
}
