export default {
  SET_LISTA_DIARIO (state, lista) {
    state.listaDiario = lista
  },

  SET_ESTA_CARREGANDO (state, value) {
    state.estaCarregando = value
  },

  SET_CONTRATO_SELECIONADO_ID (state, value) {
    state.contratoSelecionadoID = value
  },

  SET_AGENDAMENTO_PERSONAL (state, value) {
    state.agendamento_personal = value
  },

  SET_LICOES_REALIZADAS (state, value) {
    state.listaLicoesRealizadas = value
  },

  SET_LISTA_AVALIACAO_ALUNO_PERSONAL (state, lista) {
    state.listaAvaliacoesAluno = lista
  },

  LIMPAR_AGENDAMENTO_PERSONAL (state, value) {
    state.agendamento_personal = null
  }
}
