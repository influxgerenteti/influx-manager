<?php

namespace App\Repository\Importacao;

use App\Entity\Importacao\BibliotecaClassificacao;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Symfony\Bridge\Doctrine\RegistryInterface;

/**
 *
 * @method BibliotecaClassificacao|null find($id, $lockMode = null, $lockVersion = null)
 * @method BibliotecaClassificacao|null findOneBy(array $criteria, array $orderBy = null)
 * @method BibliotecaClassificacao[]    findAll()
 * @method BibliotecaClassificacao[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class BibliotecaClassificacaoRepository extends ServiceEntityRepository
{


    public function __construct(RegistryInterface $registry)
    {
        parent::__construct($registry, BibliotecaClassificacao::class);
    }


}
