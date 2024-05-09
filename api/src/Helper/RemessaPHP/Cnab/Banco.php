<?php

namespace App\Helper\RemessaPHP\Cnab;

class Banco
{
    const BANCO_DO_BRASIL = 1;
    const SANTANDER       = 33;
    const CEF           = 104;
    const BRADESCO      = 237;
    const ITAU          = 341;
    const SICREDI       = 748;
    const POSTO_SICREDI = 30;
    const BYTE_ID_SICREDI = 2;

    public static function getBanco($codigo)
    {
        if ($codigo === self::BANCO_DO_BRASIL) {
            return [
                'codigo_do_banco' => self::BANCO_DO_BRASIL,
                'nome_do_banco'   => 'BANCO DO BRASIL S.A.',
            ];
        } else if ($codigo === self::ITAU) {
            return [
                'codigo_do_banco' => self::ITAU,
                'nome_do_banco'   => 'BANCO ITAU SA',
            ];
        } else if ($codigo === self::CEF) {
            return [
                'codigo_do_banco' => self::CEF,
                'nome_do_banco'   => 'CAIXA ECONOMICA FEDERAL',
            ];
        } else if ($codigo === self::SANTANDER) {
            return [
                'codigo_do_banco' => self::SANTANDER,
                'nome_do_banco'   => 'BANCO SANTANDER (BRASIL) S/A',
            ];
        } else if ($codigo === self::BRADESCO) {
            return [
                'codigo_do_banco' => self::BRADESCO,
                'nome_do_banco'   => 'BRADESCO',
            ];
        } else if ($codigo === self::SICREDI) {
            return [
                'codigo_do_banco' => self::SICREDI,
                'nome_do_banco'   => 'SICREDI',
            ];
        } else {
            return false;
        }//end if
    }

    public static function existBanco($codigo_banco)
    {
        $banco = self::getBanco($codigo_banco);
        if (empty($banco) === true) {
            return false;
        }

        return true;
    }


}
