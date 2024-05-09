<?php

namespace App\DataFixtures;

use App\Entity\Principal\FollowupComercial;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\Persistence\ObjectManager;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;

class FollowUpComercialFixtures extends Fixture implements DependentFixtureInterface
{

    public const FOLLOW_UP_1 = "follow_up_1";

    public function load(ObjectManager $manager)
    {
        $followUp1 = new FollowupComercial();
        $followUp1->setInteressado($this->getReference(InteressadoFixtures::INTERESSADO5_REFERENCE));
        $followUp1->setUsuario($this->getReference(UsuarioFixtures::USUARIO_REFERENCE));
        $followUp1->setConsultorFuncionario($this->getReference(FuncionarioFixtures::FUNCIONARIO_REFERENCE));
        $followUp1->setTipoContato($this->getReference("tipo_contato_email"));
        $followUp1->setCurso($this->getReference(CursoFixtures::CURSO_INGLES_REFERENCE));
        $followUp1->setWorkflow($this->getReference(WorkflowFixtures::WORKFLOW_CONTATO_INICIAL));
        $followUp1->setTipoLead("R");
        $followUp1->setGrauInteresse("I");
        $followUp1->setPeriodoPretendido("M");
        $followUp1->setFollowup("16/12/2019 19:54 - Administrador - Grau de Interesse: Interessado\nPARA QUEM É O CURSO: Para mim\nCOMO NOS CONHECEU: Através contato com email\nOBJETIVO DETALHADO: Aprender bem o idioma\nCONHECIMENTO DO IDIOMA: Pouco\nFORMA DE PGTO E PARCELAMENTO: \nMOMENTO MÁGICO: \nMELHOR PERIODO PARA CONTATO: \nOUTRAS INFORMAÇÕES: ");
        $manager->persist($followUp1);
        $this->addReference(self::FOLLOW_UP_1, $followUp1);

        $manager->flush();
    }

    public function getDependencies()
    {
        return [
            InteressadoFixtures::class,
            UsuarioFixtures::class,
            TipoContatoFixtures::class,
            CursoFixtures::class,
            WorkflowFixtures::class,
            FuncionarioFixtures::class,
        ];
    }


}
