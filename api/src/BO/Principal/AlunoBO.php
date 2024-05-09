<?php
namespace App\BO\Principal;

use App\Helper\ConstanteParametros;
use App\Helper\SituacoesSistema;
use Doctrine\ORM\EntityManagerInterface;

/**
 *
 * @author Rodrigo de Souza Fernandes (GATI labs)
 */
class AlunoBO extends GenericBO
{


    function __construct(EntityManagerInterface $entityManager)
    {
        parent::configuraGenericBO(
            [
                "classificacaoAlunoRepository"  => $entityManager->getRepository(\App\Entity\Principal\ClassificacaoAluno::class),
                "pessoaRepository"              => $entityManager->getRepository(\App\Entity\Principal\Pessoa::class),
                "escolaridadeRepository"        => $entityManager->getRepository(\App\Entity\Principal\Escolaridade::class),
                "relacionamentoAlunoRepository" => $entityManager->getRepository(\App\Entity\Principal\RelacionamentoAluno::class),
                "interessadoRepository"         => $entityManager->getRepository(\App\Entity\Principal\Interessado::class),
                "turmaRepository"               => $entityManager->getRepository(\App\Entity\Principal\Turma::class),
                "tipoVisibilidadeRepository"    => $entityManager->getRepository(\App\Entity\Principal\TipoVisibilidade::class),
                "midiaRepository"               => $entityManager->getRepository(\App\Entity\Principal\Midia::class),
            ]
        );
    }

    /**
     * Realiza a verificacao de midias
     *
     * @param array $parametros Ponteiro do array para realizar a formatacao
     * @param string $mensagemErro Mensagem de erro a retornar pro front-end
     *
     * @return boolean
     */
    private function verificaTipoMidiasExistem (&$parametros, &$mensagemErro)
    {
        $retornoMidia = true;

        foreach ($parametros[ConstanteParametros::CHAVE_TIPO_VISIBILIDADE] as $key => $itemMidia) {
            $midiaORM = null;
            $parametros[ConstanteParametros::CHAVE_MIDIA] = $itemMidia;

            if ($this->verificaMidiaExisteBO($parametros, $mensagemErro, $midiaORM) === true) {
                $parametros[ConstanteParametros::CHAVE_TIPO_VISIBILIDADE][$key] = $midiaORM;
            } else {
                $retornoMidia = false;
            }
        }

        return $retornoMidia;
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
        if (self::verificaClassificacaoAlunoExisteBO($parametros, $mensagemErro, $parametros[ConstanteParametros::CHAVE_CLASSIFICACAO_ALUNO]) !== true) {
            return false;
        }

        if (self::verificaPessoaExisteBO($parametros, $mensagemErro, $parametros[ConstanteParametros::CHAVE_PESSOA], ConstanteParametros::CHAVE_PESSOA, true) !== true) {
            return false;
        }

        if ($this->verificaEscolaridadeOpcional($parametros, $mensagemErro) !== true) {
            return false;
        }

        if ($this->verificaResponsavelFinanceiroOpcional($parametros, $mensagemErro) !== true) {
            return false;
        }

        if ($this->verificaResponsavelDidaticoOpcional($parametros, $mensagemErro) !== true) {
            return false;
        }

        if ($this->verificaInteressadoOpcional($parametros, $mensagemErro) !== true) {
            return false;
        }

        if ($this->verificaTipoMidiasExistem($parametros, $mensagemErro) !== true) {
            return false;
        }

        return true;
    }

    /**
     * Realiza a verificacao das regras para permitir ou nao a criacao do registro
     *
     * @param array $parametros Ponteiro do array para realizar a formatacao
     * @param string $mensagemErro Mensagem de erro a retornar pro front-end
     * @param \App\Entity\Principal\Aluno $alunoORM
     *
     * @return boolean
     */
    public function podeAtualizar(&$parametros, &$mensagemErro, $alunoORM=null)
    {
        if (self::verificaClassificacaoAlunoExisteBO($parametros, $mensagemErro, $parametros[ConstanteParametros::CHAVE_CLASSIFICACAO_ALUNO]) !== true) {
            return false;
        }

        if ($this->verificaEscolaridadeOpcional($parametros, $mensagemErro) !== true) {
            return false;
        }

        if ($this->verificaResponsavelFinanceiroOpcional($parametros, $mensagemErro, $alunoORM) !== true) {
            return false;
        }

        if ($this->verificaResponsavelDidaticoOpcional($parametros, $mensagemErro, $alunoORM) !== true) {
            return false;
        }

        if ($this->verificaInteressadoOpcional($parametros, $mensagemErro) !== true) {
            return false;
        }

        if ($this->verificaTipoMidiasExistem($parametros, $mensagemErro) !== true) {
            return false;
        }

        return true;
    }

    /**
     * Verifica se o aluno é maior de idade
     *
     * @param boolean $parametros
     * @param string $mensagemErro
     * @param \App\Entity\Principal\Aluno $alunoORM
     *
     * @return boolean
     */
    protected function verificaAlunoMaiorIdade(&$parametros, &$mensagemErro, $alunoORM=null)
    {
        $bRetorno = true;
        if ((bool) $parametros[ConstanteParametros::CHAVE_EMANCIPADO] === false) {
            $dataAtual = new \DateTime();
            if (is_null($alunoORM) === false) {
                $dataNascimento = $alunoORM->getPessoa()->getDataNascimento();
            } else {
                $dataNascimento = $parametros[ConstanteParametros::CHAVE_PESSOA]->getDataNascimento();
            }

            $intervalo      = $dataAtual->diff($dataNascimento);
            $idadeInformada = (int) $intervalo->format("%y");
            if ($idadeInformada < 18) {
                $bRetorno = false;
            }
        }

        return $bRetorno;
    }

    /**
     * Verifica se existe o indice opcional de escolaridade e pesquisa na BO de escolaridade
     *
     * @param array $parametros Ponteiro de array de parametros para realizar a validacao
     * @param string $mensagemErro Mensagem de erro a ser retornado ao front-end
     *
     * @return boolean
     */
    protected function verificaEscolaridadeOpcional(&$parametros, &$mensagemErro)
    {
        $bRetorno = true;
        if ((isset($parametros[ConstanteParametros::CHAVE_ESCOLARIDADE]) === true) && (is_null($parametros[ConstanteParametros::CHAVE_ESCOLARIDADE]) === false)) {
            $bRetorno = self::verificaEscolaridadeExisteBO($parametros, $mensagemErro, $parametros[ConstanteParametros::CHAVE_ESCOLARIDADE]);
        }

        return $bRetorno;
    }

    /**
     * Verifica se existe o indice opcional de interessado e pesquisa na BO de interessado
     *
     * @param array $parametros Ponteiro de array de parametros para realizar a validacao
     * @param string $mensagemErro Mensagem de erro a ser retornado ao front-end
     *
     * @return boolean
     */
    protected function verificaInteressadoOpcional(&$parametros, &$mensagemErro)
    {
        $bRetorno = true;
        if ((isset($parametros[ConstanteParametros::CHAVE_INTERESSADO]) === true) && (is_null($parametros[ConstanteParametros::CHAVE_INTERESSADO]) === false)) {
            $bRetorno = self::verificaInteressadoExisteBO($parametros, $mensagemErro, $parametros[ConstanteParametros::CHAVE_INTERESSADO]);
            if ($bRetorno === true) {
                $parametros[ConstanteParametros::CHAVE_INTERESSADO]->setSituacao(SituacoesSistema::SITUACAO_CONVERTIDO);
            }
        }

        return $bRetorno;
    }

    /**
     * Verifica se existe o indice opcional de pessoa e relacionamentoAluno e pesquisa na BO de pessoa e na BO de Relacionamento Aluno
     *
     * @param array $parametros Ponteiro de array de parametros para realizar a validacao
     * @param string $mensagemErro Mensagem de erro a ser retornado ao front-end
     * @param \App\Entity\Principal\Aluno $alunoORM
     *
     * @return boolean
     */
    protected function verificaResponsavelFinanceiroOpcional(&$parametros, &$mensagemErro, $alunoORM=null)
    {
        $bRetornoRespFinanPessoa = true;
        $bRetornoRespFinanRelacionamentoPessoa = true;
        if ((isset($parametros[ConstanteParametros::CHAVE_RESPONSAVEL_FINANCEIRO_PESSOA]) === true) && (is_null($parametros[ConstanteParametros::CHAVE_RESPONSAVEL_FINANCEIRO_PESSOA]) === false)) {
            $arrParamRespFinanPessoa = [];
                $arrParamRespFinanPessoa[ConstanteParametros::CHAVE_PESSOA] = $parametros[ConstanteParametros::CHAVE_RESPONSAVEL_FINANCEIRO_PESSOA];
                $bRetornoRespFinanPessoa = self::verificaPessoaExisteBO($arrParamRespFinanPessoa, $mensagemErro, $parametros[ConstanteParametros::CHAVE_RESPONSAVEL_FINANCEIRO_PESSOA], ConstanteParametros::CHAVE_PESSOA, true);
                if ($bRetornoRespFinanRelacionamentoPessoa === false) {
                $mensagemErro = "Não foi possivel encontrar o Responsável Financeiro na tabela Pessoa.";
                }
        } else {
            if ($this->verificaAlunoMaiorIdade($parametros, $mensagemErro, $alunoORM) === true) {
                $parametros[ConstanteParametros::CHAVE_RESPONSAVEL_FINANCEIRO_PESSOA] = $parametros[ConstanteParametros::CHAVE_PESSOA];
                $bRetornoRespFinanPessoa = true;
            }
        }

        if ((isset($parametros[ConstanteParametros::CHAVE_RESPONSAVEL_FINANCEIRO_RELACIONAMENTO_ALUNO]) === true) && (is_null($parametros[ConstanteParametros::CHAVE_RESPONSAVEL_FINANCEIRO_RELACIONAMENTO_ALUNO]) === false)) {
            $arrParamRespFinanRelacionamentoAluno = [];
            $arrParamRespFinanRelacionamentoAluno[ConstanteParametros::CHAVE_RELACIONAMENTO_ALUNO] = $parametros[ConstanteParametros::CHAVE_RESPONSAVEL_FINANCEIRO_RELACIONAMENTO_ALUNO];
            $bRetornoRespFinanRelacionamentoPessoa = self::verificaRelacionamentoAlunoExisteBO($arrParamRespFinanRelacionamentoAluno, $mensagemErro, $parametros[ConstanteParametros::CHAVE_RESPONSAVEL_FINANCEIRO_RELACIONAMENTO_ALUNO]);
            if ($bRetornoRespFinanRelacionamentoPessoa === false) {
                $mensagemErro = "Não foi possivel encontrar o Responsável Financeiro na tabela RelacionamentoAluno.";
                $bRetornoRespFinanRelacionamentoPessoa = false;
            }
        }

        return $bRetornoRespFinanPessoa && $bRetornoRespFinanRelacionamentoPessoa;
    }

    /**
     * Verifica se existe o indice opcional de pessoa e relacionamentoAluno e pesquisa na BO de pessoa e na BO de Relacionamento Aluno
     *
     * @param array $parametros Ponteiro de array de parametros para realizar a validacao
     * @param string $mensagemErro Mensagem de erro a ser retornado ao front-end
     * @param \App\Entity\Principal\Aluno $alunoORM
     *
     * @return boolean
     */
    protected function verificaResponsavelDidaticoOpcional(&$parametros, &$mensagemErro, $alunoORM=null)
    {
        $bRetornoRespDidatico = true;
        $bRetornoRespRel      = true;

        if ((isset($parametros[ConstanteParametros::CHAVE_RESPONSAVEL_DIDATICO_PESSOA]) === true) && (is_null($parametros[ConstanteParametros::CHAVE_RESPONSAVEL_DIDATICO_PESSOA]) === false)) {
            $arrParamRespDidaticoPessoa = [];
            $arrParamRespDidaticoPessoa[ConstanteParametros::CHAVE_PESSOA] = $parametros[ConstanteParametros::CHAVE_RESPONSAVEL_DIDATICO_PESSOA];
            $bRetornoRespDidatico = self::verificaPessoaExisteBO($arrParamRespDidaticoPessoa, $mensagemErro, $parametros[ConstanteParametros::CHAVE_RESPONSAVEL_DIDATICO_PESSOA], ConstanteParametros::CHAVE_PESSOA, true);
            if ($bRetornoRespDidatico === false) {
                $mensagemErro = "Não foi possivel encontrar o Responsável Didático na tabela Pessoa.";
            }
        } else {
            if ($this->verificaAlunoMaiorIdade($parametros, $mensagemErro, $alunoORM) === true) {
                $parametros[ConstanteParametros::CHAVE_RESPONSAVEL_DIDATICO_PESSOA] = $parametros[ConstanteParametros::CHAVE_PESSOA];
                $bRetornoRespDidatico = true;
            }
        }

        if ((isset($parametros[ConstanteParametros::CHAVE_RESPONSAVEL_DIDATICO_RELACIONAMENTO_ALUNO]) === true) && (is_null($parametros[ConstanteParametros::CHAVE_RESPONSAVEL_DIDATICO_RELACIONAMENTO_ALUNO]) === false)) {
            $arrParamRespDidaticoRelacionamentoAluno = [];
            $arrParamRespDidaticoRelacionamentoAluno[ConstanteParametros::CHAVE_RELACIONAMENTO_ALUNO] = $parametros[ConstanteParametros::CHAVE_RESPONSAVEL_DIDATICO_RELACIONAMENTO_ALUNO];
            $bRetornoRespRel = self::verificaRelacionamentoAlunoExisteBO($arrParamRespDidaticoRelacionamentoAluno, $mensagemErro, $parametros[ConstanteParametros::CHAVE_RESPONSAVEL_DIDATICO_RELACIONAMENTO_ALUNO]);
            if ($bRetornoRespRel === false) {
                $mensagemErro = "Não foi possivel encontrar o Responsável Didático na tabela RelacionamentoAluno.";
            }
        }

        return $bRetornoRespDidatico && $bRetornoRespRel;
    }

    /**
     * Verifica se conseguirá aplicar a nova turma ao contrato do aluno
     *
     * @param \App\Entity\Principal\Contrato $contratoORM
     * @param string $mensagemErro
     * @param array $parametros
     *
     * @return boolean
     */
    public function aplicaTransferencia(&$contratoORM, &$mensagemErro, $parametros)
    {
        $bRetorno        = false;
        $turmaDestinoId  = $parametros[ConstanteParametros::CHAVE_TURMA_DESTINO];
        $turmaDestinoORM = null;
        if (self::verificaTurmaExisteBO([ConstanteParametros::CHAVE_TURMA => $turmaDestinoId], $mensagemErro, $turmaDestinoORM) === true) {
            if (((bool) $parametros[ConstanteParametros::CHAVE_IGNORA_VALIDACAO_QTD_MAX_ALUNOS] === true) || (TurmaBO::verificaSePodeAdicionarAluno($turmaDestinoORM, $mensagemErro) === true)) {
                if ($turmaDestinoORM->getFranqueada()->getId() === $contratoORM->getFranqueada()->getId()) {
                    $contratoORM->setTurma($turmaDestinoORM);
                    $bRetorno = true;
                } else {
                    $mensagemErro .= "A transferência de turma, só deve ocorrer entre as turmas disponiveis pela propria franqueada.";
                }
            }
        }

        return $bRetorno;
    }

    /**
     * Verifica se existe o aluno no banco de dados, se existir, ele retorna no ultimo parametro na funcao, caso contrario, ele preenchera a msg de erro e retornara falso
     *
     * @param \App\Repository\Principal\AlunoRepository $alunoRepository Repositorio da Categoria
     * @param integer $id Chave primaria da categoria
     * @param string$mensagemErro Msg de erro para ser retornada ao front-end
     * @param \App\Entity\Principal\Aluno|null $alunoORM Retorno no caso de sucesso
     * @param boolean $retornoObjeto Informa a funcao se deve retornar como Objeto ou Array por padrao sera Array
     *
     * @return boolean true|false
     */
    public static function verificaAlunoExiste(\App\Repository\Principal\AlunoRepository $alunoRepository, $id, &$mensagemErro, &$alunoORM, $retornoObjeto=false)
    {
        if ($retornoObjeto === true) {
            $alunoORM = $alunoRepository->find($id);
        } else {
            $alunoORM = $alunoRepository->buscarPorId($id);
        }

        if ($alunoORM === null) {
            $mensagemErro = "Aluno não encontrado na base de dados.";
            return false;
        }

        return true;
    }


}
