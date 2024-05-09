<?php

namespace App\DataFixtures;

use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\Persistence\ObjectManager;
use App\Entity\Principal\Franqueada;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;

class FranqueadaFixtures extends Fixture implements DependentFixtureInterface
{


    public const FRANQUEADA_REFERENCE   = "franqueada";
    public const FRANQUEADORA_REFERENCE = "franqueadora";

    public function load(ObjectManager $manager)
    {
        $franqueadora = new Franqueada();
        $franqueadora->setNome("inFlux Franchising");
        $franqueadora->setCnpj("13069704000162");
        $franqueadora->setEmail("suporte@gatilabs.com.br");
        $franqueadora->setEmailDirecao("diogo.winck@gatilabs.com.br");
        $franqueadora->setEmailComercial("diogo.winck@gatilabs.com.br");
        $franqueadora->setFranqueadora(true);
        $franqueadora->setPercentualJuroDia(0);
        $franqueadora->setPercentualMulta(0);
        $franqueadora->setLimiteDiasAlteracaoDocumento(30);
        $franqueadora->setEstado($this->getReference(EstadoCidadeFixtures::ESTADO_PR));
        $manager->persist($franqueadora);
        $this->addReference(self::FRANQUEADORA_REFERENCE, $franqueadora);

        $franqueada = new Franqueada();
        $franqueada->setNome("inFlux Franqueada");
        $franqueada->setCnpj("31812587000167");
        $franqueada->setRazaoSocial("UNITCC Ensino de Idiomas S/A");
        $franqueada->setEstado($this->getReference(EstadoCidadeFixtures::ESTADO_PR));
        $franqueada->setCidade($this->getReference(EstadoCidadeFixtures::CIDADE_CWB));
        $franqueada->setEndereco("Rua Alberto Folloni");
        $franqueada->setNumeroEndereco("250");
        $franqueada->setBairroEndereco("Juveve");
        $franqueada->setCepEndereco("80530300");
        $franqueada->setTelefone("4130288000");
        $franqueada->setEmail("suzana.canalles@bluebrasilsrv.com.br");
        $franqueada->setEmailDirecao("diogo.winck@gatilabs.com.br");
        $franqueada->setEmailComercial("diogo.winck@gatilabs.com.br");
        $franqueada->setPercentualJuroDia(0.5);
        $franqueada->setPercentualMulta(2);
        $franqueada->setLimiteDiasAlteracaoDocumento(30);
        $manager->persist($franqueada);
        $this->addReference(self::FRANQUEADA_REFERENCE, $franqueada);

        $manager->flush();
    }

    public function getDependencies()
    {
        return [
            EstadoCidadeFixtures::class,
        ];
    }


}
