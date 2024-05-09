<?php

namespace App\Repository\Principal;

use App\Entity\Principal\Categoria;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Symfony\Bridge\Doctrine\RegistryInterface;

/**
 *
 * @method Categoria|null find($id, $lockMode = null, $lockVersion = null)
 * @method Categoria|null findOneBy(array $criteria, array $orderBy = null)
 * @method Categoria[]    findAll()
 * @method Categoria[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class CategoriaRepository extends ServiceEntityRepository
{


    public function __construct(RegistryInterface $registry)
    {
        parent::__construct($registry, Categoria::class);
    }

    /**
     * Filtra a categoria por pagina e numero de itens por pagina
     *
     * @param integer $pagina
     *
     * @return \App\Entity\Principal\Categoria[] Resultados em formato de array
     */
    public function filtrarCategoriasPorPagina($pagina=1)
    {
        $numeroItensPorPagina = 50;
        $queryBuilder         = $this->buscarTodosSemExclusao();
        return \App\Helper\FunctionHelper::montaPaginatorPaginacao($queryBuilder, $pagina, $numeroItensPorPagina);
    }

    /**
     * Busca todas as categorias que não estiverem excluídas
     *
     * @return \Doctrine\ORM\QueryBuilder
     */
    public function buscarTodosSemExclusao ()
    {
        $qb = $this->createQueryBuilder('c')
            ->select('c.id as id, c.nome as nome')
            ->where("c.excluido != 1")
            ->orderBy('c.nome', 'asc');
        return $qb;
    }


}
