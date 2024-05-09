<?php

namespace App\DataFixtures;

use App\Entity\Principal\NegociacaoParceriaWorkflow;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\Persistence\ObjectManager;

class NegociacaoParceriaWorkflowFixtures extends Fixture
{


    public const NEGOCIACAO_PARCERIA_WORKFLOW_ATIVO         = "ativo";
    public const NEGOCIACAO_PARCERIA_WORKFLOW_EM_NEGOCIACAO = "em_negociacao";
    public const NEGOCIACAO_PARCERIA_WORKFLOW_PARCERIA_NAO_REALIZADA = "parceria_nao_realizada";
    public const NEGOCIACAO_PARCERIA_WORKFLOW_PENDENTE_DE_VALIDACAO  = "pendente_de_validacao";
    public const NEGOCIACAO_PARCERIA_WORKFLOW_INATIVO = "inativo";
    public const NEGOCIACAO_PARCERIA_WORKFLOW_NEGADO_FRANQUEADORA = "negado_franqueadora";

    public function load(ObjectManager $manager)
    {
        $negociacao_workflow_1 = new NegociacaoParceriaWorkflow();
        $negociacao_workflow_1->setDescricao("Ativo");
        $negociacao_workflow_1->setSituacao("A");
        $negociacao_workflow_1->setTipoWorkflow("ATI");
        $negociacao_workflow_1->setMostrarOpcoesSituacao(true);
        $manager->persist($negociacao_workflow_1);

        $negociacao_workflow_2 = new NegociacaoParceriaWorkflow();
        $negociacao_workflow_2->setDescricao("Em Negociação");
        $negociacao_workflow_2->setSituacao("A");
        $negociacao_workflow_2->setTipoWorkflow("EN");
        $negociacao_workflow_2->setMostrarOpcoesSituacao(true);
        $manager->persist($negociacao_workflow_2);

        $negociacao_workflow_3 = new NegociacaoParceriaWorkflow();
        $negociacao_workflow_3->setDescricao("Parceria não realizada");
        $negociacao_workflow_3->setSituacao("A");
        $negociacao_workflow_3->setTipoWorkflow("PNR");
        $negociacao_workflow_3->setMostrarOpcoesSituacao(true);
        $manager->persist($negociacao_workflow_3);

        $negociacao_workflow_4 = new NegociacaoParceriaWorkflow();
        $negociacao_workflow_4->setDescricao("Pendente aprovação da franqueadora");
        $negociacao_workflow_4->setSituacao("A");
        $negociacao_workflow_4->setTipoWorkflow("PV");
        $negociacao_workflow_4->setMostrarOpcoesSituacao(false);
        $manager->persist($negociacao_workflow_4);

        $negociacao_workflow_5 = new NegociacaoParceriaWorkflow();
        $negociacao_workflow_5->setDescricao("Inativo");
        $negociacao_workflow_5->setSituacao("A");
        $negociacao_workflow_5->setTipoWorkflow("I");
        $negociacao_workflow_5->setMostrarOpcoesSituacao(false);
        $manager->persist($negociacao_workflow_5);

        $negociacao_workflow_6 = new NegociacaoParceriaWorkflow();
        $negociacao_workflow_6->setDescricao("Negado pela franqueadora");
        $negociacao_workflow_6->setSituacao("A");
        $negociacao_workflow_6->setTipoWorkflow("NE");
        $negociacao_workflow_6->setMostrarOpcoesSituacao(false);
        $manager->persist($negociacao_workflow_6);

        $manager->flush();
        $this->addReference(self::NEGOCIACAO_PARCERIA_WORKFLOW_ATIVO, $negociacao_workflow_1);
        $this->addReference(self::NEGOCIACAO_PARCERIA_WORKFLOW_EM_NEGOCIACAO, $negociacao_workflow_2);
        $this->addReference(self::NEGOCIACAO_PARCERIA_WORKFLOW_PARCERIA_NAO_REALIZADA, $negociacao_workflow_3);
        $this->addReference(self::NEGOCIACAO_PARCERIA_WORKFLOW_PENDENTE_DE_VALIDACAO, $negociacao_workflow_4);
        $this->addReference(self::NEGOCIACAO_PARCERIA_WORKFLOW_INATIVO, $negociacao_workflow_5);
        $this->addReference(self::NEGOCIACAO_PARCERIA_WORKFLOW_NEGADO_FRANQUEADORA, $negociacao_workflow_6);
    }


}
