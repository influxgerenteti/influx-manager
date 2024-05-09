<?php

namespace App\DataFixtures;

use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\Persistence\ObjectManager;
use App\Entity\Principal\ClassificacaoAluno;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;

class ClassificacaoAlunoFixtures extends Fixture implements DependentFixtureInterface
{

    public const CLASSIFICACAO_ALUNO_REFERENCE  = "classificacao_aluno";
    public const CLASSIFICACAO_ALUNO2_REFERENCE = "classificacao_aluno2";
    public const CLASSIFICACAO_ALUNO3_REFERENCE = "classificacao_aluno3";
    public const CLASSIFICACAO_ALUNO4_REFERENCE = "classificacao_aluno4";
    public const CLASSIFICACAO_ALUNO5_REFERENCE = "classificacao_aluno5";

    public function load(ObjectManager $manager)
    {
        $classificacaoAluno0 = new ClassificacaoAluno();
        $classificacaoAluno0->setNome("Especial");
        $classificacaoAluno0->setIcone("exclamation-triangle");
        $classificacaoAluno0->setFranqueada($this->getReference(FranqueadaFixtures::FRANQUEADORA_REFERENCE));
        $manager->persist($classificacaoAluno0);

        $classificacaoAluno = new ClassificacaoAluno();
        $classificacaoAluno->setNome("Especial Novo");
        $classificacaoAluno->setIcone("exclamation-triangle");
        $classificacaoAluno->setFranqueada($this->getReference(FranqueadaFixtures::FRANQUEADORA_REFERENCE));
        $manager->persist($classificacaoAluno);

        $classificacaoAluno2 = new ClassificacaoAluno();
        $classificacaoAluno2->setNome("Especial Star");
        $classificacaoAluno2->setIcone("exclamation-triangle");
        $classificacaoAluno2->setFranqueada($this->getReference(FranqueadaFixtures::FRANQUEADA_REFERENCE));
        $manager->persist($classificacaoAluno2);

        $classificacaoAluno3 = new ClassificacaoAluno();
        $classificacaoAluno3->setNome("Estável");
        $classificacaoAluno3->setIcone("heart");
        $classificacaoAluno3->setFranqueada($this->getReference(FranqueadaFixtures::FRANQUEADA_REFERENCE));
        $manager->persist($classificacaoAluno3);

        $classificacaoAluno4 = new ClassificacaoAluno();
        $classificacaoAluno4->setNome("Novo");
        $classificacaoAluno4->setIcone("laugh-beam");
        $classificacaoAluno4->setFranqueada($this->getReference(FranqueadaFixtures::FRANQUEADA_REFERENCE));
        $manager->persist($classificacaoAluno4);

        $classificacaoAluno5 = new ClassificacaoAluno();
        $classificacaoAluno5->setNome("Star");
        $classificacaoAluno5->setIcone("star");
        $classificacaoAluno5->setFranqueada($this->getReference(FranqueadaFixtures::FRANQUEADA_REFERENCE));
        $manager->persist($classificacaoAluno5);

        $classificacaoAluno6 = new ClassificacaoAluno();
        $classificacaoAluno6->setNome("Star Novo");
        $classificacaoAluno6->setIcone("star");
        $classificacaoAluno6->setFranqueada($this->getReference(FranqueadaFixtures::FRANQUEADA_REFERENCE));
        $manager->persist($classificacaoAluno6);

        $manager->flush();
        $this->addReference(self::CLASSIFICACAO_ALUNO_REFERENCE, $classificacaoAluno);
        $this->addReference(self::CLASSIFICACAO_ALUNO2_REFERENCE, $classificacaoAluno2);
        $this->addReference(self::CLASSIFICACAO_ALUNO3_REFERENCE, $classificacaoAluno3);
        $this->addReference(self::CLASSIFICACAO_ALUNO4_REFERENCE, $classificacaoAluno4);
        $this->addReference(self::CLASSIFICACAO_ALUNO5_REFERENCE, $classificacaoAluno5);
    }

    public function getDependencies()
    {
        return [
            FranqueadaFixtures::class,
        ];
    }


}
