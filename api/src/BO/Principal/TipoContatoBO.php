<?php
namespace App\BO\Principal;

/**
 *
 * @author Luiz Antonio Costa
 */
class TipoContatoBO
{


    /**
     * Verifica se o tipo Contato existe na base
     *
     * @param \App\Repository\Principal\TipoContatoRepository $tipoContatoRepository
     * @param int $id
     * @param string $mensagemErro
     * @param NULL|\App\Entity\Principal\TipoContato $tipoContatoORM
     *
     * @return boolean
     */
    public static function verificaTipoContatoExiste(\App\Repository\Principal\TipoContatoRepository $tipoContatoRepository, $id, &$mensagemErro, &$tipoContatoORM=null)
    {
        $tipoContatoORM = $tipoContatoRepository->find($id);
        if (is_null($tipoContatoORM) === true) {
            $mensagemErro = "TipoContato não encontrado na base de dados.";
            return false;
        }

        return true;
    }


}
