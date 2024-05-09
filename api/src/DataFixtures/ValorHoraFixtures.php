<?php

namespace App\DataFixtures;

use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\Persistence\ObjectManager;
use App\Entity\Principal\ValorHora;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;

class ValorHoraFixtures extends Fixture  implements DependentFixtureInterface
{
    public const VALOR_HORA1_REFFERENCE = "valor_hora_1";
    public const VALOR_HORA2_REFFERENCE = "valor_hora_2";

    public function load(ObjectManager $manager)
    {
        $arquivoCSV = __DIR__ . "/Data/valor_horas.csv";
        if (file_exists($arquivoCSV) === true) {
            $arquivoAberto = fopen($arquivoCSV, "r");
            while (($data = fgetcsv($arquivoAberto, 0, ";")) !== false) {
                $valorHora        = new ValorHora();
                $franqueada_antes = preg_replace('/[\x00-\x1F\x80-\xFF]/', '', $data[0]);
                $valorHora->setFranqueada($this->getReference($franqueada_antes));
                $valorHora->setValorHoraLinhas($this->getReference($data[1]));
                $valorHora->setNivelInstrutor($this->getReference($data[2]));
                $valorHora->setValor($data[3]);
                $valorHora->setValorExtra($data[4]);
                $valorHora->setValorBonus($data[5]);
                $manager->persist($valorHora);
                if ($this->hasReference(self::VALOR_HORA1_REFFERENCE) === false) {
                    $this->addReference(self::VALOR_HORA1_REFFERENCE, $valorHora);
                } else if (($this->hasReference(self::VALOR_HORA1_REFFERENCE) === true) && ($this->hasReference(self::VALOR_HORA2_REFFERENCE) === false)) {
                    $this->addReference(self::VALOR_HORA2_REFFERENCE, $valorHora);
                }
            }

            $manager->flush();
        } else {
            die("Arquivo de importação de valor horas não encontrado. Favor entrar em contato com a GATILabs!");
        }//end if
    }

    public function getDependencies()
    {
        return [
            ValorHoraLinhasFixtures::class,
            FranqueadaFixtures::class,
        ];
    }


}
