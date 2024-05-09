<?php

namespace App\DataFixtures;

use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\Persistence\ObjectManager;
use App\Entity\Principal\TipoMovimentoConta;

class TipoMovimentoContaFixtures extends Fixture
{

    public const TIPO_MOVIMENTO_CONTA1_REFERENCE = "tipo_movimento_conta1";
    public const TIPO_MOVIMENTO_CONTA2_REFERENCE = "tipo_movimento_conta2";

    public function load(ObjectManager $manager)
    {
        $config = new TipoMovimentoConta();
        $config->setDescricao("Débito");
        $config->setReservado(true);
        $config->setTipoOperacao("D");
        $manager->persist($config);
        $this->addReference(self::TIPO_MOVIMENTO_CONTA1_REFERENCE, $config);

        $config = new TipoMovimentoConta();
        $config->setDescricao("Crédito");
        $config->setReservado(true);
        $config->setTipoOperacao("C");
        $manager->persist($config);
        $this->addReference(self::TIPO_MOVIMENTO_CONTA2_REFERENCE, $config);

        $config = new TipoMovimentoConta();
        $config->setDescricao("Tranferência");
        $config->setReservado(true);
        $config->setTipoOperacao("T");
        $manager->persist($config);

        $manager->flush();
    }


}
