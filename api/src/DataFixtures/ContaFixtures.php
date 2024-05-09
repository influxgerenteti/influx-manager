<?php

namespace App\DataFixtures;

use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\Persistence\ObjectManager;
use App\Entity\Principal\Conta;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;

class ContaFixtures extends Fixture implements DependentFixtureInterface
{

    public const CONTA_REFERENCE       = "contaCaixa";
    public const CONTA_ITAU_REFERENCE  = "contaItau";
    public const CONTA_COFRE_REFERENCE = "contaCofre";
    public const CONTA_CAIXA_PEQUENO_REFERENCE  = "contaCaixaPequeno";
    public const CONTA_FRANQUEADA_REFERENCE     = "contaFranqueada";
    public const CONTA_CAIXA_PEQUENO2_REFERENCE = "contaCaixaPequeno2";

    public function load(ObjectManager $manager)
    {

        $franqueadora = $this->getReference(FranqueadaFixtures::FRANQUEADORA_REFERENCE);
        $franqueada   = $this->getReference(FranqueadaFixtures::FRANQUEADA_REFERENCE);

        $contaCaixa = new Conta();
        $contaCaixa->setDescricao("Caixa (todos recebimentos)");
        $contaCaixa->setFranqueada($franqueadora);
        $contaCaixa->setBanco($this->getReference(BancoFixtures::BANCO_ITAU_REFERENCE));
        $contaCaixa->setConsideraFluxoCaixa(true);
        $contaCaixa->setBancoEmiteBoleto(false);
        $contaCaixa->setSituacao("A");
        $contaCaixa->setValorSaldo(10150);
        $manager->persist($contaCaixa);

        $franqueadora->setContaPadrao($contaCaixa);
        $manager->persist($franqueadora);

        $contaCaixa2 = new Conta();
        $contaCaixa2->setDescricao("Caixa (todos recebimentos)");
        $contaCaixa2->setFranqueada($franqueada);
        $contaCaixa2->setBanco($this->getReference(BancoFixtures::BANCO_ITAU_REFERENCE));
        $contaCaixa2->setConsideraFluxoCaixa(true);
        $contaCaixa2->setBancoEmiteBoleto(false);
        $contaCaixa2->setSituacao("A");
        $contaCaixa2->setValorSaldo(0);
        $manager->persist($contaCaixa2);

        $contaItau = new Conta();
        $contaItau->setDescricao("Itaú");
        $contaItau->setFranqueada($franqueada);
        $contaItau->setBanco($this->getReference(BancoFixtures::BANCO_ITAU_REFERENCE));
        $contaItau->setConsideraFluxoCaixa(true);
        $contaItau->setBancoEmiteBoleto(false);
        $contaItau->setSituacao("A");
        $contaItau->setValorSaldo(5000);
        $manager->persist($contaItau);

        $contaCofre = new Conta();
        $contaCofre->setDescricao("Cofre");
        $contaCofre->setFranqueada($franqueada);
        $contaCofre->setBanco($this->getReference(BancoFixtures::BANCO_REFERENCE));
        $contaCofre->setConsideraFluxoCaixa(true);
        $contaCofre->setBancoEmiteBoleto(false);
        $contaCofre->setSituacao("A");
        $contaCofre->setValorSaldo(20000);
        $manager->persist($contaCofre);

        $contaCaixaPequeno = new Conta();
        $contaCaixaPequeno->setDescricao("Caixa Pequeno");
        $contaCaixaPequeno->setFranqueada($franqueada);
        $contaCaixaPequeno->setBanco($this->getReference(BancoFixtures::BANCO_REFERENCE));
        $contaCaixaPequeno->setConsideraFluxoCaixa(true);
        $contaCaixaPequeno->setBancoEmiteBoleto(false);
        $contaCaixaPequeno->setSituacao("A");
        $contaCaixaPequeno->setValorSaldo(1000);
        $manager->persist($contaCaixaPequeno);

        $contaCaixaPequeno2 = new Conta();
        $contaCaixaPequeno2->setDescricao("Caixa Pequeno");
        $contaCaixaPequeno2->setFranqueada($franqueadora);
        $contaCaixaPequeno2->setBanco($this->getReference(BancoFixtures::BANCO_REFERENCE));
        $contaCaixaPequeno2->setConsideraFluxoCaixa(true);
        $contaCaixaPequeno2->setBancoEmiteBoleto(false);
        $contaCaixaPequeno2->setSituacao("A");
        $contaCaixaPequeno2->setValorSaldo(0);
        $manager->persist($contaCaixaPequeno2);

        $contaBradesco = new Conta();
        $contaBradesco->setDescricao("Bradesco");
        $contaBradesco->setFranqueada($franqueada);
        $contaBradesco->setBanco($this->getReference(BancoFixtures::BANCO_BRADESCO_REFERENCE));
        $contaBradesco->setConsideraFluxoCaixa(true);
        $contaBradesco->setBancoEmiteBoleto(true);
        $contaBradesco->setNumeroAgencia("5716");
        $contaBradesco->setDigitoAgencia("9");
        $contaBradesco->setContaCorrente("34439");
        $contaBradesco->setDigitoContaCorrente("7");
        $contaBradesco->setEmpresaNoBanco("5068702");
        $contaBradesco->setPrimeiraInstrucao("06");
        $contaBradesco->setSegundaInstrucao("00");
        $contaBradesco->setNumeroSequenciaArquivoCobranca(710);
        $contaBradesco->setNumeroDiasProtesto(30);
        $contaBradesco->setCarteira("9");
        $contaBradesco->setTaxaJuroDia(0.5);
        $contaBradesco->setTaxaMulta(2);
        $contaBradesco->setUltimoNossoNumero(7);
        $contaBradesco->setPercentualDescontoAntecipado(5.5);
        $contaBradesco->setNumeroDiasMaxPagamentoAposVencimento(30);
        $contaBradesco->setTextoMoraDiaria("Após o vencimento cobrar Mora Diária de:");
        $contaBradesco->setTextoMultaAtraso("Após o vencimento cobrar Multa de:");
        $contaBradesco->setSituacao("A");
        $contaBradesco->setValorSaldo(0);
        $manager->persist($contaBradesco);

        $franqueada->setContaPadrao($contaBradesco);
        $manager->persist($franqueada);

        $manager->flush();
        $this->addReference(self::CONTA_REFERENCE, $contaCaixa);
        $this->addReference(self::CONTA_FRANQUEADA_REFERENCE, $contaCaixa2);
        $this->addReference(self::CONTA_ITAU_REFERENCE, $contaItau);
        $this->addReference(self::CONTA_COFRE_REFERENCE, $contaCofre);
        $this->addReference(self::CONTA_CAIXA_PEQUENO_REFERENCE, $contaCaixaPequeno);
        $this->addReference(self::CONTA_CAIXA_PEQUENO2_REFERENCE, $contaCaixaPequeno2);
    }

    public function getDependencies()
    {
        return [
            FranqueadaFixtures::class,
            BancoFixtures::class,
        ];
    }


}
