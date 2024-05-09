<?php

namespace App\DataFixtures;

use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\Persistence\ObjectManager;
use App\Entity\Principal\TipoContato;

class TipoContatoFixtures extends Fixture
{

    public const TIPO_CONTATO_REFERENCE = "tipo_contato_telefone";

    public function load(ObjectManager $manager)
    {
        $arquivoTipoContatoCVS = __DIR__ . "/Data/tipo_contato.csv";

        if (file_exists($arquivoTipoContatoCVS) === true) {
            $arquivosoAberto = fopen($arquivoTipoContatoCVS, "r");

            while (($data = fgetcsv($arquivosoAberto, 0, ";")) !== false) {
                unset($data[0]);
                $data = array_values($data);

                $tipoContato = new TipoContato();
                $tipoContato->setNome($data[0]);
                $tipoContato->setTipo($data[1]);
                $manager->persist($tipoContato);
                $this->addReference($data[2], $tipoContato);
            }

            $manager->flush();
        } else {
            die("Arquivo de importação de tipos de contatos não encontrado. Favor entrar em contato com a GATILabs!");
        }//end if

        $manager->flush();
    }


}
