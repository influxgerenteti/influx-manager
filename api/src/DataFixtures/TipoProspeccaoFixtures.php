<?php

namespace App\DataFixtures;

use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\Persistence\ObjectManager;
use App\Entity\Principal\TipoProspeccao;

class TipoProspeccaoFixtures extends Fixture
{

    public const TIPO_PROSPECCAO_REFERENCE = "prospeccao_rua";

    public function load(ObjectManager $manager)
    {
        $arquivoTipoProspeccaoCVS = __DIR__ . "/Data/tipo_prospeccao.csv";

        if (file_exists($arquivoTipoProspeccaoCVS) === true) {
            $arquivosoAberto = fopen($arquivoTipoProspeccaoCVS, "r");

            while (($data = fgetcsv($arquivosoAberto, 0, ";")) !== false) {
                unset($data[0]);
                $data = array_values($data);

                $tipoProspeccao = new TipoProspeccao();
                $tipoProspeccao->setDescricao($data[0]);
                $tipoProspeccao->setTipo($data[1]);
                if ((empty($data[2]) === false)) {
                    $tipoProspeccaoPaiORM = $this->getReference($data[2]);
                    $tipoProspeccao->setTipoPaiTipoProspeccao($tipoProspeccaoPaiORM);
                }

                $manager->persist($tipoProspeccao);

                if ((empty($data[3]) === false)) {
                    $this->addReference($data[3], $tipoProspeccao);
                }
            }

            $manager->flush();
        } else {
            die("Arquivo de importação de tipos prospeccao não encontrado. Favor entrar em contato com a GATILabs!");
        }//end if

        $manager->flush();

    }


}
