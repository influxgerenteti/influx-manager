<?php

namespace App\DataFixtures;

use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\Persistence\ObjectManager;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use App\Entity\Principal\TurmaAula;

class TurmaAulaFixtures extends Fixture implements DependentFixtureInterface
{
    public const TURMA_AULA_REFERENCE = "turmaAula";

    public function load(ObjectManager $manager)
    {
        $data = \DateTime::createFromFormat("Y-m-d\TH:i:s.uP", "2019-05-01T12:00:00.000Z");

        $turmaAula = new TurmaAula();
        $turmaAula->setFranqueada($this->getReference(FranqueadaFixtures::FRANQUEADA_REFERENCE));
        $turmaAula->setTurma($this->getReference(TurmaFixtures::TURMA2_REFERENCE));
        $turmaAula->setDataAula($data);
        $turmaAula->setFinalizada(false);
        $turmaAula->setLicao($this->getReference(PlanejamentoLicaoFixtures::LICAO));
        $manager->persist($turmaAula);
        $this->addReference(self::TURMA_AULA_REFERENCE, $turmaAula);

        $manager->flush();

    }

    public function getDependencies()
    {
        return [
            FranqueadaFixtures::class,
            TurmaFixtures::class,
            PlanejamentoLicaoFixtures::class,
        ];
    }


}
