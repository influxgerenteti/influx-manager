<?php

namespace App\Repository\Principal;

use App\Entity\Principal\OcorrenciaAcademica;
use App\Helper\ConstanteParametros;
use App\Helper\VariaveisCompartilhadas;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Symfony\Bridge\Doctrine\RegistryInterface;
use App\Helper\SituacoesSistema;

/**
 * @method OcorrenciaAcademica|null find($id, $lockMode = null, $lockVersion = null)
 * @method OcorrenciaAcademica|null findOneBy(array $criteria, array $orderBy = null)
 * @method OcorrenciaAcademica[]    findAll()
 * @method OcorrenciaAcademica[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class OcorrenciaAcademicaRepository extends ServiceEntityRepository
{


    public function __construct(RegistryInterface $registry)
    {
        parent::__construct($registry, OcorrenciaAcademica::class);
    }

    /**
     * Monta a query padrao
     *
     * @return \Doctrine\ORM\QueryBuilder
     */
    protected function montaQueryBase()
    {
        $queryBuilder = $this->createQueryBuilder('oca');
        $queryBuilder->addSelect('a');
        $queryBuilder->addSelect('ctt');
        $queryBuilder->addSelect('u');
        $queryBuilder->addSelect('p');
        $queryBuilder->addSelect('ct');
        $queryBuilder->addSelect('f');
        $queryBuilder->addSelect('to');
        $queryBuilder->addSelect('ocd');
        $queryBuilder->addSelect('fun');
        $queryBuilder->addSelect("oro");
        $queryBuilder->leftJoin("oca.aluno", "a");
        $queryBuilder->leftJoin("oca.contrato", "ctt");
        $queryBuilder->leftJoin("a.pessoa", "p");
        $queryBuilder->leftJoin("a.contratos", "ct");
        $queryBuilder->leftJoin("oca.usuario", "u");
        $queryBuilder->leftJoin("oca.funcionario", "f");
        $queryBuilder->leftJoin("oca.tipo_ocorrencia", "to");
        $queryBuilder->leftJoin("oca.ocorrenciaAcademicaDetalhes", "ocd");
        $queryBuilder->leftJoin("ocd.funcionario", "fun");
        $queryBuilder->leftJoin("oca.origem_ocorrencia", "oro");

        return $queryBuilder;
    }

    /**
     *  Busca ocorrencia academica por id
     *
     * @param int $id
     *
     * @return array|NULL
     */
    public function buscarRegistroPorId($id)
    {
        $queryBuilder = $this->montaQueryBase();
        $this->filtrarFranqueada($queryBuilder);
        $queryBuilder->andWhere("oca.id = :id");
        $queryBuilder->setParameter("id", $id);
        return  \App\Helper\FunctionHelper::retornaArrayNull($queryBuilder, true);
    }

    /**
     * Query para realizar fitlro de franqueada
     *
     * @param \Doctrine\ORM\QueryBuilder $queryBuilder
     */
    private function filtrarFranqueada(&$queryBuilder)
    {
        $queryBuilder->where('oca.franqueada = :franqueada');
        $queryBuilder->setParameter('franqueada', VariaveisCompartilhadas::$franqueadaID);
    }

    /**
     * Monta filtros data de/ate
     *
     * @param \Doctrine\ORM\QueryBuilder $queryBuilder
     * @param array $parametros
     */
    protected function montaFiltroDatas(&$queryBuilder, $parametros)
    {

        if ((isset($parametros[ConstanteParametros::CHAVE_DATA_ABERTURA]) === true)&&(empty($parametros[ConstanteParametros::CHAVE_DATA_ABERTURA]) === false)) {
            $queryBuilder->andWhere("oca.data_criacao >= :dataCriacao");
            $queryBuilder->setParameter("dataCriacao", $parametros[ConstanteParametros::CHAVE_DATA_ABERTURA]);
        }

        if ((isset($parametros[ConstanteParametros::CHAVE_DATA_FECHAMENTO]) === true)&&(empty($parametros[ConstanteParametros::CHAVE_DATA_FECHAMENTO]) === false)) {
            $queryBuilder->andWhere("oca.data_conclusao <= :dataFechamento");
            $queryBuilder->setParameter("dataFechamento", $parametros[ConstanteParametros::CHAVE_DATA_FECHAMENTO]);
        }

        if ((isset($parametros[ConstanteParametros::CHAVE_DATA_MOVIMENTACAO_DE]) === true)&&(empty($parametros[ConstanteParametros::CHAVE_DATA_MOVIMENTACAO_DE]) === false)) {
            $queryBuilder->andWhere("ocd.data_criacao >= :dataDeCriacaoDe");
            $queryBuilder->setParameter("dataDeCriacaoDe", $parametros[ConstanteParametros::CHAVE_DATA_MOVIMENTACAO_DE]);
        }

        if ((isset($parametros[ConstanteParametros::CHAVE_DATA_MOVIMENTACAO_ATE]) === true)&&(empty($parametros[ConstanteParametros::CHAVE_DATA_MOVIMENTACAO_ATE]) === false)) {
            $queryBuilder->andWhere("ocd.data_criacao <= :dataDeCriacaoAte");
            $queryBuilder->setParameter("dataDeCriacaoAte", $parametros[ConstanteParametros::CHAVE_DATA_MOVIMENTACAO_ATE]);
        }

    }

    /**
     * Monta os filtros passado pela requisicao
     *
     * @param \Doctrine\ORM\QueryBuilder $queryBuilder
     * @param array $parametros
     */
    protected function montaFiltros(&$queryBuilder, $parametros)
    {
        if ((isset($parametros[ConstanteParametros::CHAVE_ALUNO]) === true)&&(empty($parametros[ConstanteParametros::CHAVE_ALUNO]) === false)) {
            $queryBuilder->andWhere("a.id = :alunoId");
            $queryBuilder->setParameter("alunoId", $parametros[ConstanteParametros::CHAVE_ALUNO]);
        }

        if ((isset($parametros[ConstanteParametros::CHAVE_FUNCIONARIO]) === true)&&(empty($parametros[ConstanteParametros::CHAVE_FUNCIONARIO]) === false)) {
            $queryBuilder->andWhere("f.id = :idFuncionario");
            $queryBuilder->setParameter("idFuncionario", $parametros[ConstanteParametros::CHAVE_FUNCIONARIO]);
        }

        if ((isset($parametros[ConstanteParametros::CHAVE_TIPO_OCORRENCIA]) === true)&&(empty($parametros[ConstanteParametros::CHAVE_TIPO_OCORRENCIA]) === false)) {
            $queryBuilder->andWhere("to.id = :idTipoOcorrencia");
            $queryBuilder->setParameter("idTipoOcorrencia", $parametros[ConstanteParametros::CHAVE_TIPO_OCORRENCIA]);
        }

        if ((isset($parametros[ConstanteParametros::CHAVE_SITUACAO]) === true)&&(empty($parametros[ConstanteParametros::CHAVE_SITUACAO]) === false)) {
            $queryBuilder->andWhere("oca.situacao = :situacao");
            $queryBuilder->setParameter("situacao", $parametros[ConstanteParametros::CHAVE_SITUACAO]);
        }

        if ((isset($parametros[ConstanteParametros::CHAVE_CONTRATO]) === true)&&(empty($parametros[ConstanteParametros::CHAVE_CONTRATO]) === false)) {
            $queryBuilder->andWhere("ctt.id = :contratoId");
            $queryBuilder->setParameter("contratoId", $parametros[ConstanteParametros::CHAVE_CONTRATO]);
        }

        $this->montaFiltroDatas($queryBuilder, $parametros);
    }

    /**
     * Listar por página
     *
     * @param array $parametros             Parametros a serem inclusos no objeto
     * @param integer $pagina               Número da página
     * @param integer $numeroItensPorPagina Número limite de itens
     *
     * @return NULL|\App\Entity\Principal\OcorrenciaAcademica[]
     */
    public function listar ($parametros, $pagina=1, $numeroItensPorPagina=50)
    {
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
     * Busca as ocorrências acadêmicas para o Funil de Vendas
     *
     * @param int $usuarioId
     * @param array $parametros Parametros da requisicao
     *
     * @return array|NULL
     */
    public function buscaFunilVendas($usuarioId, $parametros=[])
    {
        $queryBuilder = $this->createQueryBuilder("oca");
        // $queryBuilder->addSelect("fran");
        $queryBuilder->addSelect("ocad");
        $queryBuilder->addSelect("al");
        $queryBuilder->addSelect("p");
        $queryBuilder->addSelect("func");
        $queryBuilder->addSelect("usu");
        $queryBuilder->addSelect("to");
        $queryBuilder->innerJoin("oca.franqueada", "fran");
        $queryBuilder->leftJoin("oca.tipo_ocorrencia", "to");
        $queryBuilder->leftJoin("oca.ocorrenciaAcademicaDetalhes", "ocad");
        $queryBuilder->leftJoin("oca.aluno", "al");
        $queryBuilder->leftJoin("al.pessoa", "p");
        $queryBuilder->leftJoin("oca.funcionario", "func");
        $queryBuilder->leftJoin("oca.usuario", "usu");
        $this->filtroFunilVendas($queryBuilder, $parametros);
        $this->filtroFunilVendasUsuario($queryBuilder, $usuarioId, $parametros);

        return $queryBuilder->getQuery()->getArrayResult();
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

        $queryBuilder->andWhere("oca.situacao = :situacao");
        $queryBuilder->setParameter("situacao", SituacoesSistema::SITUACAO_ABERTO);

        if ((isset($parametros[ConstanteParametros::CHAVE_DATA_AGENDAMENTO]) === true) && (empty($parametros[ConstanteParametros::CHAVE_DATA_AGENDAMENTO]) === false)) {
            $dataFormatada = explode("T", $parametros[ConstanteParametros::CHAVE_DATA_AGENDAMENTO]);
            $dataFormatada = $dataFormatada[0];
        } else {
            $dataAtual     = new \DateTime();
            $dataFormatada = $dataAtual->format("Y-m-d");
        }

        $queryBuilder->andWhere(
            $queryBuilder->expr()->orX(
                $queryBuilder->expr()->isNull("oca.data_proximo_contato"),
                $queryBuilder->expr()->andX(
                    $queryBuilder->expr()->lte('oca.data_proximo_contato', ':dataAgendamentoFim'),
                    $queryBuilder->expr()->gte('oca.data_proximo_contato', ':dataAgendamentoIni')
                )
            )
        );

        $queryBuilder->andWhere("oca.data_conclusao IS NULL");

        $queryBuilder->setParameter("dataAgendamentoIni", $dataFormatada . " 00:00:01");
        $queryBuilder->setParameter("dataAgendamentoFim", $dataFormatada . " 23:59:59");

    }

    /**
     * Busca as ocorrências acadêmicas para o Funil de Vendas
     *
     * @param int $usuarioId
     * @param array $parametros
     *
     * @return array|NULL
     */
    public function buscaFunilVendasAtrasado($usuarioId, $parametros=[])
    {
        $dateTime     = new \DateTime();
        $dataYmd      = $dateTime->format("Y-m-d");
        $queryBuilder = $this->createQueryBuilder("oca");
        // $queryBuilder->addSelect("fran");
        $queryBuilder->addSelect("ocad");
        $queryBuilder->addSelect("al");
        $queryBuilder->addSelect("p");
        $queryBuilder->addSelect("func");
        $queryBuilder->addSelect("usu");
        $queryBuilder->addSelect("to");
        $queryBuilder->innerJoin("oca.franqueada", "fran");
        $queryBuilder->leftJoin("oca.ocorrenciaAcademicaDetalhes", "ocad");
        $queryBuilder->leftJoin("oca.aluno", "al");
        $queryBuilder->leftJoin("al.pessoa", "p");
        $queryBuilder->leftJoin("oca.funcionario", "func");
        $queryBuilder->leftJoin("oca.usuario", "usu");
        $queryBuilder->join("oca.tipo_ocorrencia", "to");
        $this->filtrarFranqueada($queryBuilder);
        $this->filtroFunilVendasUsuario($queryBuilder, $usuarioId, $parametros);
        $queryBuilder->andWhere("oca.situacao = :situacao");
        $queryBuilder->setParameter("situacao", SituacoesSistema::SITUACAO_ABERTO);
        $queryBuilder->andWhere("oca.data_conclusao IS NULL");
        $queryBuilder->andWhere("oca.data_proximo_contato IS NOT NULL");
        $queryBuilder->andWhere("oca.data_proximo_contato <= :dataProximoContatoFim");
        $queryBuilder->setParameter("dataProximoContatoFim", $dataYmd . " 00:00:01");

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
                $queryBuilder->andWhere("func.id = :funcionarioLogadoId");
                $queryBuilder->setParameter("funcionarioLogadoId", $funcionarioLogadoOBJ->getId());
            }
        }

        if (isset($parametros[ConstanteParametros::CHAVE_CONSULTOR_COMERCIAL]) === true && empty($parametros[ConstanteParametros::CHAVE_CONSULTOR_COMERCIAL]) === false) {
            $usuarioConsultorOBJ            = $usuarioRepo->find($parametros[ConstanteParametros::CHAVE_CONSULTOR_COMERCIAL]);
            $funcionarioConsultorCollection = $usuarioConsultorOBJ->getFuncionarios();

            if ($funcionarioConsultorCollection->count() > 0) {
                $funcionarioConsultorOBJ = $funcionarioConsultorCollection->get(0);

                $queryBuilder->andWhere("func.id = :funcionarioConsultorId");
                $queryBuilder->setParameter("funcionarioConsultorId", $funcionarioConsultorOBJ->getId());
            }
        }
    }





    /**
     * Busca Ocorrencia por aluno e contrato e tipo
     *
     * @param int $alunoId
     * @param int $contratoId
     * @param string $tipoOrigemOcorrencia
     *
     * @return mixed|NULL|\Doctrine\DBAL\Driver\Statement|array
     */
    public function buscaOcorrenciaAlunoContratoTipo($alunoId, $contratoId, $tipoOrigemOcorrencia)
    {
        $queryBuilder = $this->montaQueryBase();
        $this->filtrarFranqueada($queryBuilder);
        $queryBuilder->andWhere("a.id = :alunoId");
        $queryBuilder->andWhere("ctt.id = :contratoId");
        $queryBuilder->andWhere("oro.tipo_origem = :tipoOrigemOcorrencia");
        $queryBuilder->setParameter("alunoId", $alunoId);
        $queryBuilder->setParameter("contratoId", $contratoId);
        $queryBuilder->setParameter("tipoOrigemOcorrencia", $tipoOrigemOcorrencia);

        return $queryBuilder->getQuery()->getOneOrNullResult();
    }

    /**
     * Retorna as ocorrências do contrato passado conforme parametros
     *
     * @param array $parametros
     *
     * @return array[]
     */
    public function getOcorrenciasContrato($parametros)
    {
        $queryBuilder = $this->createQueryBuilder('oca');
        $queryBuilder->join("oca.contrato", "ctt");
        $queryBuilder->join("oca.tipo_ocorrencia", "to");
        $this->filtrarFranqueada($queryBuilder);
        $this->montaFiltros($queryBuilder, $parametros);

        return $queryBuilder->getQuery()->getArrayResult();
    }

    public function gerarDadosRelatorioOcorrencias($parametros)
    {
        $queryBuilder = $this->createQueryBuilder('oc')
            ->select([
                'oco.descricao as origem',
                'toc.descricao as categoria',
                'toc_pai.descricao as sub_categoria',
                'alu_pes.nome_contato as aluno',
                'fun_pes.nome_contato as funcionario',
                "date_format(oc.data_criacao, '%Y-%m-%d') as data_criacao",
                "date_format(oc.data_conclusao, '%Y-%m-%d') as data_conclusao",
                "date_format(oc.data_proximo_contato, '%Y-%m-%d %H:%i') as data_proximo_contato",
                'oc.situacao',
                'ocd.texto as detalhes'
            ])
            ->leftJoin('oc.aluno','alu')
            ->leftJoin('alu.pessoa', 'alu_pes')
            ->leftJoin('oc.funcionario', 'fun')
            ->leftJoin('fun.pessoa', 'fun_pes')
            ->leftJoin('oc.tipo_ocorrencia', 'toc')
            ->leftJoin('oc.origem_ocorrencia', 'oco')
            ->leftJoin('toc.tipo_pai', 'toc_pai')
            ->leftJoin('oc.ocorrenciaAcademicaDetalhes','ocd')
            ->where('oc.franqueada = :franqueada')
            ->setParameter('franqueada', $parametros[ConstanteParametros::CHAVE_FRANQUEADA]);

        if(isset($parametros[ConstanteParametros::CHAVE_SITUACAO])){
            $queryBuilder->andWhere('oc.situacao IN (:situacao)')
                ->setParameter('situacao', $parametros[ConstanteParametros::CHAVE_SITUACAO]);
        }

        if(isset($parametros[ConstanteParametros::CHAVE_FUNCIONARIO_RESPONSAVEL])){
            $queryBuilder->andWhere('oc.funcionario = (:funcionario)')
                ->setParameter('funcionario', $parametros[ConstanteParametros::CHAVE_FUNCIONARIO_RESPONSAVEL]);
        }

        if(isset($parametros[ConstanteParametros::CHAVE_ALUNO])){
            $queryBuilder->andWhere('oc.aluno = :aluno')
                ->setParameter('aluno', $parametros[ConstanteParametros::CHAVE_ALUNO]);
        }

        if(isset($parametros[ConstanteParametros::CHAVE_TIPO_OCORRENCIA])){
            $queryBuilder->andWhere('oc.tipo_ocorrencia = :tipo_ocorrencia')
                ->setParameter('tipo_ocorrencia', $parametros[ConstanteParametros::CHAVE_TIPO_OCORRENCIA]);
        }

        if(isset($parametros[ConstanteParametros::CHAVE_DATA_INICIAL])) {
            $dataInicial = strtotime(str_replace("/", "-",$parametros[ConstanteParametros::CHAVE_DATA_INICIAL] . " 00:00:00"));
            $dataInicial = date('Y-m-d H:i:s', $dataInicial);
            $queryBuilder->andWhere("oc.data_criacao >= :data_inicial");
            $queryBuilder->setParameter('data_inicial', $dataInicial);
        }
        if(isset($parametros[ConstanteParametros::CHAVE_DATA_FINAL])) {
            $dataFinal = strtotime(str_replace("/", "-",$parametros[ConstanteParametros::CHAVE_DATA_FINAL] . " 23:59:59"));
            $dataFinal = date( 'Y-m-d H:i:s', $dataFinal);
            $queryBuilder->andWhere("oc.data_criacao <= :data_final");
            $queryBuilder->setParameter('data_final', $dataFinal);
        }

        if(isset($parametros[ConstanteParametros::CHAVE_DATA_CONTATO_INICIAL])){
            $dataInicial = strtotime(str_replace("/", "-",$parametros[ConstanteParametros::CHAVE_DATA_CONTATO_INICIAL] . " 00:00:00"));
            $dataInicial = date('Y-m-d H:i:s', $dataInicial);
            $queryBuilder->andWhere("oc.data_proximo_contato >= :data_contato_inicial");
            $queryBuilder->setParameter('data_contato_inicial', $dataInicial);
        }

        if(isset($parametros[ConstanteParametros::CHAVE_DATA_CONTATO_FINAL])){
            $dataFinal = strtotime(str_replace("/", "-",$parametros[ConstanteParametros::CHAVE_DATA_CONTATO_FINAL] . " 23:59:59"));
            $dataFinal = date( 'Y-m-d H:i:s', $dataFinal);
            $queryBuilder->andWhere("oc.data_proximo_contato <= :data_contato_final");
            $queryBuilder->setParameter('data_contato_final', $dataFinal);
        }
        
        return $queryBuilder->getQuery()->getResult();
    }
}
