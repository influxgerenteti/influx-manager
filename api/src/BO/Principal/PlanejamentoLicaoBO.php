<?php
namespace App\BO\Principal;

/**
 *
 * @author Luiz Antonio Costa
 */
class PlanejamentoLicaoBO
{


    /**
     * Verifica se existe o Planejamento Licao no banco de dados, se existir, ele retorna no ultimo parametro na funcao, caso contrario, ele preenchera a msg de erro e retornara falso
     *
     * @param \App\Repository\Principal\PlanejamentoLicaoRepository $planejamentoLicaoRepository Repositorio da Categoria
     * @param integer $id Chave primaria da categoria
     * @param string$mensagemErro Msg de erro para ser retornada ao front-end
     * @param \App\Entity\Principal\PlanejamentoLicao|null $planejamentoLicaoORM Retorno no caso de sucesso
     *
     * @return boolean true|false
     */
    public static function verificaPlanejamentoLicaoExiste(\App\Repository\Principal\PlanejamentoLicaoRepository $planejamentoLicaoRepository, $id, &$mensagemErro, &$planejamentoLicaoORM)
    {
        $planejamentoLicaoORM = $planejamentoLicaoRepository->find($id);
        if ($planejamentoLicaoORM === null) {
            $mensagemErro = "Planejamento Licao não encontrado na base de dados.";
            return false;
        }

        return true;
    }


}
