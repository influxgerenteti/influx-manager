<?php

namespace App\DataFixtures;

use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\Persistence\ObjectManager;
use App\Entity\Principal\Cargo;

class CargoFixtures extends Fixture
{

    public const CARGO_AUXILIAR_SERVICOS_REFERENCE      = "cargo_auxiliar_servicos";
    public const CARGO_CONSULTOR_VENDAS_REFERENCE       = "cargo_consultor_vendas";
    public const CARGO_COORDENADOR_PEDAGOGICO_REFERENCE = "cargo_coordenador_pedagogico";
    public const CARGO_GERENTE_REFERENCE   = "cargo_gerente";
    public const CARGO_PROFESSOR_REFERENCE = "cargo_professor";

    public function load(ObjectManager $manager)
    {
        $config = new Cargo();
        $config->setDescricao("Aux. De Serviços Gerais");
        $config->setTipo("ASG");
        $manager->persist($config);
        $this->addReference(self::CARGO_AUXILIAR_SERVICOS_REFERENCE, $config);

        $config = new Cargo();
        $config->setDescricao("Consultor de Vendas");
        $config->setTipo("CON");
        $manager->persist($config);
        $this->addReference(self::CARGO_CONSULTOR_VENDAS_REFERENCE, $config);

        $config = new Cargo();
        $config->setDescricao("Coordenador Pedagógico");
        $config->setTipo("COP");
        $manager->persist($config);
        $this->addReference(self::CARGO_COORDENADOR_PEDAGOGICO_REFERENCE, $config);

        $config = new Cargo();
        $config->setDescricao("Gerente");
        $config->setTipo("GER");
        $manager->persist($config);
        $this->addReference(self::CARGO_GERENTE_REFERENCE, $config);

        $config = new Cargo();
        $config->setDescricao("Professor");
        $config->setTipo("PRO");
        $manager->persist($config);
        $this->addReference(self::CARGO_PROFESSOR_REFERENCE, $config);

        $manager->flush();

    }


}
