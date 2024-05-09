import Vue from 'vue'
import Vuex from 'vuex'
import root from './root'
import alertas from './alertas'
import toast from './toast'
import login from './login'
import recuperarSenha from './recuperar-senha'
import criarSenha from './criar-senha'
import redefinirSenha from './redefinir-senha'
import modulos from './modulos'
import logs from './logs'
import usuarios from './usuarios'
import importador from './importador'
import franqueadas from './franqueadas'
import categorias from './categorias'
import pessoas from './pessoas'
import aluno from './aluno'
import tipoMovimentoEstoque from './tipo-movimento-estoque'
import classificacaoAlunos from './classificacao-alunos'
import parametrosFranqueadora from './parametros-franqueadora'
import banco from './banco'
import conta from './conta'
import item from './item'
import tituloPagar from './titulo-pagar'
import tituloReceber from './titulo-receber'
import tipoMovimentoConta from './tipo-movimento-conta'
import planoConta from './plano-conta'
import condicaoPagamento from './condicao-pagamento'
import formaPagamento from './forma-pagamento'
import contaPagar from './conta-pagar'
import planejamentoLicao from './planejamento-licao'
import curso from './curso'
import idioma from './idioma'
import turma from './turma'
import funcionario from './funcionario'
import horario from './horario'
import modalidadeTurma from './modalidade-turma'
import salaFranqueada from './sala-franqueada'
import sala from './sala'
import calendario from './calendario'
import chequesPagarReceber from './cheques-pagar-receber'
import cep from './cep'
import estado from './estado'
import cidade from './cidade'
import livro from './livro'
import valorHoraLinhas from './valor-hora-linhas'
import licao from './licao'
import motivosMatriculaPerdida from './motivos-matricula-perdida'
import sistemaAvaliacao from './sistema-avaliacao'
import cargo from './cargo'
import escolaridade from './escolaridade'
import relacionamentoAluno from './relacionamento-aluno'
import motivoDevolucaoCheque from './motivo-devolucao-cheque'
import valorHora from './valor-hora'
import nivelInstrutor from './nivel-instrutor'
import funcionarioDisponibilidade from './funcionario-disponibilidade'
import contasReceber from './contas-receber'
import contrato from './contrato'
import semestre from './semestre'
import diasSubsequentes from './dias-subsequentes'
import helpHint from './help-hint'
import permissao from './permissao'
import cartao from './cartao'
import itemContaReceber from './entrega-material'
import formularioFollowUp from './formulario-follow-up'
import formularioFollowUpCampos from './formulario-follow-up-campos'
import movimentacaoConta from './movimentacao-conta'
import movimentacaoEstoque from './movimentacao-estoque'
import acoesSistema from './acoes-sistema'
import papel from './papel'
import recibo from './recibo'
import segmentoEmpresaConvenio from './segmento-empresa-convenio'
import interessados from './interessados'
import workflow from './workflow'
import workflowAcao from './workflow-acao'
import tipoContato from './tipo-contato'
import convenio from './convenio'
import etapasConvenio from './etapas-convenio'
import motivoNaoFechamentoConvenio from './motivos-convenio-perdido'
import operadoraCartao from './operadora-cartao'
import relatorioCheques from './relatorio-cheques'
import relatorioContasPagar from './relatorio-contas-pagar'
import relatorioDescontos from './relatorio-descontos'
import relatorioFluxoCaixa from './relatorio-fluxo-caixa'
import relatorioPedidoMaterialDidatico from './relatorio-pedido-material-didatico'
import modeloTemplate from './modelo-template'
import prospeccao from './prospeccao'
import inputLocker from './input-locker'
import diarioClasse from './diario-classe'
import diarioPersonal from './diario-personal'
import ocorrenciaAcademica from './ocorrencia-academica'
import tipoItem from './tipo-item'
import tipoOcorrencia from './tipo-ocorrencia'
import checklist from './checklist'
import checklistAtividadeRealizada from './checklist-atividade-realizada'
import turmaAula from './turma-aula'
import alunoDiario from './aluno-diario'
import malaDiretaAluno from './mala-direta-aluno'
import relatorioBalanceteFinanceiro from './relatorio-balancete-financeiro'
import atividadeExtra from './atividade-extra'
import servico from './servico'
import reposicaoAulaAvaliacao from './reposicao-aula-avaliacao'
import nivelamento from './nivelamento'
import relatorioFuncionario from './relatorio-funcionario'
import relatorioInformacoesFuncionarios from './relatorio-informacoes-funcionario'
import relatorioTitulos from './relatorio-titulos'
import relatorioAlunos from './relatorio-alunos'
import relatorioContratos from './relatorio-contrato'
import relatorioTurma from './relatorio-turma'
import relatorioNotas from './relatorio-nota'
import relatorioNotasTurmas from './relatorio-notas-turmas'
import relatorioFrequencias from './relatorio-frequencia'
import relatorioTurmaExistente from './relatorio-turma-existente'
import cronograma from './cronograma'
import conceitoAvaliacao from './conceito-avaliacao'
import relatorioInadimplencia from './relatorio-inadimplencia'
import relatorioMatriculas from './relatorio-matriculas'
import followUp from './follow-up'
import agendamentoPersonal from './agendamento-personal'
import indisponibilidadePersonal from './indisponibilidade-personal'
import agendaCompromisso from './agenda-compromisso'
import tipoAgendamento from './tipo-agendamento'
import personal from './personal'
import reagendamentoPersonal from './reagendamento-personal'
import bonusClass from './bonus-class'
import alunosBonusClass from './alunos-bonus-class'
import tipoVisibilidade from './tipo-visibilidade'
import atividadeDollarInflux from './atividade-dollar-influx'
import movimentoDollarInflux from './movimento-dollar-influx'
import cadastroServico from './cadastro-servico'
import metaFranqueada from './meta-franqueada'
import indicadores from './indicadores'
import importacaoBoleto from './importacao-boleto'
import midia from './midia'
import negociacaoParceriaWorkflow from './negociacao-parceria-workflow'
import agendaPersonal from './pedagogico-personal-agenda'
import relatorios from './relatorios'
import relatorioAulasOcorridas from './relatorio-aula-ocorrida'
import relatorioMatriculaRenovar from './relatorio-matricula-renovar'
import relatorioControleMaterialDidatico from './relatorio-controle-material-didatico'
import relatorioRetornoConsultor from './relatorio-retorno-consultor'
import relatorioVisitas from './relatorio-visitas'
import relatorioInteressados from './relatorio-interessados'
import relatorioDadosAluno from './relatorio-dados-aluno'
import relatorioConsultaDesistencia from './relatorio-consulta-desistencia'
import relatorioNegociacaoConvenio from './relatorio-negociacao-convenio'
import relatorioServicoSolicitado from './relatorio-servico-solicitado'
import relatorioMapaSalaTurma from './relatorio-mapa-sala-turma'
import relatorioAlunosPorTurma from './relatorio-alunos-por-turma'
import relatorioSaldoHorasVipPersonal from './relatorio-saldo-horas'
import relatorioCompromissoAprendizado from './relatorio-compromisso-aprendizado'
import relatorioEstoque from './relatorio-estoque'
import relatorioValoresTurma from './relatorio-valores-turma'
import relatorioItensDeEstoque from './relatorio-itens-de-estoque'
import relatorioAtividadeExtra from './relatorio-atividade-extra'
import relatorioContatos from './relatorio-contatos'
import relatorioDisponibilidadeInstrutor from './relatorio-disponibilidade-instrutor'
import relatorioMatriculaPerdida from './relatorio-matricula-pertida'
import relatorioProspeccao from './relatorio-prospeccao'
import relatorioConsultaConversao from './relatorio-consulta-conversao'
import relatorioAulaDesmarcada from './relatorio-aula-desmarcada'
import relatorioDadosCadastro from './relatorio-dados-cadastro'
import relatorioOcorrencia from './relatorio-ocorrencia'
import relatorioSaidasEstoque from './relatorio-saidas-de-estoque'
import relatorioRetencaoAlunos from './relatorio-retencao-alunos'
import relatorioMatriculaVenda from './relatorio-matricula-venda'
import cadastroTipoOcorrencia from './cadastro-tipo-ocorrencia'
import relatorioAniversariante from './relatorio-aniversariante'
import relatorioFollowUp from './relatorio-follow-up'


// ComponenteGenerico
Vue.use(Vuex)

const store = new Vuex.Store({
  modules: {
    root,
    alertas,
    toast,
    login,
    redefinirSenha,
    recuperarSenha,
    criarSenha,
    modulos,
    logs,
    usuarios,
    importador,
    franqueadas,
    categorias,
    pessoas,
    aluno,
    tipoMovimentoEstoque,
    classificacaoAlunos,
    parametrosFranqueadora,
    banco,
    tituloPagar,
    tituloReceber,
    tipoMovimentoConta,
    conta,
    planoConta,
    item,
    contaPagar,
    condicaoPagamento,
    formaPagamento,
    planejamentoLicao,
    curso,
    idioma,
    turma,
    funcionario,
    horario,
    modalidadeTurma,
    livro,
    valorHoraLinhas,
    licao,
    motivosMatriculaPerdida,
    sistemaAvaliacao,
    valorHora,
    nivelInstrutor,
    salaFranqueada,
    sala,
    calendario,
    chequesPagarReceber,
    cep,
    estado,
    cidade,
    cargo,
    funcionarioDisponibilidade,
    escolaridade,
    relacionamentoAluno,
    motivoDevolucaoCheque,
    contasReceber,
    contrato,
    semestre,
    diasSubsequentes,
    cartao,
    itemContaReceber,
    papel,
    permissao,
    acoesSistema,
    formularioFollowUp,
    formularioFollowUpCampos,
    movimentacaoConta,
    movimentacaoEstoque,
    interessados,
    workflow,
    workflowAcao,
    tipoContato,
    segmentoEmpresaConvenio,
    convenio,
    etapasConvenio,
    motivoNaoFechamentoConvenio,
    recibo,
    operadoraCartao,
    relatorioCheques,
    relatorioContasPagar,
    relatorioDescontos,
    relatorioFluxoCaixa,
    relatorioPedidoMaterialDidatico,
    modeloTemplate,
    prospeccao,
    inputLocker,
    diarioClasse,
    diarioPersonal,
    ocorrenciaAcademica,
    tipoItem,
    tipoOcorrencia,
    malaDiretaAluno,
    checklist,
    checklistAtividadeRealizada,
    servico,
    turmaAula,
    alunoDiario,
    reposicaoAulaAvaliacao,
    relatorioMatriculas,
    relatorioBalanceteFinanceiro,
    atividadeExtra,
    nivelamento,
    cronograma,
    relatorioFuncionario,
    relatorioTitulos,
    relatorioAlunos,
    relatorioContratos,
    relatorioTurma,
    relatorioNotas,
    relatorioFrequencias,
    conceitoAvaliacao,
    relatorioInadimplencia,
    followUp,
    agendamentoPersonal,
    indisponibilidadePersonal,
    agendaCompromisso,
    personal,
    reagendamentoPersonal,
    bonusClass,
    alunosBonusClass,
    tipoAgendamento,
    tipoVisibilidade,
    atividadeDollarInflux,
    movimentoDollarInflux,
    cadastroServico,
    metaFranqueada,
    indicadores,
    importacaoBoleto,
    midia,
    negociacaoParceriaWorkflow,
    helpHint,
    agendaPersonal,
    relatorios,
    relatorioRetencaoAlunos,
    relatorioAulasOcorridas,
    relatorioMatriculaRenovar,
    relatorioInteressados,
    relatorioNotasTurmas,
    relatorioMatriculaVenda,
    relatorioDadosAluno,
    relatorioConsultaDesistencia,
    relatorioNegociacaoConvenio,
    relatorioServicoSolicitado,
    relatorioMapaSalaTurma,
    relatorioAlunosPorTurma,
    relatorioOcorrencia,
    relatorioTurmaExistente,    
    relatorioSaldoHorasVipPersonal,
    relatorioControleMaterialDidatico,
    relatorioRetornoConsultor,
    relatorioVisitas,
    relatorioCompromissoAprendizado,
    relatorioSaidasEstoque,
    relatorioEstoque,
    relatorioValoresTurma,
    relatorioItensDeEstoque,
    relatorioAtividadeExtra,
    relatorioContatos,
    relatorioAulaDesmarcada,
    relatorioInformacoesFuncionarios,
    relatorioDisponibilidadeInstrutor,
    relatorioMatriculaPerdida,
    relatorioConsultaConversao,
    relatorioProspeccao,
    relatorioDadosCadastro,
    cadastroTipoOcorrencia,
    relatorioAniversariante,
    relatorioFollowUp

  }
})

export default store
