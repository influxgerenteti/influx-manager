<?php

namespace App\DataFixtures;

use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\Persistence\ObjectManager;
use App\Entity\Principal\NivelInstrutor;

class NivelInstrutorFixtures extends Fixture
{

    public const NIVEL_INSTRUTOR_REFERENCE  = "nivel_instrutor";
    public const NIVEL_INSTRUTOR2_REFERENCE = "nivel_instrutor2";
    public const NIVEL_INSTRUTOR3_REFERENCE = "nivel_instrutor3";

    public function load(ObjectManager $manager)
    {
        $nivel = new NivelInstrutor();
        $nivel->setDescricao("Novo");
        $nivel->setAno(0);
        $manager->persist($nivel);

        $nivel2 = new NivelInstrutor();
        $nivel2->setDescricao("1 ano");
        $nivel2->setAno(1);
        $manager->persist($nivel2);

        $nivel3 = new NivelInstrutor();
        $nivel3->setDescricao("2 anos");
        $nivel3->setAno(2);
        $manager->persist($nivel3);

        $manager->flush();

        $this->addReference(self::NIVEL_INSTRUTOR_REFERENCE, $nivel);
        $this->addReference(self::NIVEL_INSTRUTOR2_REFERENCE, $nivel2);
        $this->addReference(self::NIVEL_INSTRUTOR3_REFERENCE, $nivel3);
    }


}
