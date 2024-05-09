<?php

namespace App\DataFixtures;

use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\Persistence\ObjectManager;
use App\Entity\Principal\MovimentoConta;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;

class MovimentoContaFixtures extends Fixture implements DependentFixtureInterface
{

    public const MOVIMENTO_CONTA1_REFERENCE = "movimento_conta1";
    public const MOVIMENTO_CONTA2_REFERENCE = "movimento_conta2";
    public const MOVIMENTO_CONTA3_REFERENCE = "movimento_conta3";
    public const MOVIMENTO_CONTA4_REFERENCE = "movimento_conta4";

    public function load(ObjectManager $manager)
    {
        $data      = new \DateTime();
        $dataPagar = \DateTime::createFromFormat("Y-m-d\TH:i:s.uP", "2018-09-26T14:06:40.000Z");

        // TITULO_PAGAR
        $config = new MovimentoConta();
        $config->setConta($this->getReference(ContaFixtures::CONTA_FRANQUEADA_REFERENCE));
        $config->setFranqueada($this->getReference(FranqueadaFixtures::FRANQUEADA_REFERENCE));
        $config->setTipoMovimentoConta($this->getReference(TipoMovimentoContaFixtures::TIPO_MOVIMENTO_CONTA1_REFERENCE));
        $config->setTituloPagar($this->getReference(TituloPagarFixtures::TITULO_PAGAR1_REFERENCE));
        $config->setUsuario($this->getReference(UsuarioFixtures::USUARIO_ERICK_REFERENCE));
        $config->setFormaPagamento($this->getReference(FormaPagamentoFixtures::DINHEIRO_REFERENCE));
        $config->setDataMovimento($dataPagar);
        $config->setDataContabil($dataPagar);
        $config->setOperacao("D");
        $config->setValorLancamento(225);
        $config->setValorTitulo(225);
        $config->setValorSaldoFinalConta(225);
        $manager->persist($config);
        $this->addReference(self::MOVIMENTO_CONTA1_REFERENCE, $config);

        // TITULO_RECEBER
        $config = new MovimentoConta();
        $config->setConta($this->getReference(ContaFixtures::CONTA_FRANQUEADA_REFERENCE));
        $config->setFranqueada($this->getReference(FranqueadaFixtures::FRANQUEADA_REFERENCE));
        $config->setTipoMovimentoConta($this->getReference(TipoMovimentoContaFixtures::TIPO_MOVIMENTO_CONTA2_REFERENCE));
        $config->setTituloReceber(null);
        $config->setUsuario($this->getReference(UsuarioFixtures::USUARIO_ERICK_REFERENCE));
        $config->setFormaPagamento($this->getReference(FormaPagamentoFixtures::DINHEIRO_REFERENCE));
        $config->setDataMovimento($data);
        $config->setDataContabil($data);
        $config->setOperacao("C");
        $config->setValorLancamento(350);
        $config->setValorTitulo(350);
        $config->setValorSaldoFinalConta(350);
        $manager->persist($config);
        $this->addReference(self::MOVIMENTO_CONTA2_REFERENCE, $config);

        $config = new MovimentoConta();
        $config->setConta($this->getReference(ContaFixtures::CONTA_FRANQUEADA_REFERENCE));
        $config->setFranqueada($this->getReference(FranqueadaFixtures::FRANQUEADA_REFERENCE));
        $config->setTipoMovimentoConta($this->getReference(TipoMovimentoContaFixtures::TIPO_MOVIMENTO_CONTA2_REFERENCE));
        $config->setTituloReceber(null);
        $config->setUsuario($this->getReference(UsuarioFixtures::USUARIO_ERICK_REFERENCE));
        $config->setFormaPagamento($this->getReference(FormaPagamentoFixtures::DINHEIRO_REFERENCE));
        $config->setDataMovimento($data);
        $config->setDataContabil($data);
        $config->setOperacao("C");
        $config->setValorLancamento(10000);
        $config->setValorTitulo(10000);
        $config->setValorSaldoFinalConta(10000);
        $config->setObservacao("Capital Inicial para trabalho");
        $manager->persist($config);
        $this->addReference(self::MOVIMENTO_CONTA3_REFERENCE, $config);

        $config = new MovimentoConta();
        $config->setConta($this->getReference(ContaFixtures::CONTA_ITAU_REFERENCE));
        $config->setFranqueada($this->getReference(FranqueadaFixtures::FRANQUEADA_REFERENCE));
        $config->setTipoMovimentoConta($this->getReference(TipoMovimentoContaFixtures::TIPO_MOVIMENTO_CONTA2_REFERENCE));
        $config->setTituloReceber(null);
        $config->setUsuario($this->getReference(UsuarioFixtures::USUARIO_ERICK_REFERENCE));
        $config->setFormaPagamento($this->getReference(FormaPagamentoFixtures::TRANSFERENCIA_BANCARIA_REFERENCE));
        $config->setDataMovimento($data);
        $config->setDataContabil($data);
        $config->setOperacao("C");
        $config->setValorLancamento(5000);
        $config->setValorTitulo(5000);
        $config->setValorSaldoFinalConta(5000);
        $config->setObservacao("Capital Inicial para trabalho");
        $manager->persist($config);
        $this->addReference(self::MOVIMENTO_CONTA4_REFERENCE, $config);

        $manager->flush();

    }

    public function getDependencies()
    {
        return [
            ContaFixtures::class,
            FranqueadaFixtures::class,
            TipoMovimentoContaFixtures::class,
            TituloPagarFixtures::class,
            UsuarioFixtures::class,
            FormaPagamentoFixtures::class,
        ];
    }


}
