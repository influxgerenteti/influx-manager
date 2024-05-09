<?php

namespace App\Command;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

class MakeGatiControllerCommand extends Command
{

    protected static $defaultName = 'make:gati-controller';

    protected function configure()
    {
        $this
            ->setDescription('Criar um controller Rest com base no template')
            ->addArgument('nameSpace', InputArgument::REQUIRED, 'Namespace do controller')
            ->addArgument('nomeController', InputArgument::REQUIRED, 'Nome do Controller')
            ->addArgument('nomeRota', InputArgument::REQUIRED, 'Nome da Rota')
            ->addArgument('nomeAutor', InputArgument::REQUIRED, 'Nome do Autor que esta criando');
    }

    private function formatarAutor($autor)
    {
        $autor = str_replace(".", " ", $autor);
        $autor = str_replace("_", " ", $autor);
        return $autor;
    }

    private function criarArquivo($raizDiretorioController, $diretorio, $nomeController, $controller)
    {
        $fp = fopen($raizDiretorioController . $diretorio . DIRECTORY_SEPARATOR . $nomeController . DIRECTORY_SEPARATOR . $nomeController . "Controller.php", "a");
        fwrite($fp, $controller);
        fclose($fp);
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $io        = new SymfonyStyle($input, $output);
        $namespace = $input->getArgument('nameSpace');
        $nomeController = $input->getArgument('nomeController');
        $nomeRota       = $input->getArgument('nomeRota');
        $nomeAutor      = $this->formatarAutor($input->getArgument('nomeAutor'));
        $raizDiretorioController = "./src/Controller/";

        if ((empty($nomeController) === false)
            && (empty($namespace) === false)
            && (empty($nomeRota) === false)
            && (empty($nomeAutor) === false)
        ) {
            // Controller
            $controller = file_get_contents("./src/Command/templates/Controller/Controller.tpl");
            $controller = str_replace("$(AUTOR)", $nomeAutor, $controller);
            $controller = str_replace("$(nomeController)", $nomeController, $controller);
            $controller = str_replace("$(nomeRota)", $nomeRota, $controller);
            $controller = str_replace("$(namespace)", $namespace, $controller);
            $diretorio  = str_replace("\\", "/", $namespace);
            if (is_dir($raizDiretorioController . $diretorio . DIRECTORY_SEPARATOR . $nomeController) === false) {
                if (mkdir($raizDiretorioController . $diretorio . DIRECTORY_SEPARATOR . $nomeController, 0777, true) === true) {
                    self::criarArquivo($raizDiretorioController, $diretorio, $nomeController, $controller);
                } else {
                    $io->error("Nao foi possivel criar o diretorio, cheque suas permissoes de escrita e leitura do arquivo!");
                }
            } else {
                self::criarArquivo($raizDiretorioController, $diretorio, $nomeController, $controller);
            }
        }

        $io->success('Foi criado com sucesso o Controller, favor alterar conforme desejar.');

    }


}
