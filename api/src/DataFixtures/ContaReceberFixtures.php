<?php

namespace App\DataFixtures;

use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\Persistence\ObjectManager;
use App\Entity\Principal\ContaReceber;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;

class ContaReceberFixtures extends Fixture implements DependentFixtureInterface
{

    public const CONTA_RECEBER_REFERENCE = "conta_receber";

    public function load(ObjectManager $manager)
    {
        $conta_receber = new ContaReceber();
        $conta_receber->setFranqueada($this->getReference(FranqueadaFixtures::FRANQUEADA_REFERENCE));
        $conta_receber->setAluno($this->getReference(AlunoFixtures::ALUNO_REFERENCE));
        $conta_receber->setSacadoPessoa($this->getReference(PessoaFixtures::PESSOA7_REFERENCE));
        $conta_receber->setContrato($this->getReference(ContratoFixtures::CONTRATO_REFERENCE));
        $conta_receber->setUsuario($this->getReference(UsuarioFixtures::USUARIO_ERICK_REFERENCE));
        $conta_receber->setVendedorFuncionario($this->getReference(FuncionarioFixtures::FUNCIONARIO4_REFERENCE));
        $conta_receber->setDataEmissao(\DateTime::createFromFormat("d/m/Y", "01/01/2019"));
        $conta_receber->setValorTotal(2150);
        $conta_receber->setSituacao("PEN");
        // $conta_receber->setItemContaReceber($this->getReference(ItemContaReceberFixtures::ITEM_CONTA_RECEBER_REFERENCE));
        $conta_receber->setBolsista(1);

        $manager->persist($conta_receber);
        $this->addReference(self::CONTA_RECEBER_REFERENCE, $conta_receber);

        $manager->flush();

    }

    public function getDependencies()
    {
        return [
            FranqueadaFixtures::class,
            AlunoFixtures::class,
            PessoaFixtures::class,
            ContratoFixtures::class,
            UsuarioFixtures::class,
            FuncionarioFixtures::class,
            // ItemContaReceberFixtures::class,
        ];
    }


}
