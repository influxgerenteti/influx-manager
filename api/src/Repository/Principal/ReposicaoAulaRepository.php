<?php

namespace App\Repository\Principal;

use App\Entity\Principal\ReposicaoAula;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Symfony\Bridge\Doctrine\RegistryInterface;
use App\Helper\VariaveisCompartilhadas;
use App\Helper\ConstanteParametros;

/**
 * @method ReposicaoAula|null find($id, $lockMode = null, $lockVersion = null)
 * @method ReposicaoAula|null findOneBy(array $criteria, array $orderBy = null)
 * @method ReposicaoAula[]    findAll()
 * @method ReposicaoAula[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class ReposicaoAulaRepository extends ServiceEntityRepository
{


    public function __construct(RegistryInterface $registry)
    {
        parent::__construct($registry, ReposicaoAula::class);
    }

    /**
     * Monta query principal
     *
     * @return \Doctrine\ORM\QueryBuilder
     */
    protected function montaQueryBase()
    {
        $queryBuilder = $this->createQueryBuilder("ra");
        $queryBuilder->addSelect("al");
        $queryBuilder->addSelect("p");
        $queryBuilder->addSelect("t");
        $queryBuilder->addSelect("tliv");
        $queryBuilder->addSelect("plan");
        $queryBuilder->addSelect("liv");
        $queryBuilder->addSelect("lic");
        $queryBuilder->addSelect("it");
        $queryBuilder->addSelect("tpit");
        $queryBuilder->addSelect("sf");
        $queryBuilder->addSelect("sl");
        $queryBuilder->addSelect("us");
        $queryBuilder->addSelect("re");
        $queryBuilder->addSelect("fc");
        $queryBuilder->addSelect("oca");
        $queryBuilder->addSelect("ocad");
        $queryBuilder->addSelect("func");
        $queryBuilder->addSelect("av");
        $queryBuilder->addSelect("aaTurma");
        $queryBuilder->addSelect("avnmto");
        $queryBuilder->addSelect("avnfo");
        $queryBuilder->addSelect("avnrmto");
        $queryBuilder->addSelect("avnrfo");
        $queryBuilder->addSelect("nmto");
        $queryBuilder->addSelect("nfo");
        $queryBuilder->addSelect("nrmto");
        $queryBuilder->addSelect("nrfo");
        $queryBuilder->leftJoin("ra.franqueada", "fran");
        $queryBuilder->leftJoin("ra.aluno", "al");
        $queryBuilder->leftJoin("al.pessoa", "p");
        $queryBuilder->leftJoin("ra.turma", "t");
        $queryBuilder->leftJoin("t.livro", "tliv");
        $queryBuilder->leftJoin("tliv.planejamento_licao", "plan");
        $queryBuilder->leftJoin("ra.livro", "liv");
        $queryBuilder->leftJoin("ra.licao", "lic");
        $queryBuilder->leftJoin("ra.item", "it");
        $queryBuilder->leftJoin("it.tipo_item", "tpit");
        $queryBuilder->leftJoin("ra.sala_franqueada", "sf");
        $queryBuilder->leftJoin("sf.sala", "sl");
        $queryBuilder->leftJoin("ra.usuario_solicitante", "us");
        $queryBuilder->leftJoin("ra.responsavel_execucao", "re");
        $queryBuilder->leftJoin("ra.forma_cobranca", "fc");
        $queryBuilder->leftJoin("ra.ocorrencia_academica", "oca");
        $queryBuilder->leftJoin("oca.ocorrenciaAcademicaDetalhes", "ocad");
        $queryBuilder->leftJoin("ocad.funcionario", "func");
        $queryBuilder->leftJoin("al.alunoAvaliacaos", "av", "WITH", "av.turma = t AND av.livro = liv.id");
        $queryBuilder->leftJoin("av.turma", "aaTurma");
        $queryBuilder->leftJoin("av.nota_mid_term_oral", "avnmto");
        $queryBuilder->leftJoin("av.nota_final_oral", "avnfo");
        $queryBuilder->leftJoin("av.nota_retake_mid_term_oral", "avnrmto");
        $queryBuilder->leftJoin("av.nota_retake_final_oral", "avnrfo");
        $queryBuilder->leftJoin("ra.nota_mid_term_oral", "nmto");
        $queryBuilder->leftJoin("ra.nota_final_oral", "nfo");
        $queryBuilder->leftJoin("ra.nota_retake_mid_term_oral", "nrmto");
        $queryBuilder->leftJoin("ra.nota_retake_final_oral", "nrfo");

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
            $queryBuilder->andWhere("ra.data_hora_inicio >= :dataAgendamentoDe");
            $queryBuilder->setParameter("dataAgendamentoDe", $parametros[ConstanteParametros::CHAVE_DATA_AGENDAMENTO_DE]);
        }

        if ((isset($parametros[ConstanteParametros::CHAVE_DATA_AGENDAMENTO_ATE]) === true)&&(empty($parametros[ConstanteParametros::CHAVE_DATA_AGENDAMENTO_ATE]) === false)) {
            $queryBuilder->andWhere("ra.data_hora_inicio <= :dataAgendamentoAte");
            $queryBuilder->setParameter("dataAgendamentoAte", $parametros[ConstanteParametros::CHAVE_DATA_AGENDAMENTO_ATE]);
        }

        if ((isset($parametros[ConstanteParametros::CHAVE_ITEM]) === true)&&(empty($parametros[ConstanteParametros::CHAVE_ITEM]) === false)) {
            $queryBuilder->andWhere("it.id = :itemId");
            $queryBuilder->setParameter("itemId", $parametros[ConstanteParametros::CHAVE_ITEM]);
        }

        if ((isset($parametros[ConstanteParametros::CHAVE_RESPONSAVEL_EXECUCAO]) === true)&&(empty($parametros[ConstanteParametros::CHAVE_RESPONSAVEL_EXECUCAO]) === false)) {
            $queryBuilder->andWhere("re.id = :responsavelId");
            $queryBuilder->setParameter("responsavelId", $parametros[ConstanteParametros::CHAVE_RESPONSAVEL_EXECUCAO]);
        }

        if ((isset($parametros[ConstanteParametros::CHAVE_SITUACAO]) === true)&&(empty($parametros[ConstanteParametros::CHAVE_SITUACAO]) === false)) {
            $queryBuilder->andWhere("ra.situacao IN(:situacoes)");
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
     * Filtra a Reposicao de aula por pagina
     *
     * @param array $parametros
     * @param number $pagina
     * @param number $numeroItensPorPagina
     *
     * @return \Knp\Component\Pager\Pagination\SlidingPagination
     */
    public function filtrarReposicaoAulaPorPagina($parametros, $pagina=1, $numeroItensPorPagina=50)
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
     * Buscar reposição por id
     *
     * @param int $id
     *
     * @return array|NULL
     */
    public function buscarPorId($id)
    {

        $queryBuilder = $this->montaQueryBase();
        $this->filtrarFranqueada($queryBuilder);
        $queryBuilder->andWhere("ra.id = :id");
        $queryBuilder->setParameter("id", $id);
        return  \App\Helper\FunctionHelper::retornaArrayNull($queryBuilder, true);
    }


}
