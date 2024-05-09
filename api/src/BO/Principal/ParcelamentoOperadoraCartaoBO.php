<?php
namespace App\BO\Principal;

use Doctrine\ORM\EntityManagerInterface;
use App\Helper\ConstanteParametros;

/**
 *
 * @author Luiz Antonio Costa
 */
class ParcelamentoOperadoraCartaoBO extends GenericBO
{


    function __construct(EntityManagerInterface $entityManager)
    {
        parent::configuraGenericBO(
            [
                "operadoraCartaoRepository" => $entityManager->getRepository(\App\Entity\Principal\OperadoraCartao::class),
                "planoContaRepository"      => $entityManager->getRepository(\App\Entity\Principal\PlanoConta::class),
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
        $tipoDado = gettype($parametros[ConstanteParametros::CHAVE_OPERADORA_CARTAO]);
        $bRetorno = true;
        if ($tipoDado !== 'object') {
            $bRetorno = self::verificaOperadoraCartaoExisteBO($parametros, $mensagemErro, $parametros[ConstanteParametros::CHAVE_OPERADORA_CARTAO]);
        }

        if ((self::verificaPlanoContaExisteBO($parametros, $mensagemErro, $parametros[ConstanteParametros::CHAVE_PLANO_CONTA]) === true) && ($bRetorno === true)) {
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
            $tipoDado = gettype($parametros[ConstanteParametros::CHAVE_OPERADORA_CARTAO]);
            if ((isset($parametros[ConstanteParametros::CHAVE_OPERADORA_CARTAO]) === true)&&($tipoDado === 'object')) {
                return true;
            } else {
                $mensagemErro .= "Foi esperado um objeto porem foi me passado o tipo '" . $tipoDado . "'";
            }
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
     * Verifica se existe a ParcelamentoOperadoraCartao no banco de dados, se existir, ele retorna no ultimo parametro na funcao, caso contrario, ele preenchera a msg de erro e retornara falso
     *
     * @param \App\Repository\Principal\ParcelamentoOperadoraCartaoRepository $parcelamentoOperadoraRepository Repositorio da ParcelamentoOperadoraCartao
     * @param integer $id Chave primaria da categoria
     * @param string $mensagemErro Msg de erro para ser retornada ao front-end
     * @param \App\Entity\Principal\ParcelamentoOperadoraCartao|null $parcelamentoOperadoraORM Retorno no caso de sucesso
     *
     * @return boolean true|false
     */
    public static function verificaParcelamentoOperadoraCartaoExiste(\App\Repository\Principal\ParcelamentoOperadoraCartaoRepository $parcelamentoOperadoraRepository, $id, &$mensagemErro, &$parcelamentoOperadoraORM)
    {
        if (is_array($id)) {
            $id = $id["id"];
        }
       
        $parcelamentoOperadoraORM = $parcelamentoOperadoraRepository->find($id);
        if (is_null($parcelamentoOperadoraORM) === true) {
            $mensagemErro = "ParcelamentoOperadoraORM não encontrada na base de dados.";
            return false;
        }

        return true;
    }


}
