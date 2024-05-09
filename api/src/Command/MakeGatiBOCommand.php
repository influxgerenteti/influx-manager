<?php

namespace App\Command;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

class MakeGatiBOCommand extends Command
{

    protected static $defaultName = 'make:gati-bo';

    protected function configure()
    {
        $this
            ->setDescription('Cria um BO com base no template')
            ->addArgument('nameSpace', InputArgument::REQUIRED, 'Namespace do controller')
            ->addArgument('nomeBO', InputArgument::REQUIRED, 'Nome do BO')
            ->addArgument('nomeAutor', InputArgument::REQUIRED, 'Nome do Autor que esta criando Obs: para espaco utilizar  "." ou "_"');
    }

    private function formatarAutor($autor)
    {
        $autor = str_replace(".", " ", $autor);
        $autor = str_replace("_", " ", $autor);
        return $autor;
    }

    private function criarArquivo($raizDiretorioBO, $diretorio, $nomeFacade, $facade)
    {
        $fp = fopen($raizDiretorioBO . $diretorio . DIRECTORY_SEPARATOR . $nomeFacade . "BO.php", "a");
        fwrite($fp, $facade);
        fclose($fp);
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $io        = new SymfonyStyle($input, $output);
        $namespace = $input->getArgument('nameSpace');
        $nomeBO    = $input->getArgument('nomeBO');
        $nomeAutor = $this->formatarAutor($input->getArgument('nomeAutor'));
        $raizDiretorioBO = "./src/BO/";

        if ((empty($nomeBO) === false)
            && (empty($nomeAutor) === false)
            && (empty($namespace) === false)
        ) {
            // Facade
            $bo        = file_get_contents("./src/Command/templates/BO/BO.tpl");
            $bo        = str_replace("$(AUTOR)", $nomeAutor, $bo);
            $bo        = str_replace("$(nomeBO)", $nomeBO, $bo);
            $bo        = str_replace("$(namespace)", $namespace, $bo);
            $diretorio = str_replace("\\", DIRECTORY_SEPARATOR, $namespace);
            if (is_dir($raizDiretorioBO . $diretorio) === false) {
                if (mkdir($raizDiretorioBO . $diretorio, 0777, true) === true) {
                    self::criarArquivo($raizDiretorioBO, $diretorio, $nomeBO, $bo);
                } else {
                    $io->error("Nao foi possivel criar o diretorio, cheque suas permissoes de escrita e leitura do arquivo!");
                }
            } else {
                self::criarArquivo($raizDiretorioBO, $diretorio, $nomeBO, $bo);
            }
        }

        $io->success('Foi criado com sucesso o BO, favor alterar conforme desejar.');
    }


}
