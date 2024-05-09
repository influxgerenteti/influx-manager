<?php

namespace App\Repository\Principal;

use App\Entity\Principal\TipoAgendamento;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Symfony\Bridge\Doctrine\RegistryInterface;
use App\Helper\ConstanteParametros;

/**
 * @method TipoAgendamento|null find($id, $lockMode = null, $lockVersion = null)
 * @method TipoAgendamento|null findOneBy(array $criteria, array $orderBy = null)
 * @method TipoAgendamento[]    findAll()
 * @method TipoAgendamento[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class TipoAgendamentoRepository extends ServiceEntityRepository
{


    public function __construct(RegistryInterface $registry)
    {
        parent::__construct($registry, TipoAgendamento::class);
    }

    /**
     * Query para realizar a busca de tipos de agendamento
     *
     * @return \Doctrine\ORM\QueryBuilder
     */
    protected function montaQueryBase()
    {
        $queryBuilder = $this->createQueryBuilder("tpa");

        return $queryBuilder;
    }

    /**
     * Filtra os tipos de agendamento por pagina e numero de itens por pagina
     *
     * @param array $parametros
     * @param integer $pagina
     * @param integer $numeroItensPorPagina
     *
     * @return \App\Entity\Principal\TipoAgendamento[] Resultados em formato de array
     */
    public function filtrarTipoAgendamentoPorPagina($parametros, $pagina=1, $numeroItensPorPagina=50)
    {
        $opcoes       = [];
        $queryBuilder = $this->montaQueryBase();

        if ((isset($parametros[ConstanteParametros::CHAVE_ORDENACAO_COLUNA]) === true)&&(is_null($parametros[ConstanteParametros::CHAVE_ORDENACAO_COLUNA]) === false)) {
            $queryBuilder->orderBy($parametros[ConstanteParametros::CHAVE_ORDENACAO_COLUNA], $parametros[ConstanteParametros::CHAVE_ORDENACAO_SORT]);
            $opcoes[\Knp\Component\Pager\Paginator::SORT_FIELD_PARAMETER_NAME]     = "~";
            $opcoes[\Knp\Component\Pager\Paginator::SORT_DIRECTION_PARAMETER_NAME] = "~";
        }

        return \App\Helper\FunctionHelper::montaPaginatorPaginacao($queryBuilder, $pagina, $numeroItensPorPagina, $opcoes);
    }

    /**
     * Busca o registro através da chave primaria
     *
     * @param int $id
     *
     * @return array|NULL
     */
    public function buscarRegistroPorId($id)
    {
        $queryBuilder = $this->montaQueryBase();
        $queryBuilder->andWhere("tpa.id = :id");
        $queryBuilder->setParameter("id", $id);
        return \App\Helper\FunctionHelper::retornaArrayNull($queryBuilder, true);
    }


}
