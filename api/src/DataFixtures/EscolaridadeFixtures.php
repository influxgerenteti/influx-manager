<?php

namespace App\DataFixtures;

use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\Persistence\ObjectManager;
use App\Entity\Principal\Escolaridade;

class EscolaridadeFixtures extends Fixture
{
    public const ESCOLARIDADE_REFERENCE = "ensino_medio";

    public function load(ObjectManager $manager)
    {
        $config = new Escolaridade();
        $config->setDescricao("Ensino fundamental");
        $manager->persist($config);

        $escolaridade2 = new Escolaridade();
        $escolaridade2->setDescricao("Ensino médio");
        $manager->persist($escolaridade2);

        $config = new Escolaridade();
        $config->setDescricao("Ensino superior");
        $manager->persist($config);

        $config = new Escolaridade();
        $config->setDescricao("Pós graduação");
        $manager->persist($config);

        $config = new Escolaridade();
        $config->setDescricao("Outros");
        $manager->persist($config);
        $manager->flush();

        $this->addReference(self::ESCOLARIDADE_REFERENCE, $escolaridade2);

    }


}
