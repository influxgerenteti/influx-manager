<?php

namespace App\Helper\RemessaPHP\Cnab\Remessa\Cnab240;

class Detalhe
{
    public $segmento_p;
    public $segmento_q;
    public $segmento_r;

    public $last_error;

    public function __construct(\App\Helper\RemessaPHP\Cnab\Remessa\IArquivo $arquivo)
    {
        $this->segmento_p = new SegmentoP($arquivo);
        $this->segmento_q = new SegmentoQ($arquivo);
        $this->segmento_r = new SegmentoR($arquivo);
    }

    public function validate()
    {
        $this->last_error = null;
        foreach ($this->listSegmento() as $segmento) {
            if ($segmento->validate() === false) {
                $this->last_error = get_class($segmento) . ': ' . $segmento->last_error;
            }
        }

        return is_null($this->last_error);
    }

    /**
     * Lista todos os segmentos deste detalhe.
     *
     * @return array
     */
    public function listSegmento()
    {
        return [
            $this->segmento_p,
            $this->segmento_q,
            $this->segmento_r,
        ];
    }

    /**
     * Retorna todas as linhas destes detalhes.
     *
     * @return string
     */
    public function getEncoded()
    {
        $text = [];
        foreach ($this->listSegmento() as $segmento) {
            $text[] = $segmento->getEncoded();
        }

        return implode(Arquivo::QUEBRA_LINHA, $text);
    }


}
