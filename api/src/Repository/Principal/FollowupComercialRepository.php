<?php

namespace App\Repository\Principal;

use App\Entity\Principal\FollowupComercial;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Symfony\Bridge\Doctrine\RegistryInterface;

use App\Helper\ConstanteParametros;
use App\Helper\VariaveisCompartilhadas;

/**
 * @method FollowupComercial|null find($id, $lockMode = null, $lockVersion = null)
 * @method FollowupComercial|null findOneBy(array $criteria, array $orderBy = null)
 * @method FollowupComercial[]    findAll()
 * @method FollowupComercial[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class FollowupComercialRepository extends ServiceEntityRepository
{


    public function __construct(RegistryInterface $registry)
    {
        parent::__construct($registry, FollowupComercial::class);
    }

    public function buscarFollowupsIndicadores($parametros)
    {
        $queryBuilder = $this->createQueryBuilder('followup');
        $queryBuilder->addSelect("interessado");
        $queryBuilder->addSelect("alunoInteressado");
        $queryBuilder->addSelect("tipo_contato");
        $queryBuilder->addSelect("tipo_prospeccao");
        $queryBuilder->addSelect("workflowInteressado");
        $queryBuilder->addSelect("workflowFollowup");
        $queryBuilder->addSelect("workflowAcaoFollowup");
        $queryBuilder->addSelect("motivoNaoFechamento");
        $queryBuilder->addSelect("destinoWorkflowFollowup");
        $queryBuilder->addSelect("agenda_comercial");
        $queryBuilder->addSelect("tipo_agendamento");

        $queryBuilder->join("followup.interessado", "interessado");
        $queryBuilder->leftJoin("interessado.aluno", "alunoInteressado");
        $queryBuilder->leftJoin("followup.tipo_contato", "tipo_contato");
        $queryBuilder->leftJoin("followup.tipo_prospeccao", "tipo_prospeccao");
        $queryBuilder->leftJoin("followup.agenda_comercial", "agenda_comercial");
        $queryBuilder->leftJoin("interessado.workflow", "workflowInteressado");
        $queryBuilder->leftJoin("followup.workflow", "workflowFollowup");
        $queryBuilder->leftJoin("followup.workflow_acao", "workflowAcaoFollowup");
        $queryBuilder->leftJoin("followup.motivo_nao_fechamento", "motivoNaoFechamento");
        $queryBuilder->leftJoin("workflowAcaoFollowup.destino_workflow", "destinoWorkflowFollowup");
        $queryBuilder->leftJoin("agenda_comercial.tipo_agendamento", "tipo_agendamento");
        $queryBuilder->join("interessado.franqueada", "franqueada");
        $queryBuilder->leftJoin("franqueada.estado", "estado");

        if (is_null($parametros[ConstanteParametros::CHAVE_ESTADO]) === false && empty($parametros[ConstanteParametros::CHAVE_ESTADO]) === false) {
            $queryBuilder->andWhere("estado = :estado");
            $queryBuilder->setParameter('estado', $parametros[ConstanteParametros::CHAVE_ESTADO]);
        }

        if (is_null($parametros[ConstanteParametros::CHAVE_FRANQUEADA_PERSONALIZADA]) === false && empty($parametros[ConstanteParametros::CHAVE_FRANQUEADA_PERSONALIZADA]) === false) {
            $queryBuilder->andWhere("franqueada = :franqueada");
            $queryBuilder->setParameter('franqueada', $parametros[ConstanteParametros::CHAVE_FRANQUEADA_PERSONALIZADA]);
        }

        if (is_null($parametros[ConstanteParametros::CHAVE_FUNCIONARIO]) === false && empty($parametros[ConstanteParametros::CHAVE_FUNCIONARIO]) === false) {
            $queryBuilder->andWhere("followup.consultor_funcionario = :funcionario");
            $queryBuilder->setParameter('funcionario', $parametros[ConstanteParametros::CHAVE_FUNCIONARIO]);
        }
        
        $queryBuilder->andWhere("followup.data_registro >= :inicio");
        $queryBuilder->andWhere("followup.data_registro <= :fim");
        $queryBuilder->setParameter('inicio', $parametros[ConstanteParametros::CHAVE_PERIODO][0]);
        $queryBuilder->setParameter('fim', $parametros[ConstanteParametros::CHAVE_PERIODO][1]);
        $queryBuilder->orderBy('followup.id', 'DESC');
       

        return $queryBuilder->getQuery()->getArrayResult();
    }

}
