<?php

namespace App\DataFixtures;

use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\Persistence\ObjectManager;
use App\Entity\Principal\ModalidadeTurma;

class ModalidadeTurmaFixtures extends Fixture
{

    public const MODALIDADE_TURMA1_REFERENCE = "modalidade_turma1";
    public const MODALIDADE_TURMA2_REFERENCE = "modalidade_turma2";
    public const MODALIDADE_TURMA3_REFERENCE = "modalidade_turma3";
    public const MODALIDADE_TURMA4_REFERENCE = "modalidade_turma4";

    public function load(ObjectManager $manager)
    {
        $modalidade_turma1 = new ModalidadeTurma();
        $modalidade_turma1->setDescricao("Turmas");
        $modalidade_turma1->setTipo("TUR");
        $manager->persist($modalidade_turma1);
        $this->addReference(self::MODALIDADE_TURMA1_REFERENCE, $modalidade_turma1);

        $modalidade_turma2 = new ModalidadeTurma();
        $modalidade_turma2->setDescricao("VIP");
        $modalidade_turma2->setTipo("VIP");
        $manager->persist($modalidade_turma2);
        $this->addReference(self::MODALIDADE_TURMA2_REFERENCE, $modalidade_turma2);

        $modalidade_turma3 = new ModalidadeTurma();
        $modalidade_turma3->setDescricao("Personal");
        $modalidade_turma3->setTipo("PER");
        $manager->persist($modalidade_turma3);
        $this->addReference(self::MODALIDADE_TURMA3_REFERENCE, $modalidade_turma3);

        $modalidade_turma4 = new ModalidadeTurma();
        $modalidade_turma4->setDescricao("Hybrid");
        $modalidade_turma4->setTipo("HYB");
        $manager->persist($modalidade_turma4);
        $this->addReference(self::MODALIDADE_TURMA4_REFERENCE, $modalidade_turma4);

        $manager->flush();
    }


}
