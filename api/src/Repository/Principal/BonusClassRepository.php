<?php

namespace App\Repository\Principal;

use App\Entity\Principal\BonusClass;
use App\Helper\ConstanteParametros;
use App\Helper\VariaveisCompartilhadas;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Symfony\Bridge\Doctrine\RegistryInterface;

/**
 * @method BonusClass|null find($id, $lockMode = null, $lockVersion = null)
 * @method BonusClass|null findOneBy(array $criteria, array $orderBy = null)
 * @method BonusClass[]    findAll()
 * @method BonusClass[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class BonusClassRepository extends ServiceEntityRepository
{


    public function __construct(RegistryInterface $registry)
    {
        parent::__construct($registry, BonusClass::class);
    }

    /**
     * Monta query principal
     *
     * @return \Doctrine\ORM\QueryBuilder
     */
    protected function montaQueryBase ()
    {
        $queryBuilder = $this->createQueryBuilder("bn");
        $queryBuilder->addSelect("fran");
        $queryBuilder->addSelect("func");
        $queryBuilder->addSelect("slf");
        $queryBuilder->addSelect("sl");
        $queryBuilder->addSelect("abn");
        $queryBuilder->addSelect("al");
        $queryBuilder->addSelect("ps");

        $queryBuilder->leftJoin("bn.franqueada", "fran");
        $queryBuilder->leftJoin('bn.funcionario', 'func');
        $queryBuilder->leftJoin('bn.sala_franqueada', 'slf');
        $queryBuilder->leftJoin('slf.sala', 'sl');
        $queryBuilder->leftJoin('bn.alunosBonusClasses', 'abn');
        $queryBuilder->leftJoin('abn.aluno', 'al');
        $queryBuilder->leftJoin('al.pessoa', 'ps');

        return $queryBuilder;
    }

    /**
     * Query para realizar fitlro de franqueada
     *
     * @param \Doctrine\ORM\QueryBuilder $queryBuilder
     */
    private function filtrarFranqueada(&$queryBuilder)
    {
        $queryBuilder->where('fran.id = :franqueada');
        $queryBuilder->setParameter('franqueada', VariaveisCompartilhadas::$franqueadaID);
    }

     /**
      * Filtrar o BonusClass por pagina
      *
      * @param array $parametros
      * @param number $pagina
      * @param number $numeroItensPorPagina
      *
      * @return \Knp\Component\Pager\Pagination\SlidingPagination
      */
    public function filtrarBonusClassPorPagina($parametros, $numeroItensPorPagina=50)
    {
        
        $pagina = 1;
        if (isset($parametros['pagina'])) {
            $pagina = $parametros['pagina'];
        }
        
        
        $opcoes       = [];
        $queryBuilder = $this->montaQueryBase();
        $this->filtrarFranqueada($queryBuilder);
        $this->montaFiltros($queryBuilder, $parametros);
        if ((isset($parametros[ConstanteParametros::CHAVE_ORDENACAO_COLUNA]) === true)&&(is_null($parametros[ConstanteParametros::CHAVE_ORDENACAO_COLUNA]) === false)) {
            $queryBuilder->orderBy($parametros[ConstanteParametros::CHAVE_ORDENACAO_COLUNA], $parametros[ConstanteParametros::CHAVE_ORDENACAO_SORT]);
            $opcoes[\Knp\Component\Pager\Paginator::SORT_FIELD_PARAMETER_NAME]     = "~";
            $opcoes[\Knp\Component\Pager\Paginator::SORT_DIRECTION_PARAMETER_NAME] = "~";
        }
       

        return \App\Helper\FunctionHelper::montaPaginatorPaginacao($queryBuilder, $pagina, $numeroItensPorPagina, $opcoes);
    }

    /**
     * Montar filtros para listagem
     *
     * @param \Doctrine\ORM\QueryBuilder $queryBuilder
     * @param array $parametros
     */
    protected function montaFiltros (&$queryBuilder, &$parametros)
    {

        if ((isset($parametros[ConstanteParametros::CHAVE_DATA_AGENDAMENTO_DE]) === true)&&(empty($parametros[ConstanteParametros::CHAVE_DATA_AGENDAMENTO_DE]) === false)) {
            $queryBuilder->andWhere("bn.data_aula >= :dataDe");
            $queryBuilder->setParameter("dataDe", $parametros[ConstanteParametros::CHAVE_DATA_AGENDAMENTO_DE]);
        }

        if ((isset($parametros[ConstanteParametros::CHAVE_DATA_AGENDAMENTO_ATE]) === true)&&(empty($parametros[ConstanteParametros::CHAVE_DATA_AGENDAMENTO_ATE]) === false)) {
            $queryBuilder->andWhere("bn.data_aula <= :dataAte");
            $queryBuilder->setParameter("dataAte", $parametros[ConstanteParametros::CHAVE_DATA_AGENDAMENTO_ATE]);
        }

        if ((isset($parametros[ConstanteParametros::CHAVE_FILTRO_FUNCIONARIO]) === true)&&(empty($parametros[ConstanteParametros::CHAVE_FILTRO_FUNCIONARIO]) === false)) {
            $queryBuilder->andWhere("func.id = :funcId");
            $queryBuilder->setParameter("funcId", $parametros[ConstanteParametros::CHAVE_FILTRO_FUNCIONARIO]);
        }

        if ((isset($parametros[ConstanteParametros::CHAVE_SITUACAO]) === true)&&(empty($parametros[ConstanteParametros::CHAVE_SITUACAO]) === false)) {
            $queryBuilder->andWhere("bn.situacao IN (:situacao)");
            $queryBuilder->setParameter("situacao", $parametros[ConstanteParametros::CHAVE_SITUACAO]);
        }

    }

     /**
      * Busca bonus class pela ID
      *
      * @param integer $id
      *
      * @return NULL|\App\Entity\Principal\BonusClass
      */
    public function buscarPorId ($id)
    {
        $queryBuilder = $this->montaQueryBase();
        // $this->filtrarFranqueada($queryBuilder);
        $queryBuilder->andWhere('bn.id = :id');
        $queryBuilder->setParameter('id', $id);

        return \App\Helper\FunctionHelper::retornaArrayNull($queryBuilder, true);
    }


}
