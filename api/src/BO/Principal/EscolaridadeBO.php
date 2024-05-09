<?php
namespace App\BO\Principal;

/**
 *
 * @author Rodrigo de Souza Fernandes (GATI labs)
 */
class EscolaridadeBO
{


    /**
     * Verifica se existe a escolaridade no banco de dados, se existir, ele retorna no ultimo parametro na funcao, caso contrario, ele preenchera a msg de erro e retornara falso
     *
     * @param \App\Repository\Principal\EscolaridadeRepository $escolaridadeRepositoy Repositorio da Escolaridade
     * @param integer $id Chave primaria da escolaridade
     * @param string $mensagemErro Msg de erro para ser retornada ao front-end
     * @param \App\Entity\Principal\Escolaridade|null $escolaridadeORM Retorno no caso de sucesso
     *
     * @return boolean true|false
     */
    public static function verificaEscolaridadeExiste(\App\Repository\Principal\EscolaridadeRepository $escolaridadeRepositoy, $id, &$mensagemErro, &$escolaridadeORM)
    {
        $escolaridadeORM = $escolaridadeRepositoy->find($id);
        if ($escolaridadeORM === null) {
            $mensagemErro = "Escolaridade não encontrada na base de dados.";
            return false;
        }

        return true;
    }


}
