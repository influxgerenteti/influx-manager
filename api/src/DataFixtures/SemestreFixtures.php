<?php

namespace App\DataFixtures;

use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\Persistence\ObjectManager;
use App\Entity\Principal\Semestre;

class SemestreFixtures extends Fixture
{

    public const SEMESTRE_REFERENCE  = "semestre";
    public const SEMESTRE2_REFERENCE = "semestre2";
    public const SEMESTRE3_REFERENCE = "semestre3";
    public const SEMESTRE4_REFERENCE = "semestre4";
    public const SEMESTRE5_REFERENCE = "semestre5";
    public const SEMESTRE6_REFERENCE = "semestre6";
    public const SEMESTRE7_REFERENCE = "semestre7";
    public const SEMESTRE8_REFERENCE = "semestre8";

    public function load(ObjectManager $manager)
    {
        $semestre = new Semestre();
        $semestre->setDescricao("2019/01");
        $semestre->setDataInicio(\DateTime::createFromFormat('Y-m-d', '2019-01-20'));
        $semestre->setDataTermino(\DateTime::createFromFormat('Y-m-d', '2019-06-25'));
        $manager->persist($semestre);
        $this->addReference(self::SEMESTRE_REFERENCE, $semestre);

        $semestre2 = new Semestre();
        $semestre2->setDescricao("2019/02");
        $semestre2->setDataInicio(\DateTime::createFromFormat('Y-m-d', '2019-07-15'));
        $semestre2->setDataTermino(\DateTime::createFromFormat('Y-m-d', '2019-12-18'));
        $manager->persist($semestre2);
        $this->addReference(self::SEMESTRE2_REFERENCE, $semestre2);

        $semestre3 = new Semestre();
        $semestre3->setDescricao("2020/01");
        $semestre3->setDataInicio(\DateTime::createFromFormat('Y-m-d', '2020-01-20'));
        $semestre3->setDataTermino(\DateTime::createFromFormat('Y-m-d', '2020-06-25'));
        $manager->persist($semestre3);
        $this->addReference(self::SEMESTRE3_REFERENCE, $semestre3);

        $semestre4 = new Semestre();
        $semestre4->setDescricao("2020/02");
        $semestre4->setDataInicio(\DateTime::createFromFormat('Y-m-d', '2020-07-15'));
        $semestre4->setDataTermino(\DateTime::createFromFormat('Y-m-d', '2020-12-18'));
        $manager->persist($semestre4);
        $this->addReference(self::SEMESTRE4_REFERENCE, $semestre4);

        $semestre5 = new Semestre();
        $semestre5->setDescricao("2021/01");
        $semestre5->setDataInicio(\DateTime::createFromFormat('Y-m-d', '2021-01-20'));
        $semestre5->setDataTermino(\DateTime::createFromFormat('Y-m-d', '2021-06-25'));
        $manager->persist($semestre5);
        $this->addReference(self::SEMESTRE5_REFERENCE, $semestre5);

        $semestre6 = new Semestre();
        $semestre6->setDescricao("2021/02");
        $semestre6->setDataInicio(\DateTime::createFromFormat('Y-m-d', '2021-07-15'));
        $semestre6->setDataTermino(\DateTime::createFromFormat('Y-m-d', '2021-12-18'));
        $manager->persist($semestre6);
        $this->addReference(self::SEMESTRE6_REFERENCE, $semestre6);

        $semestre7 = new Semestre();
        $semestre7->setDescricao("2022/01");
        $semestre7->setDataInicio(\DateTime::createFromFormat('Y-m-d', '2022-01-20'));
        $semestre7->setDataTermino(\DateTime::createFromFormat('Y-m-d', '2022-06-25'));
        $manager->persist($semestre7);
        $this->addReference(self::SEMESTRE7_REFERENCE, $semestre7);

        $semestre8 = new Semestre();
        $semestre8->setDescricao("2022/02");
        $semestre8->setDataInicio(\DateTime::createFromFormat('Y-m-d', '2022-07-15'));
        $semestre8->setDataTermino(\DateTime::createFromFormat('Y-m-d', '2022-12-18'));
        $manager->persist($semestre8);
        $this->addReference(self::SEMESTRE8_REFERENCE, $semestre8);

        $manager->flush();

    }


}
