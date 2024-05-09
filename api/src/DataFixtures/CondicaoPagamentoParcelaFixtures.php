<?php

namespace App\DataFixtures;

use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\Persistence\ObjectManager;
use App\Entity\Principal\CondicaoPagamentoParcela;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;

class CondicaoPagamentoParcelaFixtures extends Fixture implements DependentFixtureInterface
{


    public function load(ObjectManager $manager)
    {
        $condicaoPagamentoParcela = new CondicaoPagamentoParcela();
        $condicaoPagamentoParcela->setCondicaoPagamento($this->getReference(CondicaoPagamentoFixtures::CONDICAO_PAGAMENTO_REFERENCE));
        $condicaoPagamentoParcela->setNumeroParcela(1);
        $condicaoPagamentoParcela->setDiasVencimento(30);
        $condicaoPagamentoParcela->setPercentualParcela(30);
        $manager->persist($condicaoPagamentoParcela);

        $condicaoPagamentoParcela = new CondicaoPagamentoParcela();
        $condicaoPagamentoParcela->setCondicaoPagamento($this->getReference(CondicaoPagamentoFixtures::CONDICAO_PAGAMENTO_REFERENCE));
        $condicaoPagamentoParcela->setNumeroParcela(2);
        $condicaoPagamentoParcela->setDiasVencimento(60);
        $condicaoPagamentoParcela->setPercentualParcela(25);
        $manager->persist($condicaoPagamentoParcela);

        $condicaoPagamentoParcela = new CondicaoPagamentoParcela();
        $condicaoPagamentoParcela->setCondicaoPagamento($this->getReference(CondicaoPagamentoFixtures::CONDICAO_PAGAMENTO_REFERENCE));
        $condicaoPagamentoParcela->setNumeroParcela(3);
        $condicaoPagamentoParcela->setDiasVencimento(90);
        $condicaoPagamentoParcela->setPercentualParcela(15);
        $manager->persist($condicaoPagamentoParcela);

        $condicaoPagamentoParcela = new CondicaoPagamentoParcela();
        $condicaoPagamentoParcela->setCondicaoPagamento($this->getReference(CondicaoPagamentoFixtures::CONDICAO_PAGAMENTO_REFERENCE));
        $condicaoPagamentoParcela->setNumeroParcela(4);
        $condicaoPagamentoParcela->setDiasVencimento(120);
        $condicaoPagamentoParcela->setPercentualParcela(15);
        $manager->persist($condicaoPagamentoParcela);

        $condicaoPagamentoParcela = new CondicaoPagamentoParcela();
        $condicaoPagamentoParcela->setCondicaoPagamento($this->getReference(CondicaoPagamentoFixtures::CONDICAO_PAGAMENTO_REFERENCE));
        $condicaoPagamentoParcela->setNumeroParcela(5);
        $condicaoPagamentoParcela->setDiasVencimento(150);
        $condicaoPagamentoParcela->setPercentualParcela(15);
        $manager->persist($condicaoPagamentoParcela);

        $condicaoPagamentoParcela = new CondicaoPagamentoParcela();
        $condicaoPagamentoParcela->setCondicaoPagamento($this->getReference(CondicaoPagamentoFixtures::CONDICAO_PAGAMENTO_REFERENCE_2));
        $condicaoPagamentoParcela->setNumeroParcela(1);
        $condicaoPagamentoParcela->setDiasVencimento(0);
        $condicaoPagamentoParcela->setPercentualParcela(100);
        $manager->persist($condicaoPagamentoParcela);

        $condicaoPagamentoParcela = new CondicaoPagamentoParcela();
        $condicaoPagamentoParcela->setCondicaoPagamento($this->getReference(CondicaoPagamentoFixtures::CONDICAO_PAGAMENTO_REFERENCE_3));
        $condicaoPagamentoParcela->setNumeroParcela(1);
        $condicaoPagamentoParcela->setDiasVencimento(30);
        $condicaoPagamentoParcela->setPercentualParcela(100);
        $manager->persist($condicaoPagamentoParcela);

        $condicaoPagamentoParcela = new CondicaoPagamentoParcela();
        $condicaoPagamentoParcela->setCondicaoPagamento($this->getReference(CondicaoPagamentoFixtures::CONDICAO_PAGAMENTO_REFERENCE_4));
        $condicaoPagamentoParcela->setNumeroParcela(1);
        $condicaoPagamentoParcela->setDiasVencimento(0);
        $condicaoPagamentoParcela->setPercentualParcela(50);
        $manager->persist($condicaoPagamentoParcela);

        $condicaoPagamentoParcela = new CondicaoPagamentoParcela();
        $condicaoPagamentoParcela->setCondicaoPagamento($this->getReference(CondicaoPagamentoFixtures::CONDICAO_PAGAMENTO_REFERENCE_4));
        $condicaoPagamentoParcela->setNumeroParcela(2);
        $condicaoPagamentoParcela->setDiasVencimento(30);
        $condicaoPagamentoParcela->setPercentualParcela(50);
        $manager->persist($condicaoPagamentoParcela);

        $manager->flush();

    }

    public function getDependencies()
    {
        return [
            CondicaoPagamentoFixtures::class,
        ];
    }


}
