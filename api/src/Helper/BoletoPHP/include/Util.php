<?php
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

if (function_exists('esquerda') === false) {


    function esquerda($entra, $comp)
    {
        return substr($entra, 0, $comp);
    }


}

if (function_exists('direita') === false) {


    function direita($entra, $comp)
    {
        return substr($entra, strlen($entra) - $comp, $comp);
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
            if ($year > 0) {
                $year--;
            } else {
                $year = 99;
                $century --;
            }
        }

        return ( floor((  146097 * $century) / 4) + floor(( 1461 * $year) / 4) + floor(( 153 * $month + 2) / 5) + $day + 1721119);
    }


}//end if


if (function_exists("formata_numero") === false) {


    function formata_numero($numero, $loop, $insert, $tipo="geral")
    {
        if ($tipo === "geral") {
            $numero = str_replace(",", "", $numero);
            while (strlen($numero) < $loop) {
                $numero = $insert . $numero;
            }
        }

        if ($tipo === "valor") {
            $numero = str_replace(",", "", $numero);
            while (strlen($numero) < $loop) {
                $numero = $insert . $numero;
            }
        }

        if ($tipo === "convenio") {
            while (strlen($numero) < $loop) {
                $numero = $numero . $insert;
            }
        }

        return $numero;
    }


}//end if

if (function_exists("mask") === false) {


    function mask($val, $mask)
    {
        $maskared = '';
        $k        = 0;
        for ($i = 0; $i <= strlen($mask) - 1; $i++) {
            if ($mask[$i] === '#') {
                if (isset($val[$k]) === true) {
                    $maskared .= $val[$k++];
                }
            } else {
                if (isset($mask[$i]) === true) {
                    $maskared .= $mask[$i];
                }
            }
        }

        return $maskared;
    }


}//end if
