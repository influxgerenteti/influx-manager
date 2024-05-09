<?php

namespace App\Repository\Importacao;

use App\Entity\Importacao\AlunoMedia;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Symfony\Bridge\Doctrine\RegistryInterface;

/**
 *
 * @method AlunoMedia|null find($id, $lockMode = null, $lockVersion = null)
 * @method AlunoMedia|null findOneBy(array $criteria, array $orderBy = null)
 * @method AlunoMedia[]    findAll()
 * @method AlunoMedia[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class AlunoMediaRepository extends ServiceEntityRepository
{


    public function __construct(RegistryInterface $registry)
    {
        parent::__construct($registry, AlunoMedia::class);
    }


}
