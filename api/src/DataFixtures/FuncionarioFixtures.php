<?php

namespace App\DataFixtures;

use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\Persistence\ObjectManager;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use App\Entity\Principal\Funcionario;

class FuncionarioFixtures extends Fixture implements DependentFixtureInterface
{
    public const FUNCIONARIO_REFERENCE  = "funcionario";
    public const FUNCIONARIO2_REFERENCE = "funcionario2";
    public const FUNCIONARIO3_REFERENCE = "funcionario3";
    public const FUNCIONARIO4_REFERENCE = "funcionario4";
    public const FUNCIONARIO5_REFERENCE = "funcionario5";
    public const FUNCIONARIO6_REFERENCE = "funcionario6";

    public function load(ObjectManager $manager)
    {
        $funcionario = new Funcionario();
        $funcionario->setPessoa($this->getReference(PessoaFixtures::PESSOA_REFERENCE));
        $funcionario->setFranqueada($this->getReference(FranqueadaFixtures::FRANQUEADORA_REFERENCE));
        $funcionario->setCargo($this->getReference(CargoFixtures::CARGO_PROFESSOR_REFERENCE));
        $funcionario->setBanco($this->getReference(BancoFixtures::BANCO_REFERENCE));
        $funcionario->setApelido("Marcos");
        $funcionario->setTipoPagamento("H");
        $funcionario->setInstrutor(1);
        $funcionario->setConsultor(1);
        $funcionario->setAtendente(1);
        $funcionario->setNivelInstrutor($this->getReference((NivelInstrutorFixtures::NIVEL_INSTRUTOR_REFERENCE)));
        $funcionario->setUsuario($this->getReference(UsuarioFixtures::USUARIO_REFERENCE));
        $manager->persist($funcionario);

        $funcionario2 = new Funcionario();
        $funcionario2->setPessoa($this->getReference(PessoaFixtures::PESSOA2_REFERENCE));
        $funcionario2->setFranqueada($this->getReference(FranqueadaFixtures::FRANQUEADA_REFERENCE));
        $funcionario2->setCargo($this->getReference(CargoFixtures::CARGO_GERENTE_REFERENCE));
        $funcionario2->setBanco($this->getReference(BancoFixtures::BANCO_REFERENCE));
        $funcionario2->setApelido("Erick");
        $funcionario2->setGestorComercial(1);
        $funcionario2->setConsultor(1);
        $funcionario2->setAtendente(1);
        $funcionario2->setRecebeEmails(1);
        $funcionario2->setTipoPagamento("M");
        $funcionario2->setUsuario($this->getReference(UsuarioFixtures::USUARIO_ERICK_REFERENCE));
        $manager->persist($funcionario2);

        $funcionario3 = new Funcionario();
        $funcionario3->setPessoa($this->getReference(PessoaFixtures::PESSOA3_REFERENCE));
        $funcionario3->setFranqueada($this->getReference(FranqueadaFixtures::FRANQUEADA_REFERENCE));
        $funcionario3->setCargo($this->getReference(CargoFixtures::CARGO_CONSULTOR_VENDAS_REFERENCE));
        $funcionario3->setBanco($this->getReference(BancoFixtures::BANCO_REFERENCE));
        $funcionario3->setAgencia("0123");
        $funcionario3->setDigitoAgencia("0");
        $funcionario3->setContaCorrente("555");
        $funcionario3->setDigitoContaCorrente("9");
        $funcionario3->setApelido("Lulu");
        $funcionario3->setTipoPagamento("M");
        $funcionario3->setGestorComercial(1);
        $funcionario3->setConsultor(1);
        $funcionario3->setGestorComercialFuncionario($funcionario2);
        $funcionario3->setUsuario($this->getReference(UsuarioFixtures::USUARIO_LUZIA_REFERENCE));
        $manager->persist($funcionario3);

        $funcionario4 = new Funcionario();
        $funcionario4->setPessoa($this->getReference(PessoaFixtures::PESSOA4_REFERENCE));
        $funcionario4->setFranqueada($this->getReference(FranqueadaFixtures::FRANQUEADA_REFERENCE));
        $funcionario4->setCargo($this->getReference(CargoFixtures::CARGO_CONSULTOR_VENDAS_REFERENCE));
        $funcionario4->setBanco($this->getReference(BancoFixtures::BANCO_REFERENCE));
        $funcionario4->setApelido("Raimunda");
        $funcionario4->setTipoPagamento("M");
        $funcionario4->setConsultor(0);
        $funcionario4->setGestorComercial(0);
        $funcionario4->setAtendente(0);
        $funcionario4->setRecebeEmails(0);
        $funcionario4->setGestorComercialFuncionario($funcionario2);
        $funcionario4->setUsuario($this->getReference(UsuarioFixtures::USUARIO_RAIMUNDA_REFERENCE));
        $manager->persist($funcionario4);

        $funcionario5 = new Funcionario();
        $funcionario5->setPessoa($this->getReference(PessoaFixtures::PESSOA5_REFERENCE));
        $funcionario5->setFranqueada($this->getReference(FranqueadaFixtures::FRANQUEADA_REFERENCE));
        $funcionario5->setCargo($this->getReference(CargoFixtures::CARGO_PROFESSOR_REFERENCE));
        $funcionario5->setBanco($this->getReference(BancoFixtures::BANCO_REFERENCE));
        $funcionario5->setApelido("Dai");
        $funcionario5->setTipoPagamento("H");
        $funcionario5->setInstrutor(1);
        $funcionario5->setInstrutorPersonal(1);
        $funcionario5->setConsultor(0);
        $funcionario5->setAtendente(0);
        $funcionario5->setRecebeEmails(0);
        $funcionario5->setGestorComercial(0);
        $funcionario5->setNivelInstrutor($this->getReference((NivelInstrutorFixtures::NIVEL_INSTRUTOR2_REFERENCE)));
        $funcionario5->setGestorComercialFuncionario($funcionario2);
        $manager->persist($funcionario5);

        $funcionario6 = new Funcionario();
        $funcionario6->setPessoa($this->getReference(PessoaFixtures::PESSOA6_REFERENCE));
        $funcionario6->setFranqueada($this->getReference(FranqueadaFixtures::FRANQUEADA_REFERENCE));
        $funcionario6->setCargo($this->getReference(CargoFixtures::CARGO_PROFESSOR_REFERENCE));
        $funcionario6->setBanco($this->getReference(BancoFixtures::BANCO_REFERENCE));
        $funcionario6->setApelido("Lucas");
        $funcionario6->setTipoPagamento("H");
        $funcionario6->setInstrutor(1);
        $funcionario6->setInstrutorPersonal(0);
        $funcionario6->setConsultor(0);
        $funcionario6->setAtendente(0);
        $funcionario6->setRecebeEmails(0);
        $funcionario6->setGestorComercial(0);
        $funcionario6->setNivelInstrutor($this->getReference((NivelInstrutorFixtures::NIVEL_INSTRUTOR_REFERENCE)));
        $manager->persist($funcionario6);

        $funcionarioGestorPedagogico = new Funcionario();
        $funcionarioGestorPedagogico->setPessoa($this->getReference(PessoaFixtures::PESSOA14_REFERENCE));
        $funcionarioGestorPedagogico->setFranqueada($this->getReference(FranqueadaFixtures::FRANQUEADA_REFERENCE));
        $funcionarioGestorPedagogico->setCargo($this->getReference(CargoFixtures::CARGO_COORDENADOR_PEDAGOGICO_REFERENCE));
        $funcionarioGestorPedagogico->setBanco($this->getReference(BancoFixtures::BANCO_REFERENCE));
        $funcionarioGestorPedagogico->setApelido("Marcelo");
        $funcionarioGestorPedagogico->setTipoPagamento("M");
        $funcionarioGestorPedagogico->setInstrutor(0);
        $funcionarioGestorPedagogico->setInstrutorPersonal(0);
        $funcionarioGestorPedagogico->setConsultor(1);
        $funcionarioGestorPedagogico->setAtendente(1);
        $funcionarioGestorPedagogico->setGestorComercial(0);
        $funcionarioGestorPedagogico->setUsuario($this->getReference(UsuarioFixtures::USUARIO_MARCOS_REFERENCE));
        $manager->persist($funcionarioGestorPedagogico);

        $manager->flush();
        $this->addReference(self::FUNCIONARIO_REFERENCE, $funcionario);
        $this->addReference(self::FUNCIONARIO2_REFERENCE, $funcionario2);
        $this->addReference(self::FUNCIONARIO3_REFERENCE, $funcionario3);
        $this->addReference(self::FUNCIONARIO4_REFERENCE, $funcionario4);
        $this->addReference(self::FUNCIONARIO5_REFERENCE, $funcionario5);
        $this->addReference(self::FUNCIONARIO6_REFERENCE, $funcionario6);

    }

    public function getDependencies()
    {
        return [
            PessoaFixtures::class,
            FranqueadaFixtures::class,
            CargoFixtures::class,
            BancoFixtures::class,
            NivelInstrutorFixtures::class,
            UsuarioFixtures::class,
        ];
    }


}
