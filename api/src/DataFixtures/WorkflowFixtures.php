<?php

namespace App\DataFixtures;

use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\Persistence\ObjectManager;
use App\Entity\Principal\Workflow;
use App\Helper\SituacoesSistema;

class WorkflowFixtures extends Fixture
{
    public const WORKFLOW_CONTATO_INICIAL      = "wf_contato_inicial";
    public const WORKFLOW_RETORNO_TELEFONICO   = "wf_retorno_telefonico";
    public const WORKFLOW_APRESENTACAO_PESSOAL = "wf_apresentacao_pessoal";
    public const WORKFLOW_MATRICULA_PERDIDA    = "wf_matricula_perdida";
    public const WORKFLOW_MATRICULA_CONVERTIDO = "wf_matricula_convertico";

    public function load(ObjectManager $manager)
    {
        $workflow = new Workflow();
        $workflow->setDescricao("Contato Inicial");
        $workflow->setSituacao("A");
        $workflow->setTipoWorkflow(SituacoesSistema::WORKFLOW_CONTATO_INICIAL);
        $manager->persist($workflow);
        $this->addReference(self::WORKFLOW_CONTATO_INICIAL, $workflow);

        $workflow = new Workflow();
        $workflow->setDescricao("Retorno Telefônico");
        $workflow->setSituacao("A");
        $workflow->setTipoWorkflow(SituacoesSistema::WORKFLOW_RETORNO_TELEFONICO);
        $manager->persist($workflow);
        $this->addReference(self::WORKFLOW_RETORNO_TELEFONICO, $workflow);

        $workflow = new Workflow();
        $workflow->setDescricao("Apresentação Pessoal");
        $workflow->setSituacao("A");
        $workflow->setTipoWorkflow(SituacoesSistema::WORKFLOW_APRESENTACAO_PESSOAL);
        $manager->persist($workflow);
        $this->addReference(self::WORKFLOW_APRESENTACAO_PESSOAL, $workflow);

        $workflow = new Workflow();
        $workflow->setDescricao("Matricula Perdida");
        $workflow->setSituacao("A");
        $workflow->setTipoWorkflow(SituacoesSistema::WORKFLOW_MATRICULA_PERDIDA);
        $manager->persist($workflow);
        $this->addReference(self::WORKFLOW_MATRICULA_PERDIDA, $workflow);

        $workflow = new Workflow();
        $workflow->setDescricao("Convertido");
        $workflow->setSituacao("A");
        $workflow->setTipoWorkflow(SituacoesSistema::WORKFLOW_MATRICULA_CONVERTIDO);
        $manager->persist($workflow);
        $this->addReference(self::WORKFLOW_MATRICULA_CONVERTIDO, $workflow);

        $manager->flush();
    }


}
