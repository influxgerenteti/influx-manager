<?php

namespace App\Repository\Principal;

use App\Entity\Principal\TipoOcorrencia;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Symfony\Bridge\Doctrine\RegistryInterface;
use App\Helper\ConstanteParametros;

/**
 * @method TipoOcorrencia|null find($id, $lockMode = null, $lockVersion = null)
 * @method TipoOcorrencia|null findOneBy(array $criteria, array $orderBy = null)
 * @method TipoOcorrencia[]    findAll()
 * @method TipoOcorrencia[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class TipoOcorrenciaRepository extends ServiceEntityRepository
{


    public function __construct(RegistryInterface $registry)
    {
        parent::__construct($registry, TipoOcorrencia::class);
    }

    /**
     * Monta Query Base
     *
     * @return \Doctrine\ORM\QueryBuilder
     */
    protected function montaQueryBase()
    {
        $queryBuilder = $this->createQueryBuilder("to")
            ->select(['to','to_pai'])
            ->leftJoin('to.tipo_pai', 'to_pai');

        return $queryBuilder;
    }

    /**
     * Monta os filtros passado pela requisicao
     *
     * @param \Doctrine\ORM\QueryBuilder $queryBuilder
     * @param array $parametros
     */
    protected function montaFiltros(&$queryBuilder, $parametros)
    {
        
    }

    /**
     * Lista todos os itens
     *
     * @param array $parametros array de paramentros
     * @param int $pagina numero da pagina de busca
     * @param int $numeroItensPorPagina numero de itens para buscar
     *
     * @return \App\Entity\Principal\TipoOcorrencia[]
     */
    public function listar($parametros, $pagina=1, $numeroItensPorPagina=50)
    {
        $opcoes       = [];
        $queryBuilder = $this->montaQueryBase();
        $this->montaFiltros($queryBuilder, $parametros);

        if ((isset($parametros[ConstanteParametros::CHAVE_ORDENACAO_COLUNA]) === true)&&(is_null($parametros[ConstanteParametros::CHAVE_ORDENACAO_COLUNA]) === false)) {
            $queryBuilder->orderBy($parametros[ConstanteParametros::CHAVE_ORDENACAO_COLUNA], $parametros[ConstanteParametros::CHAVE_ORDENACAO_SORT]);
            $opcoes[\Knp\Component\Pager\Paginator::SORT_FIELD_PARAMETER_NAME]     = "~";
            $opcoes[\Knp\Component\Pager\Paginator::SORT_DIRECTION_PARAMETER_NAME] = "~";
        }

        return \App\Helper\FunctionHelper::montaPaginatorPaginacao($queryBuilder, $pagina, $numeroItensPorPagina, $opcoes);
    }

    /**
     * Busca registro pela id
     *
     * @param int $id
     *
     * @return array|NULL
     */
    public function buscaRegistroPorId($id)
    {
        $queryBuilder = $this->montaQueryBase();
        $queryBuilder->where("to.id = :idRegistro");
        $queryBuilder->setParameter("idRegistro", $id);
        return \App\Helper\FunctionHelper::retornaArrayNull($queryBuilder, true);
    }


}
