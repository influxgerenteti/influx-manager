<?php

namespace App\DataFixtures;

use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\Persistence\ObjectManager;
use App\Entity\Principal\Turma;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;

class TurmaFixtures extends Fixture implements DependentFixtureInterface
{

    public const TURMA1_REFERENCE = "turma1";
    public const TURMA2_REFERENCE = "turma2";
    public const TURMA3_REFERENCE = "turma3";
    public const TURMA4_REFERENCE = "turma4";
    public const TURMA5_REFERENCE = "turma5";

    public function load(ObjectManager $manager)
    {
        $dataInicioFixture = \DateTime::createFromFormat("Y-m-d", "2019-10-15");
        $dataFimFixture    = \DateTime::createFromFormat("Y-m-d", "2020-12-30");

        $turma1 = new Turma();
        $turma1->setFranqueada($this->getReference(FranqueadaFixtures::FRANQUEADORA_REFERENCE));
        $turma1->setModalidadeTurma($this->getReference(ModalidadeTurmaFixtures::MODALIDADE_TURMA1_REFERENCE));
        $turma1->setDescricao("Seg&Qua 18:00/19:00 A 2019/02");
        $turma1->setCurso($this->getReference(CursoFixtures::CURSO_INGLES_REFERENCE));
        $turma1->setIntensidade("R");
        $turma1->setHorario($this->getReference(HorarioFixtures::HORARIO_REFERENCE));
        $turma1->setMaximoAlunos(3);
        $turma1->setSemestre($this->getReference(SemestreFixtures::SEMESTRE2_REFERENCE));
        $turma1->setDataInicio($dataInicioFixture);
        $turma1->setDataFim($dataFimFixture);
        $turma1->setSituacao("ABE");
        $turma1->setLivro($this->getReference("livro_book_1"));
        $manager->persist($turma1);
        $this->addReference(self::TURMA1_REFERENCE, $turma1);

        $turma2 = new Turma();
        $turma2->setFranqueada($this->getReference(FranqueadaFixtures::FRANQUEADA_REFERENCE));
        $turma2->setModalidadeTurma($this->getReference(ModalidadeTurmaFixtures::MODALIDADE_TURMA1_REFERENCE));
        $turma2->setDescricao("Ter&Qui 13:00/14:00 A 2019/02");
        $turma2->setCurso($this->getReference(CursoFixtures::CURSO_INGLES_REFERENCE));
        $turma2->setIntensidade("R");
        $turma2->setHorario($this->getReference(HorarioFixtures::HORARIO2_REFERENCE));
        $turma2->setMaximoAlunos(1);
        $turma2->setSemestre($this->getReference(SemestreFixtures::SEMESTRE2_REFERENCE));
        $turma2->setDataInicio($dataInicioFixture);
        $turma2->setDataFim($dataFimFixture);
        $turma2->setSituacao("FOR");
        $turma2->setLivro($this->getReference("livro_book_2"));
        $turma2->setFuncionario($this->getReference(FuncionarioFixtures::FUNCIONARIO5_REFERENCE));
        $turma2->setSalaFranqueada($this->getReference(SalaFranqueadaFixtures::SALA_FRANQUEADA5_REFERENCE));
        $manager->persist($turma2);
        $this->addReference(self::TURMA2_REFERENCE, $turma2);

        $turma3 = new Turma();
        $turma3->setFranqueada($this->getReference(FranqueadaFixtures::FRANQUEADA_REFERENCE));
        $turma3->setModalidadeTurma($this->getReference(ModalidadeTurmaFixtures::MODALIDADE_TURMA1_REFERENCE));
        $turma3->setDescricao("Qui&Sex 14:00/15:00 A 2019/02");
        $turma3->setCurso($this->getReference(CursoFixtures::CURSO_VACATION_PLUS_REFERENCE));
        $turma3->setIntensidade("R");
        $turma3->setHorario($this->getReference(HorarioFixtures::HORARIO3_REFERENCE));
        $turma3->setMaximoAlunos(6);
        $turma3->setSemestre($this->getReference(SemestreFixtures::SEMESTRE2_REFERENCE));
        $turma3->setDataInicio($dataInicioFixture);
        $turma3->setDataFim($dataFimFixture);
        $turma3->setSituacao("ABE");
        $turma3->setLivro($this->getReference("livro_vacation_plus_1"));
        $turma3->setFuncionario($this->getReference(FuncionarioFixtures::FUNCIONARIO5_REFERENCE));
        $turma3->setSalaFranqueada($this->getReference(SalaFranqueadaFixtures::SALA_FRANQUEADA5_REFERENCE));
        $manager->persist($turma3);
        $this->addReference(self::TURMA3_REFERENCE, $turma3);

        $turma4 = new Turma();
        $turma4->setFranqueada($this->getReference(FranqueadaFixtures::FRANQUEADA_REFERENCE));
        $turma4->setModalidadeTurma($this->getReference(ModalidadeTurmaFixtures::MODALIDADE_TURMA1_REFERENCE));
        $turma4->setDescricao("Sab 10:00/11:00 A 2019/02");
        $turma4->setCurso($this->getReference(CursoFixtures::CURSO_KIDS_REFERENCE));
        $turma4->setIntensidade("R");
        $turma4->setHorario($this->getReference(HorarioFixtures::HORARIO4_REFERENCE));
        $turma4->setMaximoAlunos(5);
        $turma4->setSemestre($this->getReference(SemestreFixtures::SEMESTRE2_REFERENCE));
        $turma4->setDataInicio($dataInicioFixture);
        $turma4->setDataFim($dataFimFixture);
        $turma4->setSituacao("FOR");
        $turma4->setLivro($this->getReference("livro_it_acamino_1_kids"));
        $turma4->setFuncionario($this->getReference(FuncionarioFixtures::FUNCIONARIO6_REFERENCE));
        $turma4->setSalaFranqueada($this->getReference(SalaFranqueadaFixtures::SALA_FRANQUEADA3_REFERENCE));
        $manager->persist($turma4);
        $this->addReference(self::TURMA4_REFERENCE, $turma4);

        $turma5 = new Turma();
        $turma5->setFranqueada($this->getReference(FranqueadaFixtures::FRANQUEADA_REFERENCE));
        $turma5->setModalidadeTurma($this->getReference(ModalidadeTurmaFixtures::MODALIDADE_TURMA1_REFERENCE));
        $turma5->setDescricao("Sab 10:00/11:00 B 2019/02");
        $turma5->setCurso($this->getReference(CursoFixtures::CURSO_KIDS_REFERENCE));
        $turma5->setIntensidade("R");
        $turma5->setHorario($this->getReference(HorarioFixtures::HORARIO4_REFERENCE));
        $turma5->setMaximoAlunos(5);
        $turma5->setSemestre($this->getReference(SemestreFixtures::SEMESTRE2_REFERENCE));
        $turma5->setDataInicio($dataInicioFixture);
        $turma5->setDataFim($dataFimFixture);
        $turma5->setSituacao("ENC");
        $turma5->setLivro($this->getReference("livro_it_acamino_1_kids"));
        $manager->persist($turma5);
        $this->addReference(self::TURMA5_REFERENCE, $turma5);

        $manager->flush();
    }

    public function getDependencies()
    {
        return [
            FranqueadaFixtures::class,
            ModalidadeTurmaFixtures::class,
            CursoFixtures::class,
            LivroFixtures::class,
            FuncionarioFixtures::class,
            SalaFranqueadaFixtures::class,
        ];
    }


}
