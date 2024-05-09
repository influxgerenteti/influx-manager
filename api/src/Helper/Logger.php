<?php

declare(strict_types=1);

namespace App\Helper;

class Logger
{
    public function __construct()
    {
    }

    public static function log($message,$tag = 'manager-log')
    {

        $logFile ="/tmp/{$tag}.log";
        file_put_contents($logFile, date("Y-m-d H:i:s.").round(gettimeofday()['usec']/1000).$message.PHP_EOL , FILE_APPEND);
    }

    public static function logCodeLine() {
        $backtrace = debug_backtrace();
        $caller = $backtrace[0]; // Pega o primeiro item do backtrace, que deve ser a chamada para esta função.
        
        // Formata o horário atual. Você pode ajustar o formato conforme necessário.
        $currentTime = date('Y-m-d H:i:s');
        
        // Imprime a linha de onde a função foi chamada e o horário atual.
        // Ajuste a mensagem conforme necessário.
        echo "Linha: " . $caller['line'] . " - Horário: " . $currentTime . "\n";
    }
   
}


