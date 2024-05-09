<?php

namespace App\Repository\Principal;

use App\Entity\Principal\IndisponibilidadePersonal;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Symfony\Bridge\Doctrine\RegistryInterface;
use App\Helper\ConstanteParametros;
use App\Helper\SituacoesSistema;
use App\Helper\VariaveisCompartilhadas;
use Carbon\Carbon;
use Carbon\CarbonImmutable;

/**
 * @method IndisponibilidadePersonal|null find($id, $lockMode = null, $lockVersion = null)
 * @method IndisponibilidadePersonal|null findOneBy(array $criteria, array $orderBy = null)
 * @method IndisponibilidadePersonal[]    findAll()
 * @method IndisponibilidadePersonal[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class IndisponibilidadePersonalRepository extends ServiceEntityRepository
{


    public function __construct(RegistryInterface $registry)
    {
        parent::__construct($registry, IndisponibilidadePersonal::class);
    }

    /**
     * Monta filtros data de/até
     *
     * @param \Doctrine\ORM\QueryBuilder $queryBuilder
     * @param array $parametros
     */
    private function montaFiltroDatas(&$queryBuilder, $parametros)
    {

        $queryBuilder->andWhere(
            $queryBuilder->expr()->andX(
                $queryBuilder->expr()->eq("WEEK(i.data_inicio)", "WEEK(:diaSemanaFim)"),
                $queryBuilder->expr()->eq("YEAR(i.data_inicio)", "YEAR(:diaSemanaFim)")
            )
        );
        $queryBuilder->orWhere(
            $queryBuilder->expr()->andX(
                $queryBuilder->expr()->eq("WEEK(i.data_fim)", "WEEK(:diaSemanaInicio)"),
                $queryBuilder->expr()->eq("YEAR(i.data_fim)", "YEAR(:diaSemanaInicio)")
            )
        );

        $dataParam = CarbonImmutable::parse($parametros[ConstanteParametros::CHAVE_DATA]);

        if ($dataParam->weekday() === SituacoesSistema::DIA_SEMANA_SEGUNDA) {
            $dataInicio = Carbon::parse($dataParam);
        } else if ($dataParam->weekday() === SituacoesSistema::DIA_SEMANA_DOMINGO) {
            $dataInicio = Carbon::parse($dataParam->next('monday'));
        } else {
            $dataInicio = Carbon::parse($dataParam->previous('monday'));
        }

        if ($dataParam->weekday() === SituacoesSistema::DIA_SEMANA_SEXTA) {
            $dataFim = Carbon::parse($dataParam);
        } else {
            $dataFim = Carbon::parse($dataParam->next('friday'));
        }

        // Timezone
        $dataInicio->setTime($dataParam->hour, 0, 0);
        // Timezone
        $dataFim->setTime((23 - $dataParam->hour), 59, 59);
        $queryBuilder->setParameter('diaSemanaInicio', $dataInicio->format('Y-m-d H:i:s'));
        $queryBuilder->setParameter('diaSemanaFim', $dataFim->format('Y-m-d H:i:s'));
    }


    /**
     * Busca a lista de indisponibilidades conforme franqueada e data
     *
     * @param array $parametros
     *
     * @return \App\Entity\Principal\IndisponibilidadePersonal[]
     */
    public function listar($parametros=[])
    {
        $queryBuilder = $this->createQueryBuilder('i');
        $queryBuilder->andWhere('i.franqueada = :franqueada');
        $queryBuilder->setParameter('franqueada', VariaveisCompartilhadas::$franqueadaID);

        $this->montaFiltroDatas($queryBuilder, $parametros);

        return \App\Helper\FunctionHelper::retornaResultados($queryBuilder);
    }

    /**
     * Busca uma indisponibilidade
     *
     * @param array $id
     *
     * @return \App\Entity\Principal\IndisponibilidadePersonal|null
     */
    public function buscarPorId($id)
    {
        $queryBuilder = $this->createQueryBuilder('i')
            ->andWhere('i.franqueada = :franqueada')
            ->andWhere('i.id = :id');
        $queryBuilder->setParameter('franqueada', VariaveisCompartilhadas::$franqueadaID);
        $queryBuilder->setParameter('id', $id);

        return \App\Helper\FunctionHelper::retornaPrimeiroResultado($queryBuilder);
    }


}
