<?php
namespace App\BO\Principal;

use Doctrine\ORM\EntityManagerInterface;
use App\Helper\ConstanteParametros;

class ModeloTemplateFranqueadaBO extends GenericBO
{


    function __construct(EntityManagerInterface $entityManager)
    {
        parent::configuraGenericBO(
            [
                "franqueadaRepository"     => $entityManager->getRepository(\App\Entity\Principal\Franqueada::class),
                "modeloTemplateRepository" => $entityManager->getRepository(\App\Entity\Principal\ModeloTemplate::class),
            ]
        );
    }

    /**
     * Verifica os campos de relacionamentos que são obrigatorios
     *
     * @param array $parametros
     * @param string $mensagemErro
     *
     * @return boolean
     */
    protected function verificaCamposRelacionaisObrigatorios(&$parametros, &$mensagemErro)
    {
        if (self::verificaFranqueadaExisteBO($parametros, $mensagemErro, $parametros[ConstanteParametros::CHAVE_FRANQUEADA]) === false) {
            return false;
        }

        if (self::verificaModeloTemplateExisteBO($parametros, $mensagemErro, $parametros[ConstanteParametros::CHAVE_MODELO_TEMPLATE]) === false) {
            return false;
        }

        return true;

    }

    /**
     * Verifica as regras para verificar se pode salvar ou nao
     *
     * @param array $parametros
     * @param string $mensagemErro
     *
     * @return boolean
     */
    public function podeSalvar(&$parametros, &$mensagemErro)
    {
        if ($this->verificaCamposRelacionaisObrigatorios($parametros, $mensagemErro) === false) {
            return false;
        }

        return true;
    }


}
