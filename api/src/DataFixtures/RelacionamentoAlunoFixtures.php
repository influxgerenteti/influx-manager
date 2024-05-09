<?php

namespace App\DataFixtures;

use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\Persistence\ObjectManager;
use App\Entity\Principal\RelacionamentoAluno;

class RelacionamentoAlunoFixtures extends Fixture
{

    public const RELACIONAMENTO_ALUNO_AMIGO_REFERENCE = "relacionamento_aluno_amigo_fixture";
    public const RELACIONAMENTO_ALUNO_PAI_REFERENCE   = "relacionamento_aluno_pai_fixture";
    public const RELACIONAMENTO_ALUNO_MAE_REFERENCE   = "relacionamento_aluno_mae_fixture";

    public function load(ObjectManager $manager)
    {
        $config = new RelacionamentoAluno();
        $config->setDescricao("Amigo(a)");
        $manager->persist($config);
        $this->addReference(self::RELACIONAMENTO_ALUNO_AMIGO_REFERENCE, $config);

        $config = new RelacionamentoAluno();
        $config->setDescricao("Avô(ó)");
        $manager->persist($config);

        $config = new RelacionamentoAluno();
        $config->setDescricao("Empresa");
        $manager->persist($config);

        $config = new RelacionamentoAluno();
        $config->setDescricao("Escola");
        $manager->persist($config);

        $config = new RelacionamentoAluno();
        $config->setDescricao("Irmão(ã)");
        $manager->persist($config);

        $config = new RelacionamentoAluno();
        $config->setDescricao("Mãe");
        $manager->persist($config);
        $this->addReference(self::RELACIONAMENTO_ALUNO_MAE_REFERENCE, $config);

        $config = new RelacionamentoAluno();
        $config->setDescricao("Pai");
        $manager->persist($config);
        $this->addReference(self::RELACIONAMENTO_ALUNO_PAI_REFERENCE, $config);

        $config = new RelacionamentoAluno();
        $config->setDescricao("Responsável");
        $manager->persist($config);

        $config = new RelacionamentoAluno();
        $config->setDescricao("Tio(a)");
        $manager->persist($config);

        $config = new RelacionamentoAluno();
        $config->setDescricao("Outro");
        $manager->persist($config);

        $manager->flush();

    }


}
