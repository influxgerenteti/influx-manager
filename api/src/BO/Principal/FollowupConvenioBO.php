<?php
namespace App\BO\Principal;

use App\Helper\ConstanteParametros;
use Doctrine\ORM\EntityManagerInterface;

/**
 *
 * @author Luiz Antonio Costa
 */
class FollowupConvenioBO extends GenericBO
{


    function __construct(EntityManagerInterface $entityManager)
    {
        parent::configuraGenericBO(
            [
                "tipoContatoRepository" => $entityManager->getRepository(\App\Entity\Principal\TipoContato::class),
                "usuarioRepository"     => $entityManager->getRepository(\App\Entity\Principal\Usuario::class),
                "convenioRepository"    => $entityManager->getRepository(\App\Entity\Principal\Convenio::class),
            ]
        );
    }
    /**
     * Verifica os parametros opcionais que são relacionais e não obrigatorios
     *
     * @param array $parametros
     * @param string $mensagemErro
     *
     * @return boolean
     */
    protected function verificaParametrosRelacionaisOpcionais(&$parametros, &$mensagemErro)
    {
        $bRetornaTipoContato = true;

        if ((isset($parametros[ConstanteParametros::CHAVE_TIPO_CONTATO]) === true)&&(empty($parametros[ConstanteParametros::CHAVE_TIPO_CONTATO]) === false)) {
            $bRetornaTipoContato = self::verificaTipoContatoExisteBO($parametros, $mensagemErro, $parametros[ConstanteParametros::CHAVE_TIPO_CONTATO]);
        }

        return $bRetornaTipoContato;
    }

    /**
     * Verifica os parametros relacionais obrigatorios
     *
     * @param array $parametros
     * @param string $mensagemErro
     *
     * @return boolean
     */
    protected function verificaParametrosRelacionaisObrigatorio(&$parametros, &$mensagemErro)
    {
        if (self::verificaConvenioExisteBO($parametros, $mensagemErro, $parametros[ConstanteParametros::CHAVE_CONVENIO]) === true) {
            if (self::verificaUsuarioExisteBO($parametros, $mensagemErro, $parametros[ConstanteParametros::CHAVE_USUARIO]) === true) {
                return true;
            }
        }

        return false;
    }

    /**
     * Executa as regras para poder criar o registro
     *
     * @param array $parametros
     * @param string $mensagemErro
     *
     * @return boolean
     */
    public function podeCriar(&$parametros, &$mensagemErro)
    {
        if ($this->verificaParametrosRelacionaisObrigatorio($parametros, $mensagemErro) === true) {
            if ($this->verificaParametrosRelacionaisOpcionais($parametros, $mensagemErro) === true) {
                return true;
            }
        }

        return false;
    }

    /**
     * Verifica se o followup comercial existe
     *
     * @param \App\Repository\Principal\FollowupConvenioRepository $followupConvenioRepository
     * @param int $id
     * @param string $mensagemErro
     * @param NULL|\App\Entity\Principal\FollowupConvenio $retornoORM
     *
     * @return boolean
     */
    public static function verificaFollowupConvenioExiste(\App\Repository\Principal\FollowupConvenioRepository $followupConvenioRepository, $id, &$mensagemErro, &$retornoORM=null)
    {
        $retornoORM = $followupConvenioRepository->find($id);
        if (is_null($retornoORM) === true) {
            $mensagemErro .= "FollowupConvenio não encontrado.";
            return false;
        }

        return true;
    }


}
