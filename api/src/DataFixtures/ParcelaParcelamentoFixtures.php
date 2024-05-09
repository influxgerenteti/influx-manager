<?php

namespace App\DataFixtures;

use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\Persistence\ObjectManager;
use App\Entity\Principal\ParcelaParcelamento;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;

class ParcelaParcelamentoFixtures extends Fixture implements DependentFixtureInterface
{

    public const PARCELA_PARCELAMENTO_REFERENCE  = "parcela_parcelamento";
    public const PARCELA_PARCELAMENTO2_REFERENCE = "parcela_parcelamento2";
    public const PARCELA_PARCELAMENTO3_REFERENCE = "parcela_parcelamento3";
    public const PARCELA_PARCELAMENTO4_REFERENCE = "parcela_parcelamento4";

    public function load(ObjectManager $manager)
    {

        $parcela_parcelamento = new ParcelaParcelamento();
        $parcela_parcelamento->setParcelamentoOperadoraCartao($this->getReference(ParcelamentoOperadoraCartaoFixtures::PARCELAMENTO_OPERADORA_CREDITO_REFERENCE));
        $parcela_parcelamento->setNumeroParcela("1");
        $parcela_parcelamento->setDiasRepasse("30");
        $parcela_parcelamento->setTaxa("5");
        $manager->persist($parcela_parcelamento);
        $this->addReference(self::PARCELA_PARCELAMENTO_REFERENCE, $parcela_parcelamento);

        $parcela_parcelamento2 = new ParcelaParcelamento();
        $parcela_parcelamento2->setParcelamentoOperadoraCartao($this->getReference(ParcelamentoOperadoraCartaoFixtures::PARCELAMENTO_OPERADORA_DEBITO_REFERENCE));
        $parcela_parcelamento2->setNumeroParcela("1");
        $parcela_parcelamento2->setDiasRepasse("30");
        $parcela_parcelamento2->setTaxa("5");
        $manager->persist($parcela_parcelamento2);
        $this->addReference(self::PARCELA_PARCELAMENTO2_REFERENCE, $parcela_parcelamento2);

        $parcela_parcelamento3 = new ParcelaParcelamento();
        $parcela_parcelamento3->setParcelamentoOperadoraCartao($this->getReference(ParcelamentoOperadoraCartaoFixtures::PARCELAMENTO_OPERADORA_CREDITO2_REFERENCE));
        $parcela_parcelamento3->setNumeroParcela("1");
        $parcela_parcelamento3->setDiasRepasse("30");
        $parcela_parcelamento3->setTaxa("5");
        $manager->persist($parcela_parcelamento3);
        $this->addReference(self::PARCELA_PARCELAMENTO3_REFERENCE, $parcela_parcelamento3);

        $parcela_parcelamento4 = new ParcelaParcelamento();
        $parcela_parcelamento4->setParcelamentoOperadoraCartao($this->getReference(ParcelamentoOperadoraCartaoFixtures::PARCELAMENTO_OPERADORA_DEBITO2_REFERENCE));
        $parcela_parcelamento4->setNumeroParcela("1");
        $parcela_parcelamento4->setDiasRepasse("30");
        $parcela_parcelamento4->setTaxa("5");
        $manager->persist($parcela_parcelamento4);
        $this->addReference(self::PARCELA_PARCELAMENTO4_REFERENCE, $parcela_parcelamento4);

        $manager->flush();
    }

    public function getDependencies()
    {
        return [
            ParcelamentoOperadoraCartaoFixtures::class,
        ];
    }


}
