<?php
namespace App\BO\Principal;

use App\Helper\ConstanteParametros;
use App\Helper\SituacoesSistema;
use Doctrine\ORM\EntityManagerInterface;

/**
 *
 * @author Dayan Freitas
 */
class OcorrenciaAcademicaBO extends GenericBO
{


    function __construct(EntityManagerInterface $entityManager)
    {
        parent::configuraGenericBO(
            [
                "alunoRepository"            => $entityManager->getRepository(\App\Entity\Principal\Aluno::class),
                "contratoRepository"         => $entityManager->getRepository(\App\Entity\Principal\Contrato::class),
                "tipoOcorrenciaRepository"   => $entityManager->getRepository(\App\Entity\Principal\TipoOcorrencia::class),
                "origemOcorrenciaRepository" => $entityManager->getRepository(\App\Entity\Principal\OrigemOcorrencia::class),
                "usuarioRepository"          => $entityManager->getRepository(\App\Entity\Principal\Usuario::class),
                "funcionarioRepository"      => $entityManager->getRepository(\App\Entity\Principal\Funcionario::class),
                "franqueadaRepository"       => $entityManager->getRepository(\App\Entity\Principal\Franqueada::class),
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
        if (self::verificaFranqueadaExisteBO($parametros, $mensagemErro, $parametros[ConstanteParametros::CHAVE_FRANQUEADA]) === true) {
            if (self::verificaAlunoExisteBO($parametros, $mensagemErro, $parametros[ConstanteParametros::CHAVE_ALUNO]) === true) {
                if (self::verificaTipoOcorrenciaExisteBO($parametros, $mensagemErro, $parametros[ConstanteParametros::CHAVE_TIPO_OCORRENCIA]) === true) {
                    if (self::verificaUsuarioExisteBO($parametros, $mensagemErro, $parametros[ConstanteParametros::CHAVE_USUARIO]) === true) {
                        if (self::verificaFuncionarioExisteBO($parametros, $mensagemErro, ConstanteParametros::CHAVE_FUNCIONARIO, $parametros[ConstanteParametros::CHAVE_FUNCIONARIO]) === true) {
                            return true;
                        }
                    }
                }
            }
        }

        return false;
    }

    /**
     * Verifica os campos opcionais
     *
     * @param array $parametros
     * @param string $mensagemErro
     *
     * @return boolean
     */
    protected function verificaCamposRelacionaisOpcionais(&$parametros, &$mensagemErro)
    {
        $bRetornoContrato         = true;
        $bRetornoOrigemOcorrencia = true;

        if ((isset($parametros[ConstanteParametros::CHAVE_CONTRATO]) === true)&&(empty($parametros[ConstanteParametros::CHAVE_CONTRATO]) === false)) {
            $bRetornoContrato = self::verificaContratoExisteBO($parametros, $mensagemErro, $parametros[ConstanteParametros::CHAVE_CONTRATO]);
        }

        if ((isset($parametros[ConstanteParametros::CHAVE_ORIGEM_OCORRENCIA_TIPO]) === true)&&(empty($parametros[ConstanteParametros::CHAVE_ORIGEM_OCORRENCIA_TIPO]) === false)) {
            $bRetornoOrigemOcorrencia = self::verificaOrigemOcorrenciaPorTipoOrigemExiste($parametros, $mensagemErro, $parametros[ConstanteParametros::CHAVE_ORIGEM_OCORRENCIA]);
            unset($parametros[ConstanteParametros::CHAVE_ORIGEM_OCORRENCIA_TIPO]);
        }

        return $bRetornoContrato && $bRetornoOrigemOcorrencia;
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
        if ($parametros[ConstanteParametros::CHAVE_SITUACAO] === SituacoesSistema::OCORRENCIA_ENCERRADA) {
            $parametros[ConstanteParametros::CHAVE_DATA_CONCLUSAO] = new \DateTime();
        }

        if ($this->verificaCamposRelacionaisObrigatorios($parametros, $mensagemErro) === true) {
            if ($this->verificaCamposRelacionaisOpcionais($parametros, $mensagemErro) === true) {
                return true;
            }
        }

        return false;
    }

    /**
     * Verifica se existe a ocorrencia academica no banco de dados, se existir, ele retorna no ultimo parametro na funcao, caso contrario, ele preenchera a msg de erro e retornara falso
     *
     * @param \App\Repository\Principal\OcorrenciaAcademicaRepository $ocorrenciaAcademicaRepository
     * @param integer $id Chave primaria da categoria
     * @param string $mensagemErro Msg de erro para ser retornada ao front-end
     * @param \App\Entity\Principal\OcorrenciaAcademica|null $ocorrenciaAcademicaORM Retorno no caso de sucesso
     *
     * @return boolean true|false
     */
    public static function verificaOcorrenciaAcademicaExiste(\App\Repository\Principal\OcorrenciaAcademicaRepository $ocorrenciaAcademicaRepository, $id, &$mensagemErro, &$ocorrenciaAcademicaORM)
    {
        $ocorrenciaAcademicaORM = $ocorrenciaAcademicaRepository->find($id);
        if (is_null($ocorrenciaAcademicaORM) === true) {
            $mensagemErro = "Ocorrência não encontrada na base de dados.";
            return false;
        }

        return true;
    }


}
