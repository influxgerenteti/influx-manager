<?php

namespace App\DataFixtures;

use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\Persistence\ObjectManager;
use App\Entity\Principal\ParametrosFranqueadora;

class ParametrosFranqueadoraFixtures extends Fixture
{


    public function load(ObjectManager $manager)
    {
        $parametrosFranqueadora = new ParametrosFranqueadora();
        $parametrosFranqueadora->setDiasVariacaoVencimento(3);
        $parametrosFranqueadora->setPercentualVariacaoValores(0);
        $parametrosFranqueadora->setDiasReativacaoInteressado(7);
        $manager->persist($parametrosFranqueadora);
        $manager->flush();
    }


}
