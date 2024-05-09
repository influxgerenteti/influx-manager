<?php

namespace App\DataFixtures;

use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\Persistence\ObjectManager;
use Doctrine\Common\Persistence\ManagerRegistry;
use App\Entity\Log\ConfiguracoesTabelaLog;

class ConfiguracaoLogFixtures extends Fixture
{

    private $managerRegistry;

    public function __construct(ManagerRegistry $mr)
    {
        $this->managerRegistry = $mr;
    }

    public function load(ObjectManager $manager)
    {
        $basePrincipal     = $this->managerRegistry->getManager("base_principal");
        $baseLog           = $this->managerRegistry->getManager("base_log");
        $tabelasMetadaData = $basePrincipal->getMetadataFactory()->getAllMetadata();
        foreach ($tabelasMetadaData as $classeMetaData) {
            $nomeTabela         = $classeMetaData->getTableName();
            $configuracaoTabela = new ConfiguracoesTabelaLog();
            $configuracaoTabela->setNomeTabela($nomeTabela);
            $configuracaoTabela->setAtivo(false);
            $baseLog->persist($configuracaoTabela);
        }

        $baseLog->flush();
    }


}
