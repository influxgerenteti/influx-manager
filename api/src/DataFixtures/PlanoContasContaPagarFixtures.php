<?php

namespace App\DataFixtures;

use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;
use App\Entity\Principal\PlanoContasContaPagar;

class PlanoContasContaPagarFixtures extends Fixture implements DependentFixtureInterface
{


    public function load(ObjectManager $manager)
    {
        $plano = new PlanoContasContaPagar();
        $plano->setPlanoConta($this->getReference(PlanoContaFixtures::DESPESA_REFERENCE));
        $plano->setContaPagar($this->getReference(ContaPagarFixtures::NOTA_ENTRADA_REFERENCE));
        $plano->setComplemento('Produtos de limpeza');
        $plano->setNumeroSequencia(1);
        $plano->setValor($this->getReference(ContaPagarFixtures::NOTA_ENTRADA_REFERENCE)->getValorTotal());
        $manager->persist($plano);
        $manager->flush();
    }

    public function getDependencies()
    {
        return [
            ContaPagarFixtures::class,
            PlanoContaFixtures::class,
        ];
    }


}
