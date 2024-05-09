<?php
namespace App\BO\Principal;

use Doctrine\ORM\EntityManagerInterface;
use App\Helper\ConstanteParametros;


/**
 *
 * @author Dayan Freitas
 */
class TipoOcorrenciaBO extends GenericBO
{

    function __construct(EntityManagerInterface $entityManager)
    {
        parent::configuraGenericBO(
            [
                "franqueadaRepository" => $entityManager->getRepository(\App\Entity\Principal\Franqueada::class),
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
        if (self::verificaFranqueadaExisteBO($parametros, $mensagemErro, $parametros[ConstanteParametros::CHAVE_FRANQUEADA]) === true) {
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
    public function podeAlterar($mensagemErro, $id, $parametros)
    {
        if ($this->verificaParametrosRelacionaisObrigatorios($parametros, $mensagemErro) === true) {
            return true;
        }

        return false;
    }

    /**
     * Verifica se o tipo ocorrencia existe na base
     *
     * @param \App\Repository\Principal\TipoOcorrenciaRepository $tipoOcorrenciaRepository
     * @param int $id
     * @param string $mensagemErro
     * @param NULL|\App\Entity\Principal\TipoOcorrencia $tipoOcorrenciaORM
     *
     * @return boolean
     */
    public static function verificaTipoOcorrenciaExiste(\App\Repository\Principal\TipoOcorrenciaRepository $tipoOcorrenciaRepository, $id, &$mensagemErro, &$tipoOcorrenciaORM=null)
    {

        $tipoOcorrenciaORM = $tipoOcorrenciaRepository->find($id);

        if (is_null($tipoOcorrenciaORM) === true) {
            $mensagemErro = "Tipo Ocorrencia não encontrado na base de dados.";
            return false;
        }

        return true;
    }


}
