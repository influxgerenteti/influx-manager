<?php

namespace App\Command;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

class MakeGatiFacadeCommand extends Command
{

    protected static $defaultName = 'make:gati-facade';

    protected function configure()
    {
        $this
            ->setDescription('Cria um facade com base no template')
            ->addArgument('nameSpace', InputArgument::REQUIRED, 'Namespace do controller')
            ->addArgument('nomeFacade', InputArgument::REQUIRED, 'Nome do Facade')
            ->addArgument('nomeAutor', InputArgument::REQUIRED, 'Nome do Autor que esta criando Obs: para espaco utilizar  "." ou "_"');
    }

    private function formatarAutor($autor)
    {
        $autor = str_replace(".", " ", $autor);
        $autor = str_replace("_", " ", $autor);
        return $autor;
    }

    private function criarArquivo($raizDiretorioFacade, $diretorio, $nomeFacade, $facade)
    {
        $fp = fopen($raizDiretorioFacade . $diretorio . DIRECTORY_SEPARATOR . $nomeFacade . "Facade.php", "a");
        fwrite($fp, $facade);
        fclose($fp);
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $io         = new SymfonyStyle($input, $output);
        $namespace  = $input->getArgument('nameSpace');
        $nomeFacade = $input->getArgument('nomeFacade');
        $nomeAutor  = $this->formatarAutor($input->getArgument('nomeAutor'));
        $raizDiretorioFacade = "./src/Facade/";

        if ((empty($nomeFacade) === false)
            && (empty($nomeAutor) === false)
            && (empty($namespace) === false)
        ) {
            // Facade
            $facade    = file_get_contents("./src/Command/templates/Facade/Facade.tpl");
            $facade    = str_replace("$(AUTOR)", $nomeAutor, $facade);
            $facade    = str_replace("$(nomeFacade)", $nomeFacade, $facade);
            $facade    = str_replace("$(namespace)", $namespace, $facade);
            $diretorio = str_replace("\\", DIRECTORY_SEPARATOR, $namespace);
            if (is_dir($raizDiretorioFacade . $diretorio) === false) {
                if (mkdir($raizDiretorioFacade . $diretorio, 0777, true) === true) {
                    self::criarArquivo($raizDiretorioFacade, $diretorio, $nomeFacade, $facade);
                } else {
                    $io->error("Nao foi possivel criar o diretorio, cheque suas permissoes de escrita e leitura do arquivo!");
                }
            } else {
                self::criarArquivo($raizDiretorioFacade, $diretorio, $nomeFacade, $facade);
            }
        }

        $io->success('Foi criado com sucesso o Facade, favor alterar conforme desejar.');
    }


}
