<?php
namespace App\BO\Principal;

use Doctrine\ORM\EntityManagerInterface;

/**
 *
 * @author Luiz Antonio Costa
 */
class TipoMovimentoContaBO extends GenericBO
{


    function __construct(EntityManagerInterface $entityManager)
    {
        parent::configuraGenericBO(
            [
                "tipoMovimentoContaRepository" => $entityManager->getRepository(\App\Entity\Principal\TipoMovimentoConta::class),
            ]
        );
    }

    /**
     * Verifica a existencia do TipoMovimentoConta atraves da ID
     *
     * @param \App\Repository\Principal\TipoMovimentoContaRepository $tipoMovimentoContaRepository Tipo Movimento Conta repositorio
     * @param int $id Chave Primaria
     * @param string $mensagemErro Mensagem de erro para retornar pro front-end
     * @param null|\App\Entity\Principal\TipoMovimentoConta $resultadoORM Resultado da consulta
     *
     * @return boolean
     */
    public static function verificaTipoMovimentoContaExisteId($tipoMovimentoContaRepository, $id, &$mensagemErro, &$resultadoORM=null)
    {
        $resultadoORM = $tipoMovimentoContaRepository->find($id);
        if (is_null($resultadoORM) === true) {
            $mensagemErro .= "O Tipo Movimento Conta informado, não foi encontrado na base de dados.";
            return false;
        }

        return true;
    }


}
