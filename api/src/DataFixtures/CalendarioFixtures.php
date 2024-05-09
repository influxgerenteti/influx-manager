<?php

namespace App\DataFixtures;

use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\Persistence\ObjectManager;
use App\Entity\Principal\Calendario;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;

class CalendarioFixtures extends Fixture implements DependentFixtureInterface
{


    public function load(ObjectManager $manager)
    {
        $franqueada           = $this->getReference(FranqueadaFixtures::FRANQUEADORA_REFERENCE);
        $arquivoCalendarioCSV = __DIR__ . "/Data/calendario.csv";
        if (file_exists($arquivoCalendarioCSV) === true) {
            $arquivoCalendarioAberto = fopen($arquivoCalendarioCSV, "r");
            $i = 1;
            while (($data = fgetcsv($arquivoCalendarioAberto, 0, ";")) !== false) {
                $data = array_values($data);
                $cal  = new Calendario();
                $cal->setDescricao($data[0]);
                $dataInicio = \DateTime::createFromFormat("Y-m-d", $data[1]);
                $cal->setDataInicial($dataInicio);
                $cal->setFeriadoAnual(true);
                $cal->setFranqueada($franqueada);
                $manager->persist($cal);

                $i++;
            }

            $manager->flush();
        } else {
            die("Arquivo de importação de calendario não encontrado. Favor entrar em contato com a GATILabs!");
        }//end if
    }

    public function getDependencies()
    {
        return [
            FranqueadaFixtures::class,
        ];
    }


}
