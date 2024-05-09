<?php

namespace App\Repository\Principal;

use App\Entity\Principal\CondicaoPagamento;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Symfony\Bridge\Doctrine\RegistryInterface;

/**
 *
 * @method CondicaoPagamento|null find($id, $lockMode = null, $lockVersion = null)
 * @method CondicaoPagamento|null findOneBy(array $criteria, array $orderBy = null)
 * @method CondicaoPagamento[]    findAll()
 * @method CondicaoPagamento[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class CondicaoPagamentoRepository extends ServiceEntityRepository
{


    public function __construct(RegistryInterface $registry)
    {
        parent::__construct($registry, CondicaoPagamento::class);
    }

    /**
     * Busca a condicao de pagamento com parcelas cadastradas
     *
     * @return \Doctrine\ORM\QueryBuilder
     */
    private function buscarCondicaoPagamentoEParcelas()
    {
        $queryBuilder = $this->createQueryBuilder("cp");
        $queryBuilder->addSelect("cpp");
        $queryBuilder->leftJoin("cp.condicaoPagamentoParcelas", "cpp");
        return $queryBuilder;
    }

    /**
     * Filtra as Condicoes de Pagamentos por pagina e numero de itens por pagina
     *
     * @param integer $pagina
     * @param integer $numeroItensPorPagina
     *
     * @return \App\Entity\Principal\CondicaoPagamento[] Resultados em formato de array
     */
    public function filtrarCondicaoPagamentoPorPagina($pagina=1, $numeroItensPorPagina=50)
    {
        $queryBuilder = $this->buscarCondicaoPagamentoEParcelas();
        return \App\Helper\FunctionHelper::montaPaginatorPaginacao($queryBuilder, $pagina, $numeroItensPorPagina);
    }


}
