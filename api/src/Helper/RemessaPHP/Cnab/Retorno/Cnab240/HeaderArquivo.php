<?php

namespace App\Helper\RemessaPHP\Cnab\Retorno\Cnab240;

class HeaderArquivo extends \App\Helper\RemessaPHP\Cnab\Format\Linha
{


    public function __construct(\App\Helper\RemessaPHP\Cnab\Retorno\IArquivo $arquivo)
    {
        $yamlLoad = new \App\Helper\RemessaPHP\Cnab\Format\YamlLoad($arquivo->codigo_banco, $arquivo->layoutVersao);
        $yamlLoad->load($this, 'cnab240', 'header_arquivo');
    }

    public function getConta()
    {
        if ($this->existField('conta') === true) {
            return $this->conta;
        } else {
            return;
        }
    }

    public function getContaDac()
    {
        if ($this->existField('conta_dv') === true) {
            return $this->conta_dv;
        } else {
            return;
        }
    }

    public function getCodigoConvenio()
    {
        if ($this->existField('codigo_convenio') === true) {
            return $this->codigo_convenio;
        } else {
            return;
        }
    }


}
