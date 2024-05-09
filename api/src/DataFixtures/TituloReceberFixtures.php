<?php

namespace App\DataFixtures;

use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\Persistence\ObjectManager;
use App\Entity\Principal\TituloReceber;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;

class TituloReceberFixtures extends Fixture implements DependentFixtureInterface
{

    public const TITULOREBECER1_REFERENCE = "tituloReceber1";
    public const TITULOREBECER2_REFERENCE = "tituloReceber2";
    public const TITULOREBECER3_REFERENCE = "tituloReceber3";

    public function load(ObjectManager $manager)
    {
        $data  = \DateTime::createFromFormat("Y-m-d\TH:i:s.uP", "2019-05-01T12:00:00.000Z");
        $data2 = \DateTime::createFromFormat("Y-m-d\TH:i:s.uP", "2019-05-15T12:00:00.000Z");
        $data3 = \DateTime::createFromFormat("Y-m-d\TH:i:s.uP", "2019-05-30T12:00:00.000Z");

        $tituloReceber1 = new TituloReceber();
        $tituloReceber1->setFranqueada($this->getReference(FranqueadaFixtures::FRANQUEADA_REFERENCE));
        $tituloReceber1->setContaReceber($this->getReference(ContaReceberFixtures::CONTA_RECEBER_REFERENCE));
        $tituloReceber1->setSacadoPessoa($this->getReference(PessoaFixtures::PESSOA7_REFERENCE));
        $tituloReceber1->setAluno($this->getReference(AlunoFixtures::ALUNO_REFERENCE));
        $tituloReceber1->setConta($this->getReference(ContaFixtures::CONTA_REFERENCE));
        $tituloReceber1->setFormaCobranca($this->getReference(FormaPagamentoFixtures::DINHEIRO_REFERENCE));
        $tituloReceber1->setFormaRecebimento($this->getReference(FormaPagamentoFixtures::DINHEIRO_REFERENCE));
        // $tituloReceber1->setTransacaoCartao();
        // $tituloReceber1->setCheque();
        // $tituloReceber1->setBoleto();
        $tituloReceber1->setDataVencimento($data);
        $tituloReceber1->setDataProrrogacao($data);
        $tituloReceber1->setDataEmissao($data);
        $tituloReceber1->setValorOriginal(150);
        // $tituloReceber1->setValorDespesas($data);
        // $tituloReceber1->setTaxaMulta($data);
        // $tituloReceber1->setTaxaJuroDia($data);
        $tituloReceber1->setValorSaldoDevedor(150);
        $tituloReceber1->setObservacao("Matrícula (1/1)");
        $tituloReceber1->setSituacao("PEN");
        $tituloReceber1->setNumeroParcelaDocumento(1);
        // $tituloReceber1->setTituloReceberCheques($data);
        // $tituloReceber1->setMovimentoConta($data);
        $manager->persist($tituloReceber1);
        $this->addReference(self::TITULOREBECER1_REFERENCE, $tituloReceber1);

        $tituloReceber2 = new TituloReceber();
        $tituloReceber2->setFranqueada($this->getReference(FranqueadaFixtures::FRANQUEADA_REFERENCE));
        $tituloReceber2->setContaReceber($this->getReference(ContaReceberFixtures::CONTA_RECEBER_REFERENCE));
        $tituloReceber2->setSacadoPessoa($this->getReference(PessoaFixtures::PESSOA7_REFERENCE));
        $tituloReceber2->setAluno($this->getReference(AlunoFixtures::ALUNO_REFERENCE));
        $tituloReceber2->setConta($this->getReference(ContaFixtures::CONTA_REFERENCE));
        $tituloReceber2->setFormaCobranca($this->getReference(FormaPagamentoFixtures::DINHEIRO_REFERENCE));
        $tituloReceber2->setFormaRecebimento($this->getReference(FormaPagamentoFixtures::DINHEIRO_REFERENCE));
        // $tituloReceber2->setTransacaoCartao();
        // $tituloReceber2->setCheque();
        // $tituloReceber2->setBoleto();
        $tituloReceber2->setDataVencimento($data2);
        $tituloReceber2->setDataProrrogacao($data2);
        $tituloReceber2->setDataEmissao($data2);
        $tituloReceber2->setValorOriginal(1710);
        // $tituloReceber2->setValorDespesas($data);
        // $tituloReceber2->setTaxaMulta($data);
        // $tituloReceber2->setTaxaJuroDia($data);
        $tituloReceber2->setValorSaldoDevedor(1710);
        $tituloReceber2->setObservacao("Parcela do curso turmas (1/1)");
        $tituloReceber2->setSituacao("PEN");
        $tituloReceber2->setNumeroParcelaDocumento(2);
        // $tituloReceber2->setTituloReceberCheques($data);
        // $tituloReceber2->setMovimentoConta($data);
        $manager->persist($tituloReceber2);
        $this->addReference(self::TITULOREBECER2_REFERENCE, $tituloReceber2);

        $tituloReceber3 = new TituloReceber();
        $tituloReceber3->setFranqueada($this->getReference(FranqueadaFixtures::FRANQUEADA_REFERENCE));
        $tituloReceber3->setContaReceber($this->getReference(ContaReceberFixtures::CONTA_RECEBER_REFERENCE));
        $tituloReceber3->setSacadoPessoa($this->getReference(PessoaFixtures::PESSOA7_REFERENCE));
        $tituloReceber3->setAluno($this->getReference(AlunoFixtures::ALUNO_REFERENCE));
        $tituloReceber3->setConta($this->getReference(ContaFixtures::CONTA_REFERENCE));
        $tituloReceber3->setFormaCobranca($this->getReference(FormaPagamentoFixtures::DINHEIRO_REFERENCE));
        $tituloReceber3->setFormaRecebimento($this->getReference(FormaPagamentoFixtures::DINHEIRO_REFERENCE));
        // $tituloReceber3->setTransacaoCartao();
        // $tituloReceber3->setCheque();
        // $tituloReceber3->setBoleto();
        $tituloReceber3->setDataVencimento($data3);
        $tituloReceber3->setDataProrrogacao($data3);
        $tituloReceber3->setDataEmissao($data3);
        $tituloReceber3->setValorOriginal(200);
        // $tituloReceber3->setValorDespesas($data);
        // $tituloReceber3->setTaxaMulta($data);
        // $tituloReceber3->setTaxaJuroDia($data);
        $tituloReceber3->setValorSaldoDevedor(200);
        $tituloReceber3->setObservacao("Livros (1/1)");
        $tituloReceber3->setSituacao("PEN");
        $tituloReceber3->setNumeroParcelaDocumento(3);
        // $tituloReceber3->setTituloReceberCheques($data);
        // $tituloReceber3->setMovimentoConta($data);
        $manager->persist($tituloReceber3);
        $this->addReference(self::TITULOREBECER3_REFERENCE, $tituloReceber3);

        $manager->flush();

    }

    public function getDependencies()
    {
        return [
            FranqueadaFixtures::class,
            ContaReceberFixtures::class,
            PessoaFixtures::class,
            AlunoFixtures::class,
            ContaFixtures::class,
            FormaPagamentoFixtures::class,
        ];
    }


}
