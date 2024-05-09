<?php

namespace App\DataFixtures;

use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\Persistence\ObjectManager;
use App\Entity\Principal\WorkflowAcao;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;

class WorkflowAcaoFixtures extends Fixture implements DependentFixtureInterface
{


    public function load(ObjectManager $manager)
    {
        // Daqui para baixo Pertence tudo ao Contato Inicial
        $workflowAcao = new WorkflowAcao();
        $workflowAcao->setDescricao("Número inválido ou incorreto");
        $workflowAcao->setWorkflow($this->getReference(WorkflowFixtures::WORKFLOW_CONTATO_INICIAL));
        $workflowAcao->setDestinoWorkflow($this->getReference(WorkflowFixtures::WORKFLOW_CONTATO_INICIAL));
        $workflowAcao->setEfetivo(false);
        $manager->persist($workflowAcao);

        $workflowAcao = new WorkflowAcao();
        $workflowAcao->setDescricao("Email enviado aguardando resposta");
        $workflowAcao->setWorkflow($this->getReference(WorkflowFixtures::WORKFLOW_CONTATO_INICIAL));
        $workflowAcao->setDestinoWorkflow($this->getReference(WorkflowFixtures::WORKFLOW_CONTATO_INICIAL));
        $workflowAcao->setEfetivo(false);
        $manager->persist($workflowAcao);

        $workflowAcao = new WorkflowAcao();
        $workflowAcao->setDescricao("Não pôde falar");
        $workflowAcao->setWorkflow($this->getReference(WorkflowFixtures::WORKFLOW_CONTATO_INICIAL));
        $workflowAcao->setDestinoWorkflow($this->getReference(WorkflowFixtures::WORKFLOW_CONTATO_INICIAL));
        $workflowAcao->setEfetivo(false);
        $manager->persist($workflowAcao);

        $workflowAcao = new WorkflowAcao();
        $workflowAcao->setDescricao("Respondeu insistindo em usar o e-mail");
        $workflowAcao->setWorkflow($this->getReference(WorkflowFixtures::WORKFLOW_CONTATO_INICIAL));
        $workflowAcao->setDestinoWorkflow($this->getReference(WorkflowFixtures::WORKFLOW_CONTATO_INICIAL));
        $workflowAcao->setEfetivo(false);
        $manager->persist($workflowAcao);

        $workflowAcao = new WorkflowAcao();
        $workflowAcao->setDescricao("Somente chamou, caiu na caixa postal ou linha ocupada");
        $workflowAcao->setWorkflow($this->getReference(WorkflowFixtures::WORKFLOW_CONTATO_INICIAL));
        $workflowAcao->setDestinoWorkflow($this->getReference(WorkflowFixtures::WORKFLOW_CONTATO_INICIAL));
        $workflowAcao->setEfetivo(false);
        $manager->persist($workflowAcao);

        $workflowAcao = new WorkflowAcao();
        $workflowAcao->setDescricao("Após inúmeras tentativas de contato telefônico foi enviado mensagem por WhatsApp");
        $workflowAcao->setWorkflow($this->getReference(WorkflowFixtures::WORKFLOW_CONTATO_INICIAL));
        $workflowAcao->setDestinoWorkflow($this->getReference(WorkflowFixtures::WORKFLOW_CONTATO_INICIAL));
        $workflowAcao->setEfetivo(false);
        $manager->persist($workflowAcao);

        $workflowAcao = new WorkflowAcao();
        $workflowAcao->setDescricao("Respondeu o e-mail com o seu telefone");
        $workflowAcao->setWorkflow($this->getReference(WorkflowFixtures::WORKFLOW_CONTATO_INICIAL));
        $workflowAcao->setDestinoWorkflow($this->getReference(WorkflowFixtures::WORKFLOW_RETORNO_TELEFONICO));
        $workflowAcao->setEfetivo(true);
        $manager->persist($workflowAcao);

        $workflowAcao = new WorkflowAcao();
        $workflowAcao->setDescricao("Não agendamos visita nesta ligação");
        $workflowAcao->setWorkflow($this->getReference(WorkflowFixtures::WORKFLOW_CONTATO_INICIAL));
        $workflowAcao->setDestinoWorkflow($this->getReference(WorkflowFixtures::WORKFLOW_RETORNO_TELEFONICO));
        $workflowAcao->setEfetivo(true);
        $manager->persist($workflowAcao);

        $workflowAcao = new WorkflowAcao();
        $workflowAcao->setDescricao("Agendamos visita");
        $workflowAcao->setWorkflow($this->getReference(WorkflowFixtures::WORKFLOW_CONTATO_INICIAL));
        $workflowAcao->setDestinoWorkflow($this->getReference(WorkflowFixtures::WORKFLOW_APRESENTACAO_PESSOAL));
        $workflowAcao->setEfetivo(true);
        $manager->persist($workflowAcao);

        $workflowAcao = new WorkflowAcao();
        $workflowAcao->setDescricao("Sem resposta em uma semana do envio e após 2 envios");
        $workflowAcao->setWorkflow($this->getReference(WorkflowFixtures::WORKFLOW_CONTATO_INICIAL));
        $workflowAcao->setDestinoWorkflow($this->getReference(WorkflowFixtures::WORKFLOW_MATRICULA_PERDIDA));
        $workflowAcao->setEfetivo(false);
        $manager->persist($workflowAcao);

        $workflowAcao = new WorkflowAcao();
        $workflowAcao->setDescricao("Matricula perdida");
        $workflowAcao->setWorkflow($this->getReference(WorkflowFixtures::WORKFLOW_CONTATO_INICIAL));
        $workflowAcao->setDestinoWorkflow($this->getReference(WorkflowFixtures::WORKFLOW_MATRICULA_PERDIDA));
        $workflowAcao->setEfetivo(false);
        $manager->persist($workflowAcao);

        // Daqui para baixo pertence tudo ao Retorno telefonico
        $workflowAcao = new WorkflowAcao();
        $workflowAcao->setDescricao("Não agendamos visita nesta ligação");
        $workflowAcao->setWorkflow($this->getReference(WorkflowFixtures::WORKFLOW_RETORNO_TELEFONICO));
        $workflowAcao->setDestinoWorkflow($this->getReference(WorkflowFixtures::WORKFLOW_RETORNO_TELEFONICO));
        $workflowAcao->setEfetivo(true);
        $manager->persist($workflowAcao);

        $workflowAcao = new WorkflowAcao();
        $workflowAcao->setDescricao("Não pôde falar");
        $workflowAcao->setWorkflow($this->getReference(WorkflowFixtures::WORKFLOW_RETORNO_TELEFONICO));
        $workflowAcao->setDestinoWorkflow($this->getReference(WorkflowFixtures::WORKFLOW_RETORNO_TELEFONICO));
        $workflowAcao->setEfetivo(false);
        $manager->persist($workflowAcao);

        $workflowAcao = new WorkflowAcao();
        $workflowAcao->setDescricao("Somente chamou, caiu na caixa postal ou linha ocupada");
        $workflowAcao->setWorkflow($this->getReference(WorkflowFixtures::WORKFLOW_RETORNO_TELEFONICO));
        $workflowAcao->setDestinoWorkflow($this->getReference(WorkflowFixtures::WORKFLOW_RETORNO_TELEFONICO));
        $workflowAcao->setEfetivo(false);
        $manager->persist($workflowAcao);

        $workflowAcao = new WorkflowAcao();
        $workflowAcao->setDescricao("Após inúmeras tentativas de contato telefônico foi enviado mensagem por WhatsApp");
        $workflowAcao->setWorkflow($this->getReference(WorkflowFixtures::WORKFLOW_RETORNO_TELEFONICO));
        $workflowAcao->setDestinoWorkflow($this->getReference(WorkflowFixtures::WORKFLOW_RETORNO_TELEFONICO));
        $workflowAcao->setEfetivo(false);
        $manager->persist($workflowAcao);

        $workflowAcao = new WorkflowAcao();
        $workflowAcao->setDescricao("Agendamos visita");
        $workflowAcao->setWorkflow($this->getReference(WorkflowFixtures::WORKFLOW_RETORNO_TELEFONICO));
        $workflowAcao->setDestinoWorkflow($this->getReference(WorkflowFixtures::WORKFLOW_APRESENTACAO_PESSOAL));
        $workflowAcao->setEfetivo(true);
        $manager->persist($workflowAcao);

        $workflowAcao = new WorkflowAcao();
        $workflowAcao->setDescricao("Matricula perdida");
        $workflowAcao->setWorkflow($this->getReference(WorkflowFixtures::WORKFLOW_RETORNO_TELEFONICO));
        $workflowAcao->setDestinoWorkflow($this->getReference(WorkflowFixtures::WORKFLOW_MATRICULA_PERDIDA));
        $workflowAcao->setEfetivo(false);
        $manager->persist($workflowAcao);

        // Daqui para baixo pertence tudo ao Apresentacao Pessoal
        $workflowAcao = new WorkflowAcao();
        $workflowAcao->setDescricao("Não compareceu e precisaremos reagendar");
        $workflowAcao->setWorkflow($this->getReference(WorkflowFixtures::WORKFLOW_APRESENTACAO_PESSOAL));
        $workflowAcao->setDestinoWorkflow($this->getReference(WorkflowFixtures::WORKFLOW_RETORNO_TELEFONICO));
        $workflowAcao->setEfetivo(false);
        $manager->persist($workflowAcao);

        $workflowAcao = new WorkflowAcao();
        $workflowAcao->setDescricao("Visita reagendada");
        $workflowAcao->setWorkflow($this->getReference(WorkflowFixtures::WORKFLOW_APRESENTACAO_PESSOAL));
        $workflowAcao->setDestinoWorkflow($this->getReference(WorkflowFixtures::WORKFLOW_APRESENTACAO_PESSOAL));
        $workflowAcao->setEfetivo(true);
        $manager->persist($workflowAcao);

        $workflowAcao = new WorkflowAcao();
        $workflowAcao->setDescricao("Compareceu e reagendamos");
        $workflowAcao->setWorkflow($this->getReference(WorkflowFixtures::WORKFLOW_APRESENTACAO_PESSOAL));
        $workflowAcao->setDestinoWorkflow($this->getReference(WorkflowFixtures::WORKFLOW_APRESENTACAO_PESSOAL));
        $workflowAcao->setEfetivo(true);
        $manager->persist($workflowAcao);

        $workflowAcao = new WorkflowAcao();
        $workflowAcao->setDescricao("Compareceu e precisaremos reagendar");
        $workflowAcao->setWorkflow($this->getReference(WorkflowFixtures::WORKFLOW_APRESENTACAO_PESSOAL));
        $workflowAcao->setDestinoWorkflow($this->getReference(WorkflowFixtures::WORKFLOW_RETORNO_TELEFONICO));
        $workflowAcao->setEfetivo(true);
        $manager->persist($workflowAcao);

        $workflowAcao = new WorkflowAcao();
        $workflowAcao->setDescricao("Compareceu e fechou");
        $workflowAcao->setWorkflow($this->getReference(WorkflowFixtures::WORKFLOW_APRESENTACAO_PESSOAL));
        $workflowAcao->setDestinoWorkflow($this->getReference(WorkflowFixtures::WORKFLOW_MATRICULA_CONVERTIDO));
        $workflowAcao->setEfetivo(true);
        $manager->persist($workflowAcao);

        $workflowAcao = new WorkflowAcao();
        $workflowAcao->setDescricao("Matricula perdida");
        $workflowAcao->setWorkflow($this->getReference(WorkflowFixtures::WORKFLOW_APRESENTACAO_PESSOAL));
        $workflowAcao->setDestinoWorkflow($this->getReference(WorkflowFixtures::WORKFLOW_MATRICULA_PERDIDA));
        $workflowAcao->setEfetivo(false);
        $manager->persist($workflowAcao);

        // Matricula perdida
        $workflowAcao = new WorkflowAcao();
        $workflowAcao->setDescricao("Não agendamos visita nesta ligação");
        $workflowAcao->setWorkflow($this->getReference(WorkflowFixtures::WORKFLOW_MATRICULA_PERDIDA));
        $workflowAcao->setDestinoWorkflow($this->getReference(WorkflowFixtures::WORKFLOW_RETORNO_TELEFONICO));
        $workflowAcao->setEfetivo(true);
        $manager->persist($workflowAcao);

        $workflowAcao = new WorkflowAcao();
        $workflowAcao->setDescricao("Agendamos visita");
        $workflowAcao->setWorkflow($this->getReference(WorkflowFixtures::WORKFLOW_MATRICULA_PERDIDA));
        $workflowAcao->setDestinoWorkflow($this->getReference(WorkflowFixtures::WORKFLOW_APRESENTACAO_PESSOAL));
        $workflowAcao->setEfetivo(true);
        $manager->persist($workflowAcao);

        $manager->flush();
    }

    public function getDependencies()
    {
        return [WorkflowFixtures::class];
    }


}
