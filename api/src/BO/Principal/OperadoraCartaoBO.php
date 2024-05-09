<?php
namespace App\BO\Principal;

use Doctrine\ORM\EntityManagerInterface;
use App\Helper\ConstanteParametros;

/**
 *
 * @author Luiz Antonio Costa
 */
class OperadoraCartaoBO extends GenericBO
{


    function __construct(EntityManagerInterface $entityManager)
    {
        parent::configuraGenericBO(
            [
                "franqueadaRepository" => $entityManager->getRepository(\App\Entity\Principal\Franqueada::class),
            ]
        );
    }

    /**
     * Verifica parametros obrigatorios
     *
     * @param array $parametros
     * @param string $mensagemErro
     *
     * @return boolean
     */
    protected function verificaParametrosRelacionaisObrigatorios(&$parametros, &$mensagemErro)
    {
        if (self::verificaFranqueadaExisteBO($parametros, $mensagemErro, $parametros[ConstanteParametros::CHAVE_FRANQUEADA]) === true) {
            return true;
        }

        return false;
    }

    /**
     * Verifica se as regras estão validas e retorna true
     *
     * @param array $parametros
     * @param string $mensagemErro
     *
     * @return boolean
     */
    public function podeSalvar(&$parametros, &$mensagemErro)
    {
        if ($this->verificaParametrosRelacionaisObrigatorios($parametros, $mensagemErro) === true) {
            return true;
        }

        return false;
    }

    /**
     * Verifica se as regras estão validas e retorna true
     *
     * @param array $parametros
     * @param string $mensagemErro
     *
     * @return boolean
     */
    public function podeAlterar(&$parametros, &$mensagemErro)
    {
        if ($this->verificaParametrosRelacionaisObrigatorios($parametros, $mensagemErro) === true) {
            return true;
        }

        return false;
    }

    /**
     * Verifica se existe a operadoraCartao no banco de dados, se existir, ele retorna no ultimo parametro na funcao, caso contrario, ele preenchera a msg de erro e retornara falso
     *
     * @param \App\Repository\Principal\OperadoraCartaoRepository $operadoraCartaoRepository Repositorio da OperadoraCartao
     * @param integer $id Chave primaria da categoria
     * @param string $mensagemErro Msg de erro para ser retornada ao front-end
     * @param \App\Entity\Principal\OperadoraCartao|null $operadoraCartaoORM Retorno no caso de sucesso
     *
     * @return boolean true|false
     */
    public static function verificaOperadoraCartaoExiste(\App\Repository\Principal\OperadoraCartaoRepository $operadoraCartaoRepository, $id, &$mensagemErro, &$operadoraCartaoORM)
    {
        if (is_array($id)) {
            $id = $id["id"];
        }
       
        $operadoraCartaoORM = $operadoraCartaoRepository->find($id);
        if (is_null($operadoraCartaoORM) === true) {
            $mensagemErro = "OperadoraCartao não encontrada na base de dados.";
            return false;
        }

        return true;
    }


}
