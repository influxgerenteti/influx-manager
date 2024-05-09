<?php

namespace App\Repository\Principal;

use App\Entity\Principal\OcorrenciaAcademicaDetalhes;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Symfony\Bridge\Doctrine\RegistryInterface;

/**
 * @method OcorrenciaAcademicaDetalhes|null find($id, $lockMode = null, $lockVersion = null)
 * @method OcorrenciaAcademicaDetalhes|null findOneBy(array $criteria, array $orderBy = null)
 * @method OcorrenciaAcademicaDetalhes[]    findAll()
 * @method OcorrenciaAcademicaDetalhes[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class OcorrenciaAcademicaDetalhesRepository extends ServiceEntityRepository
{


    public function __construct(RegistryInterface $registry)
    {
        parent::__construct($registry, OcorrenciaAcademicaDetalhes::class);
    }


}
