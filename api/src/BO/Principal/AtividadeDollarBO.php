<?php
namespace App\BO\Principal;

use Doctrine\ORM\EntityManagerInterface;
use App\Helper\ConstanteParametros;

/**
 *
 * @author Rodrigo de Souza Fernandes (GATI labs)
 */
class AtividadeDollarBO extends GenericBO
{


    function __construct(EntityManagerInterface $entityManager)
    {
        parent::configuraGenericBO(
            [
                "atividadeDollarRepository" => $entityManager->getRepository(\App\Entity\Principal\AtividadeDollar::class),
            ]
        );
    }

    /**
     * Verifica no banco de dados se existe alguma Atividade Dollar já cadastrada com a descricacao informada
     *
     * @param string $descricao Descrição a ser pesquisada na Atividade Dollar
     * @param int $id ID da Atividade Dollar
     * @param null|array $resultadoORM Retorno da consulta da Atividade Dollar(ponteiro, podendo retornar NULL|Array)
     *
     * @return boolean
     */
    protected function verificaDescricaoAtividadeDollarExiste($descricao, $id=null, &$resultadoORM=null)
    {
        $resultadoORM = self::$atividadeDollarRepository->buscarAtividadeDollarPorDescricao($descricao, $id);
        return (is_null($resultadoORM) === false);
    }

    /**
     * Verifica se existe a atividadeDollar no banco de dados, se existir, ele retorna no ultimo parametro na funcao, caso contrario, ele preenchera a msg de erro e retornara falso
     *
     * @param \App\Repository\Principal\AtividadeDollarRepository $atividadeDollarRepos Repositorio do atividadeDollar
     * @param integer $id Chave primaria do atividadeDollar
     * @param string$mensagemErro Msg de erro para ser retornada ao front-end
     * @param \App\Entity\Principal\AtividadeDollar|null $retornoORM Retorno no caso de sucesso
     *
     * @return boolean true|false
     */
    public static function verificaAtividadeDollarExiste(\App\Repository\Principal\AtividadeDollarRepository &$atividadeDollarRepos=null, $id, &$mensagemErro, &$retornoORM=null)
    {
        $retornoORM = $atividadeDollarRepos->find($id);

        if ($retornoORM === null) {
            $mensagemErro = "AtividadeDollar não encontrado na base de dados.";
            return false;
        }

        return true;
    }

    /**
     * Verifica se ao executar as regras, ocorrera algum erro na validacao, caso exista retornara como false
     *
     * @param array $parametros
     * @param int $id ID da Atividade Dollar
     * @param string $mensagemErro Mensagem que ira retornar para front-end
     * @param null|array $retornoORM Retorno da consulta da Atividade Dollar(ponteiro, podendo retornar NULL|Array)
     *
     * @return boolean
     */
    public function podeSalvar($parametros, $id=null, &$mensagemErro="", &$retornoORM=null)
    {
        $bRetorno = true;

        if ($this->verificaDescricaoAtividadeDollarExiste($parametros[ConstanteParametros::CHAVE_DESCRICAO], $id, $retornoORM) === true) {
            $mensagemErro .= "Não é possivel continuar pois já existe uma Atividade Dollar com a descrição informada.";
            $bRetorno      = false;
        }

        return $bRetorno;
    }


}
