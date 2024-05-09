<?php

namespace App\DataFixtures;

use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\Persistence\ObjectManager;
use App\Entity\Principal\DiasSubsequentes;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;

class DiasSubsequentesFixtures extends Fixture implements DependentFixtureInterface
{


    public function load(ObjectManager $manager)
    {
        $franqueada   = $this->getReference(FranqueadaFixtures::FRANQUEADA_REFERENCE);
        $franqueadora = $this->getReference(FranqueadaFixtures::FRANQUEADORA_REFERENCE);

        $diasSubsequentes = new DiasSubsequentes();
        $diasSubsequentes->addFranqueada($franqueada);
        $diasSubsequentes->addFranqueada($franqueadora);
        $diasSubsequentes->setDescricao('01');
        $diasSubsequentes->setNumeroDia(1);
        $manager->persist($diasSubsequentes);

        $diasSubsequentes = new DiasSubsequentes();
        $diasSubsequentes->addFranqueada($franqueada);
        $diasSubsequentes->addFranqueada($franqueadora);
        $diasSubsequentes->setDescricao('05');
        $diasSubsequentes->setNumeroDia(5);
        $manager->persist($diasSubsequentes);

        $diasSubsequentes = new DiasSubsequentes();
        $diasSubsequentes->addFranqueada($franqueada);
        $diasSubsequentes->addFranqueada($franqueadora);
        $diasSubsequentes->setDescricao('10');
        $diasSubsequentes->setNumeroDia(10);
        $manager->persist($diasSubsequentes);

        $diasSubsequentes = new DiasSubsequentes();
        $diasSubsequentes->addFranqueada($franqueada);
        $diasSubsequentes->addFranqueada($franqueadora);
        $diasSubsequentes->setDescricao('15');
        $diasSubsequentes->setNumeroDia(15);
        $manager->persist($diasSubsequentes);

        $diasSubsequentes = new DiasSubsequentes();
        $diasSubsequentes->addFranqueada($franqueada);
        $diasSubsequentes->addFranqueada($franqueadora);
        $diasSubsequentes->setDescricao('20');
        $diasSubsequentes->setNumeroDia(20);
        $manager->persist($diasSubsequentes);

        $diasSubsequentes = new DiasSubsequentes();
        $diasSubsequentes->addFranqueada($franqueada);
        $diasSubsequentes->addFranqueada($franqueadora);
        $diasSubsequentes->setDescricao('25');
        $diasSubsequentes->setNumeroDia(25);
        $manager->persist($diasSubsequentes);

        $diasSubsequentes = new DiasSubsequentes();
        $diasSubsequentes->addFranqueada($franqueada);
        $diasSubsequentes->addFranqueada($franqueadora);
        $diasSubsequentes->setDescricao('30');
        $diasSubsequentes->setNumeroDia(30);
        $manager->persist($diasSubsequentes);

        $manager->flush();
    }

    public function getDependencies()
    {
        return [
            FranqueadaFixtures::class,
        ];
    }


}
