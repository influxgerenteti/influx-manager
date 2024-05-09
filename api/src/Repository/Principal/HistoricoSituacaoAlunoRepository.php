<?php

namespace App\Repository\Principal;

use App\Entity\Principal\HistoricoSituacaoAluno;
use App\Helper\ConstanteParametros;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Symfony\Bridge\Doctrine\RegistryInterface;

/**
 * @method HistoricoSituacaoAluno|null find($id, $lockMode = null, $lockVersion = null)
 * @method HistoricoSituacaoAluno|null findOneBy(array $criteria, array $orderBy = null)
 * @method HistoricoSituacaoAluno[]    findAll()
 * @method HistoricoSituacaoAluno[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class HistoricoSituacaoAlunoRepository extends ServiceEntityRepository
{


    public function __construct(RegistryInterface $registry)
    {
        parent::__construct($registry, HistoricoSituacaoAluno::class);
    }

    /**
     * Monta a query para ser executada no relatório
     *
     * @param array $parametros
     *
     * @return string
     */
    public function prepararDadosRelatorio($parametros)
    {
        $queryBuilder = $this->createQueryBuilder("hsa");
        $queryBuilder->select('hsa.id');

        if (is_null($parametros[ConstanteParametros::CHAVE_ANO]) === false) {
            $queryBuilder->andWhere("YEAR(hsa.data_alteracao) = (:ano)");
            $queryBuilder->setParameter("ano", $parametros[ConstanteParametros::CHAVE_ANO]);
        }

        if (is_null($parametros[ConstanteParametros::CHAVE_SITUACAO]) === false) {
            $situacao = explode(',', $parametros[ConstanteParametros::CHAVE_SITUACAO]);
            $queryBuilder->andWhere('hsa.situacao_atual in (:situacao)');
            $queryBuilder->setParameter('situacao', implode("', '", $situacao));
        }

        // Filtra e substitui a query para passar ao Jasper
        $sql = $queryBuilder->getQuery()->getSQL();

        $sql = preg_replace('/.*WHERE\s(.*)$/', '$1', $sql);

        // Seleciona somente os wheres
        $sql = preg_replace('/h0_/', 'historico_situacao_aluno', $sql);

        // Substituição de parâmetros
        $parameters = $queryBuilder->getParameters();
        foreach ($parameters as $parameter) {
            $param = $parameter->getValue();
            $sql   = preg_replace('/\?/', "'$param'", $sql, 1);
        }

        return $sql;
    }


}
