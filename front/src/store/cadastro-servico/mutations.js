export default {
  SET_LISTA (state, lista) {
    if (state.paginaAtual === 1) {
      state.lista = lista
      return
    }

    state.lista = state.lista.concat(lista)
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

  SET_ITEM_SELECIONADO_ID (state, value) {
    state.itemSelecionadoID = value
  },

  SET_ITEM_SELECIONADO (state, value) {
    state.itemSelecionado = value
  },

  SET_FILTRO_DESCRICAO (state, value) {
    state.filtros.descricao = value
  },

  SET_FILTRO_TIPO_ITEM (state, value) {
    state.filtros.tipo_item = value
  },

  SET_FILTRO_FRANQUEADA (state, value) {
    state.filtros.filtro_franqueada = value
  },

  SET_FILTRO_SITUACAO (state, value) {
    state.filtros.situacao = value
  },

  LIMPAR_ITEM_SELECIONADO (state) {
    state.itemSelecionadoID = null
    state.itemSelecionado = {
      id: null,
      descricao: null,
      narrativa: null,
      valor_venda: 0,
      plano_conta: null,
      situacao: null
    }
  },

  SET_ORDER_BY (state, value) {
    state.order = value.order
    state.direcao = value.direcao
  }
}
