export default {
  SET_ITEM_SELECIONADO (state, value) {
    state.itemSelecionado = value
  },

  SET_LISTA (state, value) {
    if (state.paginaAtual === 1) {
      state.lista = value
      return
    }

    state.lista = state.lista.concat(value)
  },

  SET_ESTA_CARREGANDO (state, value) {
    state.estaCarregando = value
  },

  SET_PAGINA_ATUAL (state, value) {
    state.paginaAtual = value
  }
}
