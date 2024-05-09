<?php

namespace App\Repository\Principal;

use App\Entity\Principal\MensagensAjuda;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Symfony\Bridge\Doctrine\RegistryInterface;

/**
 * @method MensagensAjuda|null find($id, $lockMode = null, $lockVersion = null)
 * @method MensagensAjuda|null findOneBy(array $criteria, array $orderBy = null)
 * @method MensagensAjuda[]    findAll()
 * @method MensagensAjuda[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class MensagensAjudaRepository extends ServiceEntityRepository
{


    public function __construct(RegistryInterface $registry)
    {
        parent::__construct($registry, MensagensAjuda::class);
    }

    /**
     * Busca todas as mensagens de ajuda (help-hint)
     *
     * @param string $url_modulo
     *
     * @return array
     */
    public function buscarTodos ($url_modulo)
    {
        $queryBuilder = $this->createQueryBuilder('m');
        $queryBuilder->select('m');
        $queryBuilder->join('m.modulo', 'modulo');
        $queryBuilder->where('modulo.url = :url');
        $queryBuilder->setParameter('url', $url_modulo);
        return \App\Helper\FunctionHelper::retornaArrayNull($queryBuilder);
    }


}
