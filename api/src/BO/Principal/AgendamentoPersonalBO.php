<?php
namespace App\BO\Principal;

/**
 *
 * @author Luiz Antonio Costa
 */
class AgendamentoPersonalBO
{


    /**
     * Verifica se existe a AgendamentoPersonal no banco de dados, se existir, ele retorna no ultimo parametro na funcao, caso contrario, ele preenchera a msg de erro e retornara falso
     *
     * @param \App\Repository\Principal\AgendamentoPersonalRepository $agendamentoPersonalRepository Repositorio da AgendamentoPersonal
     * @param integer $id Chave primaria da AgendamentoPersonal
     * @param string $mensagemRetorno Msg de erro para ser retornada ao front-end
     * @param \App\Entity\Principal\AgendamentoPersonal|null $agendamentoPersonalORM Retorno no caso de sucesso
     *
     * @return boolean true|false
     */
    public static function verificaAgendamentoPersonalExiste($agendamentoPersonalRepository, $id, &$mensagemRetorno, &$agendamentoPersonalORM=null)
    {
        $agendamentoPersonalORM = $agendamentoPersonalRepository->find($id);
        if (is_null($agendamentoPersonalORM) === true) {
            $mensagemErro = "AgendamentoPersonal não encontrada na base de dados.";
            return false;
        }

        return true;
    }


}
