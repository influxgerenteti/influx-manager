<?php

namespace App\DataFixtures;

use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\Persistence\ObjectManager;
use App\Entity\Principal\MotivoNaoFechamentoConvenio;

class MotivoNaoFechamentoConvenioFixtures extends Fixture
{


    public function load(ObjectManager $manager)
    {
        $arquivoCSV = __DIR__ . "/Data/motivo_nao_fechamento_convenio.csv";
        if (file_exists($arquivoCSV) === true) {
            $arquivoAberto = fopen($arquivoCSV, "r");
            while (($data = fgetcsv($arquivoAberto, 0, ";")) !== false) {
                unset($data[0]);
                $data = array_values($data);
                $motivoNaoFechamentoConvenio = new MotivoNaoFechamentoConvenio();
                $motivoNaoFechamentoConvenio->setDescricao(trim($data[0]));
                $motivoNaoFechamentoConvenio->setSituacao($data[1]);
                $manager->persist($motivoNaoFechamentoConvenio);
            }

            $manager->flush();
        } else {
            die("Arquivo de importação de MotivoNaoFechamentoConvenio não encontrado. Favor entrar em contato com a GATILabs!");
        }//end if

        $manager->flush();
    }


}
