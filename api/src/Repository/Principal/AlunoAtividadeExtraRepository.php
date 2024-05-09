<?php

namespace App\Repository\Principal;

use App\Entity\Principal\AlunoAtividadeExtra;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Symfony\Bridge\Doctrine\RegistryInterface;
use App\Helper\ConstanteParametros;

/**
 * @method AlunoAtividadeExtra|null find($id, $lockMode = null, $lockVersion = null)
 * @method AlunoAtividadeExtra|null findOneBy(array $criteria, array $orderBy = null)
 * @method AlunoAtividadeExtra[]    findAll()
 * @method AlunoAtividadeExtra[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class AlunoAtividadeExtraRepository extends ServiceEntityRepository
{


    public function __construct(RegistryInterface $registry)
    {
        parent::__construct($registry, AlunoAtividadeExtra::class);
    }
}
