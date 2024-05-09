<?php

namespace App\Repository\Principal;

use App\Entity\Principal\Conta;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Symfony\Bridge\Doctrine\RegistryInterface;
use App\Helper\ConstanteParametros;
use App\Helper\VariaveisCompartilhadas;

/**
 *
 * @method Conta|null find($id, $lockMode = null, $lockVersion = null)
 * @method Conta|null findOneBy(array $criteria, array $orderBy = null)
 * @method Conta[]    findAll()
 * @method Conta[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class ContaRepository extends ServiceEntityRepository
{


    public function __construct(RegistryInterface $registry)
    {
        parent::__construct($registry, Conta::class);
    }

    /**
     * Busca as contas utilizando e relacionamentos
     *
     * @return \Doctrine\ORM\QueryBuilder
     */
    private function buscarContas()
    {
        $queryBuilder = $this->createQueryBuilder("ct");
        $queryBuilder->addSelect("ct, fran, bc");
        $queryBuilder->join("ct.franqueada", "fran");
        $queryBuilder->leftJoin("ct.banco", "bc");

        return $queryBuilder;
    }

    /**
     * Query para realizar fitlro de franqueada
     *
     * @param \Doctrine\ORM\QueryBuilder $queryBuilder
     */
    private function filtrarFranqueada(&$queryBuilder)
    {
        $queryBuilder->where('fran = :franqueada');
        $queryBuilder->setParameter('franqueada', VariaveisCompartilhadas::$franqueadaID);
    }

    /**
     * Filtra as contas por pagina e numero de itens por pagina
     *
     * @param array $parametros
     * @param number $pagina
     * @param number $numeroItensPorPagina
     *
     * @return \App\Entity\Principal\Aluno[] Resultados em formato de array
     */
    public function filtrarContaPorPagina($parametros, $pagina=1, $numeroItensPorPagina=50)
    {
        $opcoes       = [];
        $queryBuilder = $this->buscarContas();
        $this->filtrarFranqueada($queryBuilder);

        if ((isset($parametros[ConstanteParametros::CHAVE_ORDENACAO_COLUNA]) === true)&&(is_null($parametros[ConstanteParametros::CHAVE_ORDENACAO_COLUNA]) === false)) {
            $queryBuilder->orderBy($parametros[ConstanteParametros::CHAVE_ORDENACAO_COLUNA], $parametros[ConstanteParametros::CHAVE_ORDENACAO_SORT]);
            $opcoes[\Knp\Component\Pager\Paginator::SORT_FIELD_PARAMETER_NAME]     = "~";
            $opcoes[\Knp\Component\Pager\Paginator::SORT_DIRECTION_PARAMETER_NAME] = "~";
        }

        return \App\Helper\FunctionHelper::montaPaginatorPaginacao($queryBuilder, $pagina, $numeroItensPorPagina, $opcoes);
    }

    /**
     * Buscar conta por ID
     *
     * @param number $id
     *
     * @return array|null retorna a conta com a id passada ou null
     */
    public function buscarPorID ($id)
    {
        $queryBuilder = $this->buscarContas();
        $this->filtrarFranqueada($queryBuilder);
        $queryBuilder->andWhere('ct.id = :id');
        $queryBuilder->setParameter('id', $id);
        return \App\Helper\FunctionHelper::retornaArrayNull($queryBuilder, true);
    }

    /**
     * Buscar conta de acordo com os parametros passados
     *
     * @param array $parametros
     *
     * @return array|null retorna uma conta ou null
     */
    public function buscarPorParametros ($parametros)
    {
        $queryBuilder = $this->createQueryBuilder("c");
        $queryBuilder->addSelect("c, fran");
        $queryBuilder->join("c.franqueada", "fran");
        $queryBuilder->join("c.banco", "b");
        $queryBuilder->where("1=1");

        if (isset($parametros[ConstanteParametros::CHAVE_BANCO]) === true && is_null($parametros[ConstanteParametros::CHAVE_BANCO]) === false) {
            $queryBuilder->andWhere('b.codigo = :codigo_banco');
            $queryBuilder->setParameter('codigo_banco', $parametros[ConstanteParametros::CHAVE_BANCO]);
        }

        if (isset($parametros[ConstanteParametros::CHAVE_EMPRESA_NO_BANCO]) === true && is_null($parametros[ConstanteParametros::CHAVE_EMPRESA_NO_BANCO]) === false) {
            $queryBuilder->andWhere('c.empresa_no_banco = :empresa_no_banco');
            $queryBuilder->setParameter('empresa_no_banco', $parametros[ConstanteParametros::CHAVE_EMPRESA_NO_BANCO]);
        }

        if (isset($parametros[ConstanteParametros::CHAVE_NUMERO_CONTA]) === true && is_null($parametros[ConstanteParametros::CHAVE_NUMERO_CONTA]) === false) {
            $queryBuilder->andWhere('c.conta_corrente = :conta_corrente');
            $queryBuilder->setParameter('conta_corrente', $parametros[ConstanteParametros::CHAVE_NUMERO_CONTA]);
        }

        if (isset($parametros[ConstanteParametros::CHAVE_CONTA_DV]) === true && is_null($parametros[ConstanteParametros::CHAVE_CONTA_DV]) === false) {
            $queryBuilder->andWhere('c.digito_conta_corrente = :digito_conta_corrente');
            $queryBuilder->setParameter('digito_conta_corrente', $parametros[ConstanteParametros::CHAVE_CONTA_DV]);
        }

        if (isset($parametros[ConstanteParametros::CHAVE_AGENCIA]) === true && is_null($parametros[ConstanteParametros::CHAVE_AGENCIA]) === false) {
            $queryBuilder->andWhere('c.numero_agencia = :numero_agencia');
            $queryBuilder->setParameter('numero_agencia', $parametros[ConstanteParametros::CHAVE_AGENCIA]);
        }

        return \App\Helper\FunctionHelper::retornaArrayNull($queryBuilder, true);
    }


}
