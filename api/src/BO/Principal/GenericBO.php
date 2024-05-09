<?php
namespace App\BO\Principal;

use App\Helper\ConstanteParametros;
use App\Helper\SituacoesSistema;
use DateTime;

/**
 *
 * @author Luiz Antonio Costa
 * BO = Busines object ou objeto de regra de negocia.
 */
class GenericBO
{
    /**
     *
     * @var \Doctrine\ORM\EntityManagerInterface
     */
    private $entityManager;

    /**
     *
     * @var \App\Repository\Principal\TipoMovimentoEstoqueRepository
     */
    private $tipoMovimentoEstoqueRepository;

    /**
     *
     * @var \App\Repository\Principal\FranqueadaRepository
     */
    private $franqueadaRepository;

    /**
     *
     * @var \App\Repository\Principal\ModeloTemplateRepository
     */
    private $modeloTemplateRepository;

    /**
     *
     * @var \App\Repository\Principal\BancoRepository
     */
    private $bancoRepository;

    /**
     *
     * @var \App\Repository\Principal\ContaRepository
     */
    private $contaRepository;

    /**
     *
     * @var \App\Repository\Principal\UsuarioRepository
     */
    private $usuarioRepository;

    /**
     *
     * @var \App\Repository\Principal\PessoaRepository
     */
    private $pessoaRepository;

    /**
     *
     * @var \App\Repository\Principal\CondicaoPagamentoRepository
     */
    private $condicaoPagamentoRepository;

    /**
     *
     * @var \App\Repository\Principal\ContaPagarRepository
     */
    private $contaPagarRepository;

    /**
     *
     * @var \App\Repository\Principal\ItemRepository
     */
    private $itemRepository;

    /**
     *
     * @var \App\Repository\Principal\TipoMovimentoContaRepository
     */
    private $tipoMovimentoContaRepository;

    /**
     *
     * @var \App\Repository\Principal\TituloPagarRepository
     */
    private $tituloPagarRepository;

    /**
     *
     * @var \App\Repository\Principal\TituloReceberRepository
     */
    private $tituloReceberRepository;

    /**
     *
     * @var \App\Repository\Principal\MovimentoContaRepository
     */
    private $movimentoContaRepository;

    /**
     *
     * @var \App\Repository\Principal\AlunoRepository
     */
    private $alunoRepository;

    /**
     *
     * @var \App\Repository\Principal\ItemContaReceberRepository
     */
    private $itemContaReceberRepository;

    /**
     *
     * @var \App\Repository\Principal\PlanoContaRepository
     */
    private $planoContaRepository;

    /**
     *
     * @var \App\Repository\Principal\ConceitoAvaliacaoRepository
     */
    private $conceitoAvaliacaoRepository;

    /**
     *
     * @var \App\Repository\Principal\SistemaAvaliacaoRepository
     */
    private $sistemaAvaliacaoRepository;

    /**
     *
     * @var \App\Repository\Principal\IdiomaRepository
     */
    private $idiomaRepository;

    /**
     *
     * @var \App\Repository\Principal\CursoRepository
     */
    private $cursoRepository;

    /**
     *
     * @var \App\Repository\Principal\PlanejamentoLicaoRepository
     */
    private $planejamentoLicaoRepository;

    /**
     *
     * @var \App\Repository\Principal\LicaoRepository
     */
    private $licaoRepository;

    /**
     *
     * @var \App\Repository\Principal\SalaRepository
     */
    private $salaRepository;
    /**
     *
     * @var \App\Repository\Principal\ValorHoraLinhasRepository
     */
    private $valorHoraLinhasRepository;

    /**
     *
     * @var \App\Repository\Principal\NivelInstrutorRepository
     */
    private $nivelInstrutorRepository;

    /**
     *
     * @var \App\Repository\Principal\CargoRepository
     */
    private $cargoRepository;

    /**
     *
     * @var \App\Repository\Principal\FuncionarioRepository
     */
    private $funcionarioRepository;

    /**
     *
     * @var \App\Repository\Principal\ValorHoraRepository
     */
    private $valorHoraRepository;

    /**
     *
     * @var \App\Repository\Principal\HorarioRepository
     */
    private $horarioRepository;

    /**
     *
     * @var \App\Repository\Principal\ClassificacaoAlunoRepository
     */
    private $classificacaoAlunoRepository;

    /**
     *
     * @var \App\Repository\Principal\EscolaridadeRepository
     */
    private $escolaridadeRepository;

    /**
     *
     * @var \App\Repository\Principal\RelacionamentoAlunoRepository
     */
    private $relacionamentoAlunoRepository;

    /**
     * @var \App\Repository\Principal\FormaPagamentoRepository
     */
    private $formaPagamentoRepository;

    /**
     *
     * @var \App\Repository\Principal\CalendarioRepository
     */
    private $calendarioRepository;

    /**
     *
     * @var \App\Repository\Principal\CategoriaRepository
     */
    private $categoriaRepository;

    /**
     *
     * @var \App\Repository\Principal\EstadoRepository
     */
    private $estadoRepository;

    /**
     *
     * @var \App\Repository\Principal\CidadeRepository
     */
    private $cidadeRepository;

    /**
     *
     * @var \App\Repository\Principal\MotivoDevolucaoChequeRepository
     */
    private $motivoDevolucaoChequeRepository;

    /**
     *
     * @var \App\Repository\Principal\TurmaRepository
     */
    private $turmaRepository;

     /**
     *
     * @var \App\Repository\Principal\SalaAgendamentoRepository
     */
    private $salaAgendamentoRepository;

    /**
     *
     * @var \App\Repository\Principal\ModalidadeTurmaRepository
     */
    private $modalidadeTurmaRepository;

    /**
     *
     * @var \App\Repository\Principal\LivroRepository
     */
    private $livroRepository;

    /**
     *
     * @var \App\Repository\Principal\SalaFranqueadaRepository
     */
    private $salaFranqueadaRepository;

    /**
     *
     * @var \App\Repository\Principal\ContratoRepository
     */
    private $contratoRepository;

    /**
     *
     * @var \App\Repository\Principal\AtividadeDollarRepository
     */
    private $atividadeDollarRepository;

    /**
     *
     * @var \App\Repository\Principal\MovimentoDollarRepository
     */
    private $movimentoDollarRepository;

    /**
     *
     * @var \App\Repository\Principal\SemestreRepository
     */
    private $semestreRepository;

    /**
     *
     * @var \App\Repository\Principal\WorkflowRepository
     */
    private $workflowRepository;

    /**
     *
     * @var \App\Repository\Principal\ChequeRepository
     */
    private $chequeRepository;

    /**
     *
     * @var \App\Repository\Principal\BoletoRepository
     */
    private $boletoRepository;

    /**
     *
     * @var \App\Repository\Principal\TransacaoCartaoRepository
     */
    private $transacaoCartaoRepository;

    /**
     *
     * @var \App\Repository\Principal\ContaReceberRepository
     */
    private $contaReceberRepository;

    /**
     *
     * @var \App\Repository\Principal\AcaoSistemaRepository
     */
    private $acaoSistemaRepository;

    /**
     *
     * @var \App\Repository\Principal\UrlSistemaRepository
     */
    private $urlSistemaRepository;

    /**
     *
     * @var \App\Repository\Principal\PapelRepository
     */
    private $papelRepository;

    /**
     *
     * @var \App\Repository\Principal\ModuloRepository
     */
    private $moduloRepository;

    /**
     *
     * @var \App\Repository\Principal\ModuloPapelAcaoRepository
     */
    private $moduloPapelAcaoRepository;

    /**
     *
     * @var \App\Repository\Principal\ModuloUsuarioAcaoRepository
     */
    private $moduloUsuarioAcaoRepository;

    /**
     *
     * @var \App\Repository\Principal\FormularioFollowUpRepository
     */
    private $formularioFollowUpRepository;

    /**
     *
     * @var \App\Repository\Principal\FormularioFollowUpCamposRepository
     */
    private $formularioFollowUpCamposRepository;

    /**
     *
     * @var \App\Repository\Principal\TipoContatoRepository
     */
    private $tipoContatoRepository;

    /**
     *
     * @var \App\Repository\Principal\InteressadoRepository
     */
    private $interessadoRepository;

    /**
     *
     * @var \App\Repository\Principal\MotivoNaoFechamentoMatriculaRepository
     */
    private $motivoNaoFechamentoMatriculaRepository;

    /**
     *
     * @var \App\Repository\Principal\SegmentoEmpresaConvenioRepository
     */
    private $segmentoEmpresaConvenioRepository;

    /**
     *
     * @var \App\Repository\Principal\EtapasConvenioRepository
     */
    private $etapasConvenioRepository;

    /**
     *
     * @var \App\Repository\Principal\NegociacaoParceriaWorkflowRepository
     */
    private $negociacaoParceriaWorkflowRepository;

    /**
     *
     * @var \App\Repository\Principal\MotivoNaoFechamentoConvenioRepository
     */
    private $motivoNaoFechamentoConvenioRepository;

    /**
     *
     * @var \App\Repository\Principal\ConvenioRepository
     */
    private $convenioRepository;

    /**
     *
     * @var \App\Repository\Principal\OperadoraCartaoRepository
     */
    private $operadoraCartaoRepository;

    /**
     *
     * @var \App\Repository\Principal\ParcelamentoOperadoraCartaoRepository
     */
    private $parcelamentoOperadoraCartaoRepository;

    /**
     *
     * @var \App\Repository\Principal\ParcelaParcelamentoRepository
     */
    private $parcelaParcelamentoRepository;

    /**
     *
     * @var \App\Repository\Principal\TipoAgendamentoRepository
     */
    private $tipoAgendamentoRepository;

    /**
     *
     * @var \App\Repository\Principal\AgendaComercialRepository
     */
    private $agendaComercialRepository;

    /**
     *
     * @var \App\Repository\Principal\WorkflowAcaoRepository
     */
    private $workflowAcaoRepository;

    /**
     *
     * @var \App\Repository\Principal\TipoProspeccaoRepository
     */
    private $tipoProspeccaoRepository;

    /**
     *
     * @var \App\Repository\Principal\ChecklistRepository
     */
    private $checklistRepository;

    /**
     *
     * @var \App\Repository\Principal\MidiaRepository
     */
    private $midiaRepository;

    /**
     *
     * @var \App\Repository\Principal\ChecklistAtividadeRepository
     */
    private $checklistAtividadeRepository;

    /**
     *
     * @var \App\Repository\Principal\TipoOcorrenciaRepository
     */
    private $tipoOcorrenciaRepository;

    /**
     *
     * @var \App\Repository\Principal\OcorrenciaAcademicaRepository
     */
    private $ocorrenciaAcademicaRepository;

    /**
     *  @var \App\Repository\Principal\TurmaAulaRepository
     */
    private $turmaAulaRepository;

    /**
     *
     * @var \App\Repository\Principal\AtividadeExtraRepository
     */
    private $atividadeExtraRepository;

    /**
     *
     * @var \App\Repository\Principal\AlunoAtividadeExtraRepository
     */
    private $alunoAtividadeExtraRepository;

    /**
     *
     * @var \App\Repository\Principal\ServicoRepository
     */
    private $servicoRepository;

    /**
     *
     * @var \App\Repository\Principal\AlunoAvaliacaoRepository
     */
    private $alunoAvaliacaoRepository;

    /**
     *
     * @var \App\Repository\Principal\TipoItemRepository
     */
    private $tipoItemRepository;

    /**
     *
     * @var \App\Repository\Principal\AlunoDiarioRepository
     */
    private $alunoDiarioRepository;

    /**
     *
     * @var \App\Repository\Principal\AgendaCompromissoRepository
     */
    private $agendaCompromissoRepository;

    /**
     *
     * @var \App\Repository\Principal\AgendamentoPersonalRepository
     */
    private $agendamentoPersonalRepository;

    /**
     *
     * @var \App\Repository\Principal\CreditosPersonalRepository
     */
    private $creditosPersonalRepository;

    /**
     *
     * @var \App\Repository\Principal\BonusClassRepository
     */
    private $bonusClassRepository;

    /**
     *
     * @var \App\Repository\Principal\TipoVisibilidadeRepository
     */
    private $tipoVisibilidadeRepository;

    /**
     *
     * @var \App\Repository\Principal\AlunoAvaliacaoConceitualRepository
     */
    private $alunoAvaliacaoConceitualRepository;

    /**
     *
     * @var \App\Repository\Principal\OrigemOcorrenciaRepository
     */
    private $origemOcorrenciaRepository;

    /**
     * Configura os repositorios a serem utilizados no BO estendido e deixa pronto o array para ser re-utilizado para configuracao de bo
     *
     * @param array $repositoriosArray Array com indice de propriedade e valor a ser atribuido na propriedade
     * @param \Doctrine\ORM\EntityManagerInterface $em Base de dados principal
     */
    protected function configuraRepositorios(&$repositoriosArray, \Doctrine\ORM\EntityManagerInterface $em=null)
    {
        $this->entityManager = $em;
        foreach ($repositoriosArray as $chave => $valor) {
            $this->{$chave} = $valor;
        }
    }

    /**
     * Converte a data de string JS do campo de Data Inicial e Final da pesquisa para o formato JS
     *
     * @param array $parametros Ponteiro do array para realizar a formatacao
     * @param string $mensagemErro Mensagem de erro a retornar pro front-end
     *
     * @return boolean
     */
    protected function verificaDataInicialDataFinal(&$parametros, &$mensagemErro)
    {
        if (is_null($parametros[ConstanteParametros::CHAVE_TIT_DATA_INICIAL]) === false) {
            \App\Helper\FunctionHelper::formataCampoDateTimeJS($parametros[ConstanteParametros::CHAVE_TIT_DATA_INICIAL], $parametros[ConstanteParametros::CHAVE_TIT_DATA_INICIAL]);

            if ($parametros[ConstanteParametros::CHAVE_TIT_DATA_INICIAL] === false) {
                $mensagemErro = "Ocorreu um erro na formatação da Data Inicial. Formato de data não reconhecida.";
                return false;
            }
        }

        if (is_null($parametros[ConstanteParametros::CHAVE_TIT_DATA_FINAL]) === false) {
            \App\Helper\FunctionHelper::formataCampoDateTimeJS($parametros[ConstanteParametros::CHAVE_TIT_DATA_FINAL], $parametros[ConstanteParametros::CHAVE_TIT_DATA_FINAL]);
            if ($parametros[ConstanteParametros::CHAVE_TIT_DATA_FINAL] === false) {
                $mensagemErro = "Ocorreu um erro na formatação da Data Final. Formato de data não reconhecida.";
                return false;
            }
        }

        return true;
    }

    /**
     * Cria o arquivo ORM e insere na memoria do doctrine
     *
     * @param NULL|\App\Entity\Principal\Arquivos $arquivoORM
     * @param string $mensagemErro
     * @param string $nomeArquivo
     * @param string $extensaoArquivo
     * @param string $caminhoServidorArquivo
     *
     * @return boolean
     */
    protected function criarArquivoORM(&$arquivoORM, &$mensagemErro, $nomeArquivo, $extensaoArquivo, $caminhoServidorArquivo)
    {
        try {
            $parametros = [
                ConstanteParametros::CHAVE_NOME_ARQUIVO     => $nomeArquivo,
                ConstanteParametros::CHAVE_EXTENSAO         => $extensaoArquivo,
                ConstanteParametros::CHAVE_CAMINHO_SERVIDOR => $caminhoServidorArquivo,
            ];
            $arquivoORM = \App\Factory\GeneralORMFactory::criar(\App\Entity\Principal\Arquivos::class, true, $parametros);
            $this->entityManager->persist($arquivoORM);
        } catch (\Exception $e) {
            $mensagemErro .= "\nNão foi possivel inserir o registro no banco de dados devido a algum erro no persist<br>Erro:" . $e->getMessage();
            $arquivoORM    = null;
        }

        return empty($mensagemErro) === true;
    }

    /**
     *
     * @param array $parametros Parametros a serem inclusos no objeto
     *
     * @return float $valor_calculado Retorna valor calculado
     */
    protected function calculaValorParaAbatimentoNoSaldo($parametros)
    {
        $lancamento = 0;
        $multa      = 0;
        $juros      = 0;
        $desconto   = 0;
        $baixado    = 0;

        if (isset($parametros[ConstanteParametros::CHAVE_MC_VALOR_LANCAMENTO]) === true) {
            $lancamento = (float) $parametros[ConstanteParametros::CHAVE_MC_VALOR_LANCAMENTO];
        }

        if (isset($parametros[ConstanteParametros::CHAVE_MC_VALOR_MULTA]) === true) {
            $multa = (float) $parametros[ConstanteParametros::CHAVE_MC_VALOR_MULTA];
        }

        if (isset($parametros[ConstanteParametros::CHAVE_MC_VALOR_JUROS]) === true) {
            $juros = (float) $parametros[ConstanteParametros::CHAVE_MC_VALOR_JUROS];
        }

        if (isset($parametros[ConstanteParametros::CHAVE_MC_VALOR_DESCONTO]) === true) {
            $desconto = (float) $parametros[ConstanteParametros::CHAVE_MC_VALOR_DESCONTO];
        }

        if (((isset($parametros[ConstanteParametros::CHAVE_TITULO_PAGAR]) === false) || (is_null($parametros[ConstanteParametros::CHAVE_TITULO_PAGAR]) === true)) && (isset($parametros[ConstanteParametros::CHAVE_MC_VALOR_DIFERENCA_BAIXA]) === true)) {
            $baixado = (float) $parametros[ConstanteParametros::CHAVE_MC_VALOR_DIFERENCA_BAIXA];
        }

        $valor_calculado = $lancamento - $multa - $juros + $desconto + $baixado;
        return round($valor_calculado, 2);
    }

    /**
     * Verifica o timestamp de uma data se é menor do que o timestamp deste exato momento
     *
     * @param \DateTime $data Data que o usuário informou no front end
     * @param string $mensagemErro Mensagem de erro a ser retornado ao front-end
     * @param string $nomeData Nome da data
     * @param boolean $igualarADozeHoras Se precisa forçar o horário a ser comparado a ser doze horas
     *
     * @return boolean
     */
    protected function verificaDatetimeDataMenorQueDatetimeAgora(&$data, &$mensagemErro, $nomeData="", $igualarADozeHoras=false)
    {
        if (is_null($data) === false) {
            $result = new DateTime;
            if ($data instanceof \DateTime === false) {
                \App\Helper\FunctionHelper::formataCampoDateTimeJS($data, $result);
                if($result == false){
                    \App\Helper\FunctionHelper::formataCampoDateTime($data, $result);
                }
                if($result == false){
                    return false;
                }
                
             
            }
            if ($igualarADozeHoras === true) {
                $result = $result->setTime(12, 00, 00);
            }

            $timestamp_data  = $result->getTimestamp();
            $timestamp_agora = (new \Datetime())->setTime(12, 00, 00)->getTimestamp();

            if ($timestamp_data > $timestamp_agora) {
                $mensagemErro = "Data $nomeData maior do que data de hoje";
                return false;
            }
        }

        return true;
    }

    /**
     * Converte a data de string JS do campo de Data para o formato \DateTime PHP
     *
     * @param string $data Ponteiro da data
     * @param string $mensagemErro Mensagem de erro a retornar pro front-end
     * @param string $nome_data Nome da Data
     *
     * @return boolean
     */
    protected function converteData(&$data, &$mensagemErro, $nome_data="")
    {
        if (is_null($data) === false) {
             \App\Helper\FunctionHelper::formataCampoDateTimeJS($data, $result);
            if($result == false){
                \App\Helper\FunctionHelper::formataCampoDateTime($data, $result);
            }
            if ($result=== false) {
                $mensagemErro .= "Ocorreu um erro na formatação de Data no campo Data " . $nome_data . ". Formato de data não reconhecida.";
                return false;
            }
            
        }

        return true;
    }

    /**
     * Converte a data de string JS do campo de Data para o formato \DateTime PHP
     *
     * @param string $data Ponteiro da data
     * @param string $mensagemErro Mensagem de erro a retornar pro front-end
     * @param string $nome_data Nome da Data
     *
     * @return boolean
     */
    protected function converteDataAndSave(&$data, &$mensagemErro, $nome_data="")
    {
        if (is_null($data) === false) {
             \App\Helper\FunctionHelper::formataCampoDateTimeJS($data, $result);
            if($result == false){
                \App\Helper\FunctionHelper::formataCampoDateTime($data, $result);
            }
            if ($result=== false) {
                $mensagemErro .= "Ocorreu um erro na formatação de Data no campo Data " . $nome_data . ". Formato de data não reconhecida.";
                return false;
            }
            $data = $result;
        }


        return true;
    }

    /**
     * Calcula a diferenca das parcelas e aplica a diferenca sempre na primeira parcela.
     *
     * @param float $valorTotalTitulo Valor total do titulo da conta a pagar
     * @param float $totalParcelas Somatoria das parcelas
     * @param array $parcelas Array de Parcelas calculadas
     */
    protected function calculaDiferencaNaPrimeiraParcela($valorTotalTitulo, $totalParcelas, &$parcelas)
    {
        $diferença = $valorTotalTitulo - $totalParcelas;
        $valor     = $parcelas[1][ConstanteParametros::CHAVE_TIT_VALOR_DOCUMENTO];
        $parcelas[1][ConstanteParametros::CHAVE_TIT_VALOR_DOCUMENTO] = number_format(($valor + $diferença), 2, ".", "");
    }

    /**
     * Realiza a verificacao das regras para permitir ou nao a criacao do registro
     *
     * @param float $valor Valor total a ser deduzido conforme o $percentual_valor
     * @param float $percentual_valor Percentual a ser deduzido no $valor
     *
     * @return string
     */
    protected function calculaValorPercentual($valor, $percentual_valor)
    {
        return number_format(round(($valor * $percentual_valor / 100), 2), 2, ".", "");
    }

    /**
     * Verifica se pode atualizar o saldo, apos o pagamento
     *
     * @param array $parametros Parametros a serem inclusos no objeto
     * @param string $mensagemErro Mensagem que ira retornar para front-end
     *
     * @return boolean
     */
    protected function verificaValoresSaldo(&$parametros, &$mensagemErro)
    {
        $parametros[ConstanteParametros::CHAVE_VALOR_SALDO_CALCULADO] = $this->calculaValorParaAbatimentoNoSaldo($parametros);

        // if ($parametros[ConstanteParametros::CHAVE_TIT_VALOR_SALDO] < $parametros[ConstanteParametros::CHAVE_VALOR_SALDO_CALCULADO]) {
        //     $mensagemErro = "O valor do lançamento é maior do que o saldo restante do título. <br />Valor saldo R$ " . number_format($parametros[ConstanteParametros::CHAVE_TIT_VALOR_SALDO], 2, ',', '.') . " <br />Valor do lançamento R$ " . number_format($parametros[ConstanteParametros::CHAVE_VALOR_SALDO_CALCULADO], 2, ',', '.');
        //     return false;
        // }

        return true;
    }

    /**
     * Calcula o valor do saldo deduzido
     *
     * @param float $valor_saldo_titulo Saldo do titulo
     * @param float $valor_saldo_calculado Valor do saldo calculado para abatimento no titulo
     */
    protected function calculaValorSaldoDeduzido(float $valor_saldo_titulo, float $valor_saldo_calculado)
    {
        return round(($valor_saldo_titulo - $valor_saldo_calculado), 2);
    }

    /**
     * Busca o funcionarioId do usuario passado por parametro, para poder utilizar essa função<br>
     * Precisará ser configurado o repositorio "usuarioRepository"(\App\Entity\Principal\Usuario)<br>
     * Caso contrario irá dar erro
     *
     * @param int $usuarioId
     *
     * @return NULL|int
     */
    public function buscaFuncionarioIdDoUsuario($usuarioId)
    {
        $funcionarioLogadoId = null;
        $usuarioORM          = self::getUsuarioRepository()->find($usuarioId);
        $funcionarioORM      = $usuarioORM->getFuncionarios()->get(0);
        if (is_null($funcionarioORM) === false) {
            $funcionarioLogadoId = $funcionarioORM->getId();
        }

        return $funcionarioLogadoId;
    }

    /**
     * Executa a verificacao de aluno do AlunoBO
     *
     * @param array $parametros Array de parametros para realizar a validacao
     * @param string $mensagemErro Mensagem de erro a ser retornado ao front-end
     * @param null|\App\Entity\Principal\Aluno $alunoORM Ponteiro para retornar o objeto e ser incorporado no indice
     * @param boolean $retornaObjeto Flag para indicar se deve buscar o objeto ou não
     *
     * @return boolean
     */
    public function verificaAlunoExisteBO($parametros, &$mensagemErro, &$alunoORM=null, $retornaObjeto=true)
    {
        $bRetorno = false;
        if (isset($parametros[ConstanteParametros::CHAVE_ALUNO]) === true) {
            $mensagemRetorno = "";
            $bRetorno        = \App\BO\Principal\AlunoBO::verificaAlunoExiste($this->alunoRepository, $parametros[ConstanteParametros::CHAVE_ALUNO], $mensagemRetorno, $alunoORM, $retornaObjeto);
            if ($bRetorno === false) {
                $mensagemErro = "Aluno não encontrado na base de dados.";
            }
        }

        return $bRetorno;
    }

    /**
     * Executa a verificacao de tipo_visibilidade do TipoVisibilidadeBO
     *
     * @param array $parametros Array de parametros para realizar a validacao
     * @param string $mensagemErro Mensagem de erro a ser retornado ao front-end
     * @param null|\App\Entity\Principal\TipoVisibilidade $tipoVisibilidadeORM Ponteiro para retornar o objeto e ser incorporado no indice
     *
     * @return boolean
     */
    public function verificaTipoVisibilidadeExisteBO($parametros, &$mensagemErro, &$tipoVisibilidadeORM=null)
    {
        $bRetorno = false;
        if (isset($parametros[ConstanteParametros::CHAVE_TIPO_VISIBILIDADE]) === true) {
            $mensagemRetorno = "";
            $bRetorno        = \App\BO\Principal\TipoVisibilidadeBO::verificaTipoVisibilidadeExiste($this->tipoVisibilidadeRepository, $parametros[ConstanteParametros::CHAVE_TIPO_VISIBILIDADE], $mensagemRetorno, $tipoVisibilidadeORM);
            if ($bRetorno === false) {
                $mensagemErro = "TipoVisibilidade não encontrado na base de dados.";
            }
        }

        return $bRetorno;
    }

    /**
     * Verifica se o registro do EtapasConvenio existe
     *
     * @param array $parametros
     * @param string $mensagemErro
     * @param NULL|\App\Entity\Principal\EtapasConvenio $etapasConvenio
     *
     * @return boolean
     */
    public function verificaEtapasConvenioBO($parametros, &$mensagemErro, &$etapasConvenio=null)
    {
        $bRetorno = false;
        if (isset($parametros[ConstanteParametros::CHAVE_ETAPAS_CONVENIO]) === true) {
            $mensagemRetorno = "";
            $bRetorno        = \App\BO\Principal\EtapasConvenioBO::verificaEtapasConvenioExiste($this->etapasConvenioRepository, $parametros[ConstanteParametros::CHAVE_ETAPAS_CONVENIO], $mensagemRetorno, $etapasConvenio);
            if ($bRetorno === false) {
                $mensagemErro = "EtapasConvenio não encontrado na base de dados.";
            }
        }

        return $bRetorno;
    }

    /**
     * Verifica se o registro do NegociacaoParceriaWorkflow existe
     *
     * @param array $parametros
     * @param string $mensagemErro
     * @param NULL|\App\Entity\Principal\NegociacaoParceriaWorkflow $negociacaoParceriaWorkflow
     *
     * @return boolean
     */
    public function verificarNegociacaoParceriaWorkflowBO($parametros, &$mensagemErro, &$negociacaoParceriaWorkflow=null)
    {
        $bRetorno = false;
        if (isset($parametros[ConstanteParametros::CHAVE_NEGOCIACAO_PARCERIA_WORKFLOW]) === true) {
            $mensagemRetorno = "";
            $bRetorno        = \App\BO\Principal\NegociacaoParceriaWorkflowBO::verificarNegociacaoParceriaWorkflowExiste($this->negociacaoParceriaWorkflowRepository, $parametros[ConstanteParametros::CHAVE_NEGOCIACAO_PARCERIA_WORKFLOW], $mensagemRetorno, $negociacaoParceriaWorkflow);
            if ($bRetorno === false) {
                $mensagemErro = "NegociacaoParceriaWorkflow não encontrado na base de dados.";
            }
        }

        return $bRetorno;
    }

    /**
     * Verifica se o registro do CreditosPersonal existe
     *
     * @param array $parametros
     * @param string $mensagemErro
     * @param NULL|\App\Entity\Principal\CreditosPersonal $creditosPersonal
     *
     * @return boolean
     */
    public function verificaCreditosPersonalBO($parametros, &$mensagemErro, &$creditosPersonal=null)
    {
        $bRetorno = false;
        if (isset($parametros[ConstanteParametros::CHAVE_CREDITOS_PERSONAL]) === true) {
            $mensagemRetorno = "";
            $bRetorno        = \App\BO\Principal\CreditosPersonalBO::verificaCreditosPersonalExiste($this->creditosPersonalRepository, $parametros[ConstanteParametros::CHAVE_CREDITOS_PERSONAL], $mensagemRetorno, $creditosPersonal);
            if ($bRetorno === false) {
                $mensagemErro = "CreditosPersonal não encontrado na base de dados.";
            }
        }

        return $bRetorno;
    }

    /**
     * Verifica se o registro do AlunoDiario existe
     *
     * @param array $parametros
     * @param string $mensagemErro
     * @param NULL|\App\Entity\Principal\AlunoDiario $alunoDiarioORM
     *
     * @return boolean
     */
    public function verificaAlunoDiarioBO($parametros, &$mensagemErro, &$alunoDiarioORM=null)
    {
        $bRetorno = false;
        if (isset($parametros[ConstanteParametros::CHAVE_ALUNO_DIARIO]) === true) {
            $mensagemRetorno = "";
            $bRetorno        = \App\BO\Principal\AlunoDiarioBO::verificaAlunoDiarioExiste($this->alunoDiarioRepository, $parametros[ConstanteParametros::CHAVE_ALUNO_DIARIO], $mensagemRetorno, $alunoDiarioORM);
            if ($bRetorno === false) {
                $mensagemErro = "AlunoDiario não encontrado na base de dados.";
            }
        }

        return $bRetorno;
    }

    /**
     * Verifica se o registro do BonusClass existe
     *
     * @param array $parametros
     * @param string $mensagemErro
     * @param NULL|\App\Entity\Principal\BonusClass $bonusClassORM
     *
     * @return boolean
     */
    public function verificaBonusClassBO($parametros, &$mensagemErro, &$bonusClassORM=null)
    {
        $bRetorno = false;
        if (isset($parametros[ConstanteParametros::CHAVE_BONUS_CLASS]) === true) {
            $mensagemRetorno = "";
            $bRetorno        = \App\BO\Principal\BonusClassBO::verificarBonusClassExiteBO($this->bonusClassRepository, $parametros[ConstanteParametros::CHAVE_BONUS_CLASS], $mensagemRetorno, $bonusClassORM);
            if ($bRetorno === false) {
                $mensagemErro = "BonusClass não encontrado na base de dados.";
            }
        }

        return $bRetorno;
    }

    /**
     * Verifica se o registro do AlunoAvaliacao existe
     *
     * @param array $parametros
     * @param string $mensagemErro
     * @param NULL|\App\Entity\Principal\AlunoAvaliacao $alunoAvaliacaoORM
     *
     * @return boolean
     */
    public function verificaAlunoAvaliacaoBO($parametros, &$mensagemErro, &$alunoAvaliacaoORM=null)
    {
        $bRetorno = false;
        if (isset($parametros[ConstanteParametros::CHAVE_ALUNO_AVALIACAO]) === true) {
            $mensagemRetorno = "";
            $bRetorno        = \App\BO\Principal\AlunoAvaliacaoBO::verificaAlunoAvaliacaoExiste($this->alunoAvaliacaoRepository, $parametros[ConstanteParametros::CHAVE_ALUNO_AVALIACAO], $mensagemRetorno, $alunoAvaliacaoORM);
            if ($bRetorno === false) {
                $mensagemErro = "AlunoAvaliacao não encontrado na base de dados.";
            }
        }

        return $bRetorno;
    }

    /**
     * Verifica se o registro do AlunoAvaliacaoConceitual existe
     *
     * @param array $parametros
     * @param string $mensagemErro
     * @param string $chavePesquisa
     * @param NULL|\App\Entity\Principal\AlunoAvaliacaoConceitual $alunoAvaliacaoConceitualORM
     *
     * @return boolean
     */
    public function verificaAlunoAvaliacaoConceitualBO($parametros, &$mensagemErro, $chavePesquisa, &$alunoAvaliacaoConceitualORM=null)
    {
        $bRetorno = false;
        if (isset($parametros[$chavePesquisa]) === true) {
            $mensagemRetorno = "";
            $bRetorno        = \App\BO\Principal\AlunoAvaliacaoConceitualBO::verificaAlunoAvaliacaoConceitualExiste($this->alunoAvaliacaoConceitualRepository, $parametros[$chavePesquisa], $mensagemRetorno, $alunoAvaliacaoConceitualORM);
            if ($bRetorno === false) {
                $mensagemErro .= "AlunoAvaliacaoConceitual não encontrado na base de dados.";
            }
        }

        return $bRetorno;
    }

    /**
     * Verifica se o registro do Nao fechamento matricula existe
     *
     * @param array $parametros
     * @param string $mensagemErro
     * @param NULL|\App\Entity\Principal\MotivoNaoFechamentoMatricula $motivoFechamentoMatricula
     *
     * @return boolean
     */
    public function verificaMotivoNaoFechamentoMatriculaBO($parametros, &$mensagemErro, &$motivoFechamentoMatricula=null)
    {
        $bRetorno = false;
        if (isset($parametros[ConstanteParametros::CHAVE_MOTIVO_NAO_FECHAMENTO_MATRICULA]) === true) {
            $mensagemRetorno = "";
            $bRetorno        = \App\BO\Principal\MotivoNaoFechamentoMatriculaBO::verificaMotivoNaoFechamentoMatriculaExiste($this->motivoNaoFechamentoMatriculaRepository, $parametros[ConstanteParametros::CHAVE_MOTIVO_NAO_FECHAMENTO_MATRICULA], $mensagemRetorno, $motivoFechamentoMatricula);
            if ($bRetorno === false) {
                $mensagemErro = "MotivoNaoFechamentoMatricula não encontrado na base de dados.";
            }
        }

        return $bRetorno;
    }

    /**
     * Executa a verificacao de TipoItem
     *
     * @param array $parametros Array de parametros para realizar a validacao
     * @param string $mensagemErro Mensagem de erro a ser retornado ao front-end
     * @param string $chavePesquisa Indice para realizar a busca
     * @param null|\App\Entity\Principal\TipoItem $tipoItemORM Ponteiro para retornar o objeto e ser incorporado no indice
     *
     * @return boolean
     */
    public function verificaTipoItemExisteBO($parametros, &$mensagemErro, $chavePesquisa=ConstanteParametros::CHAVE_TIPO_ITEM, &$tipoItemORM=null)
    {
        $bRetorno = false;
        if (isset($parametros[$chavePesquisa]) === true) {
            $mensagemRetorno = "";
            $bRetorno        = \App\BO\Principal\TipoItemBO::verificaTipoItemExiste($this->tipoItemRepository, $parametros[$chavePesquisa], $mensagemRetorno, $tipoItemORM);
            if ($bRetorno === false) {
                $mensagemErro = "TipoItem não encontrado na base de dados.";
            }
        }

        return $bRetorno;
    }

    /**
     * Executa a verificacao de OrigemOcorrenciaPorTipo
     *
     * @param array $parametros Array de parametros para realizar a validacao
     * @param string $mensagemErro Mensagem de erro a ser retornado ao front-end
     * @param null|\App\Entity\Principal\OrigemOcorrencia $retornoORM
     *
     * @return boolean
     */
    public function verificaOrigemOcorrenciaPorTipoOrigemExiste($parametros, &$mensagemErro, &$retornoORM)
    {
        $bRetorno = false;
        if (isset($parametros[ConstanteParametros::CHAVE_ORIGEM_OCORRENCIA_TIPO]) === true) {
            $bRetorno = \App\BO\Principal\OrigemOcorrenciaBO::verificaOrigemOcorrenciaPorTipoExiste($this->origemOcorrenciaRepository, $parametros[ConstanteParametros::CHAVE_ORIGEM_OCORRENCIA_TIPO], $mensagemErro, $retornoORM);
            if ($bRetorno === false) {
                $mensagemErro = "OrigemOcorrencia não encontrado na base de dados.";
            }
        }

        return $bRetorno;
    }

    /**
     * Executa a verificacao de ModuloBO
     *
     * @param array $parametros Array de parametros para realizar a validacao
     * @param string $mensagemErro Mensagem de erro a ser retornado ao front-end
     * @param null|\App\Entity\Principal\Modulo $moduloORM Ponteiro para retornar o objeto e ser incorporado no indice
     *
     * @return boolean
     */
    public function verificaModuloExisteBO($parametros, &$mensagemErro, &$moduloORM=null)
    {
        $bRetorno = false;
        if (isset($parametros[ConstanteParametros::CHAVE_MODULO]) === true) {
            $mensagemRetorno = "";
            $bRetorno        = \App\BO\Principal\ModuloBO::verificaModuloExiste($this->moduloRepository, $parametros[ConstanteParametros::CHAVE_MODULO], $mensagemRetorno, $moduloORM);
            if ($bRetorno === false) {
                $mensagemErro = "Modulo não encontrado na base de dados.";
            }
        }

        return $bRetorno;
    }

    /**
     * Executa a verificacao de AcaoSistemaBO
     *
     * @param array $parametros Array de parametros para realizar a validacao
     * @param string $mensagemErro Mensagem de erro a ser retornado ao front-end
     * @param null|\App\Entity\Principal\AcaoSistema $acaoSistemaORM Ponteiro para retornar o objeto e ser incorporado no indice
     *
     * @return boolean
     */
    public function verificaAcaoSistemaExisteBO($parametros, &$mensagemErro, &$acaoSistemaORM=null)
    {
        $bRetorno = false;
        if (isset($parametros[ConstanteParametros::CHAVE_ACAO_SISTEMA]) === true) {
            $mensagemRetorno = "";
            $bRetorno        = \App\BO\Principal\AcaoSistemaBO::verificaAcaoSistemaExiste($this->acaoSistemaRepository, $parametros[ConstanteParametros::CHAVE_ACAO_SISTEMA], $mensagemRetorno, $acaoSistemaORM);
            if ($bRetorno === false) {
                $mensagemErro = "AcaoSistema não encontrado na base de dados.";
            }
        }

        return $bRetorno;
    }

    /**
     * Executa a verificacao de HorarioBO
     *
     * @param array $parametros Array de parametros para realizar a validacao
     * @param string $mensagemErro Mensagem de erro a ser retornado ao front-end
     * @param null|\App\Entity\Principal\Horario $horarioORM Ponteiro para retornar o objeto e ser incorporado no indice
     * @param false|true $bOrdenaHorarioAulas para trazer os horarios aulas ordenados
     *
     * @return boolean
     */
    public function verificaHorarioExisteBO($parametros, &$mensagemErro, &$horarioORM=null, $bOrdenaHorarioAulas=false)
    {
        $bRetorno = false;
        if (isset($parametros[ConstanteParametros::CHAVE_HORARIO]) === true) {
            $mensagemRetorno = "";
            $bRetorno        = \App\BO\Principal\HorarioBO::verificaHorarioExiste($this->horarioRepository, $parametros[ConstanteParametros::CHAVE_HORARIO], $mensagemRetorno, $horarioORM, $bOrdenaHorarioAulas);
            if ($bRetorno === false) {
                $mensagemErro = "Horario não encontrado na base de dados.";
            }
        }

        return $bRetorno;
    }

    /**
     * Executa a verificacao de OperadoraCartao
     *
     * @param array $parametros Array de parametros para realizar a validacao
     * @param string $mensagemErro Mensagem de erro a ser retornado ao front-end
     * @param null|\App\Entity\Principal\OperadoraCartao $operadoraCartaoORM Ponteiro para retornar o objeto e ser incorporado no indice
     *
     * @return boolean
     */
    public function verificaOperadoraCartaoExisteBO($parametros, &$mensagemErro, &$operadoraCartaoORM=null)
    {
        $bRetorno = false;
        if (isset($parametros[ConstanteParametros::CHAVE_OPERADORA_CARTAO]) === true) {
            $mensagemRetorno = "";
            $bRetorno        = \App\BO\Principal\OperadoraCartaoBO::verificaOperadoraCartaoExiste($this->operadoraCartaoRepository, $parametros[ConstanteParametros::CHAVE_OPERADORA_CARTAO], $mensagemRetorno, $operadoraCartaoORM);
            if ($bRetorno === false) {
                $mensagemErro = "Operadora Cartao não encontrado na base de dados.";
            }
        }

        return $bRetorno;
    }

    /**
     * Executa a verificacao de ParcelamentoOperadoraCartao
     *
     * @param array $parametros Array de parametros para realizar a validacao
     * @param string $mensagemErro Mensagem de erro a ser retornado ao front-end
     * @param null|\App\Entity\Principal\ParcelamentoOperadoraCartao $parcelamentoOperadoraCartaoORM Ponteiro para retornar o objeto e ser incorporado no indice
     *
     * @return boolean
     */
    public function verificaParcelamentoOperadoraCartaoExisteBO($parametros, &$mensagemErro, &$parcelamentoOperadoraCartaoORM=null)
    {
        $bRetorno = false;
        if (isset($parametros[ConstanteParametros::CHAVE_PARCELAMENTO_OPERADORA_CARTAO]) === true) {
            $mensagemRetorno = "";
            $bRetorno        = \App\BO\Principal\ParcelamentoOperadoraCartaoBO::verificaParcelamentoOperadoraCartaoExiste($this->parcelamentoOperadoraCartaoRepository, $parametros[ConstanteParametros::CHAVE_PARCELAMENTO_OPERADORA_CARTAO], $mensagemRetorno, $parcelamentoOperadoraCartaoORM);
            if ($bRetorno === false) {
                $mensagemErro = "ParcelamentoOperadoraCartao não encontrado na base de dados.";
            }
        }

        return $bRetorno;
    }

    /**
     * Executa a verificacao de ParcelaParcelamento
     *
     * @param array $parametros Array de parametros para realizar a validacao
     * @param string $mensagemErro Mensagem de erro a ser retornado ao front-end
     * @param null|\App\Entity\Principal\ParcelaParcelamento $parcelaParcelamentoORM Ponteiro para retornar o objeto e ser incorporado no indice
     *
     * @return boolean
     */
    public function verificaParcelaParcelamentoExisteBO($parametros, &$mensagemErro, &$parcelaParcelamentoORM=null)
    {
        $bRetorno = false;
        if (isset($parametros[ConstanteParametros::CHAVE_PARCELA_PARCELAMENTO]) === true) {
            $mensagemRetorno = "";
            $bRetorno        = \App\BO\Principal\ParcelaParcelamentoBO::verificaParcelaParcelamentoExiste($this->parcelaParcelamentoRepository, $parametros[ConstanteParametros::CHAVE_PARCELA_PARCELAMENTO], $mensagemRetorno, $parcelaParcelamentoORM);
            if ($bRetorno === false) {
                $mensagemErro = "ParcelaParcelamento não encontrado na base de dados.";
            }
        }

        return $bRetorno;
    }

    /**
     * Executa a verificacao de TipoAgendamentoBO
     *
     * @param array $parametros Array de parametros para realizar a validacao
     * @param string $mensagemErro Mensagem de erro a ser retornado ao front-end
     * @param null|\App\Entity\Principal\TipoAgendamento $tipoAgendamentoORM Ponteiro para retornar o objeto e ser incorporado no indice
     *
     * @return boolean
     */
    public function verificaTipoAgendamentoExisteBO($parametros, &$mensagemErro, &$tipoAgendamentoORM=null)
    {
        $bRetorno = false;
        if (isset($parametros[ConstanteParametros::CHAVE_TIPO_AGENDAMENTO]) === true) {
            $mensagemRetorno = "";
            $bRetorno        = \App\BO\Principal\TipoAgendamentoBO::verificaTipoAgendamentoExiste($this->tipoAgendamentoRepository, $parametros[ConstanteParametros::CHAVE_TIPO_AGENDAMENTO], $mensagemRetorno, $tipoAgendamentoORM, true);
            if ($bRetorno === false) {
                $mensagemErro = "Tipo de agendamento não encontrado na base de dados.";
            }
        }

        return $bRetorno;
    }

    /**
     * Executa a verificacao de AgendamentoPersonalBO
     *
     * @param array $parametros Array de parametros para realizar a validacao
     * @param string $mensagemErro Mensagem de erro a ser retornado ao front-end
     * @param null|\App\Entity\Principal\AgendamentoPersonal $agendamentoPersonalORM Ponteiro para retornar o objeto e ser incorporado no indice
     *
     * @return boolean
     */
    public function verificaExisteAgendamentoPersonalBO($parametros, $mensagemErro, &$agendamentoPersonalORM=null)
    {
        $bRetorno = false;
        if (isset($parametros[ConstanteParametros::CHAVE_AGENDAMENTO_PERSONAL]) === true) {
            $mensagemRetorno = "";
            $bRetorno        = \App\BO\Principal\AgendamentoPersonalBO::verificaAgendamentoPersonalExiste($this->agendamentoPersonalRepository, $parametros[ConstanteParametros::CHAVE_AGENDAMENTO_PERSONAL], $mensagemRetorno, $agendamentoPersonalORM);
            if ($bRetorno === false) {
                $mensagemErro = "Agendamento Personal não encontrado na base de dados.";
            }
        }

        return $bRetorno;
    }

    /**
     * Executa a verificacao de TurmaAulaBO
     *
     * @param array $parametros Array de parametros para realizar a validacao
     * @param string $mensagemErro Mensagem de erro a ser retornado ao front-end
     * @param null|\App\Entity\Principal\TurmaAula $turmaAulaORM Ponteiro para retornar o objeto e ser incorporado no indice
     *
     * @return boolean
     */
    public function verificaTurmaAulaExisteBO($parametros, &$mensagemErro, &$turmaAulaORM=null)
    {
        $bRetorno = false;
        if (isset($parametros[ConstanteParametros::CHAVE_TURMA_AULA]) === true) {
            $mensagemRetorno = "";
            $bRetorno        = \App\BO\Principal\TurmaAulaBO::verificaTurmaAulaExiste($this->turmaAulaRepository, $parametros[ConstanteParametros::CHAVE_TURMA_AULA], $mensagemRetorno, $turmaAulaORM);
            if ($bRetorno === false) {
                $mensagemErro = "TurmaAula não encontrada na base de dados.";
            }
        }

        return $bRetorno;
    }

    /**
     * Executa a verificacao de AgendaComercialBO
     *
     * @param array $parametros Array de parametros para realizar a validacao
     * @param string $mensagemErro Mensagem de erro a ser retornado ao front-end
     * @param null|\App\Entity\Principal\AgendaComercial $agendaComercialORM Ponteiro para retornar o objeto e ser incorporado no indice
     * @param boolean $retornaObjeto se deve retornar o objeto ORM ou apenas um array
     *
     * @return boolean
     */
    public function verificaAgendaComercialExisteBO($parametros, &$mensagemErro, &$agendaComercialORM=null, $retornaObjeto=false)
    {
        $bRetorno = false;
        if (isset($parametros[ConstanteParametros::CHAVE_AGENDA_COMERCIAL]) === true) {
            $mensagemRetorno = "";
            $bRetorno        = \App\BO\Principal\AgendaComercialBO::verificaAgendaComercialExiste($this->agendaComercialRepository, $parametros[ConstanteParametros::CHAVE_AGENDA_COMERCIAL], $mensagemRetorno, $agendaComercialORM, $retornaObjeto);
            if ($bRetorno === false) {
                $mensagemErro = "Agenda comercial não encontrada na base de dados.";
            }
        }

        return $bRetorno;
    }

    /**
     * Executa a verificacao de TipoContatoBO
     *
     * @param array $parametros Array de parametros para realizar a validacao
     * @param string $mensagemErro Mensagem de erro a ser retornado ao front-end
     * @param null|\App\Entity\Principal\TipoContato $tipoContatoORM Ponteiro para retornar o objeto e ser incorporado no indice
     *
     * @return boolean
     */
    public function verificaTipoContatoExisteBO($parametros, &$mensagemErro, &$tipoContatoORM=null)
    {
        $bRetorno = false;
        if (isset($parametros[ConstanteParametros::CHAVE_TIPO_CONTATO]) === true) {
            $mensagemRetorno = "";
            $bRetorno        = \App\BO\Principal\TipoContatoBO::verificaTipoContatoExiste($this->tipoContatoRepository, $parametros[ConstanteParametros::CHAVE_TIPO_CONTATO], $mensagemRetorno, $tipoContatoORM);
            if ($bRetorno === false) {
                $mensagemErro = "Tipo Contato não encontrado na base de dados.";
            }
        }

        return $bRetorno;
    }

    /**
     * Executa a verificacao de OcorrenciaAcademicaBO
     *
     * @param array $parametros Array de parametros para realizar a validacao
     * @param string $mensagemErro Mensagem de erro a ser retornado ao front-end
     * @param null|\App\Entity\Principal\OcorrenciaAcademica $ocorrenciaAcademicaORM Ponteiro para retornar o objeto e ser incorporado no indice
     *
     * @return boolean
     */
    public function verificaOcorrenciaAcademicaExisteBO($parametros, &$mensagemErro, &$ocorrenciaAcademicaORM=null)
    {
        $bRetorno = false;

        if (isset($parametros[ConstanteParametros::CHAVE_OCORRENCIA_ACADEMICA]) === true) {
            $mensagemRetorno = "";
            $bRetorno        = \App\BO\Principal\OcorrenciaAcademicaBO::verificaOcorrenciaAcademicaExiste($this->ocorrenciaAcademicaRepository, $parametros[ConstanteParametros::CHAVE_OCORRENCIA_ACADEMICA], $mensagemRetorno, $ocorrenciaAcademicaORM);
            if ($bRetorno === false) {
                $mensagemErro = "Tipo ocorrencia academica não encontrado na base de dados.";
            }
        }

        return $bRetorno;
    }

    /**
     * Executa a verificacao de TipoOcorrenciaBO
     *
     * @param array $parametros Array de parametros para realizar a validacao
     * @param string $mensagemErro Mensagem de erro a ser retornado ao front-end
     * @param null|\App\Entity\Principal\TipoOcorrencia $tipoOcorrenciaORM Ponteiro para retornar o objeto e ser incorporado no indice
     *
     * @return boolean
     */
    public function verificaTipoOcorrenciaExisteBO($parametros, &$mensagemErro, &$tipoOcorrenciaORM=null)
    {
        $bRetorno = false;
        if (isset($parametros[ConstanteParametros::CHAVE_TIPO_OCORRENCIA]) === true) {
            $mensagemRetorno = "";
            $bRetorno        = \App\BO\Principal\TipoOcorrenciaBO::verificaTipoOcorrenciaExiste($this->tipoOcorrenciaRepository, $parametros[ConstanteParametros::CHAVE_TIPO_OCORRENCIA], $mensagemRetorno, $tipoOcorrenciaORM);
            if ($bRetorno === false) {
                $mensagemErro = "Tipo ocorrencia não encontrado na base de dados.";
            }
        }

        return $bRetorno;
    }


    /**
     * Executa a verificacao de TipoProspeccaoBO
     *
     * @param array $parametros Array de parametros para realizar a validacao
     * @param string $mensagemErro Mensagem de erro a ser retornado ao front-end
     * @param null|\App\Entity\Principal\TipoProspeccao $tipoProspeccaoORM Ponteiro para retornar o objeto e ser incorporado no indice
     *
     * @return boolean
     */
    public function verificaTipoProspeccaoExisteBO($parametros, &$mensagemErro, &$tipoProspeccaoORM=null)
    {
        $bRetorno = false;
        if (isset($parametros[ConstanteParametros::CHAVE_TIPO_PROSPECCAO]) === true) {
            $mensagemRetorno = "";
            $bRetorno        = \App\BO\Principal\TipoProspeccaoBO::verificaTipoProspeccaoExiste($this->tipoProspeccaoRepository, $parametros[ConstanteParametros::CHAVE_TIPO_PROSPECCAO], $mensagemRetorno, $tipoProspeccaoORM);
            if ($bRetorno === false) {
                $mensagemErro = "Tipo prospeccao não encontrado na base de dados.";
            }
        }

        return $bRetorno;
    }

    /**
     * Executa a verificacao de ModalidadeTumaBO
     *
     * @param array $parametros Array de parametros para realizar a validacao
     * @param string $mensagemErro Mensagem de erro a ser retornado ao front-end
     * @param null|\App\Entity\Principal\ModalidadeTurma $modalidadeORM Ponteiro para retornar o objeto e ser incorporado no indice
     *
     * @return boolean
     */
    public function verificaModalidadeTurmaExisteBO($parametros, &$mensagemErro, &$modalidadeORM=null)
    {
        $bRetorno = false;
        if (isset($parametros[ConstanteParametros::CHAVE_MODALIDADE_TURMA]) === true) {
            $mensagemRetorno = "";
            $bRetorno        = \App\BO\Principal\ModalidadeTurmaBO::modalidadeTurmaExiste($this->modalidadeTurmaRepository, $parametros[ConstanteParametros::CHAVE_MODALIDADE_TURMA], $mensagemRetorno, $modalidadeORM);
            if ($bRetorno === false) {
                $mensagemErro = "Modalidade não encontrada na base de dados.";
            }
        }

        return $bRetorno;
    }

    /**
     * Executa a verificacao de MotivoNaoFechamentoConvenio
     *
     * @param array $parametros Array de parametros para realizar a validacao
     * @param string $mensagemErro Mensagem de erro a ser retornado ao front-end
     * @param null|\App\Entity\Principal\MotivoNaoFechamentoConvenio $motivoNaoFechamentoConvenioORM Ponteiro para retornar o objeto e ser incorporado no indice
     *
     * @return boolean
     */
    public function verificaMotivoNaoFechamentoConvenioExisteBO($parametros, &$mensagemErro, &$motivoNaoFechamentoConvenioORM=null)
    {
        $bRetorno = false;
        if (isset($parametros[ConstanteParametros::CHAVE_MOTIVO_NAO_FECHAMENTO_CONVENIO]) === true) {
            $mensagemRetorno = "";
            $bRetorno        = \App\BO\Principal\MotivoNaoFechamentoConvenioBO::verificaMotivoNaoFechamentoConvenioExiste($this->motivoNaoFechamentoConvenioRepository, $parametros[ConstanteParametros::CHAVE_MOTIVO_NAO_FECHAMENTO_CONVENIO], $mensagemRetorno, $motivoNaoFechamentoConvenioORM);
            if ($bRetorno === false) {
                $mensagemErro = "MotivoNaoFechamentoConvenio não encontrado na base de dados.";
            }
        }

        return $bRetorno;
    }

    /**
     * Executa a verificacao de SegmentoEmpresaConvenio
     *
     * @param array $parametros Array de parametros para realizar a validacao
     * @param string $mensagemErro Mensagem de erro a ser retornado ao front-end
     * @param null|\App\Entity\Principal\SegmentoEmpresaConvenio $segmentoEmpresaConvenioORM Ponteiro para retornar o objeto e ser incorporado no indice
     *
     * @return boolean
     */
    public function verificaSegmentoEmpresaConvenioExisteBO($parametros, &$mensagemErro, &$segmentoEmpresaConvenioORM=null)
    {
        $bRetorno = false;
        if (isset($parametros[ConstanteParametros::CHAVE_SEGMENTO_EMPRESA_CONVENIO]) === true) {
            $mensagemRetorno = "";
            $bRetorno        = \App\BO\Principal\SegmentoEmpresaConvenioBO::verificaSegmentoEmpresaConvenioExiste($this->segmentoEmpresaConvenioRepository, $parametros[ConstanteParametros::CHAVE_SEGMENTO_EMPRESA_CONVENIO], $mensagemRetorno, $segmentoEmpresaConvenioORM);
            if ($bRetorno === false) {
                $mensagemErro = "SegmentoEmpresaConvenio não encontrado na base de dados.";
            }
        }

        return $bRetorno;
    }

    /**
     * Executa a verificacao de FormularioFollowUpBO
     *
     * @param array $parametros Array de parametros para realizar a validacao
     * @param string $mensagemErro Mensagem de erro a ser retornado ao front-end
     * @param null|\App\Entity\Principal\FormularioFollowUp $formularioFollowUpORM Ponteiro para retornar o objeto e ser incorporado no indice
     *
     * @return boolean
     */
    public function verificaFormularioFollowUpExisteBO($parametros, &$mensagemErro, &$formularioFollowUpORM=null)
    {
        $bRetorno = false;
        if (isset($parametros[ConstanteParametros::CHAVE_FORMULARIO_FOLLOW_UP]) === true) {
            $mensagemRetorno = "";
            $bRetorno        = \App\BO\Principal\FormularioFollowUpBO::verificaFormularioFollowUpExiste($this->formularioFollowUpRepository, $parametros[ConstanteParametros::CHAVE_FORMULARIO_FOLLOW_UP], $mensagemRetorno, $formularioFollowUpORM);
            if ($bRetorno === false) {
                $mensagemErro = "FormularioFollowUp não encontrado na base de dados.";
            }
        }

        return $bRetorno;
    }

    /**
     * Executa a verificacao de FormularioFollowUpCamposBO
     *
     * @param array $parametros Array de parametros para realizar a validacao
     * @param string $mensagemErro Mensagem de erro a ser retornado ao front-end
     * @param null|\App\Entity\Principal\FormularioFollowUpCampos $formularioFollowUpCamposORM Ponteiro para retornar o objeto e ser incorporado no indice
     *
     * @return boolean
     */
    public function verificaFormularioFollowUpCamposExisteBO($parametros, &$mensagemErro, &$formularioFollowUpCamposORM=null)
    {
        $bRetorno = false;
        if (isset($parametros[ConstanteParametros::CHAVE_FORMULARIO_FOLLOW_UP_CAMPOS]) === true) {
            $mensagemRetorno = "";
            $bRetorno        = \App\BO\Principal\FormularioFollowUpCamposBO::verificaFormularioFollowUpCamposExiste($this->formularioFollowUpCamposRepository, $parametros[ConstanteParametros::CHAVE_FORMULARIO_FOLLOW_UP_CAMPOS], $mensagemRetorno, $formularioFollowUpCamposORM);
            if ($bRetorno === false) {
                $mensagemErro = "FormularioFollowUpCampos não encontrado na base de dados.";
            }
        }

        return $bRetorno;
    }

    /**
     * Executa a verificacao de LivroBO
     *
     * @param array $parametros Array de parametros para realizar a validacao
     * @param string $mensagemErro Mensagem de erro a ser retornado ao front-end
     * @param null|\App\Entity\Principal\Livro $livroORM Ponteiro para retornar o objeto e ser incorporado no indice
     *
     * @return boolean
     */
    public function verificaLivroExisteBO($parametros, &$mensagemErro, &$livroORM=null)
    {
        $bRetorno = false;
        if (isset($parametros[ConstanteParametros::CHAVE_LIVRO]) === true) {
            $mensagemRetorno = "";
            $bRetorno        = \App\BO\Principal\LivroBO::verificaLivroExiste($this->livroRepository, $parametros[ConstanteParametros::CHAVE_LIVRO], $mensagemRetorno, $livroORM);
            if ($bRetorno === false) {
                $mensagemErro = "Livro não encontrado na base de dados.";
            }
        }

        return $bRetorno;
    }

    /**
     * Executa a verificacao de SalaFranqueadaBO
     *
     * @param array $parametros Array de parametros para realizar a validacao
     * @param string $mensagemErro Mensagem de erro a ser retornado ao front-end
     * @param null|\App\Entity\Principal\SalaFranqueada $salaFranqueadaORM Ponteiro para retornar o objeto e ser incorporado no indice
     *
     * @return boolean
     */
    public function verificaSalaFranqueadaExisteBO($parametros, &$mensagemErro, &$salaFranqueadaORM=null)
    {
        $bRetorno = false;
        if (isset($parametros[ConstanteParametros::CHAVE_SALA_FRANQUEADA]) === true) {
            $mensagemRetorno = "";
            $bRetorno        = \App\BO\Principal\SalaFranqueadaBO::verificaSalaFranqueadaExiste($this->salaFranqueadaRepository, $parametros[ConstanteParametros::CHAVE_SALA_FRANQUEADA], $mensagemRetorno, $salaFranqueadaORM);
            if ($bRetorno === false) {
                $mensagemErro = "SalaFranqueada não encontrado na base de dados.";
            }
        }

        return $bRetorno;
    }

    /**
     * Executa a verificacao de AtividadeExtraBO
     *
     * @param array $parametros Array de parametros para realizar a validacao
     * @param string $mensagemErro Mensagem de erro a ser retornado ao front-end
     * @param null|\App\Entity\Principal\AtividadeExtra $atividadeExtraORM Ponteiro para retornar o objeto e ser incorporado no indice
     *
     * @return boolean
     */
    public function verificaAtividadeExtraExisteBO($parametros, &$mensagemErro, &$atividadeExtraORM=null)
    {
        $bRetorno = false;
        if (isset($parametros[ConstanteParametros::CHAVE_ATIVIDADE_EXTRA]) === true) {
            $mensagemRetorno = "";
            $bRetorno        = \App\BO\Principal\AtividadeExtraBO::verificaAtividadeExtraExiste($this->atividadeExtraRepository, $parametros[ConstanteParametros::CHAVE_ATIVIDADE_EXTRA], $mensagemRetorno, $atividadeExtraORM);
            if ($bRetorno === false) {
                $mensagemErro = "AtividadeExtra não encontrado na base de dados.";
            }
        }

        return $bRetorno;
    }

    /**
     * Executa a verificacao de AlunoAtividadeExtraBO
     *
     * @param array $parametros Array de parametros para realizar a validacao
     * @param string $mensagemErro Mensagem de erro a ser retornado ao front-end
     * @param null|\App\Entity\Principal\AlunoAtividadeExtra $alunoAtividadeExtraORM Ponteiro para retornar o objeto e ser incorporado no indice
     *
     * @return boolean
     */
    public function verificaAlunoAtividadeExtraExisteBO($parametros, &$mensagemErro, &$alunoAtividadeExtraORM=null)
    {
        $bRetorno = false;
        if (isset($parametros[ConstanteParametros::CHAVE_ALUNO_ATIVIDADE_EXTRA]) === true) {
            $mensagemRetorno = "";
            $bRetorno        = \App\BO\Principal\AlunoAtividadeExtraBO::verificaAlunoAtividadeExtraExiste($this->alunoAtividadeExtraRepository, $parametros[ConstanteParametros::CHAVE_ALUNO_ATIVIDADE_EXTRA], $mensagemRetorno, $alunoAtividadeExtraORM);
            if ($bRetorno === false) {
                $mensagemErro = "AtividadeExtra não encontrado na base de dados.";
            }
        }

        return $bRetorno;
    }

    /**
     * Executa a verificacao de SalaFranqueadaBO
     *
     * @param array $parametros Array de parametros para realizar a validacao
     * @param string $mensagemErro Mensagem de erro a ser retornado ao front-end
     * @param null|\App\Entity\Principal\Semestre $semestreORM Ponteiro para retornar o objeto e ser incorporado no indice
     *
     * @return boolean
     */
    public function verificaSemestreExisteBO($parametros, &$mensagemErro, &$semestreORM=null)
    {
        $bRetorno = false;
        if (isset($parametros[ConstanteParametros::CHAVE_SEMESTRE]) === true) {
            $bRetorno = \App\BO\Principal\SemestreBO::verificaSemestreExiste($this->semestreRepository, $parametros[ConstanteParametros::CHAVE_SEMESTRE], $mensagemErro, $semestreORM);
            if ($bRetorno === false) {
                $mensagemErro = "Semestre não encontrado na base de dados.";
            }
        }

        return $bRetorno;
    }

    /**
     * Executa a verificacao de WorkflowBO
     *
     * @param array $parametros Array de parametros para realizar a validacao
     * @param string $mensagemErro Mensagem de erro a ser retornado ao front-end
     * @param null|\App\Entity\Principal\Workflow $workflowORM Ponteiro para retornar o objeto e ser incorporado no indice
     *
     * @return boolean
     */
    public function verificaWorkflowExisteBO($parametros, &$mensagemErro, &$workflowORM=null)
    {
        $bRetorno = false;
        if (isset($parametros[ConstanteParametros::CHAVE_WORKFLOW]) === true) {
            $bRetorno = \App\BO\Principal\WorkflowBO::verificaWorkflowExiste($this->workflowRepository, $parametros[ConstanteParametros::CHAVE_WORKFLOW], $mensagemErro, $workflowORM);
            if ($bRetorno === false) {
                $mensagemErro = "Workflow não encontrado na base de dados.";
            }
        }

        return $bRetorno;
    }

    /**
     * Executa a verificacao de WorkflowAcaoBO
     *
     * @param array $parametros Array de parametros para realizar a validacao
     * @param string $mensagemErro Mensagem de erro a ser retornado ao front-end
     * @param null|\App\Entity\Principal\WorkflowAcao $workflowAcaoORM Ponteiro para retornar o objeto e ser incorporado no indice
     *
     * @return boolean
     */
    public function verificaWorkflowAcaoExisteBO($parametros, &$mensagemErro, &$workflowAcaoORM=null)
    {
        $bRetorno = false;
        if (isset($parametros[ConstanteParametros::CHAVE_WORKFLOW_ACAO]) === true) {
            $bRetorno = \App\BO\Principal\WorkflowAcaoBO::verificaWorkflowAcaoExiste($this->workflowAcaoRepository, $parametros[ConstanteParametros::CHAVE_WORKFLOW_ACAO], $mensagemErro, $workflowAcaoORM);
            if ($bRetorno === false) {
                $mensagemErro = "WorkflowAcao não encontrado na base de dados.";
            }
        }

        return $bRetorno;
    }

    /**
     * Executa a verificacao de ContaReceber do ContaReceberBO
     *
     * @param array $parametros Array de parametros para realizar a validacao
     * @param string $mensagemErro Mensagem de erro a ser retornado ao front-end
     * @param null|\App\Entity\Principal\ContaReceber $contaReceberORM Ponteiro para retornar o objeto e ser incorporado no indice
     *
     * @return boolean
     */
    public function verificaContaReceberExisteBO($parametros, &$mensagemErro, &$contaReceberORM=null)
    {
        $bRetorno = false;
        if (isset($parametros[ConstanteParametros::CHAVE_CONTA_RECEBER]) === true) {
            $mensagemRetorno = "";
            $bRetorno        = \App\BO\Principal\ContaReceberBO::verificaContaReceberExiste($this->contaReceberRepository, $parametros[ConstanteParametros::CHAVE_CONTA_RECEBER], $mensagemRetorno, $contaReceberORM);
            if ($bRetorno === false) {
                $mensagemErro = "ContaReceber informada não encontrada na base de dados.";
            }
        }

        return $bRetorno;
    }

    /**
     * Executa a verificacao de ItemContaReceber do ItemContaReceberBO
     *
     * @param array $parametros Array de parametros para realizar a validacao
     * @param string $mensagemErro Mensagem de erro a ser retornado ao front-end
     * @param null|\App\Entity\Principal\ItemContaReceber $itemContaReceberORM Ponteiro para retornar o objeto e ser incorporado no indice
     *
     * @return boolean
     */
    public function verificaItemContaReceberExisteBO($parametros, &$mensagemErro, &$itemContaReceberORM=null)
    {
        $bRetorno = false;
        if (isset($parametros[ConstanteParametros::CHAVE_ITEM_CONTA_RECEBER]) === true) {
            $mensagemRetorno = "";
            $bRetorno        = \App\BO\Principal\ItemContaReceberBO::verificaItemContaReceberExiste($this->itemContaReceberRepository, $parametros[ConstanteParametros::CHAVE_ITEM_CONTA_RECEBER], $mensagemRetorno, $itemContaReceberORM);
            if ($bRetorno === false) {
                $mensagemErro = "ItemContaReceber informada não encontrada na base de dados.";
            }
        }

        return $bRetorno;
    }

    /**
     * Executa a verificacao de Franqueada da FranqueadaBO
     *
     * @param array $parametros Array de parametros para realizar a validacao
     * @param string $mensagemErro Mensagem de erro a ser retornado ao front-end
     * @param null|\App\Entity\Principal\Franqueada $franqueadaORM Ponteiro para retornar o objeto e ser incorporado no indice
     *
     * @return boolean
     */
    public function verificaFranqueadaExisteBO($parametros, &$mensagemErro, &$franqueadaORM=null)
    {
        $bRetorno = false;
        if (isset($parametros[ConstanteParametros::CHAVE_FRANQUEADA]) === true) {
    
            $bRetorno = \App\BO\Principal\FranqueadaBO::franqueadaExisteBanco($this->franqueadaRepository, 
                                                                                ["id" => $parametros[ConstanteParametros::CHAVE_FRANQUEADA]], 
                                                                                    $franqueadaORM);
            if ($bRetorno === false) {
                $mensagemErro = "Franqueada informada não encontrada na base de dados.";
            }
        }

        return $bRetorno;
    }

    /**
     * Executa a verificacao de ModeloTemplate
     *
     * @param array $parametros Array de parametros para realizar a validacao
     * @param string $mensagemErro Mensagem de erro a ser retornado ao front-end
     * @param null|\App\Entity\Principal\ModeloTemplate $modeloTemplateORM Ponteiro para retornar o objeto e ser incorporado no indice
     *
     * @return boolean
     */
    public function verificaModeloTemplateExisteBO($parametros, &$mensagemErro, &$modeloTemplateORM=null)
    {
        $bRetorno = false;
        if (isset($parametros[ConstanteParametros::CHAVE_MODELO_TEMPLATE]) === true) {
            $modeloTemplateORM = $this->modeloTemplateRepository->find($parametros[ConstanteParametros::CHAVE_MODELO_TEMPLATE]);
            if (is_null($modeloTemplateORM) === true) {
                $mensagemErro = "Modelo template informado não encontrado na base de dados.";
                $bRetorno     = false;
            } else {
                $bRetorno = true;
            }
        }

        return $bRetorno;
    }



    /**
     * Verifica se existe midia
     *
     * @param array $parametros Array de parametros para realizar a validacao
     * @param string $mensagemErro Mensagem de erro a ser retornado ao front-end
     * @param null|\App\Entity\Principal\Midia $midiaORM Ponteiro para retornar o objeto e ser incorporado no indice
     *
     * @return boolean
     */
    public function verificaMidiaExisteBO($parametros, &$mensagemErro, &$midiaORM=null)
    {
        $bRetorno = false;
        if (isset($parametros[ConstanteParametros::CHAVE_MIDIA]) === true) {
            $bRetorno = \App\BO\Principal\MidiaBO::verificaMidiaExiste($this->midiaRepository, $parametros[ConstanteParametros::CHAVE_MIDIA], $mensagemErro, $midiaORM);
            if ($bRetorno === false) {
                $mensagemErro = "Midia informada não encontrada na base de dados.";
            }
        }

        return $bRetorno;
    }

    /**
     * Verifica se existe checklist
     *
     * @param array $parametros Array de parametros para realizar a validacao
     * @param string $mensagemErro Mensagem de erro a ser retornado ao front-end
     * @param null|\App\Entity\Principal\Checklist $checklistORM Ponteiro para retornar o objeto e ser incorporado no indice
     *
     * @return boolean
     */
    public function verificaChecklistExisteBO($parametros, &$mensagemErro, &$checklistORM=null)
    {
        $bRetorno = false;
        if (isset($parametros[ConstanteParametros::CHAVE_CHECKLIST]) === true) {
            $bRetorno = \App\BO\Principal\ChecklistBO::verificaChecklistExiste($this->checklistRepository, $parametros[ConstanteParametros::CHAVE_CHECKLIST], $mensagemErro, $checklistORM);
            if ($bRetorno === false) {
                $mensagemErro = "Checklist informada não encontrada na base de dados.";
            }
        }

        return $bRetorno;
    }

    /**
     * Verifica se existe checklistAtividade
     *
     * @param array $parametros Array de parametros para realizar a validacao
     * @param string $mensagemErro Mensagem de erro a ser retornado ao front-end
     * @param null|\App\Entity\Principal\ChecklistAtividade $checklistAtividadeORM Ponteiro para retornar o objeto e ser incorporado no indice
     *
     * @return boolean
     */
    public function verificaChecklistAtividadeBO($parametros, &$mensagemErro, &$checklistAtividadeORM=null)
    {
        $bRetorno = false;
        if (isset($parametros[ConstanteParametros::CHAVE_CHECKLIST_ATIVIDADE]) === true) {
            $bRetorno = \App\BO\Principal\ChecklistAtividadeBO::verificaChecklistAtividadeExiste($this->checklistAtividadeRepository, $parametros[ConstanteParametros::CHAVE_CHECKLIST_ATIVIDADE], $mensagemErro, $checklistAtividadeORM);
            if ($bRetorno === false) {
                $mensagemErro = "ChecklistAtividade informada não encontrada na base de dados.";
            }
        }

        return $bRetorno;
    }

    /**
     * Executa a verificacao de Categoria da CategoriaBO
     *
     * @param array $parametros Array de parametros para realizar a validacao
     * @param string $mensagemErro Mensagem de erro a ser retornado ao front-end
     * @param null|\App\Entity\Principal\Categoria $categoriaORM Ponteiro para retornar o objeto e ser incorporado no indice
     *
     * @return boolean
     */
    public function verificaCategoriaExisteBO($parametros, &$mensagemErro, &$categoriaORM=null)
    {
        $bRetorno = false;
        if (isset($parametros[ConstanteParametros::CHAVE_CATEGORIA]) === true) {
            $bRetorno = \App\BO\Principal\CategoriaBO::verificaCategoriaExiste($this->categoriaRepository, $parametros[ConstanteParametros::CHAVE_CATEGORIA], $mensagemErro, $categoriaORM);
            if ($bRetorno === false) {
                $mensagemErro = "Categoria informada não encontrada na base de dados.";
            }
        }

        return $bRetorno;
    }

    /**
     * Executa a verificacao de MotivoDevolucaoCheque do MotivoDevolucaoChequeBO
     *
     * @param array $parametros Array de parametros para realizar a validacao
     * @param string $mensagemErro Mensagem de erro a ser retornado ao front-end
     * @param null|\App\Entity\Principal\MotivoDevolucaoCheque $motivoDevolucaoChequeORM Ponteiro para retornar o objeto e ser incorporado no indice
     *
     * @return boolean
     */
    public function verificaMotivoDevolucaoChequeExiste($parametros, &$mensagemErro, &$motivoDevolucaoChequeORM=null)
    {
        $bRetorno = false;
        if (isset($parametros[ConstanteParametros::CHAVE_MOTIVO_DEVOLUCAO_CHEQUE]) === true) {
            $bRetorno = \App\BO\Principal\MotivoDevolucaoChequeBO::verificaMotivoDevolucaoChequeExiste($this->motivoDevolucaoChequeRepository, $parametros[ConstanteParametros::CHAVE_MOTIVO_DEVOLUCAO_CHEQUE], $mensagemErro, $motivoDevolucaoChequeORM);
            if ($bRetorno === false) {
                $mensagemErro = "MotivoDevolucaoCheque informado não encontrada na base de dados.";
            }
        }

        return $bRetorno;
    }

    /**
     * Executa a verificacao de Estado do CepBO
     *
     * @param array $parametros Array de parametros para realizar a validacao
     * @param string $mensagemErro Mensagem de erro a ser retornado ao front-end
     * @param null|\App\Entity\Principal\Estado $estadoORM Ponteiro para retornar o objeto e ser incorporado no indice
     *
     * @return boolean
     */
    public function verificaEstadoExisteBO($parametros, &$mensagemErro, &$estadoORM=null)
    {
        $bRetorno = false;
        if (isset($parametros[ConstanteParametros::CHAVE_ESTADO]) === true) {
            $bRetorno = \App\BO\Principal\CepBO::verificaEstadoExiste($this->estadoRepository, $parametros[ConstanteParametros::CHAVE_ESTADO], $mensagemErro, $estadoORM);
            if ($bRetorno === false) {
                $mensagemErro = "Estado informado não encontrado na base de dados.";
            }
        }

        return $bRetorno;
    }

    /**
     * Executa a verificacao de Cidade do CepBO
     *
     * @param array $parametros Array de parametros para realizar a validacao
     * @param string $mensagemErro Mensagem de erro a ser retornado ao front-end
     * @param null|\App\Entity\Principal\Cidade $cidadeORM Ponteiro para retornar o objeto e ser incorporado no indice
     *
     * @return boolean
     */
    public function verificaCidadeExisteBO($parametros, &$mensagemErro, &$cidadeORM=null)
    {
        $bRetorno = false;
        if (isset($parametros[ConstanteParametros::CHAVE_CIDADE]) === true) {
            $bRetorno = \App\BO\Principal\CepBO::verificaCidadeExiste($this->cidadeRepository, $parametros[ConstanteParametros::CHAVE_CIDADE], $mensagemErro, $cidadeORM);
            if ($bRetorno === false) {
                $mensagemErro = "Cidade informada não encontrada na base de dados.";
            }
        }

        return $bRetorno;
    }

    /**
     * Executa a verificacao de Sala da SalaBO
     *
     * @param array $parametros Array de parametros para realizar a validacao
     * @param string $mensagemErro Mensagem de erro a ser retornado ao front-end
     * @param null|\App\Entity\Principal\Sala $salaORM Ponteiro para retornar o objeto e ser incorporado no indice
     *
     * @return boolean
     */
    public function verificaSalaExisteBO($parametros, &$mensagemErro, &$salaORM=null)
    {
        $bRetorno = false;
        if (isset($parametros[ConstanteParametros::CHAVE_SALA]) === true) {
            $bRetorno = \App\BO\Principal\SalaBO::salaExisteBanco($this->salaRepository, ["id" => $parametros[ConstanteParametros::CHAVE_SALA]], $salaORM);
            if ($bRetorno === false) {
                $mensagemErro = "Sala informada não encontrada na base de dados.";
            }
        }

        return $bRetorno;
    }

    /**
     * Executa a verificacao de Calendario da CalendarioBO
     *
     * @param array $parametros Array de parametros para realizar a validacao
     * @param string $mensagemErro Mensagem de erro a ser retornado ao front-end
     * @param null|\App\Entity\Principal\Calendario $calendarioORM Ponteiro para retornar o objeto e ser incorporado no indice
     *
     * @return boolean
     */
    public function verificaCalendarioExisteBO($parametros, &$mensagemErro, &$calendarioORM=null)
    {
        $bRetorno = false;
        if (isset($parametros[ConstanteParametros::CHAVE_CALENDARIO]) === true) {
            $bRetorno = \App\BO\Principal\CalendarioBO::verificaCalendarioExiste($this->calendarioRepository, $parametros[ConstanteParametros::CHAVE_CALENDARIO], $mensagemErro, $calendarioORM);
            if ($bRetorno === false) {
                $mensagemErro = "Calendario informado não encontrado na base de dados.";
            }
        }

        return $bRetorno;
    }

    /**
     * Executa a verificacao de ValorHoraLinhasBO
     *
     * @param array $parametros Array de parametros para realizar a validacao
     * @param string $mensagemErro Mensagem de erro a ser retornado ao front-end
     * @param null|\App\Entity\Principal\ValorHoraLinhas $valorHoraLinhasORM Ponteiro para retornar o objeto e ser incorporado no indice
     *
     * @return boolean
     */
    public function verificaValorHoraLinhasExisteBO($parametros, &$mensagemErro, &$valorHoraLinhasORM=null)
    {
        $bRetorno = false;
        if (isset($parametros[ConstanteParametros::CHAVE_VALOR_HORA_LINHAS]) === true) {
            $bRetorno = \App\BO\Principal\ValorHoraLinhasBO::valorHoraLinhasExisteBanco($this->valorHoraLinhasRepository, $parametros[ConstanteParametros::CHAVE_VALOR_HORA_LINHAS], $mensagemErro, $valorHoraLinhasORM);
            if ($bRetorno === false) {
                $mensagemErro = "Valor hora linhas não encontrada na base de dados.";
            }
        }

        return $bRetorno;
    }

    /**
     * Executa a verificacao de NivelInstrutorBO
     *
     * @param array $parametros Array de parametros para realizar a validacao
     * @param string $mensagemErro Mensagem de erro a ser retornado ao front-end
     * @param null|\App\Entity\Principal\NivelInstrutor $nivelInstrutorORM Ponteiro para retornar o objeto e ser incorporado no indice
     *
     * @return boolean
     */
    public function verificaNivelInstrutorExisteBO($parametros, &$mensagemErro, &$nivelInstrutorORM=null)
    {
        $bRetorno = false;
        if (isset($parametros[ConstanteParametros::CHAVE_NIVEL_INSTRUTOR]) === true) {
            $bRetorno = \App\BO\Principal\NivelInstrutorBO::nivelInstrutorExisteBanco($this->nivelInstrutorRepository, ["id" => $parametros[ConstanteParametros::CHAVE_NIVEL_INSTRUTOR]], $nivelInstrutorORM);
            if ($bRetorno === false) {
                $mensagemErro = "Nivel instrutor não encontrado na base de dados.";
            }
        }

        return $bRetorno;
    }

    /**
     * Executa a verificacao de CargoBO
     *
     * @param array $parametros Array de parametros para realizar a validacao
     * @param string $mensagemErro Mensagem de erro a ser retornado ao front-end
     * @param null|\App\Entity\Principal\Cargo $cargoORM Ponteiro para retornar o objeto e ser incorporado no indice
     *
     * @return boolean
     */
    public function verificaCargoExisteBO($parametros, &$mensagemErro, &$cargoORM=null)
    {
        $bRetorno = false;
        if (isset($parametros[ConstanteParametros::CHAVE_CARGO]) === true) {
            $bRetorno = \App\BO\Principal\CargoBO::cargoExisteBanco($this->cargoRepository, ["id" => $parametros[ConstanteParametros::CHAVE_CARGO]], $cargoORM);
            if ($bRetorno === false) {
                $mensagemErro = "Cargo não encontrado na base de dados.";
            }
        }

        return $bRetorno;
    }

    /**
     * Executa a verificacao de ValorHoraBO
     *
     * @param array $parametros Array de parametros para realizar a validacao
     * @param string $mensagemErro Mensagem de erro a ser retornado ao front-end
     * @param null|\App\Entity\Principal\ValorHora $valorHoraORM Ponteiro para retornar o objeto e ser incorporado no indice
     *
     * @return boolean
     */
    public function verificaValorHoraExisteBO($parametros, &$mensagemErro, &$valorHoraORM=null)
    {
        $bRetorno = false;
        if (isset($parametros[ConstanteParametros::CHAVE_VALOR_HORA]) === true) {
            $bRetorno = \App\BO\Principal\ValorHoraBO::verificaValorHoraExiste($this->valorHoraRepository, $parametros[ConstanteParametros::CHAVE_VALOR_HORA], $mensagemErro, $valorHoraORM);
            if ($bRetorno === false) {
                $mensagemErro = "Valor hora não encontrado na base de dados.";
            }
        }

        return $bRetorno;
    }

    /**
     * Executa a verificacao de FuncionarioBO
     *
     * @param array $parametros Array de parametros para realizar a validacao
     * @param string $mensagemErro Mensagem de erro a ser retornado ao front-end
     * @param string $chavePesquisa Chave a ser pesquisado
     * @param null|\App\Entity\Principal\Funcionario $funcionarioORM Ponteiro para retornar o objeto e ser incorporado no indice
     *
     * @return boolean
     */
    public function verificaFuncionarioExisteBO($parametros, &$mensagemErro, $chavePesquisa, &$funcionarioORM=null)
    {
        $bRetorno = false;
        if (isset($parametros[$chavePesquisa]) === true) {
            $bRetorno = \App\BO\Principal\FuncionarioBO::verificaFuncionarioExiste($this->funcionarioRepository, $parametros[$chavePesquisa], $mensagemErro, $funcionarioORM);
            if ($bRetorno === false) {
                $mensagemErro = "Funcionario não encontrado na base de dados.";
            }
        }

        return $bRetorno;
    }

    /**
     * Executa a verificacao de ClassificacaoAluno da ClassificacaoAlunoBO
     *
     * @param array $parametros Array de parametros para realizar a validacao
     * @param string $mensagemErro Mensagem de erro a ser retornado ao front-end
     * @param null|\App\Entity\Principal\ClassificacaoAluno $classificacaoAlunoORM Ponteiro para retornar o objeto e ser incorporado no indice
     *
     * @return boolean
     */
    public function verificaClassificacaoAlunoExisteBO($parametros, &$mensagemErro, &$classificacaoAlunoORM=null)
    {
        $bRetorno = false;
        if (isset($parametros[ConstanteParametros::CHAVE_CLASSIFICACAO_ALUNO]) === true) {
            $bRetorno = \App\BO\Principal\ClassificacaoAlunoBO::verificaClassificacaoAlunoExiste($this->classificacaoAlunoRepository, $parametros[ConstanteParametros::CHAVE_CLASSIFICACAO_ALUNO], $mensagemErro, $classificacaoAlunoORM, true);
            if ($bRetorno === false) {
                $mensagemErro = "ClassificacaoAluno informada não encontrada na base de dados.";
            }
        }

        return $bRetorno;
    }

    /**
     * Executa a verificacao de Escolaridade da EscolaridadeBO
     *
     * @param array $parametros Array de parametros para realizar a validacao
     * @param string $mensagemErro Mensagem de erro a ser retornado ao front-end
     * @param null|\App\Entity\Principal\Escolaridade $escolaridadeORM Ponteiro para retornar o objeto e ser incorporado no indice
     *
     * @return boolean
     */
    public function verificaEscolaridadeExisteBO($parametros, &$mensagemErro, &$escolaridadeORM=null)
    {
        $bRetorno = false;
        if (isset($parametros[ConstanteParametros::CHAVE_ESCOLARIDADE]) === true) {
            $bRetorno = \App\BO\Principal\EscolaridadeBO::verificaEscolaridadeExiste($this->escolaridadeRepository, ["id" => $parametros[ConstanteParametros::CHAVE_ESCOLARIDADE]], $mensagemErro, $escolaridadeORM);
            if ($bRetorno === false) {
                $mensagemErro = "Escolaridade informada não encontrada na base de dados.";
            }
        }

        return $bRetorno;
    }

    /**
     * Executa a verificacao de RelacionamentoAluno da RelacionamentoAlunoBO
     *
     * @param array $parametros Array de parametros para realizar a validacao
     * @param string $mensagemErro Mensagem de erro a ser retornado ao front-end
     * @param null|\App\Entity\Principal\RelacionamentoAluno $relacionamentoAlunoORM Ponteiro para retornar o objeto e ser incorporado no indice
     *
     * @return boolean
     */
    public function verificaRelacionamentoAlunoExisteBO($parametros, &$mensagemErro, &$relacionamentoAlunoORM=null)
    {
        $bRetorno = false;
        if (isset($parametros[ConstanteParametros::CHAVE_RELACIONAMENTO_ALUNO]) === true) {
            $bRetorno = \App\BO\Principal\RelacionamentoAlunoBO::verificaRelacionamentoAlunoExiste($this->relacionamentoAlunoRepository, ["id" => $parametros[ConstanteParametros::CHAVE_RELACIONAMENTO_ALUNO]], $mensagemErro, $relacionamentoAlunoORM);
            if ($bRetorno === false) {
                $mensagemErro = "RelacionamentoAluno informada não encontrada na base de dados.";
            }
        }

        return $bRetorno;
    }

    /**
     * Executa a verificacao de TipoMovimentoEstoque do TipoMovimentoEstoqueBO
     *
     * @param array $parametros Array para verificar se a chave existe para entao realizar a verificacao
     * @param string $mensagemErro Mensagem de erro a ser retornado ao front-end
     * @param null|\App\Entity\Principal\TipoMovimentoEstoque $retornoORM Ponteiro para retornar o objeto para ser incorporado no indice de Tipo Movimento Estoque
     *
     * @return boolean
     */
    public function verificaTipoMovimentoEstoqueExisteBO($parametros, &$mensagemErro, &$retornoORM=null)
    {
        $bRetorno = false;
        if (isset($parametros[ConstanteParametros::CHAVE_TIPO_MOVIMENTO_ESTOQUE]) === true) {
            $mensagem = "";
            $bRetorno = \App\BO\Principal\TipoMovimentoEstoqueBO::verificaTpMovimentoEstoqueExiste($this->tipoMovimentoEstoqueRepository, $parametros[ConstanteParametros::CHAVE_TIPO_MOVIMENTO_ESTOQUE], $mensagem, $retornoORM);
            if ($bRetorno === false) {
                $mensagemErro .= "Tipo Movimento Estoque não encontrado no banco de dados.";
            }
        }

        return $bRetorno;
    }

    /**
     * Executa a funcao de verificar se o TipoMovimentoConta existe no TipoMovimentoContaBO
     *
     * @param array $parametros Array para verificar se a chave existe para entao realizar a verificacao
     * @param string $mensagemErro Mensagem de erro a ser retornado ao front-end
     * @param null|\App\Entity\Principal\TipoMovimentoConta $retornoORM Ponteiro para retornar o objeto para ser incorporado no indice de Banco
     *
     * @return boolean
     */
    public function verificaTipoMovimentoContaExisteBO($parametros, &$mensagemErro, &$retornoORM=null)
    {
        $bRetorno = false;
        if (isset($parametros[ConstanteParametros::CHAVE_TIPO_MOVIMENTO_CONTA]) === true) {
            $mensagem = "";
            $bRetorno = \App\BO\Principal\TipoMovimentoContaBO::verificaTipoMovimentoContaExisteId($this->tipoMovimentoContaRepository, $parametros[ConstanteParametros::CHAVE_TIPO_MOVIMENTO_CONTA], $mensagem, $retornoORM);
            if ($bRetorno === false) {
                $mensagemErro .= "TipoMovimentoConta não encontrado na base de dados";
            }
        }

        return $bRetorno;
    }

    /**
     * Executa a funcao de verificar se o TituloPagar existe no TituloPagarBO
     *
     * @param array $parametros Array para verificar se a chave existe para entao realizar a verificacao
     * @param string $mensagemErro Mensagem de erro a ser retornado ao front-end
     * @param null|\App\Entity\Principal\TituloPagar $retornoORM Ponteiro para retornar o objeto para ser incorporado no indice de Banco
     *
     * @return boolean
     */
    public function verificaTituloPagarExisteBO($parametros, &$mensagemErro, &$retornoORM=null)
    {
        $bRetorno = false;
        if (isset($parametros[ConstanteParametros::CHAVE_TITULO_PAGAR]) === true) {
            $mensagem = "";
            $bRetorno = \App\BO\Principal\TituloPagarBO::verificaTituloPagarExisteId($this->tituloPagarRepository, $parametros[ConstanteParametros::CHAVE_TITULO_PAGAR], $mensagem, $retornoORM);
            if ($bRetorno === false) {
                $mensagemErro .= "TituloPagar não encontrado na base de dados";
            }
        }

        return $bRetorno;
    }

    /**
     * Executa a funcao de verificar se o TituloReceber existe no TituloReceberBO
     *
     * @param array $parametros Array para verificar se a chave existe para entao realizar a verificacao
     * @param string $mensagemErro Mensagem de erro a ser retornado ao front-end
     * @param null|\App\Entity\Principal\TituloPagar $retornoORM Ponteiro para retornar o objeto para ser incorporado no indice de Banco
     *
     * @return boolean
     */
    public function verificaTituloReceberExisteBO($parametros, &$mensagemErro, &$retornoORM=null)
    {
        $bRetorno = false;
        if (isset($parametros[ConstanteParametros::CHAVE_TITULO_RECEBER]) === true) {
            $mensagem = "";
            $bRetorno = \App\BO\Principal\TituloReceberBO::verificaTituloReceberExisteId($this->tituloReceberRepository, $parametros[ConstanteParametros::CHAVE_TITULO_RECEBER], $mensagem, $retornoORM);
            if ($bRetorno === false) {
                $mensagemErro .= "TituloReceber não encontrado na base de dados";
            }
        }

        return $bRetorno;
    }

    /**
     * Executa a funcao de verificar se o MovimentoConta existe no MovimentoContaBO
     *
     * @param array $parametros Array para verificar se a chave existe para entao realizar a verificacao
     * @param string $mensagemErro Mensagem de erro a ser retornado ao front-end
     * @param null|\App\Entity\Principal\MovimentoConta $retornoORM Ponteiro para retornar o objeto para ser incorporado no indice de Banco
     *
     * @return boolean
     */
    public function verificaMovimentoContaExisteBO($parametros, &$mensagemErro, &$retornoORM=null)
    {
        $bRetorno = false;
        if (isset($parametros[ConstanteParametros::CHAVE_MOVIMENTO_CONTA]) === true) {
            $mensagem = "";
            $bRetorno = \App\BO\Principal\MovimentoContaBO::verificaMovimentoContaExisteId($this->movimentoContaRepository, $parametros[ConstanteParametros::CHAVE_MOVIMENTO_CONTA], $mensagem, $retornoORM);
            if ($bRetorno === false) {
                $mensagemErro .= "MovimentoConta não encontrado na base de dados";
            }
        }

        return $bRetorno;
    }

    /**
     * Executa a verificao de Usuario do UsuarioBO
     *
     * @param array $parametros Array para verificar se a chave existe para entao realizar a verificacao
     * @param string $mensagemErro Mensagem de erro a ser retornado ao front-end
     * @param null|\App\Entity\Principal\Usuario $retornoORM Ponteiro para retornar o objeto para ser incorporado no indice de Tipo Movimento Estoque
     * @param boolean $atendenteUsuario Se for para usar atendente_usuario como chave
     *
     * @return boolean
     */
    public function verificaUsuarioExisteBO($parametros, &$mensagemErro, &$retornoORM=null, $atendenteUsuario=false)
    {
        $bRetorno = false;
        if (isset($parametros[ConstanteParametros::CHAVE_USUARIO]) === true) {
            $bRetorno = \App\BO\Principal\UsuarioBO::usuarioExisteBanco($this->usuarioRepository, ["id" => $parametros[ConstanteParametros::CHAVE_USUARIO]], $retornoORM);
        }

        if (isset($parametros[ConstanteParametros::CHAVE_ATENDENTE_USUARIO]) === true) {
            $bRetorno = \App\BO\Principal\UsuarioBO::usuarioExisteBanco($this->usuarioRepository, ["id" => $parametros[ConstanteParametros::CHAVE_ATENDENTE_USUARIO]], $retornoORM);
        }

        if ($bRetorno === false) {
            $mensagemErro .= "Usuario não encontrado no banco de dados.";
        }

        return $bRetorno;
    }

    /**
     * Verifica se o interessado existe na base
     *
     * @param array $parametros
     * @param string $mensagemErro
     * @param NULL|\App\Entity\Principal\Interessado $retornoORM
     *
     * @return boolean
     */
    public function verificaInteressadoExisteBO($parametros, &$mensagemErro, &$retornoORM=null)
    {
        $bRetorno = false;
        if (isset($parametros[ConstanteParametros::CHAVE_INTERESSADO]) === true) {
            $mensagem = "";
            $bRetorno = \App\BO\Principal\InteressadoBO::verificaInteressadoExiste($this->interessadoRepository, $parametros[ConstanteParametros::CHAVE_INTERESSADO], $mensagem, $retornoORM);
            if ($bRetorno === false) {
                $mensagemErro .= "Interessado não encontrado na base de dados";
            }
        }

        return $bRetorno;
    }

    /**
     * Verifica se o interessado existe na base
     *
     * @param array $parametros
     * @param string $mensagemErro
     * @param NULL|\App\Entity\Principal\Convenio $retornoORM
     *
     * @return boolean
     */
    public function verificaConvenioExisteBO($parametros, &$mensagemErro, &$retornoORM=null)
    {
        $bRetorno = false;
        if (isset($parametros[ConstanteParametros::CHAVE_CONVENIO]) === true) {
            $mensagem = "";
            $bRetorno = \App\BO\Principal\ConvenioBO::verificaConvenioExiste($this->convenioRepository, $parametros[ConstanteParametros::CHAVE_CONVENIO], $mensagem, $retornoORM);
            if ($bRetorno === false) {
                $mensagemErro .= "Convenio não encontrado na base de dados";
            }
        }

        return $bRetorno;
    }

    /**
     * Executa a funcao de verificar se o Banco existe atraves do ID do BancoBO
     *
     * @param array $parametros Array para verificar se a chave existe para entao realizar a verificacao
     * @param string $mensagemErro Mensagem de erro a ser retornado ao front-end
     * @param null|\App\Entity\Principal\Banco $retornoORM Ponteiro para retornar o objeto para ser incorporado no indice de Banco
     *
     * @return boolean
     */
    public function verificaBancoExisteBO($parametros, &$mensagemErro, &$retornoORM=null)
    {
        $bRetorno = false;
        if (isset($parametros[ConstanteParametros::CHAVE_BANCO]) === true) {
            $mensagem = "";
            $bRetorno = \App\BO\Principal\BancoBO::verificaBancoExiste($parametros[ConstanteParametros::CHAVE_BANCO], $mensagem, $retornoORM, $this->bancoRepository);
            if ($bRetorno === false) {
                $mensagemErro .= "Banco não encontrado na base de dados";
            }
        }

        return $bRetorno;
    }

    /**
     * Executa a funcao de verificar se o Banco existe atraves do ID do BancoBO
     *
     * @param array $parametros Array para verificar se a chave existe para entao realizar a verificacao
     * @param string $mensagemErro Mensagem de erro a ser retornado ao front-end
     * @param null|\App\Entity\Principal\Conta $retornoORM Ponteiro para retornar o objeto para ser incorporado no indice de Banco
     *
     * @return boolean
     */
    public function verificaContaExisteBO($parametros, &$mensagemErro, &$retornoORM=null)
    {
        $bRetorno = false;
        if (isset($parametros[ConstanteParametros::CHAVE_CONTA]) === true) {
            $mensagem = "";
            $bRetorno = \App\BO\Principal\ContaBO::verificaContaIdExiste($this->contaRepository, $parametros[ConstanteParametros::CHAVE_CONTA], $mensagem, $retornoORM);
            if ($bRetorno === false) {
                $mensagemErro .= "Conta não encontrada na base de dados";
            }
        }

        return $bRetorno;
    }

    /**
     * Executa a funcao de verificar se o Cheque existe atraves do ID do Cheque
     *
     * @param array $parametros Array para verificar se a chave existe para entao realizar a verificacao
     * @param string $mensagemErro Mensagem de erro a ser retornado ao front-end
     * @param null|\App\Entity\Principal\Cheque $retornoORM Ponteiro para retornar o objeto para ser incorporado no indice de Cheque
     *
     * @return boolean
     */
    public function verificaChequeExisteBO($parametros, &$mensagemErro, &$retornoORM=null)
    {
        $bRetorno = false;
        if (isset($parametros[ConstanteParametros::CHAVE_CHEQUE]) === true) {
            $mensagem = "";
            $bRetorno = \App\BO\Principal\ChequeBO::verificaChequeExiste($this->chequeRepository, $parametros[ConstanteParametros::CHAVE_CHEQUE], $mensagem, $retornoORM);
            if ($bRetorno === false) {
                $mensagemErro .= "Cheque não encontrada na base de dados";
            }
        }

        return $bRetorno;
    }

    /**
     * Executa a funcao de verificar se o Boleto existe atraves do ID do Boleto
     *
     * @param array $parametros Array para verificar se a chave existe para entao realizar a verificacao
     * @param string $mensagemErro Mensagem de erro a ser retornado ao front-end
     * @param null|\App\Entity\Principal\Boleto $retornoORM Ponteiro para retornar o objeto para ser incorporado no indice de Boleto
     *
     * @return boolean
     */
    public function verificaBoletoExisteBO($parametros, &$mensagemErro, &$retornoORM=null)
    {
        $bRetorno = false;
        if (isset($parametros[ConstanteParametros::CHAVE_BOLETO]) === true) {
            $mensagem = "";
            $bRetorno = \App\BO\Principal\BoletoBO::verificaBoletoExiste($this->boletoRepository, $parametros[ConstanteParametros::CHAVE_BOLETO], $mensagem, $retornoORM);
            if ($bRetorno === false) {
                $mensagemErro .= "Boleto não encontrada na base de dados";
            }
        }

        return $bRetorno;
    }

    /**
     * Executa a funcao de verificar se o TransacaoCartao existe atraves do ID do TransacaoCartao
     *
     * @param array $parametros Array para verificar se a chave existe para entao realizar a verificacao
     * @param string $mensagemErro Mensagem de erro a ser retornado ao front-end
     * @param null|\App\Entity\Principal\TransacaoCartao $retornoORM Ponteiro para retornar o objeto para ser incorporado no indice de TransacaoCartao
     *
     * @return boolean
     */
    public function verificaTransacaoCartaoExisteBO($parametros, &$mensagemErro, &$retornoORM=null)
    {
        $bRetorno = false;
        if (isset($parametros[ConstanteParametros::CHAVE_TRANSACAO_CARTAO]) === true) {
            $mensagem = "";
            $bRetorno = \App\BO\Principal\TransacaoCartaoBO::verificaTransacaoCartaoExiste($this->transacaoCartaoRepository, $parametros[ConstanteParametros::CHAVE_TRANSACAO_CARTAO], $mensagem, $retornoORM);
            if ($bRetorno === false) {
                $mensagemErro .= "TransacaoCartao não encontrada na base de dados";
            }
        }

        return $bRetorno;
    }


    /**
     * Executa a funcao de verificar se a forma de pagamento existe
     *
     * @param array $parametros Array para verificar se a chave existe para entao realizar a verificacao
     * @param string $mensagemErro Mensagem de erro a ser retornado ao front-end
     * @param null|\App\Entity\Principal\FormaPagamento $retornoORM Ponteiro para retornar o objeto para ser incorporado no indice
     * @param string $chave Chave dos parâmetros a ser usada
     *
     * @return boolean
     */
    public function verificaFormaPagamentoExisteBO($parametros, &$mensagemErro, &$retornoORM=null, $chave="forma_pagamento")
    {
        $bRetorno = false;

        if (isset($parametros[$chave]) === true) {
            $mensagem = "";
            $bRetorno = \App\BO\Principal\FormaPagamentoBO::verificaFormaPagamentoExiste($this->formaPagamentoRepository, $parametros[$chave], $mensagem, $retornoORM);
            if ($bRetorno === false) {
                $mensagemErro .= "Forma de pagamento não encontrada na base de dados";
            }
        }

        return $bRetorno;
    }

    /**
     * Executa a funcao de verificar se a Pessoa existe do PessoaBO
     *
     * @param array $parametros Array para verificar se a chave existe para entao realizar a verificacao
     * @param string $mensagemErro Mensagem de erro a ser retornado ao front-end
     * @param null|\App\Entity\Principal\Pessoa $retornoORM Ponteiro para retornar o objeto para ser incorporado no indice de Banco
     * @param string $chavePesquisa Chave de pesquisa de pessoa no array, por padrao eh a pessoa
     * @param boolean $retornarObjeto Se deve retornar como objeto
     *
     * @return boolean
     */
    public function verificaPessoaExisteBO($parametros, &$mensagemErro, &$retornoORM=null, $chavePesquisa="pessoa", $retornarObjeto=false)
    {
        $bRetorno = false;
        if (isset($parametros[$chavePesquisa]) === true) {
            $mensagem = "";
            $bRetorno = \App\BO\Principal\PessoaBO::verificaPessoaExiste($this->pessoaRepository, $parametros[$chavePesquisa], $mensagem, $retornoORM, $retornarObjeto);
            if ($bRetorno === false) {
                $mensagemErro .= "Pessoa não encontrada na base de dados";
            }
        } else {
            $mensagemErro .= "Pessoa não encontrada na requisição";
        }

        return $bRetorno;
    }

    /**
     * Executa a funcao de verificar se o ConceitoAvaliacao existe
     *
     * @param array $parametros Array para verificar se a chave existe para entao realizar a verificacao
     * @param string $mensagemErro Mensagem de erro a ser retornado ao front-end
     * @param string $chavePesquisa Campo no array para pesquisar
     * @param \App\Entity\Principal\ConceitoAvaliacao $retornoORM Ponteiro para retornar o objeto para ser incorporado no indice de ConceitoAvaliacao
     *
     * @return boolean
     */
    public function verificaConceitoAvaliacao($parametros, &$mensagemErro, $chavePesquisa, &$retornoORM=null)
    {
        $bRetorno = false;
        if (isset($parametros[$chavePesquisa]) === true) {
            $bRetorno = \App\BO\Principal\ConceitoAvaliacaoBO::verificaConceitoAvaliacaoExiste($this->conceitoAvaliacaoRepository, $parametros[$chavePesquisa], $mensagemErro, $retornoORM);
            if ($bRetorno === false) {
                $mensagemErro .= "Conceito avaliacao não encontrada na base de dados";
            }
        }

        return $bRetorno;
    }

    /**
     * Executa a funcao de verificar se o SistemaAvaliacao existe
     *
     * @param array $parametros Array para verificar se a chave existe para entao realizar a verificacao
     * @param string $mensagemErro Mensagem de erro a ser retornado ao front-end
     * @param null|\App\Entity\Principal\SistemaAvaliacao $retornoORM Ponteiro para retornar o objeto para ser incorporado no indice de Banco
     *
     * @return boolean
     */
    public function verificaSistemaAvaliacao($parametros, &$mensagemErro, &$retornoORM)
    {
        $bRetorno = false;
        if (isset($parametros[ConstanteParametros::CHAVE_SISTEMA_AVALIACAO]) === true) {
            $bRetorno = \App\BO\Principal\SistemaAvaliacaoBO::verificaSistemaAvaliacaoExiste($this->sistemaAvaliacaoRepository, $parametros[ConstanteParametros::CHAVE_SISTEMA_AVALIACAO], $mensagemErro, $retornoORM);
            if ($bRetorno === false) {
                $mensagemErro .= "Sistema Avaliacao não encontrada na base de dados";
            }
        }

        return $bRetorno;
    }

    /**
     * Executa a funcao de verificar se o Idioma existe
     *
     * @param array $parametros Array para verificar se a chave existe para entao realizar a verificacao
     * @param string $mensagemErro Mensagem de erro a ser retornado ao front-end
     * @param null|\App\Entity\Principal\Idioma $retornoORM Ponteiro para retornar o objeto para ser incorporado no indice de Banco
     *
     * @return boolean
     */
    public function verificaIdioma($parametros, &$mensagemErro, &$retornoORM)
    {
        $bRetorno = false;
        if (isset($parametros[ConstanteParametros::CHAVE_IDIOMA]) === true) {
            $bRetorno = \App\BO\Principal\IdiomaBO::verificaIdiomaExiste($this->idiomaRepository, $parametros[ConstanteParametros::CHAVE_IDIOMA], $mensagemErro, $retornoORM);
            if ($bRetorno === false) {
                $mensagemErro .= "Idioma não encontrada na base de dados";
            }
        }

        return $bRetorno;
    }

    /**
     * Executa a funcao de verificar se o Licao existe
     *
     * @param array $parametros Array para verificar se a chave existe para entao realizar a verificacao
     * @param string $mensagemErro Mensagem de erro a ser retornado ao front-end
     * @param null|\App\Entity\Principal\Licao $retornoORM Ponteiro para retornar o objeto para ser incorporado no indice de Banco
     *
     * @return boolean
     */
    public function verificaLicaoExisteBO($parametros, &$mensagemErro, &$retornoORM)
    {
        $bRetorno = false;
        if (isset($parametros[ConstanteParametros::CHAVE_LICAO]) === true) {
            $bRetorno = \App\BO\Principal\LicaoBO::verificaLicaoExiste($this->licaoRepository, $parametros[ConstanteParametros::CHAVE_LICAO], $mensagemErro, $retornoORM);
            if ($bRetorno === false) {
                $mensagemErro .= "Licao não encontrada na base de dados";
            }
        }

        return $bRetorno;
    }

    /**
     * Executa a funcao de verificar se o PlanejamentoLicao existe
     *
     * @param array $parametros Array para verificar se a chave existe para entao realizar a verificacao
     * @param string $mensagemErro Mensagem de erro a ser retornado ao front-end
     * @param null|\App\Entity\Principal\PlanejamentoLicao $retornoORM Ponteiro para retornar o objeto para ser incorporado no indice de Banco
     *
     * @return boolean
     */
    public function verificaPlanejamentoLicao($parametros, &$mensagemErro, &$retornoORM)
    {
        $bRetorno = false;
        if (isset($parametros[ConstanteParametros::CHAVE_PLANEJAMENTO_LICAO]) === true) {
            $bRetorno = \App\BO\Principal\PlanejamentoLicaoBO::verificaPlanejamentoLicaoExiste($this->planejamentoLicaoRepository, $parametros[ConstanteParametros::CHAVE_PLANEJAMENTO_LICAO], $mensagemErro, $retornoORM);
            if ($bRetorno === false) {
                $mensagemErro .= "Planejamento Licao não encontrado na base de dados";
            }
        }

        return $bRetorno;
    }

    /**
     * Executa a funcao de verificar se o Curso existe
     *
     * @param array $parametros Array para verificar se a chave existe para entao realizar a verificacao
     * @param string $mensagemErro Mensagem de erro a ser retornado ao front-end
     * @param null|\App\Entity\Principal\Curso $retornoORM Ponteiro para retornar o objeto para ser incorporado no indice de Banco
     *
     * @return boolean
     */
    public function verificaCursoExisteBO($parametros, &$mensagemErro, &$retornoORM)
    {
        $bRetorno = false;
        if (isset($parametros[ConstanteParametros::CHAVE_CURSO]) === true) {
            $bRetorno = \App\BO\Principal\CursoBO::verificaCursoExiste($this->cursoRepository, $parametros[ConstanteParametros::CHAVE_CURSO], $mensagemErro, $retornoORM, true);
            if ($bRetorno === false) {
                $mensagemErro .= "Curso não encontrada na base de dados";
            }
        }

        return $bRetorno;
    }

    /**
     * Executa a funcao de verificar se a condicao de pagamento existe do CondicaoPagamentoBO
     *
     * @param array $parametros Array para verificar se a chave existe para entao realizar a verificacao
     * @param string $mensagemErro Mensagem de erro a ser retornado ao front-end
     * @param null|\App\Entity\Principal\CondicaoPagamento $retornoORM Ponteiro para retornar o objeto para ser incorporado no indice de Banco
     *
     * @return boolean
     */
    public function verificaCondicaoPagamentoExisteBO($parametros, &$mensagemErro, &$retornoORM=null)
    {
        $bRetorno = false;
        if (isset($parametros[ConstanteParametros::CHAVE_CONDICAO_PAGAMENTO]) === true) {
            $bRetorno = \App\BO\Principal\CondicaoPagamentoBO::verificaCondicaoPagamentoExiste($this->condicaoPagamentoRepository, $parametros[ConstanteParametros::CHAVE_CONDICAO_PAGAMENTO], $retornoORM);
            if ($bRetorno === false) {
                $mensagemErro .= "Condicao de Pagamento não encontrada na base de dados";
            }
        }

        return $bRetorno;
    }

    /**
     * Executa a funcao de verificacao da turma
     *
     * @param array $parametros
     * @param string $mensagemErro
     * @param \App\Entity\Principal\Turma $retornoORM
     *
     * @return boolean
     */
    public function verificaTurmaExisteBO($parametros, &$mensagemErro, &$retornoORM=null)
    {
        $bRetorno = false;
        if (isset($parametros[ConstanteParametros::CHAVE_TURMA]) === true) {
            $bRetorno = \App\BO\Principal\TurmaBO::verificaTurmaExiste($this->turmaRepository, $parametros[ConstanteParametros::CHAVE_TURMA], $mensagemErro, $retornoORM);
            if ($bRetorno === false) {
                $mensagemErro .= "Turma não encontrada na base de dados";
            }
        }

        return $bRetorno;
    }

    /**
     * Executa a verificacao de existencia do contrato do ContratoBO
     *
     * @param array $parametros
     * @param string $mensagemErro
     * @param \App\Entity\Principal\Contrato $retornoORM
     *
     * @return boolean
     */
    public function verificaContratoExisteBO($parametros, &$mensagemErro, &$retornoORM=null)
    {
        $bRetorno = false;
        if (isset($parametros[ConstanteParametros::CHAVE_CONTRATO]) === true) {
            $bRetorno = \App\BO\Principal\ContratoBO::verificaContratoExiste($this->contratoRepository, $parametros[ConstanteParametros::CHAVE_CONTRATO], $mensagemErro, $retornoORM);
            if ($bRetorno === false) {
                $mensagemErro .= "Contrato não encontrado na base de dados";
            }
        }

        return $bRetorno;
    }

    /**
     * Executa a verificacao de existencia da AtividadeDollar da AtividadeDollarBO
     *
     * @param array $parametros
     * @param string $mensagemErro
     * @param \App\Entity\Principal\AtividadeDollar $retornoORM
     *
     * @return boolean
     */
    public function verificaAtividadeDollarExisteBO($parametros, &$mensagemErro, &$retornoORM=null)
    {
        $bRetorno = false;
        if (isset($parametros[ConstanteParametros::CHAVE_ATIVIDADE_DOLLAR]) === true) {
            $bRetorno = \App\BO\Principal\AtividadeDollarBO::verificaAtividadeDollarExiste($this->atividadeDollarRepository, $parametros[ConstanteParametros::CHAVE_ATIVIDADE_DOLLAR], $mensagemErro, $retornoORM);
            if ($bRetorno === false) {
                $mensagemErro .= "AtividadeDollar não encontrada na base de dados";
            }
        }

        return $bRetorno;
    }

    /**
     * Executa a verificacao de existencia da MovimentoDollar da MovimentoDollarBO
     *
     * @param array $parametros
     * @param string $mensagemErro
     * @param \App\Entity\Principal\MovimentoDollar $retornoORM
     *
     * @return boolean
     */
    public function verificaMovimentoDollarExisteBO($parametros, &$mensagemErro, &$retornoORM=null)
    {
        $bRetorno = false;
        if (isset($parametros[ConstanteParametros::CHAVE_MOVIMENTO_DOLLAR]) === true) {
            $bRetorno = \App\BO\Principal\MovimentoDollarBO::verificaMovimentoDollarExiste($this->movimentoDollarRepository, $parametros[ConstanteParametros::CHAVE_MOVIMENTO_DOLLAR], $mensagemErro, $retornoORM);
            if ($bRetorno === false) {
                $mensagemErro .= "MovimentoDollar não encontrado na base de dados";
            }
        }

        return $bRetorno;
    }

    /**
     * Executa a funcao de verificar se a conta a pagar existe do ContaPagarBO
     *
     * @param array $parametros Array para verificar se a chave existe para entao realizar a verificacao
     * @param string $mensagemErro Mensagem de erro a ser retornado ao front-end
     * @param null|\App\Entity\Principal\ContaPagar $retornoORM Ponteiro para retornar o objeto para ser incorporado no indice de Banco
     *
     * @return boolean
     */
    public function verificaContaPagarExisteBO($parametros, &$mensagemErro, &$retornoORM=null)
    {
        $bRetorno = false;
        if (isset($parametros[ConstanteParametros::CHAVE_CONTA_PAGAR]) === true) {
            $mensagem = "";
            $bRetorno = \App\BO\Principal\ContaPagarBO::verificaContaPagarExiste($this->contaPagarRepository, $parametros[ConstanteParametros::CHAVE_CONTA_PAGAR], $mensagem, $retornoORM);
            if ($bRetorno === false) {
                $mensagemErro .= "Conta a pagar não encontrada na base de dados";
            }
        }

        return $bRetorno;
    }

    /**
     * Executa a funcao de verificar se o item existe do GenericItemBO
     *
     * @param array $parametros Array para verificar se a chave existe para entao realizar a verificacao
     * @param string $mensagemErro Mensagem de erro a ser retornado ao front-end
     * @param null|\App\Entity\Principal\Item $retornoORM Ponteiro para retornar o objeto para ser incorporado no indice de Banco
     *
     * @return boolean
     */
    public function verificaItemExisteBO($parametros, &$mensagemErro, &$retornoORM=null)
    {
        $bRetorno = false;
        if (isset($parametros[ConstanteParametros::CHAVE_ITEM]) === true) {
            $bRetorno = \App\BO\Principal\GenericItemBO::verificaItemExistePorId($parametros[ConstanteParametros::CHAVE_ITEM], $mensagemErro, $retornoORM, $this->itemRepository, true);
            if ($bRetorno === false) {
                $mensagemErro .= "Item não encontrado na base.";
            }
        }

        return $bRetorno;
    }

    /**
     * Executa a funcao de verificar se o servico existe do servicoBO
     *
     * @param array $parametros Array para verificar se a chave existe para entao realizar a verificacao
     * @param string $mensagemErro Mensagem de erro a ser retornado ao front-end
     * @param null|\App\Entity\Principal\Servico $retornoORM Ponteiro para retornar o objeto para ser incorporado no indice de Banco
     *
     * @return boolean
     */
    public function verificaServicoExiteBO($parametros, &$mensagemErro, &$retornoORM=null)
    {
        $bRetorno = false;
        if (isset($parametros[ConstanteParametros::CHAVE_SERVICO]) === true) {
            $retornoORM = $this->servicoRepository->find($parametros[ConstanteParametros::CHAVE_SERVICO]);

            if (is_null($retornoORM) === true) {
                $mensagemErro .= "Servico não encontrado na base de dados";
            } else {
                $bRetorno = true;
            }
        }

        return $bRetorno;
    }

    /**
     * Executa a função de verificar se o plano de conta existe
     *
     * @param array $parametros Array para verificar se a chave existe para entao realizar a verificacao
     * @param string $mensagemErro Mensagem de erro a ser retornado ao front-end
     * @param null|\App\Entity\Principal\PlanoConta $retornoORM Ponteiro para retornar o objeto para ser incorporado no indice
     *
     * @return boolean
     */
    public function verificaPlanoContaExisteBO($parametros, &$mensagemErro, &$retornoORM=null)
    {
        $bRetorno = false;
        if (isset($parametros[ConstanteParametros::CHAVE_PLANO_CONTA]) === true) {
            $mensagem = "";
            $bRetorno = \App\BO\Principal\PlanoContaBO::verificaPlanoContaExiste($this->planoContaRepository, $parametros[ConstanteParametros::CHAVE_PLANO_CONTA], $mensagem, $retornoORM);
            if ($bRetorno === false) {
                $mensagemErro .= "Plano de conta não encontrado";
            }
        }

        return $bRetorno;
    }

    /**
     * Executa a função de verificar se o agendamento personal existe
     *
     * @param array $parametros Array para verificar se a chave existe para entao realizar a verificacao
     * @param string $mensagemErro Mensagem de erro a ser retornado ao front-end
     * @param null|\App\Entity\Principal\AgendamentoPersonal $retornoORM Ponteiro para retornar o objeto para ser incorporado no indice
     *
     * @return boolean
     */
    public function verificaAgendamentoPersonalExiste($parametros, &$mensagemErro, &$retornoORM=null)
    {
        $bRetorno = false;
        if (isset($parametros[ConstanteParametros::CHAVE_AGENDAMENTO_PERSONAL]) === true) {
            $mensagem = "";
            $bRetorno = \App\BO\Principal\AgendamentoPersonalBO::verificaAgendamentoPersonalExiste($this->agendamentoPersonalRepository, $parametros[ConstanteParametros::CHAVE_AGENDAMENTO_PERSONAL], $mensagem, $retornoORM);
            if ($bRetorno === false) {
                $mensagemErro .= "AgendamentoPersonal não encontrado";
            }
        }

        return $bRetorno;
    }

    /**
     * Executa a função de verificar se o plano de conta possui filhos
     *
     * @param array $parametros Array para verificar se a chave existe para entao realizar a verificacao
     * @param string $mensagemErro Mensagem de erro a ser retornado ao front-end
     *
     * @return boolean
     */
    public function verificaExisteFilhosPlanoContaBO($parametros, &$mensagemErro)
    {
        $bRetorno = false;
        if (isset($parametros[ConstanteParametros::CHAVE_PLANO_CONTA]) === true) {
            $bRetorno = \App\BO\Principal\PlanoContaBO::verificaPlanoContaTemFilhos($parametros[ConstanteParametros::CHAVE_PLANO_CONTA], $mensagemErro, $this->planoContaRepository, true);
        }

        return $bRetorno;
    }

    /**
     * Verifica se a data Final eh maior que a data final via timeStamp
     *
     * @param \DateTime $dataFinal
     * @param \DateTime $dataInicio
     *
     * @return boolean
     */
    public static function verificaDataFinalMaiorDataInicio(\DateTime $dataFinal, \DateTime $dataInicio)
    {
        $timeStampInicio = $dataInicio->getTimestamp();
        $timeStampFinal  = $dataFinal->getTimestamp();
        if (($timeStampFinal >= $timeStampInicio) === true) {
            return true;
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
    public function podeCalcularParcelas(&$parametros, &$mensagemErro, &$objetoORM)
    {
        $bRetorno = false;
        if (self::verificaCondicaoPagamentoExisteBO($parametros, $mensagemErro, $objetoORM) === true) {
            $bRetorno = true;

            \App\Helper\FunctionHelper::formataCampoDateTimeJS($parametros[ConstanteParametros::CHAVE_NF_DATA_EMISSAO], $parametros[ConstanteParametros::CHAVE_NF_DATA_EMISSAO]);

            if ($parametros[ConstanteParametros::CHAVE_NF_DATA_EMISSAO] === false) {
                $mensagemErro = "Ocorreu um erro na formatação de Data no campo Data de Emissão. Formato de data não reconhecida.";
                $bRetorno     = false;
            }
        }

        return $bRetorno;
    }

    /**
     * Calcula as parcelas conforme o percentual da parcela
     *
     * @param \App\Entity\Principal\CondicaoPagamentoParcela[] $condicaoPagamentoParcelas Array de objetos de condicaoPagamentoParcelas
     * @param array $parametros Parametros a serem inclusos no objeto
     * @param array $parcelas Retorna um array de Parcelas calculadas
     */
    public function calculaTotalParcelas($condicaoPagamentoParcelas, $parametros, &$parcelas)
    {
        $totalParcelas = 0;
        $valorParcela  = 0;
        foreach ($condicaoPagamentoParcelas as $parcelas_itens) {
            $data_vencimento = clone $parametros[ConstanteParametros::CHAVE_NF_DATA_EMISSAO];
            $data_vencimento->add(\DateInterval::createfromdatestring('+' . $parcelas_itens->getDiasVencimento() . ' days'));

            $valorParcela   = $this->calculaValorPercentual($parametros[ConstanteParametros::CHAVE_NF_VALOR_TITULO], $parcelas_itens->getPercentualParcela());
            $totalParcelas += $valorParcela;

            $parcelas[$parcelas_itens->getNumeroParcela()] = [
                ConstanteParametros::CHAVE_ID                           => $parcelas_itens,
                ConstanteParametros::CHAVE_TIT_DATA_VENCIMENTO          => $data_vencimento,
                ConstanteParametros::CHAVE_TIT_NUMERO_PARCELA_DOCUMENTO => $parcelas_itens->getNumeroParcela(),
                ConstanteParametros::CHAVE_TIT_VALOR_DOCUMENTO          => $valorParcela,
            ];
        }//end foreach

        $this->calculaDiferencaNaPrimeiraParcela($parametros[ConstanteParametros::CHAVE_NF_VALOR_TITULO], $totalParcelas, $parcelas);
    }

    /**
     * Verifica se pode atualizar o saldo, apos o pagamento
     *
     * @param array $parametros Parametros a serem inclusos no objeto
     * @param \App\Entity\Principal\TituloReceber|\App\Entity\Principal\TituloPagar $objetoORM Ponteiro para retornar o objeto para ser incorporado no indice de Banco
     */
    public function calculaValoresSaldo(&$parametros, &$objetoORM)
    {
        $valor_saldo_titulo_deduzido = $this->calculaValorSaldoDeduzido($parametros[ConstanteParametros::CHAVE_TIT_VALOR_SALDO], $parametros[ConstanteParametros::CHAVE_VALOR_SALDO_CALCULADO]);
        $situacao_titulo = SituacoesSistema::SITUACAO_PENDENTE;

        if ($valor_saldo_titulo_deduzido <= 0.0) {
            $situacao_titulo = SituacoesSistema::SITUACAO_LIQUIDADO;
        }

        $valor         = 0;
        $isTituloPagar = false;

        if (method_exists($objetoORM, "getValorDocumento") === true) {
            $valor         = (float) $objetoORM->getValorDocumento();
            $isTituloPagar = true;
        } else {
            $valor = (float) $objetoORM->getValorOriginal();
        }

        // if ((- (float) $parametros[ConstanteParametros::CHAVE_MC_VALOR_DIFERENCA_BAIXA]) === $valor) {
        //     $situacao_titulo = SituacoesSistema::SITUACAO_BAIXADO;
        // }

        if ($isTituloPagar === true) {
            $objetoORM->setValorSaldo($valor_saldo_titulo_deduzido);
        } else {
            $objetoORM->setValorSaldoDevedor($valor_saldo_titulo_deduzido);
        }

        if (is_null($situacao_titulo) === false) {
            $objetoORM->setSituacao($situacao_titulo);
        }
    }

    /**
     * Configura o GenericBO para receber os parametros e classes
     *
     * @param array $repositorios Array com os repositorios a serem configurados<b><br>Exemplo:</b>["bancoRepository"=> \App\Repository\BancoRepository]
     */
    public function configuraGenericBO($repositorios=[])
    {
        self::configuraRepositorios($repositorios);
    }

    /**
     *
     * @return \App\Repository\Principal\FranqueadaRepository
     */
    public function getFranqueadaRepository()
    {
        return $this->franqueadaRepository;
    }

    /**
     *
     * @return \App\Repository\Principal\ParametrosFranqueadoraRepository
     */
    public function getParametrosFranqueadoraRepository()
    {
        return $this->parametrosFranqueadoraRepository;
    }

    /**
     *
     * @return \App\Repository\Principal\TipoMovimentoEstoqueRepository
     */
    public function getTipoMovimentoEstoqueRepository()
    {
        return $this->tipoMovimentoEstoqueRepository;
    }

    /**
     *
     * @return \App\Repository\Principal\UsuarioRepository
     */
    public function getUsuarioRepository()
    {
        return $this->usuarioRepository;
    }

    /**
     *
     * @return \App\Repository\Principal\BancoRepository
     */
    public function getBancoRepository()
    {
        return $this->bancoRepository;
    }

    /**
     *
     * @return \App\Repository\Principal\PessoaRepository
     */
    public function getPessoaRepository()
    {
        return $this->pessoaRepository;
    }

    /**
     *
     * @return \App\Repository\Principal\CondicaoPagamentoRepository
     */
    public function getCondicaoPagamentoRepository()
    {
        return $this->condicaoPagamentoRepository;
    }

    /**
     *
     * @return \App\Repository\Principal\ContaPagarRepository
     */
    public function getContaPagarRepository()
    {
        return $this->contaPagarRepository;
    }

    /**
     *
     * @return \App\Repository\Principal\ItemRepository
     */
    public function getItemRepository()
    {
        return $this->itemRepository;
    }

    /**
     *
     * @return \App\Repository\Principal\TipoMovimentoContaRepository
     */
    public function getTipoMovimentoContaRepository()
    {
        return $this->tipoMovimentoContaRepository;
    }

    /**
     *
     * @return \App\Repository\Principal\TituloPagarRepository
     */
    public function getTituloPagarRepository()
    {
        return $this->tituloPagarRepository;
    }

    /**
     *
     * @return \App\Repository\Principal\TituloReceberRepository
     */
    public function getTituloReceberRepository()
    {
        return $this->tituloReceberRepository;
    }

    /**
     *
     * @return \App\Repository\Principal\MovimentoContaRepository
     */
    public function getMovimentoContaRepository()
    {
        return $this->movimentoContaRepository;
    }

    /**
     *
     * @return \App\Repository\Principal\PlanoContaRepository
     */
    public function getPlanoContaRepository()
    {
        return $this->planoContaRepository;
    }

    /**
     *
     * @return \App\Repository\Principal\FormaPagamentoRepository
     */
    public function getFormaPagamentoRepository()
    {
        return $this->formaPagamentoRepository;
    }

    /**
     *
     * @return \App\Repository\Principal\ConceitoAvaliacaoRepository
     */
    public function getConceitoAvaliacaoRepository()
    {
        return $this->conceitoAvaliacaoRepository;
    }

    /**
     *
     * @return \App\Repository\Principal\ClassificacaoAlunoRepository
     */
    public function getClassificacaoAluno()
    {
        return $this->classificacaoAlunoRepository;
    }

    /**
     *
     * @return \App\Repository\Principal\EscolaridadeRepository
     */
    public function getEscolaridade()
    {
        return $this->escolaridadeRepository;
    }

    /**
     *
     * @return \App\Repository\Principal\RelacionamentoAlunoRepository
     */
    public function getRelacionamentoAluno()
    {
        return $this->relacionamentoAlunoRepository;
    }

    /**
     * @return \App\Repository\Principal\ContaRepository
     */
    public function getContaRepository()
    {
        return $this->contaRepository;
    }

    /**
     * @return \App\Repository\Principal\AlunoRepository
     */
    public function getAlunoRepository()
    {
        return $this->alunoRepository;
    }

    /**
     * @return \App\Repository\Principal\ItemContaReceberRepository
     */
    public function getItemContaReceberRepository()
    {
        return $this->itemContaReceberRepository;
    }

    /**
     * @return \App\Repository\Principal\SistemaAvaliacaoRepository
     */
    public function getSistemaAvaliacaoRepository()
    {
        return $this->sistemaAvaliacaoRepository;
    }

    /**
     * @return \App\Repository\Principal\IdiomaRepository
     */
    public function getIdiomaRepository()
    {
        return $this->idiomaRepository;
    }

    /**
     * @return \App\Repository\Principal\CursoRepository
     */
    public function getCursoRepository()
    {
        return $this->cursoRepository;
    }

    /**
     * @return \App\Repository\Principal\PlanejamentoLicaoRepository
     */
    public function getPlanejamentoLicaoRepository()
    {
        return $this->planejamentoLicaoRepository;
    }

    /**
     * @return \App\Repository\Principal\LicaoRepository
     */
    public function getLicaoRepository()
    {
        return $this->licaoRepository;
    }

    /**
     * @return \App\Repository\Principal\SalaRepository
     */
    public function getSalaRepository()
    {
        return $this->salaRepository;
    }

    /**
     * @return \App\Repository\Principal\ValorHoraLinhasRepository
     */
    public function getValorHoraLinhasRepository()
    {
        return $this->valorHoraLinhasRepository;
    }

    /**
     * @return \App\Repository\Principal\NivelInstrutorRepository
     */
    public function getNivelInstrutorRepository()
    {
        return $this->nivelInstrutorRepository;
    }

    /**
     * @return \App\Repository\Principal\CargoRepository
     */
    public function getCargoRepository()
    {
        return $this->cargoRepository;
    }

    /**
     * @return \App\Repository\Principal\FuncionarioRepository
     */
    public function getFuncionarioRepository()
    {
        return $this->funcionarioRepository;
    }

    /**
     * @return \App\Repository\Principal\ValorHoraRepository
     */
    public function getValorHoraRepository()
    {
        return $this->valorHoraRepository;
    }

    /**
     * @return \App\Repository\Principal\HorarioRepository
     */
    public function getHorarioRepository()
    {
        return $this->horarioRepository;
    }

    /**
     * @return \App\Repository\Principal\ClassificacaoAlunoRepository
     */
    public function getClassificacaoAlunoRepository()
    {
        return $this->classificacaoAlunoRepository;
    }

    /**
     * @return \App\Repository\Principal\EscolaridadeRepository
     */
    public function getEscolaridadeRepository()
    {
        return $this->escolaridadeRepository;
    }

    /**
     * @return \App\Repository\Principal\RelacionamentoAlunoRepository
     */
    public function getRelacionamentoAlunoRepository()
    {
        return $this->relacionamentoAlunoRepository;
    }

    /**
     * @return \App\Repository\Principal\CalendarioRepository
     */
    public function getCalendarioRepository()
    {
        return $this->calendarioRepository;
    }

    /**
     * @return \App\Repository\Principal\CategoriaRepository
     */
    public function getCategoriaRepository()
    {
        return $this->categoriaRepository;
    }

    /**
     * @return \App\Repository\Principal\EstadoRepository
     */
    public function getEstadoRepository()
    {
        return $this->estadoRepository;
    }

    /**
     * @return \App\Repository\Principal\CidadeRepository
     */
    public function getCidadeRepository()
    {
        return $this->cidadeRepository;
    }

    /**
     * @return \App\Repository\Principal\MotivoDevolucaoChequeRepository
     */
    public function getMotivoDevolucaoChequeRepository()
    {
        return $this->motivoDevolucaoChequeRepository;
    }

    /**
     * @return \App\Repository\Principal\TurmaRepository
     */
    public function getTurmaRepository()
    {
        return $this->turmaRepository;
    }

        /**
     * @return \App\Repository\Principal\SalaAgendamentoRepository
     */
    public function getSalaAgendamentoRepository()
    {
        return $this->salaAgendamentoRepository;
    }

    /**
     * @return \App\Repository\Principal\ModalidadeTurmaRepository
     */
    public function getModalidadeTurmaRepository()
    {
        return $this->modalidadeTurmaRepository;
    }

    /**
     * @return \App\Repository\Principal\LivroRepository
     */
    public function getLivroRepository()
    {
        return $this->livroRepository;
    }

    /**
     * @return \App\Repository\Principal\SalaFranqueadaRepository
     */
    public function getSalaFranqueadaRepository()
    {
        return $this->salaFranqueadaRepository;
    }

    /**
     *
     * @return \App\Repository\Principal\ContratoRepository
     */
    public function getContratoRepository()
    {
        return $this->contratoRepository;
    }

    /**
     *
     * @return \App\Repository\Principal\AtividadeDollarRepository
     */
    public function getAtividadeDollarRepository()
    {
        return $this->atividadeDollarRepository;
    }

    /**
     *
     * @return \App\Repository\Principal\MovimentoDollarRepository
     */
    public function getMovimentoDollarRepository()
    {
        return $this->movimentoDollarRepository;
    }

    /**
     *
     * @return \App\Repository\Principal\SemestreRepository
     */
    public function getSemestreRepository()
    {
        return $this->semestreRepository;
    }

    /**
     * @return \App\Repository\Principal\WorkflowRepository
     */
    public function getWorkflowRepository()
    {
        return $this->workflowRepository;
    }

    /**
     * @return \App\Repository\Principal\ChequeRepository
     */
    public function getChequeRepository()
    {
        return $this->chequeRepository;
    }

    /**
     * @return \App\Repository\Principal\BoletoRepository
     */
    public function getBoletoRepository()
    {
        return $this->boletoRepository;
    }

    /**
     * @return \App\Repository\Principal\TransacaoCartaoRepository
     */
    public function getTransacaoCartaoRepository()
    {
        return $this->transacaoCartaoRepository;
    }

    /**
     * @return \App\Repository\Principal\ContaReceberRepository
     */
    public function getContaReceberRepository()
    {
        return $this->contaReceberRepository;
    }

    /**
     * @return \App\Repository\Principal\AcaoSistemaRepository
     */
    public function getAcaoSistemaRepository()
    {
        return $this->acaoSistemaRepository;
    }

    /**
     * @return \App\Repository\Principal\UrlSistemaRepository
     */
    public function getUrlSistemaRepository()
    {
        return $this->urlSistemaRepository;
    }

    /**
     * @return \App\Repository\Principal\PapelRepository
     */
    public function getPapelRepository()
    {
        return $this->papelRepository;
    }

    /**
     * @return \App\Repository\Principal\ModuloRepository
     */
    public function getModuloRepository()
    {
        return $this->moduloRepository;
    }

    /**
     * @return \App\Repository\Principal\ModuloPapelAcaoRepository
     */
    public function getModuloPapelAcaoRepository()
    {
        return $this->moduloPapelAcaoRepository;
    }

    /**
     * @return \App\Repository\Principal\ModuloUsuarioAcaoRepository
     */
    public function getModuloUsuarioAcaoRepository()
    {
        return $this->moduloUsuarioAcaoRepository;
    }

    /**
     * @return \App\Repository\Principal\FormularioFollowUpRepository
     */
    public function getFormularioFollowUpRepository()
    {
        return $this->formularioFollowUpRepository;
    }

    /**
     * @return \App\Repository\Principal\FormularioFollowUpCamposRepository
     */
    public function getFormularioFollowUpCamposRepository()
    {
        return $this->formularioFollowUpCamposRepository;
    }

    /**
     * @return \App\Repository\Principal\TipoContatoRepository
     */
    public function getTipoContatoRepository()
    {
        return $this->tipoContatoRepository;
    }

    /**
     * @return \App\Repository\Principal\InteressadoRepository
     */
    public function getInteressadoRepository()
    {
        return $this->interessadoRepository;
    }

    /**
     * @return \App\Repository\Principal\MotivoNaoFechamentoMatriculaRepository
     */
    public function getMotivoNaoFechamentoMatriculaRepository()
    {
        return $this->motivoNaoFechamentoMatriculaRepository;
    }

    /**
     * @return \App\Repository\Principal\SegmentoEmpresaConvenioRepository
     */
    public function getSegmentoEmpresaConvenioRepository()
    {
        return $this->segmentoEmpresaConvenioRepository;
    }

    /**
     * @return \App\Repository\Principal\TipoAgendamentoRepository
     */
    public function getTipoAgendamentoRepository()
    {
        return $this->tipoAgendamentoRepository;
    }

    /**
     * @return \App\Repository\Principal\AgendaCompromissoRepository
     */
    public function getAgendaCompromissoRepository()
    {
        return $this->agendaCompromissoRepository;
    }

    /**
     * @return \App\Repository\Principal\ModeloTemplateRepository
     */
    public function getModeloTemplateRepository()
    {
        return $this->modeloTemplateRepository;
    }

    /**
     * Realiza o metodo para gravar o arquivo e armazenar o seu diretorio na tabela
     *
     * @param \Symfony\Component\HttpFoundation\File\UploadedFile $arquivoUploadedFile Arquivo Upload
     * @param string $mensagemRetorno Mensagem de retorno de erro
     * @param object $objetoORM Objeto ORM para configurar o campo de string(para armazenar o diretorio)
     * @param string $nomeFuncaoSet Nome da função para configurar o arquivo no ORM
     * @param string $nomeFuncaoGet Nome da função para retorar o arquivo
     * @param string $diretorioParaSalvar Diretorio de localização arquivo para ser salvo no servidor
     *
     * @todo Remover o uso e os parametros: <b>$nomeFuncaoSet</b> e <b>$nomeFuncaoGet</b> assim que tiver a issue de alteração de anexos e arquivos
     *
     * @return boolean true|false
     */
    public function gravaArquivo($arquivoUploadedFile, &$mensagemRetorno, $objetoORM, $nomeFuncaoSet, $nomeFuncaoGet, $diretorioParaSalvar="./../public/uploads/")
    {
        $bReturn = true;

        if (is_file($arquivoUploadedFile) === true) {
            if (((gettype($arquivoUploadedFile) !== "string"))&&($arquivoUploadedFile->isValid() === true)) {
                // TODO: Eventualmente remover o código para remoção de arquivos no servidor
                $arquivoAntigo = $objetoORM->{$nomeFuncaoGet}();
                if ((is_file($arquivoAntigo) === true) && (file_exists($arquivoAntigo)) === true) {
                    unlink($arquivoAntigo);
                }

                $nomeArquivo = uniqid() . '.' . $arquivoUploadedFile->guessExtension();

                $caminhoUploads = realpath($diretorioParaSalvar);
                if ($caminhoUploads === false) {
                    mkdir($diretorioParaSalvar);
                    $caminhoUploads = realpath($diretorioParaSalvar);
                }

                $arquivoUploadedFile->move($caminhoUploads, $nomeArquivo);
                // TODO: O código abaixo está comentado para quando tivermos a issue de alteração de arquivos, economizar tempo
                // if(method_exists($objetoORM, "addArquivos") === true) {
                // $arquivoORM = null;
                // $bReturn = $this->criarArquivoORM($arquivoORM, $mensagemRetorno, $nomeArquivo, $arquivoUploadedFile->guessExtension(), ($diretorioParaSalvar . $nomeArquivo));
                // $objetoORM->addArquivos($arquivoORM);
                // } else {
                // $mensagemRetorno = "O objeto passado, não possui relacionamento com a tabela de Arquivos[AddArquivos], favor verificar.";
                // $bReturn = false;
                // }
                $objetoORM->{$nomeFuncaoSet}($diretorioParaSalvar . $nomeArquivo);
            } else if ((empty($arquivoUploadedFile) === false)&&(gettype($arquivoUploadedFile) === "string")) {
                // TODO: Eventualmente remover este código, pois como será relacionado com ManyToMany, não faz sentido mais substituir pela mesma string
                $objetoORM->{$nomeFuncaoSet}($arquivoUploadedFile);
            } else {
                $bReturn         = false;
                $mensagemRetorno = $arquivoUploadedFile->getErrorMessage() . "[Codigo ERRO PHP]:" . $arquivoUploadedFile->getError();
            }//end if
        } else {
            // TODO: O código abaixo está comentado para quando tivermos a issue de alteração de arquivos, economizar tempo
            // if(method_exists($objetoORM, "getArquivos") === true) {
            // $colecaoArquivos = $objetoORM->getArquivos();
            // $contador = $colecaoArquivos->count();
            // for($i = 0; $i < $contador; $i++) {
            // $tempObj = $colecaoArquivos->get($i);
            // $objetoORM->removeArquivos($tempObj);
            // }
            // } else {
            // $mensagemRetorno = "O objeto passado, não possui relacionamento com a tabela de Arquivos[GetArquivos], favor verificar.";
            // $bReturn = false;
            // }
            $objetoORM->{$nomeFuncaoSet}("");
        }//end if

        return $bReturn;
    }


}
