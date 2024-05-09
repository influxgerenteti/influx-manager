export default {
  SET_LISTA (state, lista) {
    if (state.paginaAtual === 1) {
      state.lista = lista
      return
    }

    state.lista = state.lista.concat(lista)
  },

  SET_PAGINA_ATUAL (state, pagina) {
    state.paginaAtual = pagina
  },

  SET_ESTA_CARREGANDO (state, value) {
    state.estaCarregando = value
  },

  SET_PARAMETROS (state, value) {
    state.parametros = value
  },
}
