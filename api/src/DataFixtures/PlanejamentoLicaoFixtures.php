<?php

namespace App\DataFixtures;

use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\Persistence\ObjectManager;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use App\Entity\Principal\PlanejamentoLicao;
use App\Entity\Principal\Licao;

class PlanejamentoLicaoFixtures extends Fixture implements DependentFixtureInterface
{

    public const PLANEJAMENTO_8AULAS  = "planejamento_licao8aulas";
    public const PLANEJAMENTO_32AULAS = "planejamento_licao32aulas";
    public const PLANEJAMENTO_1AULA   = "planejamento_licao1aula";
    public const LICAO = "licao";

    public function load(ObjectManager $manager)
    {
        $arquivoJSON = __DIR__ . "/Data/planejamentolicao_licao.json";
        if (file_exists($arquivoJSON) === true) {
            $arquivoAberto           = file_get_contents($arquivoJSON);
            $arquivoJSONDecodificado = json_decode($arquivoAberto);
            $dados = $arquivoJSONDecodificado->dados;
            foreach ($dados as $data) {
                $planejamentoLicao = new PlanejamentoLicao();
                $planejamentoLicao->setDescricao($data->descricao);
                $manager->persist($planejamentoLicao);
                $this->addReference($data->fixtureReference, $planejamentoLicao);
                $numero = 1;
                foreach ($data->licaos as $licao) {
                    $licaoORM = new Licao();
                    $licaoORM->setPlanejamentoLicao($planejamentoLicao);
                    $licaoORM->setDescricao($licao->descricao);
                    $licaoORM->setNumero($numero);
                    $manager->persist($licaoORM);
                    $this->addReference($licao->fixtureReferenceLicao, $licaoORM);
                    $numero++;
                }
            }

            $manager->flush();
        } else {
            die("Arquivo de importação de PlanejamentoLicaoXLicao não encontrado. Favor entrar em contato com a GATILabs!");
        }//end if

    }

    public function getDependencies()
    {
        return [
            ClassificacaoAlunoFixtures::class,
            PessoaFixtures::class,
        ];
    }


}
