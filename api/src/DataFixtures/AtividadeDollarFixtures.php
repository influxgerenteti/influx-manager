<?php

namespace App\DataFixtures;

use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\Persistence\ObjectManager;
use App\Entity\Principal\AtividadeDollar;

class AtividadeDollarFixtures extends Fixture
{


    public function load(ObjectManager $manager)
    {
        $atividadeDollar0 = new AtividadeDollar();
        $atividadeDollar0->setDescricao("HomeWork");
        $manager->persist($atividadeDollar0);

        $atividadeDollar1 = new AtividadeDollar();
        $atividadeDollar1->setDescricao("Composition");
        $manager->persist($atividadeDollar1);

        $atividadeDollar2 = new AtividadeDollar();
        $atividadeDollar2->setDescricao("Super Review");
        $manager->persist($atividadeDollar2);

        $atividadeDollar3 = new AtividadeDollar();
        $atividadeDollar3->setDescricao("Extra Ativities");
        $manager->persist($atividadeDollar3);

        $atividadeDollar4 = new AtividadeDollar();
        $atividadeDollar4->setDescricao("Convers. Day");
        $manager->persist($atividadeDollar4);

        $atividadeDollar5 = new AtividadeDollar();
        $atividadeDollar5->setDescricao("Excursions");
        $manager->persist($atividadeDollar5);

        $atividadeDollar6 = new AtividadeDollar();
        $atividadeDollar6->setDescricao("Speaking Eng. in Class");
        $manager->persist($atividadeDollar6);

        $atividadeDollar7 = new AtividadeDollar();
        $atividadeDollar7->setDescricao("No Absences");
        $manager->persist($atividadeDollar7);

        $manager->flush();
    }


}
