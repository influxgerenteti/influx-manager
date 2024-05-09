<?php

namespace App\DataFixtures;

use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\Persistence\ObjectManager;
use App\Entity\Principal\Papel;

class PapelFixtures extends Fixture
{
    public const PAPEL_ADMINISTRADOR_FRANQUEADORA = "admin_franqueadora";
    public const PAPEL_ADMINISTRADOR = "pppadmin";
    public const PAPEL_CONSULTOR     = "ppconsultor";
    public const PAPEL_GERENTE       = "ppgerente";
    public const PAPEL_COORDENADOR   = "ppcoordenador";

    public function load(ObjectManager $manager)
    {
        $papelAdministradorFranqueadora = new Papel();
        $papelAdministradorFranqueadora->setDescricao("Administrador Franqueadora");
        $papelAdministradorFranqueadora->setOculto(true);
        $manager->persist($papelAdministradorFranqueadora);
        $this->addReference(self::PAPEL_ADMINISTRADOR_FRANQUEADORA, $papelAdministradorFranqueadora);

        $papelAdministrador = new Papel();
        $papelAdministrador->setDescricao("Franqueado");
        $manager->persist($papelAdministrador);
        $this->addReference(self::PAPEL_ADMINISTRADOR, $papelAdministrador);

        $papelConsultor = new Papel();
        $papelConsultor->setDescricao("Consultor");
        $manager->persist($papelConsultor);
        $this->addReference(self::PAPEL_CONSULTOR, $papelConsultor);

        $papelGerente = new Papel();
        $papelGerente->setDescricao("Gerente");
        $manager->persist($papelGerente);
        $this->addReference(self::PAPEL_GERENTE, $papelGerente);

        $papelCoordenador = new Papel();
        $papelCoordenador->setDescricao("Coordenador");
        $manager->persist($papelCoordenador);
        $this->addReference(self::PAPEL_COORDENADOR, $papelCoordenador);

        $manager->flush();
    }


}
