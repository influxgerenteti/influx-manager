<?php

namespace App\Repository\Principal;

use App\Entity\Principal\AtividadeExtra;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Symfony\Bridge\Doctrine\RegistryInterface;
use App\Helper\ConstanteParametros;
use App\Helper\VariaveisCompartilhadas;

/**
 * @method AtividadeExtra|null find($id, $lockMode = null, $lockVersion = null)
 * @method AtividadeExtra|null findOneBy(array $criteria, array $orderBy = null)
 * @method AtividadeExtra[]    findAll()
 * @method AtividadeExtra[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class AtividadeExtraRepository extends ServiceEntityRepository
{


    public function __construct(RegistryInterface $registry)
    {
        parent::__construct($registry, AtividadeExtra::class);
    }

    /**
     * Monta query principal
     *
     * @return \Doctrine\ORM\QueryBuilder
     */
    protected function montaQueryBase()
    {
        $queryBuilder = $this->createQueryBuilder("ae");
        $queryBuilder->addSelect("it");
        $queryBuilder->addSelect("ti");
        $queryBuilder->addSelect("sf");
        $queryBuilder->addSelect("sl");
        $queryBuilder->addSelect("us");
        $queryBuilder->addSelect("re");
        $queryBuilder->addSelect("fc");
        $queryBuilder->addSelect("aae");
        $queryBuilder->addSelect("al");
        $queryBuilder->addSelect("p");
        $queryBuilder->addSelect("iae");
        $queryBuilder->addSelect("int");
        $queryBuilder->addSelect("flc");
        $queryBuilder->addSelect("ilv");
        $queryBuilder->addSelect("wf");
        $queryBuilder->addSelect("cae");
        $queryBuilder->leftJoin("ae.franqueada", "fran");
        $queryBuilder->leftJoin("ae.item", "it");
        $queryBuilder->leftJoin("it.tipo_item", "ti");
        $queryBuilder->leftJoin("ae.sala_franqueada", "sf");
        $queryBuilder->leftJoin("sf.sala", "sl");
        $queryBuilder->leftJoin("ae.usuario_solicitante", "us");
        $queryBuilder->leftJoin("ae.responsaveis_execucacao", "re");
        $queryBuilder->leftJoin("ae.forma_cobranca", "fc");
        $queryBuilder->leftJoin("ae.alunoAtividadeExtras", "aae");
        $queryBuilder->leftJoin("ae.convidadoAtividadeExtras", "cae");
        $queryBuilder->leftJoin("aae.aluno", "al");
        $queryBuilder->leftJoin("al.pessoa", "p");
        $queryBuilder->leftJoin("ae.interessadoAtividadeExtra", "iae");
        $queryBuilder->leftJoin("iae.interessado", "int");
        $queryBuilder->leftJoin("int.workflow", "wf");
        $queryBuilder->leftJoin("int.followupComercials", "flc");
        $queryBuilder->leftJoin("iae.livro", "ilv");
        return $queryBuilder;
    }

    /**
     * Monta os filtros
     *
     * @param \Doctrine\ORM\QueryBuilder $queryBuilder
     * @param array $parametros
     */
    protected function montaFiltros(&$queryBuilder, $parametros)
    {
        if ((isset($parametros[ConstanteParametros::CHAVE_DATA_AGENDAMENTO_DE]) === true)&&(empty($parametros[ConstanteParametros::CHAVE_DATA_AGENDAMENTO_DE]) === false)) {
            $queryBuilder->andWhere("ae.data_hora_inicio >= :dataAgendamentoDe");
            $queryBuilder->setParameter("dataAgendamentoDe", $parametros[ConstanteParametros::CHAVE_DATA_AGENDAMENTO_DE]);
        }

        if ((isset($parametros[ConstanteParametros::CHAVE_DATA_AGENDAMENTO_ATE]) === true)&&(empty($parametros[ConstanteParametros::CHAVE_DATA_AGENDAMENTO_ATE]) === false)) {
            $queryBuilder->andWhere("ae.data_hora_inicio <= :dataAgendamentoAte");
            $queryBuilder->setParameter("dataAgendamentoAte", $parametros[ConstanteParametros::CHAVE_DATA_AGENDAMENTO_ATE]);
        }

        if ((isset($parametros[ConstanteParametros::CHAVE_ITEM]) === true)&&(empty($parametros[ConstanteParametros::CHAVE_ITEM]) === false)) {
            $queryBuilder->andWhere("it.id = :itemId");
            $queryBuilder->setParameter("itemId", $parametros[ConstanteParametros::CHAVE_ITEM]);
        }

        if ((isset($parametros[ConstanteParametros::CHAVE_TIPO]) === true)&&(empty($parametros[ConstanteParametros::CHAVE_TIPO]) === false)) {
            $queryBuilder->andWhere("ti.tipo = :tipoItem");
            $queryBuilder->setParameter("tipoItem", $parametros[ConstanteParametros::CHAVE_TIPO]);
        }

        if ((isset($parametros[ConstanteParametros::CHAVE_RESPONSAVEL_EXECUCAO]) === true)&&(empty($parametros[ConstanteParametros::CHAVE_RESPONSAVEL_EXECUCAO]) === false)) {
            $queryBuilder->andWhere("re.id = :responsavelId");
            $queryBuilder->setParameter("responsavelId", $parametros[ConstanteParametros::CHAVE_RESPONSAVEL_EXECUCAO]);
        }

        if ((isset($parametros[ConstanteParametros::CHAVE_SITUACAO]) === true)&&(empty($parametros[ConstanteParametros::CHAVE_SITUACAO]) === false)) {
            $queryBuilder->andWhere("ae.situacao IN(:situacoes)");
            $queryBuilder->setParameter("situacoes", $parametros[ConstanteParametros::CHAVE_SITUACAO]);
        }
    }

    /**
     * Query para realizar fitlro de franqueada
     *
     * @param \Doctrine\ORM\QueryBuilder $queryBuilder
     */
    private function filtrarFranqueada(&$queryBuilder)
    {
        $queryBuilder->where('fran.id = :franqueada');
        $queryBuilder->setParameter('franqueada', VariaveisCompartilhadas::$franqueadaID);
    }

    /**
     * Filtra a AtividadeExtra por pagina
     *
     * @param array $parametros
     * @param number $pagina
     * @param number $numeroItensPorPagina
     *
     * @return \Knp\Component\Pager\Pagination\SlidingPagination
     */
    public function filtrarAtividadeExtraPorPagina($parametros, $numeroItensPorPagina=50)
    {
        $pagina = 1;
        if (isset($parametros['pagina'])) {
            $pagina = $parametros['pagina'];
        }

        $opcoes       = [];
        $queryBuilder = $this->montaQueryBase();
        $this->filtrarFranqueada($queryBuilder);
        $this->montaFiltros($queryBuilder, $parametros);
        if ((isset($parametros[ConstanteParametros::CHAVE_ORDENACAO_COLUNA]) === true)&&(is_null($parametros[ConstanteParametros::CHAVE_ORDENACAO_COLUNA]) === false)) {
            $queryBuilder->orderBy($parametros[ConstanteParametros::CHAVE_ORDENACAO_COLUNA], $parametros[ConstanteParametros::CHAVE_ORDENACAO_SORT]);
            $opcoes[\Knp\Component\Pager\Paginator::SORT_FIELD_PARAMETER_NAME]     = "~";
            $opcoes[\Knp\Component\Pager\Paginator::SORT_DIRECTION_PARAMETER_NAME] = "~";
        }

        return \App\Helper\FunctionHelper::montaPaginatorPaginacao($queryBuilder, $pagina, $numeroItensPorPagina, $opcoes);
    }

    /**
     *  Busca registro de atividade extra por id
     *
     * @param int $id
     *
     * @return array|NULL
     */
    public function buscarRegistroPorId($id)
    {
        $queryBuilder = $this->montaQueryBase();
        $this->filtrarFranqueada($queryBuilder);
        $queryBuilder->andWhere("ae.id = :id");
        $queryBuilder->setParameter("id", $id);

        return  \App\Helper\FunctionHelper::retornaArrayNull($queryBuilder, true);
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
        $queryBuilder = $this->createQueryBuilder("ae");
        $queryBuilder->select('ae.id');
        $queryBuilder->join('ae.item', 'i');
        $queryBuilder->join('i.tipo_item', 'ti');
        $queryBuilder->leftJoin('ae.alunoAtividadeExtras', 'aae');
        $queryBuilder->leftJoin('aae.aluno', 'a');
        $queryBuilder->leftJoin('a.pessoa', 'p');
        $queryBuilder->leftJoin('ae.conta_receber', 'cr');

        $queryBuilder->where('ae.franqueada = :franqueada');
        $queryBuilder->setParameter('franqueada', VariaveisCompartilhadas::$franqueadaID);

        if ((isset($parametros[ConstanteParametros::CHAVE_FILTRO_TIPO]) === true)&&(is_null($parametros[ConstanteParametros::CHAVE_FILTRO_TIPO]) === false)) {
            $queryBuilder->andWhere('ti.tipo = :tipo');
            $queryBuilder->setParameter('tipo', $parametros[ConstanteParametros::CHAVE_FILTRO_TIPO]);
        }

        if ((isset($parametros[ConstanteParametros::CHAVE_DATA_INICIAL]) === true)&&(is_null($parametros[ConstanteParametros::CHAVE_DATA_INICIAL]) === false)) {
            $queryBuilder->andWhere('ae.data_hora_inicio >= :data_inicial');
            $queryBuilder->setParameter('data_inicial', $parametros[ConstanteParametros::CHAVE_DATA_INICIAL]);
        }

        if ((isset($parametros[ConstanteParametros::CHAVE_DATA_FINAL]) === true)&&(is_null($parametros[ConstanteParametros::CHAVE_DATA_FINAL]) === false)) {
            $queryBuilder->andWhere('ae.data_hora_fim <= :data_final');
            $queryBuilder->setParameter('data_final', $parametros[ConstanteParametros::CHAVE_DATA_FINAL]);
        }

        if ((isset($parametros[ConstanteParametros::CHAVE_FILTRO_ALUNO]) === true)&&(is_null($parametros[ConstanteParametros::CHAVE_FILTRO_ALUNO]) === false)) {
            $queryBuilder->andWhere('a = :aluno');
            $queryBuilder->setParameter('aluno', $parametros[ConstanteParametros::CHAVE_FILTRO_ALUNO]);
        }

        if ((isset($parametros[ConstanteParametros::CHAVE_SITUACAO]) === true)&&(is_null($parametros[ConstanteParametros::CHAVE_SITUACAO]) === false)) {
            $situacao = explode(',', $parametros[ConstanteParametros::CHAVE_SITUACAO]);
            $queryBuilder->andWhere("ae.situacao IN (:situacao)");
            $queryBuilder->setParameter('situacao', implode("', '", $situacao));
        }

        // Filtra e substitui a query para passar ao Jasper
        $sql = $queryBuilder->getQuery()->getSQL();

        $sql = preg_replace('/.*WHERE\s(.*)$/', '$1', $sql);

        // Seleciona somente os wheres
        $sql = preg_replace('/a0_/', 'ae', $sql);
        $sql = preg_replace('/i1_/', 'i', $sql);
        $sql = preg_replace('/t2_/', 'ti', $sql);
        $sql = preg_replace('/a3_/', 'aae', $sql);
        $sql = preg_replace('/a4_/', 'a', $sql);
        $sql = preg_replace('/p5_/', 'p', $sql);
        $sql = preg_replace('/a7_/', 'aecr', $sql);
        $sql = preg_replace('/c6_/', 'cr', $sql);

        // Substituição de parâmetros
        $parameters = $queryBuilder->getParameters();
        foreach ($parameters as $parameter) {
            $param = $parameter->getValue();
            $sql   = preg_replace('/\?/', "'$param'", $sql, 1);
        }

        return $sql;
    }

    public function fetch($parametros) {
        $query = $this->createQueryBuilder("a")
            ->select([
                "a.id",
                "a.descricao_atividade as descricao"
            ])
            ->where("a.franqueada = :franqueada")
            ->setParameter("franqueada", $parametros[ConstanteParametros::CHAVE_FRANQUEADA])
            ->andWhere("a.descricao_atividade IS NOT NULL");
        return $query->getQuery()->getResult();
    }

    public function buscarDadosRelatorioAtividadesExtras($parametros)
    {
        $queryBuilder = $this->createQueryBuilder("ae")
            ->select([
                'ae.id',
                'i.descricao',
                'p.nome_contato as aluno',
                'aae.presenca',
                "date_format(ae.data_hora_inicio, '%Y-%m-%d') as data_atividade",
                "date_format(ae.data_hora_inicio, '%H:%i') as hora_inicial_atividade",
                "date_format(ae.data_hora_fim, '%H:%i') as hora_final_atividade",
                'ae.valor',
                'ae.situacao'
            ])
            ->leftJoin('ae.alunoAtividadeExtras', 'aae')
            ->leftjoin('ae.item', 'i')
            ->leftjoin('i.tipo_item', 'ti')
            ->leftJoin('aae.aluno', 'a')
            ->leftJoin('a.pessoa', 'p')
            ->where("i.descricao <> 'Nivelamento'")
            ->andWhere('ae.franqueada = :franqueada')
            ->setParameter('franqueada', VariaveisCompartilhadas::$franqueadaID)
            ->orderBy("i.descricao")
            ->addOrderBy("date_format(ae.data_hora_inicio, '%Y-%m-%d')", 'DESC')
            ->addOrderBy("date_format(ae.data_hora_inicio, '%H:%i')")
            ->addOrderBy("date_format(ae.data_hora_fim, '%H:%i')")
            ->addOrderBy("p.nome_contato");

        if(isset($parametros[ConstanteParametros::CHAVE_ATIVIDADE_EXTRA])) {
            $queryBuilder->andWhere("ae.item in (:atividade_extra)");
            $queryBuilder->setParameter("atividade_extra", $parametros[ConstanteParametros::CHAVE_ATIVIDADE_EXTRA ]);
        }

        if(isset($parametros[ConstanteParametros::CHAVE_SITUACAO])) {
            $queryBuilder->andWhere("ae.situacao in (:situacao)");
            $queryBuilder->setParameter("situacao", $parametros[ConstanteParametros::CHAVE_SITUACAO ]);
        }

        if ((isset($parametros[ConstanteParametros::CHAVE_ALUNO]) === true) && (is_null($parametros[ConstanteParametros::CHAVE_ALUNO]) === false)) {
            $queryBuilder->andWhere("a = (:aluno)");
            $queryBuilder->setParameter("aluno", $parametros[ConstanteParametros::CHAVE_ALUNO]);
        }

        if(isset($parametros[ConstanteParametros::CHAVE_DATA_INICIAL])) {
            $dataInicial = strtotime(str_replace("/", "-",$parametros[ConstanteParametros::CHAVE_DATA_INICIAL] . " 00:00:00"));
            $dataInicial = date('Y-m-d H:i:s', $dataInicial);
            $queryBuilder->andWhere("ae.data_hora_inicio >= :data_inicial");
            $queryBuilder->setParameter('data_inicial', $dataInicial);
        }

        if(isset($parametros[ConstanteParametros::CHAVE_DATA_FINAL])) {
            $dataFinal = strtotime(str_replace("/", "-",$parametros[ConstanteParametros::CHAVE_DATA_FINAL] . " 23:59:59"));
            $dataFinal = date('Y-m-d H:i:s', $dataFinal);
            $queryBuilder->andWhere("ae.data_hora_fim <= :data_final");
            $queryBuilder->setParameter('data_final', $dataFinal);
        }

        return $queryBuilder->getQuery()->getResult();
        
    }
}
