<?php
namespace App\BO\Principal;

use App\Helper\ConstanteParametros;

/**
 *
 * @author Marcelo Andre Naegeler
 */
class TokenBO
{


    /**
     * Valida a criação/atualização de senha do usuário
     *
     * @param string $mensagem Mensagem de erro a ser mostrada ao usuário.
     * @param array $parametros Dados senha e confirmarSenha a serem validados.
     * @param \App\Entity\Principal\Usuario $usuario Usuário que a senha será atualizada.
     *
     * @return boolean
     */
    public static function podeAtualizarSenha (&$mensagem, $parametros=[], \App\Entity\Principal\Usuario $usuario=null)
    {
        if (isset($parametros[ConstanteParametros::CHAVE_SENHA]) === false || isset($parametros[ConstanteParametros::CHAVE_CONFIRMAR_SENHA]) === false) {
            $mensagem = 'Campos senha e confirmar senha estão inválidos.';
            return false;
        }

        $senha          = $parametros[ConstanteParametros::CHAVE_SENHA];
        $confirmarSenha = $parametros[ConstanteParametros::CHAVE_CONFIRMAR_SENHA];

        if ($senha !== '' && $senha !== $confirmarSenha) {
            $mensagem = 'Campos senha e confirmar senha estão inválidos.';
            return false;
        }

        if ($usuario === null) {
            $mensagem = 'Usuário inválido.';
            return false;
        }

        return true;
    }


}
