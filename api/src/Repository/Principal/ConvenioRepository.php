<?php

namespace App\Repository\Principal;

use App\Entity\Principal\Convenio;
use App\Entity\Principal\Franqueada;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Symfony\Bridge\Doctrine\RegistryInterface;
use App\Helper\ConstanteParametros;
use App\Helper\VariaveisCompartilhadas;
use App\Helper\SituacoesSistema;

/**
 * @method Convenio|null find($id, $lockMode = null, $lockVersion = null)
 * @method Convenio|null findOneBy(array $criteria, array $orderBy = null)
 * @method Convenio[]    findAll()
 * @method Convenio[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class ConvenioRepository extends ServiceEntityRepository
{


    public function __construct(RegistryInterface $registry)
    {
        parent::__construct($registry, Convenio::class);
    }

    /**
     * Monta query principal
     *
     * @return \Doctrine\ORM\QueryBuilder
     */
    protected function montaQueryBase()
    {
        $queryBuilder = $this->createQueryBuilder("conv");
        $queryBuilder->addSelect("fran");
        $queryBuilder->addSelect("p");
        $queryBuilder->addSelect("cid");
        $queryBuilder->addSelect("est");
        $queryBuilder->addSelect("ec");
        $queryBuilder->addSelect("sec");
        $queryBuilder->addSelect("mnfc");
        $queryBuilder->addSelect("cf");
        $queryBuilder->addSelect("npf");
        $queryBuilder->addSelect("flw");
        $queryBuilder->leftJoin("conv.franqueada", "fran");
        $queryBuilder->leftJoin("conv.pessoa", "p");
        $queryBuilder->leftJoin("p.cidade", "cid");
        $queryBuilder->leftJoin("p.estado", "est");
        $queryBuilder->leftJoin("conv.etapas_convenio", "ec");
        $queryBuilder->leftJoin("conv.segmento_empresa_convenio", "sec");
        $queryBuilder->leftJoin("conv.motivo_nao_fechamento_convenio", "mnfc");
        $queryBuilder->leftJoin("conv.consultor_funcionario", "cf");
        $queryBuilder->leftJoin("conv.negociacao_parceria_workflow", "npf");
        $queryBuilder->leftJoin("conv.followupConvenios", "flw");
        return $queryBuilder;
    }


    /**
     * Busca todos convenios da franqueada informada
     *
     * @param String $nome
     *
     * @return array
     */
    public function buscarEmpresaPorNome ($nome)
    {
        $queryBuilder = $this->createQueryBuilder('conv');
        $queryBuilder->select('conv,pessoa');
        $queryBuilder->join('conv.franqueada', 'franqueada');
        $queryBuilder->join('conv.pessoa', 'pessoa');

        $queryBuilder->where('pessoa.nome_fantasia LIKE :nome');
        $queryBuilder->andWhere('franqueada = :franqueada');

        $queryBuilder->setParameter('nome', "%$nome%");
        $queryBuilder->setParameter('franqueada', VariaveisCompartilhadas::$franqueadaID);

        $queryBuilder->distinct();

        return \App\Helper\FunctionHelper::retornaArrayNull($queryBuilder);

    }

    /**
     * Query para realizar fitlro de franqueada
     *
     * @param \Doctrine\ORM\QueryBuilder $queryBuilder
     */
    private function filtrarFranqueada(&$queryBuilder, $parametros=[])
    {
        if ((isset($parametros[ConstanteParametros::CHAVE_USUARIO_FRANQUEADORA]) === false)||(((bool) $parametros[ConstanteParametros::CHAVE_USUARIO_FRANQUEADORA]) === false)) {
            $queryBuilder->andWhere('conv.franqueada = :franqueada');
            $queryBuilder->setParameter('franqueada', VariaveisCompartilhadas::$franqueadaID);
        }
    }

    /**
     * Query para realizar filtro de convenios:
     *      Que foram criados pela franqueadora;
     *      Que possuem abrangencia nacional;
     *      Que foram criados por uma franqueadora que está no mesmo estado da franqueadora onde o usuário está logado.
     *
     * @param array $parametros
     * @param \Doctrine\ORM\QueryBuilder $queryBuilder
     */
    private function filtrarEstaduaisFranqueada($parametros, &$queryBuilder)
    {
        if (isset($parametros[ConstanteParametros::CHAVE_FRANQUEADA]) === true && is_null($parametros[ConstanteParametros::CHAVE_FRANQUEADA]) === false) {
            $queryBuilder->join(
                Franqueada::class,
                'franqueada_logada',
                \Doctrine\ORM\Query\Expr\Join::WITH,
                'franqueada_logada.id = :franqueada_logada'
            );
            $queryBuilder->setParameter('franqueada_logada', VariaveisCompartilhadas::$franqueadaID);
            $queryBuilder->andWhere(
                $queryBuilder->expr()->orX(
                    $queryBuilder->expr()->eq("franqueada_logada.estado", "fran.estado"),
                    $queryBuilder->expr()->eq("fran.franqueadora", 1),
                    $queryBuilder->expr()->eq("conv.abrangencia_nacional", 1)
                )
            );
        }
    }

    /**
     * Monta filtros data de/ate e horarios de/ate
     *
     * @param \Doctrine\ORM\QueryBuilder $queryBuilder
     * @param array $parametros
     */
    protected function montaFiltroDatasHorarios(&$queryBuilder, $parametros)
    {
        if ((isset($parametros[ConstanteParametros::CHAVE_DATA_PROXIMO_CONTATO_DE]) === true)&&(empty($parametros[ConstanteParametros::CHAVE_DATA_PROXIMO_CONTATO_DE]) === false)) {
            $queryBuilder->andWhere("conv.data_proximo_contato >= :dataDe");
            $queryBuilder->setParameter("dataDe", $parametros[ConstanteParametros::CHAVE_DATA_PROXIMO_CONTATO_DE]);
        }

        if ((isset($parametros[ConstanteParametros::CHAVE_DATA_PROXIMO_CONTATO_ATE]) === true)&&(empty($parametros[ConstanteParametros::CHAVE_DATA_PROXIMO_CONTATO_ATE]) === false)) {
            $queryBuilder->andWhere("conv.data_proximo_contato <= :dataAte");
            $queryBuilder->setParameter("dataAte", $parametros[ConstanteParametros::CHAVE_DATA_PROXIMO_CONTATO_ATE]);
        }

        if ((isset($parametros[ConstanteParametros::CHAVE_HORARIO_PROXIMO_CONTATO_DE]) === true)&&(empty($parametros[ConstanteParametros::CHAVE_HORARIO_PROXIMO_CONTATO_DE]) === false)) {
            $queryBuilder->andWhere("conv.horario_proximo_contato >= :horaDe");
            $queryBuilder->setParameter("horaDe", $parametros[ConstanteParametros::CHAVE_HORARIO_PROXIMO_CONTATO_DE]);
        }

        if ((isset($parametros[ConstanteParametros::CHAVE_HORARIO_PROXIMO_CONTATO_ATE]) === true)&&(empty($parametros[ConstanteParametros::CHAVE_HORARIO_PROXIMO_CONTATO_ATE]) === false)) {
            $queryBuilder->andWhere("conv.horario_proximo_contato <= :horaAte");
            $queryBuilder->setParameter("horaAte", $parametros[ConstanteParametros::CHAVE_HORARIO_PROXIMO_CONTATO_ATE]);
        }
    }

    /**
     * Monta os filtros
     *
     * @param array $parametros
     * @param \Doctrine\ORM\QueryBuilder $queryBuilder
     */
    protected function montaFiltros($parametros, &$queryBuilder)
    {
        if ((isset($parametros[ConstanteParametros::CHAVE_USUARIO_FRANQUEADORA]) === true)&&(((bool) $parametros[ConstanteParametros::CHAVE_USUARIO_FRANQUEADORA]) === false)) {
            if ((isset($parametros[ConstanteParametros::CHAVE_FRANQUEADA]) === true)&&(empty($parametros[ConstanteParametros::CHAVE_FRANQUEADA]) === false)) {
                // $queryBuilder->andWhere("fran.id = :idFranqueada");
                // $queryBuilder->setParameter("idFranqueada", $parametros[ConstanteParametros::CHAVE_FRANQUEADA]);
                $queryBuilder->andWhere(
                    $queryBuilder->expr()->orX(
                        $queryBuilder->expr()->eq("fran.id", $parametros[ConstanteParametros::CHAVE_FRANQUEADA]),
                        $queryBuilder->expr()->andX(
                            $queryBuilder->expr()->eq("conv.abrangencia_nacional", 1),
                            $queryBuilder->expr()->eq("conv.situacao", "'" . 'ATI' . "'")
                        )
                    )
                );
            }
        } else {
            $queryBuilder->andWhere(
                $queryBuilder->expr()->orX(
                    $queryBuilder->expr()->eq("fran.franqueadora", 1),
                    $queryBuilder->expr()->andX(
                        $queryBuilder->expr()->eq("fran.franqueadora", 0),
                        $queryBuilder->expr()->eq("conv.situacao", ":situacaoF")
                    )
                )
            );

            $queryBuilder->setParameter("situacaoF", SituacoesSistema::SITUACAO_PENDENTE_VALIDACAO);
        }//end if

        if ((isset($parametros[ConstanteParametros::CHAVE_NOME_CONTATO]) === true)&&(empty($parametros[ConstanteParametros::CHAVE_NOME_CONTATO]) === false)) {
            $queryBuilder->andWhere("conv.nome_contato LIKE :nomeContato");
            $queryBuilder->setParameter("nomeContato", "%" . $parametros[ConstanteParametros::CHAVE_NOME_CONTATO] . "%");
        }

        if ((isset($parametros[ConstanteParametros::CHAVE_ETAPAS_CONVENIO]) === true)&&(empty($parametros[ConstanteParametros::CHAVE_ETAPAS_CONVENIO]) === false)) {
            $queryBuilder->andWhere("ec.id = :idEtapasConvenio");
            $queryBuilder->setParameter("idEtapasConvenio", $parametros[ConstanteParametros::CHAVE_ETAPAS_CONVENIO]);
        }

        if ((isset($parametros[ConstanteParametros::CHAVE_PESSOA]) === true)&&(empty($parametros[ConstanteParametros::CHAVE_PESSOA]) === false)) {
            $queryBuilder->andWhere("p.id = :idPessoa");
            $queryBuilder->setParameter("idPessoa", $parametros[ConstanteParametros::CHAVE_PESSOA]);
        }

        if ((isset($parametros[ConstanteParametros::CHAVE_CONSULTOR_FUNCIONARIO]) === true)&&(empty($parametros[ConstanteParametros::CHAVE_CONSULTOR_FUNCIONARIO]) === false)) {
            $queryBuilder->andWhere("cf.id = :idConsultorFuncionario");
            $queryBuilder->setParameter("idConsultorFuncionario", $parametros[ConstanteParametros::CHAVE_CONSULTOR_FUNCIONARIO]);
        }

        if ((isset($parametros[ConstanteParametros::CHAVE_SEGMENTO_EMPRESA_CONVENIO]) === true)&&(empty($parametros[ConstanteParametros::CHAVE_SEGMENTO_EMPRESA_CONVENIO]) === false)) {
            $queryBuilder->andWhere("sec.id = :idSegmentoEmpresaConvenio");
            $queryBuilder->setParameter("idSegmentoEmpresaConvenio", $parametros[ConstanteParametros::CHAVE_SEGMENTO_EMPRESA_CONVENIO]);
        }

        if ((isset($parametros[ConstanteParametros::CHAVE_SITUACAO]) === true)&&(empty($parametros[ConstanteParametros::CHAVE_SITUACAO]) === false)) {
            $queryBuilder->andWhere("conv.situacao = :situacaoInteressado");
            $queryBuilder->setParameter("situacaoInteressado", $parametros[ConstanteParametros::CHAVE_SITUACAO]);
        }

        $this->montaFiltroDatasHorarios($queryBuilder, $parametros);
    }

    /**
     * Monta os filtros
     *
     * @param array $parametros
     * @param \Doctrine\ORM\QueryBuilder $queryBuilder
     */
    protected function montaFiltrosNacionais($parametros, &$queryBuilder)
    {
        if ((isset($parametros[ConstanteParametros::CHAVE_PESSOA]) === true)&&(empty($parametros[ConstanteParametros::CHAVE_PESSOA]) === false)) {
            $queryBuilder->andWhere("p.id = :idPessoa");
            $queryBuilder->setParameter("idPessoa", $parametros[ConstanteParametros::CHAVE_PESSOA]);
        }

        if ((isset($parametros[ConstanteParametros::CHAVE_CNPJ]) === true)&&(empty($parametros[ConstanteParametros::CHAVE_CNPJ]) === false)) {
            $queryBuilder->andWhere("p.cnpj_cpf = :cnpjEmpresa");
            $queryBuilder->setParameter("cnpjEmpresa", $parametros[ConstanteParametros::CHAVE_CNPJ]);
        }

        if ((isset($parametros[ConstanteParametros::CHAVE_SEGMENTO_EMPRESA]) === true)&&(empty($parametros[ConstanteParametros::CHAVE_SEGMENTO_EMPRESA]) === false)) {
            $queryBuilder->andWhere('sec.id = :idSegmento');
            $queryBuilder->setParameter('idSegmento', $parametros[ConstanteParametros::CHAVE_SEGMENTO_EMPRESA]);
        }

        if ((isset($parametros[ConstanteParametros::CHAVE_RAZAO_SOCIAL]) === true)&&(empty($parametros[ConstanteParametros::CHAVE_RAZAO_SOCIAL]) === false)) {
            $queryBuilder->andWhere('p.razao_social like :razaoSocial');
            $queryBuilder->setParameter('razaoSocial', '%' . $parametros[ConstanteParametros::CHAVE_RAZAO_SOCIAL] . '%');
        }

        if ((isset($parametros[ConstanteParametros::CHAVE_UNIDADE_RESPONSAVEL]) === true)&&(empty($parametros[ConstanteParametros::CHAVE_UNIDADE_RESPONSAVEL]) === false)) {
            $queryBuilder->andWhere('fran.nome  like :unidadeResponsavel');
            $queryBuilder->setParameter('unidadeResponsavel', '%' . $parametros[ConstanteParametros::CHAVE_UNIDADE_RESPONSAVEL] . '%');
        }

        if ((isset($parametros[ConstanteParametros::CHAVE_ETAPAS_CONVENIO]) === true)&&(empty($parametros[ConstanteParametros::CHAVE_ETAPAS_CONVENIO]) === false)) {
            $queryBuilder->andWhere('conv.etapas_convenio = :idEtapas');
            $queryBuilder->setParameter('idEtapas', $parametros[ConstanteParametros::CHAVE_ETAPAS_CONVENIO]);
        }

        if ((isset($parametros[ConstanteParametros::CHAVE_SITUACAO]) === true)&&(empty($parametros[ConstanteParametros::CHAVE_SITUACAO]) === false)) {
            $queryBuilder->andWhere('conv.situacao  = :situacao');
            $queryBuilder->setParameter('situacao', $parametros[ConstanteParametros::CHAVE_SITUACAO]);
        }

        if ((isset($parametros[ConstanteParametros::CHAVE_FILTRO_ABRANGENCIA]) === true)) {
            $tipoAbrangencia = $parametros[ConstanteParametros::CHAVE_FILTRO_ABRANGENCIA];

            // 1: Apenas criadas pela franqueada
            // 2: Apenas os que possuem Abrangencia nacional
            // 3: Apenas os que possuem abrangencia nacional e da cidade
            switch ($tipoAbrangencia) {
                case 1:
            {
                $queryBuilder->andWhere("fran.id = :idFranqueada");
                $queryBuilder->setParameter("idFranqueada", VariaveisCompartilhadas::$franqueadaID);
                }
            break;
                case 2:
            {
                $queryBuilder->andWhere("conv.abrangencia_nacional = :abrNacional");
                $queryBuilder->setParameter("abrNacional", true);
                }
            break;
                case 3:
            {
                $cidadeIdQuery = $this->_em->createQueryBuilder();
                $cidadeIdQuery->select("c.id");
                $cidadeIdQuery->from(\App\Entity\Principal\Franqueada::class, "fra");
                $cidadeIdQuery->leftJoin("fra.cidade", "c");
                $cidadeIdQuery->where("fra.id = :franqueadaId");
                $cidadeIdQuery->setParameter("franqueadaId", VariaveisCompartilhadas::$franqueadaID);
                $cidadeId = $cidadeIdQuery->getQuery()->getSingleScalarResult();
                $subQuery = $this->_em->createQueryBuilder();
                $subQuery->select("fra.id");
                $subQuery->from(\App\Entity\Principal\Franqueada::class, "fra");
                $subQuery->where("fra.cidade = :cidadeId");
                $queryBuilder->setParameter("cidadeId", $cidadeId);
                $comparacaoAbrangenciaNacional = $queryBuilder->expr()->eq("conv.abrangencia_nacional", 0);
                $comparacaoFranqueadaId        = $queryBuilder->expr()->in("fran.id", $subQuery->getDQL());
                $orX = $queryBuilder->expr()->orX($comparacaoAbrangenciaNacional, $comparacaoFranqueadaId);
                $queryBuilder->andWhere($orX);
                }
            break;
            }//end switch
        }//end if
    }

    /**
     * Monta os filtros de data
     *
     * @param array $parametros
     * @param \Doctrine\ORM\QueryBuilder $queryBuilder
     */
    private function montarFiltosPorData($parametros, &$queryBuilder)
    {
        if ((isset($parametros[ConstanteParametros::CHAVE_DATA_DE_CADASTRO_DE]) === true)&&(empty($parametros[ConstanteParametros::CHAVE_DATA_DE_CADASTRO_DE]) === false)) {
            $queryBuilder->andWhere('conv.data_primeiro_atendimento  >= :dataCadastroDe');
            $queryBuilder->setParameter('dataCadastroDe', $parametros[ConstanteParametros::CHAVE_DATA_DE_CADASTRO_DE]);
        }

        if ((isset($parametros[ConstanteParametros::CHAVE_DATA_DE_CADASTRO_ATE]) === true)&&(empty($parametros[ConstanteParametros::CHAVE_DATA_DE_CADASTRO_ATE]) === false)) {
            $queryBuilder->andWhere('conv.data_primeiro_atendimento  <= :dataCadastroAte');
            $queryBuilder->setParameter('dataCadastroAte', $parametros[ConstanteParametros::CHAVE_DATA_DE_CADASTRO_ATE]);
        }
    }

    /**
     * Monta os filtros de situacao
     *
     * @param array $parametros
     * @param \Doctrine\ORM\QueryBuilder $queryBuilder
     */
    private function montarFiltroDeSituacao($parametros, &$queryBuilder)
    {
        $queryBuilder->where("conv.situacao = :situacao");
        $queryBuilder->setParameter("situacao", "ATI");

        if ((isset($parametros[ConstanteParametros::CHAVE_SITUACAO]) === true)&&(empty($parametros[ConstanteParametros::CHAVE_SITUACAO]) === false)) {
            $queryBuilder->andWhere('conv.situacao  = :situacao');
            $queryBuilder->setParameter('situacao', $parametros[ConstanteParametros::CHAVE_SITUACAO]);
        }
    }

    /**
     * Realiza os filtros para o Funil de vendas
     *
     * @param \Doctrine\ORM\QueryBuilder $queryBuilder
     * @param array $parametros
     */
    protected function filtroFunilVendas(&$queryBuilder, $parametros)
    {
        // $queryBuilder->andWhere("conv.situacao <> :situacaoPV");
        // $queryBuilder->setParameter("situacaoPV", SituacoesSistema::SITUACAO_PENDENTE_VALIDACAO_FRANQUEADORA);
        if (isset($parametros[ConstanteParametros::CHAVE_DATA_AGENDAMENTO]) === true) {
            if (empty($parametros[ConstanteParametros::CHAVE_DATA_AGENDAMENTO]) === false) {
                $dataArray = explode("T", $parametros[ConstanteParametros::CHAVE_DATA_AGENDAMENTO]);
                $queryBuilder->andWhere("conv.data_proximo_contato >= :dataAgendamentoIni");
                $queryBuilder->andWhere("conv.data_proximo_contato <= :dataAgendamentoFim");
                $queryBuilder->setParameter("dataAgendamentoIni", $dataArray[0] . " 00:00:01");
                $queryBuilder->setParameter("dataAgendamentoFim", $dataArray[0] . " 23:59:59");
            } else {
                $dataAtual     = new \DateTime();
                $dataFormatada = $dataAtual->format("Y-m-d");
                $queryBuilder->andWhere("conv.data_proximo_contato >= :dataAgendamentoIni");
                $queryBuilder->andWhere("conv.data_proximo_contato <= :dataAgendamentoFim");
                $queryBuilder->setParameter("dataAgendamentoIni", $dataFormatada . " 00:00:01");
                $queryBuilder->setParameter("dataAgendamentoFim", $dataFormatada . " 23:59:59");
            }
        }

    }

    /**
     * Realiza os filtros para o Funil de vendas
     *
     * @param \Doctrine\ORM\QueryBuilder $queryBuilder
     */
    protected function filtroFunilVendasAtrasado(&$queryBuilder)
    {
        $queryBuilder->andWhere("conv.situacao <> :situacaoPV");
        $queryBuilder->setParameter("situacaoPV", SituacoesSistema::SITUACAO_PENDENTE_VALIDACAO_FRANQUEADORA);

        $dataAtual     = new \DateTime();
        $dataFormatada = $dataAtual->format("Y-m-d");
        $queryBuilder->andWhere("conv.data_proximo_contato <= :dataAgendamentoFim");
        $queryBuilder->setParameter("dataAgendamentoFim", $dataFormatada . " 00:00:01");

    }

    /**
     * Realiza os filtros para o Funil de vendas
     *
     * @param \Doctrine\ORM\QueryBuilder $queryBuilder
     * @param array $parametros
     */
    protected function filtroFollowupConvenio(&$queryBuilder, $parametros)
    {
        if ((isset($parametros[ConstanteParametros::CHAVE_DATA_CADASTRO_DE]) === true)&&(empty($parametros[ConstanteParametros::CHAVE_DATA_CADASTRO_DE]) === false)) {
            $dataCadastroDe = explode("T", $parametros[ConstanteParametros::CHAVE_DATA_CADASTRO_DE]);
            $queryBuilder->andWhere("fupc.data_registro >= :dataCadastroDe");
            $queryBuilder->setParameter("dataCadastroDe", $dataCadastroDe[0] . " 00:00:01");
        }

        if ((isset($parametros[ConstanteParametros::CHAVE_DATA_CADASTRO_ATE]) === true)&&(empty($parametros[ConstanteParametros::CHAVE_DATA_CADASTRO_ATE]) === false)) {
            $dataCadastroAte = explode("T", $parametros[ConstanteParametros::CHAVE_DATA_CADASTRO_ATE]);
            $queryBuilder->andWhere("fupc.data_registro <= :dataCadastroAte");
            $queryBuilder->setParameter("dataCadastroAte", $dataCadastroAte[0] . " 23:59:59");
        }

        if ((isset($parametros[ConstanteParametros::CHAVE_DATA_PROXIMO_CONTATO_DE]) === true)&&(empty($parametros[ConstanteParametros::CHAVE_DATA_PROXIMO_CONTATO_DE]) === false)) {
            $dataProximoContatoDe = explode("T", $parametros[ConstanteParametros::CHAVE_DATA_PROXIMO_CONTATO_DE]);
            $queryBuilder->andWhere("conv.data_proximo_contato >= :dataDe");
            $queryBuilder->setParameter("dataDe", $dataProximoContatoDe[0] . " 00:00:01");
        }

        if ((isset($parametros[ConstanteParametros::CHAVE_DATA_PROXIMO_CONTATO_ATE]) === true)&&(empty($parametros[ConstanteParametros::CHAVE_DATA_PROXIMO_CONTATO_ATE]) === false)) {
            $dataProximoContatoAte = explode("T", $parametros[ConstanteParametros::CHAVE_DATA_PROXIMO_CONTATO_ATE]);
            $queryBuilder->andWhere("conv.data_proximo_contato <= :dataAte");
            $queryBuilder->setParameter("dataAte", $dataProximoContatoAte[0] . " 23:59:59");
        }

        if ((isset($parametros[ConstanteParametros::CHAVE_CONVENIADO]) === true)&&(empty($parametros[ConstanteParametros::CHAVE_CONVENIADO]) === false)) {
            $queryBuilder->andWhere("p.id <= :convenioId");
            $queryBuilder->setParameter("convenioId", $parametros[ConstanteParametros::CHAVE_CONVENIADO]);
        }

        if ((isset($parametros[ConstanteParametros::CHAVE_CONSULTOR_RESPONSAVEL_FUNCIONARIO]) === true)&&(empty($parametros[ConstanteParametros::CHAVE_CONSULTOR_RESPONSAVEL_FUNCIONARIO]) === false)) {
            $queryBuilder->andWhere("cf.id = :consultorId");
            $queryBuilder->setParameter("consultorId", $parametros[ConstanteParametros::CHAVE_CONSULTOR_RESPONSAVEL_FUNCIONARIO]);
        }

    }

    /**
     * Filtra os registros por pagina
     *
     * @param array $parametros
     * @param number $pagina
     * @param number $numeroItensPorPagina
     *
     * @return \Knp\Component\Pager\Pagination\SlidingPagination
     */
    public function filtrarConvenioPorPagina($parametros, $pagina=1, $numeroItensPorPagina=50)
    {
        $opcoes       = [];
        $queryBuilder = $this->montaQueryBase();
        $this->montaFiltros($parametros, $queryBuilder);

        if ((isset($parametros[ConstanteParametros::CHAVE_ORDENACAO_COLUNA]) === true)&&(is_null($parametros[ConstanteParametros::CHAVE_ORDENACAO_COLUNA]) === false)) {
            $queryBuilder->orderBy($parametros[ConstanteParametros::CHAVE_ORDENACAO_COLUNA], $parametros[ConstanteParametros::CHAVE_ORDENACAO_SORT]);
            $opcoes[\Knp\Component\Pager\Paginator::SORT_FIELD_PARAMETER_NAME]     = "~";
            $opcoes[\Knp\Component\Pager\Paginator::SORT_DIRECTION_PARAMETER_NAME] = "~";
        }

        return \App\Helper\FunctionHelper::montaPaginatorPaginacao($queryBuilder, $pagina, $numeroItensPorPagina, $opcoes);
    }

    /**
     * Filtra os followup por pagina
     *
     * @param array $parametros
     * @param number $numeroItensPorPagina
     *
     * @return \Knp\Component\Pager\Pagination\SlidingPagination
     */
    public function filtrarConvenioFollowup($parametros, $numeroItensPorPagina=50)
    {
        $opcoes       = [];
        $queryBuilder = $this->montaQueryBase();
        $queryBuilder->addSelect("fupc");
        $queryBuilder->join("conv.followupConvenios", "fupc");
        $this->filtrarFranqueada($queryBuilder, $parametros);
        $this->filtroFollowupConvenio($queryBuilder, $parametros);

        if ((isset($parametros[ConstanteParametros::CHAVE_ORDENACAO_COLUNA]) === true)&&(is_null($parametros[ConstanteParametros::CHAVE_ORDENACAO_COLUNA]) === false)) {
            $queryBuilder->orderBy($parametros[ConstanteParametros::CHAVE_ORDENACAO_COLUNA], $parametros[ConstanteParametros::CHAVE_ORDENACAO_SORT]);
            $opcoes[\Knp\Component\Pager\Paginator::SORT_FIELD_PARAMETER_NAME] = "~";
            // $opcoes[\Knp\Component\Pager\Paginator::SORT_DIRECTION_PARAMETER_NAME] = "~";
        }

        $opcoes["wrap-queries"] = "true";
        return \App\Helper\FunctionHelper::montaPaginatorPaginacao($queryBuilder, $parametros[ConstanteParametros::CHAVE_PAGINA], $numeroItensPorPagina, $opcoes);

    }

    /**
     * Filtra os registros por pagina
     *
     * @param array $parametros
     * @param number $pagina
     * @param number $numeroItensPorPagina
     *
     * @return \Knp\Component\Pager\Pagination\SlidingPagination
     */
    public function filtrarConvenioNacionaisAtivosPorPagina($parametros, $pagina=1, $numeroItensPorPagina=50)
    {
        $opcoes       = [];
        $queryBuilder = $this->montaQueryBase();

        $queryBuilder->where("conv.situacao = :situacao");
        $queryBuilder->setParameter("situacao", "ATI");

        $this->montaFiltrosNacionais($parametros, $queryBuilder);
        $this->montarFiltosPorData($parametros, $queryBuilder);
        $this->filtrarEstaduaisFranqueada($parametros, $queryBuilder);

        if ((isset($parametros[ConstanteParametros::CHAVE_ORDENACAO_COLUNA]) === true)&&(is_null($parametros[ConstanteParametros::CHAVE_ORDENACAO_COLUNA]) === false)) {
            $queryBuilder->orderBy($parametros[ConstanteParametros::CHAVE_ORDENACAO_COLUNA], $parametros[ConstanteParametros::CHAVE_ORDENACAO_SORT]);
            $opcoes[\Knp\Component\Pager\Paginator::SORT_FIELD_PARAMETER_NAME]     = "~";
            $opcoes[\Knp\Component\Pager\Paginator::SORT_DIRECTION_PARAMETER_NAME] = "~";
        }

        return \App\Helper\FunctionHelper::montaPaginatorPaginacao($queryBuilder, $pagina, $numeroItensPorPagina, $opcoes);
    }

    /**
     * Filtra o registro por ID
     *
     * @param int $id
     * @param int $parametros
     *
     * @return array|NULL
     */
    public function buscarRegistroPorId($id, $parametros)
    {
        $queryBuilder = $this->montaQueryBase();
        $this->filtrarFranqueada($queryBuilder, $parametros);

        $queryBuilder->andWhere("conv.id = :id");
        $queryBuilder->setParameter("id", $id);
        return \App\Helper\FunctionHelper::retornaArrayNull($queryBuilder, true);
    }

    /**
     * Busca convênios por nome
     *
     * @param string $query
     *
     * @return array|NULL
     */
    public function buscarPorNome($query)
    {
        $queryBuilder = $this->montaQueryBase();
        $queryBuilder->andWhere("conv.nome_contato LIKE :nome");
        $queryBuilder->orWhere("p.nome_contato LIKE :nome");

        $queryBuilder->setParameter("nome", "%$query%");

        return \App\Helper\FunctionHelper::retornaArrayNull($queryBuilder);
    }

    /**
     * Busca convênios ATIVOS por nome
     *
     * @param string $query
     * @param array $parametros
     *
     * @return array|NULL
     */
    public function buscarAtivosPorNome($query, $parametros)
    {
        $queryBuilder = $this->montaQueryBase();
        $queryBuilder->andWhere("conv.situacao = :situacao");
        $queryBuilder->andWhere(
            $queryBuilder->expr()->orX(
                "conv.nome_contato LIKE :nome",
                "p.nome_contato LIKE :nome",
                "p.razao_social LIKE :nome",
                "p.nome_fantasia LIKE :nome"
            )
        );

        $queryBuilder->setParameter("nome", "%$query%");
        $queryBuilder->setParameter("situacao", "ATI");
        $this->filtrarEstaduaisFranqueada($parametros, $queryBuilder);

        return \App\Helper\FunctionHelper::retornaArrayNull($queryBuilder);
    }

    /**
     * Retorna resultados para funil de vendas
     *
     * @param int $usuarioId
     * @param array $parametros
     *
     * @return array|NULL
     */
    public function buscaFunilVendas($usuarioId, $parametros=[])
    {
        $queryBuilder = $this->montaQueryBase();
        $this->filtrarFranqueada($queryBuilder, $parametros);
        $this->filtroFunilVendas($queryBuilder, $parametros);
        $this->filtroFunilVendasUsuario($queryBuilder, $usuarioId, $parametros);

        return $queryBuilder->getQuery()->getArrayResult();
    }

    /**
     * Retorna resultados para funil de vendas
     *
     * @param int $usuarioId
     * @param array $parametros
     *
     * @return array|NULL
     */
    public function buscaFunilVendasAtrasado($usuarioId, $parametros=[])
    {
        $queryBuilder = $this->montaQueryBase();
        $this->filtrarFranqueada($queryBuilder);
        $this->filtroFunilVendasAtrasado($queryBuilder);
        $this->filtroFunilVendasUsuario($queryBuilder, $usuarioId, $parametros);

        return $queryBuilder->getQuery()->getArrayResult();
    }

    /**
     * Gera o filtro por usuário -> pode ser tanto o logado, quanto algum consultor específico
     *
     * @param \Doctrine\ORM\QueryBuilder $queryBuilder
     * @param int $usuarioId
     * @param array $parametros
     */
    private function filtroFunilVendasUsuario(&$queryBuilder, $usuarioId, $parametros=[])
    {
        $usuarioRepo      = $this->_em->getRepository(\App\Entity\Principal\Usuario::class);
        $usuarioLogadoOBJ = $usuarioRepo->find($usuarioId);
        $funcionarioLogadoCollection = $usuarioLogadoOBJ->getFuncionarios();

        if ($funcionarioLogadoCollection->count() > 0) {
            $funcionarioLogadoOBJ = $funcionarioLogadoCollection->get(0);

            if ($funcionarioLogadoOBJ->getGestorComercial() === false) {
                $queryBuilder->andWhere("cf.id = :funcionarioLogadoId");
                $queryBuilder->setParameter("funcionarioLogadoId", $funcionarioLogadoOBJ->getId());
            }
        }

        if (isset($parametros[ConstanteParametros::CHAVE_CONSULTOR_COMERCIAL]) === true && empty($parametros[ConstanteParametros::CHAVE_CONSULTOR_COMERCIAL]) === false) {
            $usuarioConsultorOBJ            = $usuarioRepo->find($parametros[ConstanteParametros::CHAVE_CONSULTOR_COMERCIAL]);
            $funcionarioConsultorCollection = $usuarioConsultorOBJ->getFuncionarios();

            if ($funcionarioConsultorCollection->count() > 0) {
                $funcionarioConsultorOBJ = $funcionarioConsultorCollection->get(0);

                $queryBuilder->andWhere("cf.id = :funcionarioConsultorId");
                $queryBuilder->setParameter("funcionarioConsultorId", $funcionarioConsultorOBJ->getId());
            }
        }
    }

    public function gerarDadosRelatorioNegociacaoConvenios($parametros)
    {
        $queryBuilder = $this->createQueryBuilder("conv")
            ->select([
                'p.nome_fantasia',
                'conv.situacao',
                'conv.abrangencia_nacional',
                'sec.descricao',
                'func.apelido as consultor_responsavel',
                "date_format(conv.data_proximo_contato, '%Y-%m-%d') as data_ultimo_contato",
                "date_format(conv.horario_proximo_contato, '%H:%i') as horario_ultimo_contato",
                'func.apelido as ultimo_consultor_contato',
                'mnfc.descricao as motivo_nao_fechamento_convenio'
            ])
            ->leftJoin('conv.pessoa', 'p')
            ->leftJoin('conv.consultor_funcionario', 'func')
            ->leftJoin('conv.segmento_empresa_convenio', 'sec')
            ->leftJoin('conv.motivo_nao_fechamento_convenio', 'mnfc')
            ->andWhere('conv.franqueada = :franqueada')
            ->setParameter('franqueada', $parametros[ConstanteParametros::CHAVE_FRANQUEADA]);

            if(isset($parametros[ConstanteParametros::CHAVE_SITUACAO])) {
                $queryBuilder->andWhere("conv.situacao in (:situacao)");
                $queryBuilder->setParameter("situacao", $parametros[ConstanteParametros::CHAVE_SITUACAO ]);
            }

            if(isset($parametros[ConstanteParametros::CHAVE_SEGMENTO_EMPRESA_CONVENIO])) {
                $queryBuilder->andWhere("sec.id in (:segmento_empresa_convenio)");
                $queryBuilder->setParameter("segmento_empresa_convenio", $parametros[ConstanteParametros::CHAVE_SEGMENTO_EMPRESA_CONVENIO]);
            }

            if(isset($parametros[ConstanteParametros::CHAVE_ABRANGENCIA_NACIONAL])) {
                $nacional = $parametros[ConstanteParametros::CHAVE_ABRANGENCIA_NACIONAL] == 'N';
                $queryBuilder->andWhere("conv.abrangencia_nacional in (:abrangencia_nacional)");
                $queryBuilder->setParameter("abrangencia_nacional", $parametros[ConstanteParametros::CHAVE_ABRANGENCIA_NACIONAL]);
            }

            return $queryBuilder->getQuery()->getResult();
    }


}
