<?php

namespace App\DataFixtures;

use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\Persistence\ObjectManager;
use App\Entity\Principal\Sala;
use PhpOffice\PhpSpreadsheet\Reader\Slk;

class SalaFixtures extends Fixture
{

    public const SALA_ATIVIDADE_EXTRA_1 = "sala_atividade_extra_1";

    public const SALA_PERSONAL_1 = "sala_personal_1";
    public const SALA_PERSONAL_2 = "sala_personal_2";
    public const SALA_PERSONAL_3 = "sala_personal_3";

    public const SALA_VIDEO_1 = "sala_video_1";
    public const SALA_VIDEO_2 = "sala_video_2";

    public const SALA_1 = "sala_1";
    public const SALA_2 = "sala_2";
    public const SALA_3 = "sala_3";
    public const SALA_4 = "sala_4";
    public const SALA_5 = "sala_5";

    public const SALA_6  = "sala_6";
    public const SALA_7  = "sala_7";
    public const SALA_8  = "sala_8";
    public const SALA_9  = "sala_9";
    public const SALA_10 = "sala_10";
    public const SALA_11 = "sala_11";
    public const SALA_12 = "sala_12";
    public const SALA_13 = "sala_13";
    public const SALA_14 = "sala_14";
    public const SALA_15 = "sala_15";

    public const SALA_16 = "sala_16";
    public const SALA_17 = "sala_17";
    public const SALA_18 = "sala_18";
    public const SALA_19 = "sala_19";
    public const SALA_20 = "sala_20";
    public const SALA_21 = "sala_21";
    public const SALA_22 = "sala_22";
    public const SALA_23 = "sala_23";
    public const SALA_24 = "sala_24";

    public const SALA_25 = "sala_25";
    public const SALA_26 = "sala_26";
    public const SALA_27 = "sala_27";
    public const SALA_28 = "sala_28";
    public const SALA_29 = "sala_29";
    public const SALA_30 = "sala_30";


    public function load(ObjectManager $manager)
    {
        // Personal
        $salaPersonal_1 = new Sala();
        $salaPersonal_1->setDescricao("Personal A");
        $manager->persist($salaPersonal_1);
        $this->addReference(self::SALA_PERSONAL_1, $salaPersonal_1);

        $salaPersonal_2 = new Sala();
        $salaPersonal_2->setDescricao("Personal B");
        $manager->persist($salaPersonal_2);
        $this->addReference(self::SALA_PERSONAL_2, $salaPersonal_2);

        $salaPersonal_3 = new Sala();
        $salaPersonal_3->setDescricao("Personal C");
        $manager->persist($salaPersonal_3);
        $this->addReference(self::SALA_PERSONAL_3, $salaPersonal_3);

        // Video
        $salaVideo1 = new Sala();
        $salaVideo1->setDescricao("Sala de Vídeo 01");
        $manager->persist($salaVideo1);
        $this->addReference(self::SALA_VIDEO_1, $salaVideo1);

        $salaVideo2 = new Sala();
        $salaVideo2->setDescricao("Sala de Vídeo 02");
        $manager->persist($salaVideo2);
        $this->addReference(self::SALA_VIDEO_2, $salaVideo2);

        // Aula
        $sala1 = new Sala();
        $sala1->setDescricao("Sala 01");
        $manager->persist($sala1);
        $this->addReference(self::SALA_1, $sala1);

        $sala2 = new Sala();
        $sala2->setDescricao("Sala 02");
        $manager->persist($sala2);
        $this->addReference(self::SALA_2, $sala2);

        $sala3 = new Sala();
        $sala3->setDescricao("Sala 03");
        $manager->persist($sala3);
        $this->addReference(self::SALA_3, $sala3);

        $sala4 = new Sala();
        $sala4->setDescricao("Sala 04");
        $manager->persist($sala4);
        $this->addReference(self::SALA_4, $sala4);

        $sala5 = new Sala();
        $sala5->setDescricao("Sala 05");
        $manager->persist($sala5);
        $this->addReference(self::SALA_5, $sala5);

        $sala6 = new Sala();
        $sala6->setDescricao("Sala 06");
        $manager->persist($sala6);
        $this->addReference(self::SALA_6, $sala6);

        $sala7 = new Sala();
        $sala7->setDescricao("Sala 07");
        $manager->persist($sala7);
        $this->addReference(self::SALA_7, $sala7);

        $sala8 = new Sala();
        $sala8->setDescricao("Sala 08");
        $manager->persist($sala8);
        $this->addReference(self::SALA_8, $sala8);

        $sala9 = new Sala();
        $sala9->setDescricao("Sala 09");
        $manager->persist($sala9);
        $this->addReference(self::SALA_9, $sala9);

        $sala10 = new Sala();
        $sala10->setDescricao("Sala 10");
        $manager->persist($sala10);
        $this->addReference(self::SALA_10, $sala10);

        $sala11 = new Sala();
        $sala11->setDescricao("Sala 11");
        $manager->persist($sala11);
        $this->addReference(self::SALA_11, $sala11);

        $sala12 = new Sala();
        $sala12->setDescricao("Sala 12");
        $manager->persist($sala12);
        $this->addReference(self::SALA_12, $sala12);

        $sala13 = new Sala();
        $sala13->setDescricao("Sala 13");
        $manager->persist($sala13);
        $this->addReference(self::SALA_13, $sala13);

        $sala14 = new Sala();
        $sala14->setDescricao("Sala 14");
        $manager->persist($sala14);
        $this->addReference(self::SALA_14, $sala14);

        $sala15 = new Sala();
        $sala15->setDescricao("Sala 15");
        $manager->persist($sala15);
        $this->addReference(self::SALA_15, $sala15);

        $sala16 = new Sala();
        $sala16->setDescricao("Sala 16");
        $manager->persist($sala16);
        $this->addReference(self::SALA_16, $sala16);

        $sala17 = new Sala();
        $sala17->setDescricao("Sala 17");
        $manager->persist($sala17);
        $this->addReference(self::SALA_17, $sala17);

        $sala18 = new Sala();
        $sala18->setDescricao("Sala 18");
        $manager->persist($sala18);
        $this->addReference(self::SALA_18, $sala18);

        $sala19 = new Sala();
        $sala19->setDescricao("Sala 19");
        $manager->persist($sala19);
        $this->addReference(self::SALA_19, $sala19);

        $sala20 = new Sala();
        $sala20->setDescricao("Sala 20");
        $manager->persist($sala20);
        $this->addReference(self::SALA_20, $sala20);

        $sala21 = new Sala();
        $sala21->setDescricao("Sala 21");
        $manager->persist($sala21);
        $this->addReference(self::SALA_21, $sala21);

        $sala22 = new Sala();
        $sala22->setDescricao("Sala 22");
        $manager->persist($sala22);
        $this->addReference(self::SALA_22, $sala22);

        $sala23 = new Sala();
        $sala23->setDescricao("Sala 23");
        $manager->persist($sala23);
        $this->addReference(self::SALA_23, $sala23);

        $sala24 = new Sala();
        $sala24->setDescricao("Sala 24");
        $manager->persist($sala24);
        $this->addReference(self::SALA_24, $sala24);

        $sala25 = new Sala();
        $sala25->setDescricao("Sala 25");
        $manager->persist($sala25);
        $this->addReference(self::SALA_25, $sala25);

        $sala26 = new Sala();
        $sala26->setDescricao("Sala 26");
        $manager->persist($sala26);
        $this->addReference(self::SALA_26, $sala26);

        $sala27 = new Sala();
        $sala27->setDescricao("Sala 27");
        $manager->persist($sala27);
        $this->addReference(self::SALA_27, $sala27);

        $sala28 = new Sala();
        $sala28->setDescricao("Sala 28");
        $manager->persist($sala28);
        $this->addReference(self::SALA_28, $sala28);

        $sala29 = new Sala();
        $sala29->setDescricao("Sala 29");
        $manager->persist($sala29);
        $this->addReference(self::SALA_29, $sala29);

        $sala30 = new Sala();
        $sala30->setDescricao("Sala 30");
        $manager->persist($sala30);
        $this->addReference(self::SALA_30, $sala30);

        $atividadeExtra = new Sala();
        $atividadeExtra->setDescricao("Atividade Extra");
        $manager->persist($atividadeExtra);
        $this->addReference(self::SALA_ATIVIDADE_EXTRA_1, $atividadeExtra);

        $manager->flush();
    }


}
