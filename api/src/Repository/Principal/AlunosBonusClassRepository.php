<?php

namespace App\Repository\Principal;

use App\Entity\Principal\AlunosBonusClass;
use App\Helper\ConstanteParametros;
use App\Helper\VariaveisCompartilhadas;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Symfony\Bridge\Doctrine\RegistryInterface;

/**
 * @method AlunosBonusClass|null find($id, $lockMode = null, $lockVersion = null)
 * @method AlunosBonusClass|null findOneBy(array $criteria, array $orderBy = null)
 * @method AlunosBonusClass[]    findAll()
 * @method AlunosBonusClass[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class AlunosBonusClassRepository extends ServiceEntityRepository
{


    public function __construct(RegistryInterface $registry)
    {
        parent::__construct($registry, AlunosBonusClass::class);
    }


    /**
     * Monta query principal
     *
     * @return \Doctrine\ORM\QueryBuilder
     */
    protected function montaQueryBase ()
    {
        $queryBuilder = $this->createQueryBuilder("abn");
        $queryBuilder->addSelect('al');
        $queryBuilder->addSelect('pes');
        $queryBuilder->addSelect('bn');

        $queryBuilder->leftJoin('abn.aluno', 'al');
        $queryBuilder->leftJoin('al.pessoa', 'pes');
        $queryBuilder->leftJoin('abn.bonus_class', 'bn');

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
     * Montar filtros para listagem
     *
     * @param \Doctrine\ORM\QueryBuilder $queryBuilder
     * @param array $parametros
     */
    protected function montaFiltros (&$queryBuilder, &$parametros)
    {

    }


    /**
     * Filtrar o AlunosBonusClass por pagina
     *
     * @param array $parametros
     * @param number $pagina
     * @param number $numeroItensPorPagina
     *
     * @return \Knp\Component\Pager\Pagination\SlidingPagination
     */
    public function filtrarBonusClassPorPagina ($parametros, $pagina=1, $numeroItensPorPagina=50)
    {

        $opcoes       = [];
        $queryBuilder = $this->montaQueryBase();
        // $this->filtrarFranqueada($queryBuilder);
        $this->montaFiltros($queryBuilder, $parametros);
        if ((isset($parametros[ConstanteParametros::CHAVE_ORDENACAO_COLUNA]) === true)&&(is_null($parametros[ConstanteParametros::CHAVE_ORDENACAO_COLUNA]) === false)) {
            $queryBuilder->orderBy($parametros[ConstanteParametros::CHAVE_ORDENACAO_COLUNA], $parametros[ConstanteParametros::CHAVE_ORDENACAO_SORT]);
            $opcoes[\Knp\Component\Pager\Paginator::SORT_FIELD_PARAMETER_NAME]     = "~";
            $opcoes[\Knp\Component\Pager\Paginator::SORT_DIRECTION_PARAMETER_NAME] = "~";
        }

        return \App\Helper\FunctionHelper::montaPaginatorPaginacao($queryBuilder, $pagina, $numeroItensPorPagina, $opcoes);
    }

        /**
     * Filtrar o AlunosBonusClass Principal
     *
     * @param array $parametros
     *
     * @return \Knp\Component\Pager\Pagination\SlidingPagination
     */
    public function filtrarBonusClassAlunoPrincipal ($parametros)
    {
        $queryBuilder = $this->montaQueryBase();
        $queryBuilder->andwhere('abn.selecionado = 1 ');
        
        if ((isset($parametros[ConstanteParametros::CHAVE_ALUNO]) === true)&&(is_null($parametros[ConstanteParametros::CHAVE_ALUNO]) === false)) {
            $queryBuilder->andwhere('abn.aluno <> :aluno');
            $queryBuilder->setParameter('aluno', $parametros[ConstanteParametros::CHAVE_ALUNO]);
        }
 
        if ((isset($parametros[ConstanteParametros::CHAVE_BONUS_CLASS]) === true)&&(is_null($parametros[ConstanteParametros::CHAVE_BONUS_CLASS]) === false)) {
            $queryBuilder->andWhere('abn.bonus_class = :bonusClass');
            $queryBuilder->setParameter('bonusClass', $parametros[ConstanteParametros::CHAVE_BONUS_CLASS]);
        }
 
        if ((isset($parametros[ConstanteParametros::CHAVE_HORARIO_AULA]) === true)&&(is_null($parametros[ConstanteParametros::CHAVE_HORARIO_AULA]) === false)) {
            $queryBuilder->andWhere('abn.horario_aula = :horarioAula');
            $queryBuilder->setParameter('horarioAula', $parametros[ConstanteParametros::CHAVE_HORARIO_AULA]);
        }
 
        $dados =  $queryBuilder->getQuery()->getResult(\Doctrine\ORM\Query::HYDRATE_ARRAY);

        if ($dados) {
           return true;
        } else {
            return false;
        }
    }


}
