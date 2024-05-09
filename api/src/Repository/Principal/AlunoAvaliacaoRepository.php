<?php

namespace App\Repository\Principal;

use App\Entity\Principal\AlunoAvaliacao;
use App\Helper\ConstanteParametros;
use App\Helper\VariaveisCompartilhadas;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Common\Persistence\ManagerRegistry;
use Symfony\Bridge\Doctrine\RegistryInterface;


/**
 * @method AlunoAvaliacao|null find($id, $lockMode = null, $lockVersion = null)
 * @method AlunoAvaliacao|null findOneBy(array $criteria, array $orderBy = null)
 * @method AlunoAvaliacao[]    findAll()
 * @method AlunoAvaliacao[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class AlunoAvaliacaoRepository extends ServiceEntityRepository
{

    private ManagerRegistry $managerRegister;



    public function __construct(RegistryInterface $registry, ManagerRegistry $managerRegister)
    {
        parent::__construct($registry, AlunoAvaliacao::class);
        $this->managerRegister = $managerRegister;

    }

    /**
     * Busca AlunoAvaliacao através dos parametros informados
     *
     * @param int $franqueadaId
     * @param int $alunoId
     * @param int $livroId
     * @param int $turmaId
     *
     * @return \App\Entity\Principal\AlunoAvaliacao|NULL
     */
    public function buscaAlunoAvalicaoPorFranqueadaAlunoLivroTurma($franqueadaId, $alunoId, $livroId, $turmaId)
    {
        $queryBuilder = $this->createQueryBuilder("av");
        $queryBuilder->addSelect("fran");
        $queryBuilder->addSelect("al");
        $queryBuilder->addSelect("liv");
        $queryBuilder->addSelect("tur");
        $queryBuilder->leftJoin("av.franqueada", "fran");
        $queryBuilder->leftJoin("av.aluno", "al");
        $queryBuilder->leftJoin("av.livro", "liv");
        $queryBuilder->leftJoin("av.turma", "tur");
        $queryBuilder->andWhere("fran.id = :franqueadaId");
        $queryBuilder->andWhere("al.id = :alunoId");
        $queryBuilder->andWhere("liv.id = :livroId");
        $queryBuilder->andWhere("tur.id = :turmaId");
        $queryBuilder->setParameter("franqueadaId", $franqueadaId);
        $queryBuilder->setParameter("alunoId", $alunoId);
        $queryBuilder->setParameter("livroId", $livroId);
        $queryBuilder->setParameter("turmaId", $turmaId);

        return $queryBuilder->getQuery()->getOneOrNullResult();
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
        $qtdade_de  = '';
        $qtdade_ate = '';

        $queryBuilder = $this->createQueryBuilder("aa");
        $queryBuilder->select('aa.id');
        $queryBuilder->join('aa.turma', 't');
        $queryBuilder->join('aa.aluno', 'a');
        $queryBuilder->join('a.pessoa', 'p');

        $queryBuilder->where('aa.franqueada = :franqueada');
        $queryBuilder->setParameter('franqueada', VariaveisCompartilhadas::$franqueadaID);

        $queryBuilder->andWhere("t.intensidade = 'R'");

        if ((isset($parametros[ConstanteParametros::CHAVE_TURMA]) === true)&&(is_null($parametros[ConstanteParametros::CHAVE_TURMA]) === false)) {
            $queryBuilder->andWhere('aa.turma = :turma');
            $queryBuilder->setParameter('turma', $parametros[ConstanteParametros::CHAVE_TURMA]);
        }

        if ((isset($parametros[ConstanteParametros::CHAVE_FUNCIONARIO]) === true)&&(is_null($parametros[ConstanteParametros::CHAVE_FUNCIONARIO]) === false)) {
            $queryBuilder->andWhere('t.funcionario = :funcionario');
            $queryBuilder->setParameter('funcionario', $parametros[ConstanteParametros::CHAVE_FUNCIONARIO]);
        }

        if ((isset($parametros[ConstanteParametros::CHAVE_ALUNO]) === true)&&(is_null($parametros[ConstanteParametros::CHAVE_ALUNO]) === false)) {
            $queryBuilder->andWhere('aa.aluno = :aluno');
            $queryBuilder->setParameter('aluno', $parametros[ConstanteParametros::CHAVE_ALUNO]);
        }

        if ((isset($parametros[ConstanteParametros::CHAVE_LIVRO]) === true)&&(is_null($parametros[ConstanteParametros::CHAVE_LIVRO]) === false)) {
            $queryBuilder->andWhere('aa.livro = :livro');
            $queryBuilder->setParameter('livro', $parametros[ConstanteParametros::CHAVE_LIVRO]);
        }

        if ((isset($parametros[ConstanteParametros::CHAVE_CLASSIFICACAO_ALUNO]) === true)&&(is_null($parametros[ConstanteParametros::CHAVE_CLASSIFICACAO_ALUNO]) === false)) {
            $situacao = explode(',', $parametros[ConstanteParametros::CHAVE_CLASSIFICACAO_ALUNO]);
            $queryBuilder->andWhere("a.classificacao_aluno IN (:classificacao_aluno)");
            $queryBuilder->setParameter('classificacao_aluno', implode("', '", $situacao));
        }

        if ((isset($parametros[ConstanteParametros::CHAVE_QUANTIDADE_FALTAS_DE]) === true)&&(is_null($parametros[ConstanteParametros::CHAVE_QUANTIDADE_FALTAS_DE]) === false)) {
            $subQuery = $this->_em->createQueryBuilder();
            $subQuery->select("COUNT(ad1.presenca)");
            $subQuery->from("aluno_diario", "ad1");
            $subQuery->innerJoin("turma_aula", "ta1");
            $subQuery->where($subQuery->expr()->eq("ad1.turma_aula_id", "ta1.id"));
            $subQuery->andWhere($subQuery->expr()->eq("ad1.presenca", "'P'"));
            $subQuery->andWhere($subQuery->expr()->eq("ta1.turma_id", "aa.turma_id"));
            $subQuery->andWhere($subQuery->expr()->eq("ad1.aluno_id", "aa.aluno_id"));

            $qtdade_de = " and (" . $subQuery->getDQL() . ") >=" . $parametros[ConstanteParametros::CHAVE_QUANTIDADE_FALTAS_DE];
        }

        if ((isset($parametros[ConstanteParametros::CHAVE_QUANTIDADE_FALTAS_ATE]) === true)&&(is_null($parametros[ConstanteParametros::CHAVE_QUANTIDADE_FALTAS_ATE]) === false)) {
            $subQuery = $this->_em->createQueryBuilder();
            $subQuery->select("COUNT(ad2.presenca)");
            $subQuery->from("aluno_diario", "ad2");
            $subQuery->innerJoin("turma_aula", "ta2");
            $subQuery->where($subQuery->expr()->eq("ad2.turma_aula_id", "ta2.id"));
            $subQuery->andWhere($subQuery->expr()->eq("ad2.presenca", "'P'"));
            $subQuery->andWhere($subQuery->expr()->eq("ta2.turma_id", "aa.turma_id"));
            $subQuery->andWhere($subQuery->expr()->eq("ad2.aluno_id", "aa.aluno_id"));

            $qtdade_ate = " and (" . $subQuery->getDQL() . ") >=" . $parametros[ConstanteParametros::CHAVE_QUANTIDADE_FALTAS_ATE];
        }

        // Filtra e substitui a query para passar ao Jasper
        $sql = $queryBuilder->getQuery()->getSQL();
        $sql = preg_replace('/.*WHERE\s(.*)$/', '$1', $sql);

        // Seleciona somente os wheres
        $sql = preg_replace('/a0_/', 'aa', $sql);
        $sql = preg_replace('/t1_/', 't', $sql);
        $sql = preg_replace('/a2_/', 'a', $sql);
        $sql = preg_replace('/p3_/', 'p', $sql);

        // Substituição de parâmetros
        $parameters = $queryBuilder->getParameters();
        foreach ($parameters as $parameter) {
            $param = $parameter->getValue();
            $sql   = preg_replace('/\?/', "'$param'", $sql, 1);
        }

        $sql = $sql . $qtdade_de . $qtdade_ate;

        return $sql;
    }


    /**
     * Monta a query para ser executada no relatório
     *
     * @param string $mensagemErro
     * @param array $parametros
     *
     * @return array
     */
    public function buscarDadosRelatorioNotasPersonal(&$mensagemErro, $parametros)
    {
        $queryBuilder = $this->createQueryBuilder("aa");
        $queryBuilder->select(
            [
                'a.id as aluno_id',
                'p.nome_contato as nome_aluno',
                'l.descricao as livro',
                'aa.nota_mid_term_escrita as mid_term',
                'aa.nota_final_escrita as final_test',
                'c.id as contrato_id',
            ]
        );
        $queryBuilder->join('aa.livro', 'l');
        $queryBuilder->join('aa.contrato', 'c');
        $queryBuilder->join('c.aluno', 'a');
        $queryBuilder->join('a.pessoa', 'p');
        $queryBuilder->where('aa.personal = 1');

        $queryBuilder->andWhere('aa.franqueada = :franqueada');
        $queryBuilder->setParameter('franqueada', VariaveisCompartilhadas::$franqueadaID);
        if ((isset($parametros[ConstanteParametros::CHAVE_DATA_PERIODO_DE]) === true)&&(is_null($parametros[ConstanteParametros::CHAVE_DATA_PERIODO_DE]) === false)) {
            $queryBuilder->andWhere('c.data_inicio_contrato >= :periodo_de');
            $queryBuilder->setParameter('periodo_de', $parametros[ConstanteParametros::CHAVE_DATA_PERIODO_DE]);
        }

        if ((isset($parametros[ConstanteParametros::CHAVE_DATA_PERIODO_ATE]) === true)&&(is_null($parametros[ConstanteParametros::CHAVE_DATA_PERIODO_ATE]) === false)) {
            $queryBuilder->andWhere('c.data_termino_contrato <= :periodo_ate');
            $queryBuilder->setParameter('periodo_ate', $parametros[ConstanteParametros::CHAVE_DATA_PERIODO_ATE]);
        }

        if ((isset($parametros[ConstanteParametros::CHAVE_LIVRO]) === true)&&(is_null($parametros[ConstanteParametros::CHAVE_LIVRO]) === false)) {
            $queryBuilder->andWhere('aa.livro = :livro');
            $queryBuilder->setParameter('livro', $parametros[ConstanteParametros::CHAVE_LIVRO]);
        }

        if ((isset($parametros[ConstanteParametros::CHAVE_VALOR_MID_TERM_MIN]) === true)&&(is_null($parametros[ConstanteParametros::CHAVE_VALOR_MID_TERM_MIN]) === false)) {
            $queryBuilder->andWhere('COALESCE(aa.nota_mid_term_escrita, 0) >= :valor_mid_term_min');
            $queryBuilder->setParameter('valor_mid_term_min', $parametros[ConstanteParametros::CHAVE_VALOR_MID_TERM_MIN]);
        }

        if ((isset($parametros[ConstanteParametros::CHAVE_VALOR_MID_TERM_MAX]) === true)&&(is_null($parametros[ConstanteParametros::CHAVE_VALOR_MID_TERM_MAX]) === false)) {
            $queryBuilder->andWhere('COALESCE(aa.nota_mid_term_escrita, 0) <= :valor_mid_term_max');
            $queryBuilder->setParameter('valor_mid_term_max', $parametros[ConstanteParametros::CHAVE_VALOR_MID_TERM_MAX]);
        }

        if ((isset($parametros[ConstanteParametros::CHAVE_VALOR_FINAL_TEST_MIN]) === true)&&(is_null($parametros[ConstanteParametros::CHAVE_VALOR_FINAL_TEST_MIN]) === false)) {
            $queryBuilder->andWhere('COALESCE(aa.nota_final_escrita, 0) >= :valor_final_test_min');
            $queryBuilder->setParameter('valor_final_test_min', $parametros[ConstanteParametros::CHAVE_VALOR_FINAL_TEST_MIN]);
        }

        if ((isset($parametros[ConstanteParametros::CHAVE_VALOR_FINAL_TEST_MAX]) === true)&&(is_null($parametros[ConstanteParametros::CHAVE_VALOR_FINAL_TEST_MAX]) === false)) {
            $queryBuilder->andWhere('COALESCE(aa.nota_final_escrita, 0) <= :valor_final_test_max');
            $queryBuilder->setParameter('valor_final_test_max', $parametros[ConstanteParametros::CHAVE_VALOR_FINAL_TEST_MAX]);
        }

        // TODO: Montar filtro nota WG minima
        // TODO: Montar filtro nota WG maxima
        return $queryBuilder->getQuery()->getResult(\Doctrine\ORM\Query::HYDRATE_ARRAY);
    }

    /**
     * Retorna a média da turma fornecida
     *
     * @param int $idTurma
     *
     * @return float
     */
    public function getMediaTurma($idTurma)
    {
        // TODO: Fazer
    }

    /**
     * Monta a query para ser executada no relatório
     *
     * @param string $mensagemErro
     * @param array $parametros
     *
     * @return array
     */
    public function buscarDadosRelatorioNotasAgrupadoTurma(&$mensagemErro, $parametros)
    {

        $queryBuilder = $this->createQueryBuilder("aa");
        $queryBuilder->select(
            [
                'a.id as aluno_id',
                'p.nome_contato as nome_aluno',
                'aa.nota_mid_term_escrita as mid_term',
                'aa.nota_final_escrita as final_test',
                't.id as turma_id',
                't.descricao as turma_descricao',
                'l.descricao as livro_descricao',
                'f.apelido as instrutor_apelido',
            ]
        );
        $queryBuilder->join('aa.livro', 'l');
        $queryBuilder->join('aa.contrato', 'c');
        $queryBuilder->join('c.aluno', 'a');
        $queryBuilder->join('a.pessoa', 'p');
        $queryBuilder->join('aa.turma', 't');
        $queryBuilder->join('t.funcionario', 'f');

        $queryBuilder->andWhere('aa.franqueada = :franqueada');
        $queryBuilder->andWhere('t.excluido = 0');
        $queryBuilder->setParameter('franqueada', VariaveisCompartilhadas::$franqueadaID);

        // TODO: Montar filtro nota WG minima
        // TODO: Montar filtro nota WG maxima
        if ((isset($parametros[ConstanteParametros::CHAVE_INSTRUTOR_FLAG]) === true)&&(is_null($parametros[ConstanteParametros::CHAVE_INSTRUTOR_FLAG]) === false)) {
            $queryBuilder->andWhere('f.id = :instrutor');
            $queryBuilder->setParameter('instrutor', $parametros[ConstanteParametros::CHAVE_INSTRUTOR_FLAG]);
        }

        if ((isset($parametros[ConstanteParametros::CHAVE_TURMA]) === true)&&(is_null($parametros[ConstanteParametros::CHAVE_TURMA]) === false)) {
            $queryBuilder->andWhere('t.id = :turma');
            $queryBuilder->setParameter('turma', $parametros[ConstanteParametros::CHAVE_TURMA]);
        }

        if (isset($parametros[ConstanteParametros::CHAVE_SITUACAO_TURMA]) === true && count($parametros[ConstanteParametros::CHAVE_SITUACAO_TURMA]) > 0) {
            $queryBuilder->andWhere("t.situacao in ('" . implode("', '", $parametros[ConstanteParametros::CHAVE_SITUACAO_TURMA]) . "')");
        }

        if (isset($parametros[ConstanteParametros::CHAVE_MODALIDADE_TURMA]) === true && count($parametros[ConstanteParametros::CHAVE_MODALIDADE_TURMA]) > 0) {
            $queryBuilder->andWhere("m.tipo in ('" . implode("', '", $parametros[ConstanteParametros::CHAVE_MODALIDADE_TURMA]) . "')");
        }

        if ((isset($parametros[ConstanteParametros::CHAVE_DATA_PERIODO_DE]) === true)&&(is_null($parametros[ConstanteParametros::CHAVE_DATA_PERIODO_DE]) === false)) {
            $queryBuilder->andWhere('c.data_inicio_contrato >= :periodo_de');
            $queryBuilder->setParameter('periodo_de', $parametros[ConstanteParametros::CHAVE_DATA_PERIODO_DE]);
        }

        if ((isset($parametros[ConstanteParametros::CHAVE_DATA_PERIODO_ATE]) === true)&&(is_null($parametros[ConstanteParametros::CHAVE_DATA_PERIODO_ATE]) === false)) {
            $queryBuilder->andWhere('c.data_termino_contrato <= :periodo_ate');
            $queryBuilder->setParameter('periodo_ate', $parametros[ConstanteParametros::CHAVE_DATA_PERIODO_ATE]);
        }

        if ((isset($parametros[ConstanteParametros::CHAVE_LIVRO]) === true)&&(is_null($parametros[ConstanteParametros::CHAVE_LIVRO]) === false)) {
            $queryBuilder->andWhere('aa.livro = :livro');
            $queryBuilder->setParameter('livro', $parametros[ConstanteParametros::CHAVE_LIVRO]);
        }

        if ((isset($parametros[ConstanteParametros::CHAVE_VALOR_MID_TERM_MIN]) === true)&&(is_null($parametros[ConstanteParametros::CHAVE_VALOR_MID_TERM_MIN]) === false)) {
            $queryBuilder->andWhere('COALESCE(aa.nota_mid_term_escrita, 0) >= :valor_mid_term_min');
            $queryBuilder->setParameter('valor_mid_term_min', $parametros[ConstanteParametros::CHAVE_VALOR_MID_TERM_MIN]);
        }

        if ((isset($parametros[ConstanteParametros::CHAVE_VALOR_MID_TERM_MAX]) === true)&&(is_null($parametros[ConstanteParametros::CHAVE_VALOR_MID_TERM_MAX]) === false)) {
            $queryBuilder->andWhere('COALESCE(aa.nota_mid_term_escrita, 0) <= :valor_mid_term_max');
            $queryBuilder->setParameter('valor_mid_term_max', $parametros[ConstanteParametros::CHAVE_VALOR_MID_TERM_MAX]);
        }

        if ((isset($parametros[ConstanteParametros::CHAVE_VALOR_FINAL_TEST_MIN]) === true)&&(is_null($parametros[ConstanteParametros::CHAVE_VALOR_FINAL_TEST_MIN]) === false)) {
            $queryBuilder->andWhere('COALESCE(aa.nota_final_escrita, 0) >= :valor_final_test_min');
            $queryBuilder->setParameter('valor_final_test_min', $parametros[ConstanteParametros::CHAVE_VALOR_FINAL_TEST_MIN]);
        }

        if ((isset($parametros[ConstanteParametros::CHAVE_VALOR_FINAL_TEST_MAX]) === true)&&(is_null($parametros[ConstanteParametros::CHAVE_VALOR_FINAL_TEST_MAX]) === false)) {
            $queryBuilder->andWhere('COALESCE(aa.nota_final_escrita, 0) <= :valor_final_test_max');
            $queryBuilder->setParameter('valor_final_test_max', $parametros[ConstanteParametros::CHAVE_VALOR_FINAL_TEST_MAX]);
        }
        return $queryBuilder->getQuery()->getResult(\Doctrine\ORM\Query::HYDRATE_ARRAY);
    }

    /**
     * Monta a query para ser executada no relatório
     *
     * @param string $mensagemErro
     * @param array $parametros
     *
     * @return array
     */
    public function buscarDadosRelatorioNotasAlunosAgrupadoTurma(&$mensagemErro, $parametros)
    {

        $franqueada = VariaveisCompartilhadas::$franqueadaID;  

        $sql ="SELECT
                    a0_.id AS aluno_id,
                    p0_.nome_contato AS nome_aluno,
                    aa.nota_mid_term_escrita AS mid_term,
                    aa.nota_final_escrita AS final_test,
                    t3_.id AS turma_id,
                    t3_.descricao AS turma_descricao,
                    l0_.descricao AS livro_descricao,
                    f.apelido as instrutor_apelido
                FROM aluno a0_
                INNER JOIN pessoa p0_ ON a0_.pessoa_id = p0_.id
                INNER JOIN contrato c6_ ON c6_.aluno_id  = a0_.id
                INNER JOIN turma t3_ ON	c6_.turma_id = t3_.id
                LEFT JOIN livro l0_ on c6_.livro_id = l0_.id
                LEFT JOIN aluno_avaliacao aa on aa.livro_id = l0_.id and aa.aluno_id = a0_.id and aa.turma_id = t3_.id
                INNER JOIN funcionario f ON	t3_.funcionario_id = f.id

                where (c6_.franqueada_id = {$franqueada}  and c6_.situacao <> 'C') and 
                      (c6_.excluido = 0) ";

            if ((isset($parametros[ConstanteParametros::CHAVE_ALUNO]) === true)&&(is_null($parametros[ConstanteParametros::CHAVE_ALUNO]) === false)) {
                $alunoId = $parametros[ConstanteParametros::CHAVE_ALUNO];
                $sql = $sql. " and (a0_.id = {$alunoId}) ";
            }

            if ((isset($parametros[ConstanteParametros::CHAVE_INSTRUTOR_FLAG]) === true)&&(is_null($parametros[ConstanteParametros::CHAVE_INSTRUTOR_FLAG]) === false)) {
                $instrutorId = $parametros[ConstanteParametros::CHAVE_INSTRUTOR_FLAG];
                $sql = $sql. " and (f.id = {$instrutorId}) ";
                
            }

            if ((isset($parametros[ConstanteParametros::CHAVE_TURMA]) === true)&&(is_null($parametros[ConstanteParametros::CHAVE_TURMA]) === false)) {
                $turmaId = $parametros[ConstanteParametros::CHAVE_TURMA];
                $sql = $sql. " and (t3_.id = {$turmaId}) ";
            }

            if (isset($parametros[ConstanteParametros::CHAVE_SITUACAO_TURMA]) === true && count($parametros[ConstanteParametros::CHAVE_SITUACAO_TURMA]) > 0) {
                $situacaoTurma = "'". implode("', '", $parametros[ConstanteParametros::CHAVE_SITUACAO_TURMA]) . "'";
                $sql = $sql. " and (t3_.situacao in  ({$situacaoTurma})) ";                
            }

            if (isset($parametros[ConstanteParametros::CHAVE_MODALIDADE_TURMA]) === true && count($parametros[ConstanteParametros::CHAVE_MODALIDADE_TURMA]) > 0) {
                $modalidade_turma_id = "'". implode("', '", $parametros[ConstanteParametros::CHAVE_MODALIDADE_TURMA]) . "'";
                $sql = $sql. " and (t3_.modalidade_turma_id in  ({$modalidade_turma_id})) ";
           }

            if ((isset($parametros[ConstanteParametros::CHAVE_DATA_PERIODO_DE]) === true)&&(is_null($parametros[ConstanteParametros::CHAVE_DATA_PERIODO_DE]) === false)) {
                $date=$parametros[ConstanteParametros::CHAVE_DATA_PERIODO_DE];
                $dataAlt      = strtotime($date);
                $periodo_de = strval(getdate($dataAlt)["year"]) . '-' . strval(getdate($dataAlt)["mon"]) . '-' . strval(getdate($dataAlt)["mday"]);
                $sql = $sql. " and (c6_.data_inicio_contrato >= '{$periodo_de} 00:00:01') ";
     //           $sql = $sql. " and (c6_.data_inicio_contrato >= {$periodo_de}) ";
            }
    
            if ((isset($parametros[ConstanteParametros::CHAVE_DATA_PERIODO_ATE]) === true)&&(is_null($parametros[ConstanteParametros::CHAVE_DATA_PERIODO_ATE]) === false)) {
                $date=$parametros[ConstanteParametros::CHAVE_DATA_PERIODO_ATE];
                $dataAlt      = strtotime($date);
                $periodo_ate = strval(getdate($dataAlt)["year"]) . '-' . strval(getdate($dataAlt)["mon"]) . '-' . strval(getdate($dataAlt)["mday"]);
                $sql = $sql. " and (c6_.data_termino_contrato <= '{$periodo_ate} 23:23:59') "; 
            }           

            if ((isset($parametros[ConstanteParametros::CHAVE_LIVRO]) === true)&&(is_null($parametros[ConstanteParametros::CHAVE_LIVRO]) === false)) {
                $livroId = $parametros[ConstanteParametros::CHAVE_LIVRO];
                $sql = $sql. " and (l0_.id = {$livroId}) ";
            }


            if ((isset($parametros[ConstanteParametros::CHAVE_VALOR_MID_TERM_MIN]) === true)&&(is_null($parametros[ConstanteParametros::CHAVE_VALOR_MID_TERM_MIN]) === false)) {
                $valorMidTermMin = $parametros[ConstanteParametros::CHAVE_VALOR_MID_TERM_MIN];
                $sql = $sql. " and (COALESCE(aa.nota_mid_term_escrita, 0) >= {$valorMidTermMin}) ";
           }

            if ((isset($parametros[ConstanteParametros::CHAVE_VALOR_MID_TERM_MAX]) === true)&&(is_null($parametros[ConstanteParametros::CHAVE_VALOR_MID_TERM_MAX]) === false)) {
                $valorMidTermMax = $parametros[ConstanteParametros::CHAVE_VALOR_MID_TERM_MAX];
                $sql = $sql. " and (COALESCE(aa.nota_mid_term_escrita, 0) <= {$valorMidTermMax}) ";
            }

            if ((isset($parametros[ConstanteParametros::CHAVE_VALOR_FINAL_TEST_MIN]) === true)&&(is_null($parametros[ConstanteParametros::CHAVE_VALOR_FINAL_TEST_MIN]) === false)) {
                $valorFinalTestMin = $parametros[ConstanteParametros::CHAVE_VALOR_FINAL_TEST_MIN];
                $sql = $sql. " and (COALESCE(aa.nota_final_escrita, 0) >= {$valorFinalTestMin}) ";
            }

            if ((isset($parametros[ConstanteParametros::CHAVE_VALOR_FINAL_TEST_MAX]) === true)&&(is_null($parametros[ConstanteParametros::CHAVE_VALOR_FINAL_TEST_MAX]) === false)) {
                $valorFinalTestMax = $parametros[ConstanteParametros::CHAVE_VALOR_FINAL_TEST_MAX];
                $sql = $sql. " and (COALESCE(aa.nota_final_escrita, 0) <= {$valorFinalTestMax}) ";
            }

            return $this->managerRegister->getConnection()->fetchAllAssociative($sql);
    }

    /**
     * Monta a query para ser executada no relatório
     *
     * @param string $mensagemErro
     * @param array $parametros
     *
     * @return array
     */
    public function buscarDadosRelatorioNotasTurmasAlunosAgrupadoTurma(&$mensagemErro, $parametros)
    {
        $franqueada = VariaveisCompartilhadas::$franqueadaID;  

        $sql ="SELECT
                    t3_.id AS turma_id,
                    t3_.descricao AS turma,
                    t3_.modalidade_turma_id AS modalidade_id,
                    l0_.descricao AS livro,
                    f.apelido as professor,
                    (SELECT SUM(CASE ad.presenca WHEN 'P' THEN 1 WHEN 'R' THEN 1 ELSE 0 END) / SUM(CASE ad.presenca WHEN 'P' THEN 1 WHEN 'R' THEN 1 WHEN 'F' THEN 1 ELSE 0 END) * 100  AS media) AS mediaFrequencia,
                    AVG(aa.nota_mid_term_test) as mediaMidTermTest,
                    AVG(aa.nota_mid_term_composition) as mediaMidTermComposition,
                    AVG(aa.nota_retake_mid_term_escrita) as midTermRetake,
                    AVG(aa.nota_final_test) as mediaFinalTest,
                    AVG(aa.nota_final_composition) as mediaFinalComposition,
                    AVG(aa.nota_retake_final_escrita) as mediaFinalRetake
                FROM aluno a0_
                INNER JOIN contrato c6_ ON c6_.aluno_id = a0_.id
                INNER JOIN turma t3_ ON c6_.turma_id = t3_.id
                INNER JOIN turma_aula ta ON ta.turma_id = t3_.id
                INNER JOIN aluno_diario ad ON ad.turma_aula_id = ta.id
                LEFT JOIN livro l0_ ON c6_.livro_id = l0_.id
                LEFT JOIN aluno_avaliacao aa ON aa.livro_id = l0_.id AND aa.aluno_id = a0_.id AND aa.turma_id = t3_.id
                INNER JOIN funcionario f ON t3_.funcionario_id = f.id

                WHERE (c6_.franqueada_id = {$franqueada} AND c6_.situacao <> 'C') AND 
                      (c6_.excluido = 0) ";
            
            $sql = $this->montarFiltrosNotasTurmas($sql, $parametros);

            $sql .= " GROUP BY t3_.id";
            
            return $this->managerRegister->getConnection()->fetchAllAssociative($sql);
    }

    /**
     * Monta a query para ser executada no relatório
     *
     * @param string $mensagemErro
     * @param array $parametros
     *
     * @return array
     */
    public function buscarDadosRelatorioNotasTurmasAlunosNovo($parametros)
    {
        $franqueada = VariaveisCompartilhadas::$franqueadaID;  

        $sql ="SELECT
                    a0_.id AS aluno_id,
                    p0_.nome_contato AS nome_aluno,
                    l0_.descricao AS livro,
                    f.apelido as professor,
                    t3_.modalidade_turma_id as modalidade_id,
                    t3_.descricao AS turma,
                    (SELECT SUM(CASE ad.presenca WHEN 'P' THEN 1 WHEN 'R' THEN 1 ELSE 0 END) / SUM(CASE ad.presenca WHEN 'P' THEN 1 WHEN 'R' THEN 1 WHEN 'F' THEN 1 ELSE 0 END) * 100  AS media) AS frequencia,                    
                    (CASE WHEN aa.nota_mid_term_test IS NULL THEN '--' ELSE aa.nota_mid_term_test END) as mid_term_test,
                    (CASE WHEN aa.nota_mid_term_composition IS NULL THEN '--' ELSE aa.nota_mid_term_composition END) as mid_term_composition,
                    (CASE WHEN aa.nota_retake_mid_term_escrita IS NULL THEN '--' ELSE aa.nota_retake_mid_term_escrita END) as mid_term_retake,
                    (CASE WHEN aa.nota_final_test IS NULL THEN '--' ELSE aa.nota_final_test END) as final_test,   
                    (CASE WHEN aa.nota_final_composition IS NULL THEN '--' ELSE aa.nota_final_composition END) as final_composition,
                    (CASE WHEN aa.nota_retake_final_escrita IS NULL THEN '--' ELSE aa.nota_retake_final_escrita END) as final_retake
                FROM aluno a0_
                INNER JOIN pessoa p0_ ON a0_.pessoa_id = p0_.id
                INNER JOIN contrato c6_ ON c6_.aluno_id  = a0_.id
                INNER JOIN turma t3_ ON	c6_.turma_id = t3_.id 
                LEFT JOIN livro l0_ on c6_.livro_id = l0_.id
                LEFT JOIN turma_aula ta ON ta.turma_id = t3_.id 
                LEFT JOIN aluno_diario ad ON ad.turma_aula_id = ta.id AND ad.aluno_id = a0_.id AND ad.livro_id = l0_.id
                LEFT JOIN aluno_avaliacao aa on aa.livro_id = l0_.id AND aa.aluno_id = a0_.id AND aa.turma_id = t3_.id AND aa.contrato_id = c6_.id
                INNER JOIN funcionario f ON	t3_.funcionario_id = f.id
                WHERE (c6_.franqueada_id = {$franqueada}  AND c6_.situacao <> 'C') AND 
                (c6_.excluido = 0) ";

        $sql = $this->montarFiltrosNotasTurmas($sql, $parametros);
        
        $sql = $sql. " GROUP BY a0_.id, t3_.id";
        
        return $this->managerRegister->getConnection()->fetchAllAssociative($sql);
    }

    /**
     * Montar filtros para o SQL que busca os dados do Relatório de Notas Turmas
     * 
     * @param string $parametros
     * @return string
     */
    public function montarFiltrosNotasTurmas($sql, $parametros){
        
        if ((isset($parametros[ConstanteParametros::CHAVE_ALUNO]) === true)&&(is_null($parametros[ConstanteParametros::CHAVE_ALUNO]) === false)) {
            $alunoId = $parametros[ConstanteParametros::CHAVE_ALUNO];
            $sql = $sql. " AND (a0_.id = {$alunoId}) ";
        }

        if ((isset($parametros[ConstanteParametros::CHAVE_INSTRUTOR_FLAG]) === true)&&(is_null($parametros[ConstanteParametros::CHAVE_INSTRUTOR_FLAG]) === false)) {
            $instrutorId = $parametros[ConstanteParametros::CHAVE_INSTRUTOR_FLAG];
            $sql = $sql. " AND (f.id = {$instrutorId}) ";
            
        }

        if ((isset($parametros[ConstanteParametros::CHAVE_TURMA]) === true)&&(is_null($parametros[ConstanteParametros::CHAVE_TURMA]) === false)) {
            $turmaId = $parametros[ConstanteParametros::CHAVE_TURMA];
            $sql = $sql. " AND (t3_.id = {$turmaId}) ";
        }

        if (isset($parametros[ConstanteParametros::CHAVE_SITUACAO_TURMA])) {
            $situacaoTurma = "'". implode("', '", $parametros[ConstanteParametros::CHAVE_SITUACAO_TURMA]) . "'";
            $sql = $sql. " AND (t3_.situacao IN  ({$situacaoTurma})) ";                
        }

        if (isset($parametros[ConstanteParametros::CHAVE_MODALIDADE_TURMA])) {
            $modalidade_turma_id = "'". implode("', '", $parametros[ConstanteParametros::CHAVE_MODALIDADE_TURMA]) . "'";
            $sql = $sql. " AND (t3_.modalidade_turma_id IN  ({$modalidade_turma_id})) ";
        }

        if(isset($parametros[ConstanteParametros::CHAVE_SEMESTRE]) && !is_null($parametros[ConstanteParametros::CHAVE_SEMESTRE])) {
            $sql = $sql . " AND (c6_.semestre_id = ". $parametros[ConstanteParametros::CHAVE_SEMESTRE] .")";
        }

        if ((isset($parametros[ConstanteParametros::CHAVE_LIVRO])) && !(is_null($parametros[ConstanteParametros::CHAVE_LIVRO]))) {
            $livroId = $parametros[ConstanteParametros::CHAVE_LIVRO];
            $sql = $sql. " AND (l0_.id = {$livroId}) ";
        }

        if ((isset($parametros[ConstanteParametros::CHAVE_VALOR_MID_TERM_MIN])) && !(is_null($parametros[ConstanteParametros::CHAVE_VALOR_MID_TERM_MIN]))) {
            $valorMidTermMin =(float) $parametros[ConstanteParametros::CHAVE_VALOR_MID_TERM_MIN];
            $sql = $sql. " AND (COALESCE(aa.nota_mid_term_escrita, 0) >= {$valorMidTermMin}) ";
        }

        if ((isset($parametros[ConstanteParametros::CHAVE_VALOR_MID_TERM_MAX])) && !(is_null($parametros[ConstanteParametros::CHAVE_VALOR_MID_TERM_MAX]))) {
            $valorMidTermMax = (float) $parametros[ConstanteParametros::CHAVE_VALOR_MID_TERM_MAX];
            $sql = $sql. " AND (COALESCE(aa.nota_mid_term_escrita, 0) <= {$valorMidTermMax}) ";
        }

        if ((isset($parametros[ConstanteParametros::CHAVE_VALOR_FINAL_TEST_MIN])) && !(is_null($parametros[ConstanteParametros::CHAVE_VALOR_FINAL_TEST_MIN]))) {
            $valorFinalTestMin = (float) $parametros[ConstanteParametros::CHAVE_VALOR_FINAL_TEST_MIN];
            $sql = $sql. " AND (COALESCE(aa.nota_final_escrita, 0) >= {$valorFinalTestMin}) ";
        }

        if ((isset($parametros[ConstanteParametros::CHAVE_VALOR_FINAL_TEST_MAX])) && !(is_null($parametros[ConstanteParametros::CHAVE_VALOR_FINAL_TEST_MAX]))) {
            $valorFinalTestMax = (float) $parametros[ConstanteParametros::CHAVE_VALOR_FINAL_TEST_MAX];
            $sql = $sql. " AND (COALESCE(aa.nota_final_escrita, 0) <= {$valorFinalTestMax}) ";
        }

        return $sql;
    }

    /**
     * Monta a query para ser executada no relatório
     *
     * @param string $mensagemErro
     * @param array $parametros
     *
     * @return array
     */
    public function buscarDadosRelatorioNotasAlunosNovo(&$mensagemErro, $parametros)
    {
        $franqueada = VariaveisCompartilhadas::$franqueadaID;  

        $sql ="SELECT
                    a0_.id AS aluno_id,
                    p0_.nome_contato AS nome_aluno,
                    l0_.descricao AS livro_descricao,
                    aa.nota_mid_term_escrita AS mid_term,
                    aa.nota_final_escrita AS final_test,
                    f.apelido as instrutor_apelido,
                    f.id as instrutor_id,
                    m5_.tipo AS modalidade_turma,
	                c6_.id AS contrato_id,
                    t3_.descricao AS turma_descricao,
                    t3_.id As turma_id,
                    t3_.data_inicio As turma_data_inicio,
                    t3_.data_fim As turma_data_fim,
                    (select count(ad.presenca)
                        from aluno_diario ad
                        inner join turma_aula ta on ad.turma_aula_id = ta.id
                        where 
                            ad.franqueada_id = c6_.franqueada_id
                            and ta.turma_id = t3_.id
                            and ad.aluno_id = c6_.aluno_id
                            and ad.presenca = 'F' ) as faltas,                    
                    (CASE  t3_.situacao
                    when 'ENC' THEN 'Encerrado' 
                    when 'ABE' THEN 'Aberto'
                    when 'FOR' THEN 'Formacao'
                    else '' end) as situacao
                FROM aluno a0_
                INNER JOIN pessoa p0_ ON a0_.pessoa_id = p0_.id
                INNER JOIN contrato c6_ ON c6_.aluno_id  = a0_.id
                INNER JOIN modalidade_turma m5_ ON c6_.modalidade_turma_id = m5_.id
                INNER JOIN turma t3_ ON	c6_.turma_id = t3_.id 
                LEFT JOIN livro l0_ on c6_.livro_id = l0_.id
                left join turma_aula ta ON ta.turma_id = t3_.id 
                LEFT JOIN aluno_avaliacao aa on aa.livro_id = l0_.id and aa.aluno_id = a0_.id and aa.turma_id = t3_.id
                INNER JOIN funcionario f ON	t3_.funcionario_id = f.id

                where (c6_.franqueada_id = {$franqueada}  and c6_.situacao <> 'C') and 
                      (c6_.excluido = 0) ";
        if ((isset($parametros[ConstanteParametros::CHAVE_ALUNO]) === true)&&(is_null($parametros[ConstanteParametros::CHAVE_ALUNO]) === false)) {
            $alunoId = $parametros[ConstanteParametros::CHAVE_ALUNO];
            $sql = $sql. " and (a0_.id = {$alunoId}) ";
        }

        if (isset($parametros[ConstanteParametros::CHAVE_SITUACAO_TURMA]) === true && count($parametros[ConstanteParametros::CHAVE_SITUACAO_TURMA]) > 0) {
            $situacaoTurma = "'". implode("', '", $parametros[ConstanteParametros::CHAVE_SITUACAO_TURMA]) . "'";
            $sql = $sql. " and (t3_.situacao in  ({$situacaoTurma})) ";                
        }

        if ((isset($parametros[ConstanteParametros::CHAVE_TURMA]) === true)&&(is_null($parametros[ConstanteParametros::CHAVE_TURMA]) === false)) {
            $turmaId = $parametros[ConstanteParametros::CHAVE_TURMA];
            $sql = $sql. " and (t3_.id = {$turmaId}) ";
        }

        if ((isset($parametros[ConstanteParametros::CHAVE_DATA_PERIODO_DE]) === true)&&(is_null($parametros[ConstanteParametros::CHAVE_DATA_PERIODO_DE]) === false)) {
            $date=$parametros[ConstanteParametros::CHAVE_DATA_PERIODO_DE];
            $dataAlt      = strtotime($date);
            $periodo_de = strval(getdate($dataAlt)["year"]) . '-' . strval(getdate($dataAlt)["mon"]) . '-' . strval(getdate($dataAlt)["mday"]);
            $sql = $sql. " and (c6_.data_inicio_contrato >= '{$periodo_de} 00:00:01') ";
 //           $sql = $sql. " and (c6_.data_inicio_contrato >= {$periodo_de}) ";
        }

        if ((isset($parametros[ConstanteParametros::CHAVE_DATA_PERIODO_ATE]) === true)&&(is_null($parametros[ConstanteParametros::CHAVE_DATA_PERIODO_ATE]) === false)) {
            $date=$parametros[ConstanteParametros::CHAVE_DATA_PERIODO_ATE];
            $dataAlt      = strtotime($date);
            $periodo_ate = strval(getdate($dataAlt)["year"]) . '-' . strval(getdate($dataAlt)["mon"]) . '-' . strval(getdate($dataAlt)["mday"]);
            $sql = $sql. " and (c6_.data_termino_contrato <= '{$periodo_ate} 23:23:59') ";
        }

        if ((isset($parametros[ConstanteParametros::CHAVE_LIVRO]) === true)&&(is_null($parametros[ConstanteParametros::CHAVE_LIVRO]) === false)) {
            $livroId = $parametros[ConstanteParametros::CHAVE_LIVRO];
            $sql = $sql. " and (l0_.id = {$livroId}) ";
        }
        
        if ((isset($parametros[ConstanteParametros::CHAVE_VALOR_MID_TERM_MIN]) === true)&&(is_null($parametros[ConstanteParametros::CHAVE_VALOR_MID_TERM_MIN]) === false)) {
            $valorMidTermMin = $parametros[ConstanteParametros::CHAVE_VALOR_MID_TERM_MIN];
            $sql = $sql. " and (COALESCE(aa.nota_mid_term_escrita, 0) >= {$valorMidTermMin}) ";
        }

        if ((isset($parametros[ConstanteParametros::CHAVE_VALOR_MID_TERM_MAX]) === true)&&(is_null($parametros[ConstanteParametros::CHAVE_VALOR_MID_TERM_MAX]) === false)) {
            $valorMidTermMax = $parametros[ConstanteParametros::CHAVE_VALOR_MID_TERM_MAX];
            $sql = $sql. " and (COALESCE(aa.nota_mid_term_escrita, 0) <= {$valorMidTermMax}) ";
        }

        if ((isset($parametros[ConstanteParametros::CHAVE_VALOR_FINAL_TEST_MIN]) === true)&&(is_null($parametros[ConstanteParametros::CHAVE_VALOR_FINAL_TEST_MIN]) === false)) {
            $valorFinalTestMin = $parametros[ConstanteParametros::CHAVE_VALOR_FINAL_TEST_MIN];
            $sql = $sql. " and (COALESCE(aa.nota_final_escrita, 0) >= {$valorFinalTestMin}) ";
        }

        if ((isset($parametros[ConstanteParametros::CHAVE_VALOR_FINAL_TEST_MAX]) === true)&&(is_null($parametros[ConstanteParametros::CHAVE_VALOR_FINAL_TEST_MAX]) === false)) {
            $valorFinalTestMax = $parametros[ConstanteParametros::CHAVE_VALOR_FINAL_TEST_MAX];
            $sql = $sql. " and (COALESCE(aa.nota_final_escrita, 0) <= {$valorFinalTestMax}) ";
        }

        $sql = $sql. " GROUP BY a0_.id, p0_.nome_contato, l0_.descricao,
                        aa.nota_mid_term_escrita, aa.nota_final_escrita, f.apelido,
                        f.id, m5_.tipo, c6_.id, faltas ";

         return $this->managerRegister->getConnection()->fetchAllAssociative($sql);
 
        // TODO: Montar filtro nota WG minima
        // TODO: Montar filtro nota WG maxima
 //       return $queryBuilder->getQuery()->getResult(\Doctrine\ORM\Query::HYDRATE_ARRAY);
    }

    /**
     * Monta a query para ser executada no relatório
     *
     * @param int $AlunoID
     * @param int $turmaid
     *
     * @return array
     */
    public function buscarNotasAlunoAvaliacaoConceitualID($alunoId,$turmaid)
    {
        

        $franqueada = VariaveisCompartilhadas::$franqueadaID; 
 
        $sql ="SELECT nota_listening_1.descricao as nota_listening_1_descricao,
                        nota_listening_1.valor as nota_listening_1_Nota,
                        nota_speaking_1.descricao as nota_speaking_1_descricao,
                        nota_speaking_1.valor as nota_speaking_1_Nota,	   
                        nota_writing_1.descricao as nota_writing_1_descricao,
                        nota_writing_1.valor as nota_writing_1_Nota,
                        nota_listening_2.descricao as nota_listening_2_descricao,
                        nota_listening_2.valor as nota_listening_2_Nota,	   
                        nota_speaking_2.descricao as nota_speaking_2_descricao,
                        nota_speaking_2.valor as nota_speaking_2_Nota,
                        nota_writing_2.descricao as nota_writing_2_descricao,
                        nota_writing_2.valor as nota_writing_2_Nota	   
                        FROM aluno_avaliacao_conceitual aac 
                LEFT JOIN conceito_avaliacao nota_listening_1 ON aac.nota_listening_1_id = nota_listening_1.id
                LEFT JOIN conceito_avaliacao nota_speaking_1 ON	aac.nota_speaking_1_id = nota_speaking_1.id
                LEFT JOIN conceito_avaliacao nota_writing_1 ON	aac.nota_writing_1_id = nota_writing_1.id
                LEFT JOIN conceito_avaliacao nota_listening_2 ON aac.nota_listening_2_id = nota_listening_2.id
                LEFT JOIN conceito_avaliacao nota_speaking_2 ON	aac.nota_speaking_2_id = nota_speaking_2.id
                LEFT JOIN conceito_avaliacao nota_writing_2 ON aac.nota_writing_2_id = nota_writing_2.id	   
                WHERE aac.turma_id = {$turmaid} and aac.aluno_id = {$alunoId} and aac.franqueada_id = {$franqueada}";

            return $this->managerRegister->getConnection()->fetchAllAssociative($sql);
    }

       /**
     * Monta a query para ser executada no relatório
     *
     * @param int $AlunoID
     * @param int $turmaid
     *
     * @return array
     */
    public function buscarNotasAlunoAvaliacaoID($alunoId,$turmaid)
    {
        

        $franqueada = VariaveisCompartilhadas::$franqueadaID; 
 
        $sql ="SELECT nota_mid_term_oral.descricao as nota_mid_term_oral_descricao,
                        nota_final_oral.descricao as nota_final_oral_descricao,
                        nota_retake_mid_term_oral.descricao as nota_retake_mid_term_oral_descricao,
                        nota_retake_final_oral.descricao as nota_retake_final_oral_descricao,
                        av.nota_mid_term_test as mid_term_t,
                        av.nota_mid_term_composition as mid_term_c,
                        av.nota_mid_term_escrita as mid_term_wg,
                        av.nota_retake_mid_term_escrita as mid_term_retake_wg,
                        av.nota_final_test as final_test_t,
                        av.nota_final_composition as final_test_c,
                        av.nota_final_escrita as final_test_wg,
                        av.nota_retake_final_escrita as final_test_retake_wg                                                
                        FROM aluno_avaliacao av
                LEFT JOIN conceito_avaliacao nota_mid_term_oral ON av.nota_mid_term_oral_id = nota_mid_term_oral.id
                LEFT JOIN conceito_avaliacao nota_final_oral ON av.nota_final_oral_id = nota_final_oral.id
                LEFT JOIN conceito_avaliacao nota_retake_mid_term_oral ON av.nota_retake_mid_term_oral_id = nota_retake_mid_term_oral.id
                LEFT JOIN conceito_avaliacao nota_retake_final_oral ON av.nota_retake_final_oral_id = nota_retake_final_oral.id	   
                WHERE av.turma_id = {$turmaid} and av.aluno_id = {$alunoId} and av.franqueada_id = {$franqueada}";

            return $this->managerRegister->getConnection()->fetchAllAssociative($sql);
    }

       /**
     * Monta a query para ser executada no relatório
     *
     * @param string $mensagemErro
     * @param array $parametros
     *
     * @return array
     */
    public function buscarDadosRelatorioNotasAlunos(&$mensagemErro, $parametros)
    {

        $queryBuilder = $this->createQueryBuilder("aa");
        $queryBuilder->select(
            [
                'a.id as aluno_id',
                'p.nome_contato as nome_aluno',
                'l.descricao as livro_descricao',
                'aa.nota_mid_term_escrita as mid_term',
                'aa.nota_final_escrita as final_test',
                'f.apelido as instrutor_apelido',
                'f.id as instrutor_id',
                'm.tipo as modalidade_turma',
                'c.id as contrato_id',
            ]
        );

        $queryBuilder->join('aa.livro', 'l');
        $queryBuilder->join('aa.contrato', 'c');
        $queryBuilder->join('c.modalidade_turma', 'm');
        $queryBuilder->join('c.aluno', 'a');
        $queryBuilder->join('a.pessoa', 'p');
        $queryBuilder->leftJoin('aa.turma', 't', 'WITH', 't.excluido = 0');
        $queryBuilder->leftJoin('t.funcionario', 'f');

        $queryBuilder->andWhere('aa.franqueada = :franqueada');
        $queryBuilder->setParameter('franqueada', VariaveisCompartilhadas::$franqueadaID);

        if ((isset($parametros[ConstanteParametros::CHAVE_DATA_PERIODO_DE]) === true)&&(is_null($parametros[ConstanteParametros::CHAVE_DATA_PERIODO_DE]) === false)) {
            $queryBuilder->andWhere('c.data_inicio_contrato >= :periodo_de');
            $queryBuilder->setParameter('periodo_de', $parametros[ConstanteParametros::CHAVE_DATA_PERIODO_DE]);
        }

        if ((isset($parametros[ConstanteParametros::CHAVE_DATA_PERIODO_ATE]) === true)&&(is_null($parametros[ConstanteParametros::CHAVE_DATA_PERIODO_ATE]) === false)) {
            $queryBuilder->andWhere('c.data_termino_contrato <= :periodo_ate');
            $queryBuilder->setParameter('periodo_ate', $parametros[ConstanteParametros::CHAVE_DATA_PERIODO_ATE]);
        }

        if ((isset($parametros[ConstanteParametros::CHAVE_LIVRO]) === true)&&(is_null($parametros[ConstanteParametros::CHAVE_LIVRO]) === false)) {
            $queryBuilder->andWhere('aa.livro = :livro');
            $queryBuilder->setParameter('livro', $parametros[ConstanteParametros::CHAVE_LIVRO]);
        }

        if ((isset($parametros[ConstanteParametros::CHAVE_VALOR_MID_TERM_MIN]) === true)&&(is_null($parametros[ConstanteParametros::CHAVE_VALOR_MID_TERM_MIN]) === false)) {
            $queryBuilder->andWhere('COALESCE(aa.nota_mid_term_escrita, 0) >= :valor_mid_term_min');
            $queryBuilder->setParameter('valor_mid_term_min', $parametros[ConstanteParametros::CHAVE_VALOR_MID_TERM_MIN]);
        }

        if ((isset($parametros[ConstanteParametros::CHAVE_VALOR_MID_TERM_MAX]) === true)&&(is_null($parametros[ConstanteParametros::CHAVE_VALOR_MID_TERM_MAX]) === false)) {
            $queryBuilder->andWhere('COALESCE(aa.nota_mid_term_escrita, 0) <= :valor_mid_term_max');
            $queryBuilder->setParameter('valor_mid_term_max', $parametros[ConstanteParametros::CHAVE_VALOR_MID_TERM_MAX]);
        }

        if ((isset($parametros[ConstanteParametros::CHAVE_VALOR_FINAL_TEST_MIN]) === true)&&(is_null($parametros[ConstanteParametros::CHAVE_VALOR_FINAL_TEST_MIN]) === false)) {
            $queryBuilder->andWhere('COALESCE(aa.nota_final_escrita, 0) >= :valor_final_test_min');
            $queryBuilder->setParameter('valor_final_test_min', $parametros[ConstanteParametros::CHAVE_VALOR_FINAL_TEST_MIN]);
        }

        if ((isset($parametros[ConstanteParametros::CHAVE_VALOR_FINAL_TEST_MAX]) === true)&&(is_null($parametros[ConstanteParametros::CHAVE_VALOR_FINAL_TEST_MAX]) === false)) {
            $queryBuilder->andWhere('COALESCE(aa.nota_final_escrita, 0) <= :valor_final_test_max');
            $queryBuilder->setParameter('valor_final_test_max', $parametros[ConstanteParametros::CHAVE_VALOR_FINAL_TEST_MAX]);
        }


        // TODO: Montar filtro nota WG minima
        // TODO: Montar filtro nota WG maxima
        return $queryBuilder->getQuery()->getResult(\Doctrine\ORM\Query::HYDRATE_ARRAY);
    }

}
