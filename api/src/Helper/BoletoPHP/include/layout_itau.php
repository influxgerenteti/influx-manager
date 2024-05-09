<?php include 'get_base64_image.php'; ?>
<!DOCTYPE HTML PUBLIC '-//W3C//DTD HTML 4.0 Transitional//EN'>
<HTML>
<HEAD>
    <title><?php echo "2ª Via do boleto - inFlux"; ?></title>
    <META http-equiv=Content-Type content=text/html charset=UTF-8>
    <meta name="Generator" content="Projeto BoletoPHP - www.boletophp.com.br - Licen�a GPL" />
    <style type=text/css>
    .boleto-itau .cp {
        font: bold 10px Arial;
        color: black
    }
    .boleto-itau .ti {
        font: 9px Arial, Helvetica, sans-serif
    }
    .boleto-itau .ld {
        font: bold 12px Arial;
        color: #000000
    }
    .boleto-itau .ct {
        FONT: 9px "Arial Narrow";
        COLOR: #000033
    }
    .boleto-itau .ct2 {
        FONT: 9px "Arial Narrow";
        COLOR: #000033;
        padding-left: 88px;
    }
    .boleto-itau .cn {
        FONT: 9px Arial;
        COLOR: black
    }
    .boleto-itau .bc {
        font: bold 14px Arial;
        color: #000000
    }
    .boleto-itau .ld2 {
        font: bold 12px Arial;
        color: #000000
    }
    .boleto-itau .campo {
        padding-left: 5px;
        padding-right: 5px;
    }
    </style>
</head>

<BODY text=#000000 bgColor=#ffffff topMargin=0 rightMargin=0>
<div class="boleto-itau" style="margin: 0 auto; width: 666px;">
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
    <table cellspacing=0 cellpadding=0 width=666 border=0 > 
        <tr>
            <td width=230>
            </td>
            <td class="ct2" width=130>
                Autenticação Mecânica
            </td>
            <td width=218>
            </td>
        </tr>
        <tr>
            <td>
            </td>
            <td width=218>
            </td>
            <td class="cp" width=218 align="right">
                Recibo do Pagador
            </td>           
        </tr>       
        <tr>
            <td width=230>
            </td>
            <td valign=top width=218 height=1>
                <img height=1 src="data:image/png;base64, <?php echo $Base64_2_image; ?>" width=218 border=0>
            </td>
            <td valign=top width=218 height=1>
                <img height=1 src="data:image/png;base64, <?php echo $Base64_2_image; ?>" width=218 border=0>
            </td>
        </tr>
    </table>
    <table cellspacing=0 cellpadding=0 width=666 border=0>
        <tr>
            <td class=cp width=230>
                <span class="campo">
                    <IMG SRC="<?php echo 'http://' . $host . '/images/boleto/logoitau.jpg'; ?>" width="90" border=0>
                </span>
            </td>
            <td>            
                <table cellspacing=0 cellpadding=0 border=0 >
                    <tbody>
                        <tr>
                            <td class=ct valign=top width=7 height=13>
                                <img height=13 src="data:image/png;base64, <?php echo $Base64_1_image; ?>" width=1 border=0>
                            </td>
                            <td class=ct valign=top width=211 height=13>
                                Vencimento
                            </td>
                            <td class=ct valign=top width=7 height=13>
                                <img height=13 src="data:image/png;base64, <?php echo $Base64_1_image; ?>" width=1 border=0>
                            </td>
                            <td class=ct valign=top width=211 height=13>
                                Valor do Documento
                            </td>
                        </tr>
                        <tr>
                            <td class=cp valign=top width=7 height=15>
                                <img height=15 src="data:image/png;base64, <?php echo $Base64_1_image; ?>" width=1 border=0>
                            </td>
                            <td class=cp valign=top width=180 height=15>
                                <span class="campo">
                                    <?php echo $parametros["data_vencimento"]->format('d/m/Y');?>
                                </span>
                            </td>
                            <td class=cp valign=top width=7 height=15>
                                <img height=15 src="data:image/png;base64, <?php echo $Base64_1_image; ?>" width=1 border=0>
                            </td>                       
                            <td class=cp valign=top width=180 align=right height=15>
                                <span class="campo">
                                    <?php echo $parametros["valor_saldo"];?>
                                </span>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </td>
        </tr>
        <tbody>
            <tr>
                <td colspan=5>
                    <img height=1 src="data:image/png;base64, <?php echo $Base64_2_image; ?>" width=666 border=0>
                </td>
            </tr>
        </tbody>
    </table>
    <table cellspacing=0 cellpadding=0 width=666 border=0>
        <tbody>
            <tr>
                <td class=ct valign=top width=7 height=13>
                </td>
                <td class=ct valign=top width=313 height=13>
                    Pagador
                </td>
                <td class=ct valign=top width=7 height=13>
                    <img height=13  src="data:image/png;base64, <?php echo $Base64_1_image; ?>" width=1 border=0>
                </td>
                <td class=ct valign=top width=339 height=13>
                    Beneficiário
                </td>
            </tr>
            <tr>
                <td class=cp valign=top width=7 height=12>
                </td>
                <td class=cp valign=top width=313 height=12>
                    <span class="campo">
                        <?php echo $parametros["sacado_nome"]; ?>
                    </span>
                </td>
                <td class=cp valign=top width=7 height=14>
                    <img height=14 src="data:image/png;base64, <?php echo $Base64_1_image; ?>" width=1 border=0>
                </td>
                <td class=cp valign=top width=339 height=12>
                    <span class="campo">
                        <?php echo $parametros["cedente_nome"]?>
                    </span>
                </td>
            </tr>
            <tr>
                <td valign=top width=7 height=1>
                    <img height=1 src="data:image/png;base64, <?php echo $Base64_2_image; ?>" width=7 border=0>
                </td>
                <td valign=top width=313 height=1>
                    <img height=1 src="data:image/png;base64, <?php echo $Base64_2_image; ?>" width=313 border=0>
                </td>
                <td valign=top width=7 height=1>
                    <img height=1 src="data:image/png;base64, <?php echo $Base64_2_image; ?>" width=7 border=0>
                </td>
                <td valign=top width=339 height=1>
                    <img height=1 src="data:image/png;base64, <?php echo $Base64_2_image; ?>" width=339 border=0>
                </td>
            </tr>
        </tbody>
    </table>
    <table cellspacing=0 cellpadding=0 border=0>
        <tbody>
            <tr>
                <td class=ct valign=top width=7 height=13>
                </td>
                <td class=ct valign=top width=659 height=13>
                    Endereço Beneficiário / Sacador Avalista
                </td>
            </tr>
            <tr>
                <td class=cp valign=top width=7 height=12>
                </td>
                <td class=cp valign=top width=659 height=12>
                    <span class="campo">
                        <?php echo $parametros["cedente_endereco"];?>
                    </span>
                </td>
            </tr>
            <tr>
                <td valign=top width=7 height=1>
                    <img height=1 src="data:image/png;base64, <?php echo $Base64_2_image; ?>" width=7 border=0>
                </td>
                <td valign=top width=659 height=1>
                    <img height=1 src="data:image/png;base64, <?php echo $Base64_2_image; ?>" width=659 border=0>
                </td>
            </tr>
        </tbody>
    </table>
    <table cellspacing=0 cellpadding=0 border=0>
        <tbody>
            <tr>
                <td class=ct valign=top width=7 height=13>
                </td>
                <td class=ct valign=top width=168 height=13>
                    Agência / Código Beneficiário
                </td>
                <td class=ct valign=top width=7 height=13>
                    <img height=13 src="data:image/png;base64, <?php echo $Base64_1_image; ?>" width=1 border=0>
                </td>
                <td class=ct valign=top width=160 height=13>
                    Nosso Número
                </td>
                <td class=ct valign=top width=7 height=13>
                    <img height=13 src="data:image/png;base64, <?php echo $Base64_1_image; ?>" width=1 border=0>
                </td>
                <td class=ct valign=top width=150 height=13>
                    Nº Documento
                </td>
                <td class=ct valign=top width=7 height=13>
                    <img height=13 src="data:image/png;base64, <?php echo $Base64_1_image; ?>" width=1 border=0>
                </td>
                <td class=ct valign=top width=160 height=13>
                    CNPJ
                </td>
            </tr>
            <tr>
                <td class=cp valign=top width=7 height=12>
                </td>
                <td class=cp valign=top width=168 height=12>
                    <span class="campo">
                        <?php echo $parametros['agencia_conta'];?>
                    </span>
                </td>
                <td class=cp valign=top width=7 height=12>
                    <img height=12 src="data:image/png;base64, <?php echo $Base64_1_image; ?>" width=1 border=0>
                </td>
                <td class=cp valign=top width=160 height=12>
                    <span class="campo">
                        <?php echo $parametros["nosso_numero"]?>
                    </span>
                </td>
                <td class=cp valign=top width=7 height=12>
                    <img height=12 src="data:image/png;base64, <?php echo $Base64_1_image; ?>" width=1 border=0>
                </td>
                <td class=cp valign=top width=150 height=12>
                    <span class="campo">
                        <?php echo $parametros["numero_documento"];?>
                    </span>
                </td>
                <td class=cp valign=top width=7 height=12>
                    <img height=12 src="data:image/png;base64, <?php echo $Base64_1_image; ?>" width=1 border=0>
                </td>
                <td class=cp valign=top width=160 height=12>
                    <span class="campo">
                        <?php echo mask($parametros['cedente_cnpj'], '##.###.###/####-##'); ?>                       
                    </span>
                </td>
            </tr>
            <tr>
                <td valign=top width=7 height=1>
                    <img height=1 src="data:image/png;base64, <?php echo $Base64_2_image; ?>" width=7 border=0>
                </td>
                <td valign=top width=168 height=1>
                    <img height=1 src="data:image/png;base64, <?php echo $Base64_2_image; ?>" width=168 border=0>
                </td>
                <td valign=top width=7 height=1>
                    <img height=1 src="data:image/png;base64, <?php echo $Base64_2_image; ?>" width=7 border=0>
                </td>
                <td valign=top width=160 height=1>
                    <img height=1 src="data:image/png;base64, <?php echo $Base64_2_image; ?>" width=160 border=0>
                </td>
                <td valign=top width=7 height=1>
                    <img height=1 src="data:image/png;base64, <?php echo $Base64_2_image; ?>" width=7 border=0>
                </td>
                <td valign=top width=150 height=1>
                    <img height=1 src="data:image/png;base64, <?php echo $Base64_2_image; ?>" width=150 border=0>
                </td>
                <td valign=top width=7 height=1>
                    <img height=1 src="data:image/png;base64, <?php echo $Base64_2_image; ?>" width=7 border=0>
                </td>
                <td valign=top width=160 height=1>
                    <img height=1 src="data:image/png;base64, <?php echo $Base64_2_image; ?>" width=160 border=0>
                </td>
            </tr>
        </tbody>
    </table>
    <table cellspacing=0 cellpadding=0 width=666 border=0>
        <tbody>
            <tr>
                <td width=7>
                </td>
                <td width=500 class=cp>
                    <br><br><br><br>
                </td>
                <td width=159>
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
                    <div align=right>
                        Corte na linha pontilhada
                    </div>
                </td>
            </tr>
            <tr>
                <td class=ct width=666>
                    <img height=1 src="data:image/png;base64, <?php echo $Base64_6_image; ?>" width=665 border=0>
                </td>
            </tr>
        </tbody>
    </table>
    <br>
    <table cellspacing=0 cellpadding=0 width=666 border=0>
        <tr>
            <td class=cp width=150>
                <span class="campo">
                    <IMG SRC="<?php echo 'http://' . $host . '/images/boleto/logoitau.jpg'; ?>" width="90" border=0>
                </span>
            </td>
            <td width=3 valign=bottom>
                <img height=22 src="data:image/png;base64, <?php echo $Base64_3_image; ?>" width=2 border=0>
            </td>
            <td class=cpt width=58 valign=bottom>
                <div align=center>
                    <font class=bc>
                        <?php echo $parametros['codigo_banco_com_dv'];?>
                    </font>
                </div>
            </td>
            <td width=3 valign=bottom>
                <img height=22 src="data:image/png;base64, <?php echo $Base64_3_image; ?>" width=2 border=0>
            </td>
            <td class=ld align=right width=453 valign=bottom>
                <span class=ld> 
                    <span class="campotitulo">
                        <?php echo $parametros['linha_digitavel'];?>
                    </span>
                </span>
            </td>
        </tr>
        <tbody>
            <tr>
                <td colspan=5>
                    <img height=1 src="data:image/png;base64, <?php echo $Base64_2_image; ?>" width=666 border=0>
                </td>
            </tr>
        </tbody>
    </table>
    <table cellspacing=0 cellpadding=0 border=0>
        <tbody>
            <tr>
                <td class=ct valign=top width=7 height=13>
                </td>
                <td class=ct valign=top width=472 height=13>
                    Local de Pagamento
                </td>
                <td class=ct valign=top width=7 height=13>
                    <img height=13 src="data:image/png;base64, <?php echo $Base64_1_image; ?>" width=1 border=0>
                </td>
                <td class=ct valign=top width=180 height=13>
                    Vencimento
                </td>
            </tr>
            <tr>
                <td class=cp valign=top width=7 height=12>
                </td>
                <td class=cp valign=top width=472 height=12>
                    ATÉ O VENCIMENTO, PREFERENCIALMENTE NO ITAÚ<br>
                    APÓS O VENCIMENTO, SOMENTE NO ITAÚ
                </td>
                <td class=cp valign=top width=7 height=12>
                    <img height=28 src="data:image/png;base64, <?php echo $Base64_1_image; ?>" width=1 border=0>
                </td>
                <td class=cp valign=bottom align=right width=180 height=12>
                    <span class="campo">
                        <?php echo $parametros["data_vencimento"]->format('d/m/Y');?>
                    </span>
                </td>
            </tr>
            <tr>
                <td valign=top width=7 height=1>
                    <img height=1 src="data:image/png;base64, <?php echo $Base64_2_image; ?>" width=7 border=0>
                </td>
                <td valign=top width=472 height=1>
                    <img height=1 src="data:image/png;base64, <?php echo $Base64_2_image; ?>" width=472 border=0>
                </td>
                <td valign=top width=7 height=1>
                    <img height=1 src="data:image/png;base64, <?php echo $Base64_2_image; ?>" width=7 border=0>
                </td>
                <td valign=top width=180 height=1>
                    <img height=1 src="data:image/png;base64, <?php echo $Base64_2_image; ?>" width=180 border=0>
                </td>
            </tr>
        </tbody>
    </table>
    <table cellspacing=0 cellpadding=0 border=0>
        <tbody>
            <tr>
                <td class=ct valign=top width=7 height=13>
                </td>
                <td class=ct valign=top width=472 height=13>
                    Beneficiário
                </td>
                <td class=ct valign=top width=7 height=13>
                    <img height=13 src="data:image/png;base64, <?php echo $Base64_1_image; ?>" width=1 border=0>
                </td>
                <td class=ct valign=top width=180 height=13>
                    Agência / Código Beneficiário
                </td>
            </tr>
            <tr>
                <td class=cp valign=top width=7 height=12>
                </td>
                <td class=cp valign=top width=472 height=12>
                    <span class="campo">
                        <?php echo $parametros["cedente_nome"];?>
                    </span>
                </td>
                <td class=cp valign=top width=7 height=12>
                    <img height=14 src="data:image/png;base64, <?php echo $Base64_1_image; ?>" width=1 border=0>
                </td>
                <td class=cp valign=top align=right width=180 height=12>
                    <span class="campo">
                        <?php echo $parametros['agencia_conta'];?>
                    </span>
                </td>
            </tr>
            <tr>
                <td valign=top width=7 height=1>
                    <img height=1 src="data:image/png;base64, <?php echo $Base64_2_image; ?>" width=7 border=0>
                </td>
                <td valign=top width=472 height=1>
                    <img height=1 src="data:image/png;base64, <?php echo $Base64_2_image; ?>" width=472 border=0>
                </td>
                <td valign=top width=7 height=1>
                    <img height=1 src="data:image/png;base64, <?php echo $Base64_2_image; ?>" width=7 border=0>
                </td>
                <td valign=top width=180 height=1>
                    <img height=1 src="data:image/png;base64, <?php echo $Base64_2_image; ?>" width=180 border=0>
                </td>
            </tr>
        </tbody>
    </table>
    <table cellspacing=0 cellpadding=0 border=0>
        <tbody>
            <tr>
                <td class=ct valign=top width=7 height=13>
                </td>
                <td class=ct valign=top width=113 height=13>
                    Data do Documento
                </td>
                <td class=ct valign=top width=7 height=13>
                    <img height=13 src="data:image/png;base64, <?php echo $Base64_1_image; ?>" width=1 border=0>
                </td>
                <td class=ct valign=top width=143 height=13>
                    Nº do Documento
                </td>
                <td class=ct valign=top width=7 height=13>
                    <img height=13 src="data:image/png;base64, <?php echo $Base64_1_image; ?>" width=1 border=0>
                </td>
                <td class=ct valign=top width=62 height=13>
                    Espécie doc.
                </td>
                <td class=ct valign=top width=7 height=13>
                    <img height=13 src="data:image/png;base64, <?php echo $Base64_1_image; ?>" width=1 border=0>
                </td>
                <td class=ct valign=top width=34 height=13>
                    Aceite
                </td>
                <td class=ct valign=top width=7 height=13>
                    <img height=13 src="data:image/png;base64, <?php echo $Base64_1_image; ?>" width=1 border=0>
                </td>
                <td class=ct valign=top width=92 height=13>
                    Data de Processamento
                </td>
                <td class=ct valign=top width=7 height=13>
                    <img height=13 src="data:image/png;base64, <?php echo $Base64_1_image; ?>" width=1 border=0>
                </td>
                <td class=ct valign=top width=180 height=13>
                    Nosso Número
                </td>
            </tr>
            <tr>
                <td class=cp valign=top width=7 height=12>
                </td>
                <td class=cp valign=top width=113 height=12>
                    <div align=left>
                        <span class="campo">
                            <?php echo $parametros["data_documento"]->format('d/m/Y');?>
                        </span>
                    </div>
                </td>
                <td class=cp valign=top width=7 height=12>
                    <img height=14 src="data:image/png;base64, <?php echo $Base64_1_image; ?>" width=1 border=0>
                </td>
                <td class=cp valign=top width=143 height=12>
                    <span class="campo">
                        <?php echo $parametros["numero_documento"];?>
                    </span>
                </td>
                <td class=cp valign=top width=7 height=12>
                    <img height=14 src="data:image/png;base64, <?php echo $Base64_1_image; ?>" width=1 border=0>
                </td>
                <td class=cp valign=top width=62 height=12>
                    <div align=left>
                        <span class="campo">
                            <?php echo $parametros["especie_doc2"]?>
                        </span>
                    </div>
                </td>
                <td class=cp valign=top width=7 height=12>
                    <img height=14 src="data:image/png;base64, <?php echo $Base64_1_image; ?>" width=1 border=0>
                </td>
                <td class=cp valign=top width=34 height=12>
                    <div align=left>
                        <span class="campo">
                            <?php echo $parametros["aceite"]?>
                        </span>
                    </div>
                </td>
                <td class=cp valign=top width=7 height=12>
                    <img height=14 src="data:image/png;base64, <?php echo $Base64_1_image; ?>" width=1 border=0>
                </td>
                <td class=cp valign=top width=92 height=12>
                    <div align=left>
                        <span class="campo">
                            <?php echo date('d/m/Y');?>
                        </span>
                    </div>
                </td>
                <td class=cp valign=top width=7 height=12>
                    <img height=14 src="data:image/png;base64, <?php echo $Base64_1_image; ?>" width=1 border=0>
                </td>
                <td class=cp valign=top align=right width=180 height=12>
                    <span class="campo">
                        <?php echo $parametros["nosso_numero"]?>
                    </span>
                </td>
            </tr>
            <tr>
                <td valign=top width=7 height=1>
                    <img height=1 src="data:image/png;base64, <?php echo $Base64_2_image; ?>" width=7 border=0>
                </td>
                <td valign=top width=113 height=1>
                    <img height=1 src="data:image/png;base64, <?php echo $Base64_2_image; ?>" width=113 border=0>
                </td>
                <td valign=top width=7 height=1>
                    <img height=1 src="data:image/png;base64, <?php echo $Base64_2_image; ?>" width=7 border=0>
                </td>
                <td valign=top width=143 height=1>
                    <img height=1 src="data:image/png;base64, <?php echo $Base64_2_image; ?>" width=143 border=0>
                </td>
                <td valign=top width=7 height=1>
                    <img height=1 src="data:image/png;base64, <?php echo $Base64_2_image; ?>" width=7 border=0>
                </td>
                <td valign=top width=62 height=1>
                    <img height=1 src="data:image/png;base64, <?php echo $Base64_2_image; ?>" width=62 border=0>
                </td>
                <td valign=top width=7 height=1>
                    <img height=1 src="data:image/png;base64, <?php echo $Base64_2_image; ?>" width=7 border=0>
                </td>
                <td valign=top width=34 height=1>
                    <img height=1 src="data:image/png;base64, <?php echo $Base64_2_image; ?>" width=34 border=0>
                </td>
                <td valign=top width=7 height=1>
                    <img height=1 src="data:image/png;base64, <?php echo $Base64_2_image; ?>" width=7 border=0>
                </td>
                <td valign=top width=92 height=1>
                    <img height=1 src="data:image/png;base64, <?php echo $Base64_2_image; ?>" width=92 border=0>
                </td>
                <td valign=top width=7 height=1>
                    <img height=1 src="data:image/png;base64, <?php echo $Base64_2_image; ?>" width=7 border=0>
                </td>
                <td valign=top width=180 height=1>
                    <img height=1 src="data:image/png;base64, <?php echo $Base64_2_image; ?>" width=180 border=0>
                </td>
            </tr>
        </tbody>
    </table>
    <table cellspacing=0 cellpadding=0 border=0>
        <tbody>
            <tr>
                <td class=ct valign=top width=7 height=13>
                </td>
                <td class=ct valign=top COLSPAN="3" height=13>
                    Uso do Banco
                </td>
                <td class=ct valign=top height=13 width=7>
                    <img height=13 src="data:image/png;base64, <?php echo $Base64_1_image; ?>" width=1 border=0>
                </td>
                <td class=ct valign=top width=83 height=13>
                    Carteira
                </td>
                <td class=ct valign=top height=13 width=7>
                    <img height=13 src="data:image/png;base64, <?php echo $Base64_1_image; ?>" width=1 border=0>
                </td>
                <td class=ct valign=top width=53 height=13>
                    Espécie
                </td>
                <td class=ct valign=top height=13 width=7>
                    <img height=13 src="data:image/png;base64, <?php echo $Base64_1_image; ?>" width=1 border=0>
                </td>
                <td class=ct valign=top width=103 height=13>
                    Quantidade
                </td>
                <td class=ct valign=top height=13 width=7>
                    <img height=13 src="data:image/png;base64, <?php echo $Base64_1_image; ?>" width=1 border=0>
                </td>
                <td class=ct valign=top width=92 height=13>
                    Valor Documento
                </td>
                <td class=ct valign=top width=7 height=13>
                    <img height=13 src="data:image/png;base64, <?php echo $Base64_1_image; ?>" width=1 border=0>
                </td>
                <td class=ct valign=top width=180 height=13>
                    (=) Valor documento
                </td>
            </tr>
            <tr>
                <td class=cp valign=top width=7 height=12>
                </td>
                <td valign=top class=cp height=12 COLSPAN="3">
                    <div align=left>
                    </div>
                </td>
                <td class=cp valign=top width=7 height=12>
                    <img height=14 src="data:image/png;base64, <?php echo $Base64_1_image; ?>" width=1 border=0>
                </td>
                <td class=cp valign=top width=83>
                    <div align=left>
                        <span class="campo">
                            <?php echo $parametros["carteira"]?>
                        </span>
                    </div>
                </td>
                <td class=cp valign=top width=7 height=12>
                    <img height=14 src="data:image/png;base64, <?php echo $Base64_1_image; ?>" width=1 border=0>
                </td>
                <td class=cp valign=top width=53>
                    <div align=left>
                        <span class="campo">
                            <?php echo $parametros["especie"]?>
                        </span>
                    </div>
                </td>
                <td class=cp valign=top width=7 height=12>
                    <img height=14 src="data:image/png;base64, <?php echo $Base64_1_image; ?>" width=1 border=0>
                </td>
                <td class=cp valign=top width=103>
                    <span class="campo">
                        <?php echo "";?>
                    </span>
                 </td>
                <td class=cp valign=top width=7 height=12>
                    <img height=14 src="data:image/png;base64, <?php echo $Base64_1_image; ?>" width=1 border=0>
                </td>
                <td class=cp valign=top width=92>
                    <span class="campo">
                        <?php echo "";?>
                    </span>
                </td>
                <td class=cp valign=top width=7 height=12>
                    <img height=14 src="data:image/png;base64, <?php echo $Base64_1_image; ?>" width=1 border=0>
                </td>
                <td class=cp valign=top align=right width=180 height=12>
                    <span class="campo">
                        <?php echo $parametros["valor_saldo"];?>
                    </span>
                </td>
            </tr>
            <tr>
                <td valign=top width=7 height=1>
                    <img height=1 src="data:image/png;base64, <?php echo $Base64_2_image; ?>" width=7 border=0>
                </td>
                <td valign=top width=7 height=1>
                    <img height=1 src="data:image/png;base64, <?php echo $Base64_2_image; ?>" width=75 border=0>
                </td>
                <td valign=top width=7 height=1>
                    <img height=1 src="data:image/png;base64, <?php echo $Base64_2_image; ?>" width=7 border=0>
                </td>
                <td valign=top width=31 height=1>
                    <img height=1 src="data:image/png;base64, <?php echo $Base64_2_image; ?>" width=31 border=0>
                </td>
                <td valign=top width=7 height=1>
                    <img height=1 src="data:image/png;base64, <?php echo $Base64_2_image; ?>" width=7 border=0>
                </td>
                <td valign=top width=83 height=1>
                    <img height=1 src="data:image/png;base64, <?php echo $Base64_2_image; ?>" width=83 border=0>
                </td>
                <td valign=top width=7 height=1>
                    <img height=1 src="data:image/png;base64, <?php echo $Base64_2_image; ?>" width=7 border=0>
                </td>
                <td valign=top width=53 height=1>
                    <img height=1 src="data:image/png;base64, <?php echo $Base64_2_image; ?>" width=53 border=0>
                </td>
                <td valign=top width=7 height=1>
                    <img height=1 src="data:image/png;base64, <?php echo $Base64_2_image; ?>" width=7 border=0>
                </td>
                <td valign=top width=103 height=1>
                    <img height=1 src="data:image/png;base64, <?php echo $Base64_2_image; ?>" width=103 border=0>
                </td>
                <td valign=top width=7 height=1>
                    <img height=1 src="data:image/png;base64, <?php echo $Base64_2_image; ?>" width=7 border=0>
                </td>
                <td valign=top width=92 height=1>
                    <img height=1 src="data:image/png;base64, <?php echo $Base64_2_image; ?>" width=92 border=0>
                </td>
                <td valign=top width=7 height=1>
                    <img height=1 src="data:image/png;base64, <?php echo $Base64_2_image; ?>" width=7 border=0>
                </td>
                <td valign=top width=180 height=1>
                    <img height=1 src="data:image/png;base64, <?php echo $Base64_2_image; ?>" width=180 border=0>
                </td>
            </tr>
        </tbody>
    </table>
    <table cellspacing=0 cellpadding=0 width=666 border=0>
        <tbody>
            <tr>
                <td width=10>
                    <table cellspacing=0 cellpadding=0 border=0 align=left>
                        <tbody>
                            <tr>
                                <td class=ct valign=top width=7 height=13>
                                </td>
                            </tr>
                            <tr>
                                <td class=cp valign=top width=7 height=12>
                                </td>
                            </tr>
                            <tr>
                                <td valign=top width=7 height=1>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </td>
                <td valign=top width=468 rowspan=5>
                    <font class=ct>
                        Instruções de responsabilidade do BENEFICIÁRIO. Qualquer dúvida sobre este boleto, contate o BENEFICIÁRIO 
                    </font>
                    <br><br>
                    <span class=cp> 
                        <?php echo $parametros["instrucoes"];?><br>
                        <?php echo "";?>
                        <br><br>
                    </span>
                </td>
                <td align=right width=188>
                    <table cellspacing=0 cellpadding=0 border=0>
                        <tbody>
                            <tr>
                                <td class=ct valign=top width=7 height=13>
                                    <img height=13 src="data:image/png;base64, <?php echo $Base64_1_image; ?>" width=1  border=0>
                                </td>
                                <td class=ct valign=top width=180 height=13>
                                    (-) Desconto/Abatimento
                                </td>
                            </tr>
                            <tr>
                                <td class=cp valign=top width=7 height=12>
                                    <img height=12 src="data:image/png;base64, <?php echo $Base64_1_image; ?>" width=1  border=0>
                                </td>
                                <td class=cp valign=top align=right width=180 height=12>
                                </td>
                            </tr>
                            <tr>
                                <td valign=top width=7 height=1>
                                    <img height=1 src="data:image/png;base64, <?php echo $Base64_2_image; ?>" width=7 border=0>
                                </td>
                                <td valign=top width=180 height=1>
                                    <img height=1 src="data:image/png;base64, <?php echo $Base64_2_image; ?>" width=180 border=0>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </td>
            </tr>
            <tr>
                <td width=10>
                    <table cellspacing=0 cellpadding=0 border=0 align=left>
                        <tbody>
                            <tr>
                                <td class=ct valign=top width=7 height=13>
                                </td>
                            </tr>
                            <tr>
                                <td class=cp valign=top width=7 height=12>
                                </td>
                            </tr>
                            <tr>
                                <td valign=top width=7 height=1>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </td>
                <td align=right width=188>
                    <table cellspacing=0 cellpadding=0 border=0>
                        <tbody>
                            <tr>
                                <td class=ct valign=top width=7 height=13>
                                    <img height=13 src="data:image/png;base64, <?php echo $Base64_1_image; ?>" width=1  border=0>
                                </td>
                                <td class=ct valign=top width=180 height=13>
                                </td>
                            </tr>
                            <tr>
                                <td class=cp valign=top width=7 height=12>
                                    <img height=12 src="data:image/png;base64, <?php echo $Base64_1_image; ?>" width=1  border=0>
                                </td>
                                <td class=cp valign=top align=right width=180 height=12>
                                </td>
                            </tr>
                            <tr>
                                <td valign=top width=7 height=1>
                                    <img height=1 src="data:image/png;base64, <?php echo $Base64_2_image; ?>" width=7 border=0>
                                </td>
                                <td valign=top width=180 height=1>
                                    <img height=1 src="data:image/png;base64, <?php echo $Base64_2_image; ?>" width=180 border=0>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </td>
            </tr>
            <tr>
                <td width=10>
                    <table cellspacing=0 cellpadding=0 border=0 align=left>
                        <tbody>
                            <tr>
                                <td class=ct valign=top width=7 height=13>
                                </td>
                            </tr>
                            <tr>
                                <td class=cp valign=top width=7 height=12>
                                </td>
                            </tr>
                            <tr>
                                <td valign=top width=7 height=1>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </td>
                <td align=right width=188>
                    <table cellspacing=0 cellpadding=0 border=0>
                        <tbody>
                            <tr>
                                <td class=ct valign=top width=7 height=13>
                                    <img height=13 src="data:image/png;base64, <?php echo $Base64_1_image; ?>" width=1  border=0>
                                </td>
                                <td class=ct valign=top width=180 height=13>
                                    (+) Mora/Multa
                                </td>
                            </tr>
                            <tr>
                                <td class=cp valign=top width=7 height=12>
                                    <img height=12 src="data:image/png;base64, <?php echo $Base64_1_image; ?>" width=1  border=0>
                                </td>
                                <td class=cp valign=top align=right width=180 height=12>
                                </td>
                            </tr>
                            <tr>
                                <td valign=top width=7 height=1>
                                    <img height=1 src="data:image/png;base64, <?php echo $Base64_2_image; ?>" width=7 border=0>
                                </td>
                                <td valign=top width=180 height=1>
                                    <img height=1 src="data:image/png;base64, <?php echo $Base64_2_image; ?>" width=180 border=0>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </td>
            </tr>
            <tr>
                <td width=10><table cellspacing=0 cellpadding=0 border=0>
                        <tbody>
                            <tr>
                                <td class=ct valign=top width=7 height=13>
                                </td>
                            </tr>
                            <tr>
                                <td class=cp valign=top width=7 height=12>
                                </td>
                            </tr>
                            <tr>
                                <td valign=top width=7 height=1>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </td>
                <td align=right width=188>
                    <table cellspacing=0 cellpadding=0 border=0>
                        <tbody>
                            <tr>
                                <td class=ct valign=top width=7 height=13>
                                    <img height=13 src="data:image/png;base64, <?php echo $Base64_1_image; ?>" width=1  border=0>
                                </td>
                                <td class=ct valign=top width=180 height=13>
                                </td>
                            </tr>
                            <tr>
                                <td class=cp valign=top width=7 height=12>
                                    <img height=12 src="data:image/png;base64, <?php echo $Base64_1_image; ?>" width=1  border=0>
                                </td>
                                <td class=cp valign=top align=right width=180 height=12>
                                </td>
                            </tr>
                            <tr>
                                <td valign=top width=7 height=1>
                                    <img height=1 src="data:image/png;base64, <?php echo $Base64_2_image; ?>" width=7 border=0>
                                </td>
                                <td valign=top width=180 height=1>
                                    <img height=1 src="data:image/png;base64, <?php echo $Base64_2_image; ?>" width=180 border=0>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </td>
            </tr>
            <tr>
                <td width=10>
                    <table cellspacing=0 cellpadding=0 border=0 align=left>
                        <tbody>
                            <tr>
                                <td class=ct valign=top width=7 height=13>
                                </td>
                            </tr>
                            <tr>
                                <td class=cp valign=top width=7 height=12>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </td>
                <td align=right width=188>
                    <table cellspacing=0 cellpadding=0 border=0>
                        <tbody>
                            <tr>
                                <td class=ct valign=top width=7 height=13>
                                    <img height=13 src="data:image/png;base64, <?php echo $Base64_1_image; ?>" width=1 border=0>
                                </td>
                                <td class=ct valign=top width=180 height=13>
                                    (=) Valor cobrado
                                </td>
                            </tr>
                            <tr>
                                <td class=cp valign=top width=7 height=12>
                                    <img height=12 src="data:image/png;base64, <?php echo $Base64_1_image; ?>" width=1 border=0>
                                </td>
                                <td class=cp valign=top align=right width=180 height=12>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </td>
            </tr>
        </tbody>
    </table>
    <table cellspacing=0 cellpadding=0 width=666 border=0>
        <tbody>
            <tr>
                <td valign=top width=666 height=1>
                    <img height=1 src="data:image/png;base64, <?php echo $Base64_2_image; ?>" width=666 border=0>
                </td>
            </tr>
        </tbody>
    </table>
    <table cellspacing=0 cellpadding=0 border=0>
        <tbody>
            <tr>
                <td class=ct valign=top width=7 height=13>
                    
                </td>
                <td class=ct valign=top width=100 height=13>
                    Pagador:
                </td>
                <td class=cp valign=top width=309 height=13>
                    <span class="campo">
                        <?php echo $parametros["sacado_nome"]?>
                    </span>
                </td>
                <td class=cp valign=top width=250 height=13>
                    <span class="campo">
                        <?php
                        if (strlen($parametros["sacado_cpf_cnpj"]) < 12) {
                            echo "CPF/CNPJ - " . mask($parametros['sacado_cpf_cnpj'], '###.###.###-##');
                        } else {
                            echo "CPF/CNPJ - " . mask($parametros['sacado_cpf_cnpj'], '##.###.###/####-##');
                        }?>
                    </span>
                </td>
            </tr>
            <tr>
                <td class=cp valign=top width=7 height=12>
                </td>
                <td class=ct valign=top width=100 height=13>
                    Endereço:
                </td>
                <td class=cp valign=top width=559 colspan="2" height=13>
                    <span class="campo">
                        <?php echo "{$parametros["sacado_endereco"]}, {$parametros["sacado_endereco_numero"]},  {$parametros["sacado_bairro"]} - {$parametros["sacado_cidade"]}/{$parametros["sacado_estado"]} - CEP: " . mask($parametros["sacado_cep"], '##.###-###');?>
                    </span>
                </td>
            </tr>
        </tbody>
    </table>
    <table cellspacing=0 cellpadding=0 border=0>
        <tbody>
            <tr>
                <td class=cp valign=top width=7 height=12>
                </td>
                <td class=ct valign=top width=100 height=13>
                    Sacador/Avalista:
                </td>
                <td class=cp valign=top width=309 height=13>
                    <span class="campo">
                        <?php echo $parametros["cedente_nome"];?>
                    </span>
                </td>
                <td class=cp valign=top width=250 height=13>
                    <span class="campo">
                        <?php echo "CNPJ - " . mask($parametros['cedente_cnpj'], '##.###.###/####-##'); ?>
                    </span>
                </td>
            </tr>
            <tr>
                <td valign=top width=7 height=1>
                    <img height=1 src="data:image/png;base64, <?php echo $Base64_2_image; ?>" width=7 border=0>
                </td>
                <td valign=top width=100 height=1>
                    <img height=1 src="data:image/png;base64, <?php echo $Base64_2_image; ?>" width=100 border=0>
                </td>
                <td valign=top width=309 height=1>
                    <img height=1 src="data:image/png;base64, <?php echo $Base64_2_image; ?>" width=309 border=0>
                </td>
                <td valign=top width=250 height=1>
                    <img height=1 src="data:image/png;base64, <?php echo $Base64_2_image; ?>" width=250 border=0>
                </td>
            </tr>
        </tbody>
    </table>

    <TABLE cellSpacing=0 cellPadding=0 border=0 width=666>
        <TBODY>
            <TR>
                <TD class=ct width=7 height=12>
                </TD>
                <TD class=ct width=409>
                    
                </TD>
                <TD class=ct width=250>
                    <div align=right>
                        <b class=cp>Ficha de Compensação</b><br>Autenticação Mecânica
                    </div>
                </TD>
            </TR>
            <TR>
                <TD class=ct colspan=3></TD>
            </tr>
        </tbody>
    </table>
    <TABLE cellSpacing=0 cellPadding=0 width=666 border=0>
        <TBODY>
            <TR>
                <TD vAlign=bottom align=left height=50>
                    <?php echo fbarcodeItau($parametros['codigo_barras'], $host); ?>
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
                    <div align=right>
                        Corte na linha pontilhada
                    </div>
                </TD>
            </TR>
            <TR>
                <TD class=ct width=666>
                    <img height=1 src="data:image/png;base64, <?php echo $Base64_6_image; ?>" width=665 border=0>
                </TD>
            </tr>
        </tbody>
    </table>
</div>
</BODY>
</HTML>
