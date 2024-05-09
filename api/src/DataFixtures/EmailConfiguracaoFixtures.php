<?php

namespace App\DataFixtures;

use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\Persistence\ObjectManager;
use App\Entity\Principal\EmailConfiguracao;

class EmailConfiguracaoFixtures extends Fixture
{


    public function load(ObjectManager $manager)
    {
        $emailConfiguracao = new EmailConfiguracao();
        $emailConfiguracao->setIsSmtp(true);
        $emailConfiguracao->setHost("smtp.umbler.com");
        $emailConfiguracao->setPort(587);
        $emailConfiguracao->setEncryption("");
        $emailConfiguracao->setUsername("envio@gatilabs.com.br");
        $emailConfiguracao->setPassword("gati@2019@");
        $emailConfiguracao->setFromNome("Suporte GATI labs.");
        $emailConfiguracao->setFromEmail("envio@gatilabs.com.br");

        $manager->persist($emailConfiguracao);
        $manager->flush();
    }


}
