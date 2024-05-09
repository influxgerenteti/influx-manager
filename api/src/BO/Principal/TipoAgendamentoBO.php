<?php
namespace App\BO\Principal;

/**
 *
 * @author Augusto Lucas de Souza (GATI labs)
 */
class TipoAgendamentoBO extends GenericBO
{


    /**
     * Verifica se existe o tipo de agendamento no banco de dados, se existir, ele retorna no último parâmetro na função, caso contrário, ele preenche a mensagem de erro e retorna falso
     *
     * @param \App\Repository\Principal\TipoAgendamentoRepository $tipoAgendamentoRepository Repositório do TipoAgendamento
     * @param integer $id Chave primária do TipoAgendamento
     * @param string $mensagemErro Mensagem de erro para ser retornada ao front-end
     * @param \App\Entity\Principal\TipoAgendamento|null $tipoAgendamentoORM Retorno no caso de sucesso
     * @param boolean $retornoObjeto Informa a função se deve retornar como Objetivo ou Array por padrão será Array
     *
     * @return boolean true|false
     */
    public static function verificaTipoAgendamentoExiste(\App\Repository\Principal\TipoAgendamentoRepository $tipoAgendamentoRepository, $id, &$mensagemErro, &$tipoAgendamentoORM, $retornoObjeto=false)
    {
        if ($retornoObjeto === true) {
            $tipoAgendamentoORM = $tipoAgendamentoRepository->find($id);
        } else {
            $tipoAgendamentoORM = $tipoAgendamentoRepository->buscarRegistroPorId($id);
        }

        if ($tipoAgendamentoORM === null) {
            $mensagemErro = "Tipo de agendamento não encontrado na base de dados.";
            return false;
        }

        return true;
    }


}
