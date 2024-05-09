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

  SET_PADRAO (state, value) {
    state.itemSelecionado.padrao = value
  },

  SET_ANO (state, value) {
    state.itemSelecionado.ano = value
  },

  LIMPAR_ITEM_SELECIONADO (state) {
    state.itemSelecionadoID = null
    state.itemSelecionado = {
      id: null,
      descricao: null,
      padrao: null,
      ano: null,
      situacao: null
    }
  },

  SET_DATA (state, value) {
    state.itemSelecionado.dataFeriado = value
  },

  SET_EHFERIADO (state, value) {
    state.itemSelecionado.ehFeriado = value
  }
}
