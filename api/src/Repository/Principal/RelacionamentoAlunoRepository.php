<?php

namespace App\Repository\Principal;

use App\Entity\Principal\RelacionamentoAluno;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Symfony\Bridge\Doctrine\RegistryInterface;

/**
 * @method RelacionamentoAluno|null find($id, $lockMode = null, $lockVersion = null)
 * @method RelacionamentoAluno|null findOneBy(array $criteria, array $orderBy = null)
 * @method RelacionamentoAluno[]    findAll()
 * @method RelacionamentoAluno[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class RelacionamentoAlunoRepository extends ServiceEntityRepository
{


    public function __construct(RegistryInterface $registry)
    {
        parent::__construct($registry, RelacionamentoAluno::class);
    }

    /**
     * Monta a query base para Relacionamento Aluno
     *
     * @return \Doctrine\ORM\QueryBuilder
     */
    protected function montaQueryBase()
    {
        $queryBuilder = $this->createQueryBuilder("relAluno");
        return $queryBuilder;
    }

    /**
     * Filtra o Relacionamento Aluno por pagina
     *
     * @param array $parametros
     * @param number $pagina
     * @param number $numeroItensPorPagina
     *
     * @return \Knp\Component\Pager\Pagination\SlidingPagination
     */
    public function filtrarRelacionamentoAlunoPorPagina($parametros, $pagina=1, $numeroItensPorPagina=50)
    {
        $queryBuilder = $this->montaQueryBase();
        $queryBuilder->where("relAluno.situacao = :situacao");
        $queryBuilder->setParameter("situacao", "A");
        return \App\Helper\FunctionHelper::montaPaginatorPaginacao($queryBuilder, $pagina, $numeroItensPorPagina);
    }


}
