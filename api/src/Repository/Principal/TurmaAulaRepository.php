<?php

namespace App\Repository\Principal;

use App\Entity\Principal\TurmaAula;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Symfony\Bridge\Doctrine\RegistryInterface;
use App\Helper\ConstanteParametros;
use App\Helper\VariaveisCompartilhadas;

/**
 * @method TurmaAula|null find($id, $lockMode = null, $lockVersion = null)
 * @method TurmaAula|null findOneBy(array $criteria, array $orderBy = null)
 * @method TurmaAula[]    findAll()
 * @method TurmaAula[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class TurmaAulaRepository extends ServiceEntityRepository
{


    public function __construct(RegistryInterface $registry)
    {
        parent::__construct($registry, TurmaAula::class);
    }

    /**
     * Monta Query principal
     *
     * @return \Doctrine\ORM\QueryBuilder
     */
    protected function montaQueryBase()
    {
        $queryBuilder = $this->createQueryBuilder("tma");
        $queryBuilder->addSelect("alunoDiarios");
        $queryBuilder->addSelect("alunosAvaliacao");
        $queryBuilder->addSelect("alunosAvaliacaoConceituais");
        $queryBuilder->addSelect("anl1");
        $queryBuilder->addSelect("ans1");
        $queryBuilder->addSelect("anw1");
        $queryBuilder->addSelect("anl2");
        $queryBuilder->addSelect("ans2");
        $queryBuilder->addSelect("anw2");
        $queryBuilder->addSelect("t");
        $queryBuilder->addSelect("funcTma");
        $queryBuilder->addSelect("ctts");
        $queryBuilder->addSelect("al");
        $queryBuilder->addSelect("pessAluno");
        $queryBuilder->addSelect("lic");
        $queryBuilder->addSelect("fran");
        $queryBuilder->addSelect("livro");
        $queryBuilder->addSelect("func");
        $queryBuilder->addSelect("sf");
        $queryBuilder->addSelect("sla");
        $queryBuilder->addSelect("cur");
        $queryBuilder->leftJoin("tma.turma", "t");
        $queryBuilder->leftJoin("tma.funcionario", "funcTma");
        $queryBuilder->leftJoin("t.curso", "cur");
        $queryBuilder->leftJoin("t.livro", "livro");
        $queryBuilder->leftJoin("t.funcionario", "func");
        $queryBuilder->leftJoin("t.sala_franqueada", "sf");
        $queryBuilder->leftJoin("sf.sala", "sla");
        $queryBuilder->leftJoin("t.contratos", "ctts", "WITH", "ctts.situacao = 'V'");
        $queryBuilder->leftJoin("ctts.aluno", "al");
        $queryBuilder->leftJoin("al.pessoa", "pessAluno");
        $queryBuilder->leftJoin("tma.licao", "lic");
        $queryBuilder->leftJoin("al.alunoDiarios", "alunoDiarios", "WITH", "alunoDiarios.livro = t.livro AND alunoDiarios.turma_aula = tma.id AND tma.licao MEMBER OF alunoDiarios.licao");
        $queryBuilder->leftJoin("al.alunoAvaliacaos", "alunosAvaliacao", "WITH", "alunosAvaliacao.livro = t.livro");
        $queryBuilder->leftJoin("al.alunoAvaliacaoConceituals", "alunosAvaliacaoConceituais", "WITH", "alunosAvaliacaoConceituais.livro = t.livro");
        $queryBuilder->leftJoin("alunosAvaliacaoConceituais.nota_listening_1", "anl1");
        $queryBuilder->leftJoin("alunosAvaliacaoConceituais.nota_speaking_1", "ans1");
        $queryBuilder->leftJoin("alunosAvaliacaoConceituais.nota_writing_1", "anw1");
        $queryBuilder->leftJoin("alunosAvaliacaoConceituais.nota_listening_2", "anl2");
        $queryBuilder->leftJoin("alunosAvaliacaoConceituais.nota_speaking_2", "ans2");
        $queryBuilder->leftJoin("alunosAvaliacaoConceituais.nota_writing_2", "anw2");
        $queryBuilder->leftJoin("tma.franqueada", "fran");
        return $queryBuilder;
    }

    /**
     * Monta os filtros para poder retornar os dados
     *
     * @param \Doctrine\ORM\QueryBuilder $queryBuilder
     * @param array $parametros
     */
    protected function montaFiltros(&$queryBuilder, $parametros)
    {
        $queryBuilder->andWhere("t.id = :turmaId");
        $queryBuilder->setParameter("turmaId", $parametros[ConstanteParametros::CHAVE_TURMA]);
        $queryBuilder->andWhere("fran.id = :franqueadaId");
        $queryBuilder->setParameter("franqueadaId", VariaveisCompartilhadas::$franqueadaID);

        if ((isset($parametros[ConstanteParametros::CHAVE_TURMA_AULA]) === true)&&(empty($parametros[ConstanteParametros::CHAVE_TURMA_AULA]) === false)) {
            $queryBuilder->andWhere("tma.id = :turmaAulaId");
            $queryBuilder->andWhere("tma.finalizada = :turmaFinalizada");
            $queryBuilder->setParameter("turmaFinalizada", true);
            $queryBuilder->setParameter("turmaAulaId", $parametros[ConstanteParametros::CHAVE_TURMA_AULA]);
        } else {
            $queryBuilder->andWhere("tma.finalizada = :turmaFinalizada");
            $queryBuilder->setParameter("turmaFinalizada", false);
            $queryBuilder->addOrderBy("tma.data_aula", "ASC");
        }
    }

    /**
     * Busca as turmas aulas com base nos parametros passados
     *
     * @param array $parametros
     *
     * @return array|NULL
     */
    public function listarDados($parametros)
    {
        $queryBuilder = $this->montaQueryBase();
        $queryBuilder->andWhere("alunosAvaliacao.personal = :alunoPersonal");
        $queryBuilder->andWhere("alunosAvaliacaoConceituais.personal = :alunoPersonal");
        $queryBuilder->setParameter("alunoPersonal", false);
        $this->montaFiltros($queryBuilder, $parametros);
        return \App\Helper\FunctionHelper::retornaArrayNull($queryBuilder, true);
    }

    /**
     * Lista historico das aulas
     *
     * @param int $turmaId
     *
     * @return array|NULL
     */
    public function listarHistorico($turmaId)
    {
        $queryBuilder = $this->createQueryBuilder("tma");
        $queryBuilder->select("tma.id as id, liv.descricao as livro_descricao, lic.id as licaoId, lic.descricao as licao_descricao, func.apelido as funcionario_apelido, sla.descricao as sala_descricao, tma.data_aula as turma_aula_data_aula");
        $queryBuilder->leftJoin("tma.licao", "lic");
        $queryBuilder->leftJoin("tma.turma", "t");
        $queryBuilder->leftJoin("t.livro", "liv");
        $queryBuilder->leftJoin("tma.funcionario", "func");
        $queryBuilder->leftJoin("t.sala_franqueada", "sfa");
        $queryBuilder->leftJoin("sfa.sala", "sla");
        $queryBuilder->leftJoin("tma.franqueada", "fran");
        $queryBuilder->andWhere("t.id = :turmaId");
        $queryBuilder->andWhere("fran.id = :franqueadaId");
        $queryBuilder->andWhere("tma.finalizada = :aulaFinalizada");
        $queryBuilder->setParameter("aulaFinalizada", true);
        $queryBuilder->setParameter("turmaId", $turmaId);
        $queryBuilder->setParameter("franqueadaId", VariaveisCompartilhadas::$franqueadaID);
        return \App\Helper\FunctionHelper::retornaArrayNull($queryBuilder);
// ** select para testes **

//         SELECT 
// 	tma.id, 
// 	liv.descricao as livro_descricao, 
// 	lic.id as licaoId, 
// 	lic.descricao as licao_descricao,
//     func.apelido as funcionario_apelido,
//     (SELECT sla.descricao FROM influx_crm_prod.sala as sla INNER JOIN influx_crm_prod.sala_franqueada as sfa ON sla.id = sfa.sala_id  WHERE sfa.franqueada_id = t.franqueada_id and sla.id = t.sala_franqueada_id ) as sala_descricao,
//      tma.data_aula as turma_aula_data_aula
    
    
// FROM influx_crm_prod.turma_aula as tma
// 	LEFT JOIN influx_crm_prod.turma as t on tma.turma_id = t.id
// 	LEFT JOIN influx_crm_prod.licao as lic on lic.id = tma.licao_id
// 	LEFT JOIN influx_crm_prod.livro as liv on liv.id = t.livro_id
// 	LEFT JOIN influx_crm_prod.funcionario as func on func.id = t.funcionario_id
	
//  WHERE
//  tma.finalizada = true
//  and 
//  tma.turma_id = 1396;
    }

    /**
     * Retorna os alunos diarios que já foram finalizados
     *
     * @param int $turmaId
     *
     * @return array|NULL
     */
    public function listarHomeworkPorTurma($turmaId)
    {
        $queryBuilder = $this->createQueryBuilder("tma");
        $queryBuilder->addSelect("lic");
        $queryBuilder->addSelect("alunoDiario");
        $queryBuilder->addSelect("a");
        $queryBuilder->addSelect("p");
        $queryBuilder->leftJoin("tma.licao", "lic");
        $queryBuilder->leftJoin("tma.turma", "t");
        $queryBuilder->leftJoin("tma.alunoDiarios", "alunoDiario");
        $queryBuilder->leftJoin("alunoDiario.aluno", "a");
        $queryBuilder->leftJoin("a.pessoa", "p");
        $queryBuilder->leftJoin("tma.franqueada", "fran");
        $queryBuilder->andWhere("t.id = :turmaId");
        $queryBuilder->andWhere("fran.id = :franqueadaId");
        $queryBuilder->andWhere("tma.finalizada = :aulaFinalizada");
        $queryBuilder->setParameter("aulaFinalizada", true);
        $queryBuilder->setParameter("turmaId", $turmaId);
        $queryBuilder->setParameter("franqueadaId", VariaveisCompartilhadas::$franqueadaID);
        return \App\Helper\FunctionHelper::retornaArrayNull($queryBuilder);
    }

    /**
     * Busca a turmaAula através dos parametros informados
     *
     * @param int $franqueadaId
     * @param int $turmaId
     * @param int $licaoId
     *
     * @return mixed|NULL|\App\Entity\Principal\TurmaAula
     */
    public function buscarTurmaAulaPorFranqueadaTurmaLicao($franqueadaId, $turmaId, $licaoId)
    {
        $queryBuilder = $this->montaQueryBase();
        $queryBuilder->andWhere("fran.id = :franqueadaId");
        $queryBuilder->andWhere("t.id = :turmaId");
        $queryBuilder->andWhere("lic.id = :licaoId");
        $queryBuilder->setParameter("franqueadaId", $franqueadaId);
        $queryBuilder->setParameter("turmaId", $turmaId);
        $queryBuilder->setParameter("licaoId", $licaoId);
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
        
        $queryBuilder = $this->createQueryBuilder("ta");
        $queryBuilder->select(
            't.id',
            'ta.finalizada',
            't.situacao',
            't.descricao as turma',
            "date_format(ta.data_aula, '%Y-%m-%d %H:%i') as data_aula",
            "lc.descricao as licao",
            "f.apelido as professor",
            "l.descricao as livro",
        );
        $queryBuilder->join('ta.turma', 't');
        $queryBuilder->join('t.livro', 'l');
        $queryBuilder->join('t.curso', 'c');
        $queryBuilder->join('ta.licao', 'lc');
        $queryBuilder->leftJoin('ta.alunoDiarios', 'ad');
        $queryBuilder->leftJoin('ta.funcionario', 'f');

        $queryBuilder->where('ta.franqueada = :franqueada');
        $queryBuilder->setParameter('franqueada', VariaveisCompartilhadas::$franqueadaID);
        $queryBuilder->distinct();

        if ((isset($parametros[ConstanteParametros::CHAVE_DATA_INICIAL]))&&(!is_null($parametros[ConstanteParametros::CHAVE_DATA_INICIAL]))) {
            $dataInicial = date('Y-m-d', strtotime(isset($parametros[ConstanteParametros::CHAVE_DATA_INICIAL]) ? str_replace('/', '-', $parametros[ConstanteParametros::CHAVE_DATA_INICIAL]) : "-1 months"));
            $queryBuilder->andWhere('ta.data_aula >= :data_inicial');
            $queryBuilder->setParameter('data_inicial', $dataInicial);
        }

        if ((isset($parametros[ConstanteParametros::CHAVE_DATA_FINAL]))&&(!is_null($parametros[ConstanteParametros::CHAVE_DATA_FINAL]))) {
            $dataFinal = date('Y-m-d', strtotime(isset($parametros[ConstanteParametros::CHAVE_DATA_FINAL]) ? str_replace('/', '-', $parametros[ConstanteParametros::CHAVE_DATA_FINAL]) : "today"));

            $queryBuilder->andWhere('ta.data_aula <= :data_final');
            $queryBuilder->setParameter('data_final', $dataFinal);
        }

        if ((isset($parametros[ConstanteParametros::CHAVE_CURSO]))&&(!is_null($parametros[ConstanteParametros::CHAVE_CURSO]))) {
            $queryBuilder->andWhere('c.id = :curso');
            $queryBuilder->setParameter('curso', $parametros[ConstanteParametros::CHAVE_CURSO]);
        }

        if ((isset($parametros[ConstanteParametros::CHAVE_LIVRO]))&&(!is_null($parametros[ConstanteParametros::CHAVE_LIVRO]))) {
            $queryBuilder->andWhere('l.id = :livro');
            $queryBuilder->setParameter('livro', $parametros[ConstanteParametros::CHAVE_LIVRO]);
        }
    
        if ((isset($parametros[ConstanteParametros::CHAVE_IDIOMA]))&&(!is_null($parametros[ConstanteParametros::CHAVE_IDIOMA]))) {
            $queryBuilder->andWhere('c.idioma = :idioma');
            $queryBuilder->setParameter('idioma', $parametros[ConstanteParametros::CHAVE_IDIOMA]);
        }

        if ((isset($parametros[ConstanteParametros::CHAVE_TURMA]))&&(!is_null($parametros[ConstanteParametros::CHAVE_TURMA]))) {
            $queryBuilder->andWhere('t = :turma');
            $queryBuilder->setParameter('turma', $parametros[ConstanteParametros::CHAVE_TURMA]);
        }

        if (isset($parametros[ConstanteParametros::CHAVE_INSTRUTOR_FLAG])) {
            $queryBuilder->andWhere('f.id = :funcionario');
            $queryBuilder->setParameter('funcionario', $parametros[ConstanteParametros::CHAVE_INSTRUTOR_FLAG]);
        }

        if ((isset($parametros[ConstanteParametros::CHAVE_FINALIZADA]) === true)&&(!is_null($parametros[ConstanteParametros::CHAVE_FINALIZADA]))) {
            $queryBuilder->andWhere('ta.finalizada = :finalizada');
            $queryBuilder->setParameter('finalizada', $parametros[ConstanteParametros::CHAVE_FINALIZADA]);
        }
        
        // if ((isset($parametros[ConstanteParametros::CHAVE_SITUACAO]) === true)&&(!is_null($parametros[ConstanteParametros::CHAVE_SITUACAO]))) {
        //     $queryBuilder->andWhere('t.situacao = :situacao');
        //     $queryBuilder->setParameter('situacao', $parametros[ConstanteParametros::CHAVE_SITUACAO]);
        // }

        if(isset($parametros[ConstanteParametros::CHAVE_MODALIDADE_TURMA])) {
            $queryBuilder->andWhere('t.modalidade_turma = :filtro_modalidade_turma')
                ->setParameter('filtro_modalidade_turma', $parametros[ConstanteParametros::CHAVE_MODALIDADE_TURMA]);
        }    
        
        return $queryBuilder->getQuery()->getResult();
    }


}
