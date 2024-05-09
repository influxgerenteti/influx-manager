<?php

namespace App\Repository\Principal;

use App\Entity\Principal\Interessado;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Symfony\Bridge\Doctrine\RegistryInterface;
use App\Helper\ConstanteParametros;
use App\Helper\VariaveisCompartilhadas;
use App\Helper\SituacoesSistema;

use function PHPSTORM_META\map;

/**
 * @method Interessado|null find($id, $lockMode = null, $lockVersion = null)
 * @method Interessado|null findOneBy(array $criteria, array $orderBy = null)
 * @method Interessado[]    findAll()
 * @method Interessado[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class InteressadoRepository extends ServiceEntityRepository
{

    /**
     * @var Registry
     */
    private $registry;

    public function __construct(RegistryInterface $registry)
    {
        parent::__construct($registry, Interessado::class);
        $this->registry = $registry;
    }

    /**
     * Monta query principal
     *
     * @return \Doctrine\ORM\QueryBuilder
     */
    protected function montaQueryBase()
    {
        $queryBuilder = $this->createQueryBuilder("it");
        $queryBuilder->addSelect("cf");
        $queryBuilder->addSelect("crf");
        $queryBuilder->addSelect("wf");
        $queryBuilder->addSelect("fran");
        $queryBuilder->addSelect("idi");
        $queryBuilder->addSelect("fupc");
        $queryBuilder->addSelect("mnf");
        $queryBuilder->addSelect("al");
        $queryBuilder->addSelect("cu");
        $queryBuilder->addSelect("tc");
        $queryBuilder->addSelect("tp");
        $queryBuilder->addSelect("iae");
        $queryBuilder->addSelect("ae");
        $queryBuilder->addSelect("item");
        $queryBuilder->addSelect("tipoItem");
        $queryBuilder->addSelect("re");
        $queryBuilder->addSelect("pes");
        $queryBuilder->addSelect("lvro");
        $queryBuilder->join("it.franqueada", "fran");
        $queryBuilder->leftJoin("it.tipo_contato", "tc");
        $queryBuilder->leftJoin("it.interessadoAtividadeExtras", "iae");
        $queryBuilder->leftJoin("iae.livro", "lvro");
        $queryBuilder->leftJoin("iae.atividade_extra", "ae");
        $queryBuilder->leftJoin("ae.responsaveis_execucacao", "re");
        $queryBuilder->leftJoin("ae.item", "item");
        $queryBuilder->leftJoin("item.tipo_item", "tipoItem");
        $queryBuilder->leftJoin("it.tipo_prospeccao", "tp");
        $queryBuilder->leftJoin("it.curso", "cu");
        $queryBuilder->leftJoin("it.aluno", "al");
        $queryBuilder->leftJoin("it.consultor_funcionario", "cf");
        $queryBuilder->leftJoin("it.consultor_responsavel_funcionario", "crf");
        $queryBuilder->leftJoin("it.workflow", "wf");
        $queryBuilder->leftJoin("it.motivo_nao_fechamento", "mnf");
        $queryBuilder->leftJoin("it.followupComercials", "fupc");
        $queryBuilder->leftJoin("it.idiomas", "idi");
        $queryBuilder->leftJoin("it.idiomas", "idi2");
        $queryBuilder->leftJoin("it.pessoa_indicou", "pes");
        return $queryBuilder;
    }

    /**
     * Query para realizar fitlro de franqueada
     *
     * @param \Doctrine\ORM\QueryBuilder $queryBuilder
     */
    private function filtrarFranqueada(&$queryBuilder)
    {
        $queryBuilder->andWhere('fran.id = :franqueada');
        $queryBuilder->setParameter('franqueada', VariaveisCompartilhadas::$franqueadaID);
    }

    /**
     * Monta filtros data de/ate e horarios de/ate
     *
     * @param \Doctrine\ORM\QueryBuilder $queryBuilder
     * @param array $parametros
     */
    protected function montaFiltroDatasHorarios(&$queryBuilder, $parametros)
    {
        if ((isset($parametros[ConstanteParametros::CHAVE_DATA_CADASTRO_DE]) === true)&&(empty($parametros[ConstanteParametros::CHAVE_DATA_CADASTRO_DE]) === false)) {
            $queryBuilder->andWhere("it.data_cadastro >= :dataCadastroDe");
            $queryBuilder->setParameter("dataCadastroDe", $parametros[ConstanteParametros::CHAVE_DATA_CADASTRO_DE]);
        }

        if ((isset($parametros[ConstanteParametros::CHAVE_DATA_CADASTRO_ATE]) === true)&&(empty($parametros[ConstanteParametros::CHAVE_DATA_CADASTRO_ATE]) === false)) {
            $queryBuilder->andWhere("it.data_cadastro <= :dataCadastroAte");
            $queryBuilder->setParameter("dataCadastroAte", $parametros[ConstanteParametros::CHAVE_DATA_CADASTRO_ATE]);
        }

        if ((isset($parametros[ConstanteParametros::CHAVE_DATA_VALIDADE_PROMOCAO_DE]) === true)&&(empty($parametros[ConstanteParametros::CHAVE_DATA_VALIDADE_PROMOCAO_DE]) === false)) {
            $queryBuilder->andWhere("it.data_validade_promocao >= :dataValidadeDe");
            $queryBuilder->setParameter("dataValidadeDe", $parametros[ConstanteParametros::CHAVE_DATA_VALIDADE_PROMOCAO_DE]);
        }

        if ((isset($parametros[ConstanteParametros::CHAVE_DATA_VALIDADE_PROMOCAO_ATE]) === true)&&(empty($parametros[ConstanteParametros::CHAVE_DATA_VALIDADE_PROMOCAO_ATE]) === false)) {
            $queryBuilder->andWhere("it.data_validade_promocao <= :dataValidadeAte");
            $queryBuilder->setParameter("dataValidadeAte", $parametros[ConstanteParametros::CHAVE_DATA_VALIDADE_PROMOCAO_ATE]);
        }

        if ((isset($parametros[ConstanteParametros::CHAVE_DATA_PROXIMO_CONTATO_DE]) === true)&&(empty($parametros[ConstanteParametros::CHAVE_DATA_PROXIMO_CONTATO_DE]) === false)) {
            $queryBuilder->andWhere("agc.data_agendamento >= :dataDe");
            $queryBuilder->setParameter("dataDe", $parametros[ConstanteParametros::CHAVE_DATA_PROXIMO_CONTATO_DE]);
        }

        if ((isset($parametros[ConstanteParametros::CHAVE_DATA_PROXIMO_CONTATO_ATE]) === true)&&(empty($parametros[ConstanteParametros::CHAVE_DATA_PROXIMO_CONTATO_ATE]) === false)) {
            $queryBuilder->andWhere("agc.data_agendamento <= :dataAte");
            $queryBuilder->setParameter("dataAte", $parametros[ConstanteParametros::CHAVE_DATA_PROXIMO_CONTATO_ATE]);
        }

        if ((isset($parametros[ConstanteParametros::CHAVE_HORARIO_PROXIMO_CONTATO_DE]) === true)&&(empty($parametros[ConstanteParametros::CHAVE_HORARIO_PROXIMO_CONTATO_DE]) === false)) {
            $queryBuilder->andWhere("TIME(agc.data_agendamento) >= :horaDe");
            $queryBuilder->setParameter("horaDe", $parametros[ConstanteParametros::CHAVE_HORARIO_PROXIMO_CONTATO_DE]);
        }

        if ((isset($parametros[ConstanteParametros::CHAVE_HORARIO_PROXIMO_CONTATO_ATE]) === true)&&(empty($parametros[ConstanteParametros::CHAVE_HORARIO_PROXIMO_CONTATO_ATE]) === false)) {
            $queryBuilder->andWhere("TIME(agc.data_agendamento) <= :horaAte");
            $queryBuilder->setParameter("horaAte", $parametros[ConstanteParametros::CHAVE_HORARIO_PROXIMO_CONTATO_ATE]);
        }
    }

    /**
     * Monta os filtros de followups(listagem/tela) na query
     *
     * @param \Doctrine\ORM\QueryBuilder $queryBuilder
     * @param array $parametros
     */
    protected function montaFiltrosFollowups(&$queryBuilder, $parametros)
    {
        if ((isset($parametros[ConstanteParametros::CHAVE_DATA_CADASTRO_DE]) === true)&&(empty($parametros[ConstanteParametros::CHAVE_DATA_CADASTRO_DE]) === false)) {
            $dataCadastroDe = explode("T", $parametros[ConstanteParametros::CHAVE_DATA_CADASTRO_DE]);
            $queryBuilder->andWhere("fc.data_registro >= :dataCadastroDe");
            $queryBuilder->setParameter("dataCadastroDe", $dataCadastroDe[0] . " 00:00:01");
        }

        if ((isset($parametros[ConstanteParametros::CHAVE_DATA_CADASTRO_ATE]) === true)&&(empty($parametros[ConstanteParametros::CHAVE_DATA_CADASTRO_ATE]) === false)) {
            $dataCadastroAte = explode("T", $parametros[ConstanteParametros::CHAVE_DATA_CADASTRO_ATE]);
            $queryBuilder->andWhere("fc.data_registro <= :dataCadastroAte");
            $queryBuilder->setParameter("dataCadastroAte", $dataCadastroAte[0] . " 23:59:59");
        }

        if ((isset($parametros[ConstanteParametros::CHAVE_DATA_PROXIMO_CONTATO_DE]) === true)&&(empty($parametros[ConstanteParametros::CHAVE_DATA_PROXIMO_CONTATO_DE]) === false)) {
            $dataProximoContatoDe = explode("T", $parametros[ConstanteParametros::CHAVE_DATA_PROXIMO_CONTATO_DE]);
            $queryBuilder->andWhere("agc.data_agendamento >= :dataProximoContatoDe");
            $queryBuilder->setParameter("dataProximoContatoDe", $dataProximoContatoDe[0] . " 00:00:01");
        }

        if ((isset($parametros[ConstanteParametros::CHAVE_DATA_PROXIMO_CONTATO_ATE]) === true)&&(empty($parametros[ConstanteParametros::CHAVE_DATA_PROXIMO_CONTATO_ATE]) === false)) {
            $dataProximoContatoAte = explode("T", $parametros[ConstanteParametros::CHAVE_DATA_PROXIMO_CONTATO_ATE]);
            $queryBuilder->andWhere("agc.data_agendamento <= :dataProximoContatoAte");
            $queryBuilder->setParameter("dataProximoContatoAte", $dataProximoContatoAte[0] . " 23:59:59");
        }

        if ((isset($parametros[ConstanteParametros::CHAVE_DATA_TERMINO_CONTRATO_DE]) === true)&&(empty($parametros[ConstanteParametros::CHAVE_DATA_TERMINO_CONTRATO_DE]) === false)) {
            $dataTerminoContratoDe = explode("T", $parametros[ConstanteParametros::CHAVE_DATA_TERMINO_CONTRATO_DE]);
            $queryBuilder->andWhere("ctts.data_termino_contrato >= :dataTerminoContratoDe");
            $queryBuilder->setParameter("dataTerminoContratoDe", $dataTerminoContratoDe[0] . " 00:00:01");
        }

        if ((isset($parametros[ConstanteParametros::CHAVE_DATA_TERMINO_CONTRATO_ATE]) === true)&&(empty($parametros[ConstanteParametros::CHAVE_DATA_TERMINO_CONTRATO_ATE]) === false)) {
            $dataTerminoContratoAte = explode("T", $parametros[ConstanteParametros::CHAVE_DATA_TERMINO_CONTRATO_ATE]);
            $queryBuilder->andWhere("ctts.data_termino_contrato <= :dataTerminoContratoAte");
            $queryBuilder->setParameter("dataTerminoContratoAte", $dataTerminoContratoAte[0] . " 23:59:59");
        }

        if ((isset($parametros[ConstanteParametros::CHAVE_SITUACAO_INTERESSADO]) === true)&&((empty($parametros[ConstanteParametros::CHAVE_SITUACAO_INTERESSADO]) === false))) {
            $queryBuilder->andWhere("it.situacao IN (:situacaoInteressado)");
            $queryBuilder->setParameter("situacaoInteressado", $parametros[ConstanteParametros::CHAVE_SITUACAO_INTERESSADO]);
        }

        if ((isset($parametros[ConstanteParametros::CHAVE_GRAU_INTERESSE]) === true)&&((empty($parametros[ConstanteParametros::CHAVE_GRAU_INTERESSE]) === false))) {
            $queryBuilder->andWhere("it.grau_interesse IN (:grauInteresse)");
            $queryBuilder->setParameter("grauInteresse", $parametros[ConstanteParametros::CHAVE_GRAU_INTERESSE]);
        }

        if ((isset($parametros[ConstanteParametros::CHAVE_SITUACAO_CONTRATO]) === true)&&((empty($parametros[ConstanteParametros::CHAVE_SITUACAO_CONTRATO]) === false))) {
            $queryBuilder->andWhere("ctts.situacao IN (:situacaoContrato)");
            $queryBuilder->setParameter("situacaoContrato", $parametros[ConstanteParametros::CHAVE_SITUACAO_CONTRATO]);
        }

        if ((isset($parametros[ConstanteParametros::CHAVE_SITUACAO_ALUNO]) === true)&&((empty($parametros[ConstanteParametros::CHAVE_SITUACAO_ALUNO]) === false))) {
            $queryBuilder->andWhere("al.situacao IN (:situacaoAluno)");
            $queryBuilder->setParameter("situacaoAluno", $parametros[ConstanteParametros::CHAVE_SITUACAO_ALUNO]);
        }

        if ((isset($parametros[ConstanteParametros::CHAVE_INTERESSADO]) === true)&&(empty($parametros[ConstanteParametros::CHAVE_INTERESSADO]) === false)) {
            $queryBuilder->andWhere("it.id = :interessadoId");
            $queryBuilder->setParameter("interessadoId", $parametros[ConstanteParametros::CHAVE_INTERESSADO]);
        }

        if ((isset($parametros[ConstanteParametros::CHAVE_TIPO_LEAD]) === true)&&(empty($parametros[ConstanteParametros::CHAVE_TIPO_LEAD]) === false)) {
            $queryBuilder->andWhere("it.tipo_lead = :tiposLead");
            $queryBuilder->setParameter("tiposLead", $parametros[ConstanteParametros::CHAVE_TIPO_LEAD]);
        }

        if ((isset($parametros[ConstanteParametros::CHAVE_CONSULTOR_RESPONSAVEL_FUNCIONARIO]) === true)&&(empty($parametros[ConstanteParametros::CHAVE_CONSULTOR_RESPONSAVEL_FUNCIONARIO]) === false)) {
            $queryBuilder->andWhere(
                 $queryBuilder->expr()->orX(
                    $queryBuilder->expr()->eq("consulFunc.id",":consultorId"),
                    $queryBuilder->expr()->eq("it.consultor_responsavel_funcionario", ":consultorId")
                )
               );
            $queryBuilder->setParameter("consultorId", $parametros[ConstanteParametros::CHAVE_CONSULTOR_RESPONSAVEL_FUNCIONARIO]);
        }

        if ((isset($parametros[ConstanteParametros::CHAVE_RESPONSAVEL_VENDA_FUNCIONARIO]) === true)&&(empty($parametros[ConstanteParametros::CHAVE_RESPONSAVEL_VENDA_FUNCIONARIO]) === false)) {
            $queryBuilder->andWhere("respVendaContrato.id = :respVendaId");
            $queryBuilder->setParameter("respVendaId", $parametros[ConstanteParametros::CHAVE_RESPONSAVEL_VENDA_FUNCIONARIO]);
        }
    }

    /**
     * Monta os filtros na query
     *
     * @param \Doctrine\ORM\QueryBuilder $queryBuilder
     * @param array $parametros
     */
    protected function montaFiltros(&$queryBuilder, $parametros)
    {
        if ((isset($parametros[ConstanteParametros::CHAVE_INTERESSADO]) === true)&&(empty($parametros[ConstanteParametros::CHAVE_INTERESSADO]) === false)) {
            $queryBuilder->andWhere("it.id = :idInterressado");
            $queryBuilder->setParameter("idInterressado", $parametros[ConstanteParametros::CHAVE_INTERESSADO]);
        }

        if ((isset($parametros[ConstanteParametros::CHAVE_NOME]) === true)&&(empty($parametros[ConstanteParametros::CHAVE_NOME]) === false)) {
            $queryBuilder->andWhere("it.nome LIKE :nomeInteressado");
            $queryBuilder->setParameter("nomeInteressado", "%" . $parametros[ConstanteParametros::CHAVE_NOME] . "%");
        }

        if ((isset($parametros[ConstanteParametros::CHAVE_TELEFONE]) === true)&&(empty($parametros[ConstanteParametros::CHAVE_TELEFONE]) === false)) {
            $queryBuilder->andWhere(
                $queryBuilder->expr()->orX(
                    $queryBuilder->expr()->like("it.telefone_contato", ":telefoneInteressado"),
                    $queryBuilder->expr()->like("it.telefone_secundario", ":telefoneInteressado")
                )
            );
            $queryBuilder->setParameter("telefoneInteressado", "%" . $parametros[ConstanteParametros::CHAVE_TELEFONE] . "%");
        }

        if ((isset($parametros[ConstanteParametros::CHAVE_EMAIL]) === true)&&(empty($parametros[ConstanteParametros::CHAVE_EMAIL]) === false)) {
            $queryBuilder->andWhere(
                $queryBuilder->expr()->orX(
                    $queryBuilder->expr()->like("it.email_contato", ":emailInteressado"),
                    $queryBuilder->expr()->like("it.email_secundario", ":emailInteressado")
                )
            );
            $queryBuilder->setParameter("emailInteressado", "%" . $parametros[ConstanteParametros::CHAVE_EMAIL] . "%");
        }

        if ((isset($parametros[ConstanteParametros::CHAVE_IDADE]) === true)&&(empty($parametros[ConstanteParametros::CHAVE_IDADE]) === false)) {
            $queryBuilder->andWhere("it.idade = :idadeInteressado");
            $queryBuilder->setParameter("idadeInteressado", $parametros[ConstanteParametros::CHAVE_IDADE]);
        }

        if ((isset($parametros[ConstanteParametros::CHAVE_GRAU_INTERESSE]) === true)&&(empty($parametros[ConstanteParametros::CHAVE_GRAU_INTERESSE]) === false)) {
            $queryBuilder->andWhere("it.grau_interesse IN (:grauInteresse)");
            $queryBuilder->setParameter("grauInteresse", $parametros[ConstanteParametros::CHAVE_GRAU_INTERESSE]);
        }

        if ((isset($parametros[ConstanteParametros::CHAVE_IDIOMA]) === true)&&(empty($parametros[ConstanteParametros::CHAVE_IDIOMA]) === false)) {
            $queryBuilder->andWhere("idi2.id = :idiomaInteressado");
            $queryBuilder->setParameter("idiomaInteressado", $parametros[ConstanteParametros::CHAVE_IDIOMA]);
        }

        if ((isset($parametros[ConstanteParametros::CHAVE_CONSULTOR]) === true)&&(empty($parametros[ConstanteParametros::CHAVE_CONSULTOR]) === false)) {
            $queryBuilder->andWhere(
                $queryBuilder->expr()->orX(
                    $queryBuilder->expr()->eq("cf.id", ":funcionarioId"),
                    $queryBuilder->expr()->eq("crf.id", ":funcionarioId")
                )
            );
            $queryBuilder->setParameter("funcionarioId", $parametros[ConstanteParametros::CHAVE_CONSULTOR]);
        }

        if ((isset($parametros[ConstanteParametros::CHAVE_TIPO_LEAD]) === true)&&(empty($parametros[ConstanteParametros::CHAVE_TIPO_LEAD]) === false)) {
            $queryBuilder->andWhere("it.tipo_lead IN (:tiposLead)");
            $queryBuilder->setParameter("tiposLead", $parametros[ConstanteParametros::CHAVE_TIPO_LEAD]);
        }

        if ((isset($parametros[ConstanteParametros::CHAVE_TIPO_CONTATO]) === true)&&(empty($parametros[ConstanteParametros::CHAVE_TIPO_CONTATO]) === false)) {
            $queryBuilder->andWhere("it.tipo_contato = :tipoContato");
            $queryBuilder->setParameter("tipoContato", $parametros[ConstanteParametros::CHAVE_TIPO_CONTATO]);
        }

        if ((isset($parametros[ConstanteParametros::CHAVE_TIPO_PROSPECCAO]) === true)&&(empty($parametros[ConstanteParametros::CHAVE_TIPO_PROSPECCAO]) === false)) {
            $queryBuilder->andWhere("it.tipo_prospeccao = :tipoProspeccao");
            $queryBuilder->setParameter("tipoProspeccao", $parametros[ConstanteParametros::CHAVE_TIPO_PROSPECCAO]);
        }

        if ((isset($parametros[ConstanteParametros::CHAVE_PERIODO_PRETENDIDO]) === true)&&(empty($parametros[ConstanteParametros::CHAVE_PERIODO_PRETENDIDO]) === false)) {
            $queryBuilder->andWhere("it.periodo_pretendido = :periodoPretendido");
            $queryBuilder->setParameter("periodoPretendido", $parametros[ConstanteParametros::CHAVE_PERIODO_PRETENDIDO]);
        }

        if ((isset($parametros[ConstanteParametros::CHAVE_WORKFLOW]) === true)&&(empty($parametros[ConstanteParametros::CHAVE_WORKFLOW]) === false)) {
            $queryBuilder->andWhere("wf.id = :workflow");
            $queryBuilder->setParameter("workflow", $parametros[ConstanteParametros::CHAVE_WORKFLOW]);
        }

        if ((isset($parametros[ConstanteParametros::CHAVE_MOTIVO_NAO_FECHAMENTO_MATRICULA]) === true)&&(empty($parametros[ConstanteParametros::CHAVE_MOTIVO_NAO_FECHAMENTO_MATRICULA]) === false)) {
            $queryBuilder->andWhere("mnf.id = :motivoNaoFechamento");
            $queryBuilder->setParameter("motivoNaoFechamento", $parametros[ConstanteParametros::CHAVE_MOTIVO_NAO_FECHAMENTO_MATRICULA]);
        }

        if ((isset($parametros[ConstanteParametros::CHAVE_SITUACAO]) === true)&&(empty($parametros[ConstanteParametros::CHAVE_SITUACAO]) === false)) {
            $queryBuilder->andWhere("it.situacao IN (:situacaoInteressado)");
            $queryBuilder->setParameter("situacaoInteressado", $parametros[ConstanteParametros::CHAVE_SITUACAO]);
        }

        $this->montaFiltroDatasHorarios($queryBuilder, $parametros);
    }

    /**
     * Realiza os filtros para o Funil de vendas
     *
     * @param \Doctrine\ORM\QueryBuilder $queryBuilder
     * @param array $parametros
     */
    protected function filtroFunilVendas(&$queryBuilder, $parametros)
    {
        $this->filtrarFranqueada($queryBuilder);
        $queryBuilder->andWhere("ac.situacao = :situacaoAgenda");
        $queryBuilder->setParameter("situacaoAgenda", SituacoesSistema::SITUACAO_NAO_CONCLUIDO);

        if ((isset($parametros[ConstanteParametros::CHAVE_DATA_AGENDAMENTO]) === true) && (empty($parametros[ConstanteParametros::CHAVE_DATA_AGENDAMENTO]) === false)) {
            $dataFormatada = explode("T", $parametros[ConstanteParametros::CHAVE_DATA_AGENDAMENTO]);
            $dataFormatada = $dataFormatada[0];
        } else {
            $dataAtual     = new \DateTime();
            $dataFormatada = $dataAtual->format("Y-m-d");
        }

        $queryBuilder->andWhere("ac.data_agendamento >= :dataAgendamentoIni");
        $queryBuilder->andWhere("ac.data_agendamento <= :dataAgendamentoFim");
        $queryBuilder->setParameter("dataAgendamentoIni", $dataFormatada . " 00:00:01");
        $queryBuilder->setParameter("dataAgendamentoFim", $dataFormatada . " 23:59:59");
    }

    /**
     * Realiza os filtros para o Funil de vendas
     *
     * @param \Doctrine\ORM\QueryBuilder $queryBuilder
     */
    protected function filtroFunilVendasAtrasado(&$queryBuilder)
    {
        $date    = new \DateTime();
        $dataYmd = $date->format("Y-m-d");
        $this->filtrarFranqueada($queryBuilder);
        $queryBuilder->andWhere("ac.situacao = :situacaoAgenda");
        $queryBuilder->andWhere("ac.data_agendamento <= :dataAgendamentoFim");
        $queryBuilder->setParameter("dataAgendamentoFim", $dataYmd . " 00:00:01");
        $queryBuilder->setParameter("situacaoAgenda", SituacoesSistema::SITUACAO_NAO_CONCLUIDO);
    }

    /**
     * Filtra os interessados por pagina aplicando o filtro
     *
     * @param array $parametros
     * @param number $numeroItensPorPagina
     *
     * @return \Knp\Component\Pager\Pagination\SlidingPagination
     */
    public function filtraInteressadoPorPagina($parametros, $numeroItensPorPagina=50)
    {
        $opcoes       = [];
        $queryBuilder = $this->montaQueryBase();
        $queryBuilder->addSelect("agc");
        $queryBuilder->leftJoin("it.agendaComerciais", "agc", "WITH", "agc.situacao = '" . SituacoesSistema::SITUACAO_NAO_CONCLUIDO . "'");
        $this->filtrarFranqueada($queryBuilder);
        $this->montaFiltros($queryBuilder, $parametros);
        if ((isset($parametros[ConstanteParametros::CHAVE_ORDENACAO_COLUNA]) === true)&&(is_null($parametros[ConstanteParametros::CHAVE_ORDENACAO_COLUNA]) === false)) {
            $queryBuilder->orderBy($parametros[ConstanteParametros::CHAVE_ORDENACAO_COLUNA], $parametros[ConstanteParametros::CHAVE_ORDENACAO_SORT]);
            $opcoes[\Knp\Component\Pager\Paginator::SORT_FIELD_PARAMETER_NAME] = "~";
            // $opcoes[\Knp\Component\Pager\Paginator::SORT_DIRECTION_PARAMETER_NAME] = "~";
            $opcoes['wrap-queries'] = true;
        }

        return \App\Helper\FunctionHelper::montaPaginatorPaginacao($queryBuilder, $parametros[ConstanteParametros::CHAVE_PAGINA], $numeroItensPorPagina, $opcoes);
    }

    /**
     * Filtra os Followups dos interessados por pagina aplicando os filtros
     *
     * @param array $parametros
     *
     * @return array
     */
    public function filtraFollowupInteressado($parametros, $numeroItensPorPagina=50)
    {
        $opcoes       = [];
        $queryBuilder = $this->createQueryBuilder("it");
        $queryBuilder->addSelect("fc");
        $queryBuilder->addSelect("wf");
        $queryBuilder->addSelect("agc");
        $queryBuilder->addSelect("al");
        $queryBuilder->addSelect("mtnf");
        $queryBuilder->addSelect("ctts");
        $queryBuilder->addSelect("consulFunc");
        $queryBuilder->addSelect("respVendaContrato");
        $queryBuilder->addSelect("tc");
        $queryBuilder->addSelect("tp");
        $queryBuilder->leftJoin("it.tipo_contato", "tc");
        $queryBuilder->leftJoin("it.tipo_prospeccao", "tp");
        $queryBuilder->join("it.followupComercials", "fc");
        $queryBuilder->leftJoin("it.workflow", "wf");
        $queryBuilder->leftJoin("it.agendaComerciais", "agc");
        $queryBuilder->leftJoin("agc.funcionario", "consulFunc");
        $queryBuilder->join("it.franqueada", "fran");
        $queryBuilder->leftJoin("it.aluno", "al");
        $queryBuilder->leftJoin("it.motivo_nao_fechamento", "mtnf");
        $queryBuilder->leftJoin("al.contratos", "ctts");
        $queryBuilder->leftJoin("ctts.responsavel_venda_funcionario", "respVendaContrato");
        $queryBuilder->addOrderBy("agc.id", "DESC");
        $queryBuilder->addOrderBy("fc.id", "DESC");
        $queryBuilder->andWhere("mtnf IS NULL");
        $queryBuilder->andWhere("it.situacao <> 'I'");

        if ((int) $parametros[ConstanteParametros::CHAVE_TIPO_FOLLOWUP_SELECIONADO] === 1) {
            $queryBuilder->andWhere("ctts IS NULL");
        }

        if ((int) $parametros[ConstanteParametros::CHAVE_TIPO_FOLLOWUP_SELECIONADO] === 3) {
            $queryBuilder->andWhere("ctts IS NOT NULL");
        }

        $this->filtrarFranqueada($queryBuilder);
        $this->montaFiltrosFollowups($queryBuilder, $parametros);

        if ((isset($parametros[ConstanteParametros::CHAVE_ORDENACAO_COLUNA]) === true)&&(is_null($parametros[ConstanteParametros::CHAVE_ORDENACAO_COLUNA]) === false)) {
            $queryBuilder->orderBy($parametros[ConstanteParametros::CHAVE_ORDENACAO_COLUNA], $parametros[ConstanteParametros::CHAVE_ORDENACAO_SORT]);
            $opcoes[\Knp\Component\Pager\Paginator::SORT_FIELD_PARAMETER_NAME] = "~";
            // $opcoes[\Knp\Component\Pager\Paginator::SORT_DIRECTION_PARAMETER_NAME] = "~";
        }
        
        $opcoes["wrap-queries"] = "true";
        return \App\Helper\FunctionHelper::montaPaginatorPaginacao($queryBuilder, $parametros[ConstanteParametros::CHAVE_PAGINA], $numeroItensPorPagina, $opcoes);
    }

    /**
     * Busca o registro pela chave primaria
     *
     * @param int $id
     *
     * @return array|NULL
     */
    public function buscaPorId($id, $parametros=[])
    {
        //  $queryBuilder = $this->montaQueryBase();
        // $queryBuilder->addSelect("agc");
        // $queryBuilder->leftJoin("it.agendaComerciais", "agc");
        // $this->filtrarFranqueada($queryBuilder);
        // $queryBuilder->andWhere("it.id = :id");
        // $queryBuilder->setParameter("id", $id);
        // return \App\Helper\FunctionHelper::retornaArrayNull($queryBuilder, true);
        $em = $this->getEntityManager();

        $sqlInteressado = "
            SELECT it.*, pi_.nome_contato as pessoa_indicou_nome, curso.descricao as curso_descricao, 
                consultor_funcionario.id consultor_funcionario_id, consultor_funcionario.apelido consultor_funcionario_apelido,
                consultor_responsavel_funcionario.id consultor_responsavel_funcionario_id, consultor_responsavel_funcionario.apelido consultor_responsavel_funcionario_apelido,
                pessoa.cnpj_cpf aluno_cnpj_cpf, tipo_contato.id tipo_contato_id, tipo_contato.nome tipo_contato_nome, tipo_contato.tipo tipo_contato_tipo,
                GROUP_CONCAT(interessado_idioma.idioma_id SEPARATOR ',') idiomas,
                workflow.descricao workflow_descricao, workflow.tipo_workflow workflow_tipo_workflow, workflow.situacao workflow_situacao
            FROM interessado it
            LEFT JOIN tipo_contato ON it.tipo_contato_id = tipo_contato.id
            LEFT JOIN curso ON it.curso_id = curso.id
            LEFT JOIN workflow ON it.workflow_id = workflow.id
            LEFT JOIN interessado_idioma ON it.id = interessado_idioma.interessado_id
            LEFT JOIN funcionario consultor_funcionario ON it.consultor_funcionario_id = consultor_funcionario.id
            LEFT JOIN funcionario consultor_responsavel_funcionario ON it.consultor_responsavel_funcionario_id = consultor_responsavel_funcionario.id
            LEFT JOIN aluno ON it.aluno_id = aluno.id
            LEFT JOIN pessoa ON aluno.pessoa_id = pessoa.id
            LEFT JOIN pessoa pi_ ON it.pessoa_indicou_id  = pi_.id
            WHERE it.id = $id
        ";



        $stmt = $em->getConnection()->prepare($sqlInteressado);
        $stmt->execute();
        $interessado = $stmt->fetch();

        $interessado_id = $interessado["id"];
        $sqlFollowUp = "
        SELECT flwup.*
        FROM followup_comercial flwup
        WHERE flwup.interessado_id = $interessado_id
        ";

        $stmt = $em->getConnection()->prepare($sqlFollowUp);
        $stmt->execute();
        $followUps = $stmt->fetchAll();

        $sqlNivelamentos = "
            SELECT atividade_extra.data_hora_inicio, atividade_extra.data_hora_fim, atividade_extra.situacao, livro.id livro_id, livro.descricao livro_descricao, tipo_item.tipo item_tipo
            FROM interessado_atividade_extra
            LEFT JOIN atividade_extra ON interessado_atividade_extra.atividade_extra_id = atividade_extra.id
            LEFT JOIN item ON atividade_extra.item_id = item.id
            LEFT JOIN tipo_item ON item.tipo_item_id = tipo_item.id
            LEFT JOIN livro ON interessado_atividade_extra.livro_id = livro.id
            WHERE interessado_atividade_extra.interessado_id = $id
        ";

        $stmt = $em->getConnection()->prepare($sqlNivelamentos);
        $stmt->execute();
        $nivelamentos = $stmt->fetchAll();

        $interessado['interessadoAtividadeExtras'] = $nivelamentos;
        $interessado['followupComercials'] = $followUps;

        return $interessado;
    }

     /**
      * Busca interessados por nome
      *
      * @param string $query nome a ser buscado
      *
      * @return array|NULL
      */
    public function buscaInteressadoPorNome ($query)
    {
        $queryBuilder = $this->montaQueryBase();
        $this->filtrarFranqueada($queryBuilder);
        $queryBuilder->andWhere("it.nome LIKE :nome");
        $queryBuilder->andWhere("it.situacao = :situacaoInteressado");
        $queryBuilder->setParameter("nome", "%$query%");
        $queryBuilder->setParameter("situacaoInteressado", SituacoesSistema::SITUACAO_ATIVO);
        $queryBuilder->orWhere(
            $queryBuilder->expr()->orX(
                $queryBuilder->expr()->like("SUBSTRING(it.telefone_contato, -4)", ":telefoneInteressado"),
                $queryBuilder->expr()->like("SUBSTRING(it.telefone_secundario, -4)", ":telefoneInteressado")
            )
        );
        $queryBuilder->setParameter("telefoneInteressado", "%" . $query . "%");
        return \App\Helper\FunctionHelper::retornaArrayNull($queryBuilder);
    }

    /**
     * Busca interessados por nome ou telefone primario ou telefone secundario
     *
     * @param string $query nome a ser buscado
     *
     * @return array|NULL
     */
    public function buscaInteressadoPorNomeOuTelefone ($query)
    {
        $queryBuilder = $this->montaQueryBase();
        $this->filtrarFranqueada($queryBuilder);

        $queryBuilder->andWhere(
            $queryBuilder->expr()->orX(
                $queryBuilder->expr()->like("it.telefone_contato", ":telefoneInteressado"),
                $queryBuilder->expr()->like("it.telefone_secundario", ":telefoneInteressado"),
                $queryBuilder->expr()->like("it.nome", ":nome")
            )
        );

        // Subquery pra não pegar interessado que já tenha contrato!
        $subQuery = $this->_em->createQueryBuilder();
        $subQuery->select('i');
        $subQuery->from(Interessado::class, 'i');
        $subQuery->join('i.aluno', 'a');
        $subQuery->join('a.contratos', 'c');
        $subQuery->where('c.situacao = :situacaoVigente');
        $subQuery->andWhere('i.id = it.id');
        $queryBuilder->andWhere($queryBuilder->expr()->not($queryBuilder->expr()->exists($subQuery->getDQL())));

        $queryBuilder->setParameter("nome", "%$query%");
        $queryBuilder->setParameter("telefoneInteressado", "%" . $query . "%");
        $queryBuilder->setParameter("situacaoVigente", SituacoesSistema::SITUACAO_CONTRATO_VIGENTE);

        return \App\Helper\FunctionHelper::retornaArrayNull($queryBuilder);
    }



    /**
     * Gera o filtro por usuário -> pode ser tanto o logado, quanto algum consultor específico
     *
     * @param \Doctrine\ORM\QueryBuilder $queryBuilder
     * @param int $usuarioId
     * @param array $parametros
     */
    private function filtroFunilVendasUsuario(&$queryBuilder, $usuarioId,$parametros=[])
    {
       
        $usuarioRepo      = $this->_em->getRepository(\App\Entity\Principal\Usuario::class);
        $usuarioLogadoOBJ = $usuarioRepo->find($usuarioId);
       
        
        //se passar um consultor como parametro filtra o conteúdo
        if (isset($parametros[ConstanteParametros::CHAVE_CONSULTOR_COMERCIAL]) === true && empty($parametros[ConstanteParametros::CHAVE_CONSULTOR_COMERCIAL]) === false) {            
            //usuario do consultor passado como parametro
            $usuario            = $usuarioRepo->find($parametros[ConstanteParametros::CHAVE_CONSULTOR_COMERCIAL]);
            // como um usuário pode ter acesso a varias escolas porém com permissoes diferentes
            $funcionariosLigadosAoUsuario = $usuario->getFuncionarios();
            foreach ($funcionariosLigadosAoUsuario as $funcionario) {
                if($funcionario->getUsuario()->getId() == $usuario->getId() && $funcionario->getFranqueada()->getId() == VariaveisCompartilhadas::$franqueadaID ){
                        $queryBuilder->andWhere("cf.id = :funcionarioLogadoId");
                        $queryBuilder->setParameter("funcionarioLogadoId", $funcionario->getId());
                }
    
            }
        }
        else{
            //usuario logado
            $usuario =  $usuarioRepo->find($usuarioId);       
            // como um usuário pode ter acesso a varias escolas porém com permissoes diferentes
            $funcionariosLigadosAoUsuario = $usuario->getFuncionarios();
            foreach ($funcionariosLigadosAoUsuario as $funcionario) {
                if($funcionario->getUsuario()->getId() == $usuario->getId() && $funcionario->getFranqueada()->getId() == VariaveisCompartilhadas::$franqueadaID ){
                    //verifica se o funcionario é gestor comercial, do contrario filtra apenas a agenda relacionada ao usuário
                    if(!((bool)$funcionario->getGestorComercial() === true)){
                        $queryBuilder->andWhere("f.id = :funcionarioLogadoId");
                        $queryBuilder->setParameter("funcionarioLogadoId", $funcionario->getId());
                    }
                    
                }
    
            }
        }
        

          

    

        //codigo antigo bugado deixei aqui caso o meu apresente alguma falha e precise de consulta

        // if ($funcionarioLogadoCollection->count() > 0) {
        //     $funcionarioLogadoOBJ = $funcionarioLogadoCollection->get(0);

        //     if ($funcionarioLogadoOBJ->getGestorComercial() === false) {
        //         $queryBuilder->andWhere("f.id = :funcionarioLogadoId");
        //         $queryBuilder->setParameter("funcionarioLogadoId", $funcionarioLogadoOBJ->getId());
        //     }
        // }

        // if (isset($parametros[ConstanteParametros::CHAVE_CONSULTOR_COMERCIAL]) === true && empty($parametros[ConstanteParametros::CHAVE_CONSULTOR_COMERCIAL]) === false) {
        //     $usuarioConsultorOBJ            = $usuarioRepo->find($parametros[ConstanteParametros::CHAVE_CONSULTOR_COMERCIAL]);
        //     $funcionarioConsultorCollection = $usuarioConsultorOBJ->getFuncionarios();

        //     if ($funcionarioConsultorCollection->count() > 0) {
        //         $funcionarioConsultorOBJ = $funcionarioConsultorCollection->get(0);

        //         $queryBuilder->andWhere("f.id = :funcionarioConsultorId");
        //         $queryBuilder->setParameter("funcionarioConsultorId", $funcionarioConsultorOBJ->getId());
        //     }
        // }
    }

    /**
     * Busca os interessados para o Funil de Vendas
     *
     * @param int $usuarioId
     * @param array $parametros
     *
     * @return array|NULL
     */
    public function buscaFunilVendas($usuarioId, $parametros=[])
    {
        $queryBuilder = $this->createQueryBuilder("it");
        $queryBuilder->addSelect("wf");
        $queryBuilder->addSelect("ac");
        // $queryBuilder->addSelect("fran");
        $queryBuilder->addSelect("cf");
        $queryBuilder->leftJoin("it.workflow", "wf");
        $queryBuilder->leftJoin("it.agendaComerciais", "ac");
        $queryBuilder->leftJoin("ac.funcionario", "f");
        $queryBuilder->leftJoin("it.franqueada", "fran");
        $queryBuilder->leftJoin("it.consultor_responsavel_funcionario", "cf");
        $this->filtroFunilVendas($queryBuilder, $parametros);
        $this->filtroFunilVendasUsuario($queryBuilder, $usuarioId, $parametros);

        return $queryBuilder->getQuery()->getArrayResult();
    }

    /**
     * Busca os interessados para o Funil de Vendas que estão atrasados
     *
     * @param int $usuarioId
     * @param array $parametros
     *
     * @return array|NULL
     */
    public function buscaFunilVendasAtrasado($usuarioId, $parametros=[])
    {
        $queryBuilder = $this->createQueryBuilder("it");
        $queryBuilder->addSelect("wf");
        $queryBuilder->addSelect("ac");
        // $queryBuilder->addSelect("fran");
        $queryBuilder->addSelect("cf");
        $queryBuilder->leftJoin("it.workflow", "wf");
        $queryBuilder->leftJoin("it.agendaComerciais", "ac");
        $queryBuilder->leftJoin("ac.funcionario", "f");
        $queryBuilder->leftJoin("it.franqueada", "fran");
        $queryBuilder->leftJoin("it.consultor_responsavel_funcionario", "cf");
        $this->filtroFunilVendasAtrasado($queryBuilder);
        $this->filtroFunilVendasUsuario($queryBuilder, $usuarioId, $parametros);

        return $queryBuilder->getQuery()->getArrayResult();
    }

    /**
     * Monta a query para ser executada no relatório
     *
     * @param array $parametros
     *
     * @return string
     */
    public function prepararDadosRelatorioInteressadosPeriodo($parametros)
    {
        $queryBuilder = $this->createQueryBuilder("i");
        $queryBuilder->select(
            [
                'i.id',
                'i.nome',
                'p2.nome_contato as consultor',
                'i.idade',
                'id.descricao as idioma',
                'l.descricao',
                'i.periodo_pretendido',
                'p.telefone_preferencial',
                'p.email_preferencial'
            ]
        );
        $queryBuilder->leftJoin('i.idiomas', 'id');
        $queryBuilder->leftJoin('i.interessadoAtividadeExtras', 'iae');
        $queryBuilder->leftJoin('iae.livro', 'l');
        $queryBuilder->leftJoin('i.aluno', 'a');
        $queryBuilder->leftJoin('a.pessoa', 'p');
        $queryBuilder->leftJoin('i.consultor_funcionario', 'c');
        $queryBuilder->leftJoin('c.pessoa', 'p2');
        $queryBuilder->andWhere("i.situacao = 'A'");
        $queryBuilder->andWhere('i.franqueada = :franqueada');
        $queryBuilder->setParameter('franqueada', VariaveisCompartilhadas::$franqueadaID);

        if (is_null($parametros[ConstanteParametros::CHAVE_PERIODO_PRETENDIDO]) === false) {
            $situacao = explode(',', $parametros[ConstanteParametros::CHAVE_PERIODO_PRETENDIDO]);
            $queryBuilder->andWhere("i.periodo_pretendido IN (:periodo)");
            $queryBuilder->setParameter('periodo', implode("', '", $situacao));
        }

        if (is_null($parametros[ConstanteParametros::CHAVE_IDIOMA]) === false) {
            $queryBuilder->andWhere('id = :idioma');
            $queryBuilder->setParameter('idioma', $parametros[ConstanteParametros::CHAVE_IDIOMA]);
        }

        if (is_null($parametros[ConstanteParametros::CHAVE_LIVRO]) === false) {
            $queryBuilder->andWhere('l = :livro');
            $queryBuilder->setParameter('livro', $parametros[ConstanteParametros::CHAVE_LIVRO]);
        }

        if(isset($parametros[ConstanteParametros::CHAVE_DATA_INICIAL])){
            $dataInicial = strtotime(str_replace("/", "-",$parametros[ConstanteParametros::CHAVE_DATA_INICIAL] . " 00:00:00"));
            $dataInicial = date('Y-m-d H:i:s', $dataInicial);
            $queryBuilder->andWhere('i.data_cadastro >= :filtro_data_inicial')
                ->setParameter('filtro_data_inicial', $dataInicial);
        }

        if(isset($parametros[ConstanteParametros::CHAVE_DATA_FINAL])){
            $dataFinal = strtotime(str_replace("/","-", $parametros[ConstanteParametros::CHAVE_DATA_FINAL] . " 23:59:59"));
            $dataFinal = date('Y-m-d H:i:s', $dataFinal);
            $queryBuilder->andWhere('i.data_cadastro <= :filtro_data_final')
                ->setParameter('filtro_data_final', $dataFinal);
        }

        return $queryBuilder->getQuery()->getResult();
    }

    /**
     * Monta a query para ser executada no relatório
     *
     * @param array $parametros
     *
     * @return string
     */
    public function prepararDadosRelatorioMatriculasPerdidas($parametros)
    {
        $queryBuilder = $this->createQueryBuilder("i")
            ->select([
                'i.id',
                "date_format(i.data_primeiro_atendimento, '%Y-%m-%d') as data_primeiro_atendimento",
                'i.nome',
                'i.telefone_contato',
                'i.email_contato',
                'p.nome_contato as funcionario',
                'c.descricao as curso'
            ])
            ->innerJoin('i.consultor_funcionario', 'f')
            ->innerJoin('f.pessoa', 'p')
            ->leftJoin('i.curso', 'c')
            ->andWhere('i.franqueada = :franqueada')
            ->setParameter('franqueada', VariaveisCompartilhadas::$franqueadaID)
            ->andWhere('i.data_matricula_perdida is not null');

        if(isset($parametros[ConstanteParametros::CHAVE_DATA_INICIAL])){
            $dataInicial = strtotime(str_replace("/", "-",$parametros[ConstanteParametros::CHAVE_DATA_INICIAL] . " 00:00:00"));
            $dataInicial = date('Y-m-d H:i:s', $dataInicial);
            $queryBuilder->andWhere('i.data_primeiro_atendimento >= :data_inicial')
                ->setParameter('data_inicial', $dataInicial);
        }

        if(isset($parametros[ConstanteParametros::CHAVE_DATA_FINAL])){
            $dataFinal = strtotime(str_replace("/","-", $parametros[ConstanteParametros::CHAVE_DATA_FINAL] . " 23:59:59"));
            $dataFinal = date('Y-m-d H:i:s', $dataFinal);
            $queryBuilder->andWhere('i.data_primeiro_atendimento <= :data_final')
                ->setParameter('data_final', $dataFinal);
        }

        $sql = $queryBuilder->getQuery()->getResult();

        return $sql;
    }



    /**
     * Busca todos interessados que tem o telefone passado cadastrado
     *
     * @param array $parametros
     *
     * @return array|null
     */
    public function buscarPorTelefone ($parametros)
    {
        $queryBuilder = $this->createQueryBuilder('i');
        $queryBuilder->select('i');
        $queryBuilder->join('i.franqueada', 'franqueada');
        $queryBuilder->where('franqueada = :franqueada');
        $queryBuilder->andWhere(
            $queryBuilder->expr()->orX(
                $queryBuilder->expr()->eq("i.telefone_contato", ":telefone"),
                $queryBuilder->expr()->eq("i.telefone_secundario", ":telefone")
            )
        );

        $queryBuilder->setParameter('franqueada', $parametros[ConstanteParametros::CHAVE_FRANQUEADA]);
        $queryBuilder->setParameter('telefone', $parametros[ConstanteParametros::CHAVE_TELEFONE]);
        $queryBuilder->distinct();

        return \App\Helper\FunctionHelper::retornaArrayNull($queryBuilder);

    }

    public function buscarRelatorioVisitas($parametros)
    {
        $queryBuilder = $this->createQueryBuilder('i')
            ->select([
                'i.nome as interessado',
                'f.apelido as consultor',
                "date_format(i.data_cadastro, '%Y-%m-%d') as data",
                'i.tipo_lead as tipo',
                'i.situacao',
                'p.nome_contato'
            ])
            ->join('i.consultor_funcionario', 'f')
            ->join('f.pessoa', 'p')
            ->where('i.franqueada = :franqueada')
            ->setParameter('franqueada', $parametros[ConstanteParametros::CHAVE_FRANQUEADA]);

        if(key_exists(ConstanteParametros::CHAVE_DATA_INICIAL, $parametros) && is_null($parametros[ConstanteParametros::CHAVE_DATA_INICIAL]) === false) {
            $dataInicial = strtotime(str_replace("/", "-",$parametros[ConstanteParametros::CHAVE_DATA_INICIAL] . " 00:00:00"));
            $dataInicial = date('Y-m-d H:i:s', $dataInicial);
            $queryBuilder->andWhere("i.data_cadastro >= :data_inicial");
            $queryBuilder->setParameter('data_inicial', $dataInicial);
        }
        if(key_exists(ConstanteParametros::CHAVE_DATA_FINAL, $parametros) && is_null($parametros[ConstanteParametros::CHAVE_DATA_FINAL]) === false) {
            $dataFinal = strtotime(str_replace("/", "-",$parametros[ConstanteParametros::CHAVE_DATA_FINAL] . " 23:59:59"));
            $dataFinal = date( 'Y-m-d H:i:s', $dataFinal);
            $queryBuilder->andWhere("i.data_cadastro <= :data_final");
            $queryBuilder->setParameter('data_final', $dataFinal);
        }
        if(key_exists(ConstanteParametros::CHAVE_CONSULTOR, $parametros) && is_null($parametros[ConstanteParametros::CHAVE_CONSULTOR]) === false) {
            $consultor = $parametros[ConstanteParametros::CHAVE_CONSULTOR];
            $queryBuilder->andWhere("f.id = :consultor");
            $queryBuilder->setParameter('consultor', $consultor);
        }
        if(key_exists(ConstanteParametros::CHAVE_TIPO_CONTATO, $parametros) && is_null($parametros[ConstanteParametros::CHAVE_TIPO_CONTATO]) === false) {
            $situacao = explode(',', $parametros[ConstanteParametros::CHAVE_TIPO_CONTATO]);
            $queryBuilder->andWhere("i.tipo_lead IN (:situacao)");
            $queryBuilder->setParameter('situacao', $situacao, \Doctrine\DBAL\Connection::PARAM_STR_ARRAY);
        }

        return $queryBuilder->getQuery()->getResult();
    }

    public function consultaDadosRelatorioRetornoConsultor($parametros)
    {
        $queryBuilder = $this->createQueryBuilder('i')
            ->select([
                'i.nome',
                "CASE WHEN i.aluno IS NOT NULL THEN 1 ELSE 0 END as efetivo",
                "date_format(i.data_cadastro, '%Y-%m-%d') as data_cadastro",
                'p.nome_contato',
                "i.situacao",
                "i.tipo_lead"
            ])
            ->leftJoin('i.consultor_funcionario', 'c')
            ->leftJoin('c.pessoa', 'p')
            ->andWhere('i.franqueada = :franqueada')
            ->setParameter('franqueada', $parametros[ConstanteParametros::CHAVE_FRANQUEADA]);
        
        if(key_exists(ConstanteParametros::CHAVE_DATA_INICIAL, $parametros) && is_null($parametros[ConstanteParametros::CHAVE_DATA_INICIAL]) === false) {
            $dataInicial = strtotime(str_replace("/", "-",$parametros[ConstanteParametros::CHAVE_DATA_INICIAL] . " 00:00:00"));
            $dataInicial = date('Y-m-d H:i:s', $dataInicial);
            $queryBuilder->andWhere("i.data_cadastro >= :data_inicial");
            $queryBuilder->setParameter('data_inicial', $dataInicial);
        }
        if(key_exists(ConstanteParametros::CHAVE_DATA_FINAL, $parametros) && is_null($parametros[ConstanteParametros::CHAVE_DATA_FINAL]) === false) {
            $dataFinal = strtotime(str_replace("/", "-",$parametros[ConstanteParametros::CHAVE_DATA_FINAL] . " 23:59:59"));
            $dataFinal = date( 'Y-m-d H:i:s', $dataFinal);
            $queryBuilder->andWhere("i.data_cadastro <= :data_final");
            $queryBuilder->setParameter('data_final', $dataFinal);
        }
        if(key_exists(ConstanteParametros::CHAVE_CONSULTOR, $parametros) && is_null($parametros[ConstanteParametros::CHAVE_CONSULTOR]) === false) {
            $consultor = $parametros[ConstanteParametros::CHAVE_CONSULTOR];
            $queryBuilder->andWhere("c.id = :consultor");
            $queryBuilder->setParameter('consultor', $consultor);
        }
        if(key_exists(ConstanteParametros::CHAVE_TIPO_CONTATO, $parametros) && is_null($parametros[ConstanteParametros::CHAVE_TIPO_CONTATO]) === false) {
            $tipoLead = $parametros[ConstanteParametros::CHAVE_TIPO_CONTATO];
            $queryBuilder->andWhere("i.tipo_lead IN (:tipo_lead)");
            $queryBuilder->setParameter('tipo_lead', $tipoLead);
        }

        return $queryBuilder->getQuery()->getResult();
    }

    public function gerarDadosRelatorioContatos($parametros)
    {
        $queryBuilder = $this->createQueryBuilder('i')
            ->select([
                "i.nome as interessado",
                "i.situacao",
                "i.tipo_lead",
                "date_format(i.data_cadastro, '%Y-%m-%d') as data_cadastro",
                "p.nome_contato as consultor",
                "tp.descricao as prospeccao",
                "tc.nome as contato"
            ])
            ->leftJoin('i.consultor_funcionario', 'c')
            ->leftJoin('c.pessoa', 'p')
            ->leftJoin('i.tipo_prospeccao', 'tp')
            ->leftJoin('i.tipo_contato', 'tc')
            ->andWhere("i.franqueada = :franqueada")
            ->setParameter("franqueada", $parametros[ConstanteParametros::CHAVE_FRANQUEADA]);
        
        if(key_exists(ConstanteParametros::CHAVE_DATA_INICIAL, $parametros) && is_null($parametros[ConstanteParametros::CHAVE_DATA_INICIAL]) === false) {
            $dataInicial = strtotime(str_replace("/", "-",$parametros[ConstanteParametros::CHAVE_DATA_INICIAL] . " 00:00:00"));
            $dataInicial = date('Y-m-d H:i:s', $dataInicial);
            $queryBuilder->andWhere("i.data_cadastro >= :data_inicial");
            $queryBuilder->setParameter('data_inicial', $dataInicial);
        }
        if(key_exists(ConstanteParametros::CHAVE_DATA_FINAL, $parametros) && is_null($parametros[ConstanteParametros::CHAVE_DATA_FINAL]) === false) {
            $dataFinal = strtotime(str_replace("/", "-",$parametros[ConstanteParametros::CHAVE_DATA_FINAL] . " 23:59:59"));
            $dataFinal = date( 'Y-m-d H:i:s', $dataFinal);
            $queryBuilder->andWhere("i.data_cadastro <= :data_final");
            $queryBuilder->setParameter('data_final', $dataFinal);
        }
        if(key_exists(ConstanteParametros::CHAVE_CONSULTOR, $parametros) && is_null($parametros[ConstanteParametros::CHAVE_CONSULTOR]) === false) {
            $consultor = $parametros[ConstanteParametros::CHAVE_CONSULTOR];
            $queryBuilder->andWhere("c.id = :consultor");
            $queryBuilder->setParameter('consultor', $consultor);
        }
        if(key_exists(ConstanteParametros::CHAVE_TIPO_CONTATO, $parametros) && is_null($parametros[ConstanteParametros::CHAVE_TIPO_CONTATO]) === false) {
            $tipoContato = $parametros[ConstanteParametros::CHAVE_TIPO_CONTATO];
            $queryBuilder->andWhere("i.tipo_contato = (:tipo_contato)");
            $queryBuilder->setParameter('tipo_contato', $tipoContato);
        }
        if(key_exists(ConstanteParametros::CHAVE_TIPO_PROSPECCAO, $parametros) && is_null($parametros[ConstanteParametros::CHAVE_TIPO_PROSPECCAO]) === false) {
            $tipoProspeccao = $parametros[ConstanteParametros::CHAVE_TIPO_PROSPECCAO];
            $queryBuilder->andWhere("i.tipo_prospeccao = (:tipo_prospeccao)");
            $queryBuilder->setParameter('tipo_prospeccao', $tipoProspeccao);
        }
        if(key_exists(ConstanteParametros::CHAVE_TIPO_LEAD, $parametros) && is_null($parametros[ConstanteParametros::CHAVE_TIPO_LEAD]) === false) {
            $tipoLead = $parametros[ConstanteParametros::CHAVE_TIPO_LEAD];
            $queryBuilder->andWhere("i.tipo_lead = (:tipo_lead)");
            $queryBuilder->setParameter('tipo_lead', $tipoLead);
        }

        return $queryBuilder->getQuery()->getResult();
    }

    public function gerarDadosRelatorioConsultaDesistencias($parametros)
    {
        
        $sql = "SELECT
                    p.nome_contato AS consultor,
                    SUM(CASE WHEN i.situacao = 'C' THEN 1 ELSE 0 END) AS alunos_na_carteira,
                    SUM(CASE WHEN i.situacao = 'P' THEN 1 ELSE 0 END) AS desistencias,
                    (SUM(CASE WHEN i.situacao = 'C' THEN 1 ELSE 0 END) * 100.0 / (SUM(CASE WHEN i.situacao = 'C' THEN 1 ELSE 0 END) + SUM(CASE WHEN i.situacao = 'P' THEN 1 ELSE 0 END))) AS retencao_percentual,
                    (SUM(CASE WHEN i.situacao = 'P' THEN 1 ELSE 0 END) * 100.0 / (SUM(CASE WHEN i.situacao = 'C' THEN 1 ELSE 0 END) + SUM(CASE WHEN i.situacao = 'P' THEN 1 ELSE 0 END))) AS evasao_percentual
                FROM
                    interessado i
                INNER JOIN funcionario f ON i.consultor_responsavel_funcionario_id = f.id
                INNER JOIN pessoa p ON f.pessoa_id = p.id
                WHERE
                    i.franqueada_id = {$parametros[ConstanteParametros::CHAVE_FRANQUEADA]}";

                if(isset($parametros[ConstanteParametros::CHAVE_CONSULTOR_RESPONSAVEL_FUNCIONARIO])) {
                    $sql .= " and i.consultor_responsavel_funcionario_id = {$parametros[ConstanteParametros::CHAVE_CONSULTOR_RESPONSAVEL_FUNCIONARIO]} ";
                }

                if(isset($parametros[ConstanteParametros::CHAVE_DATA_FINAL])) {
                    $dataInicial = strtotime(str_replace("/", "-",$parametros[ConstanteParametros::CHAVE_DATA_INICIAL] . " 00:00:00"));
                    $dataInicial = date('Y-m-d H:i:s', $dataInicial);
                    $sql .= " and i.data_cadastro >= '{$dataInicial}' "; 
                }

                if(isset($parametros[ConstanteParametros::CHAVE_DATA_FINAL])) {
                    $dataFinal = strtotime(str_replace("/", "-",$parametros[ConstanteParametros::CHAVE_DATA_FINAL] . " 23:59:59"));
                    $dataFinal = date('Y-m-d H:i:s', $dataFinal);
                     $sql .= " and i.data_cadastro <= '{$dataFinal}' "; 
                }

                $sql .= " GROUP BY  p.nome_contato ";  

                $sql .= " HAVING
                     SUM(CASE WHEN i.situacao = 'C' THEN 1 ELSE 0 END) + SUM(CASE WHEN i.situacao = 'P' THEN 1 ELSE 0 END) > 0";
             
                $result = $this->registry->getConnection()->fetchAllAssociative($sql);

                return $result;
    }

    public function gerarDadosRelatorioConsultaConversao($parametros) {
        $queryBuilder = $this->createQueryBuilder('i')
            ->select([
                "i.nome as interessado",
                "CASE WHEN i.aluno IS NOT NULL THEN '1' ELSE '0' END as conversao",
                "i.situacao",
                "i.tipo_lead",
                "date_format(i.data_cadastro, '%Y-%m-%d') as data_cadastro",
                "p.nome_contato as consultor",
                "tp.descricao as prospeccao",
                "tp_pai.descricao as prospeccao_pai",
                "tc.nome as contato"
            ])
            ->leftJoin('i.consultor_funcionario', 'c')
            ->leftJoin('c.pessoa', 'p')
            ->leftJoin('i.tipo_prospeccao', 'tp')
            ->leftJoin('tp.tipo_pai_tipo_prospeccao', 'tp_pai')
            ->leftJoin('i.tipo_contato', 'tc')
            ->andWhere("i.franqueada = :franqueada")
            ->setParameter("franqueada", $parametros[ConstanteParametros::CHAVE_FRANQUEADA]);
        
        if(isset($parametros[ConstanteParametros::CHAVE_DATA_INICIAL])) {
            $dataInicial = strtotime(str_replace("/", "-",$parametros[ConstanteParametros::CHAVE_DATA_INICIAL] . " 00:00:00"));
            $dataInicial = date('Y-m-d H:i:s', $dataInicial);
            $queryBuilder->andWhere("i.data_cadastro >= :data_inicial");
            $queryBuilder->setParameter('data_inicial', $dataInicial);
        }
        if(isset($parametros[ConstanteParametros::CHAVE_DATA_FINAL])) {
            $dataFinal = strtotime(str_replace("/", "-",$parametros[ConstanteParametros::CHAVE_DATA_FINAL] . " 23:59:59"));
            $dataFinal = date( 'Y-m-d H:i:s', $dataFinal);
            $queryBuilder->andWhere("i.data_cadastro <= :data_final");
            $queryBuilder->setParameter('data_final', $dataFinal);
        }
        if(key_exists(ConstanteParametros::CHAVE_CONSULTOR, $parametros) && is_null($parametros[ConstanteParametros::CHAVE_CONSULTOR]) === false) {
            $consultor = $parametros[ConstanteParametros::CHAVE_CONSULTOR];
            $queryBuilder->andWhere("c.id = :consultor");
            $queryBuilder->setParameter('consultor', $consultor);
        }
        if(key_exists(ConstanteParametros::CHAVE_TIPO_CONTATO, $parametros) && is_null($parametros[ConstanteParametros::CHAVE_TIPO_CONTATO]) === false) {
            $tipoContato = $parametros[ConstanteParametros::CHAVE_TIPO_CONTATO];
            $queryBuilder->andWhere("i.tipo_contato = (:tipo_contato)");
            $queryBuilder->setParameter('tipo_contato', $tipoContato);
        }
        if(key_exists(ConstanteParametros::CHAVE_TIPO_PROSPECCAO, $parametros) && is_null($parametros[ConstanteParametros::CHAVE_TIPO_PROSPECCAO]) === false) {
            $tipoProspeccao = $parametros[ConstanteParametros::CHAVE_TIPO_PROSPECCAO];
            $queryBuilder->andWhere("i.tipo_prospeccao = (:tipo_prospeccao)");
            $queryBuilder->setParameter('tipo_prospeccao', $tipoProspeccao);
        }
        if(key_exists(ConstanteParametros::CHAVE_TIPO_LEAD, $parametros) && is_null($parametros[ConstanteParametros::CHAVE_TIPO_LEAD]) === false) {
            $tipoLead = $parametros[ConstanteParametros::CHAVE_TIPO_LEAD];
            $queryBuilder->andWhere("i.tipo_lead LIKE :tipo_lead");
            $queryBuilder->setParameter('tipo_lead', $tipoLead);
        }

        return $queryBuilder->getQuery()->getResult();
    }

    public function gerarDadosRelatorioProspeccao($parametros){
        $queryBuilder = $this->createQueryBuilder('interessado')
            ->select([
                'interessado.tipo_lead',
                'CASE WHEN aluno.id IS NULL THEN 0 ELSE 1 END as convertido',
                'contato.nome as tipo_contato',
                'workflow.descricao as nome_workflow',
                'prospeccao.descricao as nome_prospeccao',
                "date_format(interessado.data_cadastro, '%Y-%m-%d') as data_cadastro",
                "interessado.nome",
                'interessado.idade',
                'interessado.telefone_contato',
                'interessado.grau_interesse',
                'interessado.email_contato',
                'consultor_pes.nome_contato',
            ])
            ->leftJoin('interessado.consultor_funcionario','consultor')
            ->leftJoin('interessado.tipo_prospeccao', 'prospeccao')
            ->leftJoin('interessado.workflow', 'workflow')
            ->leftJoin('prospeccao.tipo_pai_tipo_prospeccao', 'prospeccao_pai')
            ->leftJoin('interessado.tipo_contato', 'contato')
            ->leftJoin('consultor.pessoa', 'consultor_pes')
            ->leftJoin('interessado.aluno', 'aluno')
            ->where('interessado.franqueada = :franqueada')
            ->setParameter('franqueada', VariaveisCompartilhadas::$franqueadaID)
            ->andWhere('interessado.tipo_lead IS NOT NULL');
        
        if(isset($parametros[ConstanteParametros::CHAVE_DATA_INICIAL])) {
            $dataInicial = strtotime(str_replace("/", "-",$parametros[ConstanteParametros::CHAVE_DATA_INICIAL] . " 00:00:00"));
            $dataInicial = date('Y-m-d H:i:s', $dataInicial);
            $queryBuilder->andWhere("interessado.data_cadastro >= :data_inicial");
            $queryBuilder->setParameter('data_inicial', $dataInicial);
        }

        if(isset($parametros[ConstanteParametros::CHAVE_DATA_FINAL])) {
            $dataFinal = strtotime(str_replace("/", "-",$parametros[ConstanteParametros::CHAVE_DATA_FINAL] . " 23:59:59"));
            $dataFinal = date( 'Y-m-d H:i:s', $dataFinal);
            $queryBuilder->andWhere("interessado.data_cadastro <= :data_final");
            $queryBuilder->setParameter('data_final', $dataFinal);
        }

        if(isset($parametros[ConstanteParametros::CHAVE_GRAU_INTERESSE])) {
            $queryBuilder->andWhere('interessado.grau_interesse = :grau_interesse')
                ->setParameter('grau_interesse', $parametros[ConstanteParametros::CHAVE_GRAU_INTERESSE]);
        }

        if(isset($parametros[ConstanteParametros::CHAVE_WORKFLOW])) {
            $queryBuilder->andWhere('interessado.workflow = :filtro_workflow')
                ->setParameter('filtro_workflow', $parametros[ConstanteParametros::CHAVE_WORKFLOW]);
        }

        if(isset($parametros[ConstanteParametros::CHAVE_TIPO_CONTATO])) {
            $queryBuilder->andWhere('interessado.tipo_lead LIKE :filtro_tipo_lead')
                ->setParameter('filtro_tipo_lead', $parametros[ConstanteParametros::CHAVE_TIPO_CONTATO]);
            if(isset($parametros[ConstanteParametros::CHAVE_TIPO_PROSPECCAO])) {
                if($parametros[ConstanteParametros::CHAVE_TIPO_CONTATO] == 'A') {
                    $queryBuilder->andWhere('interessado.tipo_prospeccao = :filtro_tipo_contato')
                        ->setParameter('filtro_tipo_contato', $parametros[ConstanteParametros::CHAVE_TIPO_PROSPECCAO]);
                } else {
                    $queryBuilder->andWhere('interessado.tipo_contato = :filtro_tipo_contato')
                        ->setParameter('filtro_tipo_contato', $parametros[ConstanteParametros::CHAVE_TIPO_PROSPECCAO]);
                }
            }
        }
        
        return $queryBuilder->getQuery()->getResult();
    }

    /**
     * Método para consultar os dados requisitados no Relatório de FollowUp
     * 
     * @param array $filtros
     */
    public function gerarDadosRelatorioFollowupComercial($filtros)
    {
        $sql = "SELECT 
                    followup_comercial.id AS  id_followup,
                    interessado.nome AS interessado, 
                    tipo_contato.nome AS tipo_contato,
                    interessado.telefone_contato AS telefone_contato,
                    interessado.telefone_secundario AS telefone_secundario,
                    interessado.email_contato AS email_contato,  
                    interessado.email_secundario AS email_secundario,
                    workflow.descricao AS estapa_funil,
                    interessado.situacao AS situacao, 
                    idioma.descricao AS idioma, 
                    curso.descricao AS curso, 
                    agenda_comercial.data_agendamento AS data_agendamento
                FROM influx_crm_prod.interessado as interessado
                INNER JOIN followup_comercial AS followup_comercial ON followup_comercial.interessado_id = interessado.id
                LEFT JOIN agenda_comercial AS agenda_comercial ON followup_comercial.agenda_comercial_id = agenda_comercial.id
                LEFT JOIN workflow AS workflow ON workflow.id = interessado.workflow_id
                LEFT JOIN curso AS curso ON interessado.curso_id = curso.id
                LEFT JOIN idioma AS idioma ON curso.idioma_id = idioma.id
                LEFT JOIN tipo_contato AS tipo_contato ON interessado.tipo_contato_id = tipo_contato.id
                WHERE interessado.franqueada_id = {$filtros[ConstanteParametros::CHAVE_FRANQUEADA]}";

        $sql = $this->montaFiltrosRelatorioFollowups($sql, $filtros);
        
        $orderBy = " ORDER BY interessado.nome";
        
        $sql .= $orderBy;

        $result = $this->registry->getConnection()->fetchAllAssociative($sql);

        return $result;
    }

    /**
     * Método para montar filtros do relatório de FollowUpComercial
     * @param QueryBuilder $queryBuilder - Variável é alterada na origem onde é chamado o método
     * @param array $filtros
     * 
     * @return void
     */
    protected function montaFiltrosRelatorioFollowups($sql, $filtros)
    {   
        $where = "";
        // Sempre verificar essa condicional quando adicionar novos filtros ou remover filtros
        // No caso de nenhum filtro ser selecionado essa condicional é atendida, afim de forçar filtros e dessa forma diminuir o volume de dados retornados ao front-end
        if((empty($filtros[ConstanteParametros::CHAVE_DATA_CADASTRO_DE])&&empty($filtros[ConstanteParametros::CHAVE_DATA_CADASTRO_ATE])) && (empty($filtros[ConstanteParametros::CHAVE_DATA_PROXIMO_CONTATO_DE])&&empty($filtros[ConstanteParametros::CHAVE_DATA_PROXIMO_CONTATO_ATE])) && (empty($filtros[ConstanteParametros::CHAVE_SITUACAO_INTERESSADO])) && (empty($filtros[ConstanteParametros::CHAVE_SITUACAO_CONTRATO])) && (empty($filtros[ConstanteParametros::CHAVE_TIPO_LEAD]))){

            $dataCadastroDe = ((new \DateTime())->modify("-1 month")->setTime(0,0,0))->format("Y-m-d H:i:s");
            $dataCadastroAte = ((new \DateTime())->setTime(23,59,59))->format("Y-m-d H:i:s");
            $where .= " AND followup_comercial.data_registro >= '$dataCadastroDe'";
            $where .= " AND followup_comercial.data_registro <= '$dataCadastroAte'";
            
        }
        
        // OK
        if ((isset($filtros[ConstanteParametros::CHAVE_DATA_CADASTRO_DE])) && (!empty($filtros[ConstanteParametros::CHAVE_DATA_CADASTRO_DE]))) {
            $dataCadastroDe = (new \DateTime(str_replace("/", "-", $filtros[ConstanteParametros::CHAVE_DATA_CADASTRO_DE])))->setTime(0,0,0)->format("Y-m-d H:i:s");
            $where .= " AND followup_comercial.data_registro >= '$dataCadastroDe'";
        } 
        
        // OK
        if ((isset($filtros[ConstanteParametros::CHAVE_DATA_CADASTRO_ATE])) && (!empty($filtros[ConstanteParametros::CHAVE_DATA_CADASTRO_ATE]))) {
            $dataCadastroAte = (new \DateTime(str_replace("/", "-",$filtros[ConstanteParametros::CHAVE_DATA_CADASTRO_ATE])))->setTime(23.59,59)->format("Y-m-d H:i:s");
            $where .= " AND followup_comercial.data_registro <= '$dataCadastroAte'";
        }
        
        // OK
        if ((isset($filtros[ConstanteParametros::CHAVE_DATA_PROXIMO_CONTATO_DE])) && (!empty($filtros[ConstanteParametros::CHAVE_DATA_PROXIMO_CONTATO_DE]))) {
            $dataProximoContatoDe = (new \DateTime(str_replace("/", "-", $filtros[ConstanteParametros::CHAVE_DATA_PROXIMO_CONTATO_DE])))->setTime(0,0,0)->format("Y-m-d H:i:s");
            $where .= " AND agenda_comercial.data_agendamento >= '$dataProximoContatoDe'";
            
        }
        
        // OK
        if ((isset($filtros[ConstanteParametros::CHAVE_DATA_PROXIMO_CONTATO_ATE])) && (!empty($filtros[ConstanteParametros::CHAVE_DATA_PROXIMO_CONTATO_ATE]))) {
            $dataProximoContatoAte = (new \DateTime(str_replace("/", "-", $filtros[ConstanteParametros::CHAVE_DATA_PROXIMO_CONTATO_ATE])))->setTime(23.59,59)->format("Y-m-d H:i:s");
            $where .= " AND agenda_comercial.data_agendamento >= '$dataProximoContatoAte'";
        }

        
        // OK
        if ((isset($filtros[ConstanteParametros::CHAVE_SITUACAO_INTERESSADO])) && (!(empty($filtros[ConstanteParametros::CHAVE_SITUACAO_INTERESSADO])))) {
            $situacoes = implode("','", $filtros[ConstanteParametros::CHAVE_SITUACAO_INTERESSADO]);
            $where .= " AND interessado.situacao IN ('{$situacoes}')";
        }
        
        //OK
        if((isset($filtros[ConstanteParametros::CHAVE_TIPO_LEAD])) && (!(empty($filtros[ConstanteParametros::CHAVE_TIPO_LEAD])))){
            $tipo_lead = implode("','", $filtros[ConstanteParametros::CHAVE_TIPO_LEAD]);
            $where .= " AND interessado.tipo_lead IN ('{$tipo_lead}')";

        }
        // FILTRO AINDA NÃO SOLICITADO PARA USO NO FRONT, MAS FILTRAGEM ATRAVÉS DO BACK-END JÁ DISPONÍVEL
        if ((isset($filtros[ConstanteParametros::CHAVE_SITUACAO_CONTRATO])) && (!(empty($filtros[ConstanteParametros::CHAVE_SITUACAO_CONTRATO])))) {
            $situacoes = implode("','", $filtros[ConstanteParametros::CHAVE_SITUACAO_CONTRATO]);
            $where .= " AND  IN ('{$situacoes}')";
        }
          
        // FILTRO AINDA NÃO SOLICITADO PARA USO NO FRONT, MAS FILTRAGEM ATRAVÉS DO BACK-END JÁ DISPONÍVEL
            if ((isset($filtros[ConstanteParametros::CHAVE_DATA_TERMINO_CONTRATO_DE])) && (!empty($filtros[ConstanteParametros::CHAVE_DATA_TERMINO_CONTRATO_DE]))) {
                $dataTerminoContratoDe = explode("T", $filtros[ConstanteParametros::CHAVE_DATA_TERMINO_CONTRATO_DE]);
            }

            // FILTRO AINDA NÃO SOLICITADO PARA USO NO FRONT, MAS FILTRAGEM ATRAVÉS DO BACK-END JÁ DISPONÍVEL
            if ((isset($filtros[ConstanteParametros::CHAVE_DATA_TERMINO_CONTRATO_ATE])) && (!empty($filtros[ConstanteParametros::CHAVE_DATA_TERMINO_CONTRATO_ATE]))) {
                $dataTerminoContratoAte = explode("T", $filtros[ConstanteParametros::CHAVE_DATA_TERMINO_CONTRATO_ATE]);
            }


            // FILTRO AINDA NÃO SOLICITADO PARA USO NO FRONT, MAS FILTRAGEM ATRAVÉS DO BACK-END JÁ DISPONÍVEL
            if ((isset($filtros[ConstanteParametros::CHAVE_GRAU_INTERESSE])) && (!(empty($filtros[ConstanteParametros::CHAVE_GRAU_INTERESSE])))) {
            }
            
            
            // FILTRO AINDA NÃO SOLICITADO PARA USO NO FRONT, MAS FILTRAGEM ATRAVÉS DO BACK-END JÁ DISPONÍVEL
            if ((isset($filtros[ConstanteParametros::CHAVE_SITUACAO_ALUNO])) && (!(empty($filtros[ConstanteParametros::CHAVE_SITUACAO_ALUNO])))) {
            }

            // FILTRO AINDA NÃO SOLICITADO PARA USO NO FRONT, MAS FILTRAGEM ATRAVÉS DO BACK-END JÁ DISPONÍVEL
            if ((isset($filtros[ConstanteParametros::CHAVE_INTERESSADO]))&&(!empty($filtros[ConstanteParametros::CHAVE_INTERESSADO]))) {
            }

            // FILTRO AINDA NÃO SOLICITADO PARA USO NO FRONT, MAS FILTRAGEM ATRAVÉS DO BACK-END JÁ DISPONÍVEL
            if ((isset($filtros[ConstanteParametros::CHAVE_CONSULTOR_RESPONSAVEL_FUNCIONARIO]))&&(!empty($filtros[ConstanteParametros::CHAVE_CONSULTOR_RESPONSAVEL_FUNCIONARIO]))) {
            }

            // FILTRO AINDA NÃO SOLICITADO PARA USO NO FRONT, MAS FILTRAGEM ATRAVÉS DO BACK-END JÁ DISPONÍVEL
            if ((isset($filtros[ConstanteParametros::CHAVE_RESPONSAVEL_VENDA_FUNCIONARIO]))&&(!empty($filtros[ConstanteParametros::CHAVE_RESPONSAVEL_VENDA_FUNCIONARIO]))) {
            }

            
        return $sql.$where;
        
    }
}
