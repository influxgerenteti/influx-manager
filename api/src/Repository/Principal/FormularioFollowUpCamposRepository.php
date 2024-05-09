<?php

namespace App\Repository\Principal;

use App\Entity\Principal\FormularioFollowUpCampos;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Symfony\Bridge\Doctrine\RegistryInterface;

/**
 * @method FormularioFollowUpCampos|null find($id, $lockMode = null, $lockVersion = null)
 * @method FormularioFollowUpCampos|null findOneBy(array $criteria, array $orderBy = null)
 * @method FormularioFollowUpCampos[]    findAll()
 * @method FormularioFollowUpCampos[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class FormularioFollowUpCamposRepository extends ServiceEntityRepository
{


    public function __construct(RegistryInterface $registry)
    {
        parent::__construct($registry, FormularioFollowUpCampos::class);
    }

    /**
     * Monta query padrão
     *
     * @return \Doctrine\ORM\QueryBuilder
     */
    protected function montaQueryBase()
    {
        $queryBuilder = $this->createQueryBuilder("ffupc");
        $queryBuilder->addSelect("ffup");
        $queryBuilder->leftJoin("ffupc.formulario_follow_up", "ffup");

        return $queryBuilder;
    }

    /**
     * Busca todos os campos através do formularioId
     *
     * @param int $formularioId
     *
     * @return array|NULL
     */
    public function filtraPorFormulario($formularioId)
    {
        $queryBuilder = $this->montaQueryBase();
        $queryBuilder->where("ffup.id = :formularioId");
        $queryBuilder->setParameter("formularioId", $formularioId);
        return \App\Helper\FunctionHelper::retornaArrayNull($queryBuilder);
    }


}
