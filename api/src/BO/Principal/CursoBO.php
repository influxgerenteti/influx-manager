<?php
namespace App\BO\Principal;

use App\Helper\ConstanteParametros;

/**
 *
 * @author Luiz Antonio Costa
 */
class CursoBO
{


    public static function podeAtualizar(&$parametros, &$mensagemErro, $idiomaRepository, $itemRepository, $modalidadeTurmaRepository)
    {
        $bRetorno = true;
        if ((isset($parametros[ConstanteParametros::CHAVE_IDIOMA]) === true) && (empty($parametros[ConstanteParametros::CHAVE_IDIOMA]) === false)) {
            $parametros[ConstanteParametros::CHAVE_IDIOMA] = $idiomaRepository->find($parametros[ConstanteParametros::CHAVE_IDIOMA]);
            if (is_null($parametros[ConstanteParametros::CHAVE_IDIOMA]) === true) {
                $bRetorno     = false;
                $mensagemErro = "Não foi possível encontrar o idioma socitado.";
            }
        }

        if ((isset($parametros[ConstanteParametros::CHAVE_SERVICO]) === true) && (empty($parametros[ConstanteParametros::CHAVE_SERVICO]) === false)) {
            $parametros[ConstanteParametros::CHAVE_SERVICO] = $itemRepository->find($parametros[ConstanteParametros::CHAVE_SERVICO]);
            if (is_null($parametros[ConstanteParametros::CHAVE_SERVICO]) === true) {
                $bRetorno     = false;
                $mensagemErro = "Não foi possível encontrar o item socitado.";
            }
        }

        
        if ((isset($parametros[ConstanteParametros::CHAVE_MODALIDADE_TURMA]) === true) && (empty($parametros[ConstanteParametros::CHAVE_MODALIDADE_TURMA]) === false)) {
            $parametros[ConstanteParametros::CHAVE_MODALIDADE_TURMA] = $modalidadeTurmaRepository->find($parametros[ConstanteParametros::CHAVE_MODALIDADE_TURMA]);
            if (is_null($parametros[ConstanteParametros::CHAVE_MODALIDADE_TURMA]) === true) {
                $bRetorno     = false;
                $mensagemErro = "Não foi possível encontrar o Modalidade Turma socitado.";
            }
        }

        return $bRetorno;
    }

    /**
     * Verifica se existe o aluno no banco de dados, se existir, ele retorna no ultimo parametro na funcao, caso contrario, ele preenchera a msg de erro e retornara falso
     *
     * @param \App\Repository\Principal\CursoRepository $cursoRepository Repositorio da Categoria
     * @param integer $id Chave primaria da categoria
     * @param string$mensagemErro Msg de erro para ser retornada ao front-end
     * @param \App\Entity\Principal\Curso|null $cursoORM Retorno no caso de sucesso
     * @param boolean $retornoObjeto Informa a funcao se deve retornar como Objeto ou Array por padrao sera Array
     *
     * @return boolean true|false
     */
    public static function verificaCursoExiste(\App\Repository\Principal\CursoRepository $cursoRepository, $id, &$mensagemErro, &$cursoORM, $retornoObjeto=false)
    {
        if ($retornoObjeto === true) {
            $cursoORM = $cursoRepository->find($id);
        } else {
            $cursoORM = $cursoRepository->buscarPorId($id);
        }

        if ($cursoORM === null) {
            $mensagemErro = "Curso não encontrado na base de dados.";
            return false;
        }

        return true;
    }


}
