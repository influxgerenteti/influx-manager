<?php

namespace App\Repository\Principal;

use App\Entity\Principal\FormaPagamento;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Symfony\Bridge\Doctrine\RegistryInterface;
use App\Helper\ConstanteParametros;
use App\Helper\VariaveisCompartilhadas;

/**
 * @method FormaPagamento|null find($id, $lockMode = null, $lockVersion = null)
 * @method FormaPagamento|null findOneBy(array $criteria, array $orderBy = null)
 * @method FormaPagamento[]    findAll()
 * @method FormaPagamento[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class FormaPagamentoRepository extends ServiceEntityRepository
{


    public function __construct(RegistryInterface $registry)
    {
        parent::__construct($registry, FormaPagamento::class);
    }


 /**
     * Monta QueryBase para forma de pagamento partindo da Franqueada
     *
     * @return \Doctrine\ORM\QueryBuilder
     */
    protected function montaQueryBaseFranqueada()
    {

        $queryBuilder = $this->createQueryBuilder("fp");
       
        return $queryBuilder;
    }


    /**
     * Monta a query para Forma de pagamento
     *
     * @return \Doctrine\ORM\QueryBuilder
     */
    private function montaQueryBase()
    {
        $queryBuilder = $this->createQueryBuilder("fp");
        return $queryBuilder;
    }

    /**
     * Busca a forma de pagamento com filtros
     *
     * @param array $parametros parametros da requisicao
     * @param number $pagina
     * @param number $numeroItensPorPagina
     *
     * @return \Knp\Component\Pager\Pagination\SlidingPagination
     */
    public function filtraFormaPagamentoPorPagina($parametros, $pagina=1, $numeroItensPorPagina=50)
    {
        $opcoes       = [];
        $queryBuilder = $this->montaQueryBaseFranqueada();
        if (isset($parametros[ConstanteParametros::CHAVE_DESCRICAO]) === true) {
            $queryBuilder->where("UPPER(fp.descricao) LIKE :descricao");
            $queryBuilder->setParameter("descricao", "%" . strtoupper($parametros[ConstanteParametros::CHAVE_DESCRICAO]) . "%");
        }
            //FILTRA FRANQUEADA LOGADA
            $queryBuilder->innerJoin("fp.franqueada", "fr");
            $queryBuilder->andWhere("fr.id = :franqueadaId");
            $queryBuilder->setParameter("franqueadaId", VariaveisCompartilhadas::$franqueadaID);
      

        if ((isset($parametros[ConstanteParametros::CHAVE_ORDENACAO_COLUNA]) === true)&&(is_null($parametros[ConstanteParametros::CHAVE_ORDENACAO_COLUNA]) === false)) {
            $queryBuilder->orderBy($parametros[ConstanteParametros::CHAVE_ORDENACAO_COLUNA], $parametros[ConstanteParametros::CHAVE_ORDENACAO_SORT]);
            $opcoes[\Knp\Component\Pager\Paginator::SORT_FIELD_PARAMETER_NAME]     = "~";
            $opcoes[\Knp\Component\Pager\Paginator::SORT_DIRECTION_PARAMETER_NAME] = "~";
        }

        return \App\Helper\FunctionHelper::montaPaginatorPaginacao($queryBuilder, $pagina, $numeroItensPorPagina, $opcoes);
    }

    /**
     * Buscar por Id
     *
     * @param int $id
     * @param boolean $retornarObjeto se deverá ser retornado como instância de classe
     *
     * @return array|NULL
     */
    public function buscarPorId($id, $retornarObjeto=false)
    {
        $queryBuilder = $this->montaQueryBase();
        $queryBuilder->where("fp.id = :id");
        $queryBuilder->setParameter("id", $id);

        if ($retornarObjeto === true) {
            return $queryBuilder->getQuery()->getOneOrNullResult();
        }

        return \App\Helper\FunctionHelper::retornaArrayNull($queryBuilder, true);
    }

    /**
     * Busca o item por descricao
     *
     * @param string $descricao
     *
     * @return mixed|\App\Entity\Principal\FormaPagamento|NULL
     */
    public function buscaPorDescricao($descricao)
    {
        $queryBuilder = $this->montaQueryBase();
        $queryBuilder->where("UPPER(fp.descricao) = :descricao");
        $queryBuilder->setParameter("descricao", strtoupper($descricao));
        return $queryBuilder->getQuery()->getOneOrNullResult(\Doctrine\ORM\Query::HYDRATE_ARRAY);
    }

    /**
     * Busca todas as formas de pagamento que correspondam com o parametro de descrição
     *
     * @param string $descricao
     *
     * @return \App\Entity\Principal\FormaPagamento[]
     */
    public function buscarVariosPorDescricao($descricao)
    {
        $queryBuilder = $this->montaQueryBase();
        $queryBuilder->where("UPPER(fp.descricao) LIKE :descricao");
        $queryBuilder->setParameter("descricao", "%$descricao%");
        return $queryBuilder->getQuery()->getResult(\Doctrine\ORM\Query::HYDRATE_ARRAY);
    }


}
