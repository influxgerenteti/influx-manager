<?php

namespace App\DataFixtures;

use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\Persistence\ObjectManager;
use App\Entity\Principal\Contrato;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;

class ContratoFixtures extends Fixture implements DependentFixtureInterface
{

    public const CONTRATO_REFERENCE = "contrato";

    public function load(ObjectManager $manager)
    {
        $contrato = new Contrato();
        $contrato->setFranqueada($this->getReference(FranqueadaFixtures::FRANQUEADA_REFERENCE));
        $contrato->setAluno($this->getReference(AlunoFixtures::ALUNO_REFERENCE));
        $contrato->setBolsista(1);
        $contrato->setSequenciaContrato(1);
        $contrato->setSituacao("V");
        $contrato->setTipoContrato("M");
        $contrato->setModalidadeTurma($this->getReference(ModalidadeTurmaFixtures::MODALIDADE_TURMA1_REFERENCE));
        $contrato->setTurma($this->getReference(TurmaFixtures::TURMA2_REFERENCE));
        $contrato->setResponsavelVendaFuncionario($this->getReference(FuncionarioFixtures::FUNCIONARIO6_REFERENCE));
        $contrato->setResponsavelCarteiraFuncionario($this->getReference(FuncionarioFixtures::FUNCIONARIO6_REFERENCE));
        $contrato->setResponsavelFinanceiroPessoa($this->getReference(PessoaFixtures::PESSOA8_REFERENCE));
        $contrato->setLivro($this->getReference("livro_book_1"));
        $contrato->setCurso($this->getReference(CursoFixtures::CURSO_INGLES_REFERENCE));
        $contrato->setSemestre($this->getReference(SemestreFixtures::SEMESTRE2_REFERENCE));
        $manager->persist($contrato);
        $this->addReference(self::CONTRATO_REFERENCE, $contrato);
        $manager->flush();
    }

    public function getDependencies()
    {
        return [
            FranqueadaFixtures::class,
            AlunoFixtures::class,
            TurmaFixtures::class,
            FuncionarioFixtures::class,
            ModalidadeTurmaFixtures::class,
            PessoaFixtures::class,
            LivroFixtures::class,
            CursoFixtures::class,
            SemestreFixtures::class,
        ];
    }


}
