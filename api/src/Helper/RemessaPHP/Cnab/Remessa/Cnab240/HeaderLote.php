<?php

namespace App\Helper\RemessaPHP\Cnab\Remessa\Cnab240;

class HeaderLote extends \App\Helper\RemessaPHP\Cnab\Format\Linha
{


    public function __construct(\App\Helper\RemessaPHP\Cnab\Remessa\IArquivo $arquivo)
    {
        $yamlLoad = new \App\Helper\RemessaPHP\Cnab\Format\YamlLoad($arquivo->codigo_banco, $arquivo->layoutVersao);
        $yamlLoad->load($this, 'cnab240', 'header_lote');
    }


}
