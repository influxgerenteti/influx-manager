<?php

namespace App\DataFixtures;

use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\Persistence\ObjectManager;
use App\Entity\Principal\TituloPagar;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;

class TituloPagarFixtures extends Fixture implements DependentFixtureInterface
{

    public const TITULO_PAGAR1_REFERENCE = "titulo_pagar1";
    public const TITULO_PAGAR2_REFERENCE = "titulo_pagar2";
    public const TITULO_PAGAR3_REFERENCE = "titulo_pagar3";
    public const TITULO_PAGAR4_REFERENCE = "titulo_pagar4";
    public const TITULO_PAGAR5_REFERENCE = "titulo_pagar5";

    public function load(ObjectManager $manager)
    {
        $data   = \DateTime::createFromFormat("Y-m-d\TH:i:s.uP", "2018-09-26T14:06:40.000Z");
        $config = new TituloPagar();
        $config->setConta($this->getReference(ContaFixtures::CONTA_REFERENCE));
        $config->setFavorecidoPessoa($this->getReference(PessoaFixtures::PESSOA2_REFERENCE));
        $config->setFranqueada($this->getReference(FranqueadaFixtures::FRANQUEADA_REFERENCE));
        $config->setContaPagar($this->getReference(ContaPagarFixtures::NOTA_ENTRADA_REFERENCE));
        $config->setFormaCobranca($this->getReference(FormaPagamentoFixtures::DINHEIRO_REFERENCE));
        $config->setDataVencimento($data);
        $config->setDataProrrogacao($data);
        $config->setNumeroParcelaDocumento(1);
        $config->setValorDocumento(225);
        $config->setValorSaldo(0);
        $config->setNarrativaPlanoConta("Vale-transporte");
        $config->setSituacao("LIQ");
        $manager->persist($config);
        $this->addReference(self::TITULO_PAGAR1_REFERENCE, $config);

        $data   = \DateTime::createFromFormat("Y-m-d\TH:i:s.uP", "2018-10-26T14:06:40.000Z");
        $config = new TituloPagar();
        $config->setConta($this->getReference(ContaFixtures::CONTA_REFERENCE));
        $config->setFavorecidoPessoa($this->getReference(PessoaFixtures::PESSOA2_REFERENCE));
        $config->setFranqueada($this->getReference(FranqueadaFixtures::FRANQUEADA_REFERENCE));
        $config->setContaPagar($this->getReference(ContaPagarFixtures::NOTA_ENTRADA_REFERENCE));
        $config->setFormaCobranca($this->getReference(FormaPagamentoFixtures::CHEQUE_REFERENCE));
        $config->setDataVencimento($data);
        $config->setDataProrrogacao($data);
        $config->setNumeroParcelaDocumento(2);
        $config->setValorDocumento(275);
        $config->setValorSaldo(275);
        $manager->persist($config);
        $this->addReference(self::TITULO_PAGAR2_REFERENCE, $config);

        $data   = \DateTime::createFromFormat("Y-m-d\TH:i:s.uP", "2018-11-25T14:06:40.000Z");
        $config = new TituloPagar();
        $config->setConta($this->getReference(ContaFixtures::CONTA_REFERENCE));
        $config->setFavorecidoPessoa($this->getReference(PessoaFixtures::PESSOA2_REFERENCE));
        $config->setFranqueada($this->getReference(FranqueadaFixtures::FRANQUEADA_REFERENCE));
        $config->setContaPagar($this->getReference(ContaPagarFixtures::NOTA_ENTRADA_REFERENCE));
        $config->setFormaCobranca($this->getReference(FormaPagamentoFixtures::CHEQUE_REFERENCE));
        $config->setDataVencimento($data);
        $config->setDataProrrogacao($data);
        $config->setNumeroParcelaDocumento(3);
        $config->setValorDocumento(225);
        $config->setValorSaldo(225);
        $manager->persist($config);
        $this->addReference(self::TITULO_PAGAR3_REFERENCE, $config);

        $data   = \DateTime::createFromFormat("Y-m-d\TH:i:s.uP", "2018-12-25T14:06:40.000Z");
        $config = new TituloPagar();
        $config->setConta($this->getReference(ContaFixtures::CONTA_REFERENCE));
        $config->setFavorecidoPessoa($this->getReference(PessoaFixtures::PESSOA2_REFERENCE));
        $config->setFranqueada($this->getReference(FranqueadaFixtures::FRANQUEADA_REFERENCE));
        $config->setContaPagar($this->getReference(ContaPagarFixtures::NOTA_ENTRADA_REFERENCE));
        $config->setFormaCobranca($this->getReference(FormaPagamentoFixtures::CHEQUE_REFERENCE));
        $config->setDataVencimento($data);
        $config->setDataProrrogacao($data);
        $config->setNumeroParcelaDocumento(4);
        $config->setValorDocumento(225);
        $config->setValorSaldo(225);
        $manager->persist($config);
        $this->addReference(self::TITULO_PAGAR4_REFERENCE, $config);

        $data   = \DateTime::createFromFormat("Y-m-d\TH:i:s.uP", "2019-01-24T14:06:40.000Z");
        $config = new TituloPagar();
        $config->setConta($this->getReference(ContaFixtures::CONTA_REFERENCE));
        $config->setFavorecidoPessoa($this->getReference(PessoaFixtures::PESSOA2_REFERENCE));
        $config->setFranqueada($this->getReference(FranqueadaFixtures::FRANQUEADA_REFERENCE));
        $config->setContaPagar($this->getReference(ContaPagarFixtures::NOTA_ENTRADA_REFERENCE));
        $config->setFormaCobranca($this->getReference(FormaPagamentoFixtures::CHEQUE_REFERENCE));
        $config->setDataVencimento($data);
        $config->setDataProrrogacao($data);
        $config->setNumeroParcelaDocumento(5);
        $config->setValorDocumento(225);
        $config->setValorSaldo(225);
        $manager->persist($config);
        $this->addReference(self::TITULO_PAGAR5_REFERENCE, $config);

        $manager->flush();

    }

    public function getDependencies()
    {
        return [
            ContaFixtures::class,
            FranqueadaFixtures::class,
            PessoaFixtures::class,
            ContaPagarFixtures::class,
            FormaPagamentoFixtures::class,
        ];
    }


}
