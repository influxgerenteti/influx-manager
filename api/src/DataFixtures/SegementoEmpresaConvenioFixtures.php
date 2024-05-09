<?php

namespace App\DataFixtures;

use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\Persistence\ObjectManager;
use App\Entity\Principal\SegmentoEmpresaConvenio;

class SegementoEmpresaConvenioFixtures extends Fixture
{


    public function load(ObjectManager $manager)
    {
        $arquivoCSV = __DIR__ . "/Data/segmento_empresa_convenio.csv";
        if (file_exists($arquivoCSV) === true) {
            $arquivoAberto = fopen($arquivoCSV, "r");
            while (($data = fgetcsv($arquivoAberto, 0, ";")) !== false) {
                unset($data[0]);
                $data          = array_values($data);
                $etapaConvenio = new SegmentoEmpresaConvenio();
                $etapaConvenio->setDescricao(trim($data[0]));
                $etapaConvenio->setSituacao($data[1]);
                $manager->persist($etapaConvenio);
            }

            $manager->flush();
        } else {
            die("Arquivo de importação de Segmento de Empresa Convenio não encontrado. Favor entrar em contato com a GATILabs!");
        }//end if

        $manager->flush();
    }


}
