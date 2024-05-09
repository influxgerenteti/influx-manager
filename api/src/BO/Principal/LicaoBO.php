<?php
namespace App\BO\Principal;

/**
 *
 * @author Luiz Antonio Costa
 */
class LicaoBO
{


    /**
     * Verifica se existe a Licao no banco de dados, se existir, ele retorna no ultimo parametro na funcao, caso contrario, ele preenchera a msg de erro e retornara falso
     *
     * @param \App\Repository\Principal\LicaoRepository $licaoRepository Repositorio da Licao
     * @param integer $id Chave primaria da Licao
     * @param string $mensagemErro Msg de erro para ser retornada ao front-end
     * @param \App\Entity\Principal\Licao|null $licaoORM Retorno no caso de sucesso
     *
     * @return boolean true|false
     */
    public static function verificaLicaoExiste(\App\Repository\Principal\LicaoRepository $licaoRepository, $id, &$mensagemErro, &$licaoORM)
    {
        $licaoORM = $licaoRepository->find($id);
        if (is_null($licaoORM) === true) {
            $mensagemErro = "Licao não encontrada na base de dados.";
            return false;
        }

        return true;
    }


}
