<?php include 'get_base64_image.php'; ?>
<!DOCTYPE HTML PUBLIC '-//W3C//DTD HTML 4.0 Transitional//EN'>
<html>
<head>
    <title>2ª Via do boleto - inFlux</title>
    <META http-equiv=Content-Type content=text/html charset=UTF-8>
    <meta name="Generator" content="Projeto BoletoPHP - www.boletophp.com.br - Licença GPL" />
    <style type=text/css>
        .boleto-sicredi .cp {
            font: bold 10px Arial;
            color: black
        }

        .boleto-sicredi .ti {
            font: 9px Arial, Helvetica, sans-serif
        }

        .boleto-sicredi .ld {
            font: bold 12px Arial;
            color: #000000
        }

        .boleto-sicredi .ct {
            FONT: 9px "Arial Narrow";
            COLOR: #000033
        }

        .boleto-sicredi .cn {
            FONT: 9px Arial;
            COLOR: black
        }

        .boleto-sicredi .bc {
            font: bold 14px Arial;
            color: #000000
        }

        .boleto-sicredi .ld2 {
            font: bold 12px Arial;
            color: #000000
        }

        .boleto-sicredi .instrucoes {
            font-style: Arial;
            font-size: 10px;
            color: black;
            text-transform: uppercase;
        }
    </style>
</head>
<body text=#000000 bgColor=#ffffff topMargin=0 rightMargin=0>
<div class="boleto-sicredi" style="margin: 0 auto; width: 666px;">
    <table width=666 cellspacing=0 cellpadding=0 border=0>
        <tr>
            <td valign=top class=cp>
                <div ALIGN="CENTER">Instruções de Impressão</div>
            </td>
        </tr>
        <tr>
            <td valign=top class=cp>
                <div ALIGN="left">
                    <p>
                        <li>Imprima em impressora jato de tinta (ink jet) ou laser em qualidade normal ou alta (Não use modo econômico).<br>
                        <li>Utilize folha A4 (210 x 297 mm) ou Carta (216 x 279 mm) e margens mínimas à esquerda e à direita do formulário.<br>
                        <li>Corte na linha indicada. Não rasure, risque, fure ou dobre a região onde se encontra o código de barras.<br>
                        <li>Caso não apareça o código de barras no final, clique em F5 para atualizar esta tela.
                        <li>Caso tenha problemas ao imprimir, copie a seqüencia numérica abaixo e pague no caixa eletrônico ou no internet banking:<br><br>
                            <span class="ld2">
                                &nbsp;&nbsp;&nbsp;&nbsp;Linha Digitável: &nbsp;<?php echo $parametros["linha_digitavel"] ?><br>
                                &nbsp;&nbsp;&nbsp;&nbsp;Valor: &nbsp;&nbsp;R$ <?php echo $parametros["valor_saldo"] ?><br>
                            </span>
                </div>
            </td>
        </tr>
    </table>
    <br>
    <table cellspacing=0 cellpadding=0 width=666 border=0>
        <tbody>
            <tr>
                <td class=ct width=666>
                    <img height=1 src="data:image/png;base64, <?php echo $Base64_6_image; ?>" width=665 border=0>
                </td>
            </tr>
            <tr>
                <td class=ct width=666>
                    <div align=right><b class=cp>Recibo do Sacado</b></div>
                </td>
            </tr>
        </tbody>
    </table>
    <table width=666 cellspacing=5 cellpadding=0 border=0>
        <tr>
            <td width=41></td>
        </tr>
    </table>
    <table width=666 cellspacing=5 cellpadding=0 border=0 align=Default>
        <tr>
            <td width="41"><img src="data:image/png;base64, <?php echo $logoBase64_image; ?>" height="100"></td>
            <?php $cedente_cnpj_string = '';
            if (isset($parametros["cedente_cnpj"]) === true) {
                $cedente_cnpj_string = "<br>" . $parametros["cedente_cnpj"];
            }//logoSicredBase64_image
            ?>
            <td class=ti width=455>2ª Via do boleto - inFlux <?php echo $cedente_cnpj_string ?><br>
                <?php echo $parametros["cedente_endereco"]; ?><br>
                <br>
            </td>
            <td align=RIGHT width=150 class=ti>&nbsp;</td>
        </tr>
    </table>
    <br>
    <table cellspacing=0 cellpadding=0 width=666 border=0>
        <tr>
            <td class=cp width=150>
                <span class="campo"><img SRC="data:image/png;base64, <?php echo $logoSicredBase64_image; ?>" width="150" height="40" border=0></span>
            </td>
            <td width=3 valign=bottom><img height=22 src="data:image/png;base64, <?php echo $Base64_3_image; ?>" width=2 border=0></td>
            <td class=cpt width=58 valign=bottom>
                <div align=center>
                    <font class=bc><?php echo $parametros["codigo_banco_com_dv"] ?></font>
                </div>
            </td>
            <td width=3 valign=bottom><img height=22 src="data:image/png;base64, <?php echo $Base64_3_image; ?>" width=2 border=0></td>
            <td class=ld align=right width=453 valign=bottom><span class=ld>
                    <span class="campotitulo">
                        <?php echo $parametros["linha_digitavel"] ?>
                    </span>
            </td>
        </tr>
        <tbody>
            <tr>
                <td colspan=5><img height=2 src="data:image/png;base64, <?php echo $Base64_2_image; ?>" width=666 border=0></td>
            </tr>
        </tbody>
    </table>
    <table cellspacing=0 cellpadding=0 border=0>
        <tbody>
            <tr>
                <td class=ct valign=top width=7 height=13><img height=13 src="data:image/png;base64, <?php echo $Base64_1_image;?>" width=1 border=0></td>
                <td class=ct valign=top width=238 height=13>Cedente</td>
                <td class=ct valign=top width=7 height=13><img height=13 src="data:image/png;base64, <?php echo $Base64_1_image; ?>" width=1 border=0></td>
                <td class=ct valign=top width=146 height=13>Agência/Código do Cedente</td>
                <td class=ct valign=top width=7 height=13><img height=13 src="data:image/png;base64, <?php echo $Base64_1_image; ?>" width=1 border=0></td>
                <td class=ct valign=top width=54 height=13>Espécie</td>
                <td class=ct valign=top width=7 height=13><img height=13 src="data:image/png;base64, <?php echo $Base64_1_image; ?>" width=1 border=0></td>
                <td class=ct valign=top width=73 height=13>Quantidade</td>
                <td class=ct valign=top width=7 height=13><img height=13 src="data:image/png;base64, <?php echo $Base64_1_image; ?>" width=1 border=0></td>
                <td class=ct valign=top width=120 height=13>Nosso número</td>
            </tr>
            <tr>
                <td class=cp valign=top width=7 height=12><img height=12 src="data:image/png;base64, <?php echo $Base64_1_image; ?>" width=1 border=0></td>
                <td class=cp valign=top width=238 height=12><?php echo $parametros["cedente_nome"]; ?></td>
                <td class=cp valign=top width=7 height=12><img height=12 src="data:image/png;base64, <?php echo $Base64_1_image; ?>" width=1 border=0></td>
                <td class=cp valign=top width=146 height=12><?php echo $parametros["agencia_codigo"] ?></td>
                <td class=cp valign=top width=7 height=12><img height=12 src="data:image/png;base64, <?php echo $Base64_1_image; ?>" width=1 border=0></td>
                <td class=cp valign=top width=54 height=12><?php echo $parametros["especie"] ?></td>
                <td class=cp valign=top width=7 height=12><img height=12 src="data:image/png;base64, <?php echo $Base64_1_image; ?>" width=1 border=0></td>
                <td class=cp valign=top width=73 height=12><?php echo $parametros["quantidade"] ?></td>
                <td class=cp valign=top width=7 height=12><img height=12 src="data:image/png;base64, <?php echo $Base64_1_image; ?>" width=1 border=0></td>
                <td class=cp valign=top align=right width=120 height=12><?php echo $parametros["nosso_numero"] ?></td>
            </tr>
            <tr>
                <td valign=top colspan=10 height=1><img height=1 src="data:image/png;base64, <?php echo $Base64_2_image; ?>" width=666 border=0></td>
            </tr>
        </tbody>
    </table>
    <table cellspacing=0 cellpadding=0 border=0>
        <tbody>
            <tr>
                <td class=ct valign=top width=7 height=13><img height=13 src="data:image/png;base64, <?php echo $Base64_1_image; ?>" width=1 border=0></td>
                <td class=ct valign=top colspan=3 height=13>Número do documento</td>
                <td class=ct valign=top width=7 height=13><img height=13 src="data:image/png;base64, <?php echo $Base64_1_image; ?>" width=1 border=0></td>
                <td class=ct valign=top width=132 height=13>CPF/CNPJ</td>
                <td class=ct valign=top width=7 height=13><img height=13 src="data:image/png;base64, <?php echo $Base64_1_image; ?>" width=1 border=0></td>
                <td class=ct valign=top width=134 height=13>Vencimento</td>
                <td class=ct valign=top width=7 height=13><img height=13 src="data:image/png;base64, <?php echo $Base64_1_image; ?>" width=1 border=0></td>
                <td class=ct valign=top width=180 height=13>Valor documento</td>
            </tr>
            <tr>
                <td class=cp valign=top width=7 height=12><img height=12 src="data:image/png;base64, <?php echo $Base64_1_image; ?>" width=1 border=0></td>
                <td class=cp valign=top colspan=3 height=12>
                    <span class="campo">
                        <?php echo $parametros["numero_documento"] ?>
                    </span>
                </td>
                <td class=cp valign=top width=7 height=12><img height=12 src="data:image/png;base64, <?php echo $Base64_1_image; ?>" width=1 border=0></td>
                <td class=cp valign=top width=132 height=12>
                    <span class="campo">
                        <?php echo mask($parametros['cedente_cnpj'], '##.###.###/####-##') ?>
                    </span>
                </td>
                <td class=cp valign=top width=7 height=12><img height=12 src="data:image/png;base64, <?php echo $Base64_1_image; ?>" width=1 border=0></td>
                <td class=cp valign=top width=134 height=12>
                    <span class="campo">
                        <?php echo $parametros["data_vencimento"]->format('d/m/Y') ?>
                    </span>
                </td>
                <td class=cp valign=top width=7 height=12><img height=12 src="data:image/png;base64, <?php echo $Base64_1_image; ?>" width=1 border=0></td>
                <td class=cp valign=top align=right width=180 height=12>
                    <span class="campo">
                        <?php echo $parametros["valor_saldo"] ?>
                    </span>
                </td>
            </tr>
            <tr>
                <td valign=top colspan=10 height=1><img height=1 src="data:image/png;base64, <?php echo $Base64_2_image; ?>" width=666 border=0></td>
            </tr>
        </tbody>
    </table>
    <table cellspacing=0 cellpadding=0 border=0>
        <tbody>
            <tr>
                <td class=ct valign=top width=7 height=13><img height=13 src="data:image/png;base64, <?php echo $Base64_1_image; ?>" width=1 border=0></td>
                <td class=ct valign=top width=143 height=13>(-) Desconto / Abatimentos</td>
                <td class=ct valign=top width=7 height=13><img height=13 src="data:image/png;base64, <?php echo $Base64_1_image; ?>" width=1 border=0></td>
                <td class=ct valign=top width=107 height=13>(-) Outras deduções</td>
                <td class=ct valign=top width=7 height=13><img height=13 src="data:image/png;base64, <?php echo $Base64_1_image; ?>" width=1 border=0></td>
                <td class=ct valign=top width=88 height=13>(+) Mora / Multa</td>
                <td class=ct valign=top width=7 height=13><img height=13 src="data:image/png;base64, <?php echo $Base64_1_image; ?>" width=1 border=0></td>
                <td class=ct valign=top width=113 height=13>(+) Outros acréscimos</td>
                <td class=ct valign=top width=7 height=13><img height=13 src="data:image/png;base64, <?php echo $Base64_1_image; ?>" width=1 border=0></td>
                <td class=ct valign=top width=180 height=13>(=) Valor cobrado</td>
            </tr>
            <tr>
                <td class=cp valign=top width=7 height=12><img height=12 src="data:image/png;base64, <?php echo $Base64_1_image; ?>" width=1 border=0></td>
                <td class=cp valign=top align=right width=143 height=12></td>
                <td class=cp valign=top width=7 height=12><img height=12 src="data:image/png;base64, <?php echo $Base64_1_image; ?>" width=1 border=0></td>
                <td class=cp valign=top align=right width=107 height=12></td>
                <td class=cp valign=top width=7 height=12><img height=12 src="data:image/png;base64, <?php echo $Base64_1_image; ?>" width=1 border=0></td>
                <td class=cp valign=top align=right width=88 height=12></td>
                <td class=cp valign=top width=7 height=12><img height=12 src="data:image/png;base64, <?php echo $Base64_1_image; ?>" width=1 border=0></td>
                <td class=cp valign=top align=right width=113 height=12></td>
                <td class=cp valign=top width=7 height=12><img height=12 src="data:image/png;base64, <?php echo $Base64_1_image; ?>" width=1 border=0></td>
                <td class=cp valign=top align=right width=180 height=12></td>
            </tr>
            <tr>
                <td valign=top colspan=10 height=1><img height=1 src="data:image/png;base64, <?php echo $Base64_2_image; ?>" width=666 border=0></td>
            </tr>
        </tbody>
    </table>
    <table cellspacing=0 cellpadding=0 border=0>
        <tbody>
            <tr>
                <td class=ct valign=top width=7 height=13><img height=13 src="data:image/png;base64, <?php echo $Base64_1_image; ?>" width=1 border=0></td>
                <td class=ct valign=top width=659 height=13>Sacado</td>
            </tr>
            <tr>
                <td class=cp valign=top width=7 height=12><img height=12 src="data:image/png;base64, <?php echo $Base64_1_image; ?>" width=1 border=0></td>
                <td class=cp valign=top width=659 height=12>
                    <span class="campo">
                        <?php echo $parametros["sacado_nome"]; ?>
                    </span>
                </td>
            </tr>
            <tr>
                <td valign=top width=7 height=1><img height=1 src="data:image/png;base64, <?php echo $Base64_2_image; ?>" width=7 border=0></td>
                <td valign=top width=659 height=1><img height=1 src="data:image/png;base64, <?php echo $Base64_2_image; ?>" width=659 border=0></td>
            </tr>
        </tbody>
    </table>
    <table cellspacing=0 cellpadding=0 border=0>
        <tbody>
            <tr>
                <td class=ct width=7 height=12></td>
                <td class=ct width=514>Demonstrativo</td>
                <td class=ct  colspan="2" height=12><div align=right>Autenticação mecânica</div></td>
            </tr>
            <tr>
                <td width=7></td>
                <td class=cp width=514>
                    <span class="campo">
                        <?php echo $parametros["demonstrativo1"] ?><br>
                        <?php echo $parametros["demonstrativo2"] ?><br>
                        <?php echo $parametros["demonstrativo3"] ?><br>
                    </span>
                </td>
                <td width=7></td>
                <td width=138></td>
            </tr>
        </tbody>
    </table>
    <table cellspacing=0 cellpadding=0 width=666 border=0>
        <tbody>
            <tr>
                <td width=7></td>
                <td width=500 class=cp>
                    <br><br><br>
                </td>
                <td width=159></td>
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
                    <div align=right>Corte na linha pontilhada</div>
                </td>
            </tr>
            <tr>
                <td class=ct width=666><img height=1 src="data:image/png;base64, <?php echo $Base64_6_image; ?>" width=665 border=0></td>
            </tr>
        </tbody>
    </table><br>
    <table cellspacing=0 cellpadding=0 width=666 border=0>
        <tr>
            <td class=cp width=150>
                <span class="campo"><img SRC="data:image/png;base64, <?php echo $logoSicredBase64_image; ?>" width="150" height="40" border=0></span>
            </td>
            <td width=3 valign=bottom><img height=22 src="data:image/png;base64, <?php echo $Base64_3_image; ?>" width=2 border=0></td>
            <td class=cpt width=58 valign=bottom>
                <div align=center>
                    <font class=bc><?php echo $parametros["codigo_banco_com_dv"] ?></font>
                </div>
            </td>
            <td width=3 valign=bottom><img height=22 src="data:image/png;base64, <?php echo $Base64_3_image; ?>" width=2 border=0></td>
            <td class=ld align=right width=453 valign=bottom><span class=ld>
                <span class="campotitulo">
                    <?php echo $parametros["linha_digitavel"] ?>
                </span></span>
            </td>
        </tr>
        <tbody>
            <tr>
                <td colspan=5><img height=2 src="data:image/png;base64, <?php echo $Base64_2_image; ?>" width=666 border=0></td>
            </tr>
        </tbody>
    </table>
    <table cellspacing=0 cellpadding=0 border=0>
        <tbody>
            <tr>
                <td class=ct valign=top width=7 height=13><img height=13 src="data:image/png;base64, <?php echo $Base64_1_image; ?>" width=1 border=0></td>
                <td class=ct valign=top width=472 height=13>Local de pagamento</td>
                <td class=ct valign=top width=7 height=13><img height=13 src="data:image/png;base64, <?php echo $Base64_1_image; ?>" width=1 border=0></td>
                <td class=ct valign=top width=180 height=13>Vencimento</td>
            </tr>
            <tr>
                <td class=cp valign=top width=7 height=12><img height=12 src="data:image/png;base64, <?php echo $Base64_1_image; ?>" width=1 border=0></td>
                <td class=cp valign=top width=472 height=12>Pagável em qualquer Banco até o vencimento</td>
                <td class=cp valign=top width=7 height=12><img height=12 src="data:image/png;base64, <?php echo $Base64_1_image; ?>" width=1 border=0></td>
                <td class=cp valign=top align=right width=180 height=12>
                    <span class="campo">
                        <?php echo $parametros["data_vencimento"]->format('d/m/Y') ?>
                    </span>
                </td>
            </tr>
            <tr>
                <td valign=top width=7 height=1><img height=1 src="data:image/png;base64, <?php echo $Base64_2_image; ?>" width=7 border=0></td>
                <td valign=top width=472 height=1><img height=1 src="data:image/png;base64, <?php echo $Base64_2_image; ?>" width=472 border=0></td>
                <td valign=top width=7 height=1><img height=1 src="data:image/png;base64, <?php echo $Base64_2_image; ?>" width=7 border=0></td>
                <td valign=top width=180 height=1><img height=1 src="data:image/png;base64, <?php echo $Base64_2_image; ?>" width=180 border=0></td>
            </tr>
        </tbody>
    </table>
    <table cellspacing=0 cellpadding=0 border=0>
        <tbody>
            <tr>
                <td class=ct valign=top width=7 height=13><img height=13 src="data:image/png;base64, <?php echo $Base64_1_image; ?>" width=1 border=0></td>
                <td class=ct valign=top width=472 height=13>Cedente</td>
                <td class=ct valign=top width=7 height=13><img height=13 src="data:image/png;base64, <?php echo $Base64_1_image; ?>" width=1 border=0></td>
                <td class=ct valign=top width=180 height=13>Agência/Código cedente</td>
            </tr>
            <tr>
                <td class=cp valign=top width=7 height=12><img height=12 src="data:image/png;base64, <?php echo $Base64_1_image; ?>" width=1 border=0></td>
                <td class=cp valign=top width=472 height=12>
                    <span class="campo">
                        <?php echo $parametros["cedente_nome"] ?>
                    </span>
                </td>
                <td class=cp valign=top width=7 height=12><img height=12 src="data:image/png;base64, <?php echo $Base64_1_image; ?>" width=1 border=0></td>
                <td class=cp valign=top align=right width=180 height=12>
                    <span class="campo">
                        <?php echo $parametros["agencia_codigo"] ?>
                    </span>
                </td>
            </tr>
            <tr>
                <td valign=top width=7 height=1><img height=1 src="data:image/png;base64, <?php echo $Base64_2_image; ?>" width=7 border=0></td>
                <td valign=top width=472 height=1><img height=1 src="data:image/png;base64, <?php echo $Base64_2_image; ?>" width=472 border=0></td>
                <td valign=top width=7 height=1><img height=1 src="data:image/png;base64, <?php echo $Base64_2_image; ?>" width=7 border=0></td>
                <td valign=top width=180 height=1><img height=1 src="data:image/png;base64, <?php echo $Base64_2_image; ?>" width=180 border=0></td>
            </tr>
        </tbody>
    </table>
    <table cellspacing=0 cellpadding=0 border=0>
        <tbody>
            <tr>
                <td class=ct valign=top width=7 height=13>
                    <img height=13 src="data:image/png;base64, <?php echo $Base64_1_image; ?>" width=1 border=0>
                </td>
                <td class=ct valign=top width=113 height=13>Data do documento</td>
                <td class=ct valign=top width=7 height=13> <img height=13 src="data:image/png;base64, <?php echo $Base64_1_image; ?>" width=1 border=0></td>
                <td class=ct valign=top width=109 height=13>Nº documento</td>
                <td class=ct valign=top width=7 height=13> <img height=13 src="data:image/png;base64, <?php echo $Base64_1_image; ?>" width=1 border=0></td>
                <td class=ct valign=top width=79 height=13>Espécie doc.</td>
                <td class=ct valign=top width=7 height=13> <img height=13 src="data:image/png;base64, <?php echo $Base64_1_image; ?>" width=1 border=0></td>
                <td class=ct valign=top width=34 height=13>Aceite</td>
                <td class=ct valign=top width=7 height=13>
                    <img height=13 src="data:image/png;base64, <?php echo $Base64_1_image; ?>" width=1 border=0>
                </td>
                <td class=ct valign=top width=109 height=13>Data processamento</td>
                <td class=ct valign=top width=7 height=13> <img height=13 src="data:image/png;base64, <?php echo $Base64_1_image; ?>" width=1 border=0></td>
                <td class=ct valign=top width=180 height=13>Nosso número</td>
            </tr>
            <tr>
                <td class=cp valign=top width=7 height=12><img height=12 src="data:image/png;base64, <?php echo $Base64_1_image; ?>" width=1 border=0></td>
                <td class=cp valign=top width=113 height=12>
                    <div align=left>
                        <span class="campo">
                            <?php echo $parametros["data_documento"]->format('d/m/Y') ?>
                        </span>
                    </div>
                </td>
                <td class=cp valign=top width=7 height=12><img height=12 src="data:image/png;base64, <?php echo $Base64_1_image; ?>" width=1 border=0></td>
                <td class=cp valign=top width=109 height=12>
                    <span class="campo">
                        <?php echo $parametros["numero_documento"] ?>
                    </span>
                </td>
                <td class=cp valign=top width=7 height=12><img height=12 src="data:image/png;base64, <?php echo $Base64_1_image; ?>" width=1 border=0></td>
                <td class=cp valign=top width=79 height=12>
                    <div align=left><span class="campo">
                            <?php echo $parametros["especie_doc2"] ?>
                        </span>
                    </div>
                </td>
                <td class=cp valign=top width=7 height=12><img height=12 src="data:image/png;base64, <?php echo $Base64_1_image; ?>" width=1 border=0></td>
                <td class=cp valign=top width=34 height=12>
                    <div align=left><span class="campo">
                            <?php echo $parametros["aceite"] ?>
                        </span>
                    </div>
                </td>
                <td class=cp valign=top width=7 height=12><img height=12 src="data:image/png;base64, <?php echo $Base64_1_image; ?>" width=1 border=0></td>
                <td class=cp valign=top width=109 height=12>
                    <div align=left>
                        <span class="campo">
                            <?php echo $parametros["data_processamento"] ?>
                        </span>
                    </div>
                </td>
                <td class=cp valign=top width=7 height=12><img height=12 src="data:image/png;base64, <?php echo $Base64_1_image; ?>" width=1 border=0></td>
                <td class=cp valign=top align=right width=180 height=12>
                    <span class="campo">
                        <?php echo $parametros["nosso_numero"] ?>
                    </span>
                </td>
            </tr>
            <tr>
                <td valign=top width=7 height=1><img height=1 src="data:image/png;base64, <?php echo $Base64_2_image; ?>" width=7 border=0></td>
                <td valign=top width=113 height=1><img height=1 src="data:image/png;base64, <?php echo $Base64_2_image; ?>" width=113 border=0></td>
                <td valign=top width=7 height=1>
                    <img height=1 src="data:image/png;base64, <?php echo $Base64_2_image; ?>" width=7 border=0>
                </td>
                <td valign=top width=109 height=1><img height=1 src="data:image/png;base64, <?php echo $Base64_2_image; ?>" width=109 border=0></td>
                <td valign=top width=7 height=1>
                    <img height=1 src="data:image/png;base64, <?php echo $Base64_2_image; ?>" width=7 border=0>
                </td>
                <td valign=top width=79 height=1><img height=1 src="data:image/png;base64, <?php echo $Base64_2_image; ?>" width=79 border=0></td>
                <td valign=top width=7 height=1>
                    <img height=1 src="data:image/png;base64, <?php echo $Base64_2_image; ?>" width=7 border=0>
                </td>
                <td valign=top width=34 height=1><img height=1 src="data:image/png;base64, <?php echo $Base64_2_image; ?>" width=34 border=0></td>
                <td valign=top width=7 height=1>
                    <img height=1 src="data:image/png;base64, <?php echo $Base64_2_image; ?>" width=7 border=0>
                </td>
                <td valign=top width=109 height=1><img height=1 src="data:image/png;base64, <?php echo $Base64_2_image; ?>" width=109 border=0></td>
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
                <td class=ct valign=top width=7 height=13> <img height=13 src="data:image/png;base64, <?php echo $Base64_1_image; ?>" width=1 border=0></td>
                <td class=ct valign=top colspan="3" height=13>Uso do banco</td>
                <td class=ct valign=top height=13 width=7> <img height=13 src="data:image/png;base64, <?php echo $Base64_1_image; ?>" width=1 border=0></td>
                <td class=ct valign=top width=109 height=13>Carteira</td>
                <td class=ct valign=top height=13 width=7>
                    <img height=13 src="data:image/png;base64, <?php echo $Base64_1_image; ?>" width=1 border=0>
                </td>
                <td class=ct valign=top width=79 height=13>Espécie</td>
                <td class=ct valign=top height=13 width=7>
                    <img height=13 src="data:image/png;base64, <?php echo $Base64_1_image; ?>" width=1 border=0>
                </td>
                <td class=ct valign=top width=150 height=13>Quantidade</td>
                <td class=ct valign=top height=13 width=7>
                    <img height=13 src="data:image/png;base64, <?php echo $Base64_1_image; ?>" width=1 border=0>
                </td>
                <td class=ct valign=top width=180 height=13>(=) Valor documento</td>
            </tr>
            <tr>
                <td class=cp valign=top width=7 height=12><img height=12 src="data:image/png;base64, <?php echo $Base64_1_image; ?>" width=1 border=0></td>
                <td valign=top class=cp height=12 colspan="3">
                    <div align=left>
                    </div>
                </td>
                <td class=cp valign=top width=7 height=12><img height=12 src="data:image/png;base64, <?php echo $Base64_1_image; ?>" width=1 border=0></td>
                <td class=cp valign=top width=109>
                    <div align=left> <span class="campo">
                            <?php echo $parametros["carteira"] ?>
                        </span></div>
                </td>
                <td class=cp valign=top width=7 height=12><img height=12 src="data:image/png;base64, <?php echo $Base64_1_image; ?>" width=1 border=0></td>
                <td class=cp valign=top width=79>
                    <div align=left><span class="campo">
                            <?php echo $parametros["especie"] ?>
                        </span>
                    </div>
                </td>
                <td class=cp valign=top width=7 height=12><img height=12 src="data:image/png;base64, <?php echo $Base64_1_image; ?>" width=1 border=0></td>
                <td class=cp valign=top width=150><span class="campo">
                        <?php echo $parametros["quantidade"] ?>
                    </span>
                </td>
                <td class=cp valign=top width=7 height=12><img height=12 src="data:image/png;base64, <?php echo $Base64_1_image; ?>" width=1 border=0></td>
                <td class=cp valign=top align=right width=180 height=12>
                    <span class="campo">
                        <?php echo $parametros["valor_saldo"] ?>
                    </span>
                </td>
            </tr>
            <tr>
                <td valign=top width=7 height=1> <img height=1 src="data:image/png;base64, <?php echo $Base64_2_image; ?>" width=7 border=0></td>
                <td valign=top width=7 height=1><img height=1 src="data:image/png;base64, <?php echo $Base64_2_image; ?>" width=75 border=0></td>
                <td valign=top width=7 height=1><img height=1 src="data:image/png;base64, <?php echo $Base64_2_image; ?>" width=7 border=0></td>
                <td valign=top width=31 height=1><img height=1 src="data:image/png;base64, <?php echo $Base64_2_image; ?>" width=31 border=0></td>
                <td valign=top width=7 height=1>
                    <img height=1 src="data:image/png;base64, <?php echo $Base64_2_image; ?>" width=7 border=0>
                </td>
                <td valign=top width=109 height=1><img height=1 src="data:image/png;base64, <?php echo $Base64_2_image; ?>" width=109 border=0></td>
                <td valign=top width=7 height=1>
                    <img height=1 src="data:image/png;base64, <?php echo $Base64_2_image; ?>" width=7 border=0>
                </td>
                <td valign=top width=79 height=1><img height=1 src="data:image/png;base64, <?php echo $Base64_2_image; ?>" width=79 border=0></td>
                <td valign=top width=7 height=1>
                    <img height=1 src="data:image/png;base64, <?php echo $Base64_2_image; ?>" width=7 border=0>
                </td>
                <td valign=top width=150 height=1><img height=1 src="data:image/png;base64, <?php echo $Base64_2_image; ?>" width=150 border=0></td>
                <td valign=top width=7 height=1>
                    <img height=1 src="data:image/png;base64, <?php echo $Base64_2_image; ?>" width=7 border=0>
                </td>
                <td valign=top width=180 height=1><img height=1 src="data:image/png;base64, <?php echo $Base64_2_image; ?>" width=180 border=0></td>
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
                                <td class=ct valign=top width=7 height=13><img height=13 src="data:image/png;base64, <?php echo $Base64_1_image; ?>" width=1 border=0></td>
                            </tr>
                            <tr>
                                <td class=cp valign=top width=7 height=12><img height=12 src="data:image/png;base64, <?php echo $Base64_1_image; ?>" width=1 border=0></td>
                            </tr>
                            <tr>
                                <td valign=top width=7 height=1><img height=1 src="data:image/png;base64, <?php echo $Base64_2_image; ?>" width=1 border=0></td>
                            </tr>
                        </tbody>
                    </table>
                </td>
                <td valign=top width=468 rowspan=5>
                    <font class=ct>
                        Instruções:
                    </font>
                    <br>
                    <span class=instrucoes>
                        <?php echo $parametros["instrucoes"]; ?>
                        <?php echo $parametros["observacao"]; ?>
                        <?php echo $parametros["instrucoesDescontoAntecipacao"]; ?>
                        <?php echo $parametros["instrucoes2"]; ?><br>
                        <?php echo $parametros["instrucoes1"]; ?><br>
                        <?php echo $parametros["maxDiasAposVencimento"]; ?>
                        <?php echo ""; ?>
                        <br><br>
                    </span>
                </td>
                <td align=right width=188>
                    <table cellspacing=0 cellpadding=0 border=0>
                        <tbody>
                            <tr>
                                <td class=ct valign=top width=7 height=13><img height=13 src="data:image/png;base64, <?php echo $Base64_1_image; ?>" width=1 border=0></td>
                                <td class=ct valign=top width=180 height=13>(-) Desconto / Abatimentos</td>
                            </tr>
                            <tr>
                                <td class=cp valign=top width=7 height=12><img height=12 src="data:image/png;base64, <?php echo $Base64_1_image; ?>" width=1 border=0></td>
                                <td class=cp valign=top align=right width=180 height=12></td>
                            </tr>
                            <tr>
                                <td valign=top width=7 height=1><img height=1 src="data:image/png;base64, <?php echo $Base64_2_image; ?>" width=7 border=0></td>
                                <td valign=top width=180 height=1><img height=1 src="data:image/png;base64, <?php echo $Base64_2_image; ?>" width=180 border=0></td>
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
                                <td class=ct valign=top width=7 height=13><img height=13 src="data:image/png;base64, <?php echo $Base64_1_image; ?>" width=1 border=0></td>
                            </tr>
                            <tr>
                                <td class=cp valign=top width=7 height=12><img height=12 src="data:image/png;base64, <?php echo $Base64_1_image; ?>" width=1 border=0></td>
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
                                <td class=ct valign=top width=7 height=13><img height=13 src="data:image/png;base64, <?php echo $Base64_1_image; ?>" width=1 border=0></td>
                                <td class=ct valign=top width=180 height=13>(-) Outras deduções</td>
                            </tr>
                            <tr>
                                <td class=cp valign=top width=7 height=12> <img height=12 src="data:image/png;base64, <?php echo $Base64_1_image; ?>" width=1 border=0></td>
                                <td class=cp valign=top align=right width=180 height=12></td>
                            </tr>
                            <tr>
                                <td valign=top width=7 height=1><img height=1 src="data:image/png;base64, <?php echo $Base64_2_image; ?>" width=7 border=0></td>
                                <td valign=top width=180 height=1><img height=1 src="data:image/png;base64, <?php echo $Base64_2_image; ?>" width=180 border=0></td>
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
                                    <img height=13 src="data:image/png;base64, <?php echo $Base64_1_image; ?>" width=1 border=0>
                                </td>
                            </tr>
                            <tr>
                                <td class=cp valign=top width=7 height=12><img height=12 src="data:image/png;base64, <?php echo $Base64_1_image; ?>" width=1 border=0></td>
                            </tr>
                            <tr>
                                <td valign=top width=7 height=1><img height=1 src="data:image/png;base64, <?php echo $Base64_2_image; ?>" width=1 border=0></td>
                            </tr>
                        </tbody>
                    </table>
                </td>
                <td align=right width=188>
                    <table cellspacing=0 cellpadding=0 border=0>
                        <tbody>
                            <tr>
                                <td class=ct valign=top width=7 height=13><img height=13 src="data:image/png;base64, <?php echo $Base64_1_image; ?>" width=1 border=0></td>
                                <td class=ct valign=top width=180 height=13>(+) Mora / Multa</td>
                            </tr>
                            <tr>
                                <td class=cp valign=top width=7 height=12><img height=12 src="data:image/png;base64, <?php echo $Base64_1_image; ?>" width=1 border=0></td>
                                <td class=cp valign=top align=right width=180 height=12></td>
                            </tr>
                            <tr>
                                <td valign=top width=7 height=1> <img height=1 src="data:image/png;base64, <?php echo $Base64_2_image; ?>" width=7 border=0></td>
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
                                <td class=ct valign=top width=7 height=13><img height=13 src="data:image/png;base64, <?php echo $Base64_1_image; ?>" width=1 border=0></td>
                            </tr>
                            <tr>
                                <td class=cp valign=top width=7 height=12><img height=12 src="data:image/png;base64, <?php echo $Base64_1_image; ?>" width=1 border=0></td>
                            </tr>
                            <tr>
                                <td valign=top width=7 height=1><img height=1 src="data:image/png;base64, <?php echo $Base64_2_image; ?>" width=1 border=0></td>
                            </tr>
                        </tbody>
                    </table>
                </td>
                <td align=right width=188>
                    <table cellspacing=0 cellpadding=0 border=0>
                        <tbody>
                            <tr>
                                <td class=ct valign=top width=7 height=13><img height=13 src="data:image/png;base64, <?php echo $Base64_1_image; ?>" width=1 border=0></td>
                                <td class=ct valign=top width=180 height=13>(+) Outros acréscimos</td>
                            </tr>
                            <tr>
                                <td class=cp valign=top width=7 height=12><img height=12 src="data:image/png;base64, <?php echo $Base64_1_image; ?>" width=1 border=0></td>
                                <td class=cp valign=top align=right width=180 height=12></td>
                            </tr>
                            <tr>
                                <td valign=top width=7 height=1><img height=1 src="data:image/png;base64, <?php echo $Base64_2_image; ?>" width=7 border=0></td>
                                <td valign=top width=180 height=1><img height=1 src="data:image/png;base64, <?php echo $Base64_2_image; ?>" width=180 border=0></td>
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
                                <td class=ct valign=top width=7 height=13><img height=13 src="data:image/png;base64, <?php echo $Base64_1_image; ?>" width=1 border=0></td>
                            </tr>
                            <tr>
                                <td class=cp valign=top width=7 height=12><img height=12 src="data:image/png;base64, <?php echo $Base64_1_image; ?>" width=1 border=0></td>
                            </tr>
                        </tbody>
                    </table>
                </td>
                <td align=right width=188>
                    <table cellspacing=0 cellpadding=0 border=0>
                        <tbody>
                            <tr>
                                <td class=ct valign=top width=7 height=13><img height=13 src="data:image/png;base64, <?php echo $Base64_1_image; ?>" width=1 border=0></td>
                                <td class=ct valign=top width=180 height=13>(=) Valor cobrado</td>
                            </tr>
                            <tr>
                                <td class=cp valign=top width=7 height=12><img height=12 src="data:image/png;base64, <?php echo $Base64_1_image; ?>" width=1 border=0></td>
                                <td class=cp valign=top align=right width=180 height=12></td>
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
                <td valign=top width=666 height=1><img height=1 src="data:image/png;base64, <?php echo $Base64_2_image; ?>" width=666 border=0></td>
            </tr>
        </tbody>
    </table>
    <table cellspacing=0 cellpadding=0 border=0>
        <tbody>
            <tr>
                <td class=ct valign=top width=7 height=13><img height=13 src="data:image/png;base64, <?php echo $Base64_1_image; ?>" width=1 border=0></td>
                <td class=ct valign=top width=659 height=13>Sacado</td>
            </tr>
            <tr>
                <td class=cp valign=top width=7 height=12><img height=12 src="data:image/png;base64, <?php echo $Base64_1_image; ?>" width=1 border=0></td>
                <td class=cp valign=top width=659 height=12><span class="campo">
                        <?php echo $parametros["sacado_nome"]; ?>
                    </span>
                </td>
            </tr>
        </tbody>
    </table>
    <table cellspacing=0 cellpadding=0 border=0>
        <tbody>
            <tr>
                <td class=cp valign=top width=7 height=12><img height=12 src="data:image/png;base64, <?php echo $Base64_1_image; ?>" width=1 border=0></td>
                <td class=cp valign=top width=659 height=12><span class="campo">
                        <?php echo $parametros["sacado_endereco"]; ?>
                    </span>
                </td>
            </tr>
        </tbody>
    </table>
    <table cellspacing=0 cellpadding=0 border=0>
        <tbody>
            <tr>
                <td class=ct valign=top width=7 height=13><img height=13 src="data:image/png;base64, <?php echo $Base64_1_image; ?>" width=1 border=0></td>
                <td class=cp valign=top width=472 height=13>
                    <span class="campo">
                        <?php echo $parametros["endereco2"]; ?>
                    </span>
                </td>
                <td class=ct valign=top width=7 height=13><img height=13 src="data:image/png;base64, <?php echo $Base64_1_image; ?>" width=1 border=0></td>
                <td class=ct valign=top width=180 height=13>Cód. baixa</td>
            </tr>
            <tr>
                <td valign=top width=7 height=1><img height=1 src="data:image/png;base64, <?php echo $Base64_2_image; ?>" width=7 border=0></td>
                <td valign=top width=472 height=1><img height=1 src="data:image/png;base64, <?php echo $Base64_2_image; ?>" width=472 border=0></td>
                <td valign=top width=7 height=1><img height=1 src="data:image/png;base64, <?php echo $Base64_2_image; ?>" width=7 border=0></td>
                <td valign=top width=180 height=1><img height=1 src="data:image/png;base64, <?php echo $Base64_2_image; ?>" width=180 border=0></td>
            </tr>
        </tbody>
    </table>
    <table cellSpacing=0 cellPadding=0 border=0 width=666>
        <tbody>
            <tr>
                <td class=ct width=7 height=12></td>
                <td class=ct width=379>Sacador/Avalista</td>
                <td class=ct width=280>
                    <div align=right>Autenticação mecânica - <b class=cp>Ficha de Compensação</b></div>
                </td>
            </tr>
            <tr>
                <td class=ct colspan=3></td>
            </tr>
        </tbody>
    </table>
    <table cellSpacing=0 cellPadding=0 width=666 border=0>
        <tbody>
            <tr>
                <td vAlign=bottom align=left height=50><?php fbarcode($parametros["codigo_barras"], $host); ?></td>
            </tr>
        </tbody>
    </table>
    <table cellSpacing=0 cellPadding=0 width=666 border=0>
        <tr>
            <td class=ct width=666></td>
        </tr>
        <tbody>
            <tr>
                <td class=ct width=666>
                    <div align=right>Corte na linha pontilhada</div>
                </td>
            </tr>
            <tr>
                <td class=ct width=666><img height=1 src="data:image/png;base64, <?php echo $Base64_6_image; ?>" width=665 border=0></TD>
            </tr>
        </tbody>
    </table>
</body>
</html>