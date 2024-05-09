<?php
// phpcs:ignoreFile

if (!function_exists("fbarcodeBB")) {
	function fbarcodeBB($valor, $host)
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
	?><img src="<?php echo 'http://'.$host.'/images/boleto/p.png'; ?>" width=<?php echo $fino?> height=<?php echo $altura?> border=0><img 
	src="<?php echo 'http://'.$host.'/images/boleto/b.png'; ?>" width=<?php echo $fino?> height=<?php echo $altura?> border=0><img 
	src="<?php echo 'http://'.$host.'/images/boleto/p.png'; ?>" width=<?php echo $fino?> height=<?php echo $altura?> border=0><img 
	src="<?php echo 'http://'.$host.'/images/boleto/b.png'; ?>" width=<?php echo $fino?> height=<?php echo $altura?> border=0><img 
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

/*
#################################################
FUN��O DO M�DULO 10 RETIRADA DO PHPBOLETO

ESTA FUN��O PEGA O D�GITO VERIFICADOR DO PRIMEIRO, SEGUNDO
E TERCEIRO CAMPOS DA LINHA DIGIT�VEL
#################################################
*/
if (!function_exists("modulo10BB")) {
	function modulo10BB($num)
	{ 
		$numtotal10 = 0;
		$fator = 2;
	 
		for ($i = strlen($num); $i > 0; $i--) {
			$numeros[$i] = substr($num,$i-1,1);
			$parcial10[$i] = $numeros[$i] * $fator;
			$numtotal10 .= $parcial10[$i];
			if ($fator == 2) {
				$fator = 1;
			}
			else {
				$fator = 2; 
			}
		}
		
		$soma = 0;
		for ($i = strlen($numtotal10); $i > 0; $i--) {
			$numeros[$i] = substr($numtotal10,$i-1,1);
			$soma += $numeros[$i]; 
		}
		$resto = $soma % 10;
		$digito = 10 - $resto;
		if ($resto == 0) {
			$digito = 0;
		}

		return $digito;
	}
}

/*
#################################################
FUN��O DO M�DULO 11 RETIRADA DO PHPBOLETO

MODIFIQUEI ALGUMAS COISAS...

ESTA FUN��O PEGA O D�GITO VERIFICADOR:

NOSSONUMERO
AGENCIA
CONTA
CAMPO 4 DA LINHA DIGIT�VEL
#################################################
*/

if (!function_exists("modulo11BB")) {
	function modulo11BB($num, $base=9, $r=0)
	{

		$soma = 0;
		$fator = 2;

		for ($i = strlen($num); $i > 0; $i--) {
			$numeros[$i] = substr($num,$i-1,1);
			$parcial[$i] = $numeros[$i] * $fator;
			$soma += $parcial[$i];

			if ($fator == $base) {
				$fator = 1;
			}
			$fator++;
		}
		if ($r == 0) {
			$soma *= 10;
			$digito = $soma % 11;
			
			//corrigido
			if ($digito == 10) {
				$digito = "X";
			}

			/*
			alterado por mim, Daniel Schultz

			Vamos explicar:

			O m�dulo 11 s� gera os digitos verificadores do nossonumero,
			agencia, conta e digito verificador com codigo de barras (aquele que fica sozinho e triste na linha digit�vel)
			s� que � foi um rolo...pq ele nao podia resultar em 0, e o pessoal do phpboleto se esqueceu disso...
			
			No BB, os d�gitos verificadores podem ser X ou 0 (zero) para agencia, conta e nosso numero,
			mas nunca pode ser X ou 0 (zero) para a linha digit�vel, justamente por ser totalmente num�rica.

			Quando passamos os dados para a fun��o, fica assim:

			Agencia = sempre 4 digitos
			Conta = at� 8 d�gitos
			Nosso n�mero = de 1 a 17 digitos

			A unica vari�vel que passa 17 digitos � a da linha digitada, justamente por ter 43 caracteres

			Entao vamos definir ai embaixo o seguinte...

			se (strlen($num) == 43) { n�o deixar dar digito X ou 0 }
			*/
			
			if (strlen($num) == "43") {
				//ent�o estamos checando a linha digit�vel
				if ($digito == "0" or $digito == "X" or $digito > 9) {
						$digito = 1;
				}
			}
			return $digito;
		} 
		elseif ($r == 1){
			$resto = $soma % 11;
			return $resto;
		}
	}
}

/*
Montagem da linha digit�vel - Fun��o tirada do PHPBoleto
N�o mudei nada
*/
if (!function_exists("montaLinhaDigitavelBB")) {
	function montaLinhaDigitavelBB($linha)
	{
	    // Posi��o 	Conte�do
	    // 1 a 3    N�mero do banco
	    // 4        C�digo da Moeda - 9 para Real
	    // 5        Digito verificador do C�digo de Barras
	    // 6 a 19   Valor (12 inteiros e 2 decimais)
	    // 20 a 44  Campo Livre definido por cada banco

	    // 1. Campo - composto pelo c�digo do banco, c�digo da mo�da, as cinco primeiras posi��es
	    // do campo livre e DV (modulo10) deste campo
	    $p1 = substr($linha, 0, 4);
	    $p2 = substr($linha, 19, 5);
	    $p3 = modulo10BB("$p1$p2");
	    $p4 = "$p1$p2$p3";
	    $p5 = substr($p4, 0, 5);
	    $p6 = substr($p4, 5);
	    $campo1 = "$p5.$p6";
	    
	    // 2. Campo - composto pelas posi�oes 6 a 15 do campo livre
	    // e livre e DV (modulo10) deste campo
	    $p1 = substr($linha, 24, 10);
	    $p2 = modulo10BB($p1);
	    $p3 = "$p1$p2";
	    $p4 = substr($p3, 0, 5);
	    $p5 = substr($p3, 5);
	    $campo2 = "$p4.$p5";

	    // 3. Campo composto pelas posicoes 16 a 25 do campo livre
	    // e livre e DV (modulo10) deste campo
	    $p1 = substr($linha, 34, 10);
	    $p2 = modulo10BB($p1);
	    $p3 = "$p1$p2";
	    $p4 = substr($p3, 0, 5);
	    $p5 = substr($p3, 5);
	    $campo3 = "$p4.$p5";
	    
	    // 4. Campo - digito verificador do codigo de barras
	    $campo4 = substr($linha, 4, 1);

	    // 5. Campo composto pelo valor nominal pelo valor nominal do documento, sem
	    // indicacao de zeros a esquerda e sem edicao (sem ponto e virgula). Quando se
	    // tratar de valor zerado, a representacao deve ser 000 (tres zeros).
	    $campo5 = substr($linha, 5, 14);

	    return "$campo1 $campo2 $campo3 $campo4 $campo5"; 
	}
}

if (!function_exists('geraCodigoBancoBB')) {
  function geraCodigoBancoBB($numero)
  {
      $parte1 = substr($numero, 0, 3);
      $parte2 = modulo11BB($parte1);
      return $parte1 . "-" . $parte2;
  }
}

$codigobanco         = "001";
$codigo_banco_com_dv = geraCodigoBancoBB($codigobanco);
$nummoeda            = "9";
$fator_vencimento    = fator_vencimento($parametros["data_vencimento"]->format('d/m/Y'));

// valor tem 10 digitos, sem virgula
$valor = formata_numero($parametros["valor_saldo"], 10, 0, "valor");
// agencia � sempre 4 digitos
$agencia = formata_numero($parametros['agencia'], 4, 0);
// conta � sempre 8 digitos
$conta    = formata_numero($parametros["conta"], 8, 0);
$carteira = substr($parametros["carteira"], 0, 2);
// agencia e conta
$agencia_conta = $agencia . "-" . modulo11BB($agencia) . " / " . $conta . "-" . modulo11BB($conta);
// Zeros: usado quando convenio de 7 digitos
$livre_zeros = '000000';

if ($parametros["convenio"] > 999999) { // código do convênio com 7 dígitos

    $convenio = $parametros["convenio"];
    $nossonum = formata_numero($parametros["nosso_numero"], 10, 0);

    $nossonumero17 = $convenio . $nossonum;

	$dv = modulo11BB("$codigobanco$nummoeda$fator_vencimento$valor$livre_zeros$nossonumero17$carteira");

	$linha = "$codigobanco$nummoeda$dv$fator_vencimento$valor$livre_zeros$nossonumero17$carteira";

	$parametros["nosso_numero"] = $nossonumero17 . '-' . modulo11BB($nossonumero17);

} else {

    if ($parametros["convenio"] > 9999) { // código do convênio com 6 dígitos

        $convenio = formata_numero($parametros["convenio"], 6, 0);
        $nossonum = formata_numero($parametros["nosso_numero"], 5, 0);

    } else { // código do convênio com 4 dígitos

        $convenio = formata_numero($parametros["convenio"], 4, 0);
        $nossonum = formata_numero($parametros["nosso_numero"], 7, 0);
    }

    $nossonumero11 = $convenio . $nossonum;

	$dv = modulo11BB("$codigobanco$nummoeda$fator_vencimento$valor$nossonumero11$agencia$conta$carteira");

	$linha = "$codigobanco$nummoeda$dv$fator_vencimento$valor$nossonumero11$agencia$conta$carteira";	

	$parametros["nosso_numero"] = $nossonumero11 . '-' . modulo11BB($nossonumero11);
} 

$parametros["codigo_barras"]       = $linha;
$parametros["linha_digitavel"]     = montaLinhaDigitavelBB($linha);
$parametros["agencia_conta"]       = $agencia_conta;
$parametros["codigo_banco_com_dv"] = $codigo_banco_com_dv;
