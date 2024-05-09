export default {

  SET_LISTA (state, lista) {
    if (state.paginaAtual === 1) {
      state.lista = lista
      return
    }
    state.lista = state.lista.concat(lista)
  },

  SET_ORDER_BY (state, value) {
    state.order = value.order
    state.direcao = value.direcao
  },

  SET_SELECIONADO (state, id) {
    state.itemId = id
  },

  LIMPAR_ITEM (state) {
    state.itemId = null
    state.item = {}
  },

  SET_ITEM (state, item) {
    state.item = item
  },

  SET_DESCRICAO (state, descricao) {
    state.item.descricao = descricao
  },

  SET_TIPO_MOVIMENTO (state, tipoMovimento) {
    state.item.tipo_movimento = tipoMovimento
  },

  SET_SITUACAO (state, situacao) {
    state.item.situacao = situacao
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

  SET_ESTA_CARREGANDO (state, estaCarregando) {
    state.estaCarregando = estaCarregando
  }

}
