<?php

namespace App\Repository\Principal;

use App\Entity\Principal\Servico;
use App\Helper\ConstanteParametros;
use App\Helper\VariaveisCompartilhadas;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Symfony\Bridge\Doctrine\RegistryInterface;


/**
 * @method Servico|null find($id, $lockMode = null, $lockVersion = null)
 * @method Servico|null findOneBy(array $criteria, array $orderBy = null)
 * @method Servico[]    findAll()
 * @method Servico[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class ServicoRepository extends ServiceEntityRepository
{


    public function __construct(RegistryInterface $registry)
    {
        parent::__construct($registry, Servico::class);
    }

     /**
      * Busca os registros da tabela utilizando left join
      *
      * @return \Doctrine\ORM\QueryBuilder
      */
    protected function montaQueryBase()
    {
        $queryBuilder = $this->createQueryBuilder("s");
        $queryBuilder->addSelect("a");
        $queryBuilder->addSelect("p");
        $queryBuilder->addSelect("i");
        $queryBuilder->addSelect("fun");
        $queryBuilder->addSelect("fran");
        $queryBuilder->addSelect("sh");
        $queryBuilder->addSelect("func");
        $queryBuilder->addSelect("fc");
        $queryBuilder->leftJoin("s.aluno", "a");
        $queryBuilder->leftJoin("a.pessoa", "p");
        $queryBuilder->leftJoin("s.item", "i");
        $queryBuilder->leftJoin("s.funcionario", "fun");
        $queryBuilder->leftJoin("s.franqueada", "fran");
        $queryBuilder->leftJoin("s.servicoHistoricos", "sh");
        $queryBuilder->leftJoin("sh.funcionario", "func");
        $queryBuilder->leftJoin("s.forma_cobranca", "fc");

        return $queryBuilder;
    }

    /**
     * Query para realizar fitlro de franqueada
     *
     * @param \Doctrine\ORM\QueryBuilder $queryBuilder
     */
    private function filtrarFranqueada(&$queryBuilder)
    {
        $queryBuilder->where("fran.id =:franqueada");
        $queryBuilder->setParameter('franqueada', VariaveisCompartilhadas::$franqueadaID);
    }

    /**
     * Monta a query com os filtros passados
     *
     * @param \Doctrine\ORM\QueryBuilder $queryBuilder
     * @param array $parametros
     */
    protected function montaFiltros(&$queryBuilder, $parametros)
    {

        if ((isset($parametros[ConstanteParametros::CHAVE_PROTOCOLO]) === true)&&(empty($parametros[ConstanteParametros::CHAVE_PROTOCOLO]) === false)) {
            $queryBuilder->andWhere("s.protocolo =:protocolo");
            $queryBuilder->setParameter("protocolo", $parametros[ConstanteParametros::CHAVE_PROTOCOLO]);
        }

        if ((isset($parametros[ConstanteParametros::CHAVE_ALUNO]) === true)&&(empty($parametros[ConstanteParametros::CHAVE_ALUNO]) === false)) {
            $queryBuilder->andWhere("a.id =:aluno");
            $queryBuilder->setParameter("aluno", $parametros[ConstanteParametros::CHAVE_ALUNO]);
        }

        if ((isset($parametros[ConstanteParametros::CHAVE_SITUACAO]) === true) && (empty($parametros[ConstanteParametros::CHAVE_SITUACAO]) === false)) {
            $queryBuilder->andWhere("s.situacao IN (:situacao)");
            $queryBuilder->setParameter("situacao", $parametros[ConstanteParametros::CHAVE_SITUACAO]);
        }

        if ((isset($parametros[ConstanteParametros::CHAVE_DATA_SOLICITACAO_DE]) === true)&&(empty($parametros[ConstanteParametros::CHAVE_DATA_SOLICITACAO_DE]) === false)) {
            $queryBuilder->andWhere("s.data_solicitacao >= :dataSolicitacaoDe");
            $queryBuilder->setParameter("dataSolicitacaoDe", $parametros[ConstanteParametros::CHAVE_DATA_SOLICITACAO_DE]);
        }

        if ((isset($parametros[ConstanteParametros::CHAVE_DATA_SOLICITACAO_ATE]) === true)&&(empty($parametros[ConstanteParametros::CHAVE_DATA_SOLICITACAO_ATE]) === false)) {
            $queryBuilder->andWhere("s.data_solicitacao <= :dataSolicitacaoAte");
            $queryBuilder->setParameter("dataSolicitacaoAte", $parametros[ConstanteParametros::CHAVE_DATA_SOLICITACAO_ATE]);
        }
    }

    /**
     * @param integer $id
     *
     * @return NULL|\App\Entity\Principal\Servico
     */
    public function buscarServicoId ($id)
    {
        $queryBuilder = $this->montaQueryBase();
        $this->filtrarFranqueada($queryBuilder);
        $queryBuilder->andWhere('s.id = :id');
        $queryBuilder->setParameter('id', $id);

        return \App\Helper\FunctionHelper::retornaArrayNull($queryBuilder, true);
    }

    /**
     * Busca todos os servicos cadastrados no sistema
     *
     * @param array $parametros Parametros da requisicao
     *
     * @return NULL|\App\Entity\Principal\Servico[]
     */
    public function listar($parametros)
    {
        $numeroItensPorPagina = 50;
        $opcoes       = [];
        $queryBuilder = $this->montaQueryBase();
        $this->filtrarFranqueada($queryBuilder);
        $this->montaFiltros($queryBuilder, $parametros);

        if ((isset($parametros[ConstanteParametros::CHAVE_ORDENACAO_COLUNA]) === true)&&(is_null($parametros[ConstanteParametros::CHAVE_ORDENACAO_COLUNA]) === false)) {
            $queryBuilder->orderBy($parametros[ConstanteParametros::CHAVE_ORDENACAO_COLUNA], $parametros[ConstanteParametros::CHAVE_ORDENACAO_SORT]);
            $opcoes[\Knp\Component\Pager\Paginator::SORT_FIELD_PARAMETER_NAME]     = "~";
            $opcoes[\Knp\Component\Pager\Paginator::SORT_DIRECTION_PARAMETER_NAME] = "~";
        }

        return \App\Helper\FunctionHelper::montaPaginatorPaginacao($queryBuilder, $parametros[ConstanteParametros::CHAVE_PAGINA], $numeroItensPorPagina, $opcoes);
    }


    /**
     * Buscar servico por numero de protocolo
     *
     * @param string $query numero de protocolo
     *
     * @return array|NULL
     */
    public function buscarServicoPorNumeroDeProtocolo($query)
    {
        $queryBuilder = $this->montaQueryBase();
        $this->filtrarFranqueada($queryBuilder);
        $queryBuilder->andWhere("s.protocolo LIKE :protocolo");
        $queryBuilder->setParameter("protocolo", "%$query%");

        return $queryBuilder->getQuery()->getResult(\Doctrine\ORM\Query::HYDRATE_ARRAY);
    }


}
