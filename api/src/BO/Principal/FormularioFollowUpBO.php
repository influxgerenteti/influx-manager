<?php
namespace App\BO\Principal;

use Doctrine\ORM\EntityManagerInterface;
use App\Helper\ConstanteParametros;

/**
 *
 * @author Luiz Antonio Costa
 */
class FormularioFollowUpBO extends GenericBO
{


    function __construct(EntityManagerInterface $entityManager)
    {
        parent::configuraGenericBO(
            [
                "formularioFollowUpRepository" => $entityManager->getRepository(\App\Entity\Principal\FormularioFollowUp::class),
                "usuarioRepository"            => $entityManager->getRepository(\App\Entity\Principal\Usuario::class),
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
        if (self::verificaUsuarioExisteBO($parametros, $mensagemErro, $parametros[ConstanteParametros::CHAVE_USUARIO]) === true) {
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
     * Verifica se existe existe um registro de FormularioFollowUp no banco de dados com a id informado
     *
     * @param \App\Repository\Principal\FormularioFollowUpRepository $formularioFollowUpRepository Repositorio do formulario follow up
     * @param integer $id Chave primaria do formulario
     * @param string $mensagemErro Msg de erro para ser retornada ao front-end
     * @param \App\Entity\Principal\FormularioFollowUp|null $formularioFollowUpORM Retorno no caso de sucesso
     *
     * @return boolean true|false
     */
    public static function verificaFormularioFollowUpExiste(\App\Repository\Principal\FormularioFollowUpRepository $formularioFollowUpRepository, $id, &$mensagemErro, &$formularioFollowUpORM)
    {
        $formularioFollowUpORM = $formularioFollowUpRepository->find($id);
        if (is_null($formularioFollowUpORM) === true) {
            $mensagemErro = "FormularioFollowUp não encontrado na base de dados.";
            return false;
        }

        return true;
    }


}
