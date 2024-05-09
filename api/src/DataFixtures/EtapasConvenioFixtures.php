<?php

namespace App\DataFixtures;

use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\Persistence\ObjectManager;
use App\Entity\Principal\EtapasConvenio;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;

class EtapasConvenioFixtures extends Fixture implements DependentFixtureInterface
{


    public function load(ObjectManager $manager)
    {
        $arquivoCSV = __DIR__ . "/Data/etapas_convenio.csv";
        if (file_exists($arquivoCSV) === true) {
            $arquivoAberto = fopen($arquivoCSV, "r");
            while (($data = fgetcsv($arquivoAberto, 0, ";")) !== false) {
                unset($data[0]);
                $data          = array_values($data);
                $etapaConvenio = new EtapasConvenio();
                $etapaConvenio->setDescricao(trim($data[0]));
                $etapaConvenio->setNegociacaoParceriaWorkflow($this->getReference(trim($data[1])));
                $etapaConvenio->setRetiraFluxo($data[2]);
                $etapaConvenio->setParceriaFirmada($data[3]);
                $manager->persist($etapaConvenio);
            }

            $manager->flush();
        } else {
            die("Arquivo de importação de Etapas de Convenio não encontrado. Favor entrar em contato com a GATILabs!");
        }//end if

        $manager->flush();
    }

    public function getDependencies()
    {
        return [NegociacaoParceriaWorkflowFixtures::class];
    }


}
