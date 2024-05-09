<?php

namespace App\Repository\Principal;

use App\Entity\Principal\AlunoDiario;
use App\Helper\ConstanteParametros;
use App\Helper\VariaveisCompartilhadas;
use DateInterval;
use DatePeriod;
use DateTime;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use DoctrineExtensions\Query\Mysql\Month;
use Symfony\Bridge\Doctrine\RegistryInterface;

/**
 * @method AlunoDiario|null find($id, $lockMode = null, $lockVersion = null)
 * @method AlunoDiario|null findOneBy(array $criteria, array $orderBy = null)
 * @method AlunoDiario[]    findAll()
 * @method AlunoDiario[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class AlunoDiarioRepository extends ServiceEntityRepository
{


    public function __construct(RegistryInterface $registry)
    {
        parent::__construct($registry, AlunoDiario::class);
    }

    /**
     * Monta Query principal
     *
     * @return \Doctrine\ORM\QueryBuilder
     */
    protected function montaQueryBase()
    {
        $queryBuilder = $this->createQueryBuilder("ad");
        $queryBuilder->addSelect("fran");
        $queryBuilder->addSelect("al");
        $queryBuilder->addSelect("cur");
        $queryBuilder->addSelect("tma");
        $queryBuilder->addSelect("func");
        $queryBuilder->addSelect("liv");
        $queryBuilder->addSelect("sf");
        $queryBuilder->addSelect("lic");
        $queryBuilder->leftJoin("ad.franqueada", "fran");
        $queryBuilder->leftJoin("ad.aluno", "al");
        $queryBuilder->leftJoin("ad.curso", "cur");
        $queryBuilder->leftJoin("ad.turma_aula", "tma");
        $queryBuilder->leftJoin("ad.funcionario", "func");
        $queryBuilder->leftJoin("ad.livro", "liv");
        $queryBuilder->leftJoin("ad.sala_franqueada", "sf");
        $queryBuilder->leftJoin("ad.licao", "lic");

        return $queryBuilder;
    }

    /**
     * Busca o AlunoDiario pelos parametros informados
     *
     * @param int $franqueadaId
     * @param int $alunoId
     * @param int $cursoId
     * @param int $turmaAulaId
     * @param int $livroId
     * @param int $licaoId
     *
     * @return mixed|NULL|\App\Entity\Principal\AlunoDiario
     */
    public function buscaAlunoDiarioPorFranqueadaAlunoCursoTurmaAulaLivro($franqueadaId, $alunoId, $cursoId, $turmaAulaId, $livroId, $licaoId)
    {
        $queryBuilder = $this->montaQueryBase();
        $queryBuilder->andWhere("fran.id = :franqueadaId");
        $queryBuilder->andWhere("al.id = :alunoId");
        $queryBuilder->andWhere("cur.id = :cursoId");
        $queryBuilder->andWhere("tma.id = :turmaAulaId");
        $queryBuilder->andWhere("liv.id = :livroId");
        $queryBuilder->setParameter("franqueadaId", $franqueadaId);
        $queryBuilder->setParameter("alunoId", $alunoId);
        $queryBuilder->setParameter("cursoId", $cursoId);
        $queryBuilder->setParameter("turmaAulaId", $turmaAulaId);
        $queryBuilder->setParameter("livroId", $livroId);
        return $queryBuilder->getQuery()->getOneOrNullResult();
    }


    /**
     * Monta a query para ser executada no relatório
     * 
     * @param array $parametros
     *
     * @return array
     */
    public function buscarDadosRelatorioFrequencia($parametros)
    {
        $queryBuilder = $this->createQueryBuilder("ad")
            ->select([
                "p.nome_contato AS aluno_nome",
                "turma.descricao AS aluno_turma",
                "SUM(CASE
                    WHEN ad.presenca = 'P' THEN 1
                    ELSE 0
                END) / COUNT(CASE WHEN ad.presenca = 'F' OR ad.presenca = 'P' THEN 1 ELSE 0 END) * 100 AS frequencia", 
                "SUM(CASE
                    WHEN ad.presenca = 'F' AND MONTH(ad.data_criacao) = 1 THEN 1
                    ELSE 0
                END) AS faltas_janeiro",
                "SUM(CASE
                    WHEN ad.presenca = 'P' AND MONTH(ad.data_criacao) = 1 THEN 1
                    ELSE 0
                END) AS presencas_janeiro",
                "SUM(CASE
                    WHEN ad.presenca = 'F' AND MONTH(ad.data_criacao) = 2 THEN 1
                    ELSE 0
                END) AS faltas_fevereiro",
                "SUM(CASE
                    WHEN ad.presenca = 'P' AND MONTH(ad.data_criacao) = 2 THEN 1
                    ELSE 0
                END) AS presencas_fevereiro",
                "SUM(CASE
                    WHEN ad.presenca = 'F' AND MONTH(ad.data_criacao) = 3 THEN 1
                    ELSE 0
                END) AS faltas_marco",
                "SUM(CASE
                    WHEN ad.presenca = 'P' AND MONTH(ad.data_criacao) = 3 THEN 1
                    ELSE 0
                END) AS presencas_marco",
                "SUM(CASE
                    WHEN ad.presenca = 'F' AND MONTH(ad.data_criacao) = 4 THEN 1
                    ELSE 0
                END) AS faltas_abril",
                "SUM(CASE
                    WHEN ad.presenca = 'P' AND MONTH(ad.data_criacao) = 4 THEN 1
                    ELSE 0
                END) AS presencas_abril",
                "SUM(CASE
                    WHEN ad.presenca = 'F' AND MONTH(ad.data_criacao) = 5 THEN 1
                    ELSE 0
                END) AS faltas_maio",
                "SUM(CASE
                    WHEN ad.presenca = 'P' AND MONTH(ad.data_criacao) = 5 THEN 1
                    ELSE 0
                END) AS presencas_maio",
                "SUM(CASE
                    WHEN ad.presenca = 'F' AND MONTH(ad.data_criacao) = 6 THEN 1
                    ELSE 0
                END) AS faltas_junho",
                "SUM(CASE
                    WHEN ad.presenca = 'P' AND MONTH(ad.data_criacao) = 6 THEN 1
                    ELSE 0
                END) AS presencas_junho",
                "SUM(CASE
                    WHEN ad.presenca = 'F' AND MONTH(ad.data_criacao) = 7 THEN 1
                    ELSE 0
                END) AS faltas_julho",
                "SUM(CASE
                    WHEN ad.presenca = 'P' AND MONTH(ad.data_criacao) = 7 THEN 1
                    ELSE 0
                END) AS presencas_julho",
                "SUM(CASE
                    WHEN ad.presenca = 'F' AND MONTH(ad.data_criacao) = 8 THEN 1
                    ELSE 0
                END) AS faltas_agosto",
                "SUM(CASE
                    WHEN ad.presenca = 'P' AND MONTH(ad.data_criacao) = 8 THEN 1
                    ELSE 0
                END) AS presencas_agosto",
                "SUM(CASE
                    WHEN ad.presenca = 'F' AND MONTH(ad.data_criacao) = 9 THEN 1
                    ELSE 0
                END) AS faltas_setembro",
                "SUM(CASE
                    WHEN ad.presenca = 'P' AND MONTH(ad.data_criacao) = 9 THEN 1
                    ELSE 0
                END) AS presencas_setembro",
                "SUM(CASE
                    WHEN ad.presenca = 'F' AND MONTH(ad.data_criacao) = 10 THEN 1
                    ELSE 0
                END) AS faltas_outubro",
                "SUM(CASE
                    WHEN ad.presenca = 'P' AND MONTH(ad.data_criacao) = 10 THEN 1
                    ELSE 0
                END) AS presencas_outubro",
                "SUM(CASE
                    WHEN ad.presenca = 'F' AND MONTH(ad.data_criacao) = 11 THEN 1
                    ELSE 0
                END) AS faltas_novembro",
                "SUM(CASE
                    WHEN ad.presenca = 'P' AND MONTH(ad.data_criacao) = 11 THEN 1
                    ELSE 0
                END) AS presencas_novembro",
                "SUM(CASE
                    WHEN ad.presenca = 'F' AND MONTH(ad.data_criacao) = 12 THEN 1
                    ELSE 0
                END) AS faltas_dezembro",
                "SUM(CASE
                    WHEN ad.presenca = 'P' AND MONTH(ad.data_criacao) = 12 THEN 1
                    ELSE 0
                END) AS presencas_dezembro",
                "COUNT(CASE
                           WHEN ad.presenca = 'F' OR ad.presenca = 'P' THEN 1
                       ELSE 0
                END) as total_aulas",
                "SUM(CASE WHEN ad.presenca = 'P' THEN 1 ELSE 0 END) as total_presencas",
                "SUM(CASE WHEN ad.presenca = 'F' THEN 1 ELSE 0 END) as total_faltas"
            ])
            ->innerJoin('ad.turma_aula', 'tma')
            ->innerJoin('ad.aluno', 'al')
            ->innerJoin('al.pessoa', 'p')
            ->innerJoin('tma.turma', 'turma')
            ->groupBy('al')
            ->orderBy('p.nome_contato')
            ->where('tma.franqueada = :franqueada')
            ->andWhere('tma.finalizada = 1 ')
            ->setParameter('franqueada', VariaveisCompartilhadas::$franqueadaID);

            if(isset($parametros[ConstanteParametros::CHAVE_DATA_INICIAL])) {
                $dataInicial = strtotime(str_replace("/", "-",$parametros[ConstanteParametros::CHAVE_DATA_INICIAL] . " 00:00:00"));
                $dataInicial = date('Y-m-d H:i:s', $dataInicial);
                $queryBuilder->andWhere("tma.data_aula >= :data_inicial");
                $queryBuilder->setParameter('data_inicial', $dataInicial);
                $dataInicial = (str_replace("/", "-",$parametros[ConstanteParametros::CHAVE_DATA_INICIAL]));
            }
            
            if(isset($parametros[ConstanteParametros::CHAVE_DATA_FINAL])) {
                $dataFinal = strtotime(str_replace("/", "-",$parametros[ConstanteParametros::CHAVE_DATA_FINAL] . " 23:59:59"));
                $dataFinal = date('Y-m-d H:i:s', $dataFinal);
                $queryBuilder->andWhere("tma.data_aula <= :data_final");
                $queryBuilder->setParameter('data_final', $dataFinal);
                $dataFinal = (str_replace("/", "-",$parametros[ConstanteParametros::CHAVE_DATA_FINAL]));
            }

            if ((isset($parametros[ConstanteParametros::CHAVE_ALUNO]))&&(!is_null($parametros[ConstanteParametros::CHAVE_ALUNO]))) {
                $queryBuilder->andWhere('al.id = :alunoId');
                $queryBuilder->setParameter('alunoId', $parametros[ConstanteParametros::CHAVE_ALUNO]);
            }
            
            if((isset($parametros[ConstanteParametros::CHAVE_TURMA]))&&(!is_null($parametros[ConstanteParametros::CHAVE_TURMA]))){
                $queryBuilder->andWhere('turma.id = :turmaId');
                $queryBuilder->setParameter('turmaId', $parametros[ConstanteParametros::CHAVE_TURMA]);
            }
            
            $data = $queryBuilder->getQuery()->getResult();

            return $this->prepareResultFrequencia($data, $dataInicial, $dataFinal);

    }

    /**
     * Método que estrutura o objeto de retorno do relatório de frequência
     * O objeto é estrutura de acordo com o período passado
     * 
     * @param array $data
     * @param string $dataInicial
     * @param string $dataFinal
     * @return array
     */
    public function prepareResultFrequencia($data, $dataInicial, $dataFinal)
    {   
        $mesesPosInResult = [[3,4],[5,6],[7,8],[9,10],[11,12],[13,14],[15,16],[17,18],[19,20],[21,22],[23,24],[25,26]];
        
        $mesInicial = (int) substr($dataInicial, 3, 2);
        $mesFinal = (int) substr($dataFinal, 3, 2);
    
        $posQueFicam = array_slice($mesesPosInResult, $mesInicial - 1, (($mesFinal - $mesInicial) + 1));   

        $tmpPosQueFicam = [];
        
        foreach($posQueFicam as $valor){
            foreach($valor as $pos){
                $tmpPosQueFicam[] = $pos;
            }
        }
        $i = 0;
        $pos = 0;
        $result = [];
        foreach($data as $aluno){
            $result[$pos] = 
                [
                    "aluno_nome" => $aluno['aluno_nome'],
                    "aluno_turma" => $aluno['aluno_turma'],
                    "frequencia" => $aluno['frequencia'],
                    "total_aulas" => $aluno['total_aulas'],
                    "total_presencas" => $aluno['total_presencas'],
                    "total_faltas" => $aluno['total_faltas']
                ];

            foreach($aluno as $indice => $value){
                if(in_array($i, $tmpPosQueFicam)){ 
                    $result[$pos][$indice] = $value;
                }
                $i = $i + 1;
            }
            $i = 0;
            $pos = $pos + 1;
        }

        return $result;
    }

     /**
     * Monta a query para ser executada no relatório
     * 
     * @param string $mensagemErro
     * @param array $parametros
     *
     * @return array
     */
    public function buscarDadosRelatorioFrequenciaAluno($id)
    {
        $queryBuilder = $this->createQueryBuilder("ad");
        $selects      = [
            'p.nome_contato as Aluno',
            "SUM(CASE when ad.presenca = 'P' then 1 else 0 end) / COUNT(ad.id) * 100 as frequencia",
        ];

        $queryBuilder->select($selects);
        $queryBuilder->join('ad.turma_aula', 'ta');
        $queryBuilder->join('ad.aluno', 'a');
        $queryBuilder->join('a.pessoa', 'p');
        
        $queryBuilder->where('ta.franqueada = :franqueada');
        $queryBuilder->setParameter('franqueada', VariaveisCompartilhadas::$franqueadaID);
        
        $queryBuilder->andWhere("a.id = :id");
        $queryBuilder->setParameter(':id', $id);
        
        $dados = $queryBuilder->getQuery()->getResult(\Doctrine\ORM\Query::HYDRATE_ARRAY);
        
        return $dados;
    }

         /**
     * Monta a query para ser executada no relatório
     *
     * @param string $mensagemErro
     * @param array $parametros
     *
     * @return array
     */
    public function buscarDadosRelatorioFrequenciaAlunoNovo(&$mensagemErro, $id, $turmaid)
    {

        $queryBuilder = $this->createQueryBuilder("ad");
        $selects      = [
            'p.nome_contato as Aluno',
            "SUM(CASE when ad.presenca = 'P' then 1 else 0 end) / COUNT(ad.id) * 100 as frequencia",
        ];

        $queryBuilder->select($selects);
        $queryBuilder->join('ad.turma_aula', 'ta');
        $queryBuilder->join('ad.aluno', 'a');
        $queryBuilder->join('a.pessoa', 'p');
        $queryBuilder->groupBy('a.id');
        $queryBuilder->orderBy('p.nome_contato', 'ASC');

        $queryBuilder->where('ta.franqueada = :franqueada');
        $queryBuilder->setParameter('franqueada', VariaveisCompartilhadas::$franqueadaID);

        $queryBuilder->andWhere("a.id = '$id'");
        $queryBuilder->andWhere("ta.turma = '$turmaid'");
       
        $dados = $queryBuilder->getQuery()->getResult(\Doctrine\ORM\Query::HYDRATE_ARRAY);

        // $queryBuilder = $this->createQueryBuilder("ad");
        // $queryBuilder->select('count(distinct ta.id) qtde_aulas');
        // $queryBuilder->join('ad.turma_aula', 'ta');
        // $queryBuilder->join('ad.aluno', 'a');

        // $queryBuilder->where('ta.franqueada = :franqueada');
        // $queryBuilder->setParameter('franqueada', VariaveisCompartilhadas::$franqueadaID);

        // $queryBuilder->andWhere("a.id = '$id'");
 
        // $totalAulas = $queryBuilder->getQuery()->getOneOrNullResult();

        // if (is_null($totalAulas) === true) {
        //     $totalAulas = 0;
        // } else {
        //     $totalAulas = $totalAulas["qtde_aulas"];
        // }

        return $dados;
    }
}
