<?php

namespace App\Repository\Principal;

use App\Entity\Principal\Boleto;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Symfony\Bridge\Doctrine\RegistryInterface;
use App\Helper\ConstanteParametros;
use App\Helper\VariaveisCompartilhadas;

/**
 * @method Boleto|null find($id, $lockMode = null, $lockVersion = null)
 * @method Boleto|null findOneBy(array $criteria, array $orderBy = null)
 * @method Boleto[]    findAll()
 * @method Boleto[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class BoletoRepository extends ServiceEntityRepository
{


    public function __construct(RegistryInterface $registry)
    {
        parent::__construct($registry, Boleto::class);
    }

    /**
     * Monta a query base
     *
     * @return \Doctrine\ORM\QueryBuilder
     */
    protected function montaQueryBase()
    {
        $queryBuilder = $this->createQueryBuilder("bl");
        $queryBuilder->addSelect("fran");
        $queryBuilder->addSelect("tr");
        $queryBuilder->addSelect("ct");
        $queryBuilder->addSelect("sp");
        $queryBuilder->addSelect("cr");
        $queryBuilder->addSelect("cont");
        $queryBuilder->addSelect("al");
        $queryBuilder->addSelect("pesal");
        $queryBuilder->leftJoin("bl.franqueada", "fran");
        $queryBuilder->leftJoin("bl.titulo_receber", "tr");
        $queryBuilder->leftJoin("tr.aluno", "al");
        $queryBuilder->leftJoin("al.pessoa", "pesal");
        $queryBuilder->leftJoin("tr.conta_receber", "cr");
        $queryBuilder->leftJoin("cr.contrato", "cont");
        $queryBuilder->join("tr.sacado_pessoa", "sp");
        $queryBuilder->leftJoin("al.pessoa", "aluno");
        $queryBuilder->leftJoin("bl.conta", "ct");

        return $queryBuilder;
    }

    /**
     * Buscar os boletos pela propiedade
     *
     * @param array $parametros
     *
     * @return NULL|\App\Entity\Principal\Boleto
     */
    public function buscarBoletoPorPropiedades($parametros)
    {
        $queryBuilder = $this->montaQueryBase();
        $this->montaFiltros($queryBuilder, $parametros);
        return $queryBuilder->getQuery()->getOneOrNullResult();
    }

    /**
     * Monta os filtros para realizacao da busca
     *
     * @param \Doctrine\ORM\QueryBuilder $queryBuilder
     * @param array $parametros
     */
    protected function montaFiltros(&$queryBuilder, $parametros)
    {
        $queryBuilder->where("fran.id = :franqueadaId");
        if ((isset($parametros[ConstanteParametros::CHAVE_FRANQUEADA]) === true)&&(empty($parametros[ConstanteParametros::CHAVE_FRANQUEADA]) === false)) {
            $queryBuilder->setParameter("franqueadaId", $parametros[ConstanteParametros::CHAVE_FRANQUEADA]);
        } else {
            $queryBuilder->setParameter("franqueadaId", VariaveisCompartilhadas::$franqueadaID);
        }

        if ((isset($parametros[ConstanteParametros::CHAVE_NOSSO_NUMERO]) === true)&&(empty($parametros[ConstanteParametros::CHAVE_NOSSO_NUMERO]) === false)) {
            $queryBuilder->andWhere("bl.nosso_numero LIKE :nossoNumero");
            $queryBuilder->setParameter("nossoNumero", "%" . $parametros[ConstanteParametros::CHAVE_NOSSO_NUMERO] . "%");
        }

        if ((isset($parametros[ConstanteParametros::CHAVE_PESSOA_ALUNO]) === true)&&(empty($parametros[ConstanteParametros::CHAVE_PESSOA_ALUNO]) === false)) {
            $queryBuilder->andWhere(
                $queryBuilder->expr()->orX(
                    $queryBuilder->expr()->eq("pesal.id", ":pessoaAlunoId"),
                    $queryBuilder->expr()->eq("sp.id", ":pessoaAlunoId")
                )
            );

            $queryBuilder->setParameter("pessoaAlunoId", $parametros[ConstanteParametros::CHAVE_PESSOA_ALUNO]);
        }

        if ((isset($parametros[ConstanteParametros::CHAVE_SITUACAO_COBRANCA]) === true)&&(empty($parametros[ConstanteParametros::CHAVE_SITUACAO_COBRANCA]) === false)) {
            $queryBuilder->andWhere("bl.situacao_cobranca IN (:situacaoCobranca)");
            $queryBuilder->setParameter("situacaoCobranca", $parametros[ConstanteParametros::CHAVE_SITUACAO_COBRANCA]);
        }

        $this->montaFiltrosEntreDatas($queryBuilder, $parametros);
    }

     /**
      * Monta os filtros por de datas para realizacao da busca
      *
      * @param \Doctrine\ORM\QueryBuilder $queryBuilder
      * @param array $parametros
      */
    private function montaFiltrosEntreDatas(&$queryBuilder, $parametros)
    {

        if ((isset($parametros[ConstanteParametros::CHAVE_DATA_VENCIMENTO_DE]) === true)&&(empty($parametros[ConstanteParametros::CHAVE_DATA_VENCIMENTO_DE]) === false)) {
            $queryBuilder->andWhere("bl.data_vencimento >= :dataVencimentoDe");
            $queryBuilder->setParameter("dataVencimentoDe", $parametros[ConstanteParametros::CHAVE_DATA_VENCIMENTO_DE]);
        }

        if ((isset($parametros[ConstanteParametros::CHAVE_DATA_VENCIMENTO_ATE]) === true)&&(empty($parametros[ConstanteParametros::CHAVE_DATA_VENCIMENTO_ATE]) === false)) {
            $queryBuilder->andWhere("bl.data_vencimento <= :dataVencimentoAte");
            $queryBuilder->setParameter("dataVencimentoAte", $parametros[ConstanteParametros::CHAVE_DATA_VENCIMENTO_ATE]);
        }

        if ((isset($parametros[ConstanteParametros::CHAVE_DATA_EMISSAO_DE]) === true)&&(empty($parametros[ConstanteParametros::CHAVE_DATA_EMISSAO_DE]) === false)) {
            $queryBuilder->andWhere("tr.data_emissao >= :dataEmissaoDe");
            $queryBuilder->setParameter("dataEmissaoDe", $parametros[ConstanteParametros::CHAVE_DATA_EMISSAO_DE]);
        }

        if ((isset($parametros[ConstanteParametros::CHAVE_DATA_EMISSAO_ATE]) === true)&&(empty($parametros[ConstanteParametros::CHAVE_DATA_EMISSAO_ATE]) === false)) {
            $queryBuilder->andWhere("tr.data_emissao <= :dataEmissaoAte");
            $queryBuilder->setParameter("dataEmissaoAte", $parametros[ConstanteParametros::CHAVE_DATA_EMISSAO_ATE]);
        }
    }

    /**
     * Filtra os boletos por pagina
     *
     * @param array $parametros
     * @param number $numeroItensPorPagina
     *
     * @return \Knp\Component\Pager\Pagination\SlidingPagination
     */
    public function filtrarBoletoPorPagina($parametros, $numeroItensPorPagina=50)
    {
        $queryBuilder = $this->montaQueryBase();
        $this->montaFiltros($queryBuilder, $parametros);
        return \App\Helper\FunctionHelper::montaPaginatorPaginacao($queryBuilder, $parametros[ConstanteParametros::CHAVE_PAGINA], $numeroItensPorPagina);
    }

    /**
     * Filtra os registros por id
     *
     * @param int $id
     *
     * @return array|NULL
     */
    public function buscarRegistroPorId($id)
    {
        $queryBuilder = $this->montaQueryBase();
        $queryBuilder->where("bl.id = :id");
        $queryBuilder->setParameter("id", $id);
        return \App\Helper\FunctionHelper::retornaArrayNull($queryBuilder);
    }

    /**
     * Buscar todos os boletos
     *
     * @param array $listaBoletos
     *
     * @return \App\Entity\Principal\Boleto[]
     */
    public function buscarBoletosPorIds($listaBoletos=[])
    {
        $queryBuilder = $this->montaQueryBase();

        $queryBuilder->andWhere("bl.id IN (:boletosId)");
        $queryBuilder->setParameter('boletosId', $listaBoletos);
        return $queryBuilder->getQuery()->getResult(\Doctrine\ORM\Query::HYDRATE_ARRAY);
    }

    /**
     * Buscar todos os boletos
     *
     * @param array $listaBoletos
     *
     * @return \App\Entity\Principal\Boleto[]
     */
    public function buscarTodosBoletosORM($listaBoletos=[])
    {
        $queryBuilder = $this->montaQueryBase();
        $queryBuilder->andWhere("bl.id IN (:boletosId)");
        $queryBuilder->setParameter('boletosId', $listaBoletos);

        return $queryBuilder->getQuery()->getResult(\Doctrine\ORM\Query::HYDRATE_OBJECT);
    }


}
