<?php

namespace App\DataFixtures;

use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\Persistence\ObjectManager;
use App\Entity\Principal\ValorHoraLinhas;

class ValorHoraLinhasFixtures extends Fixture
{


    public function load(ObjectManager $manager)
    {
        $arquivoCSV = __DIR__ . "/Data/valor_horas_linhas.csv";
        if (file_exists($arquivoCSV) === true) {
            $arquivoAberto = fopen($arquivoCSV, "r");
            while (($data = fgetcsv($arquivoAberto, 0, ";")) !== false) {
                $valorHoraLinha = new ValorHoraLinhas();
                $valorHoraLinha->setDescricao($data[0]);
                $valorHoraLinha->setTipo($data[1]);
                $valorHoraLinha->setQuantidadeAlunosMinima($data[2]);
                $valorHoraLinha->setQuantidadeAlunosMaxima($data[3]);
                $valorHoraLinha->setTipoPagamento($data[4]);
                $this->addReference($data[5], $valorHoraLinha);
                $manager->persist($valorHoraLinha);
            }

            $manager->flush();
        } else {
            die("Arquivo de importação de valor horas não encontrado. Favor entrar em contato com a GATILabs!");
        }//end if
    }


}
