<?php

namespace App\Helper\RemessaPHP\Cnab\Remessa\Cnab400;

class Header extends \App\Helper\RemessaPHP\Cnab\Format\Linha
{


    public function __construct(\App\Helper\RemessaPHP\Cnab\Remessa\IArquivo $arquivo)
    {
        $codigo_banco = $arquivo->codigo_banco;
        $yamlLoad     = new \App\Helper\RemessaPHP\Cnab\Format\YamlLoad($codigo_banco);
        $yamlLoad->load($this, 'cnab400', 'remessa/header_arquivo');
    }


}
