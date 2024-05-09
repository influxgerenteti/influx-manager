<?php

namespace App\DataFixtures;

use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\Persistence\ObjectManager;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use App\Entity\Principal\FuncionarioDisponibilidade;

class FuncionarioDisponibilidadeFixtures extends Fixture implements DependentFixtureInterface
{


    public function load(ObjectManager $manager)
    {
        // $config = new FuncionarioDisponibilidade();
        // $config->setDiaSemana("SEG");
        // $config->setFuncionario($this->getReference(FuncionarioFixtures::FUNCIONARIO_REFERENCE));
        // $config->setHoraInicial(new \DateTime());
        // $config->setHoraFinal(new \DateTime());
        // $manager->persist($config);
        // $manager->flush();
        $config = new FuncionarioDisponibilidade();
        $config->setDiaSemana("SEG");
        $config->setFuncionario($this->getReference(FuncionarioFixtures::FUNCIONARIO5_REFERENCE));
        $config->setHoraInicial(\DateTime::createFromFormat("H:i", "8:00"));
        $config->setHoraFinal(\DateTime::createFromFormat("H:i", "18:00"));
        $manager->persist($config);
        $manager->flush();

        $config = new FuncionarioDisponibilidade();
        $config->setDiaSemana("QUA");
        $config->setFuncionario($this->getReference(FuncionarioFixtures::FUNCIONARIO5_REFERENCE));
        $config->setHoraInicial(\DateTime::createFromFormat("H:i", "13:00"));
        $config->setHoraFinal(\DateTime::createFromFormat("H:i", "17:00"));
        $manager->persist($config);
        $manager->flush();
    }

    public function getDependencies()
    {
        return [
            FuncionarioFixtures::class,
        ];
    }


}
