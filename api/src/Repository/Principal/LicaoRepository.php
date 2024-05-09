<?php

namespace App\Repository\Principal;

use App\Entity\Principal\Licao;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Symfony\Bridge\Doctrine\RegistryInterface;
use App\Helper\ConstanteParametros;
use App\Helper\VariaveisCompartilhadas;
use Doctrine\Common\Persistence\ManagerRegistry;

/**
 * @method Licao|null find($id, $lockMode = null, $lockVersion = null)
 * @method Licao|null findOneBy(array $criteria, array $orderBy = null)
 * @method Licao[]    findAll()
 * @method Licao[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class LicaoRepository extends ServiceEntityRepository
{

    private ManagerRegistry $managerRegister;

    public function __construct(RegistryInterface $registry, ManagerRegistry $managerRegister)
    {
        parent::__construct($registry, Licao::class);
        $this->managerRegister = $managerRegister;
    }

    /**
     * Monta query Licao
     *
     * @return \Doctrine\ORM\QueryBuilder
     */
    protected function montaQueryBase()
    {
        $queryBuilder = $this->createQueryBuilder("licao");
        $queryBuilder->addSelect("pl");
        $queryBuilder->leftJoin("licao.planejamento_licao", "pl");
        $queryBuilder->leftJoin("licao.turmaAulas", "tmas");
        return $queryBuilder;
    }

    /**
     * Monta Filtros
     *
     * @param \Doctrine\ORM\QueryBuilder $queryBuilder
     * @param array $parametros
     */
    protected function montaFiltros(&$queryBuilder, $parametros)
    {
        if ((isset($parametros[ConstanteParametros::CHAVE_TURMA]) === true) &&(empty($parametros[ConstanteParametros::CHAVE_TURMA]) === false)) {
            $queryBuilder->andWhere("tmas.turma = :turmaId");
            $queryBuilder->andWhere("tmas.franqueada = :franqueadaId");
            $queryBuilder->setParameter("turmaId", $parametros[ConstanteParametros::CHAVE_TURMA]);
            $queryBuilder->setParameter("franqueadaId", VariaveisCompartilhadas::$franqueadaID);
        }
    }

    /**
     * Filtra o planekamento licao por pagina
     *
     * @param array $parametros
     * @param number $pagina
     * @param number $numeroItensPorPagina
     *
     * @return \Knp\Component\Pager\Pagination\SlidingPagination
     */
    public function filtrarLicaoPorPagina($parametros, $pagina=1, $numeroItensPorPagina=50)
    {
        $queryBuilder = $this->montaQueryBase();
        return \App\Helper\FunctionHelper::montaPaginatorPaginacao($queryBuilder, $pagina, $numeroItensPorPagina);
    }

    /**
     * Filtra as licoes por turma
     *
     * @param array $parametros
     *
     * @return array|NULL
     */
    public function buscarLicoesPorTurma($parametros)
    {
        $queryBuilder = $this->montaQueryBase();
        $this->montaFiltros($queryBuilder, $parametros);
        return \App\Helper\FunctionHelper::retornaArrayNull($queryBuilder);
    }

       /**
     * Filtra as licoes por turma
     *
     * @param int $turmaId
     *
     * @return array
     */
    public function buscarLicoesFaltasPorTurmaIdAlunoID($alunoId, $turmaId)
    {
       $sql ="SELECT  l.descricao, 
                    ad.presenca 
                FROM 
                aluno_diario ad
                INNER JOIN aluno a ON ad.aluno_id = a.id 
                INNER JOIN pessoa p ON a.pessoa_id = p.id
                INNER JOIN turma_aula ta ON ad.turma_aula_id = ta.id 
                INNER JOIN aluno_diario_licao adl ON ad.id = adl.aluno_diario_id 
                INNER JOIN licao l ON adl.licao_id = l.id
                WHERE ad.aluno_id = {$alunoId} and ta.turma_id = {$turmaId} and ta.finalizada = 1 and ad.presenca = 'F'";
        
        return $this->managerRegister->getConnection()->fetchAllAssociative($sql);
    }

    /**
     * Buscar Licoes por turma e turmaAula
     *
     * @param array $parametros
     *
     * @return array|NULL
     */
    public function buscarLicoesPorTurmaETurmaAula($parametros)
    {
        $queryBuilder = $this->createQueryBuilder("lic");
        $queryBuilder->addSelect("pl");
        $queryBuilder->leftJoin("lic.planejamento_licao", "pl");
        $queryBuilder->leftJoin("lic.alunoDiarios", "ad");
        $queryBuilder->leftJoin("ad.turma_aula", "tma");
        $queryBuilder->andWhere("tma.turma = :turmaId");
        $queryBuilder->andWhere("tma.id = :turmaAulaId");
        $queryBuilder->andWhere("tma.franqueada = :franqueadaId");
        $queryBuilder->setParameter("turmaAulaId", $parametros[ConstanteParametros::CHAVE_TURMA_AULA]);
        $queryBuilder->setParameter("turmaId", $parametros[ConstanteParametros::CHAVE_TURMA]);
        $queryBuilder->setParameter("franqueadaId", VariaveisCompartilhadas::$franqueadaID);
        return \App\Helper\FunctionHelper::retornaArrayNull($queryBuilder);
    }

    /**
     * Buscar homework por turma
     *
     * @param int $turmaId
     *
     * @return array|NULL
     */
    public function listarHomeworkPorTurma($turmaId)
    {
        $queryBuilder = $this->createQueryBuilder("lic");
        $queryBuilder->addSelect("ad");
        $queryBuilder->addSelect("tma");
        $queryBuilder->addSelect("al");
        $queryBuilder->addSelect("p");
        $queryBuilder->addSelect("ctts");
        $queryBuilder->leftJoin("lic.turmaAulas", "tma");
        $queryBuilder->leftJoin("tma.alunoDiarios", "ad");
        // , "WITH", "ad.turma_aula = tma.id");
        $queryBuilder->leftJoin("ad.aluno", "al");
        $queryBuilder->leftJoin("al.pessoa", "p");
        $queryBuilder->leftJoin("al.contratos", "ctts");
        $queryBuilder->andWhere("tma.finalizada = :finalizada");
        $queryBuilder->andWhere("tma.turma = :turmaId");
        $queryBuilder->andWhere("tma.franqueada = :franqueadaId");
        $queryBuilder->setParameter("finalizada", true);
        $queryBuilder->setParameter("turmaId", $turmaId);
        $queryBuilder->setParameter("franqueadaId", VariaveisCompartilhadas::$franqueadaID);

        return \App\Helper\FunctionHelper::retornaArrayNull($queryBuilder);
    }

    /**
     * Busca as licoes pelo livro
     *
     * @param int $livroId
     *
     * @return array|NULL
     */
    public function buscarLicoesPorLivro($livroId)
    {
        $queryBuilder = $this->createQueryBuilder("lic");
        $queryBuilder->leftJoin("lic.planejamento_licao", "plnj");
        $queryBuilder->leftJoin("plnj.livros", "liv");
        $queryBuilder->andWhere("liv.id = :livroId");
        $queryBuilder->setParameter("livroId", $livroId);

        return \App\Helper\FunctionHelper::retornaArrayNull($queryBuilder);
    }


}
