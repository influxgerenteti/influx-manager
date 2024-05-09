export default {
  SET_LISTA (state, lista) {
    state.lista = lista
  },

  SET_TOTAL_ITENS (state, totalItens) {
    state.totalItens = totalItens
    state.todosItensCarregados = state.totalItens <= state.lista.length
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

  SET_CARREGANDO_INFO (state, value) {
    state.carregandoInfo = value
  },

  SET_ITEM_SELECIONADO_ID (state, value) {
    state.itemSelecionadoID = value
  },

  SET_ITEM_SELECIONADO (state, value) {
    state.itemSelecionado = value
  },

  SET_FILTROS (state, value) {
    state.filtros = value
  },

  SET_FILTROS_AGENDA(state, value) {
    state.filtrosAgenda = value
  },

  LIMPAR_ITEM_SELECIONADO (state) {
    state.itemSelecionadoID = null
    state.itemSelecionado = {
      descricao: null
    }
  }
}