<?php

namespace App\DataFixtures;

use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\Persistence\ObjectManager;
use App\Entity\Principal\TipoMovimentoEstoque;

class TipoMovimentoEstoqueFixtures extends Fixture
{
    public const TPMOVIMENTO_ESTOQUE_REFERENCE  = "tipo_movimento_estoque";
    public const TPMOVIMENTO_ESTOQUE2_REFERENCE = "tipo_movimento_estoque2";

    public function load(ObjectManager $manager)
    {
        $config = new TipoMovimentoEstoque();
        $config->setDescricao("Conta a pagar");
        $config->setSituacao("A");
        $config->setTipoMovimento("E");
        $manager->persist($config);
        $this->addReference(self::TPMOVIMENTO_ESTOQUE_REFERENCE, $config);

        $config = new TipoMovimentoEstoque();
        $config->setDescricao("Exclusão de conta a pagar");
        $config->setSituacao("A");
        $config->setTipoMovimento("S");
        $manager->persist($config);

        $config = new TipoMovimentoEstoque();
        $config->setDescricao("Conta a receber");
        $config->setSituacao("A");
        $config->setTipoMovimento("S");
        $manager->persist($config);
        $this->addReference(self::TPMOVIMENTO_ESTOQUE2_REFERENCE, $config);

        $config = new TipoMovimentoEstoque();
        $config->setDescricao("Ajuste de entrada");
        $config->setSituacao("A");
        $config->setTipoMovimento("AE");
        $manager->persist($config);

        $config = new TipoMovimentoEstoque();
        $config->setDescricao("Ajuste de saida");
        $config->setSituacao("A");
        $config->setTipoMovimento("AS");
        $manager->persist($config);

        $manager->flush();
    }


}
