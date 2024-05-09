<?php

namespace App\Repository\Principal;

use App\Entity\Principal\FollowupConvenio;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Symfony\Bridge\Doctrine\RegistryInterface;

/**
 * @method FollowupConvenio|null find($id, $lockMode = null, $lockVersion = null)
 * @method FollowupConvenio|null findOneBy(array $criteria, array $orderBy = null)
 * @method FollowupConvenio[]    findAll()
 * @method FollowupConvenio[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class FollowupConvenioRepository extends ServiceEntityRepository
{


    public function __construct(RegistryInterface $registry)
    {
        parent::__construct($registry, FollowupConvenio::class);
    }


}
