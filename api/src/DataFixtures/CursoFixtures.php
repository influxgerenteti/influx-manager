<?php

namespace App\DataFixtures;

use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\Persistence\ObjectManager;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use App\Entity\Principal\Curso;

class CursoFixtures extends Fixture implements DependentFixtureInterface
{
    public const CURSO_INGLES_REFERENCE   = "curso_ingles";
    public const CURSO_ESPANHOL_REFERENCE = "curso_espanhol";
    public const CURSO_BUSINESS_REFERENCE = "curso_business";
    public const CURSO_COMUNICACAO_AVANCADA_REFERENCE = "curso_comunicacao_avancada";
    public const CURSO_JUNIOR_REFERENCE = "curso_junior";
    public const CURSO_KIDS_REFERENCE   = "curso_kids";
    public const CURSO_PREPARATORIO_TOEIC_REFERENCE = "curso_preparatorio_toeic";
    public const CURSO_TRAVELER_REFERENCE           = "curso_traveler";
    public const CURSO_VACATION_PLUS_REFERENCE      = "curso_vacation_plus";
    public const TIPO_ITEM_VALOR_CURSO_REFFERENCE   = "tipoItemValorCursoV";

    public function load(ObjectManager $manager)
    {
        $config = new Curso();
        $config->setIdioma($this->getReference(IdiomaFixtures::INGLES_REFERENCE));
        $config->setDescricao("Business");
        $config->setSigla($config->getDescricao());
        // $config->setIntensidadeIntensivo(1);
        $config->setServico($this->getReference(ItemFixtures::VALOR_CURSO));

        $manager->persist($config);
        $this->addReference(self::CURSO_BUSINESS_REFERENCE, $config);

        $config = new Curso();
        $config->setIdioma($this->getReference(IdiomaFixtures::INGLES_REFERENCE));
        $config->setDescricao("Comunicação Avançada");
        $config->setSigla($config->getDescricao());
        // $config->setIntensidadeIntensivo(1);
        // $config->setIntensidadeSemiIntensivo(1);
        $config->setServico($this->getReference(ItemFixtures::VALOR_CURSO));
        $manager->persist($config);
        $this->addReference(self::CURSO_COMUNICACAO_AVANCADA_REFERENCE, $config);

        $config = new Curso();
        $config->setIdioma($this->getReference(IdiomaFixtures::INGLES_REFERENCE));
        $config->setDescricao("Junior");
        $config->setSigla($config->getDescricao());
        $config->setServico($this->getReference(ItemFixtures::VALOR_CURSO));
        $manager->persist($config);
        $this->addReference(self::CURSO_JUNIOR_REFERENCE, $config);

        $config = new Curso();
        $config->setIdioma($this->getReference(IdiomaFixtures::INGLES_REFERENCE));
        $config->setDescricao("Kids");
        $config->setSigla($config->getDescricao());
        $config->setServico($this->getReference(ItemFixtures::VALOR_CURSO));
        $manager->persist($config);
        $this->addReference(self::CURSO_KIDS_REFERENCE, $config);

        $config = new Curso();
        $config->setIdioma($this->getReference(IdiomaFixtures::INGLES_REFERENCE));
        $config->setDescricao("Preparatório TOEIC");
        $config->setSigla($config->getDescricao());
        // $config->setIntensidadeIntensivo(1);
        $config->setServico($this->getReference(ItemFixtures::VALOR_CURSO));
        $manager->persist($config);
        $this->addReference(self::CURSO_PREPARATORIO_TOEIC_REFERENCE, $config);

        $config = new Curso();
        $config->setIdioma($this->getReference(IdiomaFixtures::ESPANHOL_REFERENCE));
        $config->setDescricao("Espanhol");
        $config->setSigla($config->getDescricao());
        // $config->setIntensidadeIntensivo(1);
        // $config->setIntensidadeSemiIntensivo(1);
        $config->setServico($this->getReference(ItemFixtures::VALOR_CURSO));
        $manager->persist($config);
        $this->addReference(self::CURSO_ESPANHOL_REFERENCE, $config);

        $config = new Curso();
        $config->setIdioma($this->getReference(IdiomaFixtures::INGLES_REFERENCE));
        $config->setDescricao("Inglês");
        $config->setSigla($config->getDescricao());
        // $config->setIntensidadeIntensivo(1);
        // $config->setIntensidadeSemiIntensivo(1);
        $config->setServico($this->getReference(ItemFixtures::VALOR_CURSO));
        $manager->persist($config);
        $this->addReference(self::CURSO_INGLES_REFERENCE, $config);

        $config = new Curso();
        $config->setIdioma($this->getReference(IdiomaFixtures::INGLES_REFERENCE));
        $config->setDescricao("Traveler");
        $config->setSigla($config->getDescricao());
        $config->setServico($this->getReference(ItemFixtures::VALOR_CURSO));
        $manager->persist($config);
        $this->addReference(self::CURSO_TRAVELER_REFERENCE, $config);

        $config = new Curso();
        $config->setIdioma($this->getReference(IdiomaFixtures::INGLES_REFERENCE));
        $config->setDescricao("Vacation Plus");
        $config->setSigla($config->getDescricao());
        $config->setServico($this->getReference(ItemFixtures::VALOR_CURSO));
        $manager->persist($config);
        $this->addReference(self::CURSO_VACATION_PLUS_REFERENCE, $config);
        $manager->flush();

    }

    public function getDependencies()
    {
        return [
            IdiomaFixtures::class,
            ItemFixtures::class,
        ];
    }


}
