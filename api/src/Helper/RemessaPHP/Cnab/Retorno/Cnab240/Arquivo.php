<?php

namespace App\Helper\RemessaPHP\Cnab\Retorno\Cnab240;

use App\Helper\RemessaPHP\Cnab\Retorno\Linha;

class Arquivo implements \App\Helper\RemessaPHP\Cnab\Retorno\IArquivo
{
    private $content;

    public $header  = false;
    public $lotes   = [];
    public $linhas  = [];
    public $trailer = false;

    public $codigo_banco;
    public $layoutVersao;

    private $filename;

    public function __construct($codigo_banco, $filename, $layoutVersao=null)
    {
        $this->filename     = $filename;
        $this->layoutVersao = $layoutVersao;

        if (file_exists($this->filename) === false) {
            throw new \Exception("Arquivo não encontrado: {$this->filename}");
        }

        $this->content = file_get_contents($this->filename);

        $this->codigo_banco = (int) $codigo_banco;

        $linhas = explode("\r\n", $this->content);
        if (count($linhas) < 2) {
            $linhas = explode("\n", $this->content);
        }

        $this->header  = new HeaderArquivo($this);
        $this->trailer = new TrailerArquivo($this);

        $lastLote = null;

        $posLinha = 0;

        foreach ($linhas as $linha) {
            if (trim($linha) === false) {
                continue;
            }

            $linhaRetorno        = new Linha();
            $linhaRetorno->pos   = $posLinha++;
            $linhaRetorno->texto = $linha;

            $this->linhas[] = $linhaRetorno;

            $tipo_registro = substr($linha, 7, 1);
            if ($tipo_registro === '0') {
                // header
                $this->header->loadFromString($linha);
                $linhaRetorno->linhaCnab = $this->header;
            } else if ($tipo_registro === '1') {
                // header do lote
                if (is_null($lastLote) === false) {
                    $this->lotes[] = $lastLote;
                }

                $lastLote         = new Lote($this);
                $lastLote->header = new HeaderLote($this);
                $lastLote->header->loadFromString($linha);

                $linhaRetorno->linhaCnab = $lastLote->header;
            } else if ($tipo_registro === '2') {
                // registros iniciais do lote (opcional)
            } else if ($tipo_registro === '3') {
                // registros de detalhe - Segmentos
                if (is_null($lastLote) === false) {
                    $linhaRetorno->linhaCnab = $lastLote->insertSegmento($linha);
                }
            } else if ($tipo_registro === '4') {
                // registros finais do lote (opcional)
            } else if ($tipo_registro === '5') {
                // registro trailer do lote
                $lastLote->trailer = new TrailerLote($this);
                $lastLote->trailer->loadFromString($linha);
                $this->lotes[]           = $lastLote;
                $linhaRetorno->linhaCnab = $lastLote->trailer;
                $lastLote = null;
            } else if ($tipo_registro === '9') {
                // trailer do arquivo
                $this->trailer->loadFromString($linha);

                $linhaRetorno->linhaCnab = $this->trailer;
            }//end if
        }//end foreach
    }

    public function listDetalhes()
    {
        $detalhes = [];
        foreach ($this->lotes as $lote) {
            foreach ($lote->listDetalhes() as $detalhe) {
                $detalhes[] = $detalhe;
            }
        }

        return $detalhes;
    }

    /**
     * Retorna o numero da conta.
     *
     * @return string
     */
    public function getConta()
    {
        return $this->header->getConta();
    }

    /**
     * Retorna o digito de auto conferencia da conta.
     *
     * @return string
     */
    public function getContaDac()
    {
        return $this->header->getContaDac();
    }

    /**
     * Retorna o codigo do banco.
     *
     * @return string
     */
    public function getCodigoBanco()
    {
        return $this->header->codigo_banco;
    }

    /**
     * Retorna o dígito verificador da conta.
     *
     * @return string
     */
    public function getNumeroConta()
    {
        return $this->header->codigo_cedente;
    }

    /**
     * Retorna o dígito verificador da conta.
     *
     * @return string
     */
    public function getContaDv()
    {
        return $this->header->codigo_cedente_dv;
    }

    /**
     * Retorna a agencia da conta.
     *
     * @return string
     */
    public function getAgencia()
    {
        return $this->header->agencia;
    }

    /**
     * Retorna a data de geração do arquivo.
     *
     * @return \DateTime
     */
    public function getDataGeracao()
    {
        $data_geracao_str = $this->header->data_geracao;
        $format           = 'dmy';
        $format_printf    = '%06d';
        if (strlen($data_geracao_str) > 6) {
            $format        = 'dmY';
            $format_printf = '%08d';
        }

        if (empty($data_geracao_str) === true) {
            return false;
        }

        return \DateTime::createFromFormat($format, sprintf($format_printf, $data_geracao_str));
    }

    /**
     * Retorna o objeto DateTime da data crédito do arquivo
     * É melhor consultar no Detalhe a data de crédito, a caixa só informa no detalhe
     * (Esta função poderá ser removida, pois em alguns banco você só encontra esta data no detalhe).
     *
     * @return DateTime
     */
    public function getDataCredito()
    {
        $lote        = $this->lotes[0];
        $header_lote = $lote->header;

        if (empty($header_lote->data_credito) === true) {
            return false;
        }

        return \DateTime::createFromFormat('dmY', sprintf('%08d', $header_lote->data_credito));
    }

    public function getCodigoConvenio()
    {
        return $this->header->getCodigoConvenio();
    }


}
