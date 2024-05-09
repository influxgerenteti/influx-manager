<?php
// phpcs:ignoreFile

if (!function_exists("digitoVerificadorNossoNumeroBradesco")) {
  function digitoVerificadorNossoNumeroBradesco($numero)
  {
  	$resto2 = modulo11Bradesco($numero, 7, 1);
       $digito = 11 - $resto2;
       if ($digito == 10) {
          $dv = "P";
       } elseif($digito == 11) {
       	$dv = 0;
  	} else {
          $dv = $digito;
       	}
  	 return $dv;
  }
}


if (!function_exists("digitoVerificadorBarraBradesco")) {
  function digitoVerificadorBarraBradesco($numero)
  {
  	$resto2 = modulo11Bradesco($numero, 9, 1);
       if ($resto2 == 0 || $resto2 == 1 || $resto2 == 10) {
          $dv = 1;
       } else {
  	 	$dv = 11 - $resto2;
       }
  	 return $dv;
  }
}

if (!function_exists("fbarcodeBradesco")) {
  function fbarcodeBradesco($valor, $host)
  {

  $fino = 1 ;
  $largo = 3 ;
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
    for($f1=9;$f1>=0;$f1--){ 
      for($f2=9;$f2>=0;$f2--){  
        $f = ($f1 * 10) + $f2 ;
        $texto = "" ;
        for($i=1;$i<6;$i++){ 
          $texto .=  substr($barcodes[$f1],($i-1),1) . substr($barcodes[$f2],($i-1),1);
        }
        $barcodes[$f] = $texto;
      }
    }


  //Desenho da barra


  //Guarda inicial
  ?><img src="<?php echo 'http://'.$host.'/images/boleto/p.png'; ?>" width=<?php echo $fino; ?> height=<?php echo $altura; ?> border=0><img 
  src="<?php echo 'http://'.$host.'/images/boleto/b.png'; ?>" width=<?php echo $fino; ?> height=<?php echo $altura; ?> border=0><img 
  src="<?php echo 'http://'.$host.'/images/boleto/p.png'; ?>" width=<?php echo $fino; ?> height=<?php echo $altura; ?> border=0><img 
  src="<?php echo 'http://'.$host.'/images/boleto/b.png'; ?>" width=<?php echo $fino; ?> height=<?php echo $altura; ?> border=0><img 
  <?php
  $texto = $valor ;
  if((strlen($texto) % 2) <> 0){
  	$texto = "0" . $texto;
  }

  // Draw dos dados
  while (strlen($texto) > 0) {
    $i = round(esquerda($texto,2));
    $texto = direita($texto,strlen($texto)-2);
    $f = $barcodes[$i];
    for($i=1;$i<11;$i+=2){
      if (substr($f,($i-1),1) == "0") {
        $f1 = $fino ;
      }else{
        $f1 = $largo ;
      }
  ?>
      src="<?php echo 'http://'.$host.'/images/boleto/p.png'; ?>" width=<?php echo $f1?> height=<?php echo $altura?> border=0><img 
  <?php
      if (substr($f,$i,1) == "0") {
        $f2 = $fino ;
      }else{
        $f2 = $largo ;
      }
  ?>
      src="<?php echo 'http://'.$host.'/images/boleto/b.png'; ?>" width=<?php echo $f2?> height=<?php echo $altura?> border=0><img 
  <?php
    }
  }

  // Draw guarda final
  ?>
  src="<?php echo 'http://'.$host.'/images/boleto/p.png'; ?>" width=<?php echo $largo?> height=<?php echo $altura?> border=0><img 
  src="<?php echo 'http://'.$host.'/images/boleto/b.png'; ?>" width=<?php echo $fino?> height=<?php echo $altura?> border=0><img 
  src="<?php echo 'http://'.$host.'/images/boleto/p.png'; ?>" width=<?php echo 1?> height=<?php echo $altura?> border=0> 
    <?php
  } //Fim da fun��o
}

if (!function_exists("modulo10Bradesco")) {
  function modulo10Bradesco($num)
  { 
  		$numtotal10 = 0;
          $fator = 2;

          // Separacao dos numeros
          for ($i = strlen($num); $i > 0; $i--) {
              // pega cada numero isoladamente
              $numeros[$i] = substr($num,$i-1,1);
              // Efetua multiplicacao do numero pelo (falor 10)
              // 2002-07-07 01:33:34 Macete para adequar ao Mod10 do Ita�
              $temp = $numeros[$i] * $fator; 
              $temp0=0;
              foreach (preg_split('//',$temp,-1,PREG_SPLIT_NO_EMPTY) as $k=>$v){ $temp0+=$v; }
              $parcial10[$i] = $temp0; //$numeros[$i] * $fator;
              // monta sequencia para soma dos digitos no (modulo 10)
              $numtotal10 += $parcial10[$i];
              if ($fator == 2) {
                  $fator = 1;
              } else {
                  $fator = 2; // intercala fator de multiplicacao (modulo 10)
              }
          }
  		
          // v�rias linhas removidas, vide fun��o original
          // Calculo do modulo 10
          $resto = $numtotal10 % 10;
          $digito = 10 - $resto;
          if ($resto == 0) {
              $digito = 0;
          }
  		
          return $digito;
  }
}

if (!function_exists("modulo11Bradesco")) {
  function modulo11Bradesco($num, $base=9, $r=0)
  {
      /**
       *   Autor:
       *           Pablo Costa <pablo@users.sourceforge.net>
       *
       *   Fun��o:
       *    Calculo do Modulo 11 para geracao do digito verificador 
       *    de boletos bancarios conforme documentos obtidos 
       *    da Febraban - www.febraban.org.br 
       *
       *   Entrada:
       *     $num: string num�rica para a qual se deseja calcularo digito verificador;
       *     $base: valor maximo de multiplicacao [2-$base]
       *     $r: quando especificado um devolve somente o resto
       *
       *   Sa�da:
       *     Retorna o Digito verificador.
       *
       *   Observa��es:
       *     - Script desenvolvido sem nenhum reaproveitamento de c�digo pr� existente.
       *     - Assume-se que a verifica��o do formato das vari�veis de entrada � feita antes da execu��o deste script.
       */                                        

      $soma = 0;
      $fator = 2;

      /* Separacao dos numeros */
      for ($i = strlen($num); $i > 0; $i--) {
          // pega cada numero isoladamente
          $numeros[$i] = substr($num,$i-1,1);
          // Efetua multiplicacao do numero pelo falor
          $parcial[$i] = $numeros[$i] * $fator;
          // Soma dos digitos
          $soma += $parcial[$i];
          if ($fator == $base) {
              // restaura fator de multiplicacao para 2 
              $fator = 1;
          }
          $fator++;
      }

      /* Calculo do modulo 11 */
      if ($r == 0) {
          $soma *= 10;
          $digito = $soma % 11;
          if ($digito == 10) {
              $digito = 0;
          }
          return $digito;
      } elseif ($r == 1){
          $resto = $soma % 11;
          return $resto;
      }
  }
}

if (!function_exists("montaLinhaDigitavelBradesco")) {
  function montaLinhaDigitavelBradesco($codigo)
  {

  	// 01-03    -> C�digo do banco sem o digito
  	// 04-04    -> C�digo da Moeda (9-Real)
  	// 05-05    -> D�gito verificador do c�digo de barras
  	// 06-09    -> Fator de vencimento
  	// 10-19    -> Valor Nominal do T�tulo
  	// 20-44    -> Campo Livre (Abaixo)
  	
  	// 20-23    -> C�digo da Agencia (sem d�gito)
  	// 24-05    -> N�mero da Carteira
  	// 26-36    -> Nosso N�mero (sem d�gito)
  	// 37-43    -> Conta do Cedente (sem d�gito)
  	// 44-44    -> Zero (Fixo)
          

          // 1. Campo - composto pelo c�digo do banco, c�digo da mo�da, as cinco primeiras posi��es
          // do campo livre e DV (modulo10) deste campo
          
          $p1 = substr($codigo, 0, 4);							// Numero do banco + Carteira
          $p2 = substr($codigo, 19, 5);						// 5 primeiras posi��es do campo livre
          $p3 = modulo10Bradesco("$p1$p2");						// Digito do campo 1
          $p4 = "$p1$p2$p3";								// Uni�o
          $campo1 = substr($p4, 0, 5).'.'.substr($p4, 5);

          // 2. Campo - composto pelas posi�oes 6 a 15 do campo livre
          // e livre e DV (modulo10) deste campo
          $p1 = substr($codigo, 24, 10);						//Posi��es de 6 a 15 do campo livre
          $p2 = modulo10Bradesco($p1);								//Digito do campo 2	
          $p3 = "$p1$p2";
          $campo2 = substr($p3, 0, 5).'.'.substr($p3, 5);

          // 3. Campo composto pelas posicoes 16 a 25 do campo livre
          // e livre e DV (modulo10) deste campo
          $p1 = substr($codigo, 34, 10);						//Posi��es de 16 a 25 do campo livre
          $p2 = modulo10Bradesco($p1);								//Digito do Campo 3
          $p3 = "$p1$p2";
          $campo3 = substr($p3, 0, 5).'.'.substr($p3, 5);

          // 4. Campo - digito verificador do codigo de barras
          $campo4 = substr($codigo, 4, 1);

          // 5. Campo composto pelo fator vencimento e valor nominal do documento, sem
          // indicacao de zeros a esquerda e sem edicao (sem ponto e virgula). Quando se
          // tratar de valor zerado, a representacao deve ser 000 (tres zeros).
  		$p1 = substr($codigo, 5, 4);
  		$p2 = substr($codigo, 9, 10);
  		$campo5 = "$p1$p2";

          return "$campo1 $campo2 $campo3 $campo4 $campo5"; 
  }
}

if (!function_exists('geraCodigoBancoBradesco')) {
  function geraCodigoBancoBradesco($numero)
  {
      $parte1 = substr($numero, 0, 3);
      $parte2 = modulo11Bradesco($parte1);
      return $parte1 . "-" . $parte2;
  }
}

$codigobanco         = "237";
$codigo_banco_com_dv = geraCodigoBancoBradesco($codigobanco);
$nummoeda            = "9";
$fator_vencimento    = fator_vencimento($parametros["data_vencimento"]->format('d/m/Y'));

// valor tem 10 digitos, sem virgula
$valor = formata_numero($parametros["valor_saldo"], 10, 0, "valor");
// agencia � 4 digitos
$agencia    = formata_numero($parametros['agencia'], 4, 0);
$agencia_dv = formata_numero($parametros['digito_agencia'], 1, 0);
// n�mero documento tem 10 digitos
$numero_documento = formata_numero($parametros['numero_documento'], 10, 0);
// carteira � 2 caracteres
$carteira = formata_numero($parametros['carteira'], 3, 0);
// nosso n�mero (sem dv) � 11 digitos
$nnum = formata_numero($parametros['carteira'], 2, 0) . formata_numero($parametros['nosso_numero'], 11, 0);
// dv do nosso n�mero
$dv_nosso_numero = digitoVerificadorNossoNumeroBradesco($nnum);

// conta cedente (sem dv) � 7 digitos
$conta = formata_numero($parametros['conta'], 7, 0);
// dv da conta cedente
$conta_dv = formata_numero($parametros['digito_conta'], 1, 0);


// 43 numeros para o calculo do digito verificador do codigo de barras
$dv = digitoVerificadorBarraBradesco("$codigobanco$nummoeda$fator_vencimento$valor$agencia$nnum$conta" . '0', 9, 0);
// Numero para o codigo de barras com 44 digitos
$linha = "$codigobanco$nummoeda$dv$fator_vencimento$valor$agencia$nnum$conta" . "0";

$nossonumero = substr($nnum, 0, 2) . '/' . substr($nnum, 2) . '-' . $dv_nosso_numero;
$agencia_conta = $agencia . "-" . $agencia_dv . " / " . $conta . "-" . $conta_dv;


$parametros["codigo_barras"]       = $linha;
$parametros["linha_digitavel"]     = montaLinhaDigitavelBradesco($linha);
$parametros["agencia_conta"]       = $agencia_conta;
$parametros["nosso_numero"]        = $nossonumero;
$parametros["carteira"]            = $carteira;
$parametros["numero_documento"]    = $numero_documento;
$parametros["codigo_banco_com_dv"] = $codigo_banco_com_dv;
