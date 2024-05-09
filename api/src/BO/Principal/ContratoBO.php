<?php
namespace App\BO\Principal;

use App\Helper\ConstanteParametros;
use App\Helper\SituacoesSistema;
use Doctrine\ORM\EntityManagerInterface;

/**
 *
 * @author Luiz Antonio Costa
 */
class ContratoBO extends GenericBO
{


    function __construct(EntityManagerInterface $entityManager)
    {
        parent::configuraGenericBO(
            [
                "alunoRepository"           => $entityManager->getRepository(\App\Entity\Principal\Aluno::class),
                "turmaRepository"           => $entityManager->getRepository(\App\Entity\Principal\Turma::class),
                "cursoRepository"           => $entityManager->getRepository(\App\Entity\Principal\Curso::class),
                "livroRepository"           => $entityManager->getRepository(\App\Entity\Principal\Livro::class),
                "pessoaRepository"          => $entityManager->getRepository(\App\Entity\Principal\Pessoa::class),
                "franqueadaRepository"      => $entityManager->getRepository(\App\Entity\Principal\Franqueada::class),
                "funcionarioRepository"     => $entityManager->getRepository(\App\Entity\Principal\Funcionario::class),
                "semestreRepository"        => $entityManager->getRepository(\App\Entity\Principal\Semestre::class),
                "convenioRepository"        => $entityManager->getRepository(\App\Entity\Principal\Convenio::class),
                "modalidadeTurmaRepository" => $entityManager->getRepository(\App\Entity\Principal\ModalidadeTurma::class),
            ]
        );
    }

    /**
     * Verifica se todos os responsaveis existem na base de dados
     *
     * @param array $parametros
     * @param string $mensagemErro
     *
     * @return boolean
     */
    protected function verificaResponsaveis(&$parametros, &$mensagemErro)
    {
        if (self::verificaFuncionarioExisteBO($parametros, $mensagemErro, ConstanteParametros::CHAVE_RESPONSAVEL_VENDA_FUNCIONARIO, $parametros[ConstanteParametros::CHAVE_RESPONSAVEL_VENDA_FUNCIONARIO]) === true) {
            if (self::verificaFuncionarioExisteBO($parametros, $mensagemErro, ConstanteParametros::CHAVE_RESPONSAVEL_CARTEIRA_FUNCIONARIO, $parametros[ConstanteParametros::CHAVE_RESPONSAVEL_CARTEIRA_FUNCIONARIO]) === true) {
                if (self::verificaPessoaExisteBO($parametros, $mensagemErro, $parametros[ConstanteParametros::CHAVE_RESPONSAVEL_FINANCEIRO_PESSOA], ConstanteParametros::CHAVE_RESPONSAVEL_FINANCEIRO_PESSOA, true) === true) {
                    return true;
                }
            }
        }

        return false;
    }

    /**
     * Verifica se os parametros de relacionamentos opcionais estao corretos
     *
     * @param array $parametros
     * @param string $mensagemErro
     *
     * @return boolean
     */
    protected function verificaRelacionamentosOpcionais(&$parametros, &$mensagemErro)
    {
        $bTurmaRetorno = true;
        $objetoORM     = null;

        if (isset($parametros[ConstanteParametros::CHAVE_TURMA]) === true) {
            $turmaId = $parametros[ConstanteParametros::CHAVE_TURMA];
            unset($parametros[ConstanteParametros::CHAVE_TURMA]);
            if (empty($turmaId) === false) {
                if (self::verificaTurmaExisteBO([ConstanteParametros::CHAVE_TURMA => $turmaId], $mensagemErro, $objetoORM) === true) {
                    $parametros[ConstanteParametros::CHAVE_TURMA] = $objetoORM;
                } else {
                    $bTurmaRetorno = false;
                }
            }
        }

        if (isset($parametros[ConstanteParametros::CHAVE_CONVENIO_DESCONTO]) === true && is_null($parametros[ConstanteParametros::CHAVE_CONVENIO_DESCONTO]) === false) {
            self::verificaConvenioExisteBO([ConstanteParametros::CHAVE_CONVENIO => $parametros[ConstanteParametros::CHAVE_CONVENIO_DESCONTO]], $mensagemErro, $parametros[ConstanteParametros::CHAVE_CONVENIO_DESCONTO]);
        }

        if ((isset($parametros[ConstanteParametros::CHAVE_CURSO]) === true) && (is_null($parametros[ConstanteParametros::CHAVE_CURSO]) === false)) {
            self::verificaCursoExisteBO($parametros, $mensagemErro, $parametros[ConstanteParametros::CHAVE_CURSO]);
        }

        return $bTurmaRetorno;
    }

    /**
     * Verifica se os parametros de relacionamentos Obrigatorios estao de acordo
     *
     * @param array $parametros
     * @param string $mensagemErro
     * @param boolean $precisaValidarAluno
     *
     * @return boolean
     */
    protected function verificaRelacionamentosObrigatorios(&$parametros, &$mensagemErro, $precisaValidarAluno=false)
    {
        if (self::verificaFranqueadaExisteBO($parametros, $mensagemErro, $parametros[ConstanteParametros::CHAVE_FRANQUEADA]) === true) {
            $modalidadeTurmaORM = self::getModalidadeTurmaRepository()->findOneBy([ConstanteParametros::CHAVE_TIPO => $parametros[ConstanteParametros::CHAVE_MODALIDADE_TURMA]]);
            if (is_null($modalidadeTurmaORM) === false) {
                $parametros[ConstanteParametros::CHAVE_MODALIDADE_TURMA] = $modalidadeTurmaORM;
                if (self::verificaLivroExisteBO($parametros, $mensagemErro, $parametros[ConstanteParametros::CHAVE_LIVRO]) === true) {
                    if (($precisaValidarAluno === true) && (self::verificaAlunoExisteBO($parametros, $mensagemErro, $parametros[ConstanteParametros::CHAVE_ALUNO]) === false)) {
                        return false;
                    }

                    if (self::verificaSemestreExisteBO($parametros, $mensagemErro, $parametros[ConstanteParametros::CHAVE_SEMESTRE]) === true) {
                        if ($this->verificaResponsaveis($parametros, $mensagemErro) === true) {
                            return true;
                        }
                    }
                }
            }
        }

        return false;
    }

    /**
     * Valida se o livro pertence a turma selecionada
     *
     * @param array $parametros
     * @param string $mensagemErro
     *
     * @return boolean
     */
    protected function verificaLivroExisteNaTurmaSelecionada(&$parametros, &$mensagemErro)
    {
        $bRetorno = true;
        if ((isset($parametros[ConstanteParametros::CHAVE_TURMA]) === true)&&(empty($parametros[ConstanteParametros::CHAVE_TURMA]) === false)) {
            $livroORM = $parametros[ConstanteParametros::CHAVE_LIVRO];
            $turmaORM = $parametros[ConstanteParametros::CHAVE_TURMA];

            if ($turmaORM->getLivro()->getId() !== $livroORM->getId()) {
                $mensagemErro = "O livro selecionado não pertence a turma selecionada.";
                $bRetorno     = false;
            }
        }

        return $bRetorno;
    }

    /**
     * Verifica se o curso existe na turma selecionada
     *
     * @param array $parametros
     * @param string $mensagemErro
     *
     * @return boolean
     */
    protected function verificaCursoExisteNaTurmaSelecionada(&$parametros, &$mensagemErro)
    {
        $bRetorno = true;

        if ((isset($parametros[ConstanteParametros::CHAVE_TURMA]) === true)&&(empty($parametros[ConstanteParametros::CHAVE_TURMA]) === false)) {
            $cursoORM = $parametros[ConstanteParametros::CHAVE_CURSO];
            $turmaORM = $parametros[ConstanteParametros::CHAVE_TURMA];

            if ($turmaORM->getCurso()->getId() !== $cursoORM->getId()) {
                $mensagemErro = "O curso selecionado não pertence a turma selecionada.";
                $bRetorno     = false;
            }
        }

        return $bRetorno;
    }

    /**
     * Verifica e o Curso pertence ao livro informado
     *
     * @param \App\Entity\Principal\Curso $cursoObj
     * @param \App\Entity\Principal\Livro $livroObj
     * @param string $mensagemErro
     *
     * @return boolean
     */
    protected function verificaCursoExisteNoLivro($cursoObj, $livroObj, &$mensagemErro)
    {
        if ((is_null($cursoObj) === false) && ($livroObj->getCurso()->contains($cursoObj) === false)) {
            $mensagemErro = "O curso informado, não pertence ao Livro selecionado.";
            return false;
        }

        return true;
    }

    /**
     * Verifica se a data de Termino do contrato é maior que a Data de Inicio
     *
     * @param array $parametros
     * @param string $mensagemErro
     *
     * @return boolean
     */
    protected function verificaDtTerminoContratoMaiorDtInicioContrato(&$parametros, &$mensagemErro)
    {
        $bRetorno = $this->normalizarDataInicioDataTerminoContrato($parametros, $mensagemErro);

        if ($bRetorno === true) {
            if (self::verificaDataFinalMaiorDataInicio($parametros[ConstanteParametros::CHAVE_DATA_TERMINO_CONTRATO], $parametros[ConstanteParametros::CHAVE_DATA_INICIO_CONTRATO]) === false) {
                $mensagemErro = "Não foi possivel prosseguir, pois a data de termino do contrato, precisa ser maior que a data de inicio do contrato.";
                $bRetorno     = false;
            }
        }

        return $bRetorno;
    }

    /**
     * Normaliza as datas de inicioContrato e terminoContrato
     *
     * @param array $parametros
     * @param string $mensagemErro
     *
     * @return boolean
     */
    protected function normalizarDataInicioDataTerminoContrato(&$parametros, &$mensagemErro)
    {
        $bRetorno = true;
        $dataInicioContratoString  = $parametros[ConstanteParametros::CHAVE_DATA_INICIO_CONTRATO];
        $dataTerminoContratoString = $parametros[ConstanteParametros::CHAVE_DATA_TERMINO_CONTRATO];

        if (empty($dataInicioContratoString) === true) {
            $mensagemErro = "Não foi possivel prosseguir, pela ausencia do campo DataInicioContrato.";
            $bRetorno     = false;
        } else if (empty($dataTerminoContratoString) === true) {
            $mensagemErro = "Não foi possivel prosseguir, pela ausencia do campo DataTerminoContrato.";
            $bRetorno     = false;
        }

        if ($bRetorno === true) {
            $dataInicioContratoObj  = false;
            $dataTerminoContratoObj = false;
            \App\Helper\FunctionHelper::formataCampoDateTimeJS($dataInicioContratoString, $dataInicioContratoObj);
            \App\Helper\FunctionHelper::formataCampoDateTimeJS($dataTerminoContratoString, $dataTerminoContratoObj);
            if ($dataInicioContratoObj === false) {
                $mensagemErro = "Não foi possivel prosseguir, pois o formato de data enviado para o campo DataInicioContrato, é invalido.";
                $bRetorno     = false;
            } else if ($dataTerminoContratoObj === false) {
                $mensagemErro = "Não foi possivel prosseguir, pois o formato de data enviado para o campo DataTerminoContrato, é invalido.";
                $bRetorno     = false;
            }

            $parametros[ConstanteParametros::CHAVE_DATA_INICIO_CONTRATO]  = $dataInicioContratoObj;
            $parametros[ConstanteParametros::CHAVE_DATA_TERMINO_CONTRATO] = $dataTerminoContratoObj;
        }

        return $bRetorno;
    }

    /**
     * Realiza a configuracao da data de matricula
     *
     * @param array $parametros
     * @param string $mensagemErro
     *
     * @return boolean
     */
    protected function configuraDataMatricula(&$parametros, &$mensagemErro)
    {
        $bRetorno = true;

        if (isset($parametros[ConstanteParametros::CHAVE_DATA_MATRICULA]) === true) {
            if (empty($parametros[ConstanteParametros::CHAVE_DATA_MATRICULA]) === false) {
                $dataMatriculaObj = false;
                \App\Helper\FunctionHelper::formataCampoDateTimeJS($parametros[ConstanteParametros::CHAVE_DATA_MATRICULA], $dataMatriculaObj);
                if ($dataMatriculaObj === false) {
                    $mensagemErro = "O formato de data para o campo DataMatricula é invalido.";
                    $bRetorno     = false;
                } else {
                    $parametros[ConstanteParametros::CHAVE_DATA_MATRICULA] = $dataMatriculaObj;
                }
            } else {
                $parametros[ConstanteParametros::CHAVE_DATA_MATRICULA] = new \DateTime();
            }
        } else if (is_null($parametros[ConstanteParametros::CHAVE_DATA_MATRICULA]) === true) {
            $parametros[ConstanteParametros::CHAVE_DATA_MATRICULA] = new \DateTime();
        }

        return $bRetorno;
    }

    /**
     * Realiza a verificacao dos dados para poder prosseguir com cadastro do novo contrato
     *
     * @param array $parametros
     * @param string $mensagemErro
     *
     * @return boolean
     */
    public function podeSalvar(&$parametros, &$mensagemErro)
    {
        if ($this->verificaRelacionamentosObrigatorios($parametros, $mensagemErro, true) === false) {
            return false;
        }

        if ($this->verificaRelacionamentosOpcionais($parametros, $mensagemErro) === false) {
            return false;
        }

        if ($this->verificaDtTerminoContratoMaiorDtInicioContrato($parametros, $mensagemErro) === false) {
            return false;
        }

        if ($this->configuraDataMatricula($parametros, $mensagemErro) === false) {
            return false;
        }

        if ($this->verificaCursoExisteNaTurmaSelecionada($parametros, $mensagemErro) === false) {
            return false;
        }

        if ($this->verificaLivroExisteNaTurmaSelecionada($parametros, $mensagemErro) === false) {
            return false;
        }

        if ($this->verificaCursoExisteNoLivro($parametros[ConstanteParametros::CHAVE_CURSO], $parametros[ConstanteParametros::CHAVE_LIVRO], $mensagemErro) === false) {
            return false;
        }

        return true;
    }

    /**
     * Realiza a verificacao dos dados E TRATA OS PARAMETROS para poder prosseguir com a alteracao dos Registros
     *
     * @param array $parametros
     * @param string $mensagemErro
     *
     * @return boolean
     */
    public function podeAlterar(&$parametros, &$mensagemErro)
    {

        if ($this->verificaRelacionamentosObrigatorios($parametros, $mensagemErro) === false) {
            return false;
        }

        if ($this->verificaRelacionamentosOpcionais($parametros, $mensagemErro) === false) {
            return false;
        }

        if ($this->verificaDtTerminoContratoMaiorDtInicioContrato($parametros, $mensagemErro) === false) {
            return false;
        }

        if ($this->configuraDataMatricula($parametros, $mensagemErro) === false) {
            return false;
        }

        /*
         * As funções abaixo, em caso de update, não devem ser usadas pra definir se pode ou não alterar,
         * apenas para tratamento dos parametros
         */

        $this->verificaCursoExisteNaTurmaSelecionada($parametros, $mensagemErro);
        $this->verificaLivroExisteNaTurmaSelecionada($parametros, $mensagemErro);
        $this->verificaCursoExisteNoLivro($parametros[ConstanteParametros::CHAVE_CURSO], $parametros[ConstanteParametros::CHAVE_LIVRO], $mensagemErro);
        return true;
    }

    /**
     * Realiza a busca do contrato e coloca o dado buscado no $resultadoORM
     *
     * @param \App\Repository\Principal\ContratoRepository $contratoRepository
     * @param integer|\App\Entity\Principal\Contrato $id
     * @param string $mensagemErro
     * @param \App\Entity\Principal\Contrato $resultadoORM
     *
     * @return boolean
     */
    public static function verificaContratoExiste(\App\Repository\Principal\ContratoRepository $contratoRepository, $id, &$mensagemErro, &$resultadoORM)
    {
        if ((empty($id) === false) && (is_string($id) === true)) {
            $id = (int) $id;
        }

        if (is_int($id) === true) {
            $resultadoORM = $contratoRepository->find($id);
        } else {
            $resultadoORM = $id;
        }

        if (is_null($resultadoORM) === true) {
            $mensagemErro .= "Contrato não foi encontrado na base de dados.";
            return false;
        }

        return true;
    }



    /**
     * Realiza a verificacao se o contrato passado pode ser encerrado
     *
     * @param string $mensagemErro
     * @param Contrato $contratoORM
     *
     * @return boolean
     */
    public function podeEncerrar(&$mensagemErro, $contratoORM)
    {
        return $contratoORM->getSituacao() === SituacoesSistema::SITUACAO_CONTRATO_VIGENTE;
    }


}
