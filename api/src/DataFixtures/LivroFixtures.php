<?php

namespace App\DataFixtures;

use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\Persistence\ObjectManager;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use App\Entity\Principal\Livro;

class LivroFixtures extends Fixture implements DependentFixtureInterface
{


    public function load(ObjectManager $manager)
    {
        $arquivoCSV = __DIR__ . "/Data/livros.csv";
        if (file_exists($arquivoCSV) === true) {
            $arquivoAberto = fopen($arquivoCSV, "r");
            while (($data = fgetcsv($arquivoAberto, 0, ";")) !== false) {
                unset($data[0]);
                // remove todos os arrays brancos
                $data            = array_values($data);
                $config          = new Livro();
                $cursoORM        = $this->getReference(trim($data[0]));
                $itemORM         = $this->getReference(trim($data[1]));
                $planejamentoORM = $this->getReference(trim($data[2]));
                if ((isset($data[3]) === true) && (empty($data[3]) === false)) {
                    $config->setProximoLivro($this->getReference(trim($data[3])));
                }

                $config->addCurso($cursoORM);
                $config->setItem($itemORM);
                $config->setPlanejamentoLicao($planejamentoORM);
                $config->setDescricao($itemORM->getDescricao());
                $manager->persist($config);
                if ((isset($data[4]) === true) && (empty($data[4]) === false)) {
                    $this->addReference($data[4], $config);
                }
            }//end while

            $manager->flush();
        } else {
            die("Arquivo de importação de Livros não encontrado. Favor entrar em contato com a GATILabs!");
        }//end if
    }

    public function getDependencies()
    {
        return [
            CursoFixtures::class,
            IdiomaFixtures::class,
            SistemaAvaliacaoFixtures::class,
            ItemFixtures::class,
            PlanejamentoLicaoFixtures::class,
        ];
    }


}
