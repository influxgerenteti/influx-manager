<?php
namespace App\Repository\Log;

use App\Entity\Log\Log;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Symfony\Bridge\Doctrine\RegistryInterface;
use Doctrine\ORM\Tools\Pagination\Paginator;
use App\Helper\ConstanteParametros;

/**
 *
 * @method Log|null find($id, $lockMode = null, $lockVersion = null)
 * @method Log|null findOneBy(array $criteria, array $orderBy = null)
 * @method Log[] findAll()
 * @method Log[] findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class LogRepository extends ServiceEntityRepository
{


    public function __construct(RegistryInterface $registry)
    {
        parent::__construct($registry, Log::class);
    }

    /**
     * Filtra o usuario por pagina e numero de itens por pagina
     *
     * @param array $parametros
     * @param integer $franqueada
     * @param integer $pagina
     *
     * @return \Doctrine\ORM\Tools\Pagination\Paginator
     */
    public function filtraLogsPorPagina($parametros, $franqueada, $pagina=1, $numeroItensPorPagina=50)
    {
        $opcoes       = [];
        $queryBuilder = $this->createQueryBuilder("logs")
            ->select("logs")
            ->where('logs.franqueada = :franqueada')
            ->setParameter('franqueada', $franqueada);

        if ((isset($parametros[ConstanteParametros::CHAVE_ORDENACAO_COLUNA]) === true)&&(is_null($parametros[ConstanteParametros::CHAVE_ORDENACAO_COLUNA]) === false)) {
            $queryBuilder->orderBy($parametros[ConstanteParametros::CHAVE_ORDENACAO_COLUNA], $parametros[ConstanteParametros::CHAVE_ORDENACAO_SORT]);
            $opcoes[\Knp\Component\Pager\Paginator::SORT_FIELD_PARAMETER_NAME]     = "~";
            $opcoes[\Knp\Component\Pager\Paginator::SORT_DIRECTION_PARAMETER_NAME] = "~";
        }

        return \App\Helper\FunctionHelper::montaPaginatorPaginacao($queryBuilder, $pagina, $numeroItensPorPagina, $opcoes);
    }


}
