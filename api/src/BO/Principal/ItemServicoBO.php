<?php
namespace App\BO\Principal;

use Doctrine\ORM\EntityManagerInterface;
use App\Helper\ConstanteParametros;
use App\Helper\SituacoesSistema;

/**
 *
 * @author Luiz Antonio Costa
 */
class ItemServicoBO extends GenericItemBO
{


    function __construct(EntityManagerInterface $entityManager)
    {
        parent::__construct($entityManager);
    }

    /**
     * Verifica se podemos prosseguir com o processo de salvar
     *
     * @param array $parametros Ponteiro de array de parametros para realizar a validacao
     * @param string $mensagemErro Mensagem de erro a ser retornado ao front-end
     *
     * @return boolean
     */
    public function podeSalvar(&$parametros, &$mensagemErro)
    {
        $bRetornoPodeCriarItem = parent::podeSalvar($parametros, $mensagemErro);
        if ($bRetornoPodeCriarItem === true) {
            if ((isset($parametros[ConstanteParametros::CHAVE_PLANO_CONTA]) === true) && (empty($parametros[ConstanteParametros::CHAVE_PLANO_CONTA]) === false)) {
                if (self::verificaPlanoContaExisteBO($parametros, $mensagemErro, $parametros[ConstanteParametros::CHAVE_PLANO_CONTA]) === true) {
                    return $bRetornoPodeCriarItem;
                }
            } else {
                $mensagemErro .= "Não foi informado um plano de conta.";
            }

            return false;
        }

        return false;
    }

    /**
     * Verifica se podemos prosseguir com o processo de alteração
     *
     * @param array $parametros Ponteiro de array de parametros para realizar a validacao
     * @param string $mensagemErro Mensagem de erro a ser retornado ao front-end
     *
     * @return boolean
     */
    public function podeAlterar(&$parametros, &$mensagemErro)
    {
        $bRetornoPodeAlterarItem = parent::podeAlterar($parametros, $mensagemErro);
        if ($bRetornoPodeAlterarItem === true) {
            if ((isset($parametros[ConstanteParametros::CHAVE_PLANO_CONTA]) === true) && (empty($parametros[ConstanteParametros::CHAVE_PLANO_CONTA]) === false)) {
                if (self::verificaPlanoContaExisteBO($parametros, $mensagemErro, $parametros[ConstanteParametros::CHAVE_PLANO_CONTA]) === true) {
                    return $bRetornoPodeAlterarItem;
                }
            } else {
                $mensagemErro .= "Não foi informado um plano de conta.";
            }

            return false;
        }

        return false;
    }


}
