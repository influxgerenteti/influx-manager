<?php

namespace App\Repository\Principal;

use App\Entity\Principal\AgendaCompromisso;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Symfony\Bridge\Doctrine\RegistryInterface;
use App\Helper\ConstanteParametros;
use App\Helper\VariaveisCompartilhadas;

/**
 * @method AgendaCompromisso|null find($id, $lockMode = null, $lockVersion = null)
 * @method AgendaCompromisso|null findOneBy(array $criteria, array $orderBy = null)
 * @method AgendaCompromisso[]    findAll()
 * @method AgendaCompromisso[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class AgendaCompromissoRepository extends ServiceEntityRepository
{


    public function __construct(RegistryInterface $registry)
    {
        parent::__construct($registry, AgendaCompromisso::class);
    }

    /**
     * Monta Query Principal
     *
     * @return \Doctrine\ORM\QueryBuilder
     */
    protected function montaQueryBuilder()
    {
        $queryBuilder = $this->createQueryBuilder("aco");
        $queryBuilder->leftJoin("aco.franqueada", "fran");
        $queryBuilder->leftJoin("aco.tipo_agendamento", "ta");
        $queryBuilder->leftJoin("aco.usuario", "usu");
        $queryBuilder->leftJoin("aco.funcionario", "func");
        $queryBuilder->leftJoin("aco.atividade_extra", "atve");
        $queryBuilder->leftJoin("aco.ocorrencia_academica", "oca");

        return $queryBuilder;
    }

    /**
     * Monta o filtro de datas da agenda
     *
     * @param \Doctrine\ORM\QueryBuilder $queryBuilder
     * @param array $parametros
     */
    protected function montaFiltroDatas(&$queryBuilder, $parametros)
    {
        if ((isset($parametros[ConstanteParametros::CHAVE_DATA_HORA_INICIO]) === true) && (empty($parametros[ConstanteParametros::CHAVE_DATA_HORA_INICIO]) === false)) {
            $dataInicio = \Carbon\Carbon::parse($parametros[ConstanteParametros::CHAVE_DATA_HORA_INICIO]);
            $dataInicio = $dataInicio->format('Y-m-d H:i:s');
            $dataFim    = null;
            if ((isset($parametros[ConstanteParametros::CHAVE_DATA_HORA_FIM]) === true) && (empty($parametros[ConstanteParametros::CHAVE_DATA_HORA_FIM]) === false)) {
                $dataFim = \Carbon\Carbon::parse($parametros[ConstanteParametros::CHAVE_DATA_HORA_FIM]);
                $dataFim = $dataFim->format('Y-m-d H:i:s');
            } else {
                // fallback buscando os próximos 30 dias, não tem problema que sobre eventos desde que preencha o calendário
                $dataFim = \Carbon\Carbon::now()->add(30, 'days');
                $dataFim = $dataFim->format('Y-m-d H:i:s');
            }

            $queryBuilder->andWhere(
                $queryBuilder->expr()->orX(
                    $queryBuilder->expr()->andX(
                        $queryBuilder->expr()->gte("aco.data_hora_inicio", "'$dataInicio'"),
                        $queryBuilder->expr()->lte("aco.data_hora_inicio", "'$dataFim'")
                    ),
                    $queryBuilder->expr()->andX(
                        $queryBuilder->expr()->isNotNull("aco.data_hora_fim"),
                        $queryBuilder->expr()->gte("aco.data_hora_fim", "'$dataInicio'"),
                        $queryBuilder->expr()->lte("aco.data_hora_fim", "'$dataFim'")
                    )
                )
            );
        }//end if
    }

    /**
     * Monta os filtros da agenda
     *
     * @param \Doctrine\ORM\QueryBuilder $queryBuilder
     * @param array $parametros
     */
    protected function montaFiltros(&$queryBuilder, $parametros)
    {
        if ((isset($parametros[ConstanteParametros::CHAVE_FUNCIONARIO]) === true) && (empty($parametros[ConstanteParametros::CHAVE_FUNCIONARIO]) === false)) {
            $queryBuilder->andWhere("func.id = :funcionarioId");
            $queryBuilder->setParameter("funcionarioId", $parametros[ConstanteParametros::CHAVE_FUNCIONARIO]);
        }

        if ((int) $parametros[ConstanteParametros::CHAVE_TIPO] === 1) {
            $queryBuilder->andWhere("fran.id = :franqueadaId");
            $queryBuilder->setParameter("franqueadaId", VariaveisCompartilhadas::$franqueadaID);
        }
    }

    /**
     * Busca todos os resultados em formato de objeto utilizando os filtros passados pelo front-end
     *
     * @param array $parametros
     *
     * @return \App\Entity\Principal\AgendaCompromisso[]
     */
    public function buscaObjetosAtravesDosFiltros($parametros)
    {
        $queryBuilder = $this->montaQueryBuilder();
        $this->montaFiltros($queryBuilder, $parametros);
        $this->montaFiltroDatas($queryBuilder, $parametros);
        return $queryBuilder->getQuery()->getResult();
    }

    /**
     * Realiza a busca de agenda através da chave primaria
     *
     * @param int $id
     *
     * @return array|NULL
     */
    public function buscarPorId($id)
    {
        $queryBuilder = $this->montaQueryBuilder();
        $queryBuilder->where("aco.id = :agendaCompromissoId");
        $queryBuilder->setParameter("agendaCompromissoId", $id);

        return \App\Helper\FunctionHelper::retornaArrayNull($queryBuilder, true);
    }

    /**
     * Verifica a disponibilidade do funcionario na franqueada através do funcionarioId, dataHoraSolicitada
     *
     * @param array $parametros
     *
     * @return boolean
     */
    public function verificaDisponibilidadeAgendaFuncionario($parametros)
    {
        $dataHoraInicioArray = explode("T", $parametros[ConstanteParametros::CHAVE_DATA_HORA_INICIO]);
        $horaInicioArray     = explode(".", $dataHoraInicioArray[1]);
        $queryBuilder        = $this->montaQueryBuilder();
        $queryBuilder->andWhere("fran.id = :franqueadaId");
        $queryBuilder->andWhere("func.id = :funcionarioId");
        $queryBuilder->andWhere("aco.data_hora_inicio = :dataHoraSolicitada");
        $queryBuilder->setParameter("franqueadaId", VariaveisCompartilhadas::$franqueadaID);
        $queryBuilder->setParameter("funcionarioId", $parametros[ConstanteParametros::CHAVE_FUNCIONARIO]);
        $queryBuilder->setParameter("dataHoraSolicitada", $dataHoraInicioArray[0] . " " . $horaInicioArray[0]);
        $resultados = $queryBuilder->getQuery()->getResult();
        return $$resultados->count() === 0;
    }


}
