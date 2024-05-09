<?php
namespace App\BO\Principal;

use App\Helper\ConstanteParametros;

/**
 *
 * @author Luiz Antonio Costa
 */
class BancoBO
{
    /**
     *
     * @var \App\Repository\Principal\BancoRepository
     */
    private static $bancoRepository;

    function __construct(\App\Repository\Principal\BancoRepository $bancoRepository)
    {
        self::$bancoRepository = $bancoRepository;
    }

    /**
     * Verifica se o codigo do banco informado existe ja cadastrado no banco de dados
     *
     * @param string $codigo Codigo do banco a ser consultado
     * @param int $id ID do banco
     * @param null|array $resultadoORM Retorno da consulta do banco(ponteiro, podendo retornar NULL|Array)
     *
     * @return boolean
     */
    protected function verificaCodigoBancoExiste($codigo, $id=null, &$resultadoORM=null)
    {
        $resultadoORM = self::$bancoRepository->buscarBancoPorCodigo($codigo, $id);
        return (is_null($resultadoORM) === false);
    }

    /**
     * Verifica no banco de dados se existe algum banco ja cadastrado com a descricacao informada
     *
     * @param string $descricao Descricao a ser pesquisada no banco
     * @param int $id ID do banco
     * @param null|array $resultadoORM Retorno da consulta do banco(ponteiro, podendo retornar NULL|Array)
     *
     * @return boolean
     */
    protected function verificaDescricaoBancoExiste($descricao, $id=null, &$resultadoORM=null)
    {
        $resultadoORM = self::$bancoRepository->buscarBancoPorDescricao($descricao, $id);
        return (is_null($resultadoORM) === false);
    }

    /**
     * Verifica se a ID do banco informada existe na base de dados
     *
     * @param int $id Chave primaria do banco de dados
     * @param string $mensagemErro Mensagem que ira retornar para front-end
     * @param null|array $retornoORM Retorno da consulta do banco(ponteiro, podendo retornar NULL|Array)
     *
     * @return boolean
     */
    public static function verificaBancoExiste($id, &$mensagemErro, &$retornoORM=null, \App\Repository\Principal\BancoRepository &$bancoRepos=null)
    {
        if (is_null(self::$bancoRepository) === true) {
            $retornoORM = $bancoRepos->find($id);
        } else {
            $retornoORM = self::$bancoRepository->find($id);
        }

        return is_null($retornoORM) === false;
    }

    /**
     * Verifica se ao executar as regras, ocorrera algum erro na validacao, caso exista retornara como false
     *
     * @param array $parametros
     * @param int $id ID do banco
     * @param string $mensagemErro Mensagem que ira retornar para front-end
     * @param null|array $retornoORM Retorno da consulta do banco(ponteiro, podendo retornar NULL|Array)
     *
     * @return boolean
     */
    public function podeSalvar($parametros, $id=null, &$mensagemErro="", &$retornoORM=null)
    {
        $bRetorno = true;

        if ($this->verificaCodigoBancoExiste($parametros[ConstanteParametros::CHAVE_CODIGO], $id, $retornoORM) === true) {
            $mensagemErro .= "Não é possivel continuar pois já existe um banco com o código informado.";
            $bRetorno      = false;
        } else if ($this->verificaDescricaoBancoExiste($parametros[ConstanteParametros::CHAVE_DESCRICAO], $id, $retornoORM) === true) {
            $mensagemErro .= "Não é possivel continuar pois já existe um banco com descrição informada.";
            $bRetorno      = false;
        }

        return $bRetorno;
    }


}
