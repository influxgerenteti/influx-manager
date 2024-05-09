<?php

namespace App\Repository\Principal;

use App\Entity\Principal\AgendaComercial;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Symfony\Bridge\Doctrine\RegistryInterface;
use App\Helper\VariaveisCompartilhadas;
use App\Helper\ConstanteParametros;
use App\Helper\FunctionHelper;

/**
 * @method AgendaComercial|null find($id, $lockMode = null, $lockVersion = null)
 * @method AgendaComercial|null findOneBy(array $criteria, array $orderBy = null)
 * @method AgendaComercial[]    findAll()
 * @method AgendaComercial[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class AgendaComercialRepository extends ServiceEntityRepository
{


    public function __construct(RegistryInterface $registry)
    {
        parent::__construct($registry, AgendaComercial::class);
    }

    /**
     * Query para realizar a busca de agendas comerciais
     *
     * @return \Doctrine\ORM\QueryBuilder
     */
    protected function montaQueryBase()
    {
        $queryBuilder = $this->createQueryBuilder("ac");
        $queryBuilder->addSelect("fran");
        $queryBuilder->addSelect("ta");
        $queryBuilder->addSelect("f");
        $queryBuilder->addSelect("u");
        $queryBuilder->addSelect("i");
        $queryBuilder->addSelect("fpc");
        $queryBuilder->addSelect("w");
        $queryBuilder->leftJoin("ac.franqueada", "fran");
        $queryBuilder->leftJoin("ac.tipo_agendamento", "ta");
        $queryBuilder->leftJoin("ac.funcionario", "f");
        $queryBuilder->leftJoin("ac.usuario", "u");
        $queryBuilder->leftJoin("ac.interessado", "i");
        $queryBuilder->leftJoin("ac.followupComercials", "fpc");
        $queryBuilder->leftJoin("i.workflow", "w");

        return $queryBuilder;
    }

    /**
     * Query para realizar fitlro de franqueada
     *
     * @param \Doctrine\ORM\QueryBuilder $queryBuilder
     */
    private function filtrarFranqueada(&$queryBuilder)
    {
        $queryBuilder->andWhere("fran.id = :franqueada");
        $queryBuilder->setParameter('franqueada', VariaveisCompartilhadas::$franqueadaID);
    }

    /**
     * Filtra as agendas comerciais por pagina e numero de itens por pagina
     *
     * @param array $parametros
     *
     * @return \App\Entity\Principal\AgendaComercial[] Resultados em formato de array
     */
    public function filtrarAgendaComercialPorPagina($parametros)
    {
        $queryBuilder = $this->montaQueryBase();
        $this->filtrarFranqueada($queryBuilder);

        $queryBuilder->andWhere("ac.situacao = 'NC'");

        if (is_null($parametros[ConstanteParametros::CHAVE_DATA]) === true) {
            $parametros[ConstanteParametros::CHAVE_DATA] = new \DateTime();
        } else {
            $parametros[ConstanteParametros::CHAVE_DATA] = FunctionHelper::formataCampoDateTimeJS($parametros[ConstanteParametros::CHAVE_DATA]);
        }

        $queryBuilder->andWhere("ac.data_agendamento >= :dataInicio");
        $queryBuilder->andWhere("ac.data_agendamento <= :dataFim");
        $queryBuilder->setParameter("dataInicio", $parametros[ConstanteParametros::CHAVE_DATA]->format('Y-m-d'));
        $queryBuilder->setParameter("dataFim", $parametros[ConstanteParametros::CHAVE_DATA]->format('Y-m-d 23:59:59'));

        if (is_null($parametros[ConstanteParametros::CHAVE_TIPO_WORKFLOW]) === false) {
            $queryBuilder->andWhere("w.tipo_workflow IN (:tipoWorkflow)");
            $queryBuilder->setParameter("tipoWorkflow", $parametros[ConstanteParametros::CHAVE_TIPO_WORKFLOW]);
        }

        if (is_null($parametros[ConstanteParametros::CHAVE_NOME]) === false) {
            $queryBuilder->andWhere("i.nome LIKE :nome");
            $queryBuilder->setParameter("nome", "%" . $parametros[ConstanteParametros::CHAVE_NOME] . "%");
        }

        if (is_null($parametros[ConstanteParametros::CHAVE_TELEFONE]) === false) {
            $queryBuilder->andWhere(
                $queryBuilder->expr()->orX(
                    $queryBuilder->expr()->like("i.telefone_contato", ":telefone"),
                    $queryBuilder->expr()->like("i.telefone_secundario", ":telefone")
                )
            );

            $queryBuilder->setParameter("telefone", "%" . $parametros[ConstanteParametros::CHAVE_TELEFONE] . "%");
        }

        return $queryBuilder->getQuery()->getArrayResult();
    }

    /**
     * Busca a agenda comercial pela chave primaria
     *
     * @param integer $id Chave primaria da agenda comercial
     *
     * @return array|NULL
     */
    public function buscarPorId($id)
    {
        $queryBuilder = $this->montaQueryBase();
        $this->filtrarFranqueada($queryBuilder);
        $queryBuilder->andWhere("ac.id = :id");
        $queryBuilder->setParameter("id", $id);
        return \App\Helper\FunctionHelper::retornaArrayNull($queryBuilder, true);
    }


}
