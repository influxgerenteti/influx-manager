<?php

namespace App\Repository\Principal;

use App\Entity\Principal\UsuarioAcesso;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Symfony\Bridge\Doctrine\RegistryInterface;

/**
 *
 * @method UsuarioAcesso|null find($id, $lockMode = null, $lockVersion = null)
 * @method UsuarioAcesso|null findOneBy(array $criteria, array $orderBy = null)
 * @method UsuarioAcesso[]    findAll()
 * @method UsuarioAcesso[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class UsuarioAcessoRepository extends ServiceEntityRepository
{


    public function __construct(RegistryInterface $registry)
    {
        parent::__construct($registry, UsuarioAcesso::class);
    }


}
