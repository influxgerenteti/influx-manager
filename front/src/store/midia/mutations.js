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

  SET_DESCRICAO (state, value) {
    state.itemSelecionado.descricao = value
  },

  SET_TIPO (state, value) {
    state.itemSelecionado.tipo = value
  },

  SET_SITUACAO (state, value) {
    state.itemSelecionado.situacao = value
  },

  LIMPAR_ITEM_SELECIONADO (state) {
    state.itemSelecionadoID = null
    state.itemSelecionado = {
      id: null,
      descricao: null,
      tipo: null,
      situacao: null
    }
  },

  LIMPAR_FILTROS (state) {
    state.filtros = {
      descricao: null,
      tipo: null,
      situacao: null
    }
  },

  SET_FILTRO_NOME (state, value) {
    state.filtros.nome = value
  },

  SET_FILTRO_DESCRICAO (state, value) {
    state.filtros.descricao = value
  },

  SET_FILTRO_TIPO (state, value) {
    state.filtros.tipo = value
  },

  SET_FILTRO_SITUACAO (state, value) {
    state.filtros.situacao = value
  },

  SET_ORDER_BY (state, value) {
    state.order = value.order
    state.direcao = value.direcao
  }
}
