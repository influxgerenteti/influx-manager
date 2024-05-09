<?php

namespace App\DataFixtures;

use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\Persistence\ObjectManager;
use App\Entity\Principal\Interessado;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;

class InteressadoFixtures extends Fixture implements DependentFixtureInterface
{

    public const INTERESSADO1_REFERENCE = "interessado1";
    public const INTERESSADO2_REFERENCE = "interessado2";
    public const INTERESSADO3_REFERENCE = "interessado3";
    public const INTERESSADO4_REFERENCE = "interessado4";
    public const INTERESSADO5_REFERENCE = "interessado5";
    public const INTERESSADO6_REFERENCE = "interessado6";

    public function load(ObjectManager $manager)
    {
        $interessado1 = new Interessado();
        $interessado1->setFranqueada($this->getReference(FranqueadaFixtures::FRANQUEADA_REFERENCE));
        $interessado1->setConsultorFuncionario($this->getReference(FuncionarioFixtures::FUNCIONARIO6_REFERENCE));
        $interessado1->setConsultorResponsavelFuncionario($this->getReference(FuncionarioFixtures::FUNCIONARIO2_REFERENCE));
        $interessado1->setTelefoneContato("(99) 99999-9999");
        $interessado1->setSexo("F");
        $interessado1->setEmailContato("beatrizpatriciajessicapeixoto-96@psq.med.br");
        $interessado1->setTipoLead("R");
        $interessado1->setNome("Beatriz Patrícia Jéssica Peixoto");
        $interessado1->setTipoContato($this->getReference(TipoContatoFixtures::TIPO_CONTATO_REFERENCE));
        $manager->persist($interessado1);
        $this->addReference(self::INTERESSADO1_REFERENCE, $interessado1);

        $interessado2 = new Interessado();
        $interessado2->setFranqueada($this->getReference(FranqueadaFixtures::FRANQUEADA_REFERENCE));
        $interessado2->setConsultorFuncionario($this->getReference(FuncionarioFixtures::FUNCIONARIO6_REFERENCE));
        $interessado2->setConsultorResponsavelFuncionario($this->getReference(FuncionarioFixtures::FUNCIONARIO2_REFERENCE));
        $interessado2->setTelefoneContato("98984279079");
        $interessado2->setSexo("M");
        $interessado2->setEmailContato("eemanuelryandavimendes@paraisopolis.com.br");
        $interessado2->setTipoLead("R");
        $interessado2->setNome("Emanuel Ryan Davi Mendes");
        $interessado2->setTipoContato($this->getReference(TipoContatoFixtures::TIPO_CONTATO_REFERENCE));
        $manager->persist($interessado2);
        $this->addReference(self::INTERESSADO2_REFERENCE, $interessado2);

        $interessado3 = new Interessado();
        $interessado3->setFranqueada($this->getReference(FranqueadaFixtures::FRANQUEADA_REFERENCE));
        $interessado3->setConsultorFuncionario($this->getReference(FuncionarioFixtures::FUNCIONARIO6_REFERENCE));
        $interessado3->setConsultorResponsavelFuncionario($this->getReference(FuncionarioFixtures::FUNCIONARIO2_REFERENCE));
        $interessado3->setTelefoneContato("83985797361");
        $interessado3->setSexo("F");
        $interessado3->setEmailContato("hhelenaelianebeatrizdaluz@yahoo.com.br");
        $interessado3->setTipoLead("R");
        $interessado3->setNome("Helena Eliane Beatriz da Luz");
        $interessado3->setTipoContato($this->getReference(TipoContatoFixtures::TIPO_CONTATO_REFERENCE));
        $manager->persist($interessado3);
        $this->addReference(self::INTERESSADO3_REFERENCE, $interessado3);

        $interessado4 = new Interessado();
        $interessado4->setFranqueada($this->getReference(FranqueadaFixtures::FRANQUEADA_REFERENCE));
        $interessado4->setConsultorFuncionario($this->getReference(FuncionarioFixtures::FUNCIONARIO6_REFERENCE));
        $interessado4->setConsultorResponsavelFuncionario($this->getReference(FuncionarioFixtures::FUNCIONARIO2_REFERENCE));
        $interessado4->setTelefoneContato("59269231070");
        $interessado4->setSexo("F");
        $interessado4->setEmailContato("allana@yahoo.com.br");
        $interessado4->setTipoLead("R");
        $interessado4->setNome("Allana Tatiane Cláudia Gomes");
        $interessado4->setTipoContato($this->getReference(TipoContatoFixtures::TIPO_CONTATO_REFERENCE));
        $manager->persist($interessado4);
        $this->addReference(self::INTERESSADO4_REFERENCE, $interessado4);

        $interessado5 = new Interessado();
        $interessado5->setConsultorFuncionario($this->getReference(FuncionarioFixtures::FUNCIONARIO_REFERENCE));
        $interessado5->setConsultorResponsavelFuncionario($this->getReference(FuncionarioFixtures::FUNCIONARIO_REFERENCE));
        $interessado5->setWorkflow($this->getReference(WorkflowFixtures::WORKFLOW_CONTATO_INICIAL));
        $interessado5->setFranqueada($this->getReference(FranqueadaFixtures::FRANQUEADORA_REFERENCE));
        $interessado5->setNome("Anthony Enzo Isaac Drumond");
        $interessado5->setIdade(17);
        $interessado5->setEmailContato("aanthonyenzoisaacdrumond@gmail.com");
        $interessado5->setEmailSecundario("aanthonyenzoisaacdrumond@hotmail.com.br");
        $interessado5->setTelefoneContato("(99) 99999-9999");
        $interessado5->setSexo("M");
        $interessado5->setTipoLead("R");
        $interessado5->setTipoContato($this->getReference("tipo_contato_email"));

        $manager->persist($interessado5);
        $this->addReference(self::INTERESSADO5_REFERENCE, $interessado5);

        $interessado6 = new Interessado();
        $interessado6->setConsultorFuncionario($this->getReference(FuncionarioFixtures::FUNCIONARIO_REFERENCE));
        $interessado6->setWorkflow($this->getReference(WorkflowFixtures::WORKFLOW_CONTATO_INICIAL));
        $interessado6->setFranqueada($this->getReference(FranqueadaFixtures::FRANQUEADORA_REFERENCE));
        $interessado6->setNome("Aurora Stella Isabelly Rodrigues");
        $interessado6->setIdade(15);
        $interessado6->setEmailContato("aurorastellaisabellyrodrigues-96@gmail.com");
        $interessado6->setTelefoneContato("(47) 93333-2222");
        $interessado6->setSexo("F");
        $manager->persist($interessado6);
        $this->addReference(self::INTERESSADO6_REFERENCE, $interessado6);

        $manager->flush();
    }

    public function getDependencies()
    {
        return [
            FuncionarioFixtures::class,
            WorkflowFixtures::class,
            TipoProspeccaoFixtures::class,
            TipoContatoFixtures::class,
        ];
    }


}
