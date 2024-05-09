<?php

namespace App\DataFixtures;

use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\Persistence\ObjectManager;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use App\Entity\Principal\FormularioFollowUp;

class FormularioFollowUpFixtures extends Fixture implements DependentFixtureInterface
{


    public function load(ObjectManager $manager)
    {
        $arquivoCamposFormularioCVS = __DIR__ . "/Data/campos_formulario.csv";
        $arquivosFormularioCVS      = __DIR__ . "/Data/formulario_follow_up.csv";

        if ((file_exists($arquivoCamposFormularioCVS) === true) && (file_exists($arquivosFormularioCVS) === true)) {
            $arquivosFormularioAberto = fopen($arquivosFormularioCVS, "r");

            while (($data = fgetcsv($arquivosFormularioAberto, 0, ";")) !== false) {
                unset($data[0]);
                $data = array_values($data);

                $formulario = new FormularioFollowUp();
                $formulario->setUsuarioAlteracao($this->getReference(trim($data[0])));
                $formulario->setDescricaoFormulario($data[1]);
                $formulario->setTipoFormulario($data[2]);
                $formulario->setFollowUpInicial($data[3]);
                $formulario->setSituacao($data[4]);

                $manager->persist($formulario);
                $this->addReference($data[5], $formulario);
            }

            $manager->flush();
        } else {
            die("Arquivo de importação de formulario follow up não encontrado. Favor entrar em contato com a GATILabs!");
        }//end if
    }

    public function getDependencies()
    {
        return [UsuarioFixtures::class];
    }


}
