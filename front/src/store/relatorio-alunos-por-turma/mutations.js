export default {
  SET_LISTA (state, lista) {
      state.lista = lista
  },
  SET_ESTA_CARREGANDO (state, value) {
      state.estaCarregando = value
  },
  SET_PARAMETROS (state, value) {
      state.parametros = value
  },

  LIMPAR_ITEM_SELECIONADO (state) {
    state.filtros = {
      turma: null,
      horarioSelecionado: null,
      sala_franqueada: null,
      instrutor: null,
      livro: null
    }
  },
}

