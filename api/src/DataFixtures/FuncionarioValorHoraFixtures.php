<?php

namespace App\DataFixtures;

use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\Persistence\ObjectManager;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use App\Entity\Principal\FuncionarioValorHora;

class FuncionarioValorHoraFixtures extends Fixture implements DependentFixtureInterface
{


    public function load(ObjectManager $manager)
    {
        $config = new FuncionarioValorHora();
        $config->setFuncionario($this->getReference(FuncionarioFixtures::FUNCIONARIO_REFERENCE));
        $config->setValorHora($this->getReference(ValorHoraFixtures::VALOR_HORA1_REFFERENCE));
        $config->setValor(25.50);
        $manager->persist($config);
        $manager->flush();

        $config = new FuncionarioValorHora();
        $config->setFuncionario($this->getReference(FuncionarioFixtures::FUNCIONARIO5_REFERENCE));
        $config->setValorHora($this->getReference(ValorHoraFixtures::VALOR_HORA2_REFFERENCE));
        $config->setValor(30.75);
        $manager->persist($config);
        $manager->flush();

    }

    public function getDependencies()
    {
        return [
            FuncionarioFixtures::class,
            ValorHoraFixtures::class,
        ];
    }


}
