<?php

namespace App\DataFixtures;

use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\Persistence\ObjectManager;
use App\Entity\Principal\MovimentoEstoque;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;

class MovimentoEstoqueFixtures extends Fixture implements DependentFixtureInterface
{
    public const MOVIMENTO_ESTOQUE_REFERENCE = "movimento_estoque";

    public function load(ObjectManager $manager)
    {
        $movimento_estoque = new MovimentoEstoque();
        $movimento_estoque->setFranqueada($this->getReference(FranqueadaFixtures::FRANQUEADA_REFERENCE));
        $movimento_estoque->setItem($this->getReference(ItemFixtures::ITEM_BOOK_1));
        $movimento_estoque->setUsuario($this->getReference(UsuarioFixtures::USUARIO_ERICK_REFERENCE));
        $movimento_estoque->setTipoMovimentoEstoque($this->getReference(TipoMovimentoEstoqueFixtures::TPMOVIMENTO_ESTOQUE2_REFERENCE));
        $movimento_estoque->setDataMovimento(\DateTime::createFromFormat("d/m/Y", "01/01/2019"));
        $movimento_estoque->setQuantidadeItem(1);
        $movimento_estoque->setQuantidadeSaldoFinal(9);
        $movimento_estoque->setValorMovimento(200);
        $movimento_estoque->setItemContaReceber($this->getReference(ItemContaReceberFixtures::ITEM_CONTA_RECEBER_LIVRO1_REFERENCE));
        $manager->persist($movimento_estoque);
        $this->addReference(self::MOVIMENTO_ESTOQUE_REFERENCE, $movimento_estoque);

        $manager->flush();
    }

    public function getDependencies()
    {
        return [
            FranqueadaFixtures::class,
            ItemFixtures::class,
            UsuarioFixtures::class,
            TipoMovimentoEstoqueFixtures::class,
            ItemContaReceberFixtures::class,
        ];
    }


}
