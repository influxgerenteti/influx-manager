<?php
namespace App\BO\Principal;

use App\Helper\ConstanteParametros;
use Doctrine\ORM\EntityManagerInterface;

/**
 *
 * @author Luiz Antonio Costa
 */
class FollowupComercialBO extends GenericBO
{


    function __construct(EntityManagerInterface $entityManager)
    {
        parent::configuraGenericBO(
            [
                "tipoContatoRepository"     => $entityManager->getRepository(\App\Entity\Principal\TipoContato::class),
                "usuarioRepository"         => $entityManager->getRepository(\App\Entity\Principal\Usuario::class),
                "interessadoRepository"     => $entityManager->getRepository(\App\Entity\Principal\Interessado::class),
                "agendaComercialRepository" => $entityManager->getRepository(\App\Entity\Principal\AgendaComercial::class),
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
        $bRetornaTipoContato     = true;
        $bRetornaAgendaComercial = true;

        if ((isset($parametros[ConstanteParametros::CHAVE_TIPO_CONTATO]) === true)&&(empty($parametros[ConstanteParametros::CHAVE_TIPO_CONTATO]) === false)) {
            $bRetornaTipoContato = self::verificaTipoContatoExisteBO($parametros, $mensagemErro, $parametros[ConstanteParametros::CHAVE_TIPO_CONTATO]);
        }

        if ((isset($parametros[ConstanteParametros::CHAVE_AGENDA_COMERCIAL]) === true)&&(empty($parametros[ConstanteParametros::CHAVE_AGENDA_COMERCIAL]) === false)) {
            $bRetornaAgendaComercial = self::verificaAgendaComercialExisteBO($parametros, $mensagemErro, $parametros[ConstanteParametros::CHAVE_AGENDA_COMERCIAL], true);
        }

        return ($bRetornaTipoContato && $bRetornaAgendaComercial);
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
        if (self::verificaInteressadoExisteBO($parametros, $mensagemErro, $parametros[ConstanteParametros::CHAVE_INTERESSADO]) === true) {
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
     * @param \App\Repository\Principal\FollowupComercialRepository $followupComercialRepository
     * @param int $id
     * @param string $mensagemErro
     * @param NULL|\App\Entity\Principal\FollowupComercial $retornoORM
     *
     * @return boolean
     */
    public static function verificaFollowupComercialExiste(\App\Repository\Principal\FollowupComercialRepository $followupComercialRepository, $id, &$mensagemErro, &$retornoORM=null)
    {
        $retornoORM = $followupComercialRepository->find($id);
        if (is_null($retornoORM) === true) {
            $mensagemErro .= "FollowupComercial não encontrado.";
            return false;
        }

        return true;
    }


}
