<?php
namespace App\BO\Principal;

use Doctrine\ORM\EntityManagerInterface;
use App\Helper\ConstanteParametros;

/**
 *
 * @author Luiz Antonio Costa
 */
class ParcelaParcelamentoBO extends GenericBO
{


    function __construct(EntityManagerInterface $entityManager)
    {
        parent::configuraGenericBO(
            [
                "parcelamentoOperadoraCartaoRepository" => $entityManager->getRepository(\App\Entity\Principal\ParcelamentoOperadoraCartao::class),
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
        if (self::verificaParcelamentoOperadoraCartaoExisteBO($parametros, $mensagemErro, $parametros[ConstanteParametros::CHAVE_PARCELAMENTO_OPERADORA_CARTAO]) === true) {
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
    public function podeSalvar($parametros, &$mensagemErro)
    {
        $tipoDado = gettype($parametros[ConstanteParametros::CHAVE_PARCELAMENTO_OPERADORA_CARTAO]);
        if ((isset($parametros[ConstanteParametros::CHAVE_PARCELAMENTO_OPERADORA_CARTAO]) === true)&&($tipoDado === 'object')) {
            return true;
        } else {
            $mensagemErro .= "Foi esperado um objeto porem foi me passado o tipo '" . $tipoDado . "'";
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
     * Verifica se existe a ParcelaParcelamentoORM no banco de dados, se existir, ele retorna no ultimo parametro na funcao, caso contrario, ele preenchera a msg de erro e retornara falso
     *
     * @param \App\Repository\Principal\ParcelaParcelamentoRepository $parcelaParcelamentoRepository Repositorio da ParcelaParcelamento
     * @param integer $id Chave primaria da categoria
     * @param string $mensagemErro Msg de erro para ser retornada ao front-end
     * @param \App\Entity\Principal\ParcelaParcelamento|null $parcelaParcelamentoORM Retorno no caso de sucesso
     *
     * @return boolean true|false
     */
    public static function verificaParcelaParcelamentoExiste(\App\Repository\Principal\ParcelaParcelamentoRepository $parcelaParcelamentoRepository, $id, &$mensagemErro, &$parcelaParcelamentoORM)
    {
        $parcelaParcelamentoORM = $parcelaParcelamentoRepository->find($id);
        if (is_null($parcelaParcelamentoORM) === true) {
            $mensagemErro = "ParcelaParcelamentoORM não encontrada na base de dados.";
            return false;
        }

        return true;
    }


}
