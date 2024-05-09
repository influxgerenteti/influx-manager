<?php
namespace App\BO\Principal;

/**
 *
 * @author Luiz Antonio Costa
 */
class MotivoDevolucaoChequeBO
{


    /**
     * Verifica se existe o motivo devolucao cheque no banco de dados, se existir, ele retorna no ultimo parametro na funcao, caso contrario, ele preenchera a msg de erro e retornara falso
     *
     * @param \App\Repository\Principal\MotivoDevolucaoChequeRepository $motivoDevolucaoChequeRepository Repositorio da Categoria
     * @param integer $id Chave primaria do MotivoDevolucaoCheque
     * @param string $mensagemErro Msg de erro para ser retornada ao front-end
     * @param \App\Entity\Principal\MotivoDevolucaoCheque|null $motivoDevolucaoChequeORM Retorno no caso de sucesso
     *
     * @return boolean true|false
     */
    public static function verificaMotivoDevolucaoChequeExiste(\App\Repository\Principal\MotivoDevolucaoChequeRepository $motivoDevolucaoChequeRepository, $id, &$mensagemErro, &$motivoDevolucaoChequeORM)
    {
        $motivoDevolucaoChequeORM = $motivoDevolucaoChequeRepository->find($id);
        if (is_null($motivoDevolucaoChequeORM) === true) {
            $mensagemErro = "MotivoDevolucao não encontrado na base de dados.";
            return false;
        }

        return true;
    }


}
