<?php
namespace App\BO\Principal;

use App\Helper\ConstanteParametros;
use Doctrine\ORM\EntityManagerInterface;
use DateTime;


/**
 *
 * @author Luiz Antonio Costa
 */
class ReposicaoAulaBO extends GenericBO
{


    function __construct(EntityManagerInterface $entityManager)
    {
        parent::configuraGenericBO(
            [
                "franqueadaRepository"        => $entityManager->getRepository(\App\Entity\Principal\Franqueada::class),
                "turmaRepository"             => $entityManager->getRepository(\App\Entity\Principal\Turma::class),
                "livroRepository"             => $entityManager->getRepository(\App\Entity\Principal\Livro::class),
                "itemRepository"              => $entityManager->getRepository(\App\Entity\Principal\Item::class),
                "licaoRepository"             => $entityManager->getRepository(\App\Entity\Principal\Licao::class),
                "salaFranqueadaRepository"    => $entityManager->getRepository(\App\Entity\Principal\SalaFranqueada::class),
                "usuarioRepository"           => $entityManager->getRepository(\App\Entity\Principal\Usuario::class),
                "funcionarioRepository"       => $entityManager->getRepository(\App\Entity\Principal\Funcionario::class),
                "formaPagamentoRepository"    => $entityManager->getRepository(\App\Entity\Principal\FormaPagamento::class),
                "alunoRepository"             => $entityManager->getRepository(\App\Entity\Principal\Aluno::class),
                "alunoDiarioRepository"       => $entityManager->getRepository(\App\Entity\Principal\AlunoDiario::class),
                "alunoAvaliacaoRepository"    => $entityManager->getRepository(\App\Entity\Principal\AlunoAvaliacao::class),
                "conceitoAvaliacaoRepository" => $entityManager->getRepository(\App\Entity\Principal\ConceitoAvaliacao::class),
            ]
        );
    }

    /**
     * Verifica os relacionamentos obrigatorios para criação/atualização do registro
     *
     * @param array $parametros
     * @param string $mensagemErro
     *
     * @return boolean
     */
    protected function verificaRelacionamentosObrigatorios(&$parametros, &$mensagemErro)
    {
        if (self::verificaFranqueadaExisteBO($parametros, $mensagemErro, $parametros[ConstanteParametros::CHAVE_FRANQUEADA]) === true) {
            if (self::verificaItemExisteBO($parametros, $mensagemErro, $parametros[ConstanteParametros::CHAVE_ITEM]) === true) {
                if (self::verificaUsuarioExisteBO([ConstanteParametros::CHAVE_USUARIO => $parametros[ConstanteParametros::CHAVE_USUARIO_SOLICITANTE]], $mensagemErro, $parametros[ConstanteParametros::CHAVE_USUARIO_SOLICITANTE]) === true) {
                    if (self::verificaFuncionarioExisteBO($parametros, $mensagemErro, ConstanteParametros::CHAVE_RESPONSAVEL_EXECUCAO, $parametros[ConstanteParametros::CHAVE_RESPONSAVEL_EXECUCAO]) === true) {
                        if (self::verificaTurmaExisteBO($parametros, $mensagemErro, $parametros[ConstanteParametros::CHAVE_TURMA]) === true) {
                            if (self::verificaLivroExisteBO($parametros, $mensagemErro, $parametros[ConstanteParametros::CHAVE_LIVRO]) === true) {
                                if (self::verificaLicaoExisteBO($parametros, $mensagemErro, $parametros[ConstanteParametros::CHAVE_LICAO]) === true) {
                                    if (self::verificaAlunoExisteBO($parametros, $mensagemErro, $parametros[ConstanteParametros::CHAVE_ALUNO], true) === true) {
                                        return true;
                                    }
                                }
                            }
                        }
                    }//end if
                }//end if
            }//end if
        }//end if

        return false;
    }

    /**
     * Verifica as notas conceituais
     *
     * @param array $parametros
     * @param string $mensagemErro
     *
     * @return boolean
     */
     private function verificarNotasConceitual(&$parametros, &$mensagemErro)
     {
        $bRetornoAvaliacaoConceitual = true;

        if ((isset($parametros[ConstanteParametros::CHAVE_NOTA_MID_TERM_ORAL]) === true)&&(empty($parametros[ConstanteParametros::CHAVE_NOTA_MID_TERM_ORAL]) === false)) {
            $bRetornoAvaliacaoConceitual = self::verificaConceitoAvaliacao($parametros, $mensagemErro, ConstanteParametros::CHAVE_NOTA_MID_TERM_ORAL, $parametros[ConstanteParametros::CHAVE_NOTA_MID_TERM_ORAL]);
        } else if ((isset($parametros[ConstanteParametros::CHAVE_NOTA_FINAL_ORAL]) === true)&&(empty($parametros[ConstanteParametros::CHAVE_NOTA_FINAL_ORAL]) === false)) {
            $bRetornoAvaliacaoConceitual = self::verificaConceitoAvaliacao($parametros, $mensagemErro, ConstanteParametros::CHAVE_NOTA_FINAL_ORAL, $parametros[ConstanteParametros::CHAVE_NOTA_FINAL_ORAL]);
        } else if ((isset($parametros[ConstanteParametros::CHAVE_NOTA_RETAKE_MID_TERM_ORAL]) === true)&&(empty($parametros[ConstanteParametros::CHAVE_NOTA_RETAKE_MID_TERM_ORAL]) === false)) {
            $bRetornoAvaliacaoConceitual = self::verificaConceitoAvaliacao($parametros, $mensagemErro, ConstanteParametros::CHAVE_NOTA_RETAKE_MID_TERM_ORAL, $parametros[ConstanteParametros::CHAVE_NOTA_RETAKE_MID_TERM_ORAL]);
        } else if ((isset($parametros[ConstanteParametros::CHAVE_NOTA_RETAKE_FINAL_ORAL]) === true)&&(empty($parametros[ConstanteParametros::CHAVE_NOTA_RETAKE_FINAL_ORAL]) === false)) {
            $bRetornoAvaliacaoConceitual = self::verificaConceitoAvaliacao($parametros, $mensagemErro, ConstanteParametros::CHAVE_NOTA_RETAKE_FINAL_ORAL, $parametros[ConstanteParametros::CHAVE_NOTA_RETAKE_FINAL_ORAL]);
        }

        return $bRetornoAvaliacaoConceitual;
     }

    /**
     * Verifica se as data e os horarios de inicio e fim estão corretos
     *
     * @param array $parametros
     * @param string $mensagemErro
     *
     * @return boolean
     */
    protected function aplicaDataInicioTermino(&$parametros, &$mensagemErro)
    {
        $dataSelecionada = $parametros[ConstanteParametros::CHAVE_DATA];
        \App\Helper\FunctionHelper::formataCampoDateTimeJS($parametros[ConstanteParametros::CHAVE_HORA_INICIO], $horarioInicio);
        $horarioInicio = $horarioInicio->format("H:i");
        if (self::converteData($dataSelecionada, $mensagemErro) === true) {
            $arrHorarioInicio = explode(":", $horarioInicio);
            $objetoData = new DateTime($dataSelecionada);
            $dataInicio       = clone $objetoData;
            $dataFinal        = clone $objetoData;
            $dataInicio->setTime($arrHorarioInicio[0], $arrHorarioInicio[1], 0);
            $dataFinal->setTime(($arrHorarioInicio[0] + 1), $arrHorarioInicio[1], 0);
            if (self::verificaDataFinalMaiorDataInicio($dataFinal, $dataInicio) === false) {
                $mensagemErro .= "O horario de término, precisa ser maior que o horario de inicio.";
            } else {
                unset($parametros[ConstanteParametros::CHAVE_DATA]);
                unset($parametros[ConstanteParametros::CHAVE_HORA_INICIO]);
                unset($parametros[ConstanteParametros::CHAVE_HORA_FINAL]);
                $parametros[ConstanteParametros::CHAVE_DATA_HORA_INICIO] = $dataInicio;
                $parametros[ConstanteParametros::CHAVE_DATA_HORA_FIM]    = $dataFinal;
                return true;
            }
        }

        return false;
    }

    /**
     * Verifica os parametros não obrigatórios
     *
     * @param array $parametros
     * @param string $mensagemErro
     *
     * @return boolean
     */
    protected function verificaRelacionamentosOpcionais(&$parametros, &$mensagemErro)
    {
        $bRetornoFormaCobranca       = true;
        $bRetornoAlunoAvaliacao      = true;
        $bRetornoAlunoDiario         = true;
        $bRetornoSalaFranqueada      = true;
        $bRetornoAvaliacaoConceitual = self::verificarNotasConceitual($parametros, $mensagemErro);

        if ((isset($parametros[ConstanteParametros::CHAVE_FORMA_COBRANCA]) === true) && (empty($parametros[ConstanteParametros::CHAVE_FORMA_COBRANCA]) === false)) {
            $bRetornoFormaCobranca = self::verificaFormaPagamentoExisteBO($parametros, $mensagemErro, $parametros[ConstanteParametros::CHAVE_FORMA_COBRANCA], ConstanteParametros::CHAVE_FORMA_COBRANCA);
        }

        if ((isset($parametros[ConstanteParametros::CHAVE_ALUNO_AVALIACAO]) === true) && (empty($parametros[ConstanteParametros::CHAVE_ALUNO_AVALIACAO]) === false)) {
            $bRetornoAlunoAvaliacao = self::verificaAlunoAvaliacaoBO($parametros, $mensagemErro, $parametros[ConstanteParametros::CHAVE_ALUNO_AVALIACAO]);
        }

        if ((isset($parametros[ConstanteParametros::CHAVE_ALUNO_DIARIO]) === true) && (empty($parametros[ConstanteParametros::CHAVE_ALUNO_DIARIO]) === false)) {
            $bRetornoAlunoDiario = self::verificaAlunoDiarioBO($parametros, $mensagemErro, $parametros[ConstanteParametros::CHAVE_ALUNO_DIARIO]);
        }

        if ((isset($parametros[ConstanteParametros::CHAVE_SALA_FRANQUEADA]) === true) && (empty($parametros[ConstanteParametros::CHAVE_SALA_FRANQUEADA]) === false)) {
            $bRetornoSalaFranqueada = self::verificaSalaFranqueadaExisteBO($parametros, $mensagemErro, $parametros[ConstanteParametros::CHAVE_SALA_FRANQUEADA]);
        }

        return $bRetornoFormaCobranca && $bRetornoAlunoAvaliacao && $bRetornoAlunoDiario && $bRetornoSalaFranqueada && $bRetornoAvaliacaoConceitual;
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
            if ($this->verificaRelacionamentosOpcionais($parametros, $mensagemErro) === true) {
                if ($this->aplicaDataInicioTermino($parametros, $mensagemErro) === true) {
                    return true;
                }
            }
        }

        return false;
    }

    /**
     * Verifica se existe a AtividadeExtra no banco de dados, se existir, ele retorna no ultimo parametro na funcao, caso contrario, ele preenchera a msg de erro e retornara falso
     *
     * @param \App\Repository\Principal\AtividadeExtraRepository $atividadeExtraRepository Repositorio da AtividadeExtra
     * @param integer $id Chave primaria da AtividadeExtra
     * @param string $mensagemErro Msg de erro para ser retornada ao front-end
     * @param \App\Entity\Principal\AtividadeExtra|null $atividadeExtraORM Retorno no caso de sucesso
     *
     * @return boolean true|false
     */
    public static function verificaAtividadeExtraExiste(\App\Repository\Principal\AtividadeExtraRepository $atividadeExtraRepository, $id, &$mensagemErro, &$atividadeExtraORM)
    {
        $atividadeExtraORM = $atividadeExtraRepository->find($id);
        if (is_null($atividadeExtraORM) === true) {
            $mensagemErro = "AtividadeExtra não encontrada na base de dados.";
            return false;
        }

        return true;
    }


}
