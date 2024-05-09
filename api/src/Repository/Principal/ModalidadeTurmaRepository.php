<?php

namespace App\Repository\Principal;

use App\Entity\Principal\ModalidadeTurma;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Symfony\Bridge\Doctrine\RegistryInterface;

/**
 * @method ModalidadeTurma|null find($id, $lockMode = null, $lockVersion = null)
 * @method ModalidadeTurma|null findOneBy(array $criteria, array $orderBy = null)
 * @method ModalidadeTurma[]    findAll()
 * @method ModalidadeTurma[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class ModalidadeTurmaRepository extends ServiceEntityRepository
{


    public function __construct(RegistryInterface $registry)
    {
        parent::__construct($registry, ModalidadeTurma::class);
    }

    public function filtraModalidadeTurmaPorPagina($parametros, $pagina=1, $numeroItensPorPagina=50)
    {
        $queryBuilder = $this->createQueryBuilder("mt");
        return \App\Helper\FunctionHelper::montaPaginatorPaginacao($queryBuilder, $pagina, $numeroItensPorPagina);
    }


}
