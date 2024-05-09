<?php

namespace App\DataFixtures;

use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\Persistence\ObjectManager;
use App\Entity\Principal\FormaPagamento;

class FormaPagamentoFixtures extends Fixture
{
    public const CARTAO_CREDITO_REFERENCE    = "cCredito";
    public const CARTAO_DEBITO_REFERENCE     = "cDebito";
    public const CHEQUE_REFERENCE            = "cheque";
    public const COBRANCA_BANCARIA_REFERENCE = "cobrancaBancaria";
    public const DINHEIRO_REFERENCE          = "dinheiro";
    public const TRANSFERENCIA_BANCARIA_REFERENCE = "transf";

    public function load(ObjectManager $manager)
    {
        $cCredito = new FormaPagamento();
        $cCredito->setDescricao("Cartão de crédito");
        $cCredito->setDescricaoAbreviada("C. Crédito");
        $cCredito->setSituacao("A");
        $cCredito->setFormaCartao(true);
        $manager->persist($cCredito);

        $cDebito = new FormaPagamento();
        $cDebito->setDescricao("Cartão de débito");
        $cDebito->setDescricaoAbreviada("C. Débito");
        $cDebito->setSituacao("A");
        $cDebito->setFormaCartao(true);
        $cDebito->setFormaCartaoDebito(true);
        $manager->persist($cDebito);

        $cheque = new FormaPagamento();
        $cheque->setDescricao("Cheque");
        $cheque->setDescricaoAbreviada("Cheque");
        $cheque->setSituacao("A");
        $cheque->setFormaCheque(true);
        $manager->persist($cheque);

        $cobrancaBancaria = new FormaPagamento();
        $cobrancaBancaria->setDescricao("Cobrança bancária");
        $cobrancaBancaria->setDescricaoAbreviada("Boleto/DDA");
        $cobrancaBancaria->setSituacao("A");
        $cobrancaBancaria->setFormaBoleto(true);
        $cobrancaBancaria->setLiquidacaoImediata(true);
        $manager->persist($cobrancaBancaria);

        $dinheiro = new FormaPagamento();
        $dinheiro->setDescricao("Dinheiro");
        $dinheiro->setDescricaoAbreviada("Dinheiro");
        $dinheiro->setSituacao("A");
        $dinheiro->setLiquidacaoImediata(true);
        $manager->persist($dinheiro);

        $transf = new FormaPagamento();
        $transf->setDescricao("Transferência bancária");
        $transf->setDescricaoAbreviada("Transf.");
        $transf->setSituacao("A");
        $transf->setFormaTrasferencia(true);
        $transf->setLiquidacaoImediata(true);
        $manager->persist($transf);

        $manager->flush();
        $this->addReference(self::CARTAO_CREDITO_REFERENCE, $cCredito);
        $this->addReference(self::CARTAO_DEBITO_REFERENCE, $cDebito);
        $this->addReference(self::CHEQUE_REFERENCE, $cheque);
        $this->addReference(self::COBRANCA_BANCARIA_REFERENCE, $cobrancaBancaria);
        $this->addReference(self::DINHEIRO_REFERENCE, $dinheiro);
        $this->addReference(self::TRANSFERENCIA_BANCARIA_REFERENCE, $transf);
    }


}
