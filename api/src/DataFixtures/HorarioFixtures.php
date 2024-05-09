<?php

namespace App\DataFixtures;

use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\Persistence\ObjectManager;
use App\Entity\Principal\Horario;
use App\Entity\Principal\HorarioAula;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;

class HorarioFixtures extends Fixture implements DependentFixtureInterface
{

    public const HORARIO_REFERENCE  = "horario";
    public const HORARIO2_REFERENCE = "horario2";
    public const HORARIO3_REFERENCE = "horario3";
    public const HORARIO4_REFERENCE = "horario4";
    public const HORARIO5_REFERENCE = "horario5";
    public const HORARIO6_REFERENCE = "horario6";
    public const HORARIO7_REFERENCE = "horario7";
    public const HORARIO8_REFERENCE = "horario8";

    public function load(ObjectManager $manager)
    {
        $horario = new Horario();
        $horario->setFranqueada($this->getReference(FranqueadaFixtures::FRANQUEADA_REFERENCE));
        $horario->setDescricao("Seg&Qua 18:00/19:00");
        $manager->persist($horario);
        $this->addReference(self::HORARIO_REFERENCE, $horario);

        $horarioAula = new HorarioAula();
        $horarioAula->setDiaSemana("SEG");
        $horarioAula->setHorario($horario);
        $horarioAula->setHorarioInicio(\DateTime::createFromFormat("H:i", "21:00"));
        $manager->persist($horarioAula);

        $horarioAula = new HorarioAula();
        $horarioAula->setDiaSemana("QUA");
        $horarioAula->setHorario($horario);
        $horarioAula->setHorarioInicio(\DateTime::createFromFormat("H:i", "22:00"));
        $manager->persist($horarioAula);

        $horario2 = new Horario();
        $horario2->setFranqueada($this->getReference(FranqueadaFixtures::FRANQUEADA_REFERENCE));
        $horario2->setDescricao("Ter&Qui 10:00/11:00");
        $manager->persist($horario2);
        $this->addReference(self::HORARIO2_REFERENCE, $horario2);

        $horarioAula2 = new HorarioAula();
        $horarioAula2->setDiaSemana("TER");
        $horarioAula2->setHorario($horario2);
        $horarioAula2->setHorarioInicio(\DateTime::createFromFormat("H:i", "13:00"));
        $manager->persist($horarioAula2);

        $horarioAula2 = new HorarioAula();
        $horarioAula2->setDiaSemana("QUI");
        $horarioAula2->setHorario($horario2);
        $horarioAula2->setHorarioInicio(\DateTime::createFromFormat("H:i", "13:00"));
        $manager->persist($horarioAula2);

        $horario3 = new Horario();
        $horario3->setFranqueada($this->getReference(FranqueadaFixtures::FRANQUEADA_REFERENCE));
        $horario3->setDescricao("Qui&Sex 11:00/12:00");
        $manager->persist($horario3);
        $this->addReference(self::HORARIO3_REFERENCE, $horario3);

        $horarioAula3 = new HorarioAula();
        $horarioAula3->setDiaSemana("QUI");
        $horarioAula3->setHorario($horario3);
        $horarioAula3->setHorarioInicio(\DateTime::createFromFormat("H:i", "14:00"));
        $manager->persist($horarioAula3);

        $horarioAula3 = new HorarioAula();
        $horarioAula3->setDiaSemana("SEX");
        $horarioAula3->setHorario($horario3);
        $horarioAula3->setHorarioInicio(\DateTime::createFromFormat("H:i", "14:00"));
        $manager->persist($horarioAula3);

        $horario4 = new Horario();
        $horario4->setFranqueada($this->getReference(FranqueadaFixtures::FRANQUEADA_REFERENCE));
        $horario4->setDescricao("Sab 08:00/09:00");
        $manager->persist($horario4);
        $this->addReference(self::HORARIO4_REFERENCE, $horario4);

        $horarioAula4 = new HorarioAula();
        $horarioAula4->setDiaSemana("SAB");
        $horarioAula4->setHorario($horario4);
        $horarioAula4->setHorarioInicio(\DateTime::createFromFormat("H:i", "11:00"));
        $manager->persist($horarioAula4);

        $horario5 = new Horario();
        $horario5->setFranqueada($this->getReference(FranqueadaFixtures::FRANQUEADORA_REFERENCE));
        $horario5->setDescricao("Seg&Qua 15:00/16:00");
        $manager->persist($horario5);
        $this->addReference(self::HORARIO5_REFERENCE, $horario5);

        $horarioAula5 = new HorarioAula();
        $horarioAula5->setDiaSemana("SEG");
        $horarioAula5->setHorario($horario5);
        $horarioAula5->setHorarioInicio(\DateTime::createFromFormat("H:i", "18:00"));
        $manager->persist($horarioAula5);

        $horarioAula5 = new HorarioAula();
        $horarioAula5->setDiaSemana("QUA");
        $horarioAula5->setHorario($horario5);
        $horarioAula5->setHorarioInicio(\DateTime::createFromFormat("H:i", "18:00"));
        $manager->persist($horarioAula);

        $horario6 = new Horario();
        $horario6->setFranqueada($this->getReference(FranqueadaFixtures::FRANQUEADORA_REFERENCE));
        $horario6->setDescricao("Ter&Qui 10:00/11:00");
        $manager->persist($horario6);
        $this->addReference(self::HORARIO6_REFERENCE, $horario6);

        $horarioAula6 = new HorarioAula();
        $horarioAula6->setDiaSemana("TER");
        $horarioAula6->setHorario($horario6);
        $horarioAula6->setHorarioInicio(\DateTime::createFromFormat("H:i", "13:00"));
        $manager->persist($horarioAula6);

        $horarioAula6 = new HorarioAula();
        $horarioAula6->setDiaSemana("QUI");
        $horarioAula6->setHorario($horario6);
        $horarioAula6->setHorarioInicio(\DateTime::createFromFormat("H:i", "13:00"));
        $manager->persist($horarioAula6);

        $horario7 = new Horario();
        $horario7->setFranqueada($this->getReference(FranqueadaFixtures::FRANQUEADORA_REFERENCE));
        $horario7->setDescricao("Qui&Sex 11:00/12:00");
        $manager->persist($horario7);
        $this->addReference(self::HORARIO7_REFERENCE, $horario7);

        $horarioAula7 = new HorarioAula();
        $horarioAula7->setDiaSemana("QUI");
        $horarioAula7->setHorario($horario7);
        $horarioAula7->setHorarioInicio(\DateTime::createFromFormat("H:i", "14:00"));
        $manager->persist($horarioAula7);

        $horarioAula7 = new HorarioAula();
        $horarioAula7->setDiaSemana("SEX");
        $horarioAula7->setHorario($horario7);
        $horarioAula7->setHorarioInicio(\DateTime::createFromFormat("H:i", "14:00"));
        $manager->persist($horarioAula7);

        $horario8 = new Horario();
        $horario8->setFranqueada($this->getReference(FranqueadaFixtures::FRANQUEADORA_REFERENCE));
        $horario8->setDescricao("Sab 08:00/09:00");
        $manager->persist($horario8);
        $this->addReference(self::HORARIO8_REFERENCE, $horario8);

        $horarioAula8 = new HorarioAula();
        $horarioAula8->setDiaSemana("SAB");
        $horarioAula8->setHorario($horario4);
        $horarioAula8->setHorarioInicio(\DateTime::createFromFormat("H:i", "11:00"));
        $manager->persist($horarioAula8);

        $manager->flush();
    }

    public function getDependencies()
    {
        return [
            FranqueadaFixtures::class,
        ];
    }


}
