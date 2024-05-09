<?php

namespace App\DataFixtures;

use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\Persistence\ObjectManager;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use App\Entity\Principal\SistemaAvaliacao;

class SistemaAvaliacaoFixtures extends Fixture implements DependentFixtureInterface
{
    public const SISTEMA_AVALIACAO_REFERENCE = "sistema_avaliacao";

    public function load(ObjectManager $manager)
    {
        $config = new SistemaAvaliacao();
        $config->setConceitoAprovacao($this->getReference(ConceitoAvaliacaoFixtures::CONCEITO1_REFERENCE));
        $config->setConceitoCorteCompromissoQualidade($this->getReference(ConceitoAvaliacaoFixtures::CONCEITO2_REFERENCE));
        $config->setDescricao("Sistema Avaliacao teste unitario");
        $config->setFrequenciaCorteCompromissoQualidade(100.0);
        $config->setFrequenciaMinima(70.0);
        $config->setNotaAprovacao(7.0);
        $config->setNotaCorteCompromissoQualidade(8.0);
        $manager->persist($config);

        $config = new SistemaAvaliacao();
        $config->setConceitoAprovacao($this->getReference(ConceitoAvaliacaoFixtures::CONCEITO_INFLUX_REF_B_NEG));
        $config->setConceitoCorteCompromissoQualidade($this->getReference(ConceitoAvaliacaoFixtures::CONCEITO_INFLUX_REF_B_POS));
        $config->setDescricao("Padrão inFlux");
        $config->setFrequenciaCorteCompromissoQualidade(85.0);
        $config->setFrequenciaMinima(75.0);
        $config->setNotaAprovacao(75);
        $config->setNotaCorteCompromissoQualidade(85);
        $manager->persist($config);

        $manager->flush();
        $this->addReference(self::SISTEMA_AVALIACAO_REFERENCE, $config);

    }

    public function getDependencies()
    {
        return [
            ConceitoAvaliacaoFixtures::class,
        ];
    }


}
