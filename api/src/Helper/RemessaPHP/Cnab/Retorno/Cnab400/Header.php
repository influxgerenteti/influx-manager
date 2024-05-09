<?php

namespace App\Helper\RemessaPHP\Cnab\Retorno\Cnab400;

class Header extends \App\Helper\RemessaPHP\Cnab\Format\Linha
{
    private $codigo_banco = null;

    public function __construct(\App\Helper\RemessaPHP\Cnab\Retorno\IArquivo $arquivo)
    {
        $this->codigo_banco = $arquivo->codigo_banco;
        $yamlLoad           = new \App\Helper\RemessaPHP\Cnab\Format\YamlLoad($arquivo->codigo_banco, $arquivo->layoutVersao);
        $yamlLoad->load($this, 'cnab400', 'retorno/header_arquivo');
    }

    public function getConta()
    {
        if ($this->existField('conta') === true) {
            return $this->conta;
        } else if ($this->codigo_banco === 104) {
            $codigo_cedente = sprintf('%016d', $this->codigo_cedente);

            return substr($codigo_cedente, 7, 8);
        }
    }

    public function getContaDac()
    {
        if ($this->existField('dac') === true) {
            return $this->dac;
        } else if ($this->codigo_banco === 104) {
            $codigo_cedente = sprintf('%016d', $this->codigo_cedente);

            return substr($codigo_cedente, 15, 1);
        }
    }

    public function getCodigoCedente()
    {
        if ($this->existField('codigo_cedente') === true) {
            return $this->codigo_cedente;
        }
    }


}
