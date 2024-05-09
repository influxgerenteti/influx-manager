export default {
  SET_PAGINA_ATUAL (state, pagina) {
    state.paginaAtual = pagina
  },

  INCREMENTAR_PAGINA_ATUAL (state) {
    state.paginaAtual++
  },

  SET_ESTA_CARREGANDO (state, carregando) {
    state.estaCarregando = carregando
  },

  SET_TOTAL_ITENS (state, total) {
    state.totalItens = total
    state.todosItensCarregados = state.totalItens <= state.lista.length
  },

  SET_LISTA (state, itens) {
    if (state.paginaAtual === 1) {
      state.lista = itens
      return
    }

    state.lista = state.lista.concat(itens)
  },

  SET_ITEM_SELECIONADO (state, id) {
    state.itemSelecionadoID = id
  },

  SET_DETALHES_ITEM_SELECIONADO (state, item) {
    state.itemSelecionado = item
  },

  LIMPAR_ITEM_SELECIONADO (state) {
    state.itemSelecionadoID = null
    state.itemSelecionado = {
      itemSelecionado: null
    }
  }
}
