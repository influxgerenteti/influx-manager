<?php
namespace App\BO\Principal;

use App\Helper\ConstanteParametros;
use App\Helper\SituacoesSistema;
use Doctrine\ORM\EntityManagerInterface;

/**
 *
 * @author Luiz Antonio Costa
 */
class ConvenioBO extends GenericBO
{


    function __construct(EntityManagerInterface $entityManager)
    {
        parent::configuraGenericBO(
            [
                "franqueadaRepository"                  => $entityManager->getRepository(\App\Entity\Principal\Franqueada::class),
                "pessoaRepository"                      => $entityManager->getRepository(\App\Entity\Principal\Pessoa::class),
                "usuarioRepository"                     => $entityManager->getRepository(\App\Entity\Principal\Usuario::class),
                "etapasConvenioRepository"              => $entityManager->getRepository(\App\Entity\Principal\EtapasConvenio::class),
                "funcionarioRepository"                 => $entityManager->getRepository(\App\Entity\Principal\Funcionario::class),
                "motivoNaoFechamentoConvenioRepository" => $entityManager->getRepository(\App\Entity\Principal\MotivoNaoFechamentoConvenio::class),
                "segmentoEmpresaConvenioRepository"     => $entityManager->getRepository(\App\Entity\Principal\SegmentoEmpresaConvenio::class),
                "negociacaoParceriaWorkflowRepository"  => $entityManager->getRepository(\App\Entity\Principal\NegociacaoParceriaWorkflow::class),
            ]
        );
    }

    /**
     * Verifica se os parametros obrigatorios foram passados e convertidos corretamente
     *
     * @param array $parametros
     * @param string $mensagemErro
     *
     * @return boolean
     */
    protected function verificaParametrosRelacionaisObrigatorios(&$parametros, &$mensagemErro)
    {
        if (self::verificaFranqueadaExisteBO($parametros, $mensagemErro, $parametros[ConstanteParametros::CHAVE_FRANQUEADA]) === true) {
            if (self::verificaPessoaExisteBO($parametros, $mensagemErro, $parametros[ConstanteParametros::CHAVE_PESSOA], ConstanteParametros::CHAVE_PESSOA, true) === true) {
                return true;
            }
        }

        return false;
    }

    /**
     * Verifica se os parametros relacionais nao tiveram erros ao aplicarem as regras
     *
     * @param array $parametros
     * @param string $mensagemErro
     *
     * @return boolean
     */
    protected function verificaParametrosRelacionaisOpcionais(&$parametros, &$mensagemErro)
    {
        $bConsultorFuncionario        = true;
        $bMotivoNaoFechamentoConvenio = true;
        $bSegmentoEmpresaConvenio     = true;
        $bEtapasConvenio = true;
        $bNegociacaoParceriaWorkflow = true;

        if ((isset($parametros[ConstanteParametros::CHAVE_CONSULTOR_FUNCIONARIO]) === true)&&(empty($parametros[ConstanteParametros::CHAVE_CONSULTOR_FUNCIONARIO]) === false)) {
            $bConsultorFuncionario = self::verificaFuncionarioExisteBO($parametros, $mensagemErro, ConstanteParametros::CHAVE_CONSULTOR_FUNCIONARIO, $parametros[ConstanteParametros::CHAVE_CONSULTOR_FUNCIONARIO]);
        }

        if ((isset($parametros[ConstanteParametros::CHAVE_MOTIVO_NAO_FECHAMENTO_CONVENIO]) === true)&&(empty($parametros[ConstanteParametros::CHAVE_MOTIVO_NAO_FECHAMENTO_CONVENIO]) === false)) {
            $bMotivoNaoFechamentoConvenio = self::verificaMotivoNaoFechamentoConvenioExisteBO($parametros, $mensagemErro, $parametros[ConstanteParametros::CHAVE_MOTIVO_NAO_FECHAMENTO_CONVENIO]);
        }

        if ((isset($parametros[ConstanteParametros::CHAVE_SEGMENTO_EMPRESA_CONVENIO]) === true)&&(empty($parametros[ConstanteParametros::CHAVE_SEGMENTO_EMPRESA_CONVENIO]) === false)) {
            $bSegmentoEmpresaConvenio = self::verificaSegmentoEmpresaConvenioExisteBO($parametros, $mensagemErro, $parametros[ConstanteParametros::CHAVE_SEGMENTO_EMPRESA_CONVENIO]);
        }

        if ((isset($parametros[ConstanteParametros::CHAVE_ETAPAS_CONVENIO]) === true)&&(empty($parametros[ConstanteParametros::CHAVE_ETAPAS_CONVENIO]) === false)) {
            $bEtapasConvenio = self::verificaEtapasConvenioBO($parametros, $mensagemErro, $parametros[ConstanteParametros::CHAVE_ETAPAS_CONVENIO]);
        }

        if ((isset($parametros[ConstanteParametros::CHAVE_NEGOCIACAO_PARCERIA_WORKFLOW]) === true)&&(empty($parametros[ConstanteParametros::CHAVE_NEGOCIACAO_PARCERIA_WORKFLOW]) === false)) {
            $bNegociacaoParceriaWorkflow = self::verificarNegociacaoParceriaWorkflowBO($parametros, $mensagemErro, $parametros[ConstanteParametros::CHAVE_NEGOCIACAO_PARCERIA_WORKFLOW]);
        }

        return ($bConsultorFuncionario && $bMotivoNaoFechamentoConvenio && $bSegmentoEmpresaConvenio && $bEtapasConvenio && $bNegociacaoParceriaWorkflow);
    }

    /**
     * Verifica as Regras para a situacao PendenteValidacaoFranqueadora
     *
     * @param \App\Entity\Principal\Convenio $objetoORM
     * @param \App\Entity\Principal\Franqueada $franqueadaSelecionadaORM
     * @param \App\Entity\Principal\Usuario $usuarioORM
     * @param array $parametros
     * @param string $mensagemErro
     *
     * @return boolean
     */
    protected function verificaSituacaoPendenteValidacaoFranqueadora(&$objetoORM, $franqueadaSelecionadaORM, $usuarioORM, &$parametros, &$mensagemErro)
    {
        $bRetorno = false;
        if ($usuarioORM->isUsuarioPertenceFranqueadora() === true) {
            $bRetornoEtapasConvenio = self::verificaEtapasConvenioBO($parametros, $mensagemErro, $parametros[ConstanteParametros::CHAVE_ETAPAS_CONVENIO]);
            $parametros[ConstanteParametros::CHAVE_DATA_AVALIACAO_FRANQUEADORA] = new \DateTime();
            if ($bRetornoEtapasConvenio === true) {
                if ((isset($parametros[ConstanteParametros::CHAVE_JUSTIFICATIVA_FRANQUEADORA]) === true)&&(empty($parametros[ConstanteParametros::CHAVE_JUSTIFICATIVA_FRANQUEADORA]) === true)) {
                    $mensagemErro .= "O campo justificativa_franqueadora é obrigatório";
                } else {
                    $etapasConvenioORM = $parametros[ConstanteParametros::CHAVE_ETAPAS_CONVENIO];
                    $bRetorno          = true;
                    if ($parametros[ConstanteParametros::CHAVE_SITUACAO] === \App\Helper\SituacoesSistema::SITUACAO_NEGADO) {
                        if (($etapasConvenioORM->getParceriaFirmada() === true)||($etapasConvenioORM->getRetiraFluxo() === false)) {
                            $mensagemErro = "A EtapaConvenio selecionada não possui os parametros parceria_firmada[true] e retira_fluxo[false].";
                            $bRetorno     = false;
                        }
                    }

                    if ($parametros[ConstanteParametros::CHAVE_SITUACAO] === \App\Helper\SituacoesSistema::SITUACAO_EM_NEGOCIACAO) {
                        if (($etapasConvenioORM->getParceriaFirmada() === true)||($etapasConvenioORM->getRetiraFluxo() === true)) {
                            $mensagemErro = "A EtapaConvenio selecionada não possui os parametros parceria_firmada[false] e retira_fluxo[false].";
                            $bRetorno     = false;
                        }
                    }
                }
            }//end if
        }//end if

        return $bRetorno;
    }

    /**
     * Verifica as regras para situacao Em Negociacao
     *
     * @param \App\Entity\Principal\Convenio $objetoORM
     * @param \App\Entity\Principal\Franqueada $franqueadaSelecionadaORM
     * @param \App\Entity\Principal\Usuario $usuarioORM
     * @param array $parametros
     * @param string $mensagemErro
     *
     * @return boolean
     */
    protected function verificaSituacaoEmNegociacao(&$objetoORM, $franqueadaSelecionadaORM, $usuarioORM, &$parametros, &$mensagemErro)
    {
        $bRetorno          = false;
        $etapasConvenioORM = $parametros[ConstanteParametros::CHAVE_ETAPAS_CONVENIO];
        if ($parametros[ConstanteParametros::CHAVE_SITUACAO] === \App\Helper\SituacoesSistema::SITUACAO_ATIVO) {
            $bRetorno = true;
            if (($etapasConvenioORM->getParceriaFirmada() === false)||($etapasConvenioORM->getRetiraFluxo() === false)) {
                $mensagemErro = "A EtapaConvenio selecionada não possui os parametros parceria_firmada[true] e retira_fluxo[true].";
                $bRetorno     = false;
            }
        }

        if ($parametros[ConstanteParametros::CHAVE_SITUACAO] === \App\Helper\SituacoesSistema::SITUACAO_EM_NEGOCIACAO) {
            $bRetorno = true;
            if (($etapasConvenioORM->getParceriaFirmada() === true)||($etapasConvenioORM->getRetiraFluxo() === true)) {
                $mensagemErro = "A EtapaConvenio selecionada não possui os parametros parceria_firmada[false] e retira_fluxo[false].";
                $bRetorno     = false;
            }
        }

        if (($parametros[ConstanteParametros::CHAVE_SITUACAO] === \App\Helper\SituacoesSistema::SITUACAO_RETORNAR_FUTURAMENTE) || ($parametros[ConstanteParametros::CHAVE_SITUACAO] === \App\Helper\SituacoesSistema::SITUACAO_SEM_COVENIO)) {
            $bRetorno = true;
            if (($etapasConvenioORM->getParceriaFirmada() === true)||($etapasConvenioORM->getRetiraFluxo() === false)) {
                $mensagemErro = "A EtapaConvenio selecionada não possui os parametros parceria_firmada[false] e retira_fluxo[true].";
                $bRetorno     = false;
            }
        }

        return $bRetorno;
    }

    /**
     * Verifica Regras situacao Ativo
     *
     * @param \App\Entity\Principal\Convenio $objetoORM
     * @param \App\Entity\Principal\Franqueada $franqueadaSelecionadaORM
     * @param \App\Entity\Principal\Usuario $usuarioORM
     * @param array $parametros
     * @param string $mensagemErro
     *
     * @return boolean
     */
    protected function verificaSituacaoAtivo(&$objetoORM, $franqueadaSelecionadaORM, $usuarioORM, &$parametros, &$mensagemErro)
    {
        if ($parametros[ConstanteParametros::CHAVE_SITUACAO] !== \App\Helper\SituacoesSistema::SITUACAO_INATIVO) {
            $mensagemErro = "Apenas a situação 'Inativa' está habilitada.";
            return false;
        }

        return true;
    }

    /**
     * Verifica Regras para situacao Inativo
     *
     * @param \App\Entity\Principal\Convenio $objetoORM
     * @param \App\Entity\Principal\Franqueada $franqueadaSelecionadaORM
     * @param \App\Entity\Principal\Usuario $usuarioORM
     * @param array $parametros
     * @param string $mensagemErro
     *
     * @return boolean
     */
    protected function verificaSituacaoInativo(&$objetoORM, $franqueadaSelecionadaORM, $usuarioORM, &$parametros, &$mensagemErro)
    {
        if ($parametros[ConstanteParametros::CHAVE_SITUACAO] !== \App\Helper\SituacoesSistema::SITUACAO_ATIVO) {
            $mensagemErro = "Apenas a situação 'Ativa' está habilitada.";
            return false;
        }

        return true;
    }

    /**
     * Verifica as regras para Retorno Futuro e Sem Convenio
     *
     * @param \App\Entity\Principal\Convenio $objetoORM
     * @param \App\Entity\Principal\Franqueada $franqueadaSelecionadaORM
     * @param \App\Entity\Principal\Usuario $usuarioORM
     * @param array $parametros
     * @param string $mensagemErro
     *
     * @return boolean
     */
    protected function verificaSituacaoRFouSC(&$objetoORM, $franqueadaSelecionadaORM, $usuarioORM, &$parametros, &$mensagemErro)
    {
        if ($parametros[ConstanteParametros::CHAVE_SITUACAO] !== \App\Helper\SituacoesSistema::SITUACAO_EM_NEGOCIACAO) {
            $mensagemErro = "Apenas a situação 'Em negociação' está habilitada.";
            return false;
        }

        return true;
    }

    /**
     * Verifica as regras para Situacao Negada
     *
     * @param \App\Entity\Principal\Convenio $objetoORM
     * @param \App\Entity\Principal\Franqueada $franqueadaSelecionadaORM
     * @param \App\Entity\Principal\Usuario $usuarioORM
     * @param array $parametros
     * @param string $mensagemErro
     *
     * @return boolean
     */
    protected function verificaSituacaoNegada(&$objetoORM, $franqueadaSelecionadaORM, $usuarioORM, &$parametros, &$mensagemErro)
    {
        if ($parametros[ConstanteParametros::CHAVE_SITUACAO] !== \App\Helper\SituacoesSistema::SITUACAO_PENDENTE_VALIDACAO_FRANQUEADORA) {
            $mensagemErro = "Apenas a situação 'Pendente Validação Franqueadora' está habilitada.";
            return false;
        }

        return true;
    }

    /**
     * Verifica as obrigatoriedades dos campos atraves do campo situacao
     *
     * @param \App\Entity\Principal\Convenio $objetoORM
     * @param \App\Entity\Principal\Franqueada $franqueadaSelecionadaORM
     * @param \App\Entity\Principal\Usuario $usuarioORM
     * @param array $parametros
     * @param string $mensagemErro
     *
     * @return boolean
     */
    protected function verificaObrigatoriosPorSituacao(&$objetoORM, $franqueadaSelecionadaORM, $usuarioORM, &$parametros, &$mensagemErro)
    {
        if ($objetoORM->getSituacao() === \App\Helper\SituacoesSistema::SITUACAO_ATIVO) {
            return $this->verificaSituacaoAtivo($objetoORM, $franqueadaSelecionadaORM, $usuarioORM, $parametros, $mensagemErro);
        }

        if ($objetoORM->getSituacao() === \App\Helper\SituacoesSistema::SITUACAO_INATIVO) {
            return $this->verificaSituacaoInativo($objetoORM, $franqueadaSelecionadaORM, $usuarioORM, $parametros, $mensagemErro);
        }

        if ($objetoORM->getSituacao() === \App\Helper\SituacoesSistema::SITUACAO_EM_NEGOCIACAO) {
            return $this->verificaSituacaoEmNegociacao($objetoORM, $franqueadaSelecionadaORM, $usuarioORM, $parametros, $mensagemErro);
        }

        if ($objetoORM->getSituacao() === \App\Helper\SituacoesSistema::SITUACAO_PENDENTE_VALIDACAO_FRANQUEADORA) {
            return $this->verificaSituacaoPendenteValidacaoFranqueadora($objetoORM, $franqueadaSelecionadaORM, $usuarioORM, $parametros, $mensagemErro);
        }

        if (($objetoORM->getSituacao() === \App\Helper\SituacoesSistema::SITUACAO_RETORNAR_FUTURAMENTE) || ($objetoORM->getSituacao() === \App\Helper\SituacoesSistema::SITUACAO_SEM_COVENIO)) {
            return $this->verificaSituacaoRFouSC($objetoORM, $franqueadaSelecionadaORM, $usuarioORM, $parametros, $mensagemErro);
        }

        if ($objetoORM->getSituacao() === \App\Helper\SituacoesSistema::SITUACAO_NEGADO) {
            return $this->verificaSituacaoNegada($objetoORM, $franqueadaSelecionadaORM, $usuarioORM, $parametros, $mensagemErro);
        }

        return false;
    }

    /**
     * Verifica se as regras para criacao do registro foram aplicadas corretamente
     *
     * @param array $parametros
     * @param string $mensagemErro
     *
     * @return boolean
     */
    public function podeCriar(&$parametros, &$mensagemErro)
    {
        if ($this->verificaParametrosRelacionaisObrigatorios($parametros, $mensagemErro) === true) {
            if ($this->verificaParametrosRelacionaisOpcionais($parametros, $mensagemErro) === true) {
                return true;
            }
        }

        return false;
    }

    /**
     * Verifica se as regras para poder atualizar o registro foram aplicadas corretamente
     *
     * @param array $parametros
     * @param string $mensagemErro
     *
     * @return boolean
     */
    public function podeAtualizar(&$parametros, &$mensagemErro)
    {
        if ($this->verificaParametrosRelacionaisOpcionais($parametros, $mensagemErro) === true) {
            return true;
        }

        return false;
    }

    /**
     * Configura os parametros para atualizacao do objeto
     *
     * @param \App\Entity\Principal\Convenio $objetoORM
     * @param \App\Entity\Principal\Franqueada $franqueadaSelecionadaORM
     * @param \App\Entity\Principal\Usuario $usuarioORM
     * @param array $parametros
     * @param string $mensagemErro
     *
     * @return boolean
     */
    public function configuraParametros(&$objetoORM, $franqueadaSelecionadaORM, $usuarioORM, &$parametros, &$mensagemErro)
    {
        $bRetorno = true;

        if ((isset($parametros[ConstanteParametros::CHAVE_ARQUIVO_CONTRATO]) === true)&&(empty($parametros[ConstanteParametros::CHAVE_ARQUIVO_CONTRATO]) === false)) {
            if (self::gravaArquivo($parametros[ConstanteParametros::CHAVE_ARQUIVO_CONTRATO], $mensagemErro, $objetoORM, "setContratoDigitalizado", "getContratoDigitalizado") === false) {
                $mensagemErro .= "Erro ao realizar a tratativa do arquivo de contrato.\n";
            }
        }

        if ((isset($parametros[ConstanteParametros::CHAVE_DATA_PRIMEIRO_ATENDIMENTO]) === true)&&(empty($parametros[ConstanteParametros::CHAVE_DATA_PRIMEIRO_ATENDIMENTO]) === false)) {
            $dataPrimeiroAtendimento = $parametros[ConstanteParametros::CHAVE_DATA_PRIMEIRO_ATENDIMENTO];
            \App\Helper\FunctionHelper::formataCampoDateTimeJS($dataPrimeiroAtendimento, $parametros[ConstanteParametros::CHAVE_DATA_PRIMEIRO_ATENDIMENTO]);
            if ($parametros[ConstanteParametros::CHAVE_DATA_PRIMEIRO_ATENDIMENTO] === false) {
                $mensagemErro .= "Erro ao converter a data de primeiro atendimento para o formato aceito pelo banco de dados.[" . $dataPrimeiroAtendimento . "]";
            }
        }

        if ((isset($parametros[ConstanteParametros::CHAVE_DATA_PROXIMO_CONTATO]) === true)&&(empty($parametros[ConstanteParametros::CHAVE_DATA_PROXIMO_CONTATO]) === false)) {
            $dataProximoContato = $parametros[ConstanteParametros::CHAVE_DATA_PROXIMO_CONTATO];
            \App\Helper\FunctionHelper::formataCampoDateTimeJS($dataProximoContato, $parametros[ConstanteParametros::CHAVE_DATA_PROXIMO_CONTATO]);
            if ($parametros[ConstanteParametros::CHAVE_DATA_PROXIMO_CONTATO] === false) {
                $mensagemErro .= "Erro ao converter a data de proximo contato para o formato aceito pelo banco de dados.[" . $dataProximoContato . "]";
            }
        } else {
            $parametros[ConstanteParametros::CHAVE_DATA_PROXIMO_CONTATO] = null;
        }

        if ((isset($parametros[ConstanteParametros::CHAVE_HORARIO_PROXIMO_CONTATO]) === true)&&(empty($parametros[ConstanteParametros::CHAVE_HORARIO_PROXIMO_CONTATO]) === false)) {
            $hora = $parametros[ConstanteParametros::CHAVE_HORARIO_PROXIMO_CONTATO];
            \App\Helper\FunctionHelper::formataCampoDateTimeJS($hora, $parametros[ConstanteParametros::CHAVE_HORARIO_PROXIMO_CONTATO]);
            if ($parametros[ConstanteParametros::CHAVE_HORARIO_PROXIMO_CONTATO] === false) {
                $mensagemErro .= "Erro ao converter a hora de proximo contato para o formato aceito pelo banco de dados.[" . $hora . "]";
            }
        } else {
            $parametros[ConstanteParametros::CHAVE_HORARIO_PROXIMO_CONTATO] = null;
        }

        if ((isset($parametros[ConstanteParametros::CHAVE_FECHAR_CONVENIO]) === true)&&((bool) $parametros[ConstanteParametros::CHAVE_FECHAR_CONVENIO] === true) && is_null($parametros[ConstanteParametros::CHAVE_SITUACAO]) === false) {
            $isNacional = (bool) $parametros[ConstanteParametros::CHAVE_ABRANGENCIA_NACIONAL];
            $parametros[ConstanteParametros::CHAVE_SITUACAO] = SituacoesSistema::SITUACAO_ATIVO_CONVENIO;

            if ($isNacional === true) {
                $parametros[ConstanteParametros::CHAVE_SITUACAO] = SituacoesSistema::SITUACAO_PENDENTE_VALIDACAO;
            }
        }

        if ($bRetorno === true) {
            \App\Helper\FunctionHelper::setParams($parametros, $objetoORM);
        }

        return $bRetorno;
    }

    /**
     * Verifica se as regras para aplicar o followup estão validas
     *
     * @param array $parametros
     * @param string $mensagemErro
     *
     * @return boolean
     */
    public function podeAdicionarFollowup(&$parametros, &$mensagemErro)
    {
        if ($this->verificaParametrosRelacionaisOpcionais($parametros, $mensagemErro) === true) {
            return true;
        }

        return false;
    }

    /**
     * Verifica se existe a categoria no banco de dados, se existir, ele retorna no ultimo parametro na funcao, caso contrario, ele preenchera a msg de erro e retornara falso
     *
     * @param \App\Repository\Principal\ConvenioRepository $convenioRepository Repositorio do convenio
     * @param integer $id Chave primaria da categoria
     * @param string $mensagemErro Msg de erro para ser retornada ao front-end
     * @param \App\Entity\Principal\Convenio|null $convenioORM Retorno no caso de sucesso
     *
     * @return boolean true|false
     */
    public static function verificaConvenioExiste(\App\Repository\Principal\ConvenioRepository $convenioRepository, $id, &$mensagemErro, &$convenioORM)
    {
        $convenioORM = $convenioRepository->find($id);
        if (is_null($convenioORM) === true) {
            $mensagemErro = "Convenio não encontrada na base de dados.";
            return false;
        }

        return true;
    }


}
