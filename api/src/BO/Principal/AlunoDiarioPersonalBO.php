<?php
namespace App\BO\Principal;

use App\Helper\ConstanteParametros;
use Doctrine\ORM\EntityManagerInterface;

/**
 *
 * @author Luiz Antonio Costa
 */
class AlunoDiarioPersonalBO extends GenericBO
{


    function __construct(EntityManagerInterface $entityManager)
    {
        parent::configuraGenericBO(
            [
                "franqueadaRepository"          => $entityManager->getRepository(\App\Entity\Principal\Franqueada::class),
                "alunoRepository"               => $entityManager->getRepository(\App\Entity\Principal\Aluno::class),
                "licaoRepository"               => $entityManager->getRepository(\App\Entity\Principal\Licao::class),
                "funcionarioRepository"         => $entityManager->getRepository(\App\Entity\Principal\Funcionario::class),
                "salaFranqueadaRepository"      => $entityManager->getRepository(\App\Entity\Principal\SalaFranqueada::class),
                "livroRepository"               => $entityManager->getRepository(\App\Entity\Principal\Livro::class),
                "agendamentoPersonalRepository" => $entityManager->getRepository(\App\Entity\Principal\AgendamentoPersonal::class),
                "creditosPersonalRepository"    => $entityManager->getRepository(\App\Entity\Principal\CreditosPersonal::class),
            ]
        );
    }

    /**
     * Verifica os relacionamentos que não são obrigatórios
     *
     * @param array $parametros
     * @param string $mensagemErro
     *
     * @return boolean
     */
    protected function verificaRelacionamentoOpcionais(&$parametros, &$mensagemErro)
    {
        $bRetornoLicao = true;

        if ((isset($parametros[ConstanteParametros::CHAVE_LICAOS]) === true)&&(empty($parametros[ConstanteParametros::CHAVE_LICAOS]) === false)&&(is_array($parametros[ConstanteParametros::CHAVE_LICAOS]) === true)) {
            if (count($parametros[ConstanteParametros::CHAVE_LICAOS]) > 0) {
                $listaLicao = [];
                foreach ($parametros[ConstanteParametros::CHAVE_LICAOS] as $licaoId) {
                    $objetoORM     = null;
                    $bRetornoLicao = self::verificaLicaoExisteBO([ConstanteParametros::CHAVE_LICAO => $licaoId], $mensagemErro, $objetoORM);
                    if ($bRetornoLicao === false) {
                        break;
                    }

                    $listaLicao[] = $objetoORM;
                }

                if ($bRetornoLicao !== false) {
                    $parametros[ConstanteParametros::CHAVE_LICAOS] = $listaLicao;
                }
            } else {
                $bRetornoLicao = false;
                $mensagemErro .= "\nSó é aceito array de licao.";
            }
        }//end if

        return $bRetornoLicao;
    }

    /**
     * Verifica se é possivel prosseguir com a criacao do registro com os parametros informados
     *
     * @param array $parametros
     * @param string $mensagemErro
     *
     * @return boolean
     */
    protected function verificaRelacionamentoObrigatorio(&$parametros, &$mensagemErro)
    {
        if (self::verificaFranqueadaExisteBO($parametros, $mensagemErro, $parametros[ConstanteParametros::CHAVE_FRANQUEADA]) === true) {
            if (self::verificaAlunoExisteBO($parametros, $mensagemErro, $parametros[ConstanteParametros::CHAVE_ALUNO], true) === true) {
                if (self::verificaFuncionarioExisteBO($parametros, $mensagemErro, ConstanteParametros::CHAVE_FUNCIONARIO, $parametros[ConstanteParametros::CHAVE_FUNCIONARIO]) === true) {
                    if (self::verificaSalaFranqueadaExisteBO($parametros, $mensagemErro, $parametros[ConstanteParametros::CHAVE_SALA_FRANQUEADA]) === true) {
                        if (self::verificaLivroExisteBO($parametros, $mensagemErro, $parametros[ConstanteParametros::CHAVE_LIVRO]) === true) {
                            if (self::verificaExisteAgendamentoPersonalBO($parametros, $mensagemErro, $parametros[ConstanteParametros::CHAVE_AGENDAMENTO_PERSONAL]) === true) {
                                if (self::verificaCreditosPersonalBO($parametros, $mensagemErro, $parametros[ConstanteParametros::CHAVE_CREDITOS_PERSONAL]) === true) {
                                    return true;
                                }
                            }
                        }
                    }
                }
            }
        }

        return false;
    }

    /**
     * Verifica as regras para criação de dados
     *
     * @param array $parametros
     * @param string $mensagemErro
     *
     * @return boolean
     */
    public function podeCriar(&$parametros, &$mensagemErro)
    {
        if ($this->verificaRelacionamentoObrigatorio($parametros, $mensagemErro) === true) {
            if ($this->verificaRelacionamentoOpcionais($parametros, $mensagemErro) === true) {
                return true;
            }
        }

        return false;
    }

    /**
     * Verifica se existe a AlunoDiarioPersonal no banco de dados, se existir, ele retorna no ultimo parametro na funcao, caso contrario, ele preenchera a msg de erro e retornara falso
     *
     * @param \App\Repository\Principal\AlunoDiarioPersonalRepository $alunoDiarioPersonalRepository Repositorio da AlunoDiarioPersonal
     * @param integer $id Chave primaria da categoria
     * @param string $mensagemErro Msg de erro para ser retornada ao front-end
     * @param \App\Entity\Principal\AlunoDiarioPersonal|null $alunoDiarioPersonalORM Retorno no caso de sucesso
     *
     * @return boolean true|false
     */
    public static function verificaAlunoDiarioExiste(\App\Repository\Principal\AlunoDiarioPersonalRepository $alunoDiarioPersonalRepository, $id, &$mensagemErro, &$alunoDiarioPersonalORM)
    {
        $alunoDiarioPersonalORMORM = $alunoDiarioPersonalRepository->find($id);
        if (is_null($alunoDiarioPersonalORMORM) === true) {
            $mensagemErro = "AlunoDiarioPersonal não encontrado na base de dados.";
            return false;
        }

        return true;
    }


}
