<?php

namespace App\Helper\RemessaPHP\Cnab;

class Factory
{
    private static $cnabFormatPath = null;

    public static function getCnabFormatPath()
    {
        if (self::$cnabFormatPath === null) {
            $optionA = dirname(__FILE__) . '/../Yaml';
            $optionB = dirname(__FILE__) . '/../../../../vendor/andersondanilo/cnab_yaml';

            if (file_exists($optionA) === true) {
                self::setCnabFormatPath($optionA);
            } else if (file_exists($optionB) === true) {
                self::setCnabFormatPath($optionB);
            } else {
                throw new \Exception('cnab_yaml não está instalado ou não foi configurado');
            }
        }

        return self::$cnabFormatPath;
    }

    public static function setCnabFormatPath($value)
    {
        self::$cnabFormatPath = $value;
    }

    /**
     * Cria um arquivo de remessa.
     *
     * @return \App\Helper\RemessaPHP\Cnab\Remessa\IArquivo
     */
    public function createRemessa($codigo_banco, $formato='cnab400', $layoutVersao=null)
    {
        if (empty($codigo_banco) === true) {
            throw new \InvalidArgumentException('$codigo_banco cannot be empty');
        }

        switch ($formato) {
            case 'cnab400':
                return new Remessa\Cnab400\Arquivo($codigo_banco, $layoutVersao);
            case 'cnab240':
                return new Remessa\Cnab240\Arquivo($codigo_banco, $layoutVersao);
            default:
                throw new \InvalidArgumentException('Invalid cnab format: ' + $formato);
        }
    }

    /**
     * Cria um arquivo de retorno.
     *
     * @param string $filename
     *
     * @return \App\Helper\RemessaPHP\Cnab\Remessa\IArquivo
     */
    public function createRetorno($filename)
    {
        $identifier = new Format\Identifier();

        if (empty($filename) === true) {
            throw new \InvalidArgumentException('$filename cannot be empty');
        }

        $format = $identifier->identifyFile($filename);

        if (empty($format) === true) {
            throw new \Exception('Formato do arquivo não identificado');
        }

        if ($format['tipo'] !== 'retorno') {
            throw new \Exception('Este não é um arquivo de retorno');
        }

        if (empty($format['banco']) === true) {
            throw new \Exception('Banco não suportado');
        }

        if (\App\Helper\RemessaPHP\Cnab\Banco::existBanco((int) $format['banco']) === false) {
            throw new \Exception('Banco não suportado');
        }

        // por enquanto só suporta o Cnab400
        if ($format['bytes'] === 400) {
            return new Retorno\Cnab400\Arquivo($format['banco'], $filename, $format['layout_versao']);
        } else if ($format['bytes'] === 240) {
            return new Retorno\Cnab240\Arquivo($format['banco'], $filename, $format['layout_versao']);
        } else {
            throw new \Exception('Formato não suportado');
        }
    }


}
