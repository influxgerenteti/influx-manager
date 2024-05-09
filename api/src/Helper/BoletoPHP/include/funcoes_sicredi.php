<?php
if (function_exists("digitoVerificador_nossonumero") === false) {


    function digitoVerificador_nossonumero($numero)
    {
        $resto2 = modulo_11($numero, 9, 1);
        // esta rotina sofrer algumas alterações para ajustar no layout do SICREDI
        $digito = 11 - $resto2;
        if ($digito > 9) {
            $dv = 0;
        } else {
            $dv = $digito;
        }

        return $dv;
    }


}

if (function_exists("digitoVerificador_campolivre") === false) {


    function digitoVerificador_campolivre($numero)
    {
        $resto2 = modulo_11($numero, 9, 1);
        // esta rotina sofreu algumas alterações para ajustar no layout do SICREDI
        if ($resto2 <= 1) {
            $dv = 0;
        } else {
            $dv = 11 - $resto2;
        }

        return $dv;
    }


}


if (function_exists("digitoVerificador_barra") === false) {


    function digitoVerificador_barra($numero)
    {
        $resto2 = modulo_11($numero, 9, 1);
        // esta rotina sofrer algumas alterações para ajustar no layout do SICREDI
        $digito = 11 - $resto2;
        if ($digito <= 1 || $digito >= 10) {
            $dv = 1;
        } else {
            $dv = $digito;
        }

        return $dv;
    }


}

// if (function_exists("srcImagem") === false){
// function srcImagem($url){
// var_dump(file_get_contents($url));die();
// return $url;
// return "data:image/png;base64," . base64_encode(@file_get_contents($url));
// }
// }
if (function_exists("fbarcode") === false) {


    function fbarcode($valor, $host)
    {

        $fino   = 1 ;
        $largo  = 3 ;
        $altura = 50 ;

        $barcodes[0] = "00110" ;
        $barcodes[1] = "10001" ;
        $barcodes[2] = "01001" ;
        $barcodes[3] = "11000" ;
        $barcodes[4] = "00101" ;
        $barcodes[5] = "10100" ;
        $barcodes[6] = "01100" ;
        $barcodes[7] = "00011" ;
        $barcodes[8] = "10010" ;
        $barcodes[9] = "01010" ;
        for ($f1 = 9;$f1 >= 0;$f1--) {
            for ($f2 = 9;$f2 >= 0;$f2--) {
                $f     = ($f1 * 10) + $f2 ;
                $texto = "" ;
            for ($i = 1;$i < 6;$i++) {
                    $texto .= substr($barcodes[$f1], ($i - 1), 1) . substr($barcodes[$f2], ($i - 1), 1);
            }

                $barcodes[$f] = $texto;
            }
        }

        // Desenho da barra
        // Guarda inicial
        ?><img src="<?php echo 'http://' . $host . '/images/boleto/p.png'; ?>" width=<?php echo $fino?> height=<?php echo $altura?> border=0><img 
        src="<?php echo 'http://' . $host . '/images/boleto/b.png'; ?>" width=<?php echo $fino?> height=<?php echo $altura?> border=0><img 
        src="<?php echo 'http://' . $host . '/images/boleto/p.png'; ?>" width=<?php echo $fino?> height=<?php echo $altura?> border=0><img 
        src="<?php echo 'http://' . $host . '/images/boleto/b.png'; ?>" width=<?php echo $fino?> height=<?php echo $altura?> border=0><img 
        <?php
        $texto = $valor ;
        if ((strlen($texto) % 2) !== 0) {
            $texto = "0" . $texto;
        }

        // Draw dos dados
        while (strlen($texto) > 0) {
            $i     = round(esquerda($texto, 2));
            $texto = direita($texto, strlen($texto) - 2);
            $f     = $barcodes[$i];
            for ($i = 1;$i < 11;$i += 2) {
                if (substr($f, ($i - 1), 1) === "0") {
                    $f1 = $fino;
                } else {
                    $f1 = $largo;
                }

                if (substr($f, $i, 1) === "0") {
                    $f2 = $fino;
                } else {
                    $f2 = $largo;
                }
        ?>
            src="<?php echo 'http://' . $host . '/images/boleto/p.png'; ?>" width=<?php echo $f1?> height=<?php echo $altura?> border=0><img 
            src="<?php echo 'http://' . $host . '/images/boleto/b.png'; ?>" width=<?php echo $f2?> height=<?php echo $altura?> border=0><img 
                <?php
            }
        }//end while

        // Draw guarda final
        ?>
        src="<?php echo 'http://' . $host . '/images/boleto/p.png'; ?>" width=<?php echo $largo?> height=<?php echo $altura?> border=0><img 
        src="<?php echo 'http://' . $host . '/images/boleto/b.png'; ?>" width=<?php echo $fino?> height=<?php echo $altura?> border=0><img 
        src="<?php echo 'http://' . $host . '/images/boleto/p.png'; ?>" width=<?php echo 1?> height=<?php echo $altura?> border=0> 
        <?php
    } //Fim da função


}//end if

if (function_exists("esquerda") === false) {


    function esquerda($entra, $comp)
    {
        return substr($entra, 0, $comp);
    }


}

if (function_exists("direita") === false) {


    function direita($entra, $comp)
    {
        return substr($entra, strlen($entra) - $comp, $comp);
    }


}

if (function_exists("fator_vencimento") === false) {


    function fator_vencimento($data)
    {
        $data = explode("/", $data);
        $ano  = $data[2];
        $mes  = $data[1];
        $dia  = $data[0];
        return(abs((_dateToDays("1997", "10", "07")) - (_dateToDays($ano, $mes, $dia))));
    }


}

if (function_exists("_dateToDays") === false) {


    function _dateToDays($year, $month, $day)
    {
        $century = substr($year, 0, 2);
        $year    = substr($year, 2, 2);
        if ($month > 2) {
            $month -= 3;
        } else {
            $month += 9;
            if ($year !== false) {
                $year--;
            } else {
                $year = 99;
                $century --;
            }
        }

        return ( floor((  146097 * $century) / 4) + floor(( 1461 * $year) / 4) + floor(( 153 * $month + 2) / 5) + $day + 1721119);
    }


}//end if

if (function_exists("modulo_10") === false) {


    function modulo_10($num)
    {
            $numtotal10 = 0;
            $fator      = 2;

            // Separacao dos numeros
            for ($i = strlen($num); $i > 0; $i--) {
            // pega cada numero isoladamente
            $numeros[$i] = substr($num, $i - 1, 1);
            // Efetua multiplicacao do numero pelo (falor 10)
            $temp  = $numeros[$i] * $fator;
            $temp0 = 0;
            foreach (preg_split('//', $temp, -1, PREG_SPLIT_NO_EMPTY) as $k => $v) {
                $temp0 += $v;
            }

            $parcial10[$i] = $temp0;
            // $numeros[$i] * $fator;
            // monta sequencia para soma dos digitos no (modulo 10)
            $numtotal10 += $parcial10[$i];
            if ($fator === 2) {
                $fator = 1;
            } else {
                $fator = 2;
                // intercala fator de multiplicacao (modulo 10)
            }
            }//end for

            // várias linhas removidas, vide função original
            // Calculo do modulo 10
            $resto  = $numtotal10 % 10;
            $digito = 10 - $resto;
            if ($resto === 0) {
            $digito = 0;
            }

            return $digito;

    }


}//end if

if (function_exists("modulo_11") === false) {


    function modulo_11($num, $base=9, $r=0)
    {
        /*
         *   Autor:
         *           Pablo Costa <pablo@users.sourceforge.net>
         *
         *   Função:
         *    Calculo do Modulo 11 para geracao do digito verificador
         *    de boletos bancarios conforme documentos obtidos
         *    da Febraban - www.febraban.org.br
         *
         *   Entrada:
         *     $num: string numérica para a qual se deseja calcularo digito verificador;
         *     $base: valor maximo de multiplicacao [2-$base]
         *     $r: quando especificado um devolve somente o resto
         *
         *   Saída:
         *     Retorna o Digito verificador.
         *
         *   Observações:
         *     - Script desenvolvido sem nenhum reaproveitamento de código pré existente.
         *     - Assume-se que a verificação do formato das variáveis de entrada é feita antes da execução deste script.
         */

        $soma  = 0;
        $fator = 2;

        // Separacao dos numeros
        for ($i = strlen($num); $i > 0; $i--) {
            // pega cada numero isoladamente
            $numeros[$i] = substr($num, $i - 1, 1);
            // Efetua multiplicacao do numero pelo falor
            $parcial[$i] = $numeros[$i] * $fator;
            // Soma dos digitos
            $soma += $parcial[$i];
            if ($fator === $base) {
                // restaura fator de multiplicacao para 2
                $fator = 1;
            }

            $fator++;
        }

        // Calculo do modulo 11
        if ($r === 0) {
            $soma  *= 10;
            $digito = $soma % 11;
            return $digito;
        } else if ($r === 1) {
            // esta rotina sofrer algumas alterações para ajustar no layout do SICREDI
            $r_div  = (int) ($soma / 11);
            $digito = ($soma - ($r_div * 11));
            return $digito;
        }
    }


}//end if

if (function_exists("monta_linha_digitavel") === false) {


    function monta_linha_digitavel($codigo)
    {

        // COMPOSICAO DO CODIGO
        // Posição | Larg | Conteúdo
        // --------+------+---------------
            // 1 a 3   |  03  | Identcação do banco
            // 4       |  01  | Código da Moeda - 9 para R$
            // 5       |  01  | Digito verificador geral do Código de Barras
            // 6 a 9   |  04  | Fator de Vencimento
        // 10 a 19 |  10  | Valor (8 inteiros e 2 decimais)
            // 20 a 44 |  25  | Campo Livre definido por cada banco (25 caracteres)
        // COMPOSICAO DA LINHA DIGITAVEL
            // 1. Campo - composto pelo código do banco, código da moéda, as cinco primeiras posições
            // do campo livre e DV (modulo10) deste campo
            $p1     = substr($codigo, 0, 4);
            $p2     = substr($codigo, 19, 5);
            $p3     = modulo_10("$p1$p2");
            $p4     = "$p1$p2$p3";
            $p5     = substr($p4, 0, 5);
            $p6     = substr($p4, 5);
            $campo1 = "$p5.$p6";

            // 2. Campo - composto pelas posiçoes 6 a 15 do campo livre
            // e livre e DV (modulo10) deste campo
            $p1     = substr($codigo, 24, 10);
            $p2     = modulo_10($p1);
            $p3     = "$p1$p2";
            $p4     = substr($p3, 0, 5);
            $p5     = substr($p3, 5);
            $campo2 = "$p4.$p5";

            // 3. Campo composto pelas posicoes 16 a 25 do campo livre
            // e livre e DV (modulo10) deste campo
            $p1     = substr($codigo, 34, 10);
            $p2     = modulo_10($p1);
            $p3     = "$p1$p2";
            $p4     = substr($p3, 0, 5);
            $p5     = substr($p3, 5);
            $campo3 = "$p4.$p5";

            // 4. Campo - digito verificador do codigo de barras
            $campo4 = substr($codigo, 4, 1);

            // 5. Campo composto pelo fator vencimento e valor nominal do documento, sem
            // indicacao de zeros a esquerda e sem edicao (sem ponto e virgula). Quando se
            // tratar de valor zerado, a representacao deve ser 000 (tres zeros).
            $p1     = substr($codigo, 5, 4);
            $p2     = substr($codigo, 9, 10);
            $campo5 = "$p1$p2";

            return "$campo1 $campo2 $campo3 $campo4 $campo5";
    }


}//end if

if (function_exists("geraCodigoBanco") === false) {


    function geraCodigoBanco($numero)
    {
        $parte1 = substr($numero, 0, 3);
        // $parte2 = modulo_11($parte1);
        return $parte1 . "-X";
    }


}





// ------------------------- DADOS DINAMICOS DO SEU CLIENTE PARA A GERACAO DO BOLETO (FIXO OU VIA GET) -------------------- //
// Os valores abaixo podem ser colocados manualmente ou ajustados p/ formulario c/ POST, GET ou de BD (MySql,Postgre,etc)   //
// DADOS DO BOLETO PARA O SEU CLIENTE
$parametros["inicio_nosso_numero"] = date("y");
// Ano da geracao do titulo ex: 07 para 2007
// ---------------------- DADOS FIXOS DE CONFIGURACAO DO SEU BOLETO --------------- //
// DADOS PERSONALIZADOS - SICREDI
// TODO: Ver qual é o posto/como pegar o posto
$parametros["posto"] = "30";
// Codigo do posto da cooperativa de credito
$parametros["byte_idt"] = "2";
// Byte de identificacao do cedente do bloqueto utilizado para compor o nosso numero.
                                  // 1 - Idtf emitente: Cooperativa | 2 a 9 - Idtf emitente: Cedente
$fatorVencimento = fator_vencimento($parametros["data_vencimento"]->format('d/m/Y'));

$codigobanco         = "748";
$codigo_banco_com_dv = geraCodigoBanco($codigobanco);
$parametros["codigo_banco_com_dv"] = $codigo_banco_com_dv;

$valor = formata_numero($parametros["valor_saldo"], 10, 0, "valor");

$agencia  = formata_numero($parametros["agencia"], 4, 0);
$conta    = formata_numero($parametros["conta"], 5, 0);
$conta_dv = formata_numero($parametros["digito_conta"], 1, 0);
// valor tem 10 digitos, sem virgula
$valor = formata_numero($parametros["valor_saldo"], 10, 0, "valor");
// posto da cooperativa de credito é dois digitos
$posto = formata_numero($parametros["posto"], 2, 0);

$carteira = $parametros["carteira"];


// $dadosboleto["demonstrativo1"] = "Pagamento de Compra na Loja Nonononono";
// $dadosboleto["demonstrativo2"] = "Mensalidade referente a nonon nonooon nononon<br>Taxa bancária - R$ ".number_format($taxa_boleto, 2, ',', '');
// $dadosboleto["demonstrativo3"] = "BoletoPhp - http://www.boletophp.com.br";
$parametros["demonstrativo1"]     = "";
$parametros["demonstrativo2"]     = "";
$parametros["demonstrativo3"]     = "";
$parametros["data_processamento"] = date('d/m/Y');
$parametros["quantidade"]         = "";
$parametros["valor_unitario"]     = "";

$parametros["endereco2"] = $parametros["sacado_cidade"] . " - " . $parametros["sacado_estado"] . " - CEP: " . mask($parametros["sacado_cep"], '##.###-###');


$nummoeda = "9";

// fillers - zeros Obs: filler1 contera 1 quando houver valor expresso no campo valor
$filler1 = 1;
$filler2 = 0;

// Byte de Identificação do cedente 1 - Cooperativa; 2 a 9 - Cedente
$byteidt = $parametros["byte_idt"];

// tipoCobranca: Cobrança 1 COM Registro
$tipoCobranca = 1;

// Codigo referente ao tipo de carteira: "1" - Carteira Simples
$tipo_carteira = 1;

// nosso número (sem dv) é 8 digitos
$nnum = $parametros["inicio_nosso_numero"] . $byteidt . formata_numero($parametros["nosso_numero"], 5, 0);

// calculo do DV do nosso número
$dv_nosso_numero = digitoVerificador_nossonumero("$agencia$posto$conta$nnum");

$nossoNumeroDv = "$nnum$dv_nosso_numero";

// formação do campo livre
$campolivre   = "$tipoCobranca$tipo_carteira$nossoNumeroDv$agencia$posto$conta$filler1$filler2";
$campoLivreDv = $campolivre . digitoVerificador_campolivre($campolivre);

// 43 numeros para o calculo do digito verificador do codigo de barras
$dv = digitoVerificador_barra("$codigobanco$nummoeda$fatorVencimento$valor$campoLivreDv", 9, 0);

// Numero para o codigo de barras com 44 digitos
$linha = "$codigobanco$nummoeda$dv$fatorVencimento$valor$campoLivreDv";

// Formata strings para impressao no boleto
$nossonumero    = substr($nossoNumeroDv, 0, 2) . '/' . substr($nossoNumeroDv, 2, 6) . '-' . substr($nossoNumeroDv, 8, 1);
$agencia_codigo = $agencia . "." . $posto . "." . $conta;

$parametros["codigo_barras"]   = $linha;
$parametros["linha_digitavel"] = monta_linha_digitavel($linha);
$parametros["agencia_codigo"]  = $agencia_codigo;
$parametros["nosso_numero"]    = $nossonumero;


