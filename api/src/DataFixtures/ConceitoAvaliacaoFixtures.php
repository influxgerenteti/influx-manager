<?php

namespace App\DataFixtures;

use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\Persistence\ObjectManager;
use App\Entity\Principal\ConceitoAvaliacao;

class ConceitoAvaliacaoFixtures extends Fixture
{
    public const CONCEITO1_REFERENCE       = "conceito1";
    public const CONCEITO2_REFERENCE       = "conceito2";
    public const CONCEITO_INFLUX_REF_B_NEG = "conceito3";
    public const CONCEITO_INFLUX_REF_B_POS = "conceito4";

    public function load(ObjectManager $manager)
    {
        $config = new ConceitoAvaliacao();
        $config->setDescricao("A");
        $config->setValor(10);
        $manager->persist($config);
        $manager->flush();
        $this->addReference(self::CONCEITO2_REFERENCE, $config);

        $config = new ConceitoAvaliacao();
        $config->setDescricao("A-");
        $config->setValor(9.5);
        $manager->persist($config);

        $config = new ConceitoAvaliacao();
        $config->setDescricao("B+");
        $config->setValor(9);
        $manager->persist($config);
        $this->addReference(self::CONCEITO_INFLUX_REF_B_POS, $config);

        $config = new ConceitoAvaliacao();
        $config->setDescricao("B");
        $config->setValor(8.5);
        $manager->persist($config);

        $config = new ConceitoAvaliacao();
        $config->setDescricao("B-");
        $config->setValor(8);
        $manager->persist($config);
        $this->addReference(self::CONCEITO_INFLUX_REF_B_NEG, $config);

        $config = new ConceitoAvaliacao();
        $config->setDescricao("C+");
        $config->setValor(7.5);
        $manager->persist($config);
        $manager->flush();
        $this->addReference(self::CONCEITO1_REFERENCE, $config);

        $config = new ConceitoAvaliacao();
        $config->setDescricao("C");
        $config->setValor(7);
        $manager->persist($config);

        $config = new ConceitoAvaliacao();
        $config->setDescricao("C-");
        $config->setValor(6.5);
        $manager->persist($config);

        $config = new ConceitoAvaliacao();
        $config->setDescricao("D+");
        $config->setValor(6);
        $manager->persist($config);

        $config = new ConceitoAvaliacao();
        $config->setDescricao("D");
        $config->setValor(5.5);
        $manager->persist($config);

        $manager->flush();

    }


}
