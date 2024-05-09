export default {

  SET_LISTA (state, lista) {
    if (state.paginaAtual === 1) {
      state.lista = lista
      return
    }
    state.lista = state.lista.concat(lista)
  },

  SET_SELECIONADO (state, id) {
    state.itemSelecionadoID = id
  },

  LIMPAR_ITEM (state) {
    state.itemSelecionadoID = null
    state.item = {
      id: null
    }
  },

  SET_ITEM (state, item) {
    state.item = item
  },

  SET_DESCRICAO (state, descricao) {
    state.item.descricao = descricao
  },

  SET_DESCRICAO_ABREVIADA (state, value) {
    state.item.descricao_abreviada = value
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
  },

  SET_ORDER_BY (state, value) {
    state.order = value.order
    state.direcao = value.direcao
  },

  SET_LIQUIDACAO_IMEDIATA (state, statusLiquidacao) {
    state.item.liquidacao_imediata = statusLiquidacao
  },

  SET_FORMA_BOLETO (state, value) {
    state.item.forma_boleto = value
  },

  SET_FORMA_CHEQUE (state, value) {
    state.item.forma_cheque = value
  },

  SET_FORMA_CREDITO (state, value) {
    state.item.forma_cartao = value
  },

  SET_FORMA_DEBITO (state, value) {
    state.item.forma_cartao_debito = value
  },

  SET_FORMA_TRANSFERENCIA (state, value) {
    state.item.forma_transferencia = value
  }

}
