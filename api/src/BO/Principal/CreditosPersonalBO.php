<?php
namespace App\BO\Principal;

/**
 *
 * @author Luiz Antonio Costa
 */
class CreditosPersonalBO
{


    /**
     * Verifica se existe a CreditosPersonal no banco de dados, se existir, ele retorna no ultimo parametro na funcao, caso contrario, ele preenchera a msg de erro e retornara falso
     *
     * @param \App\Repository\Principal\CreditosPersonalRepository $creditosPersonalRepository Repositorio da CreditosPersonal
     * @param integer $id Chave primaria da CreditosPersonal
     * @param string $mensagemRetorno Msg de erro para ser retornada ao front-end
     * @param \App\Entity\Principal\CreditosPersonal|null $creditosPersonalORM Retorno no caso de sucesso
     *
     * @return boolean true|false
     */
    public static function verificaCreditosPersonalExiste($creditosPersonalRepository, $id, &$mensagemRetorno, &$creditosPersonalORM=null)
    {
        $creditosPersonalORM = $creditosPersonalRepository->find($id);
        if (is_null($creditosPersonalORM) === true) {
            $mensagemErro = "CreditosPersonal não encontrada na base de dados.";
            return false;
        }

        return true;
    }


}
