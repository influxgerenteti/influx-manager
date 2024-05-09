<?php

namespace App\Helper\RemessaPHP\Cnab\Retorno\Cnab240;

class SegmentoW extends \App\Helper\RemessaPHP\Cnab\Format\Linha
{


    public function __construct(\App\Helper\RemessaPHP\Cnab\Retorno\IArquivo $arquivo)
    {
        $yamlLoad = new \App\Helper\RemessaPHP\Cnab\Format\YamlLoad($arquivo->codigo_banco, $arquivo->layoutVersao);
        $yamlLoad->load($this, 'cnab240', 'retorno/detalhe_segmento_w');
    }


}
