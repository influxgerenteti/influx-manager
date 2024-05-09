<?php

namespace App\DataFixtures;

use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\Persistence\ObjectManager;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use App\Entity\Principal\Aluno;
use App\Helper\SituacoesSistema;

class AlunoFixtures extends Fixture implements DependentFixtureInterface
{
    public const ALUNO_REFERENCE  = "aluno";
    public const ALUNO2_REFERENCE = "aluno2";
    public const ALUNO3_REFERENCE = "aluno3";
    public const ALUNO4_REFERENCE = "aluno4";

    public function load(ObjectManager $manager)
    {
        $aluno = new Aluno();
        $aluno->setClassificacaoAluno($this->getReference(ClassificacaoAlunoFixtures::CLASSIFICACAO_ALUNO_REFERENCE));
        $aluno->setPessoa($this->getReference(PessoaFixtures::PESSOA7_REFERENCE));
        $aluno->addInteressado($this->getReference(InteressadoFixtures::INTERESSADO1_REFERENCE));
        $aluno->setSituacao(SituacoesSistema::SITUACAO_ATIVO);
        $aluno->setResponsavelFinanceiroPessoa($this->getReference(PessoaFixtures::PESSOA8_REFERENCE));
        $aluno->setResponsavelFinanceiroRelacionamentoAluno($this->getReference(RelacionamentoAlunoFixtures::RELACIONAMENTO_ALUNO_PAI_REFERENCE));
        $aluno->setResponsavelDidaticoPessoa($this->getReference(PessoaFixtures::PESSOA8_REFERENCE));
        $aluno->setResponsavelDidaticoRelacionamentoAluno($this->getReference(RelacionamentoAlunoFixtures::RELACIONAMENTO_ALUNO_PAI_REFERENCE));
        $manager->persist($aluno);
        $this->addReference(self::ALUNO_REFERENCE, $aluno);

        $manager->flush();

    }

    public function getDependencies()
    {
        return [
            PessoaFixtures::class,
            ClassificacaoAlunoFixtures::class,
            RelacionamentoAlunoFixtures::class,
            InteressadoFixtures::class,
            TipoVisibilidadeFixtures::class,
        ];
    }


}
