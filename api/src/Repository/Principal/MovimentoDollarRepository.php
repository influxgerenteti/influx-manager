<?php

namespace App\Repository\Principal;

use App\Entity\Principal\MovimentoDollar;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Symfony\Bridge\Doctrine\RegistryInterface;
use App\Helper\ConstanteParametros;
use App\Helper\VariaveisCompartilhadas;

/**
 * @method MovimentoDollar|null find($id, $lockMode = null, $lockVersion = null)
 * @method MovimentoDollar|null findOneBy(array $criteria, array $orderBy = null)
 * @method MovimentoDollar[]    findAll()
 * @method MovimentoDollar[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class MovimentoDollarRepository extends ServiceEntityRepository
{


    public function __construct(RegistryInterface $registry)
    {
        parent::__construct($registry, MovimentoDollar::class);
    }

    /**
     * Constrói a query
     *
     * @return \Doctrine\ORM\QueryBuilder
     */
    public function montaQuery ()
    {
        $queryBuilder = $this->createQueryBuilder("md");
        $queryBuilder->addSelect('f');
        $queryBuilder->addSelect('a');
        $queryBuilder->addSelect('c');
        $queryBuilder->addSelect('ad');
        $queryBuilder->join('md.franqueada', 'f');
        $queryBuilder->join('md.aluno', 'a');
        $queryBuilder->join('md.contrato', 'c');
        $queryBuilder->leftJoin('md.atividade_dollar', 'ad');

        return $queryBuilder;
    }

    /**
     * Query para realizar fitlro de franqueada
     *
     * @param \Doctrine\ORM\QueryBuilder $queryBuilder
     */
    private function filtrarFranqueada(&$queryBuilder)
    {
        $queryBuilder->where('md.franqueada = :franqueada');
        $queryBuilder->setParameter('franqueada', VariaveisCompartilhadas::$franqueadaID);
    }

    /**
     * Soma do saldo total, respeitando os filtros
     *
     * @param \Doctrine\ORM\QueryBuilder $queryBuilder
     *
     * @return string
     */
    private function somarSaldo($queryBuilder)
    {
        $somarSaldo = clone $queryBuilder;
        $somarSaldo = $somarSaldo->select('SUM(md.valor)');
        $somarSaldo = $somarSaldo->getQuery();
        $somarSaldo = $somarSaldo->getOneOrNullResult();

        return array_values($somarSaldo)[0];
    }

    /**
     * Filtra as movimentações dollar por pagina e numero de itens por pagina
     *
     * @param array $parametros Parâmetros usados para filtros
     *
     * @return array Resultados em formato de array
     */
    public function filtrarMovimentoDollarPorPagina($parametros)
    {
        $aluno  = $parametros[ConstanteParametros::CHAVE_ALUNO];
        $pagina = $parametros[ConstanteParametros::CHAVE_PAGINA];
        $numeroItensPorPagina = 50;

        $queryBuilder = $this->montaQuery();
        $this->filtrarFranqueada($queryBuilder);

        $queryBuilder->andWhere('md.aluno = :aluno');
        $queryBuilder->setParameter('aluno', $aluno);

        // Total
        $saldo = $this->somarSaldo($queryBuilder);

        return [
            ConstanteParametros::CHAVE_ITENS => \App\Helper\FunctionHelper::montaPaginatorPaginacao($queryBuilder, $pagina, $numeroItensPorPagina),
            ConstanteParametros::CHAVE_SALDO => $saldo,
        ];
    }

    /**
     * Calcula o saldo total do aluno
     *
     * @param array $parametros Parâmetros usados para filtros
     *
     * @return float Saldo total do aluno
     */
    public function filtrarMovimentoDollarPorAluno($parametros)
    {
        $aluno = $parametros[ConstanteParametros::CHAVE_ALUNO];

        $queryBuilder = $this->montaQuery();
        $this->filtrarFranqueada($queryBuilder);

        $queryBuilder->andWhere('md.aluno = :aluno');
        $queryBuilder->setParameter('aluno', $aluno);

        // Total
        $saldo = $this->somarSaldo($queryBuilder);

        return (float) $saldo;
    }


}
