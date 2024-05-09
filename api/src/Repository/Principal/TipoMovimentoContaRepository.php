<?php

namespace App\Repository\Principal;

use App\Entity\Principal\TipoMovimentoConta;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Symfony\Bridge\Doctrine\RegistryInterface;
use App\Helper\ConstanteParametros;

/**
 *
 * @method TipoMovimentoConta|null find($id, $lockMode = null, $lockVersion = null)
 * @method TipoMovimentoConta|null findOneBy(array $criteria, array $orderBy = null)
 * @method TipoMovimentoConta[]    findAll()
 * @method TipoMovimentoConta[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class TipoMovimentoContaRepository extends ServiceEntityRepository
{


    public function __construct(RegistryInterface $registry)
    {
        parent::__construct($registry, TipoMovimentoConta::class);
    }

    /**
     * Filtra os alunos por pagina e numero de itens por pagina
     *
     * @param array $parametros
     * @param integer $pagina
     * @param integer $numeroItensPorPagina
     *
     * @return \App\Entity\Principal\TipoMovimentoConta[] Resultados em formato de array
     */
    public function filtrarTipoMovimentoContaPorPagina($parametros, $pagina=1, $numeroItensPorPagina=50)
    {
        $numeroItensPorPagina = 50;
        $opcoes       = [];
        $queryBuilder = $this->createQueryBuilder("tpmc");
        if ((isset($parametros[ConstanteParametros::CHAVE_ORDENACAO_COLUNA]) === true)&&(is_null($parametros[ConstanteParametros::CHAVE_ORDENACAO_COLUNA]) === false)) {
            $queryBuilder->orderBy($parametros[ConstanteParametros::CHAVE_ORDENACAO_COLUNA], $parametros[ConstanteParametros::CHAVE_ORDENACAO_SORT]);
            $opcoes[\Knp\Component\Pager\Paginator::SORT_FIELD_PARAMETER_NAME]     = "~";
            $opcoes[\Knp\Component\Pager\Paginator::SORT_DIRECTION_PARAMETER_NAME] = "~";
        }

        return \App\Helper\FunctionHelper::montaPaginatorPaginacao($queryBuilder, $pagina, $numeroItensPorPagina, $opcoes);
    }


}
