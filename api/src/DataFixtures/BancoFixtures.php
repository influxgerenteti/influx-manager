<?php

namespace App\DataFixtures;

use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\Persistence\ObjectManager;
use App\Entity\Principal\Banco;

class BancoFixtures extends Fixture
{
    public const BANCO_REFERENCE          = "banco";
    public const BANCO_ITAU_REFERENCE     = "banco_itau";
    public const BANCO_BRADESCO_REFERENCE = "banco_bradesco";

    public function load(ObjectManager $manager)
    {
        $banco0 = new Banco();
        $banco0->setCodigo("000");
        $banco0->setDescricao("Escola");
        $manager->persist($banco0);

        $bancoItau = new Banco();
        $bancoItau->setCodigo("341");
        $bancoItau->setDescricao("Itaú Unibanco");
        $manager->persist($bancoItau);

        $bancoBradesco = new Banco();
        $bancoBradesco->setCodigo("237");
        $bancoBradesco->setDescricao("Banco Bradesco");
        $manager->persist($bancoBradesco);

        $banco1 = new Banco();
        $banco1->setCodigo("001");
        $banco1->setDescricao("Banco do Brasil");
        $manager->persist($banco1);

        $banco2 = new Banco();
        $banco2->setCodigo("104");
        $banco2->setDescricao("Caixa Econômica Federal");
        $manager->persist($banco2);

        $manager->flush();
        $this->addReference(self::BANCO_REFERENCE, $banco0);
        $this->addReference(self::BANCO_ITAU_REFERENCE, $bancoItau);
        $this->addReference(self::BANCO_BRADESCO_REFERENCE, $bancoBradesco);
    }


}
