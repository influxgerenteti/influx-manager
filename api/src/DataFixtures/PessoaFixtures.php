<?php

namespace App\DataFixtures;

use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\Persistence\ObjectManager;
use App\Entity\Principal\Pessoa;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;

class PessoaFixtures extends Fixture implements DependentFixtureInterface
{
    public const PESSOA_REFERENCE   = "pessoa";
    public const PESSOA2_REFERENCE  = "pessoa2";
    public const PESSOA3_REFERENCE  = "pessoa3";
    public const PESSOA4_REFERENCE  = "pessoa4";
    public const PESSOA5_REFERENCE  = "pessoa5";
    public const PESSOA6_REFERENCE  = "pessoa6";
    public const PESSOA7_REFERENCE  = "pessoa7";
    public const PESSOA8_REFERENCE  = "pessoa8";
    public const PESSOA9_REFERENCE  = "pessoa9";
    public const PESSOA10_REFERENCE = "pessoa10";
    public const PESSOA11_REFERENCE = "pessoa11";
    public const PESSOA12_REFERENCE = "pessoa12";
    public const PESSOA13_REFERENCE = "pessoa13";
    public const PESSOA14_REFERENCE = "pessoa14";


    public function load(ObjectManager $manager)
    {
        $pessoa = new Pessoa();
        $pessoa->setNomeContato("Marcos da Silva");
        $pessoa->setCnpjCpf("59687664258");
        $pessoa->setTelefonePreferencial("47991234156");
        $pessoa->addFranqueada($this->getReference(FranqueadaFixtures::FRANQUEADORA_REFERENCE));
        $data = \DateTime::createFromFormat("Y-m-d\TH:i:s.uP", "1977-01-13T14:06:40.000Z");
        $pessoa->setDataNascimento($data);
        $manager->persist($pessoa);

        $pessoa2 = new Pessoa();
        $pessoa2->setNomeContato("Erick Thales Ramos");
        $pessoa2->setSexo('M');
        $pessoa2->setCepEndereco('60873010');
        $pessoa2->setEndereco('Rua Maria Quintino');
        $pessoa2->setBairroEndereco('Parque Santa Maria');
        $pessoa2->setNumeroEndereco('470');
        $pessoa2->setCnpjCpf("52640405837");
        $pessoa2->setTelefonePreferencial("85981371886");
        $pessoa2->setEmailPreferencial("erick@influx.com.br");
        $pessoa2->setTipoPessoa('F');
        $pessoa2->addFranqueada($this->getReference(FranqueadaFixtures::FRANQUEADA_REFERENCE));
        $data = \DateTime::createFromFormat("Y-m-d\TH:i:s.uP", "1985-11-19T14:06:40.000Z");
        $pessoa2->setDataNascimento($data);
        $manager->persist($pessoa2);

        $pessoa3 = new Pessoa();
        $pessoa3->setNomeContato("Luzia Sônia Nascimento");
        $pessoa3->setSexo('F');
        $pessoa3->setCepEndereco('69311062');
        $pessoa3->setNumeroEndereco('127');
        $pessoa3->setCnpjCpf("92409833179");
        $pessoa3->setTelefonePreferencial("95987658050");
        $pessoa3->setEmailPreferencial("luzianascimento@gmail.com");
        $pessoa3->setTipoPessoa('F');
        $pessoa3->addFranqueada($this->getReference(FranqueadaFixtures::FRANQUEADA_REFERENCE));
        $data = \DateTime::createFromFormat("Y-m-d\TH:i:s.uP", "1976-04-25T14:06:40.000Z");
        $pessoa3->setDataNascimento($data);
        $manager->persist($pessoa3);

        $pessoa4 = new Pessoa();
        $pessoa4->setNomeContato("Raimunda Rafaela Rezende");
        $pessoa4->setSexo('F');
        $pessoa4->setCepEndereco('54260450');
        $pessoa4->setNumeroEndereco('827');
        $pessoa4->setCnpjCpf("15109134596");
        $pessoa4->setTelefonePreferencial("81997235705");
        $pessoa4->setEmailPreferencial("raimundarafaela@gmail.com");
        $pessoa4->setTipoPessoa('F');
        $pessoa4->addFranqueada($this->getReference(FranqueadaFixtures::FRANQUEADA_REFERENCE));
        $data = \DateTime::createFromFormat("Y-m-d\TH:i:s.uP", "1985-11-19T14:06:40.000Z");
        $pessoa4->setDataNascimento($data);
        $manager->persist($pessoa4);

        $pessoa5 = new Pessoa();
        $pessoa5->setNomeContato("Daiane Antonella Eliane Almada");
        $pessoa5->setSexo('F');
        $pessoa5->setCepEndereco('21331490');
        $pessoa5->setNumeroEndereco('799');
        $pessoa5->setCnpjCpf("30957383665");
        $pessoa5->setTelefonePreferencial("21994246894");
        $pessoa5->setEmailPreferencial("daianealmada@gmail.com");
        $pessoa5->setTipoPessoa('F');
        $pessoa5->addFranqueada($this->getReference(FranqueadaFixtures::FRANQUEADA_REFERENCE));
        $data = \DateTime::createFromFormat("Y-m-d\TH:i:s.uP", "1990-12-03T14:06:40.000Z");
        $pessoa5->setDataNascimento($data);
        $manager->persist($pessoa5);

        $pessoa6 = new Pessoa();
        $pessoa6->setNomeContato("Lucas Márcio Mateus Galvão");
        $pessoa6->setSexo('M');
        $pessoa6->setCepEndereco('81730160');
        $pessoa6->setNumeroEndereco('695');
        $pessoa6->setCnpjCpf("87538846808");
        $pessoa6->setTelefonePreferencial("41986317280");
        $pessoa6->setEmailPreferencial("lucasmarciomateusgalvao@gmail.com");
        $pessoa6->setTipoPessoa('F');
        $pessoa6->addFranqueada($this->getReference(FranqueadaFixtures::FRANQUEADA_REFERENCE));
        $data = \DateTime::createFromFormat("Y-m-d\TH:i:s.uP", "1992-11-08T14:06:40.000Z");
        $pessoa6->setDataNascimento($data);
        $manager->persist($pessoa6);

        $data    = \DateTime::createFromFormat("Y-m-d\TH:i:s.uP", "1992-03-01T14:06:40.000Z");
        $pessoa7 = new Pessoa();
        $pessoa7->setNomeContato("Beatriz Patrícia Jéssica Peixoto");
        $pessoa7->setSexo('F');
        $pessoa7->setCepEndereco('76824684');
        $pessoa7->setNumeroEndereco('791');
        $pessoa7->setCnpjCpf("14703913763");
        $pessoa7->setTelefonePreferencial("63991878765");
        $pessoa7->setEmailPreferencial("beatrizpatriciajessicapeixoto@med.br");
        $pessoa7->setTipoPessoa('F');
        $pessoa7->addFranqueada($this->getReference(FranqueadaFixtures::FRANQUEADA_REFERENCE));
        $data = \DateTime::createFromFormat("Y-m-d\TH:i:s.uP", "2002-12-11T14:06:40.000Z");
        $pessoa7->setDataNascimento($data);
        $manager->persist($pessoa7);

        $pessoa8 = new Pessoa();
        $pessoa8->setNomeContato("Emanuel Ryan Davi Mendes");
        $pessoa8->setSexo('M');
        $pessoa8->setCepEndereco('77006394');
        $pessoa8->setNumeroEndereco('189');
        $pessoa8->setCnpjCpf("73902241500");
        $pessoa8->setTelefonePreferencial("63993649285");
        $pessoa8->setEmailPreferencial("eemanuelryandavimendes@paraisopolis.com.br");
        $pessoa8->setTipoPessoa('F');
        $pessoa8->addFranqueada($this->getReference(FranqueadaFixtures::FRANQUEADA_REFERENCE));
        $data = \DateTime::createFromFormat("Y-m-d\TH:i:s.uP", "1993-03-29T14:06:40.000Z");
        $pessoa8->setDataNascimento($data);
        $manager->persist($pessoa8);

        $pessoa9 = new Pessoa();
        $pessoa9->setNomeContato("Helena Eliane Beatriz da Luz");
        $pessoa9->setSexo('F');
        $pessoa9->setCepEndereco('68901470');
        $pessoa9->setNumeroEndereco('343');
        $pessoa9->setCnpjCpf("07289317530");
        $pessoa9->setTelefonePreferencial("96983063070");
        $pessoa9->setEmailPreferencial("hhelenaelianebeatrizdaluz@yahoo.com.br");
        $pessoa9->setTipoPessoa('F');
        $pessoa9->addFranqueada($this->getReference(FranqueadaFixtures::FRANQUEADA_REFERENCE));
        $data = \DateTime::createFromFormat("Y-m-d\TH:i:s.uP", "1995-10-17T14:06:40.000Z");
        $pessoa9->setDataNascimento($data);
        $manager->persist($pessoa9);

        $pessoa10 = new Pessoa();
        $pessoa10->setNomeContato("Allana Tatiane Cláudia Gomes");
        $pessoa10->setSexo('F');
        $pessoa10->setCepEndereco('69400110');
        $pessoa10->setNumeroEndereco('212');
        $pessoa10->setCnpjCpf("97891528683");
        $pessoa10->setTelefonePreferencial("92981985710");
        $pessoa10->setEmailPreferencial("allanatatianeclaudiagomes@oxiteno.com");
        $pessoa10->setTipoPessoa('F');
        $pessoa10->addFranqueada($this->getReference(FranqueadaFixtures::FRANQUEADA_REFERENCE));
        $data = \DateTime::createFromFormat("Y-m-d\TH:i:s.uP", "1991-01-22T14:06:40.000Z");
        $pessoa10->setDataNascimento($data);
        $manager->persist($pessoa10);

        $pessoa11 = new Pessoa();
        $pessoa11->setNomeContato("GATI");
        $pessoa11->setRazaoSocial("GATI labs.");
        $pessoa11->setNomeFantasia("GATI");
        $pessoa11->setTipoPessoa('J');
        $pessoa11->setCnpjCpf("29289173000153");
        $pessoa11->setTelefonePreferencial("4733055408");
        $pessoa11->addFranqueada($this->getReference(FranqueadaFixtures::FRANQUEADORA_REFERENCE));
        $data = \DateTime::createFromFormat("Y-m-d\TH:i:s.uP", "2018-01-25T14:06:40.000Z");
        $pessoa11->setDataNascimento($data);
        $manager->persist($pessoa11);

        $pessoa12 = new Pessoa();
        $pessoa12->setNomeContato("Simone e Lívia Gráfica Ltda");
        $pessoa12->setRazaoSocial("Simone e Lívia Gráfica Ltda");
        $pessoa12->setNomeFantasia("Simone e Lívia Gráfica");
        $pessoa12->setTipoPessoa('J');
        $pessoa12->setCnpjCpf("50038974000132");
        $pessoa12->setTelefonePreferencial("16984247144");
        $pessoa12->addFranqueada($this->getReference(FranqueadaFixtures::FRANQUEADA_REFERENCE));
        $data = \DateTime::createFromFormat("Y-m-d\TH:i:s.uP", "1995-03-21T14:06:40.000Z");
        $pessoa12->setDataNascimento($data);
        $manager->persist($pessoa12);

        $pessoa13 = new Pessoa();
        $pessoa13->setNomeContato("Elias e Davi Informática Ltda");
        $pessoa13->setRazaoSocial("Elias e Davi Informática Ltda");
        $pessoa13->setNomeFantasia("Elias e Davi Informática");
        $pessoa13->setTipoPessoa('J');
        $pessoa13->setCnpjCpf("33968946000103");
        $pessoa13->setTelefonePreferencial("4837965689");
        $pessoa13->addFranqueada($this->getReference(FranqueadaFixtures::FRANQUEADA_REFERENCE));
        $data = \DateTime::createFromFormat("Y-m-d\TH:i:s.uP", "1983-09-19T14:06:40.000Z");
        $pessoa13->setDataNascimento($data);
        $manager->persist($pessoa13);

        $pessoa14 = new Pessoa();
        $pessoa14->setNomeContato("Marcelo Paulo Otávio Castro");
        $pessoa14->setTipoPessoa('F');
        $pessoa14->setCnpjCpf("47818480790");
        $pessoa14->setTelefonePreferencial("8337332078");
        $pessoa14->addFranqueada($this->getReference(FranqueadaFixtures::FRANQUEADA_REFERENCE));
        $data = \DateTime::createFromFormat("Y-m-d\TH:i:s.uP", "1991-02-24T14:06:40.000Z");
        $pessoa14->setDataNascimento($data);
        $manager->persist($pessoa14);

        $this->addReference(self::PESSOA_REFERENCE, $pessoa);
        $this->addReference(self::PESSOA2_REFERENCE, $pessoa2);
        $this->addReference(self::PESSOA3_REFERENCE, $pessoa3);
        $this->addReference(self::PESSOA4_REFERENCE, $pessoa4);
        $this->addReference(self::PESSOA5_REFERENCE, $pessoa5);
        $this->addReference(self::PESSOA6_REFERENCE, $pessoa6);
        $this->addReference(self::PESSOA7_REFERENCE, $pessoa7);
        $this->addReference(self::PESSOA8_REFERENCE, $pessoa8);
        $this->addReference(self::PESSOA9_REFERENCE, $pessoa9);
        $this->addReference(self::PESSOA10_REFERENCE, $pessoa10);
        $this->addReference(self::PESSOA11_REFERENCE, $pessoa11);
        $this->addReference(self::PESSOA12_REFERENCE, $pessoa12);
        $this->addReference(self::PESSOA13_REFERENCE, $pessoa13);
        $this->addReference(self::PESSOA14_REFERENCE, $pessoa14);

        $manager->flush();
    }

    public function getDependencies()
    {
        return [
            FranqueadaFixtures::class,
        ];
    }


}
