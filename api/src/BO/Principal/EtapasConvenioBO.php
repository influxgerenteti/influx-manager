<?php
namespace App\BO\Principal;

/**
 *
 * @author Luiz Antonio Costa
 */
class EtapasConvenioBO
{


    /**
     * Verifica se existe o EtapasConvenio no banco de dados, se existir, ele retorna no ultimo parametro na funcao, caso contrario, ele preenchera a msg de erro e retornara falso
     *
     * @param \App\Repository\Principal\EtapasConvenioRepository $etapasConvenioRepository Repositorio da EtapasConvenio
     * @param integer $id Chave primaria da EtapasConvenio
     * @param string $mensagemErro Msg de erro para ser retornada ao front-end
     * @param \App\Entity\Principal\EtapasConvenio|null $etapasConvenioORM Retorno no caso de sucesso
     *
     * @return boolean true|false
     */
    public static function verificaEtapasConvenioExiste(\App\Repository\Principal\EtapasConvenioRepository $etapasConvenioRepository, $id, &$mensagemErro, &$etapasConvenioORM)
    {
        $etapasConvenioORM = $etapasConvenioRepository->find($id);
        if (is_null($etapasConvenioORM) === true) {
            $mensagemErro = "EtapasConvenio não encontrada na base de dados.";
            return false;
        }

        return true;
    }


}
