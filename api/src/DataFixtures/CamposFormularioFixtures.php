<?php

namespace App\DataFixtures;

use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\Persistence\ObjectManager;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use App\Entity\Principal\FormularioFollowUpCampos;

class CamposFormularioFixtures extends Fixture implements DependentFixtureInterface
{


    public function load(ObjectManager $manager)
    {
        $arquivoCamposFormularioCVS = __DIR__ . "/Data/campos_formulario.csv";
        $arquivosFormularioCVS      = __DIR__ . "/Data/formulario_follow_up.csv";

        if ((file_exists($arquivoCamposFormularioCVS) === true) && (file_exists($arquivosFormularioCVS) === true)) {
            $arquivosCamposFormularioAberto = fopen($arquivoCamposFormularioCVS, "r");

            while (($data = fgetcsv($arquivosCamposFormularioAberto, 0, ";")) !== false) {
                unset($data[0]);
                $data = array_values($data);

                $campoFormulario = new FormularioFollowUpCampos();
                $campoFormulario->setFormularioFollowUp($this->getReference(trim($data[0])));
                $campoFormulario->setNomeCampo($data[1]);
                $campoFormulario->setTextoLongo($data[2]);
                $manager->persist($campoFormulario);
            }

            $manager->flush();
        } else {
            die("Arquivo de importação de campos formulario de followUp não encontrado. Favor entrar em contato com a GATILabs!");
        }//end if
    }

    public function getDependencies()
    {
        return [FormularioFollowUpFixtures::class];
    }


}
