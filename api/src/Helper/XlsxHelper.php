<?php
namespace App\Helper;

use PhpOffice\PhpSpreadsheet\Reader\Xlsx;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

/**
 *
 * @author Luiz Antonio Costa
 */
class XlsxHelper
{
    /**
     *
     * @var \PhpOffice\PhpSpreadsheet\Reader\Xlsx
     */
    private $xlsxReaderObjeto;

    /**
     *
     * @var \PhpOffice\PhpSpreadsheet\Spreadsheet
     */
    private $spreadSheet;

    /**
     *
     * @var \PhpOffice\PhpSpreadsheet\Worksheet\Worksheet
     */
    private $workSheet;

    /**
     *
     * @var string
     */
    private $errorMsg;

    function __construct()
    {
        $this->xlsxReaderObjeto = new Xlsx();
    }

    /**
     * Realiza a tentativa de carregar o arquivo excel na memoria do php, caso não consiga, ele irá retornar como false.
     *
     * @param string $caminhoArquivo Diretorio com o arquivo(fullpath) exemplo: C:\Teste.txt
     *
     * @return boolean
     */
    public function carregarArquivoExcel($caminhoArquivo)
    {
        if (is_file($caminhoArquivo) === true) {
            try {
                $this->spreadSheet = $this->xlsxReaderObjeto->load($caminhoArquivo);
                $this->workSheet   = $this->spreadSheet->getActiveSheet();
                return true;
            } catch (\Exception $e) {
                $this->errorMsg = "Ocorreu um erro ao carregar o arquivo:" . $e->getMessage() . "\nParametro informado:" . $caminhoArquivo;
            }
        }

        return false;
    }

    /**
     * Retorna o valor da celula com base no Indice e Celula solicitado.
     * <b>Observacao:</b> Caso a celula nao exista, ira ser ativado o Throws do Framework.<br>
     * Caso necessite de um tratamento utilizar primeiramente o getCelula() e posteriormente getValue() no retorno do Objeto
     *
     * @param string $coluna Celula a ser consultado
     * @param number $indice Indice a ser selecionado
     *
     * @return mixed|number|boolean|string|NULL|\PhpOffice\PhpSpreadsheet\RichText\RichText
     */
    public function getValorCelulaIndice($coluna, $indice=0)
    {
        return $this->workSheet->getCell($coluna . $indice, false)->getValue();
    }

    /**
     * Retorna uma celula com base no Indice e Coluna passada
     *
     * @param string $coluna Celula a ser consultado
     * @param number $indice Indice a ser selecionado
     * @param boolean $flagDeveCriar Flag que indica se deve criar a celula caso nao encontre no arquivo, por padrao eh false.
     *
     * @return \PhpOffice\PhpSpreadsheet\Cell\Cell|NULL
     */
    public function getCelula($coluna, $indice=0, $flagDeveCriar=false)
    {
        try {
            return $this->workSheet->getCell($coluna . $indice, $flagDeveCriar);
        } catch (\Exception $e) {
            $this->errorMsg .= "Ocorreu ao tentar buscar a coluna:" . $coluna . " Indice:" . $indice;
            $this->errorMsg .= "\nA celula informada nao existe no excel.\nErro Framework:" . $e->getMessage();
        }

        return null;
    }

    /**
     * Retorna a quantidade maxima de linhas existente para a Coluna
     *
     * @param string $coluna Coluna a ser pesquisada, caso nao encontre, ele ira pesquisar em todas as colunas e retornara a maior entre elas
     *
     * @return number
     */
    public function getQuantidadeMaxLinhasColuna($coluna="")
    {
        return $this->workSheet->getHighestRow($coluna);
    }

    /**
     * Seleciona a aba informada, caso nao consiga acessar a funcao retornara como False.
     *
     * @param string $nome Nome da aba na planilha a ser selecionada
     *
     * @return boolean
     */
    public function setAbaAtivaPorNome($nome="")
    {
        try {
            $this->spreadSheet->setActiveSheetIndexByName($nome);
            $this->workSheet = $this->spreadSheet->getActiveSheet();
            return true;
        } catch (\Exception $e) {
            $this->errorMsg .= "Ocorreu um erro ao selecionar a aba:" . $nome . "\nA aba informada no arquivo não existe!\n";
            $this->errorMsg .= "Erro Framework:" . $e->getMessage();
        }

        return false;
    }

    /**
     * Retorna a mensagem de erro
     *
     * @return string
     */
    public function getErrorMsg()
    {
        return $this->errorMsg;
    }

    /**
     * Retorna o ponteiro para o SpreadSheet da classe
     *
     * @return \PhpOffice\PhpSpreadsheet\Spreadsheet
     */
    public function &getSpreadSheet()
    {
        return $this->spreadSheet;
    }

    /**
     * Retorna o ponteiro para o Worksheet da classe
     *
     * @return \PhpOffice\PhpSpreadsheet\Worksheet\Worksheet
     */
    public function &getWorkSheet()
    {
        return $this->workSheet;
    }


}
