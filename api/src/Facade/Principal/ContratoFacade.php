<?php

namespace App\Facade\Principal;

use Doctrine\Common\Persistence\ManagerRegistry;
use App\Helper\ConstanteParametros;
use App\Helper\SituacoesSistema;
use App\BO\Principal\ContratoBO;
use App\Entity\Principal\Contrato;
use App\Entity\Principal\Funcionario;
use App\Facade\Principal\AgendamentoPersonalFacade;
use App\Facade\Principal\AlunoFacade;

/**
 *
 * @author Luiz A Costa
 */
class ContratoFacade extends GenericFacade
{

    /**
     *
     * @var \App\Repository\Principal\ContratoRepository
     */
    private $contratoRepository;

    /**
     *
     * @var \App\Repository\Principal\ModeloTemplateRepository
     */
    private $modeloTemplateRepository;

    /**
     *
     * @var \App\BO\Principal\ContratoBO
     */
    private $contratoBO;

    /**
     *
     * @var \App\Facade\Principal\AgendamentoPersonalFacade
     */
    private $agendamentoPersonalFacade;

    /**
     *
     * @var \App\Facade\Principal\AlunoFacade
     */
    private $alunoFacade;

    /**
     * {@inheritDoc}
     *
     * @see \App\Facade\Principal\GenericFacade::__construct()
     */
    function __construct (ManagerRegistry $managerRegistry, $connection="base_principal")
    {
        parent::__construct($managerRegistry);
        $this->contratoRepository       = self::getEntityManager()->getRepository(\App\Entity\Principal\Contrato::class);
        $this->modeloTemplateRepository = self::getEntityManager()->getRepository(\App\Entity\Principal\ModeloTemplate::class);
        $this->contratoBO = new ContratoBO(self::getEntityManager());
        $this->agendamentoPersonalFacade = new AgendamentoPersonalFacade($managerRegistry);
        $this->alunoFacade = new AlunoFacade($managerRegistry);
    }


    /**
     * Lista todos os registros do banco de dados
     *
     * @param array $parametros Parametros da requisicao
     *
     * @return array
     */
    public function listar($parametros)
    {
        $retornoRepositorio = $this->contratoRepository->filtrarContratoPorPagina($parametros, $parametros[ConstanteParametros::CHAVE_PAGINA]);

        $retorno = [
            ConstanteParametros::CHAVE_TOTAL => $retornoRepositorio->getTotalItemCount(),
            ConstanteParametros::CHAVE_ITENS => $retornoRepositorio->getItems(),
        ];
        return $retorno;
    }

        /**
     * Lista todos os registros do banco de dados
     *
     * @param array $parametros Parametros da requisicao
     *
     * @return array
     */
    public function listarContratos($parametros)
    {
        $retornoRepositorio = $this->contratoRepository->filtrarContratosPorPagina($parametros, $parametros[ConstanteParametros::CHAVE_PAGINA]);

        $retorno = [
            ConstanteParametros::CHAVE_TOTAL => $retornoRepositorio->getTotalItemCount(),
            ConstanteParametros::CHAVE_ITENS => $retornoRepositorio->getItems(),
        ];
        return $retorno;
    }



    /**
     * Busca a quantidade de contratos vigentes em uma determinada turma
     *
     * @param int $id
     *
     * @return int
     */
    public function quantidadeContratosVigentesTurma ($id)
    {
        $retornoRepositorio = $this->contratoRepository->quantidadeContratosVigentesTurma($id);
        if (count($retornoRepositorio) === 0) {
            return 0;
        }

        return (int) $retornoRepositorio[0]["qtde"];
    }



    /**
     * Busca o registro pela chave primaria
     *
     * @param string $mensagemErro Mensagem que ira retornar para front-end
     * @param int $id Chave primaria do registro
     *
     * @return array|NULL
     */
    public function buscarPorId(&$mensagemErro, $id)
    {
        return $this->contratoRepository->buscarPorId($id);
    }

    /**
     * Busca o registro pela chave primaria
     *
     * @param string $mensagemErro Mensagem que ira retornar para front-end
     * @param string $chave Chave do contrato
     *
     * @return boolean
     */
    public function aceitarContrato( $chave)
    {
        return $this->contratoRepository->aceitarContrato($chave);
    }

    /**
     * Busca os contratos ativos através da turma
     *
     * @param int $turmaId Chave primaria da turma
     *
     * @return array|NULL
     */
    public function buscarContratosAtivosComDollarPorTurma($turmaId)
    {
        return $this->contratoRepository->buscarContratosAtivosComDollarPorTurma($turmaId);
    }

    /**
     * Cria um objeto no banco de dados
     *
     * @param string $mensagemErro Mensagem que ira retornar para front-end
     * @param array $parametros Parametros a serem inclusos no objeto
     *
     * @return mixed|null|\App\Entity\Principal\Contrato
     */
    public function criar(&$mensagemErro, $parametros=[])
    {
        $objetoORM = null;
        if ($this->contratoBO->podeSalvar($parametros, $mensagemErro) === true) {
            unset($parametros[ConstanteParametros::CHAVE_CREDITOS_PERSONAL]);
            $parametros[ConstanteParametros::CHAVE_SEQUENCIA_CONTRATO] = $this->contratoRepository->buscarUltimoSequencial($parametros[ConstanteParametros::CHAVE_ALUNO]->getId());
            $objetoORM = \App\Factory\GeneralORMFactory::criar(\App\Entity\Principal\Contrato::class, true, $parametros);
            self::persistSeguro($objetoORM, $mensagemErro);
        }

        return $objetoORM;
    }

    /**
     * Atualiza um registro no banco de dados
     *
     * @param string $mensagemErro Mensagem que ira retornar para front-end
     * @param int $id Chave primaria do registro
     * @param array $parametros Campos e valores que iram ser atualizados
     *
     * @return boolean
     */
    public function atualizar(&$mensagemErro, $id, $parametros=[])
    {
        $objetoORM = $this->contratoRepository->find($id);
        //var_dump($objetoORM->getSituacao());
        //die;
        // $dataHj = new \DateTime();
        if (is_null($objetoORM) === true) {
            $mensagemErro = "Contrato não encontrado na base de dados.";
        } else {
            if ($this->contratoBO->podeAlterar($parametros, $mensagemErro) === true) {
                $mensagemErro = '';

                if (($objetoORM->getSituacao() === SituacoesSistema::SITUACAO_CONTRATO_TRANCADO) && 
                                    ($parametros['situacao'] !== SituacoesSistema::SITUACAO_CONTRATO_TRANCADO)) {
                    $dataTrancado = '';


                    if (is_null($objetoORM->getDataCancelamentoContrato()) === false) {
                        $dataTrancado = "Trancado desde :" . $objetoORM->getDataCancelamentoContrato()->format("d/m/Y H:i");
                    }
                    $mensagemErro = "Contrato Trancado, não é possivel alterar o status!. \n". $dataTrancado;
                    return false;
                }

                if (($parametros['situacao'] === SituacoesSistema::SITUACAO_CONTRATO_TRANCADO) && 
                                                            ($objetoORM->getSituacao() !== SituacoesSistema::SITUACAO_CONTRATO_TRANCADO)) {

                    $dataHj = new \DateTime();
                    $objetoORM->setDataCancelamentoContrato($dataHj);   
                }

                self::getFctHelper()->setParams($parametros, $objetoORM);
                
                if ($objetoORM->getSituacao() === SituacoesSistema::SITUACAO_CONTRATO_CANCELADO) {
                    $dataHj = new \DateTime();
                    $objetoORM->setDataCancelamentoContrato($dataHj);
                    \App\BO\Principal\ContaReceberBO::cancelarContasReceber($objetoORM);
                }
                $alunoORM = $objetoORM->getAluno();
                $this->alunoFacade->atualizarSituacao($mensagemErro, $alunoORM);
                if (empty($mensagemErro) === false) {
                    return null;
                }

                self::flushSeguro($mensagemErro);
            }
        }

        return empty($mensagemErro);
    }




    /**
     * Atualiza o texto do contrato
     *
     * @param string $mensagemErro Mensagem que ira retornar para front-end
     * @param int $id do contrato
     * @param array $parametros
     *
     * @return array contendo HTML do contrato e código de matrícula do aluno
     */
    public function atualizarTexto(&$mensagemErro, $id, &$parametros=[])
    {
        $contratoORM = $this->contratoRepository->find($id);

        if (is_null($contratoORM) === true) {
            $mensagemErro = "Contrato não encontrado na base de dados.";
            return false;
        }

        $objetoORM = $this->contratoRepository->find($id);
        $objetoORM->setTexto($parametros["texto"]);
        self::flushSeguro($mensagemErro);

        return true;
    }


    /**
     * Imprime um contrato
     *
     * @param string $mensagemErro Mensagem que ira retornar para front-end
     * @param int $id do contrato
     * @param array $parametros
     *
     * @return array contendo HTML do contrato e código de matrícula do aluno
     */
    public function gerarHtmlContrato(&$mensagemErro, $id, &$parametros=[])
    {
        $contratoORM = $this->contratoRepository->find($id);

        if (is_null($contratoORM) === true) {
            $mensagemErro = "Contrato não encontrado na base de dados.";
            return false;
        }

        // $textoContrato = $contratoORM->getTexto();
        // if (empty($textoContrato) === false) {
            //     $textoContrato = '<page size="A4">'.$textoContrato.'</page>';
            //     return ["texto" => $textoContrato];
            // }
            
            $modeloContratoORM = $this->modeloTemplateRepository->find($parametros[ConstanteParametros::CHAVE_MODELO_CONTRATO]);
            
            if (is_null($modeloContratoORM) === true) {
                $mensagemErro = "Modelo de contrato não encontrado.";
                return false;
            }
            
            $modeloContratoHTML = $modeloContratoORM->getModeloHtml();
            $dadosContrato      = $this->organizarInformacoes($contratoORM);
            $textoContrato      = $this->transformarContrato($modeloContratoHTML, $dadosContrato);
            if ($contratoORM->getObservacao()) {
                $textoContrato = $textoContrato . '<strong>P.S.: </strong> Este contrato tem a seguinte Observação - '. $contratoORM->getObservacao() .'<br><br>';
            }
       
        return ["texto" => $textoContrato];
    }

    /**
     * Busca os dados para geração da mala direta
     *
     * @param array $parametros
     *
     * @return array|NULL
     */
    public function buscaDadosMalaDireta($parametros)
    {
        return $this->contratoRepository->buscarDadosMalaDireta($parametros);
    }

    /**
     * Busca o código de matrícula de acordo com o contrato
     *
     * @param int $id
     *
     * @return string|NULL
     */
    public function getCodigoMatricula($id)
    {
        $contratoORM = $this->contratoRepository->find($id);
        if (is_null($contratoORM) === true) {
            $mensagemErro = "Contrato não encontrado.";
            return null;
        }

        return $contratoORM->getAluno()->getId() . "/" . $contratoORM->getSequenciaContrato();
    }

    
     /**
     * Busca a data que o aluno deu aceite no contrato
     *
     * @param int $id
     *
     * @return array|NULL
     */
    public function getDadosSimplificado($idOrChave)
    {
        $contratoORM = $this->contratoRepository->find($idOrChave);
        if (is_null($contratoORM) === true) {
            $contratoORM = $this->contratoRepository->findOneBy(["chave_aceite" => $idOrChave]);
        }
        if (is_null($contratoORM) === true) {
            $mensagemErro = "Contrato não encontrado.";
            return null;
        }
        if(empty($contratoORM->getChave_aceite())){
            $contratoORM->setChave_aceite(uniqid().uniqid());
            self::persistSeguro($contratoORM, $mensagemErro);
        }

        $dados = [];

        $aluno      = $contratoORM->getAluno();

        $dados["nome_aluno"] =$aluno->getPessoa()->getNomeContato();
        $dados["email_aluno"] =$aluno->getPessoa()->getEmailPreferencial();
        if (empty($dados["email_aluno"])) {
            $dados["email_aluno"] =$aluno->getPessoa()->getEmailContato();     
        }        
        if (empty($dados["email_aluno"])) {
            $dados["email_aluno"] =$aluno->getResponsavelFinanceiroPessoa()->getEmailPreferencial();     
        }  
        if (empty($dados["email_aluno"])) {
            $dados["email_aluno"] =$aluno->getResponsavelFinanceiroPessoa()->getEmailContato();     
        }  

        $dados["telefone_aluno"] =$aluno->getPessoa()->getTelefoneContato(); 
        $dados["chave_aceite"] = $contratoORM->getChave_aceite();
        $dados["data_aceite"] = $contratoORM->getData_aceite();

       
        return $dados;
    }

    /**
     * Organiza as informações do contrato para transformar no texto do contrato
     *
     * @param \App\Entity\Principal\Contrato $contrato
     *
     * @return array
     */
    private function organizarInformacoes($contrato)
    {
        $franqueada = $contrato->getFranqueada();
        $turma      = $contrato->getTurma();
        $aluno      = $contrato->getAluno();
        $contratoObservacao = $contrato->getObservacao();
        $creditosPersonalDisponibilidade = $contrato->getCreditosPersonal();
        $alunoPessoa = $aluno->getPessoa();

        $creditosPersonalQuantidade = "";
        if (is_null($creditosPersonalDisponibilidade) === false) {
            $creditosPersonalQuantidade = $creditosPersonalDisponibilidade->getQuantidade();
        }

        $horarioTurma   = '';
        $turmaDescricao = '';

        if ($turma !== null) {
            $horario        = $turma->getHorario();
            $turmaDescricao = $turma->getDescricao();
            if ($horario !== null) {
                $horarioTurma = $horario->getDescricao();
            }
        }

        $agendamentosPersonal = $contrato->getAgendamentoPersonals();

        $personalDiaHoraAulas = '';
        $personalDataInicio   = '';
        $personalDataFim      = '';
        if (count($agendamentosPersonal) > 0) {
            $infoPersonal       = $this->agendamentoPersonalFacade->buscarInformacoesAgendamentos($mensagemErro, $agendamentosPersonal);
            $personalDataInicio = $infoPersonal["dataInicio"];
            $personalDataFim    = $infoPersonal["dataFim"];

            foreach ($infoPersonal["diasHoras"] as $diaHora) {
                if (empty($personalDiaHoraAulas) === false) {
                    $personalDiaHoraAulas .= " & ";
                }

                $personalDiaHoraAulas .= $diaHora["dia"] . ' ' . $diaHora["hora"];
            }
        }

        $franqueadaCNPJ = preg_replace('/(\d{2})(\d{3})(\d{3})(\d{4})(\d{2})/', '$1.$2.$3/$4-$5', $franqueada->getCnpj());

        $franqueadaTelefone = $franqueada->getTelefone();
        if (is_null($franqueadaTelefone) === false) {
            $franqueadaTelefone = preg_replace('/(\d{2})(\d{5})(\d{4})/', '($1) $2-$3', $franqueadaTelefone);
        }

        $franqueadaTelefoneSecundario = $franqueada->getTelefoneSecundario();
        if (is_null($franqueadaTelefoneSecundario) === false) {
            $franqueadaTelefoneSecundario = preg_replace('/(\d{2})(\d{5})(\d{4})/', '($1) $2-$3', $franqueadaTelefoneSecundario);
        }

        $franqueadaCidade = $franqueada->getCidade();
        $franqueadaEstado = $franqueada->getEstado();
        if (is_null($franqueadaCidade) === false) {
            $franqueadaCidade = $franqueadaCidade->getNome();
        }

        if (is_null($franqueadaEstado) === false) {
            $franqueadaEstado = $franqueadaEstado->getNome();
        }

        if (is_null($aluno->getResponsavelFinanceiroPessoa()) === false) {
            $pessoa = $aluno->getResponsavelFinanceiroPessoa();
        } else {
            $pessoa = $alunoPessoa;
        }

        $pessoaCNPJCPF = $pessoa->getCnpjCpf();
        if (is_null($pessoaCNPJCPF) === false) {
            if (strlen($pessoaCNPJCPF) === 11) {
                $pessoaCNPJCPF = preg_replace('/(\d{3})(\d{3})(\d{3})(\d{2})/', '$1.$2.$3-$4', $pessoaCNPJCPF);
            } else {
                $pessoaCNPJCPF = preg_replace('/(\d{2})(\d{3})(\d{3})(\d{4})(\d{2})/', '$1.$2.$3/$4-$5', $pessoaCNPJCPF);
            }
        }

        $pessoaTelefoneContato = $pessoa->getTelefoneContato();
        if (is_null($pessoaTelefoneContato) === false) {
            $pessoaTelefoneContato = preg_replace('/(\d{2})(\d{5})(\d{4})/', '($1) $2-$3', $pessoaTelefoneContato);
        }

        $pessoaTelefonePreferencial = $pessoa->getTelefonePreferencial();
        if (is_null($pessoaTelefonePreferencial) === false) {
            $pessoaTelefonePreferencial = preg_replace('/(\d{2})(\d{5})(\d{4})/', '($1) $2-$3', $pessoaTelefonePreferencial);
        }

        $pessoaTelefoneProfissional = $pessoa->getTelefonePreferencial();
        if (is_null($pessoaTelefoneProfissional) === false) {
            $pessoaTelefoneProfissional = preg_replace('/(\d{2})(\d{5})(\d{4})/', '($1) $2-$3', $pessoaTelefoneProfissional);
        }

        $pessoaCidade = $pessoa->getCidade();
        $pessoaEstado = $pessoa->getEstado();
        if (is_null($pessoaCidade) === false) {
            $pessoaCidade = $pessoaCidade->getNome();
        }

        if (is_null($pessoaEstado) === false) {
            $pessoaEstado = $pessoaEstado->getNome();
        }

        $codigoMatricula = $this->getCodigoMatricula($contrato->getId());
        $dataMatricula   = $contrato->getDataContrato()->format('d/m/Y');

        // Aluno
        $dataNascimentoAluno = $alunoPessoa->getDataNascimento();
        if (is_null($dataNascimentoAluno) === false) {
            $dataNascimentoAluno = $dataNascimentoAluno->format('d/m/Y');
        }

        $alunoCNPJCPF = $alunoPessoa->getCnpjCpf();
        if (is_null($alunoCNPJCPF) === false) {
            if (strlen($alunoCNPJCPF) === 11) {
                $alunoCNPJCPF = preg_replace('/(\d{3})(\d{3})(\d{3})(\d{2})/', '$1.$2.$3-$4', $alunoCNPJCPF);
            } else {
                $alunoCNPJCPF = preg_replace('/(\d{2})(\d{3})(\d{3})(\d{4})(\d{2})/', '$1.$2.$3/$4-$5', $alunoCNPJCPF);
            }
        }

        $alunoTelefoneContato = $alunoPessoa->getTelefoneContato();
        if (is_null($alunoTelefoneContato) === false) {
            $alunoTelefoneContato = preg_replace('/(\d{2})(\d{5})(\d{4})/', '($1) $2-$3', $alunoTelefoneContato);
        }

        $alunoTelefonePreferencial = $alunoPessoa->getTelefonePreferencial();
        if (is_null($alunoTelefonePreferencial) === false) {
            $alunoTelefonePreferencial = preg_replace('/(\d{2})(\d{5})(\d{4})/', '($1) $2-$3', $alunoTelefonePreferencial);
        }

        $alunoTelefoneProfissional = $alunoPessoa->getTelefonePreferencial();
        if (is_null($alunoTelefoneProfissional) === false) {
            $alunoTelefoneProfissional = preg_replace('/(\d{2})(\d{5})(\d{4})/', '($1) $2-$3', $alunoTelefoneProfissional);
        }

        $alunoPessoaCidade = $alunoPessoa->getCidade();
        $alunoPessoaEstado = $alunoPessoa->getEstado();
        if (is_null($alunoPessoaCidade) === false) {
            $alunoPessoaCidade = $alunoPessoaCidade->getNome();
        }

        if (is_null($alunoPessoaEstado) === false) {
            $alunoPessoaEstado = $alunoPessoaEstado->getNome();
        }
        if ($turma !== null) {
            $turmaDataInicio = $turma->getDataInicio()->format('d/m/Y'); 
            //$contrato->getDataInicioContrato()->format('d/m/Y');
            $turmaDataFim    = $contrato->getDataTerminoContrato()->format('d/m/Y');
        } else {
            $turmaDataInicio = '';
            $turmaDataFim    = '';
        }
        // Construção das parcelas
        $parcelas      = ['<ul>'];
        $contasReceber = $contrato->getContratoContaReceber();
        if (isset($contasReceber[0]) === true) {
            $contaReceber = $contasReceber[0];

            $titulos = $contaReceber->getTituloRecebers();
            foreach ($titulos as $titulo) {
                $observacao     = $titulo->getObservacao();
                $numeroParcela  = $titulo->getNumeroParcelaDocumento();
                $valorParcela   = number_format($titulo->getValorOriginal(), 2, ',', '.');
                $dataVencimento = $titulo->getDataVencimento()->format('d/m/Y');

                $parcelas[] = "<li>Parcela nº $numeroParcela no valor de R$ $valorParcela referente à $observacao, com vencimento em $dataVencimento.</li>";
            }
        }

        $parcelas[] = '</ul>';
        $parcelas   = implode("", $parcelas);

        $itensContaReceberORM = $contasReceber[0]->getItemsContaReceber()->getValues();
        if (is_array($itensContaReceberORM) === false) {
            $itensContaReceberORM = [];
        }

        foreach ($itensContaReceberORM as $item) {
            $tipoItem  = $item->getNumeroSequencia();
            $itemConta = $item->getItem();

            $diaVencimento = $item->getDiasSubsequentes();

            if ($diaVencimento === null || $diaVencimento === 0) {
                $dataVencimento = $item->getDataVencimento();
                // Alguns registros antigos não possuem data de vencimento preenchido, tratativa para evitar erro nestes casos.
                if ($dataVencimento === null) {
                    $diaVencimento = '';
                } else {
                    $diaVencimento = $dataVencimento->format('d');
                }
            }

            if ($tipoItem === 1) {
                // Matricula
                $taxaMatricula = $item->getValor();
            } else if ($tipoItem === 2) {
                // curso / modulo
                $diaVencimentoCurso            = $diaVencimento;
                $valorParcelasCursoSemDesconto = $item->getValorParcelaSemDesconto();
                $quantidadeParcelasCurso       = $item->getNumeroParcelas();
                $valorTotalCurso        = $item->getValorItem();
                $formaPagamentoDesconto = $item->getFormaPagamento();
                // Alguns registros antigos não possuem forma de pagamento preenchido, tratativa para evitar erro nestes casos.
                if ($formaPagamentoDesconto === null) {
                    $formaPagamentoDesconto = '';
                } else {
                    $formaPagamentoDesconto = $formaPagamentoDesconto->getDescricao();
                }

                $valorDesconto = $item->getValorParcelaSemDesconto() - $item->getValorParcela();
            } else if ($tipoItem === 3) {
                // material
                $diaVencimentoMaterial      = $diaVencimento;
                $quantidadeParcelasMaterial = $item->getNumeroParcelas();
                $valorParcelasMaterial      = $item->getValorParcelaSemDesconto();
                $valorTotalMaterial         = $item->getValorItem();
                $nomeMaterial = $itemConta->getDescricao();
            }//end if
        }//end foreach

        $complementoEnderecoAluno = $alunoPessoa->getComplementoEndereco();
        if (empty($complementoEnderecoAluno) === true) {
            $complementoEnderecoAluno = '';
        } else {
            $complementoEnderecoAluno = ' - ' . $complementoEnderecoAluno;
        }

        $enderecoCombinadoAluno = $alunoPessoa->getEndereco() . " " . $alunoPessoa->getNumeroEndereco() . $complementoEnderecoAluno . " - Bairro: " . $alunoPessoa->getBairroEndereco() . " - CEP: " . $alunoPessoa->getCepEndereco() . " - " . $alunoPessoaCidade . "/" . $alunoPessoaEstado;

        $complementoEnderecoFranqueada = $franqueada->getComplementoEndereco();
        if (empty($complementoEnderecoFranqueada) === true) {
            $complementoEnderecoFranqueada = '';
        } else {
            $complementoEnderecoFranqueada = ' - ' . $complementoEnderecoFranqueada;
        }

        $enderecoCombinadoFranqueada = $franqueada->getEndereco() . " " . $franqueada->getNumeroEndereco() . $complementoEnderecoFranqueada . " - Bairro: " . $franqueada->getBairroEndereco() . " - CEP: " . $franqueada->getCepEndereco() . " - " . $franqueadaCidade . "/" . $franqueadaEstado;

        $complementoEnderecoResponsavelFinanceiro = $pessoa->getComplementoEndereco();
        if (empty($complementoEnderecoResponsavelFinanceiro) === true) {
            $complementoEnderecoResponsavelFinanceiro = '';
        } else {
            $complementoEnderecoResponsavelFinanceiro = ' - ' . $complementoEnderecoResponsavelFinanceiro;
        }

        $enderecoCombinadoResponsavelFinanceiro = $pessoa->getEndereco() . " " . $pessoa->getNumeroEndereco() . $complementoEnderecoResponsavelFinanceiro . " - Bairro: " . $pessoa->getBairroEndereco() . " - CEP: " . $pessoa->getCepEndereco() . " - " . $pessoaCidade . "/" . $pessoaEstado;

        $info = [
            'franqueada.razao-social'               => $franqueada->getRazaoSocial(),
            'franqueada.nome'                       => $franqueada->getNome(),
            'franqueada.cnpj'                       => $franqueadaCNPJ,
            'franqueada.inscricao-estadual'         => $franqueada->getInscricaoEstadual(),
            'franqueada.endereco'                   => $franqueada->getEndereco(),
            'franqueada.numero-endereco'            => $franqueada->getNumeroEndereco(),
            'franqueada.bairro'                     => $franqueada->getBairroEndereco(),
            'franqueada.cidade'                     => $franqueadaCidade,
            'franqueada.estado'                     => $franqueadaEstado,
            'franqueada.complemento-endereco'       => $franqueada->getComplementoEndereco(),
            'franqueada.cep'                        => $franqueada->getCepEndereco(),
            'franqueada.endereco-combinado'         => $enderecoCombinadoFranqueada,
            'franqueada.telefone'                   => $franqueadaTelefone,
            'franqueada.telefone-secundario'        => $franqueadaTelefoneSecundario,
            'franqueada.telefone-combinado'         => $franqueadaTelefone . "/" . $franqueadaTelefoneSecundario,
            'resp-financeiro.nome'                  => $pessoa->getNomeContato(),
            'resp-financeiro.email'                 => $pessoa->getEmailPreferencial(),
            'resp-financeiro.cnpj-cpf'              => $pessoaCNPJCPF,
            'resp-financeiro.numero-identidade'     => $pessoa->getNumeroIdentidade(),
            'resp-financeiro.endereco'              => $pessoa->getEndereco(),
            'resp-financeiro.numero-endereco'       => $pessoa->getNumeroEndereco(),
            'resp-financeiro.cidade'                => $pessoaCidade,
            'resp-financeiro.estado'                => $pessoaEstado,
            'resp-financeiro.complemento-endereco'  => $pessoa->getComplementoEndereco(),
            'resp-financeiro.cep'                   => $pessoa->getCepEndereco(),
            'resp-financeiro.endereco-combinado'    => $enderecoCombinadoResponsavelFinanceiro,
            'resp-financeiro.telefone-preferencial' => $pessoaTelefonePreferencial,
            'resp-financeiro.telefone-adicional'    => $pessoaTelefoneContato,
            'resp-financeiro.telefone-profissional' => $pessoaTelefoneProfissional,
            'resp-financeiro.telefone-combinado'    => $pessoaTelefonePreferencial . "/" . $pessoaTelefoneProfissional . "/" . $pessoaTelefoneContato,
            'contrato.matricula'                    => $codigoMatricula,
            'contrato.observacao'                   => $contratoObservacao,
            'contrato.data-matricula'               => $dataMatricula,
            'aluno.nome'                            => $alunoPessoa->getNomeContato(),
            'aluno.email'                           => $alunoPessoa->getEmailPreferencial(),
            'aluno.data-nascimento'                 => $dataNascimentoAluno,
            'aluno.numero-identidade'               => $alunoPessoa->getNumeroIdentidade(),
            'aluno.cnpj-cpf'                        => $alunoCNPJCPF,
            'aluno.telefone-preferencial'           => $alunoTelefonePreferencial,
            'aluno.telefone-adicional'              => $alunoTelefoneContato,
            'aluno.telefone-profissional'           => $alunoTelefoneProfissional,
            'aluno.telefone-combinado'              => $alunoTelefonePreferencial . "/" . $alunoTelefoneProfissional . "/" . $alunoTelefoneContato,
            'aluno.endereco'                        => $alunoPessoa->getEndereco(),
            'aluno.numero-endereco'                 => $alunoPessoa->getNumeroEndereco(),
            'aluno.bairro'                          => $alunoPessoa->getBairroEndereco(),
            'aluno.cidade'                          => $alunoPessoaCidade,
            'aluno.estado'                          => $alunoPessoaEstado,
            'aluno.cep'                             => $alunoPessoa->getCepEndereco(),
            'aluno.endereco-combinado'              => $enderecoCombinadoAluno,
            'turma.descricao'                       => $turmaDescricao,
            'turma.data-inicio'                     => $turmaDataInicio,
            'turma.data-fim'                        => $turmaDataFim,
            'turma.horario'                         => $horarioTurma,
            'creditos_personal.quantidade'          => $creditosPersonalQuantidade,
            'personal.dia-hora-aulas'               => $personalDiaHoraAulas,
            'personal.data-inicio'                  => $personalDataInicio,
            'personal.data-fim'                     => $personalDataFim,
            'parcelas'                              => $parcelas,
            'contrato.forma-pagamento'              => $formaPagamentoDesconto,
            'contrato.valor-desconto'               => $valorDesconto,
            'matricula.taxa'                        => $taxaMatricula,
            'material.valor-total'                  => $valorTotalMaterial,
            'material.nome'                         => $nomeMaterial,
            'material.quantidade-parcelas'          => $quantidadeParcelasMaterial,
            'material.valor-parcelas'               => $valorParcelasMaterial,
            'material.dia-vencimento'               => $diaVencimentoMaterial,
            'modulo.valor-total'                    => $valorTotalCurso,
            'modulo.quantidade-parcelas'            => $quantidadeParcelasCurso,
            'modulo.valor-parcelas-sem-desconto'    => $valorParcelasCursoSemDesconto,
            'modulo.dia-vencimento'                 => $diaVencimentoCurso,
        ];

        return $info;
    }

    /**
     * Transforma o modelo de contrato no arquivo final, substituindo as variáveis pelos devidos conteúdos
     *
     * @param string $modeloContrato
     * @param array  $dadosContrato
     *
     * @return string
     */
    private function transformarContrato($modeloContrato, $dadosContrato)
    {
        $texto = $modeloContrato;
        foreach ($dadosContrato as $chave => $valor) {
            $texto = preg_replace("/\{\{\s?$chave\s?\}\}/", $valor, $texto);
        }

        return $texto;
    }

    /**
     * Gera as informações para a seleção de registros do relatório.
     *
     * @param array  $parametros
     * @param string $origem = 1: Quantidade de alunos por turma, 2: Matriculas (Vendas) por periodo
     *
     * @return string
     */
    public function gerarDadosRelatorio($parametros, $origem)
    {
        return $this->contratoRepository->prepararDadosRelatorio($parametros, $origem);
    }

    /**
     * Encerra um contrato, caso seja possível encerrá-lo
     *
     * @param string $mensagemErro Mensagem que ira retornar para o front-end
     * @param Contrato $contratoORM contrato o qual se deseja encerrar
     *
     * @return boolean
     */
    public function encerrar(&$mensagemErro, $contratoORM)
    {
        if ($this->contratoBO->podeEncerrar($mensagemErro, $contratoORM) === true) {
            $contratoORM->setSituacao(SituacoesSistema::SITUACAO_CONTRATO_ENCERRADO);
            $alunoORM = $contratoORM->getAluno();
            $this->alunoFacade->atualizarSituacao($mensagemErro, $alunoORM);
        }

        return empty($mensagemErro);
    }

    /**
     * Busca o instrutor de um contrato personal
     *
     * @param int $contratoId ID do contrato o qual se deseja buscar o instrutor
     *
     * @return null|Funcionario
     */
    public function getInstrutorContratoPersonal($contratoId)
    {
        $contrato = $this->contratoRepository->find($contratoId);
        if (($contrato instanceof Contrato) === false) {
            return null;
        }

        $agendamentos = $contrato->getAgendamentoPersonals();
        if (count($agendamentos) === 0) {
            return null;
        }

        return $agendamentos[0]->getFuncionario();
    }

    /**
     * Gera os dados do relatório de titulos
     *
     * @param string $mensagem
     * @param array $parametros
     *
     * @return array
     */
    public function gerarDadosRelatorioMatriculaRematriculaContratos(&$mensagem, $parametros)
    {
        $dadosRelatorio = $this->contratoRepository->buscarDadosRelatorioMatriculaRematriculaContratos($mensagem, $parametros);

        if (empty($mensagem) === false) {
            return false;
        }

        $linhas_excel = [
            [
                "Data",
                "Aluno",
                "Tipo mov.",
                "Livro",
                "Curso",
                "Idioma",
                "Professor",
                "Situação do aluno",
                "Resp. Carteira",
            ],
        ];

        $retorno      = [
            "contratos" => $dadosRelatorio,
            "totais"    => [],
        ];
        $matriculas   = 0;
        $rematriculas = 0;
        foreach ($dadosRelatorio as $dado) {
            if ($dado["tipo_movimentacao"] === 'Matricula') {
                $matriculas++;
            } else if ($dado["tipo_movimentacao"] === 'Rematricula') {
                $rematriculas++;
            }
        }

        $retorno["totais"]["Total"]        = count($dadosRelatorio);
        $retorno["totais"]["Matriculas"]   = $matriculas;
        $retorno["totais"]["Rematriculas"] = $rematriculas;

        foreach ($retorno["contratos"] as $contrato) {
            if ($contrato["tipo_modalidade"] === SituacoesSistema::MODALIDADE_PERSONAL) {
                // Como fica muito complicado filtrar no repository em caso de contrato personal, o filtro é feito diretamente aqui
                $instrutorORM = $this->getInstrutorContratoPersonal($contrato["contrato_id"]);
                if ($instrutorORM === null) {
                    if (isset($parametros[ConstanteParametros::CHAVE_INSTRUTOR_FLAG]) === true && empty($parametros[ConstanteParametros::CHAVE_INSTRUTOR_FLAG]) === false) {
                        continue;
                    }

                    $instrutor = '';
                } else {
                    if (isset($parametros[ConstanteParametros::CHAVE_INSTRUTOR_FLAG]) === true && empty($parametros[ConstanteParametros::CHAVE_INSTRUTOR_FLAG]) === false) {
                        if ($instrutorORM->getId() !== (int) $parametros[ConstanteParametros::CHAVE_INSTRUTOR_FLAG]) {
                            continue;
                        }
                    }

                    $instrutor = $instrutorORM->getApelido();
                }
            } else {
                $instrutor = $contrato["instrutor"];
            }//end if

            $linhas_excel[] = [
                $contrato["data_contrato"],
                $contrato["nome_aluno"],
                $contrato["tipo_movimentacao"],
                $contrato["livro"],
                $contrato["curso"],
                $contrato["idioma"],
                $instrutor,
                $contrato["situacao_aluno"],
                $contrato["responsavel_carteira"],
            ];
        }//end foreach

        foreach ($retorno["totais"] as $k => $v) {
            $linhas_excel[] = [
                "",
                "",
                "",
                "",
                "",
                "",
                "",
                "",
                "$k:",
                $v,
            ];
        }

        return $linhas_excel;
    }

    /**
     * Gera os dados do relatório de titulos
     *
     * @param string $mensagem
     * @param array $parametros
     *
     * @return array
     */
    public function gerarDadosRelatorioMovimentacaoContratos(&$mensagem, $parametros)
    {
        $dadosRelatorio = $this->contratoRepository->buscarDadosRelatorioMovimentacaoContratos($mensagem, $parametros);

        if (empty($mensagem) === false) {
            return false;
        }

        $cabecalho     = [
            "Nº Contrato",
            "Aluno",
            "Criado em",
            "Início",
            "Término",
            "Situação",
            "Modalidade",
            "Turma",
        ];
        $mostrarMotivo = isset($parametros[ConstanteParametros::CHAVE_MOSTRAR_MOTIVO_CANCELAMENTO]) === true && $parametros[ConstanteParametros::CHAVE_MOSTRAR_MOTIVO_CANCELAMENTO] === true;
        if ($mostrarMotivo === true) {
            $cabecalho[] = "Mot. cancelamento";
        }

        $linhas_excel = [$cabecalho];

        $retorno       = [
            "contratos" => $dadosRelatorio,
            "totais"    => [],
        ];
        $cancelamentos = 0;
        $encerramentos = 0;
        $trancados     = 0;
        $rescindidos   = 0;
        foreach ($dadosRelatorio as $dado) {
            if ($dado["situacao_contrato"] === 'Cancelado') {
                $cancelamentos++;
            } else if ($dado["situacao_contrato"] === 'Encerrado') {
                $encerramentos++;
            } else if ($dado["situacao_contrato"] === 'Trancado') {
                $trancados++;
            } else if ($dado["situacao_contrato"] === 'Rescindido') {
                $rescindidos++;
            }
        }

        $retorno["totais"]["Total"]      = count($dadosRelatorio);
        $retorno["totais"]["Cancelado"]  = $cancelamentos;
        $retorno["totais"]["Encerrado"]  = $encerramentos;
        $retorno["totais"]["Trancado"]   = $trancados;
        $retorno["totais"]["Rescindido"] = $rescindidos;

        foreach ($retorno["contratos"] as $contrato) {
            $linha = [
                $contrato["numero_contrato"],
                $contrato["nome_aluno"],
                $contrato["criado_em"],
                $contrato["data_inicio"],
                $contrato["data_termino"],
                $contrato["situacao_contrato"],
                $contrato["modalidade_turma"],
                $contrato["turma"],
            ];

            if ($mostrarMotivo === true) {
                $linha[] = $contrato["motivo_cancelamento"];
            }

            $linhas_excel[] = $linha;
        }

        foreach ($retorno["totais"] as $k => $v) {
            $linha = [
                "",
                "",
                "",
                "",
                "",
                "",
            ];
            if ($mostrarMotivo === true) {
                $linha[] = '';
            }

            $linha[] = "$k:";
            $linha[] = $v;

            $linhas_excel[] = $linha;
        }

        return $linhas_excel;
    }

    public function gerarDadosRelatorioSituacaoContrato($parametros){
        return $this->contratoRepository->buscarDadosRelatorioSituacaoContrato($parametros);
    }

    public function gerarDadosRelatorioMatriculas($parametros = null) {
        return $this->contratoRepository->buscarDadosRelatorioMatriculas($parametros);
    }

    public function gerarDadosRelatorioSaldoHoras($parametros) {
        return $this->contratoRepository->buscarDadosRelatorioSaldoHoras($parametros);
    }

    public function gerarDadosRelatorioValoresTurma($parametros) {
        return $this->contratoRepository->buscarDadosRelatorioValoresTurma($parametros);
    }
    public function gerarDadosRelatorioRetencaoAlunos($parametros){
        return $this->contratoRepository->buscarDadosRelatorioRetencaoAlunos($parametros);
    }

    public function gerarDadosRelatorioMatriculasVendas($parametros) {
        return $this->contratoRepository->buscarDadosRelatorioMatriculaVenda($parametros);
    }

}
