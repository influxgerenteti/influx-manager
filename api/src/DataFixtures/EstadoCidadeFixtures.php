<?php

namespace App\DataFixtures;

use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\Persistence\ObjectManager;
use App\Entity\Principal\Estado;
use App\Entity\Principal\Cidade;

class EstadoCidadeFixtures extends Fixture
{

    public const CIDADE_CWB = "cidade_cwb";
    public const ESTADO_PR  = "estado_pr";

    public function load(ObjectManager $manager)
    {
        $arquivoJSON = __DIR__ . "/Data/estados-cidades.json";
        if (file_exists($arquivoJSON) === true) {
            $arquivoAberto           = file_get_contents($arquivoJSON);
            $arquivoJSONDecodificado = json_decode($arquivoAberto);
            $estados = $arquivoJSONDecodificado->estados;
            foreach ($estados as $estado) {
                $estadoORM = new Estado();
                $estadoORM->setNome($estado->nome);
                $estadoORM->setSigla($estado->sigla);
                $manager->persist($estadoORM);

                if ($estado->sigla === 'PR') {
                    $this->addReference(self::ESTADO_PR, $estadoORM);
                }

                foreach ($estado->cidades as $indice => $cidade) {
                    $cidadeORM = new Cidade();
                    $cidadeORM->setEstado($estadoORM);
                    $cidadeORM->setNome($cidade);
                    $manager->persist($cidadeORM);

                    if ($cidade === "Curitiba" && $estado->sigla === "PR") {
                        $this->addReference(self::CIDADE_CWB, $cidadeORM);
                    }
                }
            }//end foreach

            $manager->flush();
        } else {
            die("Arquivo de importação de estado e cidades não encontrado. Favor entrar em contato com a GATILabs!");
        }//end if

    }


}
