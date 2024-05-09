<?php
namespace App\BO\Principal;

use Doctrine\ORM\EntityManagerInterface;
use App\Helper\ConstanteParametros;
use App\Helper\SituacoesSistema;

/**
 *
 * @author Luiz Antonio Costa
 */
class InteressadoBO extends GenericBO
{
    /**
     *
     * @var \App\Repository\Principal\InteressadoRepository
     */
    private $interessadoRepository;

/**
     *
     * @var \App\Repository\Principal\PessoaRepository
     */
    private $pessoaRepository;

    /**
     *
     * @var \App\BO\Principal\WorkflowBO
     */
    private $workflowBO;

    /**
     *
     * @var \App\BO\Principal\WorkflowAcaoBO
     */
    private $workflowAcaoBO;

    function __construct(EntityManagerInterface $entityManager)
    {
        $this->interessadoRepository = $entityManager->getRepository(\App\Entity\Principal\Interessado::class);
        $this->pessoaRepository      = $entityManager->getRepository(\App\Entity\Principal\Pessoa::class);
        $this->workflowBO            = new WorkflowBO($entityManager);
        $this->workflowAcaoBO        = new WorkflowAcaoBO($entityManager);
        parent::configuraGenericBO(
            [
                "franqueadaRepository"                   => $entityManager->getRepository(\App\Entity\Principal\Franqueada::class),
                "funcionarioRepository"                  => $entityManager->getRepository(\App\Entity\Principal\Funcionario::class),
                "pessoaRepository"                       => $entityManager->getRepository(\App\Entity\Principal\Pessoa::class),
                "workflowRepository"                     => $entityManager->getRepository(\App\Entity\Principal\Workflow::class),
                "workflowAcaoRepository"                 => $entityManager->getRepository(\App\Entity\Principal\WorkflowAcao::class),
                "alunoRepository"                        => $entityManager->getRepository(\App\Entity\Principal\Aluno::class),
                "idiomaRepository"                       => $entityManager->getRepository(\App\Entity\Principal\Idioma::class),
                "motivoNaoFechamentoMatriculaRepository" => $entityManager->getRepository(\App\Entity\Principal\MotivoNaoFechamentoMatricula::class),
                "cursoRepository"                        => $entityManager->getRepository(\App\Entity\Principal\Curso::class),
                "tipoContatoRepository"                  => $entityManager->getRepository(\App\Entity\Principal\TipoContato::class),
                "tipoProspeccaoRepository"               => $entityManager->getRepository(\App\Entity\Principal\TipoProspeccao::class),
            ]
        );
    }

    /**
     * Valida os idiomas e indexa eles em seus respectivos campos
     *
     * @param array $parametros
     * @param string $mensagemErro
     *
     * @return boolean
     */
    protected function validaIdiomas(&$parametros, &$mensagemErro)
    {
        if ((isset($parametros[ConstanteParametros::CHAVE_IDIOMAS]) === true) && (count($parametros[ConstanteParametros::CHAVE_IDIOMAS]) > 0)) {
            $idiomas      = $parametros[ConstanteParametros::CHAVE_IDIOMAS];
            $totalIdiomas = count($idiomas);
            $totalBuscado = 0;
            foreach ($idiomas as $idiomaId) {
                if (self::verificaIdioma([ConstanteParametros::CHAVE_IDIOMA => $idiomaId], $mensagemErro, $parametros[ConstanteParametros::CHAVE_IDIOMAS][$totalBuscado]) === true) {
                    $totalBuscado++;
                }
            }

            if ($totalIdiomas !== $totalBuscado) {
                return false;
            }
        }

        return true;
    }
    /**
     * Valida tipo do contato atraves do tipo lead selecionado
     *
     * @param array $parametros
     * @param string $mensagemErro
     *
     * @return boolean
     */
    protected function validaTipoContato(&$parametros, &$mensagemErro)
    {
        $bRetorno = true;

        if (isset($parametros[ConstanteParametros::CHAVE_TIPO_LEAD]) === true) {
            $bRetorno = false;

            if ($parametros[ConstanteParametros::CHAVE_TIPO_LEAD] === "A") {
                $bRetorno = self::verificaTipoProspeccaoExisteBO($parametros, $mensagemErro, $parametros[ConstanteParametros::CHAVE_TIPO_PROSPECCAO]);
            } else {
                $bRetorno = self::verificaTipoContatoExisteBO($parametros, $mensagemErro, $parametros[ConstanteParametros::CHAVE_TIPO_CONTATO]);
            }
        }

        return $bRetorno;
    }

    /**
     * Configura os parametros obrigatorios para criacao do registro
     *
     * @param array $parametros
     * @param string $mensagemErro
     *
     * @return boolean
     */
    protected function verificaCamposRelacionaisObrigatorios(&$parametros, &$mensagemErro)
    {
        if (self::verificaFranqueadaExisteBO($parametros, $mensagemErro, $parametros[ConstanteParametros::CHAVE_FRANQUEADA]) === true) {
            if ($this->validaIdiomas($parametros, $mensagemErro) === true) {
                if ($this->validaTipoContato($parametros, $mensagemErro) === true) {
                    return true;
                }
            }
        }

        return false;
    }

    /**
     * Verifica os parametros relacionais nao obrigatorios
     *
     * @param array $parametros
     * @param string $mensagemErro
     *
     * @return boolean
     */
    protected function verificaCamposRelacionaisOpcionais(&$parametros, &$mensagemErro)
    {
        $bConsultorFuncionarioRetorno            = true;
        $bPessoaIndicouRetorno            = true;
        $bConsultorResponsavelFuncionarioRetorno = true;
        $bAlunoRetorno = true;
        $bMotivoNaoFechamentoMatricula = true;
        $bWorkflowAcaoRetorno          = true;
        $bCursoRetorno            = true;
        $bDataValidaPromocao      = true;
        $bDataPrimeiroAtendimento = true;
        $parametros[ConstanteParametros::CHAVE_TIPO_WORKFLOW] = null;

        if ((isset($parametros[ConstanteParametros::CHAVE_DATA_VALIDADE_PROMOCAO]) === true)&&(empty($parametros[ConstanteParametros::CHAVE_DATA_VALIDADE_PROMOCAO]) === false)) {
            $dataValidadePromocao = null;
            $bDataValidaPromocao  = \App\Helper\FunctionHelper::formataCampoDateTimeJS($parametros[ConstanteParametros::CHAVE_DATA_VALIDADE_PROMOCAO], $dataValidadePromocao);
            if ($bDataValidaPromocao === false) {
                $mensagemErro .= "Erro ao converter a data de validade promocao para o formato aceito pelo banco de dados.[" . $parametros[ConstanteParametros::CHAVE_DATA_VALIDADE_PROMOCAO] . "]";
            } else {
                $parametros[ConstanteParametros::CHAVE_DATA_VALIDADE_PROMOCAO] = $dataValidadePromocao;
            }
        }

        if ((isset($parametros[ConstanteParametros::CHAVE_DATA_PRIMEIRO_ATENDIMENTO]) === true)&&(empty($parametros[ConstanteParametros::CHAVE_DATA_PRIMEIRO_ATENDIMENTO]) === false)) {
            $dataPrimeiroAtendimento  = null;
            $bDataPrimeiroAtendimento = \App\Helper\FunctionHelper::formataCampoDateTimeJS($parametros[ConstanteParametros::CHAVE_DATA_PRIMEIRO_ATENDIMENTO], $dataPrimeiroAtendimento);
            if ($bDataPrimeiroAtendimento === false) {
                $mensagemErro .= "Erro ao converter a data de primeiro atendimento para o formato aceito pelo banco de dados.[" . $parametros[ConstanteParametros::CHAVE_DATA_PRIMEIRO_ATENDIMENTO] . "]";
            } else {
                $parametros[ConstanteParametros::CHAVE_DATA_PRIMEIRO_ATENDIMENTO] = $dataPrimeiroAtendimento;
            }
        }

        if ((isset($parametros[ConstanteParametros::CHAVE_CURSO]) === true)&&(empty($parametros[ConstanteParametros::CHAVE_CURSO]) === false)) {
            $bCursoRetorno = self::verificaCursoExisteBO($parametros, $mensagemErro, $parametros[ConstanteParametros::CHAVE_CURSO]);
        }

        if ((isset($parametros[ConstanteParametros::CHAVE_CONSULTOR_FUNCIONARIO]) === true)&&(empty($parametros[ConstanteParametros::CHAVE_CONSULTOR_FUNCIONARIO]) === false)) {
            $bConsultorFuncionarioRetorno = self::verificaFuncionarioExisteBO($parametros, $mensagemErro, ConstanteParametros::CHAVE_CONSULTOR_FUNCIONARIO, $parametros[ConstanteParametros::CHAVE_CONSULTOR_FUNCIONARIO]);
        }
        if ((isset($parametros[ConstanteParametros::CHAVE_PESSOA_INDICOU]) === true)&&(empty($parametros[ConstanteParametros::CHAVE_PESSOA_INDICOU]) === false)) {
            $bPessoaIndicouRetorno = self::verificaPessoaExisteBO($parametros, $mensagemErro, $parametros[ConstanteParametros::CHAVE_PESSOA_INDICOU], ConstanteParametros::CHAVE_PESSOA_INDICOU, true);
        }
        
        if ((isset($parametros[ConstanteParametros::CHAVE_CONSULTOR_RESPONSAVEL_FUNCIONARIO]) === true)&&(empty($parametros[ConstanteParametros::CHAVE_CONSULTOR_RESPONSAVEL_FUNCIONARIO]) === false)) {
            $bConsultorResponsavelFuncionarioRetorno = self::verificaFuncionarioExisteBO($parametros, $mensagemErro, ConstanteParametros::CHAVE_CONSULTOR_RESPONSAVEL_FUNCIONARIO, $parametros[ConstanteParametros::CHAVE_CONSULTOR_RESPONSAVEL_FUNCIONARIO]);
        }

        if ((isset($parametros[ConstanteParametros::CHAVE_WORKFLOW_ACAO]) === true)&&(empty($parametros[ConstanteParametros::CHAVE_WORKFLOW_ACAO]) === false)) {
            $bWorkflowAcaoRetorno = self::verificaWorkflowAcaoExisteBO($parametros, $mensagemErro, $parametros[ConstanteParametros::CHAVE_WORKFLOW_ACAO]);
            if ($bWorkflowAcaoRetorno === true) {
                $this->workflowAcaoBO->aplicaProximoDestinoWorkflow($parametros[ConstanteParametros::CHAVE_WORKFLOW_ACAO], $mensagemErro, $parametros[ConstanteParametros::CHAVE_WORKFLOW]);
                $parametros[ConstanteParametros::CHAVE_SITUACAO] = SituacoesSistema::SITUACAO_ABERTO;
                if ($parametros[ConstanteParametros::CHAVE_WORKFLOW]->getTipoWorkflow() === SituacoesSistema::WORKFLOW_MATRICULA_PERDIDA) {
                    $parametros[ConstanteParametros::CHAVE_DATA_MATRICULA_PERDIDA] = new \DateTime();
                    $parametros[ConstanteParametros::CHAVE_SITUACAO] = SituacoesSistema::SITUACAO_PERDIDO;
                } else {
                    $parametros[ConstanteParametros::CHAVE_DATA_MATRICULA_PERDIDA]          = null;
                    $parametros[ConstanteParametros::CHAVE_MOTIVO_NAO_FECHAMENTO_MATRICULA] = null;
                }

                $parametros[ConstanteParametros::CHAVE_TIPO_WORKFLOW] = $parametros[ConstanteParametros::CHAVE_WORKFLOW]->getTipoWorkflow();
            }
        }

        if ((isset($parametros[ConstanteParametros::CHAVE_ALUNO]) === true)&&(empty($parametros[ConstanteParametros::CHAVE_ALUNO]) === false)) {
            $bAlunoRetorno = self::verificaAlunoExisteBO($parametros, $mensagemErro, $parametros[ConstanteParametros::CHAVE_ALUNO]);
        }

        if ((isset($parametros[ConstanteParametros::CHAVE_MOTIVO_NAO_FECHAMENTO_MATRICULA]) === true)&&(empty($parametros[ConstanteParametros::CHAVE_MOTIVO_NAO_FECHAMENTO_MATRICULA]) === false)) {
            $bMotivoNaoFechamentoMatricula = self::verificaMotivoNaoFechamentoMatriculaBO($parametros, $mensagemErro, $parametros[ConstanteParametros::CHAVE_MOTIVO_NAO_FECHAMENTO_MATRICULA]);
        }

        return ($bConsultorFuncionarioRetorno && $bPessoaIndicouRetorno && $bConsultorResponsavelFuncionarioRetorno && $bWorkflowAcaoRetorno && $bAlunoRetorno && $bMotivoNaoFechamentoMatricula && $bCursoRetorno && ($bDataValidaPromocao !== false) && ($bDataPrimeiroAtendimento !== false));
    }

    /**
     * Realiza a verificacao das regras para permitir a criacao do registro
     *
     * @param array $parametros
     * @param string $mensagemErro
     *
     * @return boolean
     */
    public function podeSalvar(&$parametros, &$mensagemErro)
    {
        if ($this->verificaCamposRelacionaisObrigatorios($parametros, $mensagemErro) === true) {
            if ($this->verificaCamposRelacionaisOpcionais($parametros, $mensagemErro) === true) {
                if ($this->workflowBO->verificaWorkflowParaAplicar($parametros) === false) {
                    $mensagemErro = "Erro critico!\nNão foi encontrado Workflow com o tipo CT ou ST na base, para prosseguir com o workflow!!!";
                } else {
                    return true;
                }
            }
        }

        return false;
    }

    /**
     * Verifica as regras para possibilitar a alteracao do registro
     *
     * @param array $parametros
     * @param string $mensagemErro
     *
     * @return boolean
     */
    public function podeAlterar(&$parametros, &$mensagemErro)
    {

        if ($this->verificaCamposRelacionaisOpcionais($parametros, $mensagemErro) === true) {
            if ($this->validaIdiomas($parametros, $mensagemErro) === true) {
                if ($this->validaTipoContato($parametros, $mensagemErro) === true) {
                    return true;
                }
            }
        }

        return false;
    }

    /**
     * Configura os campos para serem atualizados na base
     *
     * @param array $parametros
     * @param \App\Entity\Principal\Interessado $objetoORM
     * @param string $mensagemErro
     */
    public function configuraCampos($parametros, &$objetoORM, $mensagemErro)
    {
        if ((isset($parametros[ConstanteParametros::CHAVE_IDIOMAS]) === true) && (count($parametros[ConstanteParametros::CHAVE_IDIOMAS]) > 0)) {
            foreach ($parametros[ConstanteParametros::CHAVE_IDIOMAS] as $idiomaORM) {
                $objetoORM->addIdioma($idiomaORM);
            }
        }

        if (is_null($parametros[ConstanteParametros::CHAVE_CONSULTOR_RESPONSAVEL_FUNCIONARIO]) === true) {
            unset($parametros[ConstanteParametros::CHAVE_CONSULTOR_RESPONSAVEL_FUNCIONARIO]);
        }


        // if (is_null($parametros[ConstanteParametros::CHAVE_PESSOA_INDICOU]) === true) {
        //     unset($parametros[ConstanteParametros::CHAVE_PESSOA_INDICOU]);
        // } 
        \App\Helper\FunctionHelper::setParams($parametros, $objetoORM);
    }

    /**
     * Verifica se o Interessado existe atraves do campo ID
     *
     * @param \App\Repository\Principal\InteressadoRepository $interessadoRepository Repositorio do Interessado
     * @param int $id Chave primaria a ser consultada
     * @param string $mensagemErro Mensagem de erro a ser retornado ao front-end
     * @param null|\App\Entity\Principal\Interessado $resultadoORM Ponteiro para retornar o objeto
     *
     * @return boolean
     */
    public static function verificaInteressadoExiste(\App\Repository\Principal\InteressadoRepository $interessadoRepository, $id, &$mensagemErro, &$resultadoORM=null)
    {
        $resultadoORM = $interessadoRepository->find($id);
        if (is_null($resultadoORM) === true) {
            $mensagemErro .= "Interessado não foi encontrado na base de dados.";
            return false;
        }

        return true;
    }


}
