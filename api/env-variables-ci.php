<?php
/**
 * Arquivo para variáveis de ambiente no servidor CI
 *
 * @author  Marcelo André Naegeler <marcelo@gatilabs.com.br>
 * @license MIT https://mit-license.org/
 */

// Environment config
$envFile = '.env.dist';

$fopen = fopen($envFile, 'r');
$file  = fread($fopen, filesize($envFile));

$file = preg_replace('/(DATABASE_PRINCIPAL_HOST=).+/', '$1localhost', $file);
$file = preg_replace('/(DATABASE_PRINCIPAL_PORT=).+/', '$013306', $file);
$file = preg_replace('/(DATABASE_PRINCIPAL_NAME=).+/', '$1influx', $file);
$file = preg_replace('/(DATABASE_PRINCIPAL_USER=).+/', '$1root', $file);
$file = preg_replace('/(DATABASE_PRINCIPAL_PASSWORD=).+/', '$1root', $file);
$file = preg_replace('/(DATABASE_IMPORTACAO_HOST=).+/', '$1localhost', $file);
$file = preg_replace('/(DATABASE_IMPORTACAO_PORT=).+/', '$013306', $file);
$file = preg_replace('/(DATABASE_IMPORTACAO_NAME=).+/', '$1importacao_planilha', $file);
$file = preg_replace('/(DATABASE_IMPORTACAO_USER=).+/', '$1root', $file);
$file = preg_replace('/(DATABASE_IMPORTACAO_PASSWORD=).+/', '$1root', $file);
$file = preg_replace('/(DATABASE_LOG_HOST=).+/', '$1localhost', $file);
$file = preg_replace('/(DATABASE_LOG_PORT=).+/', '$013306', $file);
$file = preg_replace('/(DATABASE_LOG_NAME=).+/', '$1logs', $file);
$file = preg_replace('/(DATABASE_LOG_USER=).+/', '$1root', $file);
$file = preg_replace('/(DATABASE_LOG_PASSWORD=).+/', '$1root', $file);

fwrite(fopen('.env', 'w'), $file);

// PHPUnit config
$phpunitFile = 'phpunit.xml.dist';

$fopen = fopen($phpunitFile, 'r');
$file  = fread($fopen, filesize($phpunitFile));

$file = preg_replace('/(<env name="DATABASE_PRINCIPAL_HOST" value=").+("[\s]?\/>)/', '$1localhost$2', $file);
$file = preg_replace('/(<env name="DATABASE_PRINCIPAL_PORT" value=").+("[\s]?\/>)/', '$013306$2', $file);
$file = preg_replace('/(<env name="DATABASE_PRINCIPAL_NAME" value=").+("[\s]?\/>)/', '$1influx$2', $file);
$file = preg_replace('/(<env name="DATABASE_PRINCIPAL_USER" value=").+("[\s]?\/>)/', '$1root$2', $file);
$file = preg_replace('/(<env name="DATABASE_PRINCIPAL_PASSWORD" value=").+("[\s]?\/>)/', '$1root$2', $file);
$file = preg_replace('/(<env name="DATABASE_IMPORTACAO_HOST" value=").+("[\s]?\/>)/', '$1localhost$2', $file);
$file = preg_replace('/(<env name="DATABASE_IMPORTACAO_PORT" value=").+("[\s]?\/>)/', '$013306$2', $file);
$file = preg_replace('/(<env name="DATABASE_IMPORTACAO_NAME" value=").+("[\s]?\/>)/', '$1importacao_planilha$2', $file);
$file = preg_replace('/(<env name="DATABASE_IMPORTACAO_USER" value=").+("[\s]?\/>)/', '$1root$2', $file);
$file = preg_replace('/(<env name="DATABASE_IMPORTACAO_PASSWORD" value=").+("[\s]?\/>)/', '$1root$2', $file);
$file = preg_replace('/(<env name="DATABASE_LOG_HOST" value=").+("[\s]?\/>)/', '$1localhost$2', $file);
$file = preg_replace('/(<env name="DATABASE_LOG_PORT" value=").+("[\s]?\/>)/', '$013306$2', $file);
$file = preg_replace('/(<env name="DATABASE_LOG_NAME" value=").+("[\s]?\/>)/', '$1logs$2', $file);
$file = preg_replace('/(<env name="DATABASE_LOG_USER" value=").+("[\s]?\/>)/', '$1root$2', $file);
$file = preg_replace('/(<env name="DATABASE_LOG_PASSWORD" value=").+("[\s]?\/>)/', '$1root$2', $file);

fwrite(fopen('phpunit.xml', 'w'), $file);
