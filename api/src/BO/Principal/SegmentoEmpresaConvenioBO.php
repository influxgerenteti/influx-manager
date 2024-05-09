<?php
namespace App\BO\Principal;

/**
 *
 * @author Luiz Antonio Costa
 */
class SegmentoEmpresaConvenioBO
{


    /**
     * Verifica se existe o SegmentoEmpresaConvenio no banco de dados, se existir, ele retorna no ultimo parametro na funcao, caso contrario, ele preenchera a msg de erro e retornara falso
     *
     * @param \App\Repository\Principal\SegmentoEmpresaConvenioRepository $segmentoEmpresaConvenioRepository Repositorio da SegmentoEmpresaConvenio
     * @param integer $id Chave primaria da SegmentoEmpresaConvenio
     * @param string $mensagemErro Msg de erro para ser retornada ao front-end
     * @param \App\Entity\Principal\SegmentoEmpresaConvenio|null $segmentoEmpresaConvenioORM Retorno no caso de sucesso
     *
     * @return boolean true|false
     */
    public static function verificaSegmentoEmpresaConvenioExiste(\App\Repository\Principal\SegmentoEmpresaConvenioRepository $segmentoEmpresaConvenioRepository, $id, &$mensagemErro, &$segmentoEmpresaConvenioORM)
    {
        $segmentoEmpresaConvenioORM = $segmentoEmpresaConvenioRepository->find($id);
        if (is_null($segmentoEmpresaConvenioORM) === true) {
            $mensagemErro = "SegmentoEmpresaConvenio não encontrada na base de dados.";
            return false;
        }

        return true;
    }


}
