<?php
namespace App\BO\Principal;

use App\Helper\ConstanteParametros;
use Doctrine\ORM\EntityManagerInterface;

/**
 *
 * @author Luiz Antonio Costa
 */
class ConvidadoAtividadeExtraBO extends GenericBO
{


    function __construct(EntityManagerInterface $entityManager)
    {
        parent::configuraGenericBO(
            [
                "atividadeExtraRepository" => $entityManager->getRepository(\App\Entity\Principal\AtividadeExtra::class),
                "franqueadaRepository"     => $entityManager->getRepository(\App\Entity\Principal\Franqueada::class),
            ]
        );
    }

    /**
     * Verifica se os relacionamentos obrigatorios existem
     *
     * @param array $parametros
     * @param string $mensagemErro
     *
     * @return boolean
     */
    protected function verificaRelacionamentosObrigatorios(&$parametros, &$mensagemErro)
    {
        if (self::verificaFranqueadaExisteBO($parametros, $mensagemErro, $parametros[ConstanteParametros::CHAVE_FRANQUEADA]) === true) {
            if (is_object($parametros[ConstanteParametros::CHAVE_ATIVIDADE_EXTRA] === false) === true) {
                if (self::verificaAtividadeExtraExisteBO($parametros, $mensagemErro, $parametros[ConstanteParametros::CHAVE_ATIVIDADE_EXTRA]) === true) {
                    return true;
                }
            } else {
                return true;
            }
        }

        return false;
    }

    /**
     * Verifica as regras de relacionamentos obrigatórios para poder criar o registro
     *
     * @param array $parametros
     * @param string $mensagemErro
     *
     * @return boolean
     */
    public function podeCriar(&$parametros, &$mensagemErro)
    {
        if ($this->verificaRelacionamentosObrigatorios($parametros, $mensagemErro) === true) {
            return true;
        }

        return false;
    }


}
