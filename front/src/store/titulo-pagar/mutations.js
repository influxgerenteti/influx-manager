export default {
  SET_PARCELAS (state, value) {
    state.parcelas = value
  },

  SET_VALOR_TITULO (state, value) {
    state.valorTitulo = value
  },

  SET_DATA_VENCIMENTO (state, value) {
    state.data_vencimento = value
  },

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

  LIMPAR_FILTROS (state) {
    state.filtros = {}
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
    state.filtros.situacao = situacao
  },

  SET_DATA_INICIAL (state, inicial) {
    state.filtros.data_inicial = inicial
  },

  SET_DATA_FINAL (state, final) {
    state.filtros.data_final = final
  },

  SET_VALOR_INICIAL (state, inicial) {
    state.filtros.valor_inicial = inicial
  },

  SET_VALOR_FINAL (state, final) {
    state.filtros.valor_final = final
  },

  SET_FAVORECIDO_PESSOA (state, favorecido) {
    state.filtros.favorecido_pessoa = favorecido
  },

  SET_TITULO_PAGAR (state, item) {
    state.pagar = item
  },

  LIMPAR_TITULO_PAGAR (state) {
    state.pagar = {}
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
