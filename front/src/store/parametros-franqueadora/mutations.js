export default {

  SET_LISTA (state, lista) {
    if (state.paginaAtual === 1) {
      state.lista = lista
      return
    }
    state.lista = state.lista.concat(lista)
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

  SET_DIAS_VARIACAO_VENCIMENTO (state, diasVariacaoVencimento) {
    state.item.dias_variacao_vencimento = diasVariacaoVencimento
  },

  SET_DIAS_REATIVACAO_INTERESSADO (state, diasReativacaoInteressado) {
    state.item.dias_reativacao_interessado = diasReativacaoInteressado
  },

  SET_NUMERO_MAXIMO_PARCELAS (state, value) {
    state.item.numero_maximo_parcelas = value
  },

  SET_PERCENTUAL (state, percentual) {
    state.item.percentual_variacao_valores = percentual
    // state.item.percentual_variacao_valores = toNumber(percentual.replace('.', '').replace(',', '.'))
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

  SET_ESTA_CARREGANDO (state, carregando) {
    state.estaCarregando = carregando
  }

}
