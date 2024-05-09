export default {
  SET_TOTAL_ITENS (state, totalItens) {
    state.totalItens = totalItens
    state.todosItensCarregados = state.totalItens <= state.listaLogs.length
  },

  SET_ORDER_BY (state, value) {
    state.order = value.order
    state.direcao = value.direcao
  },

  SET_PAGINA_ATUAL (state, pagina) {
    state.paginaAtual = pagina
  },

  INCREMENTAR_PAGINA_ATUAL (state) {
    state.paginaAtual++
  },

  SET_ESTA_CARREGANDO (state, value) {
    state.estaCarregando = value
  },

  SET_LISTA (state, lista) {
    if (state.paginaAtual === 1) {
      state.listaLogs = lista
      return
    }

    state.listaLogs = state.listaLogs.concat(lista)
  },

  setLog (state, log) {
    state.objLog = log
  },

  setLogSelecionado (state, log) {
    state.logSelecionado = log
  }
}
