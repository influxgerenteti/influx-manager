<?php

namespace App\Command;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

class MakeModuloVuejsCommand extends Command
{
    protected static $defaultName = 'make:modulo-vuejs';

    protected static $diretorioTemplateJS = './src/Command/templates/js/';

    protected static $diretorioJS = './assets/js/';

    protected function configure()
    {
        $this
            ->setDescription('Cria um novo modulo VueJS')
            ->addArgument('nomeModulo', InputArgument::REQUIRED, 'Nome do novo Modulo VUEJS Sensitive case(ex: MeuNovoModulo)')
            ->addArgument('nomeModuloPai', InputArgument::REQUIRED, 'Nome do pai do novo modulo')
            ->addArgument('nomePasta', InputArgument::REQUIRED, 'Nome da pasta dos modulos');
    }

    /**
     * Move os arquivos para os diretorios
     *
     * @param string $nomePasta Nome do modulo
     * @param string $tipo Se eh store ou view
     * @param string $mensagemErro Ponteiro para retornar msg de erro
     * @param array $arquivos Array de arquivos
     *
     * @return boolean
     */
    private function moveArquivosParaDiretorioJS($nomePasta, $tipo, &$mensagemErro, $arquivos)
    {
        $diretorioCompleto = self::$diretorioJS . $tipo . DIRECTORY_SEPARATOR . $nomePasta . DIRECTORY_SEPARATOR;
        if (mkdir($diretorioCompleto, 0777, true) === true) {
            foreach ($arquivos as $nomeArquivo => $arquivo) {
                $fp = fopen($diretorioCompleto . $nomeArquivo, "a");
                fwrite($fp, $arquivo);
                fclose($fp);
            }

            return true;
        } else {
            $mensagemErro = "Nao foi possivel criar o diretorio: " . $diretorioCompleto . " \nCheque suas permissoes de escrita e leitura!!";
            return false;
        }
    }

    /**
     * Cria os arquivos de store
     *
     * @param string $nomeModulo Nome do modulo
     * @param string $nomePasta Nome da pasta
     * @param string $mensagemErro Ponteiro que contem a msg de erro
     *
     * @return boolean
     */
    private function criaStores($nomeModulo, $nomePasta, &$mensagemErro)
    {
        $actionJS    = file_get_contents(self::$diretorioTemplateJS . "store/actions.tpl");
        $indexJS     = file_get_contents(self::$diretorioTemplateJS . "store/index.tpl");
        $mutationsJS = file_get_contents(self::$diretorioTemplateJS . "store/mutations.tpl");
        $stateJS     = file_get_contents(self::$diretorioTemplateJS . "store/state.tpl");

        $actionJS = str_replace("$(nomeModulo)", $nomeModulo, $actionJS);

        $arquivos = [
            "actions.js"   => $actionJS,
            "index.js"     => $indexJS,
            "mutations.js" => $mutationsJS,
            "state.js"     => $stateJS,
        ];

        return $this->moveArquivosParaDiretorioJS($nomePasta, "store", $mensagemErro, $arquivos);
    }

    private function criaViews($nomeModulo, $nomeModuloPai, $nomePasta, $nomeModuloSensitiveCase, &$mensagemErro)
    {
        $listaVUE      = file_get_contents(self::$diretorioTemplateJS . "views/Lista.tpl");
        $formularioVUE = file_get_contents(self::$diretorioTemplateJS . "views/Formulario.tpl");

        $listaVUE      = str_replace("$(nomeModulo)", $nomeModulo, $listaVUE);
        $listaVUE      = str_replace("$(nomeModuloSensitiveCase)", $nomeModuloSensitiveCase, $listaVUE);
        $formularioVUE = str_replace("$(nomeModulo)", $nomeModulo, $formularioVUE);
        $formularioVUE = str_replace("$(nomePasta)", $nomePasta, $formularioVUE);
        $formularioVUE = str_replace("$(nomeModuloSensitiveCase)", $nomeModuloSensitiveCase, $formularioVUE);
        $formularioVUE = str_replace("$(nomeModuloPai)", $nomeModuloPai, $formularioVUE);

        $arquivos = [
            "Lista.vue"      => $listaVUE,
            "Formulario.vue" => $formularioVUE,
        ];

        return $this->moveArquivosParaDiretorioJS($nomePasta, "views", $mensagemErro, $arquivos);
    }

    private function adicionaRouter($nomeModuloPai, $nomeModulo, $nomePasta)
    {
        $nomeModuloLowerCase = strtolower($nomeModulo);
        $rota = "
    {
      path: '/{$nomeModuloPai}/{$nomePasta}',
      meta: { label: '{$nomeModulo}' },
      component: Full,
      beforeEnter: routeChanged,
      children: [
        {
          path: '',
          component: Lista{$nomeModulo},
          meta: { label: 'Lista' },
          name: '{$nomeModuloLowerCase}-lista'
        },
        {
          path: 'alterar/:id',
          component: Formulario{$nomeModulo},
          meta: { label: 'Atualizar' },
          name: '{$nomeModuloLowerCase}-alterar'
        },
        {
          path: 'adicionar',
          component: Formulario{$nomeModulo},
          meta: { label: 'Adicionar' },
          name: '{$nomeModuloLowerCase}-adicionar'
        }
      ]
    }// ComponenteGenerico
";
        $comentarioNovoModulo        = "\n/* " . $nomeModulo . " */";
        $importaListaNovoModulo      = "import Lista" . $nomeModulo . " from '../views/" . $nomePasta . "/Lista'";
        $importaFormularioNovoModulo = "import Formulario" . $nomeModulo . " from '../views/" . $nomePasta . "/Formulario'\n";
        $importCompleto = $comentarioNovoModulo . "\n" . $importaListaNovoModulo . "\n" . $importaFormularioNovoModulo . "\n// componente generico\n";

        $modoLeitura      = fopen(self::$diretorioJS . "router/index.js", 'r');
        $modoEscrita      = fopen(self::$diretorioJS . "router/index.js.tmp", 'w');
        $linhaSobrescrita = false;
        while (feof($modoLeitura) === false) {
            $linha = fgets($modoLeitura);
            if (stristr($linha, '// componente generico') !== false) {
                $linha = $importCompleto;
            } else if (stristr($linha, '// ComponenteGenerico') !== false) {
                $linha            = "    },\n" . $rota;
                $linhaSobrescrita = true;
            }

            fputs($modoEscrita, $linha);
        }

        fclose($modoLeitura);
        fclose($modoEscrita);
        if ($linhaSobrescrita === true) {
            rename(self::$diretorioJS . "router/index.js.tmp", self::$diretorioJS . "router/index.js");
        } else {
            unlink(self::$diretorioJS . "router/index.js.tmp");
        }

        return $linhaSobrescrita;
    }

    private function adicionaStore($nomeModuloSensitiveCase, $nomePasta)
    {
        $modoLeitura      = fopen(self::$diretorioJS . "store/index.js", 'r');
        $modoEscrita      = fopen(self::$diretorioJS . "store/index.js.tmp", 'w');
        $linhaSobrescrita = false;
        while (feof($modoLeitura) === false) {
            $linha = fgets($modoLeitura);
            if (stristr($linha, '// ComponenteGenerico') !== false) {
                $linha = "import " . $nomeModuloSensitiveCase . " from './" . $nomePasta . "'\n//ComponenteGenerico\n";
            } else if (stristr($linha, '    helpHint') !== false) {
                $linha            = $nomeModuloSensitiveCase . ",\n    helpHint\n";
                $linhaSobrescrita = true;
            }

            fputs($modoEscrita, $linha);
        }

        fclose($modoLeitura);
        fclose($modoEscrita);
        if ($linhaSobrescrita === true) {
            rename(self::$diretorioJS . "store/index.js.tmp", self::$diretorioJS . "store/index.js");
        } else {
            unlink(self::$diretorioJS . "store/index.js.tmp");
        }

        return $linhaSobrescrita;
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $io            = new SymfonyStyle($input, $output);
        $nomeModulo    = ucwords(trim($input->getArgument("nomeModulo")));
        $nomeModuloPai = trim($input->getArgument("nomeModuloPai"));
        $nomePasta     = trim($input->getArgument("nomePasta"));
        $nomeModuloSensitiveCase = lcfirst($nomeModulo);
        $mensagemErro            = "";

        if ($this->criaStores($nomeModulo, $nomePasta, $mensagemErro) === true) {
            if ($this->criaViews($nomeModulo, $nomeModuloPai, $nomePasta, $nomeModuloSensitiveCase, $mensagemErro) === true) {
                if ($this->adicionaRouter($nomeModuloPai, $nomeModulo, $nomePasta) === true) {
                    if ($this->adicionaStore($nomeModuloSensitiveCase, $nomePasta) === true) {
                        $retornoLint = exec("npm run lint");
                        if ($retornoLint !== false) {
                            $io->success('Novo modulo VUEJS criado com sucesso!\nNao esquecer de adicionar na fixtures de Modulo!(ModuloFixtures)');
                        } else {
                            $io->error("Nao foi possivel rodar o lint, possivelmente por causa de permissao do usuario, porem os modulos foram criados com sucesso!");
                        }
                    } else {
                        $io->error("Nao foi possivel adicionar o novo modulo no store");
                    }
                } else {
                    $io->error("Nao foi possivel adicionar o novo modulo no router");
                }
            } else {
                $io->error($mensagemErro);
            }
        } else {
            $io->error($mensagemErro);
        }//end if
    }


}
