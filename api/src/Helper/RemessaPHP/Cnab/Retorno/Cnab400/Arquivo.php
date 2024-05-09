<?php

namespace App\Helper\RemessaPHP\Cnab\Retorno\Cnab400;

use App\Helper\RemessaPHP\Cnab\Retorno\Linha;

class Arquivo implements \App\Helper\RemessaPHP\Cnab\Retorno\IArquivo
{
    private $content;

    public $header   = false;
    public $detalhes = [];
    public $linhas   = [];
    public $trailer  = false;
    public $layoutVersao;
    public $lines;
    public $codigo_banco;
    private $filename;

    public function __construct($codigo_banco, $filename, $layoutVersao=null)
    {
        $this->filename     = $filename;
        $this->layoutVersao = $layoutVersao;

        if (file_exists($this->filename) === false) {
            throw new Exception("Arquivo não encontrado: {$this->filename}");
        }

        $this->content = file_get_contents($this->filename);

        $this->codigo_banco = (int) $codigo_banco;

        $linhas = explode("\r\n", $this->content);
        if (count($linhas) < 2) {
            $linhas = explode("\n", $this->content);
        }

        $this->header  = new Header($this);
        $this->trailer = new Trailer($this);

        $posLinha = 0;

        foreach ($linhas as $linha) {
            if (trim($linha) === false) {
                continue;
            }

            $linhaRetorno        = new Linha();
            $linhaRetorno->pos   = $posLinha++;
            $linhaRetorno->texto = $linha;

            $this->linhas[] = $linhaRetorno;

            $tipo_registro = substr($linha, 0, 1);

            if ($tipo_registro === '0' && empty($linha) === false) {
                $this->header->loadFromString($linha);

                $linhaRetorno->linhaCnab = $this->header;
            } else if (in_array((int) $tipo_registro, [1, 7]) === true) {
                $detalhe = new Detalhe($this);
                $detalhe->loadFromString($linha);
                $this->detalhes[] = $detalhe;

                $linhaRetorno->linhaCnab = $detalhe;
            } else if ($tipo_registro === '9') {
                $this->trailer->loadFromString($linha);
                $linhaRetorno->linhaCnab = $this->trailer;
            }
        }//end foreach
    }

    public function listDetalhes()
    {
        return $this->detalhes;
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
     * Retorna o código do cedente / código da empresa / código do convênio (cada banco chama de um nome).
     *
     * @return string
     */
    public function getCodigoCedente()
    {
        return $this->header->getCodigoCedente();
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
        return $this->header->codigo_do_banco;
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
        $data = false;
        if (empty($this->header->data_de_geracao) === false) {
            $data = \DateTime::createFromFormat('dmy', sprintf('%06d', $this->header->data_de_geracao));
            $data->setTime(0, 0, 0);
        }

        return $data;
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
        if ($this->header->existField('data_de_credito') === true) {
            $data = false;
            if (empty($this->header->data_de_credito) === false) {
                $data = \DateTime::createFromFormat('dmy', sprintf('%06d', $this->header->data_de_credito));
                $data->setTime(0, 0, 0);
            }

            return $data;
        } else {
            return;
        }
    }


}
