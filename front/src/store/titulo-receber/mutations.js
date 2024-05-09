export default {
  SET_PARCELAS (state, value) {
    state.parcelas = value
  },

  SET_VALOR_TITULO (state, value) {
    state.valorTitulo = value
  },

  SET_CONDICAO_PAGAMENTO (state, value) {
    state.condicaoPagamento = value
  },

  SET_DATA_EMISSAO (state, value) {
    state.dataEmissao = value
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

  SET_TITULO_RECEBER (state, item) {
    state.receber = item
  },

  LIMPAR_TITULO_RECEBER (state) {
    state.receber = {}
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

  SET_TITULOS_QUITAR (state, value) {
    state.titulosQuitar = [...value]
  },
  SET_TITULO_QUITAR (state, value) {
    state.titulo= value
  },

  SET_TITULO_DETALHES (state, value) {
    state.tituloDetalhes = value
  },

  SET_TITULO_MOVIMENTOS (state, value) {
    state.tituloMovimentos = value
  },
  SET_TITULO_PAGAMENTOS (state, value) {
    state.tituloPagamentos = value
  }
}
