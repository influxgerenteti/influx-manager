<?php

namespace App\DataFixtures;

use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\Persistence\ObjectManager;
use App\Entity\Principal\Idioma;

class IdiomaFixtures extends Fixture
{
    public const INGLES_REFERENCE   = "idioma_ingles";
    public const ESPANHOL_REFERENCE = "idioma_espanhol";

    public function load(ObjectManager $manager)
    {
        $config = new Idioma();
        $config->setSigla("IN");
        $config->setDescricao("Inglês");
        $manager->persist($config);
        $this->addReference(self::INGLES_REFERENCE, $config);

        $config = new Idioma();
        $config->setSigla("ES");
        $config->setDescricao("Espanhol");
        $manager->persist($config);
        $this->addReference(self::ESPANHOL_REFERENCE, $config);

        $manager->flush();

    }


}
