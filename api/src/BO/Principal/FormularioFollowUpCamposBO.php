<?php
namespace App\BO\Principal;

use Doctrine\ORM\EntityManagerInterface;
use App\Helper\ConstanteParametros;

/**
 *
 * @author Luiz Antonio Costa
 */
class FormularioFollowUpCamposBO extends GenericBO
{


    function __construct(EntityManagerInterface $entityManager)
    {
        parent::configuraGenericBO(
            [
                "formularioFollowUpCamposRepository" => $entityManager->getRepository(\App\Entity\Principal\FormularioFollowUpCampos::class),
                "formularioFollowUpRepository"       => $entityManager->getRepository(\App\Entity\Principal\FormularioFollowUp::class),
            ]
        );
    }

    /**
     * Verifica os parametros relacionamentos obrigatorios para prosseguir
     *
     * @param array $parametros
     * @param string $mensagemErro
     *
     * @return boolean
     */
    protected function verificaCamposRelacionaisObrigatorios(&$parametros, &$mensagemErro)
    {
        if (self::verificaFormularioFollowUpExisteBO($parametros, $mensagemErro, $parametros[ConstanteParametros::CHAVE_FORMULARIO_FOLLOW_UP]) === true) {
            return true;
        }

        return false;
    }

    /**
     * Verifica as regras para poder realizar a criação do registro
     *
     * @param array $parametros
     * @param string $mensagemErro
     *
     * @return boolean
     */
    public function podeCriar(&$parametros, &$mensagemErro)
    {
        if ($this->verificaCamposRelacionaisObrigatorios($parametros, $mensagemErro) === true) {
            return true;
        }

        return false;
    }

    /**
     * Verifica as regras para poder realizar a atualização do registro
     *
     * @param array $parametros
     * @param string $mensagemErro
     *
     * @return boolean
     */
    public function podeAtualizar(&$parametros, &$mensagemErro)
    {
        if ($this->verificaCamposRelacionaisObrigatorios($parametros, $mensagemErro) === true) {
            return true;
        }

        return false;
    }

    /**
     * Verifica se existe existe um registro de FormularioFollowUpCampos no banco de dados com a id informado
     *
     * @param \App\Repository\Principal\FormularioFollowUpCamposRepository $formularioFollowUpCamposRepositoy Repositorio do formulario follow up campos
     * @param integer $id Chave primaria do FormularioFollowUpCampos
     * @param string $mensagemErro Msg de erro para ser retornada ao front-end
     * @param \App\Entity\Principal\FormularioFollowUpCampos|null $formularioFollowUpCamposORM Entidade
     *
     * @return boolean true|false
     */
    public static function verificaFormularioFollowUpCamposExiste(\App\Repository\Principal\FormularioFollowUpCamposRepository $formularioFollowUpCamposRepositoy, $id, &$mensagemErro, &$formularioFollowUpCamposORM)
    {
        $formularioFollowUpCamposORM = $formularioFollowUpCamposRepositoy->find($id);
        if (is_null($formularioFollowUpCamposORM) === true) {
            $mensagemErro = "FormularioFollowUpCampos não encontrado na base de dados.";
            return false;
        }

        return true;
    }


}
