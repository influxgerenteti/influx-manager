<?php

namespace App\DataFixtures;

use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\Persistence\ObjectManager;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use App\Entity\Principal\SalaFranqueada;

class SalaFranqueadaFixtures extends Fixture implements DependentFixtureInterface
{

    public const SALA_FRANQUEADA_REFERENCE  = "sala_franqueada";
    public const SALA_FRANQUEADA2_REFERENCE = "sala_franqueada2";
    public const SALA_FRANQUEADA3_REFERENCE = "sala_franqueada3";
    public const SALA_FRANQUEADA4_REFERENCE = "sala_franqueada4";
    public const SALA_FRANQUEADA5_REFERENCE = "sala_franqueada5";

    public const SALA_FRANQUEADORA_REFERENCE  = "sala_franqueadora";
    public const SALA_FRANQUEADORA2_REFERENCE = "sala_franqueadora2";
    public const SALA_FRANQUEADORA3_REFERENCE = "sala_franqueadora3";
    public const SALA_FRANQUEADORA4_REFERENCE = "sala_franqueadora4";
    public const SALA_FRANQUEADORA5_REFERENCE = "sala_franqueadora5";

    public function load(ObjectManager $manager)
    {
            $salaFixtures = $manager->getRepository(\App\Entity\Principal\Sala::class)->findAll();
            $franqueada   = $this->getReference(FranqueadaFixtures::FRANQUEADA_REFERENCE);
            $franqueadora = $this->getReference(FranqueadaFixtures::FRANQUEADORA_REFERENCE);

            $salaPersonal_1 = new SalaFranqueada();
            $salaPersonal_1->setLotacaoMaxima(3);
            $salaPersonal_1->setPersonal(true);
            $salaPersonal_1->setFranqueada($franqueada);
            $salaPersonal_1->setSala($salaFixtures[0]);
            $manager->persist($salaPersonal_1);
            $this->addReference(self::SALA_FRANQUEADA_REFERENCE, $salaPersonal_1);

            $salaVideo = new SalaFranqueada();
            $salaVideo->setLotacaoMaxima(null);
            $salaVideo->setPersonal(false);
            $salaVideo->setFranqueada($franqueada);
            $salaVideo->setSala($salaFixtures[4]);
            $manager->persist($salaVideo);
            $this->addReference(self::SALA_FRANQUEADA2_REFERENCE, $salaVideo);

            $sala = new SalaFranqueada();
            $sala->setLotacaoMaxima(9);
            $sala->setPersonal(false);
            $sala->setFranqueada($franqueada);
            $sala->setSala($salaFixtures[5]);
            $manager->persist($sala);
            $this->addReference(self::SALA_FRANQUEADA3_REFERENCE, $sala);

            $sala = new SalaFranqueada();
            $sala->setLotacaoMaxima(9);
            $sala->setPersonal(false);
            $sala->setFranqueada($franqueada);
            $sala->setSala($salaFixtures[7]);
            $manager->persist($sala);
            $this->addReference(self::SALA_FRANQUEADA4_REFERENCE, $sala);

            $atividadeExtra = new SalaFranqueada();
            $atividadeExtra->setLotacaoMaxima(null);
            $atividadeExtra->setPersonal(false);
            $atividadeExtra->setFranqueada($franqueada);
            $atividadeExtra->setSala($salaFixtures[35]);
            $manager->persist($atividadeExtra);
            $this->addReference(self::SALA_FRANQUEADA5_REFERENCE, $atividadeExtra);

            // -- franqueadora
            $salaPersonal_1 = new SalaFranqueada();
            $salaPersonal_1->setLotacaoMaxima(3);
            $salaPersonal_1->setPersonal(true);
            $salaPersonal_1->setFranqueada($franqueadora);
            $salaPersonal_1->setSala($salaFixtures[0]);
            $manager->persist($salaPersonal_1);
            $this->addReference(self::SALA_FRANQUEADORA_REFERENCE, $salaPersonal_1);

            $salaVideo = new SalaFranqueada();
            $salaVideo->setLotacaoMaxima(null);
            $salaVideo->setPersonal(false);
            $salaVideo->setFranqueada($franqueadora);
            $salaVideo->setSala($salaFixtures[4]);
            $manager->persist($salaVideo);
            $this->addReference(self::SALA_FRANQUEADORA2_REFERENCE, $salaVideo);

            $sala = new SalaFranqueada();
            $sala->setLotacaoMaxima(9);
            $sala->setPersonal(false);
            $sala->setFranqueada($franqueadora);
            $sala->setSala($salaFixtures[5]);
            $manager->persist($sala);
            $this->addReference(self::SALA_FRANQUEADORA3_REFERENCE, $sala);

            $sala = new SalaFranqueada();
            $sala->setLotacaoMaxima(9);
            $sala->setPersonal(false);
            $sala->setFranqueada($franqueadora);
            $sala->setSala($salaFixtures[7]);
            $manager->persist($sala);
            $this->addReference(self::SALA_FRANQUEADORA4_REFERENCE, $sala);

            $atividadeExtra = new SalaFranqueada();
            $atividadeExtra->setLotacaoMaxima(null);
            $atividadeExtra->setPersonal(false);
            $atividadeExtra->setFranqueada($franqueadora);
            $atividadeExtra->setSala($salaFixtures[35]);
            $manager->persist($atividadeExtra);
            $this->addReference(self::SALA_FRANQUEADORA5_REFERENCE, $atividadeExtra);

            $manager->flush();

    }
    public function getDependencies()
    {
        return [
            FranqueadaFixtures::class,
            SalaFixtures::class,
        ];
    }


}
