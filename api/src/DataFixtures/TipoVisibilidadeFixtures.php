<?php

namespace App\DataFixtures;

use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\Persistence\ObjectManager;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use App\Entity\Principal\TipoVisibilidade;

class TipoVisibilidadeFixtures extends Fixture implements DependentFixtureInterface
{
    public const TIPO_VISIBILIDADE_OUTDOOR  = "tpvsotd";
    public const TIPO_VISIBILIDADE_FACEBOOK = "tpvsfbk";
    public const TIPO_VISIBILIDADE_TWITTER  = "tpvstwtr";
    public const TIPO_VISIBILIDADE_OUTROS   = "tpvsots";

    public function load(ObjectManager $manager)
    {
        $tipoVisibilidadeOutdoor = new TipoVisibilidade();
        $tipoVisibilidadeOutdoor->setDescricao("Outdoor");
        $tipoVisibilidadeOutdoor->setFranqueada($this->getReference(FranqueadaFixtures::FRANQUEADORA_REFERENCE));
        $tipoVisibilidadeOutdoor->setTipo("OT");
        $manager->persist($tipoVisibilidadeOutdoor);
        $this->addReference(self::TIPO_VISIBILIDADE_OUTDOOR, $tipoVisibilidadeOutdoor);

        $tipoVisibilidadeFacebook = new TipoVisibilidade();
        $tipoVisibilidadeFacebook->setDescricao("Facebook");
        $tipoVisibilidadeFacebook->setFranqueada($this->getReference(FranqueadaFixtures::FRANQUEADORA_REFERENCE));
        $tipoVisibilidadeFacebook->setTipo("FBK");
        $manager->persist($tipoVisibilidadeFacebook);
        $this->addReference(self::TIPO_VISIBILIDADE_FACEBOOK, $tipoVisibilidadeFacebook);

        $tipoVisibilidadeTwitter = new TipoVisibilidade();
        $tipoVisibilidadeTwitter->setDescricao("Twitter");
        $tipoVisibilidadeTwitter->setFranqueada($this->getReference(FranqueadaFixtures::FRANQUEADORA_REFERENCE));
        $tipoVisibilidadeTwitter->setTipo("TWT");
        $manager->persist($tipoVisibilidadeTwitter);
        $this->addReference(self::TIPO_VISIBILIDADE_TWITTER, $tipoVisibilidadeTwitter);

        $tipoVisibilidadeOutros = new TipoVisibilidade();
        $tipoVisibilidadeOutros->setDescricao("Outros");
        $tipoVisibilidadeOutros->setFranqueada($this->getReference(FranqueadaFixtures::FRANQUEADORA_REFERENCE));
        $tipoVisibilidadeOutros->setTipo("OTS");
        $manager->persist($tipoVisibilidadeOutros);
        $this->addReference(self::TIPO_VISIBILIDADE_OUTROS, $tipoVisibilidadeOutros);

        $manager->flush();
    }

    public function getDependencies()
    {
        return [
            FranqueadaFixtures::class,
        ];
    }


}
