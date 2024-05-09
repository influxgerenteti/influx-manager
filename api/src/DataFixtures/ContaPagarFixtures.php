<?php

namespace App\DataFixtures;

use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\Persistence\ObjectManager;
use App\Entity\Principal\ContaPagar;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;

class ContaPagarFixtures extends Fixture implements DependentFixtureInterface
{

    public const NOTA_ENTRADA_REFERENCE = "conta_pagar";

    public function load(ObjectManager $manager)
    {
        $config = new ContaPagar();
        $config->setFranqueada($this->getReference(FranqueadaFixtures::FRANQUEADA_REFERENCE));
        $config->setFornecedorPessoa($this->getReference(PessoaFixtures::PESSOA2_REFERENCE));
        $config->setUsuario($this->getReference(UsuarioFixtures::USUARIO_ERICK_REFERENCE));
        $config->setDataMovimento(new \DateTime());
        $config->setFormaCobranca($this->getReference(FormaPagamentoFixtures::DINHEIRO_REFERENCE));
        $config->setValorParcela(225);
        $config->setNumeroParcelas(5);
        $config->setValorTotal(1125);
        $manager->persist($config);
        $manager->flush();

        $this->addReference(self::NOTA_ENTRADA_REFERENCE, $config);
    }

    public function getDependencies()
    {
        return [
            FranqueadaFixtures::class,
            PessoaFixtures::class,
            UsuarioFixtures::class,
            CondicaoPagamentoFixtures::class,
            FormaPagamentoFixtures::class,
        ];
    }


}
