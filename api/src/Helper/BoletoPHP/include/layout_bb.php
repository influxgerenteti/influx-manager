<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html>
<head>
<title><?php echo "2ª Via do boleto - inFlux"; ?></title>
<META http-equiv=Content-Type content=text/html charset=UTF-8>
<meta name="Generator" content="Projeto BoletoPHP - www.boletophp.com.br - Licença GPL" />
<style type="text/css">
<!--
.boleto-bb .ti {font: 9px Arial, Helvetica, sans-serif}
-->
</style>
</HEAD>
<BODY>
<STYLE>

@media screen,print {

/* *** TIPOGRAFIA BASICA *** */

.boleto-bb * {
    font-family: Arial;
    font-size: 12px;
    margin: 0;
    padding: 0;
}

.boleto-bb .notice {
    color: red;
}


/* *** LINHAS GERAIS *** */

.boleto-bb #container {
    width: 666px;
    margin: 0px auto;
    padding-bottom: 30px;
}

.boleto-bb #instructions {
    margin: 0;
    padding: 0 0 20px 0;
}

.boleto-bb #boleto {
    width: 666px;
    margin: 0;
    padding: 0;
}


/* *** CABECALHO *** */

.boleto-bb #instr_header {
    background: url("http://<?php echo $host?>/images/logo.png") no-repeat top left;
    padding-left: 85px;
    height: 65px;
    background-size: 76.3px 65px;
}

.boleto-bb #instr_header h1 {
    font-size: 16px;
    margin: 5px 0px;
}

.boleto-bb #instr_header address {
    font-style: normal;
}


.boleto-bb #instr_content h2 {
    font-size: 10px;
    font-weight: bold;
}

.boleto-bb #instr_content p {
    font-size: 10px;
    margin: 4px 0px;
}

.boleto-bb #instr_content ol {
    font-size: 10px;
    margin: 5px 0;
}

.boleto-bb #instr_content ol li {
    font-size: 10px;
    text-indent: 10px;
    margin: 2px 0px;
    list-style-position: inside;
}

.boleto-bb #instr_content ol li p {
    font-size: 10px;
    padding-bottom: 4px;
}


/* *** BOLETO *** */

.boleto-bb #boleto .cut {
    width: 666px;
    margin: 0px auto;
    border-bottom: 1px navy dashed;
}

.boleto-bb #boleto .cut p {
    margin: 0 0 5px 0;
    padding: 0px;
    font-family: 'Arial Narrow';
    font-size: 9px;
    color: navy;
}

.boleto-bb table.header {
    width: 666px;
    height: 38px;
    margin-top: 20px;
    margin-bottom: 10px;
    border-bottom: 2px navy solid;
    
}


.boleto-bb table.header div.field_cod_banco {
    width: 46px;
    height: 19px;
  margin-left: 5px;
    padding-top: 3px;
    text-align: center;
    font-size: 12px;
    font-weight: bold;
    color: navy;
    border-right: 2px solid navy;
    border-left: 2px solid navy;
}

.boleto-bb table.header td.linha_digitavel {
    width: 464px;
    text-align: right;
    font: bold 13px Arial; 
    color: navy
}

.boleto-bb table.line {
    margin-bottom: 3px;
    padding-bottom: 1px;
    border-bottom: 1px black solid;
}

.boleto-bb table.line tr.titulos td {
    height: 13px;
    font-family: 'Arial Narrow';
    font-size: 9px;
    color: navy;
    border-left: 5px #ffe000 solid;
    padding-left: 2px;
}

.boleto-bb table.line tr.campos td {
    height: 12px;
    font-size: 10px;
    color: black;
    border-left: 5px #ffe000 solid;
    padding-left: 2px;
}

.boleto-bb table.line td p {
    font-size: 10px;
}


.boleto-bb table.line tr.campos td.ag_cod_cedente,
.boleto-bb table.line tr.campos td.nosso_numero
{
    text-align: right;
}

.boleto-bb table.line tr.campos td.especie,
.boleto-bb table.line tr.campos td.qtd,
.boleto-bb table.line tr.campos td.vencimento
{
    text-align: center;
}

.boleto-bb table.line td.last_line {
    vertical-align: top;
    height: 25px;
}

.boleto-bb .alinha-bottom {
    vertical-align: bottom;
}

.boleto-bb table.line td.last_line table.line {
    margin-bottom: -5px;
    border: 0 white none;
}

.boleto-bb td.last_line table.line td.instrucoes {
    border-left: 0 white none;
    padding-left: 5px;
    padding-bottom: 0;
    margin-bottom: 0;
    height: 20px;
    vertical-align: top;
}

.boleto-bb table.line td.cedente {
    width: 659px;
}

.boleto-bb table.line td.valor_cobrado2 {
    padding-bottom: 0;
    margin-bottom: 0;
}


.boleto-bb table.line td.ag_cod_cedente {
    width: 126px;
}

.boleto-bb table.line td.especie {
    width: 35px;
}

.boleto-bb table.line td.qtd {
    width: 53px;
}

.boleto-bb table.line td.nosso_numero {
    /* width: 120px; */
    width: 115px;
    padding-right: 5px;
}

.boleto-bb table.line td.num_doc {
    width: 135px;
}

.boleto-bb table.line td.contrato {
    width: 72px;
}

.boleto-bb table.line td.cpf_cei_cnpj {
    width: 125px;
}

.boleto-bb table.line td.vencimento {
    width: 134px;
}

.boleto-bb table.line td.valor_doc {
    width: 123px;
}

.boleto-bb table.line td.desconto {
    width: 113px;
}

.boleto-bb table.line td.outras_deducoes {
    width: 112px;
}

.boleto-bb table.line td.mora_multa {
    width: 113px;
}

.boleto-bb table.line td.outros_acrescimos {
    width: 113px;
}

.boleto-bb table.line td.valor_cobrado {
    width: 180px;
    background-color: #ffc ;
}

.boleto-bb table.line td.sacado {
    width: 659px;
}

.boleto-bb table.line td.local_pagto {
    width: 472px;
}

.boleto-bb .pagavel {
    font-weight: bold;
    font-size: 11px !important;
}

.boleto-bb table.line td.vencimento2 {
    /* width: 180px; */
    width: 175px;
    padding-right: 5px;
    background-color: #ffc;
}

.boleto-bb table.line td.cedente2 {
    width: 472px;
}

.boleto-bb table.line td.ag_cod_cedente2 {
    /* width: 180px; */
    width: 175px;
    padding-right: 5px;
}

.boleto-bb table.line td.data_doc {
    width: 93px;
}

.boleto-bb table.line td.num_doc2 {
    width: 123px;
}

.boleto-bb table.line td.especie_doc {
    width: 63px;
}

.boleto-bb table.line td.aceite {
    width: 63px;
}

.boleto-bb table.line td.data_process {
    width: 102px;
}

.boleto-bb table.line td.nosso_numero2 {
    width: 180px;
}

.boleto-bb table.line td.reservado {
    width: 93px;
    background-color: #ffc;
}

.boleto-bb table.line td.carteira {
    width: 123px;
}

.boleto-bb table.line td.especie2 {
    width: 63px;
}

.boleto-bb table.line td.qtd2 {
    width: 63px;
}

.boleto-bb table.line td.xvalor {
    width: 102px;
}

.boleto-bb table.line td.valor_doc2 {
    width: 180px;
}
.boleto-bb table.line td.valor_pago {
    width:118px;
    text-align:right;
    padding-right: 5px;
}
.boleto-bb table.line td.instrucoes {
    width: 474px;
}

.boleto-bb table.line td.desconto2 {
    width: 180px; 
}

.boleto-bb table.line td.outras_deducoes2 {
    /* width: 180px; */
    width: 175px;
    padding-right: 5px;
}

.boleto-bb table.line td.mora_multa2 {
    /* width: 180px; */
    width: 175px;
    padding-right: 5px;
}

.boleto-bb table.line td.outros_acrescimos2 {
    /* width: 180px; */
    width: 175px;
    padding-right: 5px;
}

.boleto-bb table.line td.valor_cobrado2 {
    /* width: 180px; */
    width: 175px;
    padding-right: 5px;
}

.boleto-bb table.line td.sacado2 {
    width: 470px;
}

.boleto-bb table.line td.sacador_avalista {
    width: 659px;
}

.boleto-bb table.line tr.campos td.sacador_avalista {
    width: 472px;
}

.boleto-bb table.line td.cod_baixa {
    color: navy;
    width: 180px;
}




.boleto-bb div.footer {
    margin-bottom: 30px;
}

.boleto-bb div.footer p {
    width: 88px;
    margin: 0;
    padding: 0;
    padding-left: 525px;
    font-family: 'Arial Narro';
    font-size: 9px;
    color: navy;
}


.boleto-bb div.barcode {
    width: 666px;
    margin-bottom: 20px;
}

.boleto-bb .remove-borda {
    border: none !important;
}

.boleto-bb .remove-linha-amarela_titulo {
    border-left: none !important;
}
.boleto-bb .remove-linha-amarela {
    border-left: 5px #fff solid !important;
}
.boleto-bb .coluna1benef {
    width: 417px;
    padding-left: 8px !important;
}

.boleto-bb .coluna2benef {
    width: 239px;
}

}



@media print {

.boleto-bb #instructions {
    height: 1px;
    visibility: hidden;
    overflow: hidden;
}

}

</STYLE>

</head>
<body>
<div class="boleto-bb" style="margin: 0 auto; width: 666px;">

<div id="container">
    <div id="instr_header">
        <h1>
            <?php echo '2ª Via do boleto - inFlux'; ?> 
            <?php
                if ($parametros['cedente_cnpj'] !== '') {
                    echo "<br>" . mask($parametros['cedente_cnpj'], '##.###.###/####-##');
                } else {
                    echo "";
                }
            ?>
        </h1>
        <address>
            <?php echo $parametros['cedente_endereco']; ?>
            <br>
        </address>
        <address>
            <?php echo ""; ?>
        </address>
    </div>  <!-- id="instr_header" -->

    <div id="boleto">
    <table class="header" border=0 cellspacing="0" cellpadding="0">
        <tbody>
            <tr>
                <td width=150>
                    <IMG src="data:image/png;base64, <?php echo $logoBBBase64_image; ?>">
                </td>
                <td width=50>
                    <div class="field_cod_banco">
                        <?php echo $parametros['codigo_banco_com_dv'];?>
                    </div>
                </td>
                <td class="linha_digitavel">
                    <?php echo $parametros['linha_digitavel'];?>
                </td>
            </tr>
        </tbody>
    </table>

    <table class="line remove-borda" cellspacing="0" cellpadding="0">
        <tbody>
            <tr class="titulos">
                <td class="cedente">
                    Nome do Pagador/CPF/CNPJ/Endereço
                </td>
            </tr>
            <tr class="campos">
                <td class="cedente">
                    <?php
                       echo $parametros["sacado_nome"] . "&nbsp;&nbsp;&nbsp;";
                       if (strlen($parametros["sacado_cpf_cnpj"]) < 12) {
                        echo "CPF/CNPJ: " . mask($parametros['sacado_cpf_cnpj'], '###.###.###-##');
                       } else {
                        echo "CPF/CNPJ: " . mask($parametros['sacado_cpf_cnpj'], '##.###.###/####-##');
                       }
                    ?>
                </td>
            </tr>
        </tbody>
    </table>
    <table class="line" cellspacing="0" cellpadding="0">
        <tbody>
            <tr class="campos">
                <td class="cedente">
                    <?php echo "{$parametros["sacado_endereco"]}, {$parametros["sacado_endereco_numero"]},  {$parametros["sacado_bairro"]} - {$parametros["sacado_cidade"]}/{$parametros["sacado_estado"]} - CEP: " . mask($parametros["sacado_cep"], '##.###-###');?>
                </td>
            </tr>
            <tr class="titulos">
                <td class="cedente">
                    Sacador/Avalista
                </td>
            </tr>
        </tbody>
    </table>

    <table class="line" cellspacing="0" cellPadding="0">
        <tbody>
            <tr class="titulos">
                <td class="num_doc">
                    Nosso-Número
                </td>
                <td class="cpf_cei_cnpj">
                    Nr. Documento
                </TD>
                <td class="cpf_cei_cnpj">
                    Data de Vencimento
                </TD>
                <td class="valor_doc">
                    Valor do Documento
                </TD>
                <td class="valor_doc">
                    (=) Valor Pago
                </TD>
            </tr>
            <tr class="campos">
                <td class="num_doc">
                    <?php echo $parametros["nosso_numero"];?>
                </td>
                <td class="cpf_cei_cnpj">
                    <?php echo $parametros["numero_documento"];?>
                </td>
                <td class="cpf_cei_cnpj">
                    <?php echo $parametros["data_vencimento"]->format('d/m/Y');?>
                </td>
                <td class="valor_doc">
                    <?php echo $parametros["valor_saldo"];?>
                </td>
                <td class="valor_doc">
                    <?php echo ""?>
                </td>
            </tr>
        </tbody>
    </table>

    <table class="line remove-borda" cellspacing="0" cellpadding="0">
        <tbody>
            <tr class="titulos">
                <td class="sacado">
                    Nome do Beneficiário/CPF/CNPJ/Endereço
                </td>
            </tr>
            <tr class="campos">
                <td class="sacado">
                    <?php
                       echo $parametros["cedente_nome"] . "&nbsp;&nbsp;&nbsp;";
                       echo "CNPJ - " . mask($parametros['cedente_cnpj'], '##.###.###/####-##');
                    ?>
                </td>
            </tr>
        </tbody>
    </table>

    <table class="line" cellspacing="0" cellpadding="0">
        <tbody>
            <tr class="campos">
                <td class="sacado">
                    <?php echo $parametros['cedente_endereco'];?>
                </td>
            </tr>
            <tr class="campos">
                <td class="sacado">
                </td>
            </tr>
        </tbody>
    </table>

    <table class="line remove-borda" cellspacing="0" cellpadding="0" border="1">
        <tbody>
            <tr class="titulos">
                <td class="remove-borda coluna1benef">
                    Agência/Código do Beneficiário
                </td>
                <td class="remove-borda coluna2benef">
                    Autentição Mecânica
                </td>
            </tr>
            <tr class="campos">
                <td class="remove-borda coluna1benef">  
                    <?php echo $parametros['agencia_conta'];?>
                </td>
                <td class="remove-borda"></td>
            </tr>
        </tbody>
    </table>

    <br />

    <div class="cut">
        <p>
            Corte na linha pontilhada
        </p>
    </div>


    <table class="header" border="0" cellspacing="0" cellpadding="0">
        <tbody>
            <tr>
                <td width="150">
                    <IMG src="data:image/png;base64, <?php echo $logoBBBase64_image; ?>">
                </td>
                <td width="50">
                    <div class="field_cod_banco">
                        <?php echo $parametros['codigo_banco_com_dv'];?>
                    </div>
                </td>
                <td class="linha_digitavel">
                    <?php echo $parametros['linha_digitavel'];?>
                </td>
            </tr>
        </tbody>
    </table>

    <table class="line" cellspacing="0" cellpadding="0">
        <tbody>
            <tr class="titulos">
                <td class="local_pagto">
                    Local de Pagamento
                </td>
                <td class="vencimento2">
                    Data de Vencimento
                </td>
            </tr>
            <tr class="campos">
                <td class="local_pagto pagavel">
                    PAGÁVEL EM QUALQUER BANCO ATÉ O VENCIMENTO.
                </td>
                <td class="vencimento2">
                    <?php echo $parametros["data_vencimento"]->format('d/m/Y');?>
                </td>
            </tr>
        </tbody>
    </table>

    <table class="line" cellspacing="0" cellpadding="0">
        <tbody>
            <tr class="titulos">
                <td class="cedente2">
                    Nome do Beneficiário/CPF/CNPJ
                </td>
                <td class="ag_cod_cedente2">
                    Agência/Código do Beneficiário
                </td>
            </tr>
            <tr class="campos">
                <td class="cedente2">
                    <?php
                       echo $parametros["cedente_nome"] . "&nbsp;&nbsp;&nbsp;";
                       echo "CNPJ - " . mask($parametros['cedente_cnpj'], '##.###.###/####-##');
                    ?>
                </td>
                <td class="ag_cod_cedente2">
                    <?php echo $parametros['agencia_conta'];?>
                </td>
            </tr>
        </tbody>
    </table>

    <table class="line" cellspacing="0" cellpadding="0">
        <tbody>
            <tr class="titulos">
                <td class="data_doc">
                    Data do Documento
                </td>
                <td class="num_doc2">
                    Nr. Documento
                </td>
                <td class="especie_doc">
                    Espécie DOC
                </td>
                <td class="aceite">
                    Aceite
                </td>
                <td class="data_process">
                    Data do Processamento
                </td>
                <td class="nosso_numero2">
                    Nosso-Número
                </td>
            </tr>
            <tr class="campos">
                <td class="data_doc">
                    <?php echo $parametros["data_documento"]->format('d/m/Y');?>
                </td>
                <td class="num_doc2">
                    <?php echo $parametros["numero_documento"];?>
                </td>
                <td class="especie_doc">
                    <?php echo $parametros['especie_doc'];?>
                </td>
                <td class="aceite">
                    <?php echo $parametros['aceite']?>
                </td>
                <td class="data_process">
                    <?php echo date('d/m/Y');?>
                </td>
                <td class="nosso_numero2">
                    <?php echo $parametros['nosso_numero'];?> 
                </td>
            </tr>
        </tbody>
    </table>

    <table class="line" cellspacing="0" cellPadding="0">
        <tbody>
            <tr class="titulos">
                <td class="reservado">
                    Uso do Banco
                </td>
                <td class="carteira">
                    Carteira
                </td>
                <td class="especie2">
                    Espécie
                </td>
                <td class="qtd2">
                    Quantidade
                </td>
                <td class="xvalor">
                    xValor
                </td>
                <td class="valor_doc2">
                    (=) Valor do Documento
                </td>
            </tr>
            <tr class="campos">
                <td class="reservado">
                </td>
                <td class="carteira">
                    <?php echo $parametros["carteira"];?>
                    <?php
                        if ($parametros["variacao_carteira"] !== '') {
                            echo $parametros["variacao_carteira"];
                        } else {
                            echo '&nbsp;';
                        }
                    ?>
                </td>
                <td class="especie2">
                    <?php echo $parametros['especie'];?>
                </td>
                <td class="qtd2">
                    <?php echo ""?>
                </td>
                <td class="xvalor">
                    <?php echo ""?>
                </td>
                <td class="valor_doc2">
                    <?php echo $parametros["valor_saldo"];?>
                </td>
            </tr>
        </tbody>
    </table>


    <table class="line" cellspacing="0" cellpadding="0">
        <tbody>
            <tr>
                <td class="last_line" rowspan="6">
                    <table class="line" cellspacing="0" cellpadding="0">
                        <tbody>
                            <tr class="titulos">
                                <td class="instrucoes">
                                        Informações de Responsabilidade do Beneficiário 
                                </td>
                            </tr>
                            <tr class="campos">
                                <td class="instrucoes" rowspan="5">
                                    <p><?php echo $parametros["instrucoes"]; ?></p>     
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </td>
            </tr>
            <tr>
                <td>
                    <table class="line" cellspacing="0" cellpadding="0">
                        <tbody>
                            <tr class="titulos">
                                <td class="desconto2">
                                    (-) Desconto/Abatimento
                                </td>
                            </tr>
                            <tr class="campos">
                                <td class="desconto2">&nbsp;</td>
                            </tr>
                        </tbody>
                    </table>
                </td>
            </tr>
            <tr>
                <td>
                    <table class="line" cellspacing="0" cellpadding="0">
                        <tbody>
                            <tr class="titulos">
                                <td class="outras_deducoes2">
                                    (+) Juros/Multa 
                                </td>
                            </tr>
                            <tr class="campos">
                                <td class="outras_deducoes2">&nbsp;</td>
                            </tr>
                        </tbody>
                    </table>
                </td>
            </tr>
            <tr>
                <td>
                    <table class="line" cellspacing="0" cellpadding="0">
                        <tbody>
                            <tr class="titulos">
                                <td class="valor_cobrado">
                                    (=) Valor Cobrado
                                </td>
                            </tr>
                            <tr class="campos">
                                <td class="valor_cobrado">
                                    <?php
                                    // echo "valor_boleto"?>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </td>
            </tr>
            <tr>
                <td >
                    <table class="line" cellspacing="0" cellpadding="0">
                        <tbody>
                            <tr class="titulos">
                                <td class="outros_acrescimos2">
                                </td>
                            </tr>
                            <tr class="campos">
                                <td class="outros_acrescimos2">&nbsp;</td>
                            </tr>
                        </tbody>
                    </table>
                </td>
            </tr>
            <tr>
                <td>
                    <table cellspacing="0" cellpadding="0">
                        <tbody>
                            <tr class="titulos">
                                <td class="valor_cobrado2">                                 
                                </td>
                            </tr>
                            <tr class="campos">
                                <td class="valor_cobrado2">&nbsp;</td>
                            </tr>
                        </tbody>
                    </table>
                </td>
            </tr>
        </tbody>
    </table>


    <table class="line" cellspacing="0" cellPadding="0">
        <tbody>
            <tr class="titulos">
                <td class="sacado2 remove-linha-amarela_titulo" colspan="2">
                    Nome do Pagador/CPF/CNPJ/Endereço
                </td>
            </tr>
            <tr class="campos">
                <td class="sacado2 remove-linha-amarela">
                    <p><?php
                           echo $parametros["sacado_nome"] . "&nbsp;&nbsp;&nbsp;";
                           if (strlen($parametros["sacado_cpf_cnpj"]) < 12) {
                        echo "CPF/CNPJ: " . mask($parametros['sacado_cpf_cnpj'], '###.###.###-##');
                           } else {
                        echo "CPF/CNPJ: " . mask($parametros['sacado_cpf_cnpj'], '##.###.###/####-##');
                           }
                        ?></p>                  
                    <p><?php echo "{$parametros["sacado_endereco"]}, {$parametros["sacado_endereco_numero"]},  {$parametros["sacado_bairro"]} - {$parametros["sacado_cidade"]}/{$parametros["sacado_estado"]} - CEP: " . mask($parametros["sacado_cep"], '##.###-###');?></p>
                </td>
                <td class="valor_cobrado2 remove-linha-amarela alinha-bottom">
                    Código de Baixa 
                </td>
            </tr>
        </tbody>
    </table>        

    <table class="line remove-borda" cellspacing="0" cellpadding="0">
        <tbody>
            <tr>
                <td width="415" valign="top">
                    <table style="margin-left: -3px; margin-top: -2px">
                        <tbody>
                            <tr class="titulos">
                                <td class="remove-linha-amarela_titulo" valign="top">
                                    Sacador/Avalista
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </td>
                <td>
                    <table style="margin-top: -3px">
                        <tbody>
                            <tr>
                                <td>
                                    <span style="font-size: 10px;">
                                        Autenticação Mecânica -
                                    </span>
                                    <span style="font-size: 11px; font-weight: bold;">
                                        Ficha de Compensação
                                    </span>
                                    
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </td>
            </tr>
        </tbody>
    </table>        

    <div class="barcode">
        <p><br/><?php echo fbarcodeBB($parametros['codigo_barras'], $host); ?></p>
    </div>
<!--    <div class="cut"> -->
<!--        <p>Corte na linha pontilhada</p> -->
<!--    </div> -->
</div>

</div>

</div>
</body>

</html>
