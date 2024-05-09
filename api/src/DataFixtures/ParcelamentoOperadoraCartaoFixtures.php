<?php

namespace App\DataFixtures;

use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\Persistence\ObjectManager;
use App\Entity\Principal\ParcelamentoOperadoraCartao;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;

class ParcelamentoOperadoraCartaoFixtures extends Fixture implements DependentFixtureInterface
{

    public const PARCELAMENTO_OPERADORA_CREDITO_REFERENCE  = "parcelamento_operadora_credito";
    public const PARCELAMENTO_OPERADORA_DEBITO_REFERENCE   = "parcelamento_operadora_debito";
    public const PARCELAMENTO_OPERADORA_CREDITO2_REFERENCE = "parcelamento_operadora_credito2";
    public const PARCELAMENTO_OPERADORA_DEBITO2_REFERENCE  = "parcelamento_operadora_debito2";

    public function load(ObjectManager $manager)
    {
        $franqueada   = $this->getReference(FranqueadaFixtures::FRANQUEADA_REFERENCE);
        $franqueadora = $this->getReference(FranqueadaFixtures::FRANQUEADORA_REFERENCE);

        $parcelamento_operadora_credito = new ParcelamentoOperadoraCartao();
        $parcelamento_operadora_credito->setOperadoraCartao($this->getReference(OperadoraCartaoFixtures::OPERADORA_CREDITO_REFERENCE));
        $parcelamento_operadora_credito->setPlanoConta($this->getReference(PlanoContaFixtures::TAXA_BANCO_REFERENCE));
        $parcelamento_operadora_credito->setDescricao("1x em 30 dia(s)");
        $manager->persist($parcelamento_operadora_credito);
        $this->addReference(self::PARCELAMENTO_OPERADORA_CREDITO_REFERENCE, $parcelamento_operadora_credito);

        $parcelamento_operadora_debito = new ParcelamentoOperadoraCartao();
        $parcelamento_operadora_debito->setOperadoraCartao($this->getReference(OperadoraCartaoFixtures::OPERADORA_DEBITO_REFERENCE));
        $parcelamento_operadora_debito->setPlanoConta($this->getReference(PlanoContaFixtures::TAXA_BANCO_REFERENCE));
        $parcelamento_operadora_debito->setDescricao("1x em 30 dia(s)");
        $manager->persist($parcelamento_operadora_debito);
        $this->addReference(self::PARCELAMENTO_OPERADORA_DEBITO_REFERENCE, $parcelamento_operadora_debito);

        $parcelamento_operadora_credito2 = new ParcelamentoOperadoraCartao();
        $parcelamento_operadora_credito2->setOperadoraCartao($this->getReference(OperadoraCartaoFixtures::OPERADORA_CREDITO2_REFERENCE));
        $parcelamento_operadora_credito2->setPlanoConta($this->getReference(PlanoContaFixtures::TAXA_BANCO_REFERENCE));
        $parcelamento_operadora_credito2->setDescricao("1x em 30 dia(s)");
        $manager->persist($parcelamento_operadora_credito2);
        $this->addReference(self::PARCELAMENTO_OPERADORA_CREDITO2_REFERENCE, $parcelamento_operadora_credito2);

        $parcelamento_operadora_debito2 = new ParcelamentoOperadoraCartao();
        $parcelamento_operadora_debito2->setOperadoraCartao($this->getReference(OperadoraCartaoFixtures::OPERADORA_DEBITO2_REFERENCE));
        $parcelamento_operadora_debito2->setPlanoConta($this->getReference(PlanoContaFixtures::TAXA_BANCO_REFERENCE));
        $parcelamento_operadora_debito2->setDescricao("1x em 30 dia(s)");
        $manager->persist($parcelamento_operadora_debito2);
        $this->addReference(self::PARCELAMENTO_OPERADORA_DEBITO2_REFERENCE, $parcelamento_operadora_debito2);

        $manager->flush();

    }

    public function getDependencies()
    {
        return [
            OperadoraCartaoFixtures::class,
            PlanoContaFixtures::class,
        ];
    }


}
