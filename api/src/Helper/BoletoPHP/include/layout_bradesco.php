<?php include 'get_base64_image.php'; ?>
<!DOCTYPE HTML PUBLIC '-//W3C//DTD HTML 4.0 Transitional//EN'>
<HTML>
    <HEAD>
        <title><?php echo "2ª Via do boleto - inFlux"; ?></title>
        <META http-equiv=Content-Type content=text/html charset=UTF-8>
        <meta name="Generator" content="Projeto BoletoPHP - www.boletophp.com.br - Licen�a GPL" />
        <style type=text/css>
        .boleto-bradesco .cp {
            font: bold 10px Arial;
            color: black
        }

        .boleto-bradesco .cp2 {
            font: bold 11px Arial;
            color: black
        }

        .boleto-bradesco .ti {
            font: 9px Arial, Helvetica, sans-serif
        }

        .boleto-bradesco .ld {
            font: bold 11px Arial;
            color: #000000
        }

        .boleto-bradesco .ct {
            FONT: 7px "Arial Narrow";
            COLOR: #000033
        }

        .boleto-bradesco .cn {
            FONT: 9px Arial;
            COLOR: black
        }

        .boleto-bradesco .bc {
            font: bold 16px Arial;
            color: #000000
        }

        .boleto-bradesco .ld2 {
            font: bold 12px Arial;
            color: #000000
        }

        .boleto-bradesco .fundo-cinza {
            background-color: #E0E0E0;
        }
        </style>
    </head>

<BODY text=#000000 bgColor=#ffffff topMargin=0 rightMargin=0>
<div class="boleto-bradesco" style="margin: 0 auto; width: 666px">
    <table width=666 cellspacing=5 cellpadding=0 border=0>
        <tr>
            <td width=41>
            </TD>
        </tr>
    </table>
    <table width=666 cellspacing=5 cellpadding=0 border=0 >
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
                <?php echo ""; ?>
            </td>
            <td align=RIGHT width=150 class=ti>
                &nbsp;
            </td>
        </tr>
    </table>
    <BR>
    <table cellspacing=0 cellpadding=0 width=666 border=0>
        <tr>
            <td class=cp width=150>
                <span class="campo">
                    <IMG src="data:image/png;base64, <?php echo $logoBradescoBase64_image; ?>" width="150" height="40" border=0>
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
                    <img height=2 src="data:image/png;base64, <?php echo $Base64_2_image; ?>" width=666 border=0>
                </td>
            </tr>
        </tbody>
    </table>
    <table cellspacing=0 cellpadding=0 border=0>
        <tbody>
            <tr>
                <td class=ct valign=top width=7 height=13>
                    <img height=13 src="data:image/png;base64, <?php echo $Base64_1_image; ?>" width=1 border=0>
                </td>
                <td class=ct valign=top width=298 height=13>
                    Beneficiário
                </td>
                <td class=ct valign=top width=7 height=13>
                    <img height=13 src="data:image/png;base64, <?php echo $Base64_1_image; ?>" width=1 border=0>
                </td>
                <td class=ct valign=top width=126 height=13>
                    Agência/Código do Beneficiário
                </td>
                <td class=ct valign=top width=7 height=13>
                    <img height=13 src="data:image/png;base64, <?php echo $Base64_1_image; ?>" width=1 border=0>
                </td>
                <td class=ct valign=top width=34 height=13>
                    Espécie
                </td>
                <td class=ct valign=top width=7 height=13>
                    <img height=13 src="data:image/png;base64, <?php echo $Base64_1_image; ?>" width=1 border=0>
                </td>
                <td class=ct valign=top width=53 height=13>
                    Quantidade
                </td>
                <td class=ct valign=top width=7 height=13>
                    <img height=13 src="data:image/png;base64, <?php echo $Base64_1_image; ?>" width=1 border=0>
                </td>
                <td class=ct valign=top width=120 height=13>
                    Nosso-Número
                    <img align="right" height=13 src="data:image/png;base64, <?php echo $Base64_1_image; ?>" width=1 border=0>
                </td>
            </tr>
            <tr>
                <td class=cp valign=top width=7 height=12>
                    <img height=14 src="data:image/png;base64, <?php echo $Base64_1_image; ?>" width=1 border=0>
                </td>
                <td class=cp valign=top width=298 height=12>
                    <span class="campo">
                        <?php echo mask($parametros['cedente_cnpj'], '##.###.###/####-##'); ?>
                    </span>
                </td>
                <td class=cp valign=top width=7 height=12>
                    <img height=14 src="data:image/png;base64, <?php echo $Base64_1_image; ?>" width=1 border=0>
                </td>
                <td class=cp valign=top align=center width=126 height=12>
                    <span class="campo">
                        <?php echo $parametros['agencia_conta'];?>
                    </span>
                </td>
                <td class=cp valign=top width=7 height=12>
                    <img height=14 src="data:image/png;base64, <?php echo $Base64_1_image; ?>" width=1 border=0>
                </td>
                <td class=cp valign=top align=center width=34 height=12>
                    <span class="campo">
                        <?php echo $parametros["especie"]; ?>
                    </span>
                </td>
                <td class=cp valign=top width=7 height=12>
                    <img height=14 src="data:image/png;base64, <?php echo $Base64_1_image; ?>" width=1 border=0>
                </td>
                <td class=cp valign=top align=cente" width=53 height=12>
                    <span class="campo">
                        <?php echo "";?>
                    </span>
                </td>
                <td class=cp valign=top width=7 height=12>
                    <img height=14 src="data:image/png;base64, <?php echo $Base64_1_image; ?>" width=1 border=0>
                </td>
                <td class=cp valign=top align=right width=120 height=12>
                    <span class="campo">
                        <?php echo $parametros["nosso_numero"];?>
                    </span>             
                    <img align="right" height=14 src="data:image/png;base64, <?php echo $Base64_1_image; ?>" width=1 border=0>                  
                </td>
            </tr>
            <tr>
                <td valign=top width=7 height=1>
                    <img height=1 src="data:image/png;base64, <?php echo $Base64_2_image; ?>" width=7 border=0>
                </td>
                <td valign=top width=298 height=1>
                    <img height=1 src="data:image/png;base64, <?php echo $Base64_2_image; ?>" width=298 border=0>
                </td>
                <td valign=top width=7 height=1>
                    <img height=1 src="data:image/png;base64, <?php echo $Base64_2_image; ?>" width=7 border=0>
                </td>
                <td valign=top width=126 height=1>
                    <img height=1 src="data:image/png;base64, <?php echo $Base64_2_image; ?>" width=126 border=0>
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
                <td valign=top width=53 height=1>
                    <img height=1 src="data:image/png;base64, <?php echo $Base64_2_image; ?>" width=53 border=0>
                </td>
                <td valign=top width=7 height=1>
                    <img height=1 src="data:image/png;base64, <?php echo $Base64_2_image; ?>" width=7 border=0>
                </td>
                <td valign=top width=120 height=1>
                    <img height=1 src="data:image/png;base64, <?php echo $Base64_2_image; ?>" width=120 border=0>
                </td>
            </tr>
        </tbody>
    </table>
    <table cellspacing=0 cellpadding=0 border=0>
        <tbody>
            <tr>
                <td class=ct valign=top width=7 height=13>
                    <img height=13 src="data:image/png;base64, <?php echo $Base64_1_image; ?>" width=1 border=0>
                </td>
                <td class=ct valign=top colspan=3 height=13>
                    Número do Documento
                </td>
                <td class=ct valign=top width=7 height=13>
                    <img height=13 src="data:image/png;base64, <?php echo $Base64_1_image; ?>" width=1 border=0>
                </td>
                <td class=ct valign=top width=132 height=13>
                    CPF/CNPJ
                </td>
                <td class=ct valign=top width=7 height=13>
                    <img height=13 src="data:image/png;base64, <?php echo $Base64_1_image; ?>" width=1 border=0>
                </td>
                <td class=ct valign=top width=134 height=13>
                    Vencimento
                </td>
                <td class=ct valign=top width=7 height=13>
                    <img height=13 src="data:image/png;base64, <?php echo $Base64_1_image; ?>" width=1 border=0>
                </td>
                <td class=ct valign=top width=180 height=13>
                    Valor do Documento
                    <img align="right" height=13 src="data:image/png;base64, <?php echo $Base64_1_image; ?>" width=1 border=0>
                </td>
            </tr>
            <tr>
                <td class=cp valign=top width=7 height=12>
                    <img height=14 src="data:image/png;base64, <?php echo $Base64_1_image; ?>" width=1 border=0>
                </td>
                <td class=cp valign=top colspan=3 height=12>
                    <span class="campo">
                        <?php echo $parametros["numero_documento"];?>
                    </span>
                </td>
                <td class=cp valign=top width=7 height=12>
                    <img height=14 src="data:image/png;base64, <?php echo $Base64_1_image; ?>" width=1 border=0>
                </td>
                <td class=cp valign=top width=132 height=12>
                    <span class="campo">
                        <?php echo mask($parametros['cedente_cnpj'], '##.###.###/####-##'); ?>
                    </span>
                </td>
                <td class=cp valign=top width=7 height=12>
                    <img height=14 src="data:image/png;base64, <?php echo $Base64_1_image; ?>" width=1 border=0>
                </td>
                <td class=cp valign=top width=134 height=12>
                    <span class="campo">
                        <?php echo $parametros["data_vencimento"]->format('d/m/Y');?>
                    </span>
                </td>
                <td class=cp valign=top width=7 height=12>
                    <img height=14 src="data:image/png;base64, <?php echo $Base64_1_image; ?>" width=1 border=0>
                </td>
                <td class=cp valign=top align=right width=180 height=12>
                    <span class="campo">
                        <?php echo $parametros["valor_saldo"];?>
                    </span>
                    <img align="right" height=14 src="data:image/png;base64, <?php echo $Base64_1_image; ?>" width=1 border=0>
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
                <td valign=top width=72 height=1>
                    <img height=1 src="data:image/png;base64, <?php echo $Base64_2_image; ?>" width=72 border=0>
                </td>
                <td valign=top width=7 height=1>
                    <img height=1 src="data:image/png;base64, <?php echo $Base64_2_image; ?>" width=7 border=0>
                </td>
                <td valign=top width=132 height=1>
                    <img height=1 src="data:image/png;base64, <?php echo $Base64_2_image; ?>" width=132 border=0>
                </td>
                <td valign=top width=7 height=1>
                    <img height=1 src="data:image/png;base64, <?php echo $Base64_2_image; ?>" width=7 border=0>
                </td>
                <td valign=top width=134 height=1>
                    <img height=1 src="data:image/png;base64, <?php echo $Base64_2_image; ?>" width=134 border=0>
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
                    <img height=13 src="data:image/png;base64, <?php echo $Base64_1_image; ?>" width=1 border=0>
                </td>
                <td class=ct valign=top width=113 height=13>
                    (-) Desconto/Abatimentos
                </td>
                <td class=ct valign=top width=7 height=13>
                    <img height=13 src="data:image/png;base64, <?php echo $Base64_1_image; ?>" width=1 border=0>
                </td>
                <td class=ct valign=top width=112 height=13>
                    (-) Outras Deduções
                </td>
                <td class=ct valign=top width=7 height=13>
                    <img height=13 src="data:image/png;base64, <?php echo $Base64_1_image; ?>" width=1 border=0>
                </td>
                <td class=ct valign=top width=113 height=13>
                    (+) Juros/Multa
                </td>
                <td class=ct valign=top width=7 height=13>
                    <img height=13 src="data:image/png;base64, <?php echo $Base64_1_image; ?>" width=1 border=0>
                </td>
                <td class=ct valign=top width=113 height=13>
                    (+) Outros Acréscimos
                </td>
                <td class=ct valign=top width=7 height=13>
                    <img height=13 src="data:image/png;base64, <?php echo $Base64_1_image; ?>" width=1 border=0>
                </td>
                <td class=ct valign=top width=180 height=13>
                    (=) Valor Cobrado
                    <img align="right" height=13 src="data:image/png;base64, <?php echo $Base64_1_image; ?>" width=1 border=0>
                </td>
            </tr>
            <tr>
                <td class=cp valign=top width=7 height=12>
                    <img height=12 src="data:image/png;base64, <?php echo $Base64_1_image; ?>" width=1 border=0>
                </td>
                <td class=cp valign=top align=right width=113 height=12>
                </td>
                <td class=cp valign=top width=7 height=12>
                    <img height=12 src="data:image/png;base64, <?php echo $Base64_1_image; ?>" width=1 border=0>
                </td>
                <td class=cp valign=top align=right width=112 height=12>
                </td>
                <td class=cp valign=top width=7 height=12>
                    <img height=12 src="data:image/png;base64, <?php echo $Base64_1_image; ?>" width=1 border=0>
                </td>
                <td class=cp valign=top align=right width=113 height=12>
                </td>
                <td class=cp valign=top width=7 height=12>
                    <img height=12 src="data:image/png;base64, <?php echo $Base64_1_image; ?>" width=1 border=0>
                </td>
                <td class=cp valign=top align=right width=113 height=12>
                </td>
                <td class=cp valign=top width=7 height=12>
                    <img height=12 src="data:image/png;base64, <?php echo $Base64_1_image; ?>" width=1 border=0>
                </td>
                <td class=cp valign=top align=right width=180 height=12>
                    <img align="right" height=12 src="data:image/png;base64, <?php echo $Base64_1_image; ?>" width=1 border=0>
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
                <td valign=top width=112 height=1>
                    <img height=1 src="data:image/png;base64, <?php echo $Base64_2_image; ?>" width=112 border=0>
                </td>
                <td valign=top width=7 height=1>
                    <img height=1 src="data:image/png;base64, <?php echo $Base64_2_image; ?>" width=7 border=0>
                </td>
                <td valign=top width=113 height=1>
                    <img height=1 src="data:image/png;base64, <?php echo $Base64_2_image; ?>" width=113 border=0>
                </td>
                <td valign=top width=7 height=1>
                    <img height=1 src="data:image/png;base64, <?php echo $Base64_2_image; ?>" width=7 border=0>
                </td>
                <td valign=top width=113 height=1>
                    <img height=1 src="data:image/png;base64, <?php echo $Base64_2_image; ?>" width=113 border=0>
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
                    <img height=13 src="data:image/png;base64, <?php echo $Base64_1_image; ?>" width=1 border=0>
                </td>
                <td class=ct valign=top width=659 height=13>
                    Pagador
                    <img align="right" height=13 src="data:image/png;base64, <?php echo $Base64_1_image; ?>" width=1 border=0>
                </td>
            </tr>
            <tr>
                <td class=cp valign=top width=7 height=12>
                    <img height=14 src="data:image/png;base64, <?php echo $Base64_1_image; ?>" width=1 border=0>
                </td>
                <td class=cp valign=top width=659 height=12>
                    <span class="campo">
                        <?php echo $parametros["sacado_nome"];?>
                    </span>
                    <img align="right" height=14 src="data:image/png;base64, <?php echo $Base64_1_image; ?>" width=1 border=0>
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
                <td class=ct width=7 height=12>
                    <img height=14 src="data:image/png;base64, <?php echo $Base64_1_image; ?>" width=1 border=0>
                </td>
                <td class=ct width=564>
                    Demonstrativo
                </td>
                <td class=ct width=7 height=12>
                </td>
                <td class=ct width=88>
                    <img align="right" height=14 src="data:image/png;base64, <?php echo $Base64_1_image; ?>" width=1 border=0>
                </td>
            </tr>
            <tr>
                <td width=7>
                    <img height=45 src="data:image/png;base64, <?php echo $Base64_1_image; ?>" width=1 border=0>
                </td>
                <td class=cp width=564>
                    <span class="campo">
                        <?php echo $parametros["instrucoes"];?><br>
                        <?php echo "";?><br>
                        <?php echo ""; ?><br>
                    </span>
                </td>
                <td width=7>
                </td>
                <td width=88>
                    <img align="right" height=45 src="data:image/png;base64, <?php echo $Base64_1_image; ?>" width=1 border=0>
                </td>
            </tr>
            <tr>
                <td valign=top width=7 height=1>
                    <img height=1 src="data:image/png;base64, <?php echo $Base64_2_image; ?>" width=7 border=0>
                </td>
                <td valign=top width=564 height=1>
                    <img height=1 src="data:image/png;base64, <?php echo $Base64_2_image; ?>" width=564 border=0>
                </td>
                <td valign=top width=7 height=1>
                    <img height=1 src="data:image/png;base64, <?php echo $Base64_2_image; ?>" width=7 border=0>
                </td>
                <td valign=top width=88 height=1>
                    <img height=1 src="data:image/png;base64, <?php echo $Base64_2_image; ?>" width=88 border=0>
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
                    <br><br><br>
                </td>
                <td class=ct valign="top" align="right" width=159>
                    Autenticação mecânica
                </td>
            </tr>
        </tbody>
    </table>
    <table cellspacing=0 cellpadding=0 width=666 border=0>
        <tr>
            <td class=ct width=666>
            </td>
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
                    <IMG src="data:image/png;base64, <?php echo $logoBradescoBase64_image; ?>" width="150" height="40" border=0>
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
                    <img height=2 src="data:image/png;base64, <?php echo $Base64_2_image; ?>" width=666 border=0>
                </td>
            </tr>
        </tbody>
    </table>
    <table cellspacing=0 cellpadding=0 border=0>
        <tbody>
            <tr>
                <td class=ct valign=top width=7 height=13>
                    <img height=13 src="data:image/png;base64, <?php echo $Base64_1_image; ?>" width=1 border=0>
                </td>
                <td class=ct valign=top width=472 height=13>
                    Local de Pagamento
                </td>
                <td class="ct fundo-cinza" valign=top width=7 height=13>
                    <img height=13 src="data:image/png;base64, <?php echo $Base64_1_image; ?>" width=1 border=0>
                </td>
                <td class="ct fundo-cinza" valign=top width=180 height=13>
                    Vencimento
                    <img align="right" height=13 src="data:image/png;base64, <?php echo $Base64_1_image; ?>" width=1 border=0>
                </td>
            </tr>
            <tr>
                <td class=cp valign=top width=7 height=12>
                    <img height=14 src="data:image/png;base64, <?php echo $Base64_1_image; ?>" width=1 border=0>
                </td>
                <td class="cp2" valign=top width=472 height=12>
                    Pagável Preferencialmente na rede Bradesco ou no Bradesco expresso
                </td>
                <td class="cp fundo-cinza" valign=top width=7 height=12>
                    <img height=14 src="data:image/png;base64, <?php echo $Base64_1_image; ?>" width=1 border=0>
                </td>
                <td class="cp fundo-cinza" valign=top align=right width=180 height=12>
                    <span class="campo">
                        <?php echo $parametros["data_vencimento"]->format('d/m/Y');?>
                    </span>
                    <img align="right" height=14 src="data:image/png;base64, <?php echo $Base64_1_image; ?>" width=1 border=0>
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
                    <img height=13 src="data:image/png;base64, <?php echo $Base64_1_image; ?>" width=1 border=0>
                </td>
                <td class=ct valign=top width=472 height=13>
                    Nome do Beneficiário/CPF/CNPJ/Endereço
                </td>
                <td class=ct valign=top width=7 height=13>
                    <img height=13 src="data:image/png;base64, <?php echo $Base64_1_image; ?>" width=1 border=0>
                </td>
                <td class=ct valign=top width=180 height=13>
                    Agência/Código do Beneficiário
                    <img align="right" height=13 src="data:image/png;base64, <?php echo $Base64_1_image; ?>" width=1 border=0>
                </td>
            </tr>
            <tr>
                <td class=cp valign=top width=7 height=12>
                    <img height=42 src="data:image/png;base64, <?php echo $Base64_1_image; ?>" width=1 border=0>
                </td>
                <td class=cp valign=top width=472 height=12>
                    <span class="campo">
                        <?php echo $parametros["cedente_nome"] . ' - CNPJ: ' . mask($parametros['cedente_cnpj'], '##.###.###/####-##');?><br>
                        <?php echo $parametros["cedente_endereco"]?>
                        
                    </span>
                </td>
                <td class=cp valign=top width=7 height=12>
                    <img height=42 src="data:image/png;base64, <?php echo $Base64_1_image; ?>" width=1 border=0>
                </td>
                <td class=cp valign=top align=right width=180 height=12>
                    <span class="campo">
                        <?php echo $parametros['agencia_conta'];?>
                    </span>
                    <img align="right" height=42 src="data:image/png;base64, <?php echo $Base64_1_image; ?>" width=1 border=0>
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
                    <img height=13 src="data:image/png;base64, <?php echo $Base64_1_image; ?>" width=1 border=0>
                </td>
                <td class=ct valign=top width=140 height=13>
                    Data do Documento
                </td>
                <td class=ct valign=top width=7 height=13>
                    <img height=13 src="data:image/png;base64, <?php echo $Base64_1_image; ?>" width=1 border=0>
                </td>
                <td class=ct valign=top width=126 height=13>
                    Número do Documento
                </td>
                <td class=ct valign=top width=7 height=13>
                    <img height=13 src="data:image/png;base64, <?php echo $Base64_1_image; ?>" width=1 border=0>
                </td>
                <td class=ct valign=top width=62 height=13>
                    Espécie&nbsp;Doc.
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
                <td class=ct valign=top width=82 height=13>
                    Data Processamento
                </td>
                <td class=ct valign=top width=7 height=13>
                    <img height=13 src="data:image/png;base64, <?php echo $Base64_1_image; ?>" width=1 border=0>
                </td>
                <td class=ct valign=top width=180 height=13>
                    Nosso-Número
                    <img align="right" height=13 src="data:image/png;base64, <?php echo $Base64_1_image; ?>" width=1 border=0>
                </td>
            </tr>
            <tr>
                <td class=cp valign=top width=7 height=12>
                    <img height=14 src="data:image/png;base64, <?php echo $Base64_1_image; ?>" width=1 border=0>
                </td>
                <td class=cp valign=top width=140 height=12>
                    <div align=left>
                        <span class="campo">
                            <?php echo $parametros["data_documento"]->format('d/m/Y');?>
                        </span>
                    </div>
                </td>
                <td class=cp valign=top width=7 height=12>
                    <img height=14 src="data:image/png;base64, <?php echo $Base64_1_image; ?>" width=1 border=0>
                </td>
                <td class=cp valign=top width=126 height=12>
                    <span class="campo">
                        <?php echo $parametros["numero_documento"]?>
                    </span>
                </td>
                <td class=cp valign=top width=7 height=12>
                    <img height=14 src="data:image/png;base64, <?php echo $Base64_1_image; ?>" width=1 border=0>
                </td>
                <td class=cp valign=top width=62 height=12>
                    <div align=left>
                        <span class="campo">
                            <?php echo $parametros["especie_doc"]?>
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
                <td class=cp valign=top width=82 height=12>
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
                        <?php echo $parametros["nosso_numero"];?>
                    </span>
                    <img align="right" height=14 src="data:image/png;base64, <?php echo $Base64_1_image; ?>" width=1 border=0>
                </td>
            </tr>
            <tr>
                <td valign=top width=7 height=1>
                    <img height=1 src="data:image/png;base64, <?php echo $Base64_2_image; ?>" width=7 border=0>
                </td>
                <td valign=top width=140 height=1>
                    <img height=1 src="data:image/png;base64, <?php echo $Base64_2_image; ?>" width=140 border=0>
                </td>
                <td valign=top width=7 height=1>
                    <img height=1 src="data:image/png;base64, <?php echo $Base64_2_image; ?>" width=7 border=0>
                </td>
                <td valign=top width=126 height=1>
                    <img height=1 src="data:image/png;base64, <?php echo $Base64_2_image; ?>" width=126 border=0>
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
                <td valign=top width=82 height=1>
                    <img height=1 src="data:image/png;base64, <?php echo $Base64_2_image; ?>" width=82 border=0>
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
                    <img height=13 src="data:image/png;base64, <?php echo $Base64_1_image; ?>" width=1 border=0>
                </td>
                <td class=ct valign=top width=93 height=13>
                    Uso do Banco
                </td>
                <td class=ct valign=top width=7 height=13>
                    <img height=13 src="data:image/png;base64, <?php echo $Base64_1_image; ?>" width=1 border=0>
                </td>
                <td class=ct valign=top width=40 height=13>
                    CIP
                </td>
                <td class=ct valign=top height=13 width=7>
                    <img height=13 src="data:image/png;base64, <?php echo $Base64_1_image; ?>" width=1 border=0>
                </td>
                <td class=ct valign=top width=66 height=13>
                    Carteira
                </td>
                <td class=ct valign=top height=13 width=7>
                    <img height=13 src="data:image/png;base64, <?php echo $Base64_1_image; ?>" width=1 border=0>
                </td>
                <td class=ct valign=top width=53 height=13>
                    Moeda
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
                <td class=ct valign=top width=82 height=13>
                    Valor
                </td>
                <td class="ct fundo-cinza" valign=top width=7 height=13>
                    <img height=13 src="data:image/png;base64, <?php echo $Base64_1_image; ?>" width=1 border=0>
                </td>
                <td class="ct fundo-cinza" valign=top width=180 height=13>
                    Valor do Documento
                    <img align="right" height=13 src="data:image/png;base64, <?php echo $Base64_1_image; ?>" width=1 border=0>                  
                </td>
            </tr>
            <tr>
                <td class=cp valign=top width=7 height=12>
                    <img height=14 src="data:image/png;base64, <?php echo $Base64_1_image; ?>" width=1 border=0>
                </td>
                <td valign=top class=cp width=93 height=12 >
                </td>
                <td class=cp valign=top width=7 height=12>
                    <img height=14 src="data:image/png;base64, <?php echo $Base64_1_image; ?>" width=1 border=0>
                </td>
                <td valign=top class=cp width=40 height=12 >
                </td>
                <td class=cp valign=top width=7 height=12>
                    <img height=14 src="data:image/png;base64, <?php echo $Base64_1_image; ?>" width=1 border=0>
                </td>
                <td class=cp valign=top width=66>
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
                <td class=cp valign=top width=82>
                </td>
                <td class="cp fundo-cinza" valign=top width=7 height=12>
                    <img height=14 src="data:image/png;base64, <?php echo $Base64_1_image; ?>" width=1 border=0>
                </td>
                <td class="cp fundo-cinza" valign=top align=right width=180 height=12>
                    <?php echo $parametros["valor_saldo"];?>
                    <img align="right" height=14 src="data:image/png;base64, <?php echo $Base64_1_image; ?>" width=1 border=0>
                </td>
            </tr>
            <tr>
                <td valign=top width=7 height=1>
                    <img height=1 src="data:image/png;base64, <?php echo $Base64_2_image; ?>" width=7 border=0>
                </td>
                <td valign=top width=93 height=1>
                    <img height=1 src="data:image/png;base64, <?php echo $Base64_2_image; ?>" width=93 border=0>
                </td>
                <td valign=top width=7 height=1>
                    <img height=1 src="data:image/png;base64, <?php echo $Base64_2_image; ?>" width=7 border=0>
                </td>
                <td valign=top width=40 height=1>
                    <img height=1 src="data:image/png;base64, <?php echo $Base64_2_image; ?>" width=40 border=0>
                </td>
                <td valign=top width=7 height=1>
                    <img height=1 src="data:image/png;base64, <?php echo $Base64_2_image; ?>" width=7 border=0>
                </td>
                <td valign=top width=66 height=1>
                    <img height=1 src="data:image/png;base64, <?php echo $Base64_2_image; ?>" width=66 border=0>
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
                <td valign=top width=82 height=1>
                    <img height=1 src="data:image/png;base64, <?php echo $Base64_2_image; ?>" width=82 border=0>
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
                <td align=left width=7>
                    <table cellspacing=0 cellpadding=0 border=0 align=left>
                        <tbody>
                            <tr>
                                <td class=ct valign=top width=7 height=13>
                                    <img height=13 src="data:image/png;base64, <?php echo $Base64_1_image; ?>" width=1 border=0>
                                </td>
                            </tr>
                            <tr>
                                <td class=cp valign=top width=7 height=12>
                                    <img height=12 src="data:image/png;base64, <?php echo $Base64_1_image; ?>" width=1 border=0>
                                </td>
                            </tr>
                            <tr>
                                <td valign=top width=7 height=1>
                                    <img height=1 src="data:image/png;base64, <?php echo $Base64_2_image; ?>" width=1 border=0>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </td>
                <td valign=top width=470 rowspan=5>
                    <font class=ct>
                        Informações de responsabilidade do beneficiário
                    </font>
                    <br><br>
                    <span class=cp>
                        <FONT class=campo>
                            <?php echo $parametros["instrucoes1"];?><br>
                            <?php echo $parametros["instrucoes2"];?><br>
                            <?php echo $parametros["instrucoes3"];?><br>
                            <?php echo ""; ?>
                        </FONT>
                        <br><br>
                    </span>
                </td>
                <td align=right width=188>
                    <table cellspacing=0 cellpadding=0 border=0>
                        <tbody>
                            <tr>
                                <td class=ct valign=top width=7 height=13>
                                    <img height=13 src="data:image/png;base64, <?php echo $Base64_1_image; ?>" width=1 border=0>
                                </td>
                                <td class=ct valign=top width=180 height=13>
                                    (-) Desconto/Abatimento
                                    <img align="right" height=13 src="data:image/png;base64, <?php echo $Base64_1_image; ?>" width=1 border=0>
                                </td>
                            </tr>
                            <tr>
                                <td class=cp valign=top width=7 height=12>
                                    <img height=12 src="data:image/png;base64, <?php echo $Base64_1_image; ?>" width=1 border=0>
                                </td>
                                <td class=cp valign=top align=right width=180 height=12>
                                    <img align="right" height=12 src="data:image/png;base64, <?php echo $Base64_1_image; ?>" width=1 border=0>
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
                <td align=left width=7>
                    <table cellspacing=0 cellpadding=0 border=0 align=left>
                        <tbody>
                            <tr>
                                <td class=ct valign=top width=7 height=13>
                                    <img height=13 src="data:image/png;base64, <?php echo $Base64_1_image; ?>" width=1 border=0>
                                </td>
                            </tr>
                            <tr>
                                <td class=cp valign=top width=7 height=12>
                                    <img height=12 src="data:image/png;base64, <?php echo $Base64_1_image; ?>" width=1 border=0>
                                </td>
                            </tr>
                            <tr>
                                <td valign=top width=7 height=1>
                                    <img height=1 src="data:image/png;base64, <?php echo $Base64_2_image; ?>" width=1 border=0>
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
                                    <img align="right" height=13 src="data:image/png;base64, <?php echo $Base64_1_image; ?>" width=1 border=0>                      
                                </td>
                            </tr>
                            <tr>
                                <td class=cp valign=top width=7 height=12>
                                    <img height=12 src="data:image/png;base64, <?php echo $Base64_1_image; ?>" width=1  border=0>
                                </td>
                                <td class=cp valign=top align=right width=180 height=12>
                                    <img align="right" height=12 src="data:image/png;base64, <?php echo $Base64_1_image; ?>" width=1 border=0>
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
                <td align=left width=7>
                    <table cellspacing=0 cellpadding=0 border=0 align=left>
                        <tbody>
                            <tr>
                                <td class=ct valign=top width=7 height=13>
                                    <img height=13 src="data:image/png;base64, <?php echo $Base64_1_image; ?>" width=1 border=0>
                                </td>
                            </tr>
                            <tr>
                                <td class=cp valign=top width=7 height=12>
                                    <img height=12 src="data:image/png;base64, <?php echo $Base64_1_image; ?>" width=1  border=0>
                                </td>
                            </tr>
                            <tr>
                                <td valign=top width=7 height=1>
                                    <img height=1 src="data:image/png;base64, <?php echo $Base64_2_image; ?>" width=1 border=0>
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
                                    (+) Juros/Multa
                                    <img align="right" height=13 src="data:image/png;base64, <?php echo $Base64_1_image; ?>" width=1 border=0>
                                </td>
                            </tr>
                            <tr>
                                <td class=cp valign=top width=7 height=12>
                                    <img height=12 src="data:image/png;base64, <?php echo $Base64_1_image; ?>" width=1 border=0>
                                </td>
                                <td class=cp valign=top align=right width=180 height=12>
                                    <img align="right" height=12 src="data:image/png;base64, <?php echo $Base64_1_image; ?>" width=1 border=0>
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
                <td align=left width=7>
                    <table cellspacing=0 cellpadding=0 border=0 align=left>
                        <tbody>
                            <tr>
                                <td class=ct valign=top width=7 height=13>
                                    <img height=13 src="data:image/png;base64, <?php echo $Base64_1_image; ?>" width=1 border=0>
                                </td>
                            </tr>
                            <tr>
                                <td class=cp valign=top width=7 height=12>
                                    <img height=12 src="data:image/png;base64, <?php echo $Base64_1_image; ?>" width=1  border=0>
                                </td>
                            </tr>
                            <tr>
                                <td valign=top width=7 height=1>
                                    <img height=1 src="data:image/png;base64, <?php echo $Base64_2_image; ?>" width=1 border=0>
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
                                    (+) Outros Acréscimos
                                    <img align="right" height=13 src="data:image/png;base64, <?php echo $Base64_1_image; ?>" width=1 border=0>
                                </td>
                            </tr>
                            <tr>
                                <td class=cp valign=top width=7 height=12>
                                    <img height=12 src="data:image/png;base64, <?php echo $Base64_1_image; ?>" width=1 border=0>
                                </td>
                                <td class=cp valign=top align=right width=180 height=12>
                                    <img align="right" height=12 src="data:image/png;base64, <?php echo $Base64_1_image; ?>" width=1 border=0>
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
                <td align=left width=7>
                    <table cellspacing=0 cellpadding=0 border=0 align=left>
                        <tbody>
                            <tr>
                                <td class=ct valign=top width=7 height=13>
                                    <img height=13 src="data:image/png;base64, <?php echo $Base64_1_image; ?>" width=1 border=0>
                                </td>
                            </tr>
                            <tr>
                                <td class=cp valign=top width=7 height=12>
                                    <img height=12 src="data:image/png;base64, <?php echo $Base64_1_image; ?>" width=1 border=0>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </td>
                <td align=right width=188>
                    <table cellspacing=0 cellpadding=0 border=0>
                        <tbody>
                            <tr>
                                <td class="ct fundo-cinza" valign=top width=7 height=13>
                                    <img height=13 src="data:image/png;base64, <?php echo $Base64_1_image; ?>" width=1 border=0>
                                </td>
                                <td class="ct fundo-cinza" valign=top width=180 height=13>
                                    (=) Valor Cobrado
                                    <img align="right" height=13 src="data:image/png;base64, <?php echo $Base64_1_image; ?>" width=1 border=0>
                                </td>
                            </tr>
                            <tr>
                                <td class="cp fundo-cinza" valign=top width=7 height=12>
                                    <img height=12 src="data:image/png;base64, <?php echo $Base64_1_image; ?>" width=1 border=0>
                                </td>
                                <td class="cp fundo-cinza" valign=top align=right width=180 height=12>
                                    <img align="right" height=12 src="data:image/png;base64, <?php echo $Base64_1_image; ?>" width=1 border=0>
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
                    <img height=13 src="data:image/png;base64, <?php echo $Base64_1_image; ?>" width=1 border=0>
                </td>
                <td class=ct valign=top width=659 height=13>
                    Nome do Pagador/CPF/CNPJ/Endereço
                    <img height=13 src="data:image/png;base64, <?php echo $Base64_1_image; ?>" width=1 align="right" border=0>
                </td>
            </tr>
            <tr>
                <td class=cp valign=top width=7 height=12>
                    <img height=14 src="data:image/png;base64, <?php echo $Base64_1_image; ?>" width=1 border=0>
                </td>
                <td class=cp valign=top width=659 height=12>
                    <span class="campo">
                        <?php
                        echo $parametros["sacado_nome"] . ' - ';
                        if (strlen($parametros["sacado_cpf_cnpj"]) < 12) {
                            echo "CPF/CNPJ - " . mask($parametros['sacado_cpf_cnpj'], '###.###.###-##');
                        } else {
                            echo "CPF/CNPJ - " . mask($parametros['sacado_cpf_cnpj'], '##.###.###/####-##');
                        }?>
                    </span>
                    <img height=14 src="data:image/png;base64, <?php echo $Base64_1_image; ?>" width=1 align="right" border=0>
                </td>
            </tr>
        </tbody>
    </table>
    <table cellspacing=0 cellpadding=0 border=0>
        <tbody>
            <tr>
                <td class=cp valign=top width=7 height=12>
                    <img height=14 src="data:image/png;base64, <?php echo $Base64_1_image; ?>" width=1 border=0>
                </td>
                <td class=cp valign=top width=659 height=12>
                    <span class="campo">
                        <?php echo "{$parametros["sacado_endereco"]}, {$parametros["sacado_endereco_numero"]},  {$parametros["sacado_bairro"]}";?>
                    </span>
                    <img height=14 src="data:image/png;base64, <?php echo $Base64_1_image; ?>" width=1 align="right" border=0>
                </td>
            </tr>
        </tbody>
    </table>
    <table cellspacing=0 cellpadding=0 border=0>
        <tbody>
            <tr>
                <td class=ct valign=top width=7 height=13>
                    <img height=14 src="data:image/png;base64, <?php echo $Base64_1_image; ?>" width=1 border=0>
                </td>
                <td class=cp valign=top width=659 height=13>
                    <span class="campo">
                        <?php echo "{$parametros["sacado_cidade"]}/{$parametros["sacado_estado"]} - CEP: " . mask($parametros["sacado_cep"], '##.###-###');?>
                    </span>
                    <img height=14 src="data:image/png;base64, <?php echo $Base64_1_image; ?>" width=1 align="right" border=0>
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
    <TABLE cellSpacing=0 cellPadding=0 border=0 width=666>
        <TBODY>
            <TR>
                <TD class=ct width=7 height=12>
                </TD>
                <TD class=ct width=409>
                    Sacador/Avalista
                </TD>
                <TD class=ct width=250>
                    <div align=right>
                        Autenticação mecânica - <b class=cp>Ficha de Compensação</b>
                    </div>
                </TD>
            </TR>
            <TR>
                <TD class=ct colspan=3>
                </TD>
            </tr>
        </tbody>
    </table>
    <TABLE cellSpacing=0 cellPadding=0 width=666 border=0>
        <TBODY>
            <TR>
                <TD vAlign=bottom align=left height=50>
                    <?php echo fbarcodeBradesco($parametros['codigo_barras'], $host); ?> 
                </TD>
            </tr>
        </tbody>
    </table>
    <TABLE cellSpacing=0 cellPadding=0 width=666 border=0>
        <TR>
            <TD class=ct width=666>
            </TD>
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
