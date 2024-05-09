<?php include 'get_base64_image.php'; ?>
<!DOCTYPE HTML PUBLIC '-//W3C//DTD HTML 4.0 Transitional//EN'>
<HTML>
   <HEAD>
      <title><?php echo "2ª Via do boleto - inFlux"; ?></title>
      <META http-equiv=Content-Type content=text/html charset=UTF-8>
      <meta name="Generator" content="Projeto BoletoPHP - www.boletophp.com.br - Licença GPL" />
      <style type=text/css>
         <!--.boleto-cef .cp {  font: bold 10px Arial; color: black}
            <!--.boleto-cef .ti {  font: 9px Arial, Helvetica, sans-serif}
            <!--.boleto-cef .ld { font: bold 12px Arial; color: #000000}
            <!--.boleto-cef .cn { FONT: 9px Arial; COLOR: black }
            <!--.boleto-cef .bc { font: bold 16px Arial; color: #000000 }
            <!--.boleto-cef .ld2 { font: bold 12px Arial; color: #000000 }
            -->
            .boleto-cef .ct { FONT: 9px "Arial"; COLOR: #000033
            }
            .boleto-cef .ct8 { font: 16px Arial;
            }
            .boleto-cef .ct9 { font: 8px Arial;
            }
      </style>
   </head>
   <BODY text=#000000 bgColor=#ffffff topMargin=0 rightMargin=0>
<div class="boleto-cef" style="margin: 0 auto; width: 666px;">
      <table width=666 cellspacing=5 cellpadding=0 border=0>
    <tr>
      <td width=41></TD>
    </tr>
  </table>
  <table width=666 cellspacing=5 cellpadding=0 border=0>
    <tr>
      <td width=41>
        <IMG src="data:image/png;base64, <?php echo $logoBase64_image; ?>" height="65px">
      </td>
      <td class=ti width=455>
        <?php echo '2ª Via do boleto - inFlux'; ?> 
        <?php
            if ($parametros['cedente_cnpj'] !== '') {
                echo "<br>" . mask($parametros['cedente_cnpj'], '##.###.###/####-##');
            } else {
                echo "";
            }
        ?><br>
        <?php echo $parametros['cedente_endereco']; ?><br>
        <?php echo ""; ?><br>
      </td>
      <td align=RIGHT width=150 class=ti>
        &nbsp;
      </td>
    </tr>
  </table>
  <BR>

<table cellspacing=0 cellpadding=0 border=0 width=666>    
    <tbody> 
      <tr>
         <td valign=top colspan="4" height=1>
            <img height=1 src="data:image/png;base64, <?php echo $Base64_2_image; ?>" width=100% border=0>
         </td>
      </tr>
      <tr height=80>
         <td class=ct8 width=1% >
            <img height=80 src="data:image/png;base64, <?php echo $Base64_1_image; ?>" width=1 border=0>
         </td>
         <td class=ct width=29% >
            <img SRC="<?php echo 'http://' . $host . '/images/boleto/logocaixa.jpg'; ?>">
         </td>
         <td class=ct8 width=38% >
            COBRANÇA BANCÁRIA CAIXA
         </td>
         <td width=32% >
        <table cellspacing=0 cellpadding=0 border=0 width=100%>    
            <tbody align="center"> 
              <tr class="ct9">
                <td colspan="2">
                  <img height=100% src="data:image/png;base64, <?php echo $Base64_1_image; ?>" width=1 border=0 align="left">
                  Reclamações e Sugestões
                  <img height=100% src="data:image/png;base64, <?php echo $Base64_1_image; ?>" width=1 border=0 align="right">
                  <img height=1 src="data:image/png;base64, <?php echo $Base64_2_image; ?>" width=100% border=0>
                </td>
              </tr>
              <tr class="ct9">
                <td>
                  <img height=100% src="data:image/png;base64, <?php echo $Base64_1_image; ?>" width=1 border=0 align="left">                
                  DISQUE CAIXA
                  <img height=100% src="data:image/png;base64, <?php echo $Base64_1_image; ?>" width=1 border=0 align="right">
                  <img height=1 src="data:image/png;base64, <?php echo $Base64_2_image; ?>" width=100% border=0>
                </td>
                <td>
                  0800 726 0101
                  <img height=100% src="data:image/png;base64, <?php echo $Base64_1_image; ?>" width=1 border=0 align="right">
                  <img height=1 src="data:image/png;base64, <?php echo $Base64_2_image; ?>" width=100% border=0>
                </td>
              </tr>
              <tr class="ct9">
                <td>
                  <img height=100% src="data:image/png;base64, <?php echo $Base64_1_image; ?>" width=1 border=0 align="left">                  
                  OUVIDORIA
                  <img height=100% src="data:image/png;base64, <?php echo $Base64_1_image; ?>" width=1 border=0 align="right">
                  <img height=1 src="data:image/png;base64, <?php echo $Base64_2_image; ?>" width=100% border=0>
                </td>
                <td>
                  0800 725 7474
                  <img height=100% src="data:image/png;base64, <?php echo $Base64_1_image; ?>" width=1 border=0 align="right">
                  <img height=1 src="data:image/png;base64, <?php echo $Base64_2_image; ?>" width=100% border=0>
                </td>
              </tr>
              <tr class="ct9">
                <td colspan="2">
              <img height=100% src="data:image/png;base64, <?php echo $Base64_1_image; ?>" width=1 border=0 align="left">
                  www.caixa.gov.br
                  <img height=100% src="data:image/png;base64, <?php echo $Base64_1_image; ?>" width=1 border=0 align="right">
                </td>
              </tr>
            </tbody>
         </table>             
         </td>
       </tr>
       <tr>
         <td valign=top colspan="4" height=5>
            <img height=1 src="data:image/png;base64, <?php echo $Base64_2_image; ?>" width=100% border=0 >
         </td>
      </tr>
    </tbody>
 </table>
  <img height=1 src="data:image/png;base64, <?php echo $Base64_2_image; ?>" width=666 border=0 >
 <table cellspacing=0 cellpadding=0 border=0 width=666>
   <tbody>
      <tr>
         <td class=ct valign=top width=1% height=14>
            <img height=14 src="data:image/png;base64, <?php echo $Base64_1_image; ?>" width=1 border=0>
         </td>
         <td class=ct valign=top width=54% height=14>
            Beneficiário
         </td>
         <td class=ct valign=top width=1% height=14>
            <img height=14 src="data:image/png;base64, <?php echo $Base64_1_image; ?>" width=1 border=0>
         </td>
         <td class=ct valign=top width=19% height=14>
            CPF/CNPJ
         </td>
         <td class=ct valign=top width=1% height=14>
            <img height=14 src="data:image/png;base64, <?php echo $Base64_1_image; ?>" width=1 border=0>
         </td>
         <td class=ct valign=top width=24% height=14>
            Agência/Código do Cedente
            <img height=14 src="data:image/png;base64, <?php echo $Base64_1_image; ?>" width=1 border=0 align="right">
         </td>
      </tr>
      <tr>
         <td class=cp valign=top  height=14>
            <img height=14 src="data:image/png;base64, <?php echo $Base64_1_image; ?>" width=1 border=0>
         </td>
         <td class=cp valign=top  height=14>
        <?php echo $parametros["cedente_nome"]; ?>
         </td>
         <td class=cp valign=top  height=14>
            <img height=14 src="data:image/png;base64, <?php echo $Base64_1_image; ?>" width=1 border=0>
         </td>
         <td class=cp valign=top  height=14>
                <?php echo mask($parametros['cedente_cnpj'], '##.###.###/####-##'); ?>
         </td>
         <td class=cp valign=top  height=14>
            <img height=14 src="data:image/png;base64, <?php echo $Base64_1_image; ?>" width=1 border=0>
         </td>
         <td class=cp valign=top  height=14>
                <?php echo $parametros['agencia_conta'];?>
              <img height=14 src="data:image/png;base64, <?php echo $Base64_1_image; ?>" width=1 border=0 align="right">
         </td>
      </tr>
      <tr>
         <td valign=top colspan="6" height=1>
            <img height=1 src="data:image/png;base64, <?php echo $Base64_2_image; ?>" width=100% border=0>
         </td>
      </tr>
    <tr>
         <td class=ct valign=top width=1% height=14>
            <img height=14 src="data:image/png;base64, <?php echo $Base64_1_image; ?>" width=1 border=0>
         </td>
         <td class=ct valign=top width=54% height=14>
            Endereço do beneficiário
         </td>
         <td class=ct valign=top width=1% height=14>
            <img height=14 src="data:image/png;base64, <?php echo $Base64_1_image; ?>" width=1 border=0>
         </td>
         <td class=ct valign=top width=19% height=14>
            UF
         </td>
         <td class=ct valign=top width=1% height=14>
            <img height=14 src="data:image/png;base64, <?php echo $Base64_1_image; ?>" width=1 border=0>
         </td>
         <td class=ct valign=top width=24% height=14>
            CEP
            <img height=14 src="data:image/png;base64, <?php echo $Base64_1_image; ?>" width=1 border=0 align="right">
         </td>
      </tr>
      <tr>
         <td class=cp valign=top  height=14>
            <img height=14 src="data:image/png;base64, <?php echo $Base64_1_image; ?>" width=1 border=0>
         </td>
         <td class=cp valign=top  height=14>
                <?php echo $parametros["cedente_endereco"]; ?>
         </td>
         <td class=cp valign=top  height=14>
            <img height=14 src="data:image/png;base64, <?php echo $Base64_1_image; ?>" width=1 border=0>
         </td>
         <td class=cp valign=top  height=14>
                <?php echo "";
                // não tem cadastro de UF?>
         </td>
         <td class=cp valign=top  height=14>
            <img height=14 src="data:image/png;base64, <?php echo $Base64_1_image; ?>" width=1 border=0>
         </td>
         <td class=cp valign=top  height=14>
                <?php echo "";
                // não tem cadastro de CEP?>
              <img height=14 src="data:image/png;base64, <?php echo $Base64_1_image; ?>" width=1 border=0 align="right">
         </td>
      </tr>
      <tr>
         <td valign=top colspan="6" height=1>
            <img height=1 src="data:image/png;base64, <?php echo $Base64_2_image; ?>" width=100% border=0>
         </td>
      </tr>
    </tbody>
 </table>
 <table cellspacing=0 cellpadding=0 border=0 width=666>    
    <tbody>
      <tr>
         <td class=ct valign=top width=1% height=14>
            <img height=14 src="data:image/png;base64, <?php echo $Base64_1_image; ?>" width=1 border=0>
         </td>
         <td class=ct valign=top width=14% height=14>
            Data do Documento
         </td>
         <td class=ct valign=top width=1% height=14>
            <img height=14 src="data:image/png;base64, <?php echo $Base64_1_image; ?>" width=1 border=0>
         </td>
         <td class=ct valign=top width=14% height=14>
            Nº do Documento
         </td>
         <td class=ct valign=top width=1% height=14>
            <img height=14 src="data:image/png;base64, <?php echo $Base64_1_image; ?>" width=1 border=0>
         </td>
         <td class=ct valign=top width=14% height=14>
            Espécie Documento
         </td>
         <td class=ct valign=top width=1% height=14>
            <img height=14 src="data:image/png;base64, <?php echo $Base64_1_image; ?>" width=1 border=0>
         </td>
         <td class=ct valign=top width=9% height=14>
            Carteira
         </td>
         <td class=ct valign=top width=1% height=14>
            <img height=14 src="data:image/png;base64, <?php echo $Base64_1_image; ?>" width=1 border=0>
         </td>
         <td class=ct valign=top width=19% height=14>
            Data do Processamento
         </td>
         <td class=ct valign=top width=1% height=14>
            <img height=14 src="data:image/png;base64, <?php echo $Base64_1_image; ?>" width=1 border=0>
         </td>
         <td class=ct valign=top width=24% height=14>
            Nosso Número
            <img height=14 src="data:image/png;base64, <?php echo $Base64_1_image; ?>" width=1 border=0 align="right">
         </td>
      </tr>
      <tr>
         <td class=cp valign=top height=14>
            <img height=14 src="data:image/png;base64, <?php echo $Base64_1_image; ?>" width=1 border=0>
         </td>
         <td class=cp valign=top height=14>
                <?php echo $parametros["data_documento"]->format('d/m/Y');?>
         </td>
         <td class=cp valign=top height=14>
            <img height=14 src="data:image/png;base64, <?php echo $Base64_1_image; ?>" width=1 border=0>
         </td>
         <td class=cp valign=top height=14>
                <?php echo $parametros["numero_documento"]?>
         </td>
         <td class=cp valign=top height=14>
            <img height=14 src="data:image/png;base64, <?php echo $Base64_1_image; ?>" width=1 border=0>
         </td>
         <td class=cp valign=top height=14>
                <?php echo $parametros["especie"]?>
         </td>
         <td class=cp valign=top height=14>
            <img height=14 src="data:image/png;base64, <?php echo $Base64_1_image; ?>" width=1 border=0>
         </td>
         <td class=cp valign=top height=14>
                <?php echo $parametros["carteira"]?>
         </td>
         <td class=cp valign=top height=14>
            <img height=14 src="data:image/png;base64, <?php echo $Base64_1_image; ?>" width=1 border=0>
         </td>
         <td class=cp valign=top height=14>
                <?php echo date('d/m/Y');?>
         </td>
         <td class=cp valign=top height=14>
            <img height=14 src="data:image/png;base64, <?php echo $Base64_1_image; ?>" width=1 border=0>
         </td>
         <td class=cp valign=top height=14> 
                <?php echo $parametros["nosso_numero"];?>
              <img height=14 src="data:image/png;base64, <?php echo $Base64_1_image; ?>" width=1 border=0 align="right">
         </td>
      </tr>
      <tr>
         <td valign=top colspan="12" height=1>
            <img height=1 src="data:image/png;base64, <?php echo $Base64_2_image; ?>" width=100% border=0>
         </td>
      </tr>
    </tbody>
 </table>
 <table cellspacing=0 cellpadding=0 border=0 width=666>    
    <tbody> 
      <tr>
         <td class=ct valign=top width=1% height=14>
            <img height=14 src="data:image/png;base64, <?php echo $Base64_1_image; ?>" width=1 border=0>
         </td>
         <td class=ct valign=top width=74% height=14>
            Pagador
         </td>
         <td class=ct valign=top width=1% height=14>
            <img height=14 src="data:image/png;base64, <?php echo $Base64_1_image; ?>" width=1 border=0>
         </td>
         <td class=ct valign=top width=24% height=14>
            CPF / CNPJ
            <img height=14 src="data:image/png;base64, <?php echo $Base64_1_image; ?>" width=1 border=0 align="right">
         </td>
       </tr>
       <tr>
         <td class=cp valign=top height=14>
            <img height=14 src="data:image/png;base64, <?php echo $Base64_1_image; ?>" width=1 border=0>
         </td>
         <td class=cp valign=top height=14>
                <?php echo $parametros["sacado_nome"]; ?>
         </td>
         <td class=cp valign=top height=14>
            <img height=14 src="data:image/png;base64, <?php echo $Base64_1_image; ?>" width=1 border=0>
         </td>
         <td class=cp valign=top height=14>
            <?php
                if (strlen($parametros["sacado_cpf_cnpj"]) < 12) {
                echo mask($parametros['sacado_cpf_cnpj'], '###.###.###-##');
                } else {
                echo mask($parametros['sacado_cpf_cnpj'], '##.###.###/####-##');
                }
            ?>
            <img height=14 src="data:image/png;base64, <?php echo $Base64_1_image; ?>" width=1 border=0 align="right">
         </td>
       </tr>
       <tr>
         <td valign=top colspan="4" height=1>
            <img height=1 src="data:image/png;base64, <?php echo $Base64_2_image; ?>" width=100% border=0>
         </td>
      </tr>
    </tbody>
 </table>
 <table cellspacing=0 cellpadding=0 border=0 width=666>    
    <tbody>           
      <tr>
         <td class=ct valign=top width=1% height=14>
            <img height=14 src="data:image/png;base64, <?php echo $Base64_1_image; ?>" width=1 border=0>
         </td>
         <td class=ct valign=top width=74% height=14>
            Endereço do Pagador
         </td>
         <td class=ct valign=top width=1% height=14>
            <img height=14 src="data:image/png;base64, <?php echo $Base64_1_image; ?>" width=1 border=0>
         </td>
         <td class=ct valign=top width=7% height=14>
            UF
         </td>
         <td class=ct valign=top width=1% height=14>
            <img height=14 src="data:image/png;base64, <?php echo $Base64_1_image; ?>" width=1 border=0>
         </td>
         <td class=ct valign=top width=16% height=14>
            CEP
            <img height=14 src="data:image/png;base64, <?php echo $Base64_1_image; ?>" width=1 border=0 align="right">
         </td>
       </tr>
       <tr>
         <td class=cp valign=top height=14>
            <img height=14 src="data:image/png;base64, <?php echo $Base64_1_image; ?>" width=1 border=0>
         </td>
         <td class=cp valign=top height=14>
                <?php echo $parametros["sacado_cidade"]; ?>
         </td>
         <td class=cp valign=top height=14>
            <img height=14 src="data:image/png;base64, <?php echo $Base64_1_image; ?>" width=1 border=0>
         </td>
         <td class=cp valign=top height=14>
                <?php echo $parametros["sacado_estado"]?>
         </td>
         <td class=cp valign=top height=14>
            <img height=14 src="data:image/png;base64, <?php echo $Base64_1_image; ?>" width=1 border=0>
         </td>
         <td class=cp valign=top height=14>
                <?php echo mask($parametros["sacado_cep"], '##.###-###');?>
              <img height=14 src="data:image/png;base64, <?php echo $Base64_1_image; ?>" width=1 border=0 align="right">
         </td>
       </tr>
       <tr>
         <td valign=top colspan="6" height=1>
            <img height=1 src="data:image/png;base64, <?php echo $Base64_2_image; ?>" width=100% border=0>
         </td>
      </tr>
   </tbody>
</table>
<table cellspacing=0 cellpadding=0 border=0 width=666>    
    <tbody> 
      <tr>
         <td class=ct valign=top width=1% height=14>
            <img height=14 src="data:image/png;base64, <?php echo $Base64_1_image; ?>" width=1 border=0>
         </td>
         <td class=ct valign=top width=74% height=14>
            Pagador / Avalista
         </td>
         <td class=ct valign=top width=1% height=14>
            <img height=14 src="data:image/png;base64, <?php echo $Base64_1_image; ?>" width=1 border=0>
         </td>
         <td class=ct valign=top width=24% height=14>
            CPF / CNPJ
            <img height=14 src="data:image/png;base64, <?php echo $Base64_1_image; ?>" width=1 border=0 align="right">
         </td>
       </tr>
       <tr>
         <td class=cp valign=top height=14>
            <img height=14 src="data:image/png;base64, <?php echo $Base64_1_image; ?>" width=1 border=0>
         </td>
         <td class=cp valign=top height=14>
         </td>
         <td class=cp valign=top height=14>
            <img height=14 src="data:image/png;base64, <?php echo $Base64_1_image; ?>" width=1 border=0>
         </td>
         <td class=cp valign=top height=14>
              <img height=14 src="data:image/png;base64, <?php echo $Base64_1_image; ?>" width=1 border=0 align="right">
         </td>
       </tr>
       <tr>
         <td valign=top colspan="4" height=1>
            <img height=1 src="data:image/png;base64, <?php echo $Base64_2_image; ?>" width=100% border=0>
         </td>
      </tr>
    </tbody>
 </table>
 <table cellspacing=0 cellpadding=0 border=0 width=666>    
    <tbody> 
      <tr>
         <td class=ct valign=top width=1% height=14>
            <img height=14 src="data:image/png;base64, <?php echo $Base64_1_image; ?>" width=1 border=0>
         </td>
         <td class=ct valign=top width=99% height=14>
            TEXTO DE RESPONSABILIDADE DO CEDENTE:
            <img height=14 src="data:image/png;base64, <?php echo $Base64_1_image; ?>" width=1 border=0 align="right">
         </td>
       </tr>
       <tr>
         <td class=cp valign=top height=14>
            <img height=14 src="data:image/png;base64, <?php echo $Base64_1_image; ?>" width=1 border=0>
         </td>
         <td class=ct valign=top height=14>
                <?php echo $parametros["instrucoes"]; ?>
              <img height=14 src="data:image/png;base64, <?php echo $Base64_1_image; ?>" width=1 border=0 align="right">
         </td>
       </tr>
       <tr>
         <td class=cp valign=top height=14>
            <img height=14 src="data:image/png;base64, <?php echo $Base64_1_image; ?>" width=1 border=0>
         </td>
         <td class=ct valign=top height=14>
                <?php echo ""; ?>
              <img height=14 src="data:image/png;base64, <?php echo $Base64_1_image; ?>" width=1 border=0 align="right">
         </td>
       </tr>
       <tr>
         <td class=cp valign=top height=14>
            <img height=14 src="data:image/png;base64, <?php echo $Base64_1_image; ?>" width=1 border=0>
         </td>
         <td class=cp valign=top height=14>
              <img height=14 src="data:image/png;base64, <?php echo $Base64_1_image; ?>" width=1 border=0 align="right">
         </td>
       </tr>       
       <tr>
         <td class=cp valign=top height=14>
            <img height=14 src="data:image/png;base64, <?php echo $Base64_1_image; ?>" width=1 border=0>
         </td>
         <td class=cp valign=top height=14>
              <img height=14 src="data:image/png;base64, <?php echo $Base64_1_image; ?>" width=1 border=0 align="right">
         </td>
       </tr>
       <tr>
         <td class=cp valign=top height=14>
            <img height=14 src="data:image/png;base64, <?php echo $Base64_1_image; ?>" width=1 border=0>
         </td>
         <td class=cp valign=top height=14>
              <img height=14 src="data:image/png;base64, <?php echo $Base64_1_image; ?>" width=1 border=0 align="right">
         </td>
       </tr>
       <tr>
         <td class=cp valign=top height=14>
            <img height=14 src="data:image/png;base64, <?php echo $Base64_1_image; ?>" width=1 border=0>
         </td>
         <td class=cp valign=top height=14>
              <img height=14 src="data:image/png;base64, <?php echo $Base64_1_image; ?>" width=1 border=0 align="right">
         </td>
       </tr>
       <tr>
         <td class=cp valign=top height=14>
            <img height=14 src="data:image/png;base64, <?php echo $Base64_1_image; ?>" width=1 border=0>
         </td>
         <td class=cp valign=top height=14>
              <img height=14 src="data:image/png;base64, <?php echo $Base64_1_image; ?>" width=1 border=0 align="right">
         </td>
       </tr>
       <tr>
         <td valign=top colspan="2" height=1>
            <img height=1 src="data:image/png;base64, <?php echo $Base64_2_image; ?>" width=100% border=0>
         </td>
      </tr>
    </tbody>
  </table>

 <table cellspacing=0 cellpadding=0 border=0 width=666>
  <tbody>
      <tr>
         <td class=ct valign=top width=1% height=14>
            <img height=14 src="data:image/png;base64, <?php echo $Base64_1_image; ?>" width=1 border=0>
         </td>
         <td class=ct valign=top width=14% height=14>
            Moeda
         </td>
         <td class=ct valign=top width=1% height=14>
            <img height=14 src="data:image/png;base64, <?php echo $Base64_1_image; ?>" width=1 border=0>
         </td>
         <td class=ct valign=top width=14% height=14>
            Quantidade
         </td>
         <td class=ct valign=top width=1% height=14>
            <img height=14 src="data:image/png;base64, <?php echo $Base64_1_image; ?>" width=1 border=0>
         </td>
         <td class=ct valign=top width=14% height=14>
            Valor
         </td>
         <td class=ct valign=top width=1% height=14>
            <img height=14 src="data:image/png;base64, <?php echo $Base64_1_image; ?>" width=1 border=0>
         </td>
         <td class=ct valign=top width=14% height=14>
            Vencimento
         </td>
         <td class=ct valign=top width=1% height=14>
            <img height=14 src="data:image/png;base64, <?php echo $Base64_1_image; ?>" width=1 border=0>
         </td>
         <td class=ct9 valign=top width=13% height=14>
            Valor do Documento
         </td>
         <td class=ct9 valign=top width=25% height=14>
            <img height=14 src="data:image/png;base64, <?php echo $Base64_1_image; ?>" width=1 border=0 align="left">
            Autenticação Mecânica - Recibo do Sacado            
            <img height=14 src="data:image/png;base64, <?php echo $Base64_1_image; ?>" width=1 border=0 align="right">
         </td>
      </tr>
      <tr>
         <td class=cp valign=top height=14>
            <img height=14 src="data:image/png;base64, <?php echo $Base64_1_image; ?>" width=1 border=0>
         </td>
         <td class=cp valign=top height=14>
         </td>
         <td class=cp valign=top height=14>
            <img height=14 src="data:image/png;base64, <?php echo $Base64_1_image; ?>" width=1 border=0>
         </td>
         <td class=cp valign=top height=14>
         </td>
         <td class=cp valign=top height=14>
            <img height=14 src="data:image/png;base64, <?php echo $Base64_1_image; ?>" width=1 border=0>
         </td>
         <td class=cp valign=top height=14>
         </td>
         <td class=cp valign=top height=14>
            <img height=14 src="data:image/png;base64, <?php echo $Base64_1_image; ?>" width=1 border=0>
         </td>
         <td class=cp valign=top height=14>
                <?php echo $parametros["data_vencimento"]->format('d/m/Y');?>
         </td>
         <td class=cp valign=top height=14>
            <img height=14 src="data:image/png;base64, <?php echo $Base64_1_image; ?>" width=1 border=0>
         </td>
         <td class=cp valign=top height=14>
              R$ <?php echo $parametros["valor_saldo"]?>
         </td>
         <td class=cp valign=top align=right height=14> 
        <img height=14 src="data:image/png;base64, <?php echo $Base64_1_image; ?>" width=1 border=0 align="left">
              <img height=14 src="data:image/png;base64, <?php echo $Base64_1_image; ?>" width=1 border=0 align="right">
         </td>
      </tr>
      <tr>
         <td valign=top colspan="11" height=1>
            <img height=1 src="data:image/png;base64, <?php echo $Base64_2_image; ?>" width=100% border=0>
         </td>
      </tr>
  </tbody>
 </table>

<table cellspacing=0 cellpadding=0 width=666 border=0>
   <tr>
      <td class=ct width=666></td>
   </tr>
   <tbody>
      <tr>
         <td class=ct width=666>
      <br>
         </td>
      </tr>
      <tr>
         <td class=ct width=666><img height=1 src="data:image/png;base64, <?php echo $Base64_6_image; ?>" width=665 border=0></td>
      </tr>
   </tbody>
</table>
<br>
<table cellspacing=0 cellpadding=0 width=666 border=0>
  <tr>
   <td class=cp width=150> 
      <IMG SRC="<?php echo 'http://' . $host . '/images/boleto/logocaixa.jpg'; ?>" width="150" height="40" border=0>
   </td>
   <td width=3 valign=bottom>
      <img height=22 src="data:image/png;base64, <?php echo $Base64_3_image; ?>" width=2 border=0>
   </td>
   <td class=cpt width=58 valign=bottom>
      <div align=center>
        <font class=bc>
            <?php echo $parametros["codigo_banco_com_dv"]?>
        </font>
      </div>
   </td>
   <td width=3 valign=bottom>
      <img height=22 src="data:image/png;base64, <?php echo $Base64_3_image; ?>" width=2 border=0>
    </td>
   <td class=ld align=right width=453 valign=bottom>
      <span class=ld> 
          <span class="campotitulo">
            <?php echo $parametros["linha_digitavel"]?>
          </span>
        </span>
   </td>
  </tr>
 <tbody>
   <tr>
      <td colspan=5>
        <img height=2 src="data:image/png;base64, <?php echo $Base64_2_image; ?>" width=666 border=0>
      </td>
   </tr>
 </tbody>
</table>


 <table cellspacing=0 cellpadding=0 border=0 width=666>
   <tbody>
      <tr>
         <td class=ct valign=top width=1% height=14>
            <img height=14 src="data:image/png;base64, <?php echo $Base64_1_image; ?>" width=1 border=0>
         </td>
         <td class=ct valign=top width=74% height=14>
            Local do Pagamento
         </td>
         <td class=ct valign=top width=1% height=14>
            <img height=14 src="data:image/png;base64, <?php echo $Base64_1_image; ?>" width=1 border=0>
         </td>
         <td class=ct valign=top width=24% height=14>
            Vencimento
            <img height=14 src="data:image/png;base64, <?php echo $Base64_1_image; ?>" width=1 border=0 align="right">
         </td>
      </tr>
      <tr>
         <td class=cp valign=top  height=14>
            <img height=14 src="data:image/png;base64, <?php echo $Base64_1_image; ?>" width=1 border=0>
         </td>
         <td class=cp valign=top  height=14>
        PREFERENCIALMENTE NAS CASAS LOTERICAS ATÉ O VALOR LIMITE
         </td>
         <td class=cp valign=top  height=14>
            <img height=14 src="data:image/png;base64, <?php echo $Base64_1_image; ?>" width=1 border=0>
         </td>
         <td class=cp valign=top  height=14>
                <?php echo $parametros["data_vencimento"]->format('d/m/Y');?>
              <img height=14 src="data:image/png;base64, <?php echo $Base64_1_image; ?>" width=1 border=0 align="right">
         </td>
      </tr>
      <tr>
         <td valign=top colspan="4" height=1>
            <img height=1 src="data:image/png;base64, <?php echo $Base64_2_image; ?>" width=100% border=0>
         </td>
      </tr>
    </tbody>
 </table>
 <table cellspacing=0 cellpadding=0 border=0 width=666>    
    <tbody>      
    <tr>
         <td class=ct valign=top width=1% height=14>
            <img height=14 src="data:image/png;base64, <?php echo $Base64_1_image; ?>" width=1 border=0>
         </td>
         <td class=ct valign=top width=54% height=14>
            Beneficiário
         </td>
         <td class=ct valign=top width=1% height=14>
            <img height=14 src="data:image/png;base64, <?php echo $Base64_1_image; ?>" width=1 border=0>
         </td>
         <td class=ct valign=top width=19% height=14>
            CPF / CNPJ
         </td>
         <td class=ct valign=top width=1% height=14>
            <img height=14 src="data:image/png;base64, <?php echo $Base64_1_image; ?>" width=1 border=0>
         </td>
         <td class=ct valign=top width=24% height=14>
            Agência / Código do Cedente
            <img height=14 src="data:image/png;base64, <?php echo $Base64_1_image; ?>" width=1 border=0 align="right">
         </td>
      </tr>
      <tr>
         <td class=cp valign=top  height=14>
            <img height=14 src="data:image/png;base64, <?php echo $Base64_1_image; ?>" width=1 border=0>
         </td>
         <td class=cp valign=top  height=14>
                <?php echo $parametros["cedente_nome"]; ?>
         </td>
         <td class=cp valign=top  height=14>
            <img height=14 src="data:image/png;base64, <?php echo $Base64_1_image; ?>" width=1 border=0>
         </td>
         <td class=cp valign=top  height=14>
                <?php echo mask($parametros['cedente_cnpj'], '##.###.###/####-##'); ?>
         </td>
         <td class=cp valign=top  height=14>
            <img height=14 src="data:image/png;base64, <?php echo $Base64_1_image; ?>" width=1 border=0>
         </td>
         <td class=cp valign=top  height=14>
                <?php echo $parametros["agencia_conta"]?>
              <img height=14 src="data:image/png;base64, <?php echo $Base64_1_image; ?>" width=1 border=0 align="right">
         </td>
      </tr>
      <tr>
         <td valign=top colspan="6" height=1>
            <img height=1 src="data:image/png;base64, <?php echo $Base64_2_image; ?>" width=100% border=0>
         </td>
      </tr>
    </tbody>
 </table>
 <table cellspacing=0 cellpadding=0 border=0 width=666>    
    <tbody>
      <tr>
         <td class=ct valign=top width=1% height=14>
            <img height=14 src="data:image/png;base64, <?php echo $Base64_1_image; ?>" width=1 border=0>
         </td>
         <td class=ct valign=top width=14% height=14>
            Data do Documento
         </td>
         <td class=ct valign=top width=1% height=14>
            <img height=14 src="data:image/png;base64, <?php echo $Base64_1_image; ?>" width=1 border=0>
         </td>
         <td class=ct valign=top width=19% height=14>
            Nº do Documento
         </td>
         <td class=ct valign=top width=1% height=14>
            <img height=14 src="data:image/png;base64, <?php echo $Base64_1_image; ?>" width=1 border=0>
         </td>
         <td class=ct valign=top width=9% height=14>
            Espécie
         </td>
         <td class=ct valign=top width=1% height=14>
            <img height=14 src="data:image/png;base64, <?php echo $Base64_1_image; ?>" width=1 border=0>
         </td>
         <td class=ct valign=top width=9% height=14>
            Aceite
         </td>
         <td class=ct valign=top width=1% height=14>
            <img height=14 src="data:image/png;base64, <?php echo $Base64_1_image; ?>" width=1 border=0>
         </td>
         <td class=ct valign=top width=19% height=14>
            Data do Processamento
         </td>
         <td class=ct valign=top width=1% height=14>
            <img height=14 src="data:image/png;base64, <?php echo $Base64_1_image; ?>" width=1 border=0>
         </td>
         <td class=ct valign=top width=24% height=14>
            Nosso Número
            <img height=14 src="data:image/png;base64, <?php echo $Base64_1_image; ?>" width=1 border=0 align="right">
         </td>
      </tr>
      <tr>
         <td class=cp valign=top height=14>
            <img height=14 src="data:image/png;base64, <?php echo $Base64_1_image; ?>" width=1 border=0>
         </td>
         <td class=cp valign=top height=14>
                <?php echo $parametros["data_documento"]->format('d/m/Y');?>
         </td>
         <td class=cp valign=top height=14>
            <img height=14 src="data:image/png;base64, <?php echo $Base64_1_image; ?>" width=1 border=0>
         </td>
         <td class=cp valign=top height=14>
                <?php echo $parametros["numero_documento"]?>
         </td>
         <td class=cp valign=top height=14>
            <img height=14 src="data:image/png;base64, <?php echo $Base64_1_image; ?>" width=1 border=0>
         </td>
         <td class=cp valign=top height=14>
                <?php echo $parametros["especie"]?>
         </td>
         <td class=cp valign=top height=14>
            <img height=14 src="data:image/png;base64, <?php echo $Base64_1_image; ?>" width=1 border=0>
         </td>
         <td class=cp valign=top height=14>
                <?php echo $parametros["aceite"]?>
         </td>
         <td class=cp valign=top height=14>
            <img height=14 src="data:image/png;base64, <?php echo $Base64_1_image; ?>" width=1 border=0>
         </td>
         <td class=cp valign=top height=14>
                <?php echo date('d/m/Y');?>
         </td>
         <td class=cp valign=top height=14>
            <img height=14 src="data:image/png;base64, <?php echo $Base64_1_image; ?>" width=1 border=0>
         </td>
         <td class=cp valign=top height=14> 
                <?php echo $parametros["nosso_numero"];?>
              <img height=14 src="data:image/png;base64, <?php echo $Base64_1_image; ?>" width=1 border=0 align="right">
         </td>
      </tr>
      <tr>
         <td valign=top colspan="12" height=1>
            <img height=1 src="data:image/png;base64, <?php echo $Base64_2_image; ?>" width=100% border=0>
         </td>
      </tr>
    </tbody>
 </table>
 <table cellspacing=0 cellpadding=0 border=0 width=666>    
    <tbody>
      <tr>
         <td class=ct valign=top width=1% height=14>
            <img height=14 src="data:image/png;base64, <?php echo $Base64_1_image; ?>" width=1 border=0>
         </td>
         <td class=ct valign=top width=14% height=14>
            Uso do Banco
         </td>
         <td class=ct valign=top width=1% height=14>
            <img height=14 src="data:image/png;base64, <?php echo $Base64_1_image; ?>" width=1 border=0>
         </td>
         <td class=ct valign=top width=9% height=14>
            Carteira
         </td>
         <td class=ct valign=top width=1% height=14>
            <img height=14 src="data:image/png;base64, <?php echo $Base64_1_image; ?>" width=1 border=0>
         </td>
         <td class=ct valign=top width=9% height=14>
            Moeda
         </td>
         <td class=ct valign=top width=1% height=14>
            <img height=14 src="data:image/png;base64, <?php echo $Base64_1_image; ?>" width=1 border=0>
         </td>
         <td class=ct valign=top width=19% height=14>
            Quantidade
         </td>
         <td class=ct valign=top width=1% height=14>
            <img height=14 src="data:image/png;base64, <?php echo $Base64_1_image; ?>" width=1 border=0>
         </td>
         <td class=ct valign=top width=19% height=14>
            Valor
         </td>
         <td class=ct valign=top width=1% height=14>
            <img height=14 src="data:image/png;base64, <?php echo $Base64_1_image; ?>" width=1 border=0>
         </td>
         <td class=ct valign=top width=24% height=14>
            (=) Valor do Documento
            <img height=14 src="data:image/png;base64, <?php echo $Base64_1_image; ?>" width=1 border=0 align="right">
         </td>
      </tr>
      <tr>
         <td class=cp valign=top height=14>
            <img height=14 src="data:image/png;base64, <?php echo $Base64_1_image; ?>" width=1 border=0>
         </td>
         <td class=cp valign=top height=14>
         </td>
         <td class=cp valign=top height=14>
            <img height=14 src="data:image/png;base64, <?php echo $Base64_1_image; ?>" width=1 border=0>
         </td>
         <td class=cp valign=top height=14>
            <?php echo $parametros["carteira"]?>
         </td>
         <td class=cp valign=top height=14>
            <img height=14 src="data:image/png;base64, <?php echo $Base64_1_image; ?>" width=1 border=0>
         </td>
         <td class=cp valign=top height=14>
            R$
         </td>
         <td class=cp valign=top height=14>
            <img height=14 src="data:image/png;base64, <?php echo $Base64_1_image; ?>" width=1 border=0>
         </td>
         <td class=cp valign=top height=14>
         </td>
         <td class=cp valign=top height=14>
            <img height=14 src="data:image/png;base64, <?php echo $Base64_1_image; ?>" width=1 border=0>
         </td>
         <td class=cp valign=top height=14>
         </td>
         <td class=cp valign=top height=14>
            <img height=14 src="data:image/png;base64, <?php echo $Base64_1_image; ?>" width=1 border=0>
         </td>
         <td class=cp valign=top height=14>
            R$ <?php echo $parametros["valor_saldo"]?> 
              <img height=14 src="data:image/png;base64, <?php echo $Base64_1_image; ?>" width=1 border=0 align="right">
         </td>
      </tr>
      <tr>
         <td valign=top colspan="12" height=1>
            <img height=1 src="data:image/png;base64, <?php echo $Base64_2_image; ?>" width=100% border=0>
         </td>
      </tr>
    </tbody>
 </table>
 <table cellspacing=0 cellpadding=0 border=0 width=666>    
    <tbody> 
      <tr>
         <td class=ct valign=top width=1% height=14 >
            <img height=14 src="data:image/png;base64, <?php echo $Base64_1_image; ?>" width=1 border=0>
         </td>
         <td class=ct9 valign=top width=74% height=14 >
            TEXTO DE RESPONSABILIDADE DO CEDENTE:
         </td>
         <td class=ct valign=top width=1% height=14 >
            <img height=14 src="data:image/png;base64, <?php echo $Base64_1_image; ?>" width=1 border=0>
         </td>
         <td class=ct valign=top width=24% height=14>
            (-) Desconto
            <img height=14 src="data:image/png;base64, <?php echo $Base64_1_image; ?>" width=1 border=0 align="right">
         </td>
       </tr>
       <tr>
         <td class=ct valign=top width=1% height=14 >
            <img height=15 src="data:image/png;base64, <?php echo $Base64_1_image; ?>" width=1 border=0>
         </td>
         <td class=ct9 valign=top width=74% height=14 >
            <?php echo $parametros["instrucoes"]?>
         </td>
         <td class=ct valign=top width=1% height=14 >
            <img height=14 src="data:image/png;base64, <?php echo $Base64_1_image; ?>" width=1 border=0>
            <img height=1 src="data:image/png;base64, <?php echo $Base64_2_image; ?>" width=100% border=0>
         </td>
         <td class=ct valign=top width=24% height=14>
            <img height=14 src="data:image/png;base64, <?php echo $Base64_1_image; ?>" width=1 border=0 align="right">
            <img height=1 src="data:image/png;base64, <?php echo $Base64_2_image; ?>" width=100% border=0>
         </td>
       </tr>
       <tr>
         <td class=ct valign=top width=1% height=14 >
            <img height=14 src="data:image/png;base64, <?php echo $Base64_1_image; ?>" width=1 border=0>
         </td>
         <td class=ct9 valign=top width=74% height=14 >
            <?php echo "";?>
         </td>
         <td class=ct valign=top width=1% height=14 >
            <img height=14 src="data:image/png;base64, <?php echo $Base64_1_image; ?>" width=1 border=0>
         </td>
         <td class=ct valign=top width=24% height=14>
        (-) Outras Deduções/Abatimento
            <img height=14 src="data:image/png;base64, <?php echo $Base64_1_image; ?>" width=1 border=0 align="right">
         </td>
       </tr>
       <tr>
         <td class=ct valign=top width=1% height=14 >
            <img height=15 src="data:image/png;base64, <?php echo $Base64_1_image; ?>" width=1 border=0>
         </td>
         <td class=ct valign=top width=74% height=14 >
         </td>
         <td class=ct valign=top width=1% height=14 >
            <img height=14 src="data:image/png;base64, <?php echo $Base64_1_image; ?>" width=1 border=0>
            <img height=1 src="data:image/png;base64, <?php echo $Base64_2_image; ?>" width=100% border=0>
         </td>
         <td class=ct valign=top width=24% height=14>
            <img height=14 src="data:image/png;base64, <?php echo $Base64_1_image; ?>" width=1 border=0 align="right">
            <img height=1 src="data:image/png;base64, <?php echo $Base64_2_image; ?>" width=100% border=0>
         </td>
       </tr>
       <tr>
         <td class=ct valign=top width=1% height=14 >
            <img height=14 src="data:image/png;base64, <?php echo $Base64_1_image; ?>" width=1 border=0>
         </td>
         <td class=ct valign=top width=74% height=14 >
         </td>
         <td class=ct valign=top width=1% height=14 >
            <img height=14 src="data:image/png;base64, <?php echo $Base64_1_image; ?>" width=1 border=0>
         </td>
         <td class=ct valign=top width=24% height=14>
            (+) Mora/Multa/Juros
            <img height=14 src="data:image/png;base64, <?php echo $Base64_1_image; ?>" width=1 border=0 align="right">
         </td>
       </tr>
       <tr>
         <td class=ct valign=top width=1% height=14 >
            <img height=15 src="data:image/png;base64, <?php echo $Base64_1_image; ?>" width=1 border=0>
         </td>
         <td class=ct valign=top width=74% height=14 >
         </td>
         <td class=ct valign=top width=1% height=14 >
            <img height=14 src="data:image/png;base64, <?php echo $Base64_1_image; ?>" width=1 border=0>
            <img height=1 src="data:image/png;base64, <?php echo $Base64_2_image; ?>" width=100% border=0>
         </td>
         <td class=ct valign=top width=24% height=14>
            <img height=14 src="data:image/png;base64, <?php echo $Base64_1_image; ?>" width=1 border=0 align="right">
            <img height=1 src="data:image/png;base64, <?php echo $Base64_2_image; ?>" width=100% border=0>
         </td>
       </tr>
              <tr>
         <td class=ct valign=top width=1% height=14 >
            <img height=14 src="data:image/png;base64, <?php echo $Base64_1_image; ?>" width=1 border=0>
         </td>
         <td class=ct valign=top width=74% height=14 >
         </td>
         <td class=ct valign=top width=1% height=14 >
            <img height=14 src="data:image/png;base64, <?php echo $Base64_1_image; ?>" width=1 border=0>
         </td>
         <td class=ct valign=top width=24% height=14>
            (+) Outros Acréscimos
            <img height=14 src="data:image/png;base64, <?php echo $Base64_1_image; ?>" width=1 border=0 align="right">
         </td>
       </tr>
       <tr>
         <td class=ct valign=top width=1% height=14 >
            <img height=15 src="data:image/png;base64, <?php echo $Base64_1_image; ?>" width=1 border=0>
         </td>
         <td class=ct valign=top width=74% height=14 >
         </td>
         <td class=ct valign=top width=1% height=14 >
            <img height=14 src="data:image/png;base64, <?php echo $Base64_1_image; ?>" width=1 border=0>
            <img height=1 src="data:image/png;base64, <?php echo $Base64_2_image; ?>" width=100% border=0>
         </td>
         <td class=ct valign=top width=24% height=14>
            <img height=14 src="data:image/png;base64, <?php echo $Base64_1_image; ?>" width=1 border=0 align="right">
            <img height=1 src="data:image/png;base64, <?php echo $Base64_2_image; ?>" width=100% border=0>
         </td>
       </tr>
              <tr>
         <td class=ct valign=top width=1% height=14 >
            <img height=14 src="data:image/png;base64, <?php echo $Base64_1_image; ?>" width=1 border=0>
         </td>
         <td class=ct valign=top width=74% height=14 >
         </td>
         <td class=ct valign=top width=1% height=14 >
            <img height=14 src="data:image/png;base64, <?php echo $Base64_1_image; ?>" width=1 border=0>
         </td>
         <td class=ct valign=top width=24% height=14>
            (=) Valor Cobrado
            <img height=14 src="data:image/png;base64, <?php echo $Base64_1_image; ?>" width=1 border=0 align="right">
         </td>
       </tr>
       <tr>
         <td class=ct valign=top width=1% height=14 >
            <img height=14 src="data:image/png;base64, <?php echo $Base64_1_image; ?>" width=1 border=0>
         </td>
         <td class=ct valign=top width=74% height=14 >
         </td>
         <td class=ct valign=top width=1% height=14 >
            <img height=14 src="data:image/png;base64, <?php echo $Base64_1_image; ?>" width=1 border=0>
         </td>
         <td class=ct valign=top width=24% height=14>
            <img height=14 src="data:image/png;base64, <?php echo $Base64_1_image; ?>" width=1 border=0 align="right">
         </td>
       </tr>
       <tr>
         <td valign=top colspan="4" height=1>
            <img height=1 src="data:image/png;base64, <?php echo $Base64_2_image; ?>" width=100% border=0>
         </td>
      </tr>
    </tbody>
  </table>

 <table cellspacing=0 cellpadding=0 border=0 width=666>
  <tbody>
      <tr>
         <td class=ct valign=top width=1% height=14>
            <img height=14 src="data:image/png;base64, <?php echo $Base64_1_image; ?>" width=1 border=0>
         </td>
         <td class=ct valign=top width=74% >
            NOME DO PAGADOR / CPF/CNPJ / ENDEREÇO / CIDADE / UF / CEP:
         </td>
         <td class=ct valign=top width=1% >
         </td>
         <td class=ct9 valign=top width=24% >
            <img height=14 src="data:image/png;base64, <?php echo $Base64_1_image; ?>" width=1 border=0 align="right">
         </td>
      </tr>
      <tr>
         <td height=14>
            <img height=14 src="data:image/png;base64, <?php echo $Base64_1_image; ?>" width=1 border=0>
         </td>
         <td class=ct>
            <?php
                echo $parametros["sacado_nome"] . "&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;";
                if (strlen($parametros["sacado_cpf_cnpj"]) < 12) {
                echo mask($parametros['sacado_cpf_cnpj'], '###.###.###-##');
                } else {
                echo mask($parametros['sacado_cpf_cnpj'], '##.###.###/####-##');
                }
            ?>
         </td>
         <td>
         </td>
         <td class=ct> 
            <img height=14 src="data:image/png;base64, <?php echo $Base64_1_image; ?>" width=1 border=0 align="right">
         </td>
      </tr>
      <tr >
         <td height=14>
            <img height=14 src="data:image/png;base64, <?php echo $Base64_1_image; ?>" width=1 border=0>
         </td>
         <td class=ct>
            <?php echo "{$parametros["sacado_endereco"]}, {$parametros["sacado_endereco_numero"]},  {$parametros["sacado_bairro"]} - {$parametros["sacado_cidade"]}/{$parametros["sacado_estado"]} - " . mask($parametros["sacado_cep"], '##.###-###');?>
         </td>
         <td>
         </td>
         <td class=ct>
            <?php echo ""; ?>
            <img height=14 src="data:image/png;base64, <?php echo $Base64_1_image; ?>" width=1 border=0 align="right">
         </td>
      </tr>
      <tr>
         <td height=14> 
            <img height=14 src="data:image/png;base64, <?php echo $Base64_1_image; ?>" width=1 border=0>
         </td>
         <td class=ct>
            SACADOR/AVALISTA:
         </td>
         <td >
         </td>
         <td> 
              <img height=14 src="data:image/png;base64, <?php echo $Base64_1_image; ?>" width=1 border=0 align="right">
         </td>
      </tr>
      <tr>
         <td valign=top colspan="4" height=1>
            <img height=1 src="data:image/png;base64, <?php echo $Base64_2_image; ?>" width=100% border=0>
         </td>
      </tr>
  </tbody>
 </table>

<TABLE cellSpacing=0 cellPadding=0 border=0 width=666>
   <TBODY>
      <TR>
         <TD class=ct width=75% height=12></TD>
         <TD class=ct width=25% >
              Ficha de Compensação<br>
              Autenticação no verso
         </TD>
      </TR>
      <TR>
         <TD class=ct  colspan=2 ></TD>
      </tr>
   </tbody>
</table>
<TABLE cellSpacing=0 cellPadding=0 width=666 border=0>
   <TBODY>
      <TR>
         <TD vAlign=bottom align=left height=50><?php fbarcodeCEF($parametros["codigo_barras"], $host); ?> 
         </TD>
      </tr>
   </tbody>
</table>
<TABLE cellSpacing=0 cellPadding=0 width=666 border=0>
  <TR>
      <TD class=ct width=666></TD>
   </TR>
   <TBODY>
      <TR>
         <TD class=ct width=666>
            <div align=right>Corte na linha pontilhada
            </div>
         </TD>
      </TR>
      <TR>
         <TD class=ct width=666><img height=1 src="data:image/png;base64, <?php echo $Base64_6_image; ?>" width=665 border=0></TD>
      </tr>
   </tbody>
</table>
</div>
</BODY>
</HTML>
