<?php

namespace App\Repository\Principal;

use App\Entity\Principal\EmailConfiguracao;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Symfony\Bridge\Doctrine\RegistryInterface;

/**
 * @method EmailConfiguracao|null find($id, $lockMode = null, $lockVersion = null)
 * @method EmailConfiguracao|null findOneBy(array $criteria, array $orderBy = null)
 * @method EmailConfiguracao[]    findAll()
 * @method EmailConfiguracao[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class EmailConfiguracaoRepository extends ServiceEntityRepository
{


    public function __construct(RegistryInterface $registry)
    {
        parent::__construct($registry, EmailConfiguracao::class);
    }


}
