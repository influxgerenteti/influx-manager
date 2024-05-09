<?php
namespace App\BO\Principal;

use App\Helper\ConstanteParametros;
use Doctrine\ORM\EntityManagerInterface;

/**
 *
 * @author Dayan Freitas
 */
class OcorrenciaAcademicaDetalhesBO extends GenericBO
{


    function __construct(EntityManagerInterface $entityManager)
    {
        parent::configuraGenericBO(
            [
                "ocorrenciaAcademicaRepository" => $entityManager->getRepository(\App\Entity\Principal\OcorrenciaAcademica::class),
                "funcionarioRepository"         => $entityManager->getRepository(\App\Entity\Principal\Funcionario::class),
            ]
        );
    }
    /**
     * Verifica campos obrigatórios relacionados a entidade
     *
     * @param array $parametros
     * @param string $mensagemErro
     *
     * @return boolean
     */
    protected function verificaCamposRelacionaisObrigatorios(&$parametros, &$mensagemErro)
    {

        if ((is_object($parametros[ConstanteParametros::CHAVE_OCORRENCIA_ACADEMICA]) === false)) {
            if (self::verificaOcorrenciaAcademicaExisteBO($parametros, $mensagemErro, $parametros[ConstanteParametros::CHAVE_OCORRENCIA_ACADEMICA]) === true) {
                if (self::verificaFuncionarioExisteBO($parametros, $mensagemErro, ConstanteParametros::CHAVE_FUNCIONARIO, $parametros[ConstanteParametros::CHAVE_FUNCIONARIO]) === true) {
                    return true;
                }
            }
        } else {
            if (self::verificaFuncionarioExisteBO($parametros, $mensagemErro, ConstanteParametros::CHAVE_FUNCIONARIO, $parametros[ConstanteParametros::CHAVE_FUNCIONARIO]) === true) {
                return true;
            }
        }

        return false;
    }

    /**
     * Realiza a verificacao das regras para permitir ou nao a criacao do registro
     *
     * @param array $parametros Ponteiro do array para realizar a formatacao
     * @param string $mensagemErro Mensagem de erro a retornar pro front-end
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


}
