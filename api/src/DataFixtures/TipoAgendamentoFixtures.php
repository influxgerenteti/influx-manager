<?php
namespace App\DataFixtures;

use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\Persistence\ObjectManager;
use App\Entity\Principal\TipoAgendamento;
use App\Helper\SituacoesSistema;

class TipoAgendamentoFixtures extends Fixture
{


    public function load(ObjectManager $manager)
    {
        $tipo1 = new TipoAgendamento();
        $tipo1->setDescricao("Acompanhamento Pedagógico");
        $tipo1->setCor("#643264");
        $manager->persist($tipo1);

        $tipo2 = new TipoAgendamento();
        $tipo2->setDescricao("Agenda");
        $tipo2->setCor("#2e3090");
        $manager->persist($tipo2);

        $tipo3 = new TipoAgendamento();
        $tipo3->setDescricao("Captação Ativa");
        $tipo3->setCor("#eb6e23");
        $manager->persist($tipo3);

        $tipo4 = new TipoAgendamento();
        $tipo4->setDescricao("Financeiro");
        $tipo4->setCor("#ff8080");
        $manager->persist($tipo4);

        $tipo5 = new TipoAgendamento();
        $tipo5->setDescricao("Interessados");
        $tipo5->setCor("#bcd42f");
        $manager->persist($tipo5);

        $tipo6 = new TipoAgendamento();
        $tipo6->setDescricao("Memo");
        $tipo6->setCor("#e570fa");
        $manager->persist($tipo6);

        $tipo7 = new TipoAgendamento();
        $tipo7->setDescricao("Renovação");
        $tipo7->setCor("#23c8d2");
        $manager->persist($tipo7);

        $tipo8 = new TipoAgendamento();
        $tipo8->setDescricao("Score Aluno");
        $tipo8->setCor("#00c853");
        $manager->persist($tipo8);

        $tipo9 = new TipoAgendamento();
        $tipo9->setDescricao("Solicitação de Serviço");
        $tipo9->setCor("#5599ff");
        $manager->persist($tipo9);

        $tipo10 = new TipoAgendamento();
        $tipo10->setDescricao("Visita");
        $tipo10->setCor("#fafafa");
        $tipo10->setTipo(SituacoesSistema::WORKFLOW_APRESENTACAO_PESSOAL);
        $manager->persist($tipo10);

        $tipo11 = new TipoAgendamento();
        $tipo11->setDescricao("Contato Inicial");
        $tipo11->setCor("#fcfcfc");
        $tipo11->setTipo(SituacoesSistema::WORKFLOW_CONTATO_INICIAL);
        $manager->persist($tipo11);

        $tipo12 = new TipoAgendamento();
        $tipo12->setDescricao("Contato por E-mail");
        $tipo12->setCor("#fafafa");
        $tipo12->setTipo("ST");
        $manager->persist($tipo12);

        $tipo13 = new TipoAgendamento();
        $tipo13->setDescricao("Retorno Telefônico");
        $tipo13->setCor("#fafafa");
        $tipo13->setTipo(SituacoesSistema::WORKFLOW_RETORNO_TELEFONICO);
        $manager->persist($tipo13);

        $manager->flush();

    }


}
