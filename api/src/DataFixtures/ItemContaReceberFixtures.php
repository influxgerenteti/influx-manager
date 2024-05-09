<?php

namespace App\DataFixtures;

use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\Persistence\ObjectManager;
use App\Entity\Principal\ItemContaReceber;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use App\Helper\SituacoesSistema;

class ItemContaReceberFixtures extends Fixture implements DependentFixtureInterface
{

    public const ITEM_CONTA_RECEBER_LIVRO1_REFERENCE     = "item_conta_receber_livro1";
    public const ITEM_CONTA_RECEBER_MATRICULA1_REFERENCE = "item_conta_receber_matricula1";
    public const ITEM_CONTA_RECEBER_CURSO1_REFERENCE     = "item_conta_receber_curso1";

    public function load(ObjectManager $manager)
    {
        $item_conta_receber_matricula1 = new ItemContaReceber();
        $item_conta_receber_matricula1->setContaReceber($this->getReference(ContaReceberFixtures::CONTA_RECEBER_REFERENCE));
        $item_conta_receber_matricula1->setItem($this->getReference(ItemFixtures::TAXA_MATRICULA));
        $item_conta_receber_matricula1->setPlanoConta($this->getReference(PlanoContaFixtures::MATRICULA_REFERENCE));
        $item_conta_receber_matricula1->setNumeroSequencia(1);
        $item_conta_receber_matricula1->setQuantidade(1);
        $item_conta_receber_matricula1->setValor(150);
        $manager->persist($item_conta_receber_matricula1);
        $this->addReference(self::ITEM_CONTA_RECEBER_MATRICULA1_REFERENCE, $item_conta_receber_matricula1);

        $item_conta_receber_curso1 = new ItemContaReceber();
        $item_conta_receber_curso1->setContaReceber($this->getReference(ContaReceberFixtures::CONTA_RECEBER_REFERENCE));
        $item_conta_receber_curso1->setItem($this->getReference(ItemFixtures::VALOR_CURSO));
        $item_conta_receber_curso1->setPlanoConta($this->getReference(PlanoContaFixtures::VALOR_CURSO_REFERENCE));
        $item_conta_receber_curso1->setNumeroSequencia(1);
        $item_conta_receber_curso1->setQuantidade(1);
        $item_conta_receber_curso1->setValor(1710);
        $item_conta_receber_curso1->setPercentualDesconto(5);
        $item_conta_receber_curso1->setValorDesconto(10);
        $manager->persist($item_conta_receber_curso1);
        $this->addReference(self::ITEM_CONTA_RECEBER_CURSO1_REFERENCE, $item_conta_receber_curso1);

        $item_conta_receber_livro1 = new ItemContaReceber();
        $item_conta_receber_livro1->setContaReceber($this->getReference(ContaReceberFixtures::CONTA_RECEBER_REFERENCE));
        $item_conta_receber_livro1->setItem($this->getReference(ItemFixtures::ITEM_BOOK_1));
        $item_conta_receber_livro1->setPlanoConta($this->getReference(PlanoContaFixtures::PLANO_CONTA_LIVROS_REFERENCE));
        $item_conta_receber_livro1->setNumeroSequencia(1);
        $item_conta_receber_livro1->setQuantidade(1);
        $item_conta_receber_livro1->setValor(200);
        $item_conta_receber_livro1->setSituacaoEntrega(SituacoesSistema::ITEM_NAO_ENTREGUE);
        // $item_conta_receber_livro1->setDataEntrega(\DateTime::createFromFormat("d/m/Y", "01/01/2019"));
        $item_conta_receber_livro1->setUsuarioEntregue($this->getReference(UsuarioFixtures::USUARIO_ERICK_REFERENCE));
        $manager->persist($item_conta_receber_livro1);
        $this->addReference(self::ITEM_CONTA_RECEBER_LIVRO1_REFERENCE, $item_conta_receber_livro1);

        $manager->flush();
    }

    public function getDependencies()
    {
        return [
            ContaReceberFixtures::class,
            ItemFixtures::class,
            PlanoContaFixtures::class,
            UsuarioFixtures::class,
        ];
    }


}
