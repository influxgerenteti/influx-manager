<?php

namespace App\DataFixtures;

use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\Persistence\ObjectManager;
use App\Entity\Principal\OperadoraCartao;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;

class OperadoraCartaoFixtures extends Fixture implements DependentFixtureInterface
{

    public const OPERADORA_CREDITO_REFERENCE  = "operadora_credito";
    public const OPERADORA_DEBITO_REFERENCE   = "operadora_debito";
    public const OPERADORA_CREDITO2_REFERENCE = "operadora_credito2";
    public const OPERADORA_DEBITO2_REFERENCE  = "operadora_debito2";

    public function load(ObjectManager $manager)
    {

        $operadora_credito = new OperadoraCartao();
        $operadora_credito->setFranqueada($this->getReference(FranqueadaFixtures::FRANQUEADA_REFERENCE));
        $operadora_credito->setDescricao("Stone");
        $operadora_credito->setTipoOperacao("C");
        $operadora_credito->setSituacao("A");
        $manager->persist($operadora_credito);
        $this->addReference(self::OPERADORA_CREDITO_REFERENCE, $operadora_credito);

        $operadora_debito = new OperadoraCartao();
        $operadora_debito->setFranqueada($this->getReference(FranqueadaFixtures::FRANQUEADA_REFERENCE));
        $operadora_debito->setDescricao("MercadoPago");
        $operadora_debito->setTipoOperacao("D");
        $operadora_debito->setSituacao("A");
        $manager->persist($operadora_debito);
        $this->addReference(self::OPERADORA_DEBITO_REFERENCE, $operadora_debito);

        $operadora_credito2 = new OperadoraCartao();
        $operadora_credito2->setFranqueada($this->getReference(FranqueadaFixtures::FRANQUEADORA_REFERENCE));
        $operadora_credito2->setDescricao("Stone");
        $operadora_credito2->setTipoOperacao("C");
        $operadora_credito2->setSituacao("A");
        $manager->persist($operadora_credito2);
        $this->addReference(self::OPERADORA_CREDITO2_REFERENCE, $operadora_credito2);

        $operadora_debito2 = new OperadoraCartao();
        $operadora_debito2->setFranqueada($this->getReference(FranqueadaFixtures::FRANQUEADORA_REFERENCE));
        $operadora_debito2->setDescricao("MercadoPago");
        $operadora_debito2->setTipoOperacao("D");
        $operadora_debito2->setSituacao("A");
        $manager->persist($operadora_debito2);
        $this->addReference(self::OPERADORA_DEBITO2_REFERENCE, $operadora_debito2);

        $manager->flush();
    }

    public function getDependencies()
    {
        return [FranqueadaFixtures::class];
    }


}
