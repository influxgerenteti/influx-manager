<?php

namespace App\DataFixtures;

use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\Persistence\ObjectManager;
use App\Entity\Principal\Item;
use App\Entity\Principal\ItemFranqueada;
use App\Helper\SituacoesSistema;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;

class ItemFixtures extends Fixture implements DependentFixtureInterface
{

    public const ITEM_ACAMINO_1         = "it_acamino_1";
    public const ITEM_ACAMINO_2         = "it_acamino_2";
    public const ITEM_ACAMINO_3         = "it_acamino_3";
    public const ITEM_ACAMINO_4         = "it_acamino_4";
    public const ITEM_BOOK_1            = "it_book_1";
    public const ITEM_BOOK_2            = "it_book_2";
    public const ITEM_BOOK_3            = "it_book_3";
    public const ITEM_BOOK_4            = "it_book_4";
    public const ITEM_BOOK_5            = "it_book_5";
    public const ITEM_DOMINIO           = "it_dominio";
    public const ITEM_TACTICS_FOR_TOEIC = "it_tactics_for_toeic";
    public const ITEM_VACATION_PLUS_1   = "it_vacation_plus_1";
    public const ITEM_VACATION_PLUS_2   = "it_vacation_plus_2";
    public const ITEM_VACATION_PLUS_3   = "it_vacation_plus_3";
    public const ITEM_VACATION_PLUS_4   = "it_vacation_plus_4";
    public const ITEM_JUNIOR_1          = "it_junior_1";
    public const TAXA_MATRICULA         = "taxa_matricula";
    public const VALOR_CURSO            = "valor_curso";

    public function load(ObjectManager $manager)
    {
        $franqueada = $this->getReference(FranqueadaFixtures::FRANQUEADORA_REFERENCE);
        $arquivoCSV = __DIR__ . "/Data/items.csv";
        if (file_exists($arquivoCSV) === true) {
            $arquivoAberto = fopen($arquivoCSV, "r");
            while (($data = fgetcsv($arquivoAberto, 0, ";")) !== false) {
                $item = new Item();
                $item->setFranqueada($franqueada);
                $item->setDescricao($data[0]);

                $itemFranqueada = new ItemFranqueada();
                $itemFranqueada->setItem($item);
                $itemFranqueada->setFranqueada($franqueada);

                if ((isset($data[1]) === true) && (empty($data[1]) === false)) {
                    $item->setUnidadeMedida($data[1]);
                }

                if ((isset($data[2]) === true) && (empty($data[2]) === false)) {
                    $itemFranqueada->setSaldoEstoque($data[2]);
                }

                if ((isset($data[3]) === true) && (empty($data[3]) === false)) {
                    $itemFranqueada->setEstoqueMinimo($data[3]);
                }

                if ((isset($data[4]) === true) && (empty($data[4]) === false)) {
                    $itemFranqueada->setValorCompra($data[4]);
                }

                $itemFranqueada->setValorVenda($data[5]);
                $item->setPlanoConta($this->getReference($data[6]));
                $item->setTipoItem($this->getReference($data[7]));

                if ((isset($data[8]) === true) && (empty($data[8]) === false)) {
                    $this->addReference($data[8], $item);
                }

                $manager->persist($item);
            }//end while

            $manager->flush();
        } else {
            die("Arquivo de importação de items não encontrado. Favor entrar em contato com a GATILabs!");
        }//end if
    }

    public function getDependencies()
    {
        return [
            PlanoContaFixtures::class,
            FranqueadaFixtures::class,
            TipoItemFixtures::class,
        ];
    }


}
