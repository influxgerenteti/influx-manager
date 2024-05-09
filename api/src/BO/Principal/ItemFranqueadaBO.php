<?php
namespace App\BO\Principal;

use Doctrine\ORM\EntityManagerInterface;
use App\Helper\ConstanteParametros;

/**
 *
 * @author Marcelo A Naegeler
 */
class ItemFranqueadaBO extends GenericBO
{


    function __construct(EntityManagerInterface $entityManager)
    {
        parent::configuraGenericBO(
            [
                "franqueadaRepository" => $entityManager->getRepository(\App\Entity\Principal\Franqueada::class),
                "itemRepository"       => $entityManager->getRepository(\App\Entity\Principal\Item::class),
            ]
        );
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
        $parametros[ConstanteParametros::CHAVE_FRANQUEADA] = $parametros[ConstanteParametros::CHAVE_FILTRO_FRANQUEADA];
        unset($parametros[ConstanteParametros::CHAVE_FILTRO_FRANQUEADA]);
        if (self::verificaFranqueadaExisteBO($parametros, $mensagemErro, $parametros[ConstanteParametros::CHAVE_FRANQUEADA]) === true) {
            if (is_object($parametros[ConstanteParametros::CHAVE_ITEM]) === true) {
                return true;
            } else if (self::verificaItemExisteBO($parametros, $mensagemErro, $parametros[ConstanteParametros::CHAVE_ITEM]) === true) {
                if (is_null($parametros[ConstanteParametros::CHAVE_VALOR_VENDA]) === false) {
                    return true;
                } else {
                    $mensagemErro = "Valor de venda não pode ser vazio";
                }
            }
        }

        return false;
    }


}
