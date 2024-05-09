<?php
namespace App\Helper;

use PHPJasper\PHPJasper;

/**
 *
 * @author Luiz Antonio Costa
 */
class JasperHelper
{
    private const CHAVE_PARAMETROS_JASPER    = "params";
    private const CHAVE_LOCALE_JASPER        = "locale";
    private const CHAVE_FORMATO_SAIDA_JASPER = "format";
    private const CHAVE_DATABASE_JASPER      = "db_connection";
    private const CHAVE_CAMINHO_STR          = "caminhoCompleto";
    private const CHAVE_NOME_ARQUIVO_STR     = "nomeCompleto";

    /**
     *
     * @var string caminho da pasta raiz para localizacao do jrxml
     */
    private $relatorios_jrxml = "/src/Reports/jrxml/";

    /**
     *
     * @var string caminho da pasta raiz para localizacao do jasper
     */
    private $relatorios_jasper = "/src/Reports/jasper/";

    /**
     *
     * @var string caminho da pasta/arquivo raiz para onde salvar o relatório gerado
     */
    private $relatorios_gerados = "/public/relatorios/";

    /**
     *
     * @var array configuracao a ser passado para geracao de relatorio do jasper
     */
    private $options = [];

    /**
     *
     * @var array Localizacao dos relatorios jrxml
     */
    private $listaRelatoriosLocalJrxml = [];

    /**
     *
     * @var array Localizacao dos relatorios jasper
     */
    private $listaRelatoriosLocalCompilados = [];

    /**
     *
     * @var \PHPJasper\PHPJasper
     */
    private $jasperObject = null;

    /**
     * Monta o array com todos os relatorios disponibilizados no sistema
     *
     * @param string $caminho
     * @param boolean $bCompilados
     */
    private function preparaListaLocalRelatorios($caminho, $bCompilados=false)
    {
        $arquivos = array_diff(scandir($caminho), ['..', '.']);
        foreach ($arquivos as $chave => $valor) {
            if ($bCompilados === true) {
                $this->listaRelatoriosLocalCompilados[] = [
                    self::CHAVE_CAMINHO_STR      => $caminho . $valor,
                    self::CHAVE_NOME_ARQUIVO_STR => $valor,
                ];
            } else {
                $this->listaRelatoriosLocalJrxml[] = [
                    self::CHAVE_CAMINHO_STR      => $caminho . $valor,
                    self::CHAVE_NOME_ARQUIVO_STR => $valor,
                ];
            }
        }
    }

    /**
     * Verifica se todos os parametros do relatorios estao ok
     *
     * @param array $parametros
     *
     * @return boolean
     */
    private function verificaParametrosRelatorios($parametros)
    {
        $bRetorno = true;
        if (isset($this->options[self::CHAVE_PARAMETROS_JASPER]) === true) {
            foreach ($parametros as $valor) {
                $bNaoEncontrado = true;
                foreach ($this->options[self::CHAVE_PARAMETROS_JASPER] as $chave => $aloha) {
                    if (strripos($valor, $chave) !== false) {
                        $bNaoEncontrado = false;
                        break;
                    }
                }

                if ($bNaoEncontrado === true) {
                    $bRetorno = false;
                    break;
                }
            }
        }

        return $bRetorno;
    }

    /**
     * Busca caminho completo do relatorio
     *
     * @param string $relatorio
     * @param string $bJrxml
     *
     * @return string
     */
    private function buscaCaminhoRelatorioPorNome($relatorio, $bJrxml=false)
    {
        $caminhoStr = "";
        if ($bJrxml === true) {
            foreach ($this->listaRelatoriosLocalJrxml as $valor) {
                if ($valor[self::CHAVE_NOME_ARQUIVO_STR] === $relatorio) {
                    $caminhoStr = $valor[self::CHAVE_CAMINHO_STR];
                }
            }
        } else {
            foreach ($this->listaRelatoriosLocalCompilados as $valor) {
                if ($valor[self::CHAVE_NOME_ARQUIVO_STR] === $relatorio) {
                    $caminhoStr = $valor[self::CHAVE_CAMINHO_STR];
                }
            }
        }

        return $caminhoStr;
    }

    /**
     * Busca os parametros do relatorio informado
     *
     * @param string $relatorio
     *
     * @return mixed|array
     */
    public function buscaParametrosRelatorio($relatorio)
    {
        $relatorio        = str_replace(".jasper", ".jrxml", $relatorio);
        $caminhoRelatorio = $this->buscaCaminhoRelatorioPorNome($relatorio, true);
        $parametros       = $this->jasperObject->listParameters($caminhoRelatorio)->execute();
        return $parametros;
    }

    /**
     *
     * @param string $caminhoRoot Caminho do diretorio(ideia eh pegar, do root, ou seja, fora do public)
     */
    public function __construct($caminhoRoot)
    {
        $this->jasperObject       = new PHPJasper();
        $this->relatorios_jasper  = $caminhoRoot . $this->relatorios_jasper;
        $this->relatorios_jrxml   = $caminhoRoot . $this->relatorios_jrxml;
        $this->relatorios_gerados = $caminhoRoot . $this->relatorios_gerados;
        $this->options[self::CHAVE_LOCALE_JASPER] = 'pt_BR';
        $this->preparaListaLocalRelatorios($this->relatorios_jrxml);
        $this->preparaListaLocalRelatorios($this->relatorios_jasper, true);
    }

    /**
     * Configura a base de dados para enviar ao relatorio o formato do array deve possuir o formato:
     * [
     * <br>
     * 'driver' => 'mysql'
     * <br>
     * 'username' => 'DB_USERNAME',
     * <br>
     * 'password' => 'DB_PASSWORD',
     * <br>
     * 'host' => 'DB_HOST',
     * <br>
     * 'database' => 'DB_DATABASE',
     * <br>
     * 'port' => 'DB_PORT'
     * <br>
     * ]
     *
     * @param array $configBanco
     */
    public function setConexaoBanco(array $configBanco)
    {
        $this->options[self::CHAVE_DATABASE_JASPER] = $configBanco;
    }

    /**
     * Configura os parametros a serem passados para o relatorios, exemplo:
     * [
     * <br>
     * 'nome' => 'Joselildo Lino'
     * <br>
     * 'idade' => '999',
     * <br>
     * 'email' => 'soulindo@cr7.com',
     * <br>
     * 'cpf' => '99999999999',
     * <br>
     * 'usuario' => 'cr7',
     * <br>
     * 'senha' => 'digdin'
     * <br>
     * ]
     *
     * @param array $parametros
     */
    public function setParametrosRelatorios(array $parametros)
    {
        $this->options[self::CHAVE_PARAMETROS_JASPER] = $parametros;
    }

    /**
     * Configura os formatos de geracao do relatorio
     * Formatos suportados: 'pdf', 'rtf', 'xls', 'xlsx', 'docx', 'odt', 'ods', 'pptx', 'csv', 'html', 'xhtml', 'xml', 'jrprint'
     *
     * @param array $formatos
     */
    public function setFormatosDeSaida(array $formatos)
    {
        $this->options[self::CHAVE_FORMATO_SAIDA_JASPER] = $formatos;
    }

    /**
     * Formatos suportados de locale, formatos suportados estao no site
     *
     * @param string $locale
     *
     * @see https://www.oracle.com/technetwork/java/javase/java8locales-2095355.html
     */
    public function setLocale(string $locale)
    {
        $this->options[self::CHAVE_LOCALE_JASPER] = $locale;
    }

    /**
     * Altera o caminho padrão dos relatórios gerados
     * "/public/relatorios/": salvará o relatório sempre com o nome do arquivo .jasper, substituindo se já existir.
     * "/public/relatorios/nome_arquivo": salvará o relatório com o nome fornecido seguido da extensão solicitada.
     *
     * @param string $novoCaminho
     */
    public function setRelatoriosGerados (string $novoCaminho)
    {
        $this->relatorios_gerados = $novoCaminho;
    }

    /**
     * Apenas compila todos os arquivos JRXML do diretorio e joga eles para o diretorio de relatorios compilados do Jasper
     */
    public function compilaTodosRelatoriosJRXML()
    {
        foreach ($this->listaRelatoriosLocalJrxml as $arquivo) {
            $this->jasperObject->compile($arquivo[self::CHAVE_CAMINHO_STR], $this->relatorios_jasper)->execute();
        }
    }

    /**
     * Processa o relatorio
     *
     * @param string $nomeCompletoRelatorio
     * @param string $mensagemErro
     *
     * @return boolean
     */
    public function processaRelatorio($nomeCompletoRelatorio, &$mensagemErro)
    {
        $relatorio = $this->buscaCaminhoRelatorioPorNome($nomeCompletoRelatorio);
        try {
            $parametrosRelatorio = $this->buscaParametrosRelatorio($nomeCompletoRelatorio);
            if ($this->verificaParametrosRelatorios($parametrosRelatorio) === true) {
                // !Linhas para debuggar se os parametros de entrada do relatório estão OK
                // var_dump($relatorio);
                // var_dump($this->relatorios_gerados);
                // var_dump($this->options);die();
                $this->jasperObject->process($relatorio, $this->relatorios_gerados, $this->options)->execute();
                return true;
            } else {
                $parametrosPassados = "";
                if (isset($this->options[self::CHAVE_PARAMETROS_JASPER]) === true) {
                    $parametrosPassados = print_r($this->options[self::CHAVE_PARAMETROS_JASPER], true);
                }

                $mensagemErro = "Ausência de parametros no relatorio: " . $nomeCompletoRelatorio . " os parametros passados foram: " . $parametrosPassados . " os parametros aceito pelo relatorio são:" . print_r($parametrosRelatorio, true);
                // !Atenção! O jasperObject->output() mostra dados sensíveis (incluindo senha/usuário do banco de dados), a linha abaixo
                // !só deve ser usado no servidor local para desenvolvimento!
                // $mensagemErro .= " " . $this->jasperObject->output();
                return false;
            }
        } catch (\Exception $e) {
            $mensagemErro .= "Erro ao tentar gerar relatório.\n";
            $mensagemErro .= $e->getMessage();
            // !Atenção! O jasperObject->output() mostra dados sensíveis (incluindo senha/usuário do banco de dados), a linha abaixo
            // !só deve ser usado no servidor local para desenvolvimento!
            // $mensagemErro .= $this->jasperObject->output();
            return false;
        }//end try
    }


}
